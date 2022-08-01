<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportTrackingModel extends MY_Model
{
    /**
     * Get outbound tracking.
     *
     * @param array $filters
     * @return array|int
     */
    public function getOutboundTracking($filters = [])
    {
        $this->load->model('PhBidOrderSummaryModel');
        $this->load->model('PhBidOrderContainerModel');

        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branch = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $this->db->start_cache();

        $baseQuery = $this->db
            ->select([
                'bookings.no_reference',
                'booking_inbounds.no_reference AS no_reference_inbound',
                'order_summaries.nomor_order AS no_order',
                'order_summaries.tanggal_order AS order_date',
                'safe_conducts.no_safe_conduct',
                'safe_conduct_groups.no_safe_conduct_group',
                'safe_conducts.description AS safe_conduct_description',
                'transporter_entry_permits.receiver_no_police AS no_plat',
                'order_containers.nomor_kontainer AS phbid_no_plat',
                'transporter_entry_permits.receiver_vehicle AS vehicle_type',
                'order_summaries.unit AS phbid_vehicle_type',
                'transporter_entry_permits.receiver_name AS driver',
                'transporter_entry_permits.tep_code',
                'transporter_entry_permit_trackings.description AS tracking_link_description',
                'work_orders.no_work_order',
                'work_orders.taken_at',
                'work_orders.completed_at',
                'order_containers.tanggal_ambil_kontainer AS `ambil_kontainer_/_take_container`',
                'transporter_entry_permits.checked_in_at AS checked_in',
                'transporter_entry_permits.checked_out_at AS checked_out',
                'order_containers.tanggal_stuffing AS `rm_kolam_/_stuffing`',
                'order_containers.tanggal_dooring AS `dooring_/_site_transit`',
                'transporter_entry_permit_trackings.site_transit_actual_date AS site_transit_actual',
                'transporter_entry_permit_trackings.site_transit_description',
                'order_containers.tanggal_kontainer_kembali_kedepo AS `kontainer_kembali_ke_depo_/_unloading`',
                'transporter_entry_permit_trackings.unloading_actual_date AS unloading_actual',
                'transporter_entry_permit_trackings.unloading_description',
                'safe_conduct_handovers.received_date',
                'safe_conduct_handovers.driver_handover_date',
                'safe_conduct_handovers.description AS handover_description',
            ])
            ->from('transporter_entry_permit_trackings')
            ->join('transporter_entry_permits', 'transporter_entry_permits.id = transporter_entry_permit_trackings.id_tep')
            ->join(PhBidOrderContainerModel::$tableOrderContainer, 'order_containers.id = transporter_entry_permit_trackings.id_phbid_tracking', 'left')
            ->join(PhBidOrderSummaryModel::$tableOrder, 'order_summaries.id = order_containers.id_order_summary', 'left')
            ->join('safe_conducts', 'safe_conducts.id_transporter_entry_permit = transporter_entry_permits.id', 'left')
            ->join('safe_conduct_groups', 'safe_conduct_groups.id = safe_conducts.id_safe_conduct_group', 'left')
            ->join('safe_conduct_handovers', 'safe_conduct_handovers.id_safe_conduct = safe_conducts.id', 'left')
            ->join('work_orders', 'work_orders.id_safe_conduct = safe_conducts.id AND work_orders.id_transporter_entry_permit = transporter_entry_permits.id', 'left')
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = bookings.id_booking', 'left')
            ->where([
                'transporter_entry_permits.tep_category' => 'OUTBOUND'
            ]);

        if (!empty($branch)) {
            $baseQuery->where('transporter_entry_permits.id_branch', $branch);
        }

        if (!empty($search)) {
            $search = trim($search);
            $baseQuery->group_start();
            $baseQuery->or_like('bookings.no_reference', $search);
            $baseQuery->or_like('booking_inbounds.no_reference', $search);
            $baseQuery->or_like('nomor_order', $search);
            $baseQuery->or_like('no_safe_conduct', $search);
            $baseQuery->or_like('tep_code', $search);
            $baseQuery->or_like('receiver_no_police', $search);
            $baseQuery->or_like('receiver_vehicle', $search);
            $baseQuery->group_end();
        }

        if (!empty($filters)) {
            if (key_exists('customers', $filters) && !empty($filters['customers'])) {
                if (is_array($filters['customers'])) {
                    $baseQuery->where_in('bookings.id_customer', $filters['customers']);
                } else {
                    $baseQuery->where('bookings.id_customer', $filters['customers']);
                }
            }

            if (key_exists('bookings', $filters) && !empty($filters['bookings'])) {
                $baseQuery->where_in('bookings.id', $filters['bookings']);
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                    $baseQuery->having('DATE(' . $filters['date_type'] . ')>=', format_date($filters['date_from']));
                }

                if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                    $baseQuery->having('DATE(' . $filters['date_type'] . ')<=', format_date($filters['date_to']));
                }
            }
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $finalQuery = $this->db->get_compiled_select();
        $total = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$finalQuery}) AS CI_count_all_results")->row_array()['numrows'];

        if($column == 'no') $column = 'order_date';
        $page = $baseQuery->order_by($column, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        $this->db->flush_cache();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
        }

        return [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($data),
            "recordsFiltered" => $total,
            "data" => $data
        ];
    }

}

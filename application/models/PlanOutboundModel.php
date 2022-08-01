<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PlanOutboundModel extends MY_Model
{
    protected $table = 'plan_outbounds';

    /**
     * Get base query of table.
     *
     * Achievement = outstanding (realization - party) / close realization * 100
     * If outstanding = 0 then return achievement to 100% immediately
     * If close realization > outstanding then return achievement not more than 100%
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery($branchId)
            ->select([
                'bookings.no_reference',
                'booking_inbounds.no_reference AS no_reference_inbound',
                'ref_people.name AS customer_name',
                'IFNULL((plan_outbounds.party_20 - plan_outbounds.realization_20), 0) AS outstanding_20',
                'IFNULL((plan_outbounds.party_40 - plan_outbounds.realization_40), 0) AS outstanding_40',
                'IFNULL((plan_outbounds.party_lcl - plan_outbounds.realization_lcl), 0) AS outstanding_lcl',
                'IFNULL((plan_outbounds.party_20 - (plan_outbounds.realization_20 + plan_outbounds.close_realization_20)), 0) AS left_20',
                'IFNULL((plan_outbounds.party_40 - (plan_outbounds.realization_40 + plan_outbounds.close_realization_40)), 0) AS left_40',
                'IFNULL((plan_outbounds.party_lcl - (plan_outbounds.realization_lcl + plan_outbounds.close_realization_lcl)), 0) AS left_lcl',
                'IFNULL(
                    IF(
                        (plan_outbounds.party_20 - plan_outbounds.realization_20) + (plan_outbounds.party_40 - plan_outbounds.realization_40) + (plan_outbounds.party_lcl - plan_outbounds.realization_lcl) = 0
                        OR (close_realization_20 + close_realization_40 + close_realization_lcl > (plan_outbounds.party_20 - plan_outbounds.realization_20) + (plan_outbounds.party_40 - plan_outbounds.realization_40) + (plan_outbounds.party_lcl - plan_outbounds.realization_lcl))
                    , 100,
                    (
                        (plan_outbounds.close_realization_20 + plan_outbounds.close_realization_40 + plan_outbounds.close_realization_lcl) 
                        / NULLIF((plan_outbounds.party_20 - plan_outbounds.realization_20) + (plan_outbounds.party_40 - plan_outbounds.realization_40) + (plan_outbounds.party_lcl - plan_outbounds.realization_lcl), 0) 
                        * 100
                    )), 0) AS achievement',
                //'IFNULL(((plan_outbounds.close_realization_20 + plan_outbounds.close_realization_40 + plan_outbounds.close_realization_lcl) / IF((plan_outbounds.party_20 + plan_outbounds.party_40 + plan_outbounds.party_lcl) = 0, 1, (plan_outbounds.party_20 + plan_outbounds.party_40 + plan_outbounds.party_lcl)) * 100), 0) AS achievement'
            ])
            ->join('bookings', 'bookings.id = plan_outbounds.id_booking', 'left')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = bookings.id_booking', 'left')
            ->join('ref_people', 'ref_people.id = bookings.id_customer', 'left');
    }

    /**
     * Get data by custom condition.
     *
     * @param $conditions
     * @param bool $resultRow
     * @param bool $withTrashed
     * @return array|int
     */
    public function getBy($conditions, $resultRow = false, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery();

        if (key_exists('compare', $conditions)) {
            $baseQuery
                ->select([
                    'IFNULL(booking_containers.realization_20, 0) - plan_outbounds.realization_20 AS current_realization_20',
                    'IFNULL(booking_containers.realization_40, 0) - plan_outbounds.realization_40 AS current_realization_40',
                    'IFNULL(booking_goods.realization_lcl, 0) - plan_outbounds.realization_lcl AS current_realization_lcl',
                    'work_order_positions.position AS current_loading_location',
                ])
                ->join('(
                    SELECT
                        handlings.id_booking, 
                        SUM(IF(ref_containers.size = 20, 1, 0)) AS party_20,
                        SUM(IF(ref_containers.size = 40 OR ref_containers.size = 45, 1, 0)) AS party_40,
                        SUM(IF(ref_containers.size = 20 AND work_order_containers.id_container IS NOT NULL, 1, 0)) AS realization_20,
                        SUM(IF((ref_containers.size = 40 OR ref_containers.size = 45) AND work_order_containers.id_container IS NOT NULL, 1, 0)) AS realization_40
                    FROM handlings
                    INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                    INNER JOIN handling_containers ON handling_containers.id_handling = handlings.id
                    INNER JOIN ref_containers ON ref_containers.id = handling_containers.id_container
                    LEFT JOIN (
                        SELECT 
                            handlings.id_booking, 
                            work_order_containers.id_container 
                        FROM handlings
                        INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                        INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                        INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                        WHERE handling_type IN("LOAD")
                            AND handlings.is_deleted = FALSE
                            AND handlings.status = "APPROVED"
                            AND work_orders.is_deleted = FALSE
                            AND work_orders.status = "COMPLETED"
                            AND DATE(handlings.handling_date) = "' . format_date($conditions['compare_date']) . '"
                    ) AS work_order_containers ON work_order_containers.id_booking = handlings.id_booking
                        AND work_order_containers.id_container = handling_containers.id_container
                    WHERE ref_handling_types.handling_type = "LOAD"
                        AND DATE(handlings.handling_date) = "' . format_date($conditions['compare_date']) . '"
                    GROUP BY id_booking
                ) AS booking_containers', 'booking_containers.id_booking = bookings.id', 'left')
                ->join('(
                    SELECT
                        handlings.id_booking,
                        SUM(handling_goods.quantity) AS party_lcl,
                        work_order_goods.quantity AS realization_lcl
                    FROM handlings
                    INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                    INNER JOIN handling_goods ON handling_goods.id_handling = handlings.id
                    LEFT JOIN (
                        SELECT
                            handlings.id_booking, 
                            IFNULL(SUM(work_order_goods.quantity), 0) AS quantity
                        FROM handlings
                        INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                        INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                        INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                        WHERE handling_type IN("LOAD")
                            AND handlings.is_deleted = FALSE
                            AND handlings.status = "APPROVED"
                            AND work_orders.is_deleted = FALSE
                            AND work_orders.status = "COMPLETED"
                            AND DATE(handlings.handling_date) = "' . format_date($conditions['compare_date']) . '"
                        GROUP BY handlings.id_booking
                    ) AS work_order_goods ON work_order_goods.id_booking = handlings.id_booking
                    WHERE handling_goods.id_handling_container IS NULL
                        AND ref_handling_types.handling_type = "LOAD"
                        AND DATE(handlings.handling_date) = "' . format_date($conditions['compare_date']) . '"
                    GROUP BY id_booking, work_order_goods.quantity
                ) AS booking_goods', 'booking_goods.id_booking = bookings.id', 'left')
                ->join('(
                    SELECT
                        handlings.id_booking,
                        GROUP_CONCAT(DISTINCT ref_positions.position) AS position
                    FROM handlings 
                    INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                    INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                    INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                    INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                    INNER JOIN ref_positions ON ref_positions.id = work_order_containers.id_position 
                        OR ref_positions.id = work_order_goods.id_position
                    WHERE handling_type IN("LOAD")
                        AND handlings.is_deleted = FALSE
                        AND handlings.status != "REJECTED"
                        AND work_orders.is_deleted = FALSE
                        AND DATE(handlings.handling_date) = "' . format_date($conditions['compare_date']) . '"
                    GROUP BY id_booking
                ) AS work_order_positions', 'work_order_positions.id_booking = bookings.id', 'left');

            unset($conditions['compare']);
            unset($conditions['compare_date']);
        }

        foreach ($conditions as $key => $condition) {
            if(is_array($condition)) {
                if(!empty($condition)) {
                    $baseQuery->where_in($key, $condition);
                }
            } else {
                $baseQuery->where($key, $condition);
            }
        }

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if($resultRow === 'COUNT') {
            return $baseQuery->count_all_results();
        } else if ($resultRow) {
            return $baseQuery->get()->row_array();
        }

        return $baseQuery->get()->result_array();
    }
}

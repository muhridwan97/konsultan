<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookingContainerModel extends MY_Model
{
    protected $table = 'booking_containers';

    /**
     * BookingContainerModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return $this->db
            ->select([
                'booking_containers.*',
                'bookings.no_reference',
                'booking_refs.no_reference AS no_booking_reference',
                'ref_containers.no_container',
                'ref_containers.size',
                'ref_containers.type',
                'ref_positions.position'
            ])
            ->from($this->table)
            ->join('bookings', 'bookings.id = booking_containers.id_booking', 'left')
            ->join('bookings AS booking_refs', 'booking_refs.id = booking_containers.id_booking_reference', 'left')
            ->join('ref_containers', 'booking_containers.id_container = ref_containers.id', 'left')
            ->join('ref_positions', 'booking_containers.id_position = ref_positions.id', 'left');
    }

    /**
     * Get single data booking container.
     * @param $id
     * @return mixed
     */
    public function getBookingContainerById($id)
    {
        $container = $this->getBaseQuery()->where('booking_containers.id', $id);

        return $container->get()->row_array();
    }

    /**
     * Get booking containers.
     * @param $bookingId
     * @return mixed
     */
    public function getBookingContainersByBooking($bookingId)
    {
        $containers = $this->getBaseQuery()->where('booking_containers.id_booking', $bookingId);

        return $containers->get()->result_array();
    }

    /**
     * Get booking containers available for create safe conduct.
     * @param $bookingId
     * @param null $exceptSafeConductId
     * @return mixed
     */
    public function getBookingContainersByBookingSafeConduct($bookingId, $exceptSafeConductId = null)
    {
        $containers = $this->db
            ->select([
                'booking_containers.*',
                'ref_containers.no_container',
                'ref_containers.size AS container_size',
                'ref_containers.type AS container_type',
                'ref_positions.position'
            ])
            ->from($this->table)
            ->join('ref_containers', 'booking_containers.id_container = ref_containers.id', 'left')
            ->join('ref_positions', 'booking_containers.id_position = ref_positions.id', 'left')
            ->join('(
                SELECT safe_conduct_containers.* 
                FROM safe_conduct_containers
	            LEFT JOIN safe_conducts ON safe_conduct_containers.id_safe_conduct = safe_conducts.id
	            WHERE safe_conducts.is_deleted = false
                ) AS safe_conduct_containers', 'booking_containers.id = safe_conduct_containers.id_booking_container', 'left');

        if (!empty($bookingId)) {
            $containers->where('booking_containers.id_booking', $bookingId);
        }

        if (!empty($exceptSafeConductId)) {
            $containers
                ->group_start()
                ->where('safe_conduct_containers.id IS NULL')
                ->or_where('safe_conduct_containers.id_safe_conduct', $exceptSafeConductId)
                ->group_end();
        } else {
            $containers->where('safe_conduct_containers.id IS NULL');
        }

        return $containers->get()->result_array();
    }

    /**
     * Get booking containers available for create handling.
     * @param $bookingId
     * @param string $type
     * @return mixed
     */
    public function getBookingContainersByBookingHandling($bookingId, $type = 'INBOUND')
    {
        $handlingTypeId = get_setting('default_outbound_handling');
        if ($type == 'INBOUND') {
            $handlingTypeId = get_setting('default_inbound_handling');
        }

        /*
        $containers = $this->getBaseQuery()
            ->distinct()
            ->join('handlings', 'booking_containers.id_booking = handlings.id_booking', 'left')
            ->join('handling_containers', 'handling_containers.id_handling = handlings.id AND booking_containers.id_container = handling_containers.id_container', 'left')
            ->where('booking_containers.id_booking', $bookingId)
            ->where('handling_containers.id IS NULL')
            ->group_start()
                ->where('handlings.id_handling_type', $handlingTypeId)
                ->or_where('handlings.id_handling_type IS NULL')
            ->group_end();
        */

        $containers = $this->db
            ->select([
                'booking_containers.*',
                'ref_containers.no_container',
                'ref_containers.size AS container_size',
                'ref_containers.type AS container_type',
                'ref_positions.position'
            ])
            ->from($this->table)
            ->join('ref_containers', 'booking_containers.id_container = ref_containers.id', 'left')
            ->join('ref_positions', 'booking_containers.id_position = ref_positions.id', 'left')
            ->join("(
                SELECT handling_containers.* FROM handlings
                INNER JOIN handling_containers ON handling_containers.id_handling = handlings.id
                WHERE handlings.id_booking = {$bookingId} 
                    AND handlings.id_handling_type = {$handlingTypeId} 
                    AND handlings.is_deleted = FALSE 
                    AND handlings.status = 'APPROVED'
                ) AS handling_containers", 'handling_containers.id_container = booking_containers.id_container', 'left')
            ->where('booking_containers.id_booking', $bookingId)
            ->where('handling_containers.id IS NULL');

        return $containers->get()->result_array();
    }

    /**
     * Insert booking container.
     * @param $data
     * @return mixed
     */
    public function createBookingContainer($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update or replace booking container.
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateBookingContainer($data, $id)
    {
        return $this->db->update($this->table, $data, (is_array($id) ? $id : ['id' => $id]));
    }

    /**
     * Delete booking container
     * @param $id
     * @return mixed
     */
    public function deleteBookingContainer($id)
    {
        return $this->db->delete($this->table, $id);
    }

    /**
     * Delete booking container by booking id.
     * @param $bookingId
     * @return mixed
     */
    public function deleteBookingContainerByBooking($bookingId)
    {
        return $this->db->delete($this->table, ['id_booking' => $bookingId]);
    }
}

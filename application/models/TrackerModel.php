<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TrackerModel extends CI_Model
{
    /**
     * Get booking which contain the container.
     * @param $containerId
     * @param null $type
     * @param null $branchId
     * @return mixed
     */
    public function getBookingsByContainer($containerId, $type = null, $branchId = null)
    {
        $bookings = $this->db->select([
            'bookings.*',
            'ref_booking_types.booking_type',
            'ref_customers.name AS customer_name'
        ])
            ->from('bookings')
            ->join('booking_containers', 'booking_containers.id_booking = bookings.id', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_people AS ref_customers', 'bookings.id_customer = ref_customers.id', 'left')
            ->where('booking_containers.id_container', $containerId)
            ->where('bookings.is_deleted', false);

        if (!empty($type)) {
            $bookings->where('ref_booking_types.category', $type);
        }

        if(!empty($branchId)) {
            $bookings->where('bookings.id_branch', $branchId);
        }

        return $bookings->get()->result_array();
    }

    /**
     * Get safe conduct that loaded the container.
     * @param $containerId
     * @param null $type
     * @return mixed
     */
    public function getSafeConductsByContainer($containerId, $type = null)
    {
        $safeConducts = $this->db->select([
            'bookings.no_booking',
            'safe_conducts.*'
        ])
            ->from('safe_conducts')
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left')
            ->join('safe_conduct_containers', 'safe_conduct_containers.id_safe_conduct = safe_conducts.id', 'left')
            ->where('safe_conduct_containers.id_container', $containerId)
            ->where('safe_conducts.is_deleted', false);

        if (!empty($type)) {
            $safeConducts->where('safe_conducts.type', $type);
        }

        return $safeConducts->get()->result_array();
    }

    /**
     * Get safe conduct that loaded the container.
     * @param $containerId
     * @return mixed
     */
    public function getDeliveryOrdersByContainer($containerId)
    {
        $safeConducts = $this->db->select([
            'bookings.no_booking',
            'delivery_orders.*'
        ])
            ->from('delivery_orders')
            ->join('bookings', 'bookings.id = delivery_orders.id_booking', 'left')
            ->join('delivery_order_containers', 'delivery_order_containers.id_delivery_order = delivery_orders.id', 'left')
            ->where('delivery_order_containers.id_container', $containerId)
            ->where('delivery_orders.is_deleted', false);

        return $safeConducts->get()->result_array();
    }

    /**
     * Get handling by specific container.
     * @param $containerId
     * @return array
     */
    public function getHandlingsByContainer($containerId)
    {
        $workOrders = $this->db->select([
            'ref_handling_types.handling_type',
            'handlings.*',
            'work_orders.id AS id_work_order',
            'work_orders.no_work_order',
            'ref_people.name AS customer_name',
        ])
            ->from('handlings')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('ref_people', 'ref_people.id = handlings.id_customer', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id', 'left')
            ->join('handling_containers', 'handling_containers.id_handling = handlings.id', 'left')
            ->where('handling_containers.id_container', $containerId)
            ->where('handlings.is_deleted', false);

        return $workOrders->get()->result_array();
    }

    /**
     * Get work order by specific container.
     * @param $containerId
     * @param null $bookingId
     * @return array
     */
    public function getWorkOrdersByContainer($containerId, $bookingId = null)
    {
        $workOrders = $this->db->select([
            'handlings.no_handling',
            'ref_handling_types.handling_type',
            'work_orders.*'
        ])
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id', 'left')
            ->where('work_order_containers.id_container', $containerId)
            ->where('work_orders.is_deleted', false)
            ->order_by('work_orders.completed_at');

        if(!empty($bookingId)) {
            $workOrders->where('handlings.id_booking', $bookingId);
        }

        return $workOrders->get()->result_array();
    }

}
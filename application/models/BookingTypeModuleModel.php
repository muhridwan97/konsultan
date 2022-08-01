<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookingTypeModuleModel extends CI_Model
{
    private $table = 'ref_booking_type_modules';

    /**
     * BookingTypeModuleModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get booking type module settings by booking type id.
     * @param $bookingTypeId
     * @return mixed
     */
    public function getBookingTypeModuleByBookingType($bookingTypeId)
    {
        return $this->db->get_where($this->table, ['id_booking_type' => $bookingTypeId])->result_array();
    }

    /**
     * Get setting value of booking type module by category and related information.
     * @param $bookingTypeId
     * @param $module
     * @param $category
     * @param null $type
     * @return array
     */
    public function getBookingTypeModuleByCategory($bookingTypeId, $module, $category, $type = null)
    {
        $conditions = [
            'id_booking_type' => $bookingTypeId,
            'id_module' => $module,
            'category' => $category
        ];
        if (!empty($type)) {
            $conditions['type'] = $type;
        }
        return $this->db->get_where($this->table, $conditions)->row_array();
    }

    /**
     * Create new booking type module.
     * @param $data
     * @return bool
     */
    public function createBookingTypeModule($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update booking type module.
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateBookingTypeModule($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete booking type data module.
     * @param $id
     * @return mixed
     */
    public function deleteBookingTypeModule($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    /**
     * Delete booking type data module.
     * @param $bookingTypeId
     * @return mixed
     */
    public function deleteBookingTypeModuleByBookingType($bookingTypeId)
    {
        return $this->db->delete($this->table, ['id_booking_type' => $bookingTypeId]);
    }
}
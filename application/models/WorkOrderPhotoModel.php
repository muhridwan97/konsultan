<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkOrderPhotoModel extends CI_Model
{
    private $table = 'work_order_photos';

    /**
     * WorkOrderModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create new work order photo.
     * @param $data
     * @return bool
     */
    public function createWorkOrderPhoto($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update work order data.
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateWorkOrderPhoto($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete work order data.
     * @param $id
     * @param bool $softDelete
     * @return bool
     */
    public function deleteWorkOrderPhoto($id, $softDelete = true)
    {
        if ($softDelete) {
            return $this->db->update($this->table, [
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => UserModel::authenticatedUserData('id')
            ], ['id' => $id]);
        }
        return $this->db->delete($this->table, ['id' => $id]);
    }
    /**
     * Get work order data by id.
     * @param $workOrderId
     * @param bool $withTrash
     * @return mixed
     */
    public function getWorkOrderPhotoById($workOrderId)
    {
        $workOrderPhoto = $this->db
            ->select($this->table.'.*')
            ->from($this->table)
            ->where('id_work_order', $workOrderId);


        return $workOrderPhoto->get()->result_array();
    }
    
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransporterEntryPermitContainerModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_containers';

    /**
     * Get active record query builder for all related warehouse data selection.
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = NULL)
    {
       $getBaseQuery = $this->db
            ->select([
                'transporter_entry_permit_containers.*',
                'ref_containers.no_container',
                'ref_containers.size',
                'ref_containers.type',
                'ref_positions.position',
                'sum(IF(transporter_entry_permit_checklists.type = "CHECK IN",1,0)) as total_check_in',
                'sum(IF(transporter_entry_permit_checklists.type = "CHECK OUT",1,0)) as total_check_out'
            ])
            
            ->from($this->table)
            ->join('transporter_entry_permits as tep', 'tep.id = transporter_entry_permit_containers.id_tep', 'left')
            ->join('ref_containers', 'transporter_entry_permit_containers.id_container = ref_containers.id', 'left')
            ->join('ref_positions', 'transporter_entry_permit_containers.id_position = ref_positions.id', 'left')
            ->join('transporter_entry_permit_checklists', 'transporter_entry_permit_containers.id = transporter_entry_permit_checklists.id_container', 'left')
            ->group_by('transporter_entry_permit_containers.id');

        return $getBaseQuery;
    }

    /**
     * Get tep containers.
     * @param $tepId
     * @return mixed
     */
    public function getTepContainersByTep($tepId)
    {
        $containers = $this->getBaseQuery()
            ->where('transporter_entry_permit_containers.id_tep', $tepId);
        return $containers->get()->result_array();
    }

    /**
     * Insert TEP container.
     * @param $data
     * @return mixed
     */
    public function createTepContainer($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update or replace tep goods.
     * @param $data
     * @param $id
     */
    public function updateTepContainer($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete tep container
     * @param $id
     * @return mixed
     */
    public function deleteTepContainer($id)
    {
        return $this->db->delete($this->table, $id);
    }

    /**
     * Delete tep container by tep id.
     * @param $teptId
     * @return mixed
     */
    public function deleteTepContainerByTep($tepId)
    {
        return $this->db->delete($this->table, ['id_tep' => $tepId]);
    }

}
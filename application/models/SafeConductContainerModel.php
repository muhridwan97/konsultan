<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SafeConductContainerModel extends CI_Model
{
    private $table = 'safe_conduct_containers';

    /**
     * SafeConductContainerModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    private function getSafeConductContainerBaseQuery()
    {
        $bookings = $this->db
            ->select([
                'safe_conduct_containers.*',
                'safe_conducts.id_booking',
                'bookings.no_reference',
                'booking_refs.no_reference AS no_booking_reference',
                'ref_containers.no_container',
                'ref_containers.size',
                'ref_containers.type',
                'ref_positions.position',
                'sum(IF(safe_conduct_checklists.type = "CHECK IN",1,0)) as total_check_in',
                'sum(IF(safe_conduct_checklists.type = "CHECK OUT",1,0)) as total_check_out'
            ])
            
            ->from($this->table)
            ->join('safe_conducts', 'safe_conducts.id = safe_conduct_containers.id_safe_conduct', 'left')
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left')
            ->join('bookings AS booking_refs', 'booking_refs.id = safe_conduct_containers.id_booking_reference', 'left')
            ->join('ref_containers', 'safe_conduct_containers.id_container = ref_containers.id', 'left')
            ->join('ref_positions', 'safe_conduct_containers.id_position = ref_positions.id', 'left')
            ->join('safe_conduct_checklists', 'safe_conduct_containers.id = safe_conduct_checklists.id_container', 'left')
            ->group_by('safe_conduct_containers.id');

        return $bookings;
    }

    /**
     * Get safe conduct containers.
     * @param $safeConductId
     * @return mixed
     */
    public function getSafeConductContainersBySafeConduct($safeConductId)
    {
        $containers = $this->getSafeConductContainerBaseQuery()
            ->where('safe_conduct_containers.id_safe_conduct', $safeConductId);
        return $containers->get()->result_array();
    }

    /**
     * Get safe conduct containers.
     * @param $id
     * @return mixed
     */
    public function getSafeConductContainersById($id)
    {
        $containers = $this->getSafeConductContainerBaseQuery()
            ->where('safe_conduct_containers.id', $id);
        return $containers->get()->row_array();
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
        $baseQuery = $this->getSafeConductBaseQuery();

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

    /**
     * Insert safe conduct container.
     * @param $data
     * @return mixed
     */
    public function createSafeConductContainer($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update or replace safe conduct goods.
     * @param $data
     * @param $id
     */
    public function updateSafeConductContainer($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete safe conduct container
     * @param $id
     * @return mixed
     */
    public function deleteSafeConductContainer($id)
    {
        return $this->db->delete($this->table, $id);
    }

    /**
     * Delete safe conduct container by safe conduct id.
     * @param $safeConductId
     * @return mixed
     */
    public function deleteSafeConductContainerBySafeConduct($safeConductId)
    {
        return $this->db->delete($this->table, ['id_safe_conduct' => $safeConductId]);
    }
}

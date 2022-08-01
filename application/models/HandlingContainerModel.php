<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HandlingContainerModel extends MY_Model
{
    protected $table = 'handling_containers';

    /**
     * HandlingContainerModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return $this->db
            ->select([
                'handling_containers.*',
                'handlings.id_booking',
                'bookings.no_reference',
                'booking_refs.no_reference AS no_booking_reference',
                'ref_containers.no_container',
                'ref_containers.size',
                'ref_containers.type',
                'ref_positions.position',
                'ref_people.name AS owner_name',
                'GROUP_CONCAT(DISTINCT handling_container_positions.id_position_block) AS id_position_blocks',
                'GROUP_CONCAT(DISTINCT ref_position_blocks.position_block) AS position_blocks'
            ])
            ->from($this->table)
            ->join('handlings', 'handlings.id = handling_containers.id_handling', 'left')
            ->join('bookings', 'bookings.id = handlings.id_booking', 'left')
            ->join('bookings AS booking_refs', 'booking_refs.id = handling_containers.id_booking_reference', 'left')
            ->join('handling_container_positions', 'handling_container_positions.id_handling_container = handling_containers.id', 'left')
            ->join('ref_position_blocks', 'ref_position_blocks.id = handling_container_positions.id_position_block', 'left')
            ->join('ref_containers', 'handling_containers.id_container = ref_containers.id', 'left')
            ->join('ref_positions', 'ref_positions.id = handling_containers.id_position', 'left')
            ->join('ref_people', 'ref_people.id = handling_containers.id_owner', 'left')
            ->group_by('handling_containers.id');
    }

    /**
     * Get single data handling container.
     * @param $id
     * @return mixed
     */
    public function getHandlingContainerById($id)
    {
        $container = $this->getBaseQuery()->where('handling_containers.id', $id);

        return $container->get()->row_array();
    }

    /**
     * Get handling containers.
     * @param $handlingId
     * @return mixed
     */
    public function getHandlingContainersByHandling($handlingId)
    {
        $containers = $this->getBaseQuery()->where('handling_containers.id_handling', $handlingId);

        return $containers->get()->result_array();
    }

    /**
     * Insert handling container.
     * @param $data
     * @return mixed
     */
    public function createHandlingContainer($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update or replace handling container.
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateHandlingContainer($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete handling container
     * @param $id
     * @return mixed
     */
    public function deleteHandlingContainer($id)
    {
        return $this->db->delete($this->table, $id);
    }

    /**
     * Delete handling container by handling id.
     * @param $handlingId
     * @return bool
     */
    public function deleteHandlingContainersByHandling($handlingId)
    {
        return $this->db->delete($this->table, ['id_handling' => $handlingId]);
    }
}

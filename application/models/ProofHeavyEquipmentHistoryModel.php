<?php

class ProofHeavyEquipmentHistoryModel extends MY_Model
{
    protected $table = 'proof_heavy_equipment_histories';

    const TYPE_INTERNAL = 'INTERNAL';
    const TYPE_EXTERNAL = 'EXTERNAL';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery()
            ->select([
                'prv_users.name AS creator_name',
                "IF(proof_heavy_equipment_histories.type='INTERNAL',ref_heavy_equipments.name,heavy_equipment_entry_permits.heep_code) AS name",
            ])
            ->join(UserModel::$tableUser, 'prv_users.id = proof_heavy_equipment_histories.created_by', 'left')
            ->join('ref_heavy_equipments','ref_heavy_equipments.id = proof_heavy_equipment_histories.id_reference','left')
            ->join('heavy_equipment_entry_permits','heavy_equipment_entry_permits.id = proof_heavy_equipment_histories.id_reference','left');
    }
    public function getLastHistory($conditions, $resultRow = true, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery()
                    ->order_by('proof_heavy_equipment_histories.id','desc');

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

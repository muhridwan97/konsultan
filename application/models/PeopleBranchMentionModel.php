<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PeopleBranchMentionModel extends MY_Model
{
    protected $table = 'ref_people_branch_mentions';

    /**
     * People Contact Model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getMentionByPersonBranch($personBranchId, $type = null)
    {
        $mention = $this->db->select()
            ->from($this->table)
            ->where('id_person_branch', $personBranchId);

        if(!empty($type)){
            $mention->where_in($this->table . '.type', $type);
        }
        return $mention->get()->result_array();
    }

    /**
     * @param $data
     * @return bool
     */
    public function insertMention($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateMention($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteMention($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AllowanceModel extends MY_Model
{
    protected $table = 'tci_hr_v1.point_histories';

    public function __construct()
	{
		parent::__construct();
		if ($this->config->item('sso_enable')) {
        $this->table = env('DB_HR_DATABASE') . '.point_histories';
		}
    }

    /**
     * Cek reference sudah diinput atau belum
     * @param [$employeeId, $id_reference, $componentId, $date , $point]
     * @return bool
     */
    public function cekReference($where){
        $reference = $this->db->get_where($this->table, $where);
        if ($reference->num_rows() > 0) {
            return false;
        }
        return true;
    }

    /**
     * get point pada tanggal yang berbeda
     * @param [$employeeId, $id_reference, $componentId, $date]
     * @return integer
     */
    public function getPointDiffDate($where){
        $this->db->select('id_employee,id_reference, id_component, date');
        $this->db->select('MIN(point) AS real_point');
        $this->db->from($this->table);
        foreach ($where as $key => $condition) {
            if(is_array($condition)) {
                if(!empty($condition)) {
                    $this->db->where_in($key, $condition);
                }
            } else {
                $this->db->where($key, $condition);
            }
        }
        $this->db->group_by('id_employee,id_reference, id_component');
        $var = $this->db->get()->row();
        if (is_null($var)) {
            return "0";
        }
        return $var->real_point;
    }

    /**
     * cek point edit late
     * jika sum_point = -2, maka sudah diberikan -1 ketika edit jadi tidak perlu lagi memberikan poin -1 cukup memberikan poin 0
     * @param [$employeeId, $id_reference, $componentId, $date]
     * @return integer
     */
    public function cekPointEditLate($where){
        $this->db->select('id_employee,id_reference, id_component, date');
        $this->db->select('SUM(point) AS sum_point');
        $this->db->from($this->table)
                ->where('id_component', '2')//BAST
                ->where_in('description', ['late approve completed job','edited when status approve']);
        foreach ($where as $key => $condition) {
            if(is_array($condition)) {
                if(!empty($condition)) {
                    $this->db->where_in($key, $condition);
                }
            } else {
                $this->db->where($key, $condition);
            }
        }
        $this->db->group_by('id_employee,id_reference, id_component');
        $var = $this->db->get()->row();
        if (is_null($var)) {
            return "0";
        }
        return $var->sum_point;
    }
    
}
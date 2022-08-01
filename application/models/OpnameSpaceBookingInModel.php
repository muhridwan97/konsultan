<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OpnameSpaceBookingInModel extends MY_Model
{
    protected $table = 'opname_space_booking_in';

   /**
     * Get active record query builder for all related warehouse data selection.
     *
     * @param null $ChecklistTypeId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($ChecklistTypeId = null)
    {
        $opnameSpaces = $this->db
            ->select([
                'opname_space_booking_in.*',
                'ref_people.name AS customer_name',
                'ref_people.email AS customer_email',
                'ref_people.id AS id_customer',
                'opname_spaces.status',
                'opname_spaces.opname_space_date',
                'prv_users.name AS validate_name',
            ])
            ->from('opname_space_booking_in')
            ->join('bookings', 'bookings.id = opname_space_booking_in.id_booking','left')
            ->join('ref_people', 'ref_people.id = bookings.id_customer','left')
            ->join('opname_spaces', 'opname_spaces.id = opname_space_booking_in.id_opname_space','left')
            ->join(UserModel::$tableUser, 'prv_users.id = opname_spaces.validated_by', 'left');
        return $opnameSpaces;
    }

    public function getOpnameSpaceBookingInById($id)
    {

        $opnameSpaces = $this->getBaseQuery()->where('opname_space_booking_in.id_opname_space', $id);

        return $opnameSpaces->get()->result_array();
    }

    /**
     * Get Customer who in opname space.
     * @param $id
     * 
     * 
     */
    public function getOpnameSpaceCustomerById($id)
    {

        $opnameSpaces = $this->getBaseQuery()->where('opname_space_booking_in.id_opname_space', $id)
                    ->group_by('ref_people.id');

        return $opnameSpaces->get()->result_array();
    }

    /**
     * Update cycle count.
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateOpnameSpaceBookingIn($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function getReportOpnameSpace($filters = []){
        $this->load->model('OpnameSpaceModel', 'OpnameSpace');
        $opnameSpaces = $this->getBaseQuery()
                    ->join("(
                        SELECT opname_space_booking_in.id_booking,MAX(opname_spaces.opname_space_date) AS last_date FROM opname_spaces
                        INNER JOIN opname_space_booking_in ON opname_spaces.id = opname_space_booking_in.`id_opname_space`
                        where opname_spaces.status = 'VALIDATED'
                        GROUP BY opname_space_booking_in.id_booking) AS lastest","lastest.id_booking = opname_space_booking_in.`id_booking` AND lastest.last_date = opname_spaces.`opname_space_date`")
                    ->where('opname_spaces.status',OpnameSpaceModel::STATUS_VALIDATED);
        if (!empty($filters)) {
            if (is_array($filters['booking'])) {
                $opnameSpaces->where_in('opname_space_booking_in.id_booking', $filters['booking']);
            }else{
                $opnameSpaces->where('opname_space_booking_in.id_booking', $filters['booking']);
            }
        }

        return $opnameSpaces->get()->result_array();
    }

    public function getOpnameSpaceReview($filters = []){
        $this->load->model('OpnameSpaceModel', 'OpnameSpace');
        $opnameSpaces = $this->getBaseQuery()
                    ->join("(
                        SELECT opname_space_booking_in.id_booking,MAX(opname_spaces.opname_space_date) AS last_date FROM opname_spaces
                        INNER JOIN opname_space_booking_in ON opname_spaces.id = opname_space_booking_in.`id_opname_space`
                        GROUP BY opname_space_booking_in.id_booking) AS lastest","lastest.id_booking = opname_space_booking_in.`id_booking` AND lastest.last_date = opname_spaces.`opname_space_date`");
        if (!empty($filters)) {
            if (is_array($filters['booking'])) {
                $opnameSpaces->where_in('opname_space_booking_in.id_booking', $filters['booking']);
            }else{
                $opnameSpaces->where('opname_space_booking_in.id_booking', $filters['booking']);
            }
        }

        return $opnameSpaces->get()->result_array();
    }

    /**
     * cek validate persen
     * @param int $id
     * @return 
     */
    public function cekPersen($id)
    {

        $opnameSpaces = $this->getBaseQuery()->where('opname_space_booking_in.id_opname_space', $id)
                    ->group_start()
                    ->where("ROUND(opname_space_booking_in.`space_diff`/(opname_space_booking_in.`space_check` - opname_space_booking_in.`space_diff`)*100,0)<-'10'")
                    ->or_where("ROUND(opname_space_booking_in.`space_diff`/(opname_space_booking_in.`space_check` - opname_space_booking_in.`space_diff`)*100,0)>'10'")
                    ->group_end();

        return $opnameSpaces->get()->result_array();
    }
  
}
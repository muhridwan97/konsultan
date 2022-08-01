<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SynchronizeModel extends CI_Model
{
    public $connection;

    /**
     * SynchronizeModel constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $config = [
            'dsn' => 'pgsql:host=10.0.0.3;port=5432;dbname=tci_cacah',
            'username' => 'postgres',
            'password' => 'postgres',
            'dbdriver' => 'pdo',
        ];
        $this->connection = $this->load->database($config, true);
    }

    /**
     * Get basic query for booking data
     * @return mixed
     */
    private function getBaseSynchronize()
    {
        $branchId = get_active_branch('id');
        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $bookings = $this->db->select([
            'bookings.*',
            'uploads.no_upload',
            'booking_in.id AS id_booking_in',
            'booking_in.no_booking AS no_booking_in',
            'ref_booking_types.booking_type',
            'ref_booking_types.category',
            'ref_suppliers.name AS supplier_name',
            'ref_customers.name AS customer_name',
            'GROUP_CONCAT(ref_document_types.document_type SEPARATOR ",") AS document_type',
            'GROUP_CONCAT(upload_documents.no_document SEPARATOR ",") AS booking_document',
            'GROUP_CONCAT(upload_documents.id SEPARATOR ",") AS upload_document'
        ])
            ->from('bookings')
            ->join('uploads', 'bookings.id_upload = uploads.id', 'left')
            ->join('upload_documents', 'uploads.id = upload_documents.id_upload AND upload_documents.is_deleted = 0', 'left')
            ->join('ref_document_types', 'upload_documents.id_document_type = ref_document_types.id', 'left')
            ->join('ref_booking_types', 'bookings.id_booking_type = ref_booking_types.id', 'left')
            ->join('ref_people AS ref_suppliers', 'bookings.id_supplier = ref_suppliers.id', 'left')
            ->join('ref_people AS ref_customers', 'bookings.id_customer = ref_customers.id', 'left')
            ->join('bookings AS booking_in', 'bookings.id_booking = booking_in.id', 'left')
            ->group_by('bookings.id')
            ->order_by('bookings.id', 'desc');

        if (!empty($branchId)) {
            $bookings->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $bookings->where('bookings.id_customer', $customerId);
        }

        return $bookings;
    }

    public function getBookingSynchronize($bookingId = null, $status = null, $withTrashed = false)
    {
        $bookings = $this->getBaseSynchronize();

        $bookings->select(get_ext_field('NO_BC11', 'bookings.id', 'no_bc11'));
        $bookings->select(get_ext_field('TGL_BC11', 'bookings.id', 'bc11_date'));
        $bookings->select(get_ext_field('POS_BC11', 'bookings.id', 'bc11_pos'));

        if (!$withTrashed) {
            $bookings->where('bookings.is_deleted', false);
        }

        if (!empty($status)) {
            if (is_array($status)) {
                $bookings->where_in('bookings.status', $status);
            } else {
                $bookings->where('bookings.status', $status);
            }
        }

        if (!empty($bookingId)) {
            $bookings->where('bookings.id', $bookingId);
            $result = $bookings->get()->row_array();
        } else {
            $result = $bookings->get()->result_array();
        }

        return $result;
    }

    public function getTppTransByBcf($no_reference)
    {
        $tppTrans = $this->connection->select('*')
            ->from('tpp_trans')
            ->where('bc_15', $no_reference)
            ->limit(1);

        return $tppTrans->get()->row_array();
    }

    /**
     * Get auto number for tpp_trans.
     * @return string
     */
    public function getAutoNumberTppTrans()
    {
        $orderData = $this->connection->query("
            SELECT COALESCE(CAST(CAST(MAX(no_order) AS INT4) + 1 AS VARCHAR), 
            (SUBSTRING(CAST(date_part('year', now()) AS varchar), 3, 2) || '00001')) AS order_number 
            FROM tpp_trans 
            WHERE SUBSTRING(no_order, 1, 2) = SUBSTRING(CAST(date_part('year', now()) AS varchar), 3, 2) LIMIT 1
        ");

        $lastOrder = $orderData->row_array();
        $orderPad = $lastOrder['order_number'];

        return $orderPad;
    }

    /**
     * Get auto number for tpp_trans.
     * @return string
     */
    public function getAutoNumberTppTranscont($no_order)
    {
        $orderData = $this->connection->query("
            SELECT MAX(no_orderdtl) AS order_number 
            FROM tpp_transcont 
            WHERE SUBSTRING(no_orderdtl, 1, 7) = '{$no_order}' LIMIT 1
        ");

        $orderPad = '0001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = intval(substr($lastOrder['order_number'], 9, 4)) + 1;
        }

        return $no_order . '-' . str_pad($orderPad, '4', '0', STR_PAD_LEFT);
    }

    /**
     * Create new record of tpp_trans table.
     * @param $data
     * @return bool
     */
    public function createTppTrans($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->connection->insert_batch('tpp_trans', $data);
        }
        return $this->connection->insert('tpp_trans', $data);
    }

    /**
     * Create new record of tpp_transcont table.
     * @param $data
     * @return bool
     */
    public function createTppTranscont($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->connection->insert_batch('tpp_transcont', $data);
        }
        return $this->connection->insert('tpp_transcont', $data);
    }
}
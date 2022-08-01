<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DocumentTypeModel extends MY_Model
{
    protected $table = 'ref_document_types';

    const DOC_DO = 'DO';
    const DOC_ATA = 'ATA';
    const DOC_SPPB = 'SPPB';
    const DOC_TILA = 'Tila';
    const DOC_BC16_DRAFT = 'BC 1.6 Draft';
    const DOC_CONF = 'Confirmation';  
    const DOC_RECEIPT = 'Receipt';

    const RESERVED_DOCS = [self::DOC_DO, self::DOC_ATA, self::DOC_SPPB, self::DOC_TILA];

    /**
     * DocumentTypeModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $documentType
     * @return array
     */
    public function getReservedDocumentType($documentType)
    {
        $documentTypes = $this->db->from($this->table)
            ->where([
                'document_type' => $documentType,
                'is_deleted' => false
            ]);
        return $documentTypes->get()->row_array();
    }

    /**
     * Get document type by booking type.
     *
     * @param $idBookingType
     * @param null $uploadId
     * @return mixed
     */
    public function getByBookingType($idBookingType, $uploadId = null)
    {
        $documentTypes = $this->db->from($this->table)
            ->select([
                'ref_document_types.*',
                'ref_booking_document_types.is_required'
            ])
            ->join('ref_booking_document_types', 'ref_document_types.id = ref_booking_document_types.id_document_type')
            ->where('ref_booking_document_types.id_booking_type', $idBookingType)
            ->order_by('is_required DESC');

        if (!is_null($uploadId)) {
            $documentTypes->join("(SELECT * FROM upload_documents WHERE id_upload = '{$uploadId}' AND is_deleted = false) AS upload_documents", 'upload_documents.id_document_type = ref_document_types.id', 'left')
                ->where('upload_documents.id IS NULL');
        }

        return $documentTypes->get()->result_array();
    }

    /**
     * Create folder if it does not exist.
     *
     * @param $directory
     * @param null $base
     * @return bool
     */
    public function makeFolder($directory, $base = null)
    {
        $folderPath = $base;
        if (is_null($base)) {
            $folderPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $directory;
        }
        if (!file_exists($folderPath)) {
            return mkdir($folderPath);
        }
        return true;
    }

    /**
     * Rename folder to new one.
     *
     * @param $oldDirectory
     * @param $newDirectory
     * @param bool $renameOrCreate
     * @param null $base
     * @return bool
     */
    public function renameFolder($oldDirectory, $newDirectory, $renameOrCreate = true, $base = null)
    {
        $folderPath = $base;
        if (is_null($base)) {
            $folderPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR;
        }
        if (file_exists($folderPath . $oldDirectory)) {
            if ($oldDirectory != $newDirectory) {
                return rename($folderPath . $oldDirectory, $folderPath . $newDirectory);
            }
        } else if ($renameOrCreate) {
            return $this->makeFolder($newDirectory, $base);
        }
        return true;
    }

    /**
     * Get allocated doc type by customer.
     * @param $customerId
     * @return mixed
     */
    public function getDocumentTypeRemindersByCustomer($customerId)
    {
        $docTypes = $this->getBaseQuery()
            ->join('ref_people_document_type_reminders', 'ref_document_types.id = ref_people_document_type_reminders.id_document_type', 'left')
            ->where([
                'ref_people_document_type_reminders.id_customer' => $customerId,
                'ref_document_types.is_deleted' => false
            ]);

        return $docTypes->get()->result_array();
    }
}
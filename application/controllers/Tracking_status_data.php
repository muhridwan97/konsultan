<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Tracking_status_data
 * @property UploadModel $upload
 * @property UploadDocumentModel $uploadDocument
 * @property BookingModel $booking
 * @property CustomerInventoryInboundModel $customerInventoryInbound
 */
class Tracking_status_data extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('UploadModel', 'upload');
        $this->load->model('UploadDocumentModel', 'uploadDocument');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('CustomerInventoryInboundModel', 'customerInventoryInbound');

        $this->setFilterMethods([
            'search' => 'GET',
            'search_detail' => 'GET',
        ]);
    }

    /**
     * Tracking booking data view and result.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TRACKING_STATUS_DATA);

        $this->search();
    }

    public function search()
    {
        $type = get_url_param('type');
        $references = get_url_param('references', []);

        $data = [];
        foreach ($references as $index => $reference) {
            $data[$index] = [
                'id' => 0,
                'type' => $type,
                'reference' => trim($reference),
                'status' => 'Not Found',
                'description' => 'No data available',
            ];
        }

        foreach ($data as &$datum) {
            $upload = $this->upload->trackingUploadByType($datum['type'], $datum['reference']);

            $result = $this->searchData($upload);

            if (!empty($result)) {
                $datum['id'] = $result['id'];
                $datum['status'] = $result['status'];
                $datum['description'] = $result['description'];
                $datum['outbounds'] = $result['outbounds'];
            }
        }

        $this->render('tracking_status_data/search', compact('data'), 'Tracking Data');
    }

    public function search_detail($id)
    {
        $upload = $this->upload->getById($id);

        $statusResult = $this->searchData($upload);

        $this->render('tracking_status_data/search_detail', compact('statusResult'), 'Tracking Data');
    }

    private function searchData($upload)
    {
        $datum = [];

        if (!empty($upload)) {
            // 1. On-going Vessel
            $datum['id'] = $upload['id'];
            $datum['status'] = 'On-Going Vessel';
            $datum['description'] = 'Document received';
            $datum['documents'] = [
                'main_document' => $this->uploadDocument->getBy([
                    'upload_documents.id_upload' => $upload['id'],
                    'upload_documents.is_valid' => true,
                    'ref_document_types.id' => $upload['id_main_document'],
                ], true),
                'bl' => $this->uploadDocument->getBy([
                    'upload_documents.id_upload' => $upload['id'],
                    'upload_documents.is_valid' => true,
                    'ref_document_types.document_type' => 'Bill Of Loading',
                ], true),
                'invoice' => $this->uploadDocument->getBy([
                    'upload_documents.id_upload' => $upload['id'],
                    'upload_documents.is_valid' => true,
                    'ref_document_types.document_type' => 'Invoice',
                ], true),
                'eta' => $this->uploadDocument->getBy([
                    'upload_documents.id_upload' => $upload['id'],
                    'upload_documents.is_valid' => true,
                    'ref_document_types.document_type' => 'ETA',
                ], true),
                'packing_list' => $this->uploadDocument->getBy([
                    'upload_documents.id_upload' => $upload['id'],
                    'upload_documents.is_valid' => true,
                    'ref_document_types.document_type' => 'Packing List',
                ], true),
                'ata' => null,
                'sppd' => null,
            ];
            $datum['outbounds'] = [];

            // 2. Port of Discharge
            $validatedATA = $this->uploadDocument->getBy([
                'upload_documents.id_upload' => $upload['id'],
                'upload_documents.is_valid' => true,
                'ref_document_types.document_type' => 'ATA',
            ], true);
            if (!empty($validatedATA)) {
                $datum['status'] = 'Port of Discharge';
                $datum['description'] = 'Awaiting cargo readiness';
                $datum['documents']['ata'] = $validatedATA;
            }

            // 3. Complete Inbound
            $validatedSPPD = $this->uploadDocument->getBy([
                'upload_documents.id_upload' => $upload['id'],
                'upload_documents.is_valid' => true,
                'ref_document_types.document_type' => 'SPPD',
            ], true);
            if (!empty($validatedSPPD)) {
                $datum['status'] = 'Complete Inbound';
                $datum['description'] = 'Awaiting next instruction';
                $datum['documents']['sppd'] = $validatedATA;
            }

            // Outbound
            $outbounds = $this->upload->getBy([
                'uploads.is_valid' => true,
                'uploads.id_upload' => $upload['id'],
            ]);
            foreach ($outbounds as $outbound) {
                $bookingOut = $this->booking->getBookingById($outbound['id_booking']);
                $mainDocumentOutbound = $this->uploadDocument->getBy([
                    'upload_documents.id_upload' => $outbound['id'],
                    'upload_documents.is_valid' => true,
                    'ref_document_types.id' => $outbound['id_main_document'],
                ], true);

                // 4. Requested
                $datum['outbounds'][$outbound['id']] = [
                    'no_reference' => $bookingOut['no_reference'] ?? $mainDocumentOutbound['no_document'] ?? $outbound['description'],
                    'status' => 'Requested',
                    'description' => 'Outbound request received',
                    'documents' => [
                        'billing' => null,
                        'sppb' => null,
                        'sppd' => null,
                    ]
                ];

                // 5. Aw Cust. Complete
                $validatedLARTAS = $this->uploadDocument->getBy([
                    'upload_documents.id_upload' => $outbound['id'],
                    'upload_documents.is_valid' => true,
                    'ref_document_types.document_type' => 'LARTAS',
                ], true);
                if (!empty($validatedLARTAS)) {
                    $datum['outbounds'][$outbound['id']]['status'] = 'Aw Cust. Complete';
                    $datum['outbounds'][$outbound['id']]['description'] = 'LARTAS document needed';
                }

                $validatedBilling = $this->uploadDocument->getBy([
                    'upload_documents.id_upload' => $outbound['id'],
                    'upload_documents.is_valid' => true,
                    'ref_document_types.document_type' => 'E Billing',
                ], true);
                if (!empty($validatedBilling)) {
                    $datum['outbounds'][$outbound['id']]['status'] = 'Aw Cust. Complete';
                    $datum['outbounds'][$outbound['id']]['description'] = 'Awaiting payment';
                    $datum['outbounds'][$outbound['id']]['documents']['billing'] = $validatedBilling;
                }

                // 6. Customs Clearance
                $validatedSPPB = $this->uploadDocument->getBy([
                    'upload_documents.id_upload' => $outbound['id'],
                    'upload_documents.is_valid' => true,
                    'ref_document_types.document_type' => 'SPPB',
                ], true);
                if (!empty($validatedSPPB)) {
                    $datum['outbounds'][$outbound['id']]['status'] = 'Customs Clearance';
                    $datum['outbounds'][$outbound['id']]['description'] = 'Goods ready to go out';
                    $datum['outbounds'][$outbound['id']]['documents']['sppb'] = $validatedSPPB;
                }

                // 7. Complete Outbound
                $validatedSPPD = $this->uploadDocument->getBy([
                    'upload_documents.id_upload' => $outbound['id'],
                    'upload_documents.is_valid' => true,
                    'ref_document_types.document_type' => 'SPPD',
                ], true);
                if (!empty($validatedSPPD)) {
                    $datum['outbounds'][$outbound['id']]['status'] = 'Complete Outbound';
                    $datum['outbounds'][$outbound['id']]['description'] = 'All goods are out';
                    $datum['outbounds'][$outbound['id']]['documents']['sppd'] = $validatedSPPD;
                }

                // 8. Arrive
                $customerInventoryInbounds = $this->customerInventoryInbound->getComparisonInboundGoods([
                    'no_reference' => $bookingOut['no_reference'],
                ]);
                if (!empty($customerInventoryInbounds)) {
                    $customerInventoryInbound = $customerInventoryInbounds[0];
                    $datum['outbounds'][$outbound['id']]['status'] = 'Arrive';
                    $datum['outbounds'][$outbound['id']]['description'] = "Received " . numerical($customerInventoryInbound['total_received'], 2, true) . " / " . numerical($customerInventoryInbound['total_outbound'], 2, true);
                }

            }
        }

        return $datum;
    }
}
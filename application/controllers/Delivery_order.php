<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Delivery_order
 * @property DeliveryOrderModel $deliveryOrder
 * @property BookingModel $booking
 * @property DocumentTypeModel $documentType
 * @property UploadDocumentModel $uploadDocument
 * @property UploadDocumentFileModel $uploadDocumentFile
 * @property Exporter $exporter
 */
class Delivery_order extends CI_Controller
{
    /**
     * Delivery_order constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('DeliveryOrderModel', 'deliveryOrder');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('UploadDocumentModel', 'uploadDocument');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show all delivery order data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_ORDER_VIEW);

        $bookings = $this->deliveryOrder->getBookingETAStatus();

        if (get_url_param('export')) {
            $this->exporter->exportLargeResourceFromArray("Delivery trackings", $bookings);
        } else {
            $do = $this->documentType->getReservedDocumentType(DocumentTypeModel::DOC_DO);
            $ata = $this->documentType->getReservedDocumentType(DocumentTypeModel::DOC_ATA);
            $sppb = $this->documentType->getReservedDocumentType(DocumentTypeModel::DOC_SPPB);
            $tila = $this->documentType->getReservedDocumentType(DocumentTypeModel::DOC_TILA);

            $data = [
                'title' => "Delivery Orders",
                'subtitle' => "Data delivery order",
                'page' => "delivery_order/index",
                'doDocId' => get_if_exist($do, 'id'),
                'ataDocId' => get_if_exist($ata, 'id'),
                'sppbDocId' => get_if_exist($sppb, 'id'),
                'tilaDocId' => get_if_exist($tila, 'id'),
                'bookings' => $bookings
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Show view data delivery order.
     *
     * @param $bookingId
     */
    public function view($bookingId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_ORDER_VIEW);

        $booking = $this->booking->getBookingById($bookingId);
        $do = $this->documentType->getReservedDocumentType(DocumentTypeModel::DOC_DO);
        $ata = $this->documentType->getReservedDocumentType(DocumentTypeModel::DOC_ATA);
        $sppb = $this->documentType->getReservedDocumentType(DocumentTypeModel::DOC_SPPB);
        $tila = $this->documentType->getReservedDocumentType(DocumentTypeModel::DOC_TILA);

        if(empty($do) && empty($ata) && empty($sppb) && empty($tila)) {
            show_error('Reserved document DO is missing, please contact your administrator');
        }

        $uploadedDo = $this->uploadDocument->getDocumentsByBooking($bookingId, get_if_exist($do, 'id'));
        if(!empty($uploadedDo)) {
            $uploadedDo['files'] = $this->uploadDocumentFile->getFilesByBooking($bookingId, get_if_exist($do, 'id'));
        }

        $uploadedAta = $this->uploadDocument->getDocumentsByBooking($bookingId, get_if_exist($ata, 'id'));
        if(!empty($uploadedAta)) {
            $uploadedAta['files'] = $this->uploadDocumentFile->getFilesByBooking($bookingId, get_if_exist($ata, 'id'));
        }

        $uploadedSppb = $this->uploadDocument->getDocumentsByBooking($bookingId, get_if_exist($sppb, 'id'));
        if(!empty($uploadedSppb)) {
            $uploadedSppb['files'] = $this->uploadDocumentFile->getFilesByBooking($bookingId, get_if_exist($sppb, 'id'));
        }

        $uploadedTila = $this->uploadDocument->getDocumentsByBooking($bookingId, get_if_exist($tila, 'id'));
        if(!empty($uploadedTila)) {
            $uploadedTila['files'] = $this->uploadDocumentFile->getFilesByBooking($bookingId, get_if_exist($tila, 'id'));
        }

        $data = [
            'title' => "View Delivery Orders",
            'subtitle' => "Delivery order",
            'page' => "delivery_order/view",
            'booking' => $booking,
            'do' => $uploadedDo,
            'ata' => $uploadedAta,
            'sppb' => $uploadedSppb,
            'tila' => $uploadedTila,
            'doDocId' => get_if_exist($do, 'id'),
            'ataDocId' => get_if_exist($ata, 'id'),
            'sppbDocId' => get_if_exist($sppb, 'id'),
            'tilaDocId' => get_if_exist($tila, 'id'),
        ];
        $this->load->view('template/layout', $data);
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Inbound_progress
 * @property UploadModel $upload
 * @property SafeConductModel $safeConduct
 */
class Inbound_progress extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UploadModel', 'upload');
        $this->load->model('SafeConductModel', 'safeConduct');

        $this->setFilterMethods([
            'sppb_to_inbound' => 'GET',
            'sppb_no_inbound_today' => 'GET',
            'sppb_no_inbound_before_today' => 'GET',
            'sppb_inbound_in_progress' => 'GET',
            'inbound_no_stripping_today' => 'GET',
            'inbound_no_stripping_before_today' => 'GET',
            'inbound_stripping_in_progress' => 'GET',
        ]);
    }

    /**
     * Get container from job with expired DO
     */
    public function sppb_to_inbound()
    {
        $filters = [
            'date_type' => 'sppb_uploaded_at',
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d'),
            'total_moving_in_from' => 0,
            'total_moving_in_to' => 0,
        ];
        $sppbNoInboundToday = $this->upload->getSppbOperationProgress($filters);

        $filters = [
            'date_type' => 'sppb_uploaded_at',
            'date_to' => date('Y-m-d', strtotime('-1 day')),
            'total_moving_in_from' => 0,
            'total_moving_in_to' => 0,
        ];
        $sppbNoInboundBeforeToday = $this->upload->getSppbOperationProgress($filters);

        $filters = [
            'date_type' => 'booking_completed_at',
            'date' => null,
            'total_moving_in_from' => 1,
        ];
        $sppbInboundInProgress = $this->upload->getSppbOperationProgress($filters);

        $filters = [
            'date_type' => 'security_out_date',
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d'),
            'stripping_completed_at' => null
        ];
        $inboundNoStrippingToday = $this->safeConduct->getInboundProgress($filters);

        $filters = [
            'date_type' => 'security_out_date',
            'date_to' => date('Y-m-d', strtotime('-1 day')),
            'stripping_completed_at' => null
        ];
        $inboundNoStrippingBeforeToday = $this->safeConduct->getInboundProgress($filters);

        $filters = [
            'stripping_taken_at' => 'in progress',
            'stripping_completed_at' => null
        ];
        $inboundStrippingInProgress = $this->safeConduct->getInboundProgress($filters);

        $statistic = [
            'sppb_no_inbound_today' => count($sppbNoInboundToday),
            'sppb_no_inbound_before_today' => count($sppbNoInboundBeforeToday),
            'sppb_inbound_in_progress' => count($sppbInboundInProgress),
            'inbound_no_stripping_today' => count($inboundNoStrippingToday),
            'inbound_no_stripping_before_today' => count($inboundNoStrippingBeforeToday),
            'inbound_stripping_in_progress' => count($inboundStrippingInProgress),
        ];

        $this->render_json($statistic);
    }

    /**
     * Get sppb no inbound today.
     */
    public function sppb_no_inbound_today()
    {
        $filters = [
            'date_type' => 'sppb_uploaded_at',
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d'),
            'total_moving_in_from' => 0,
            'total_moving_in_to' => 0,
        ];
        $sppbNoInboundToday = $this->upload->getSppbOperationProgress($filters);

        $this->render_json($sppbNoInboundToday);
    }

    /**
     * Get sppb no inbound before today.
     */
    public function sppb_no_inbound_before_today()
    {
        $filters = [
            'date_type' => 'sppb_uploaded_at',
            'date_to' => date('Y-m-d', strtotime('-1 day')),
            'total_moving_in_from' => 0,
            'total_moving_in_to' => 0,
        ];
        $sppbNoInboundBeforeToday = $this->upload->getSppbOperationProgress($filters);

        $this->render_json($sppbNoInboundBeforeToday);
    }

    /**
     * Get sppb inbound in progress.
     */
    public function sppb_inbound_in_progress()
    {
        $filters = [
            'date_type' => 'booking_completed_at',
            'date' => null,
            'total_moving_in_from' => 1,
        ];
        $sppbInboundInProgress = $this->upload->getSppbOperationProgress($filters);

        $this->render_json($sppbInboundInProgress);
    }

    /**
     * Get sppb no inbound today.
     */
    public function inbound_no_stripping_today()
    {
        $filters = [
            'date_type' => 'security_out_date',
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d'),
            'stripping_completed_at' => null
        ];
        $inboundNoStrippingToday = $this->safeConduct->getInboundProgress($filters);

        $this->render_json($inboundNoStrippingToday);
    }

    /**
     * Get inbound no stripping before today.
     */
    public function inbound_no_stripping_before_today()
    {
        $filters = [
            'date_type' => 'security_out_date',
            'date_to' => date('Y-m-d', strtotime('-1 day')),
            'stripping_completed_at' => null
        ];
        $inboundNoStrippingBeforeToday = $this->safeConduct->getInboundProgress($filters);

        $this->render_json($inboundNoStrippingBeforeToday);
    }

    /**
     * Get inbound stripping in progress.
     */
    public function inbound_stripping_in_progress()
    {
        $filters = [
            'stripping_taken_at' => 'in progress',
            'stripping_completed_at' => null
        ];
        $inboundStrippingInProgress = $this->safeConduct->getInboundProgress($filters);

        $this->render_json($inboundStrippingInProgress);
    }
}
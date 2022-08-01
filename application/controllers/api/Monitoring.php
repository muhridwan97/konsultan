<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Cash_bond
 * @property UploadModel $upload
 */
class Monitoring extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UploadModel', 'upload');
        $this->load->model('WorkOrderContainerModel', 'WorkOrderContainer');

        $this->setFilterMethods([
            'upload_start_to_review' => 'GET',
            'upload_review_to_draft' => 'GET',
            'upload_draft_to_confirm' => 'GET',
            'upload_confirm_to_do' => 'GET',
            'upload_confirm_do_to_sppb' => 'GET',
            'upload_sppb_to_sppd' => 'GET',
            'upload_start_to_review_outbound' => 'GET',
            'upload_review_to_draft_outbound' => 'GET',
            'upload_draft_to_confirm_outbound' => 'GET',
            'upload_confirm_to_sppd_outbound' => 'GET',
            'upload_sppd_to_billing_outbound' => 'GET',
            'upload_billing_to_bpn_outbound' => 'GET',
            'upload_bpn_to_sppb_outbound' => 'GET',
            'kpi_draft_revision' => 'GET',
            'kpi_confirm_sppb' => 'GET',
            'kpi_sppb_coo_receipt' => 'GET',
            'kpi_complete_sppd' => 'GET',
            'kpi_sppd_in_billing' => 'GET',
            'kpi_bpn_sppb' => 'GET',
            'kpi_summary' => 'GET',
            'empty_container_return' => 'GET',
            'container_without_do' => 'GET',
        ]);
    }

    /**
     * Get container from job with expired DO
     */
    public function empty_container_return()
    {
        $monitoring = $this->upload->getEmptyContainerReturn();

        $this->render_json($monitoring);
    }

    /**
     * Get container from job without DO
     */
    public function container_without_do()
    {
        $monitoring = $this->upload->getContainerWithoutDO();

        $this->render_json($monitoring);
    }

    /**
     * Get upload start to review data.
     */
    public function upload_start_to_review()
    {
        $monitoring = $this->upload->getMonitoringStartToReview();

        $this->render_json($monitoring);
    }

    /**
     * Get upload on review to draft data.
     */
    public function upload_review_to_draft()
    {
        $monitoring = $this->upload->getMonitoringReviewToDraft();

        $this->render_json($monitoring);
    }

    /**
     * Get upload draft to confirm data.
     */
    public function upload_draft_to_confirm()
    {
        $monitoring = $this->upload->getMonitoringDraftToConfirm();

        $this->render_json($monitoring);
    }

    /**
     * Get upload confirm to DO data.
     */
    public function upload_confirm_to_do()
    {
        $monitoring = $this->upload->getMonitoringConfirmToDO();

        $this->render_json($monitoring);
    }

    /**
     * Get upload confirm/DO to SPPB data.
     */
    public function upload_confirm_do_to_sppb()
    {
        $monitoring = $this->upload->getMonitoringConfirmDoToSppb();

        $this->render_json($monitoring);
    }

    /**
     * Get upload sppb to sppd data.
     */
    public function upload_sppb_to_sppd()
    {
        $monitoring = $this->upload->getMonitoringSppbToSppd();

        $this->render_json($monitoring);
    }

    /**
     * Get upload start to review outbound data.
     */
    public function upload_start_to_review_outbound()
    {
        $monitoring = $this->upload->getMonitoringStartToReviewOutbound();

        $this->render_json($monitoring);
    }

     /**
     * Get upload on review to draft outbound data.
     */
    public function upload_review_to_draft_outbound()
    {
        $monitoring = $this->upload->getMonitoringReviewToDraftOut();

        $this->render_json($monitoring);
    }

    /**
     * Get upload draft to confirm outbound data.
     */
    public function upload_draft_to_confirm_outbound()
    {
        $monitoring = $this->upload->getMonitoringDraftToConfirmOut();

        $this->render_json($monitoring);
    }

    /**
     * Get upload confirm to sppd outbound data.
     */
    public function upload_confirm_to_sppd_outbound()
    {
        $monitoring = $this->upload->getMonitoringConfirmToSppdOut();

        $this->render_json($monitoring);
    } 

    /**
     * Get upload sppd to billing outbound data.
     */
    public function upload_sppd_to_billing_outbound()
    {
        $monitoring = $this->upload->getMonitoringSppdToBillingOut();

        $this->render_json($monitoring);
    } 

    /**
     * Get upload billing to bpn outbound data.
     */
    public function upload_billing_to_bpn_outbound()
    {
        $monitoring = $this->upload->getMonitoringBillingToBpnOut();

        $this->render_json($monitoring);
    } 

     /**
     * Get upload bpn to sppb outbound data.
     */
    public function upload_bpn_to_sppb_outbound()
    {
        $monitoring = $this->upload->getMonitoringBpnToSppb();

        $this->render_json($monitoring);
    }

    /**
     * Get kpi draft to revision
     */
    public function kpi_draft_revision()
    {   
        $filters = [
            'summary' => get_url_param('summary'),
            'author' => urldecode(get_url_param('author')),
            'branch' => urldecode(get_url_param('branch')),
            'booking_category' => urldecode(get_url_param('booking_category')),
            'year' => urldecode(get_url_param('year')),
            'month' => urldecode(get_url_param('month')),
        ];
        $kpi = $this->upload->getKpiDraftRevision($filters);

        $this->render_json($kpi);
    }

    /**
     * Get kpi confirm to sppb
     */
    public function kpi_confirm_sppb()
    {
        $filters = [
            'summary' => get_url_param('summary'),
            'author' => urldecode(get_url_param('author')),
            'branch' => urldecode(get_url_param('branch')),
            'booking_category' => urldecode(get_url_param('booking_category')),
            'year_confirm' => urldecode(get_url_param('year_confirm')),
            'month_confirm' => urldecode(get_url_param('month_confirm')),
        ];
        $kpi = $this->upload->getKpiConfirmSppb($filters);

        $this->render_json($kpi);
    }

    /**
     * Get sppb to coo receipt
     */
    public function kpi_sppb_coo_receipt()
    {
        $filters = [
            'summary' => get_url_param('summary'),
            'author' => urldecode(get_url_param('author')),
            'branch' => urldecode(get_url_param('branch')),
            'booking_category' => urldecode(get_url_param('booking_category')),
            'year_sppb' => urldecode(get_url_param('year_sppb')),
            'month_sppb' => urldecode(get_url_param('month_sppb')),
        ];
        $kpi = $this->upload->getKpiSppbCooReceipt($filters);

        $this->render_json($kpi);
    }

    /**
     * Get transaction complete sppd
     */
    public function kpi_complete_sppd()
    {
        $filters = [
            'summary' => get_url_param('summary'),
            'author' => urldecode(get_url_param('author')),
            'branch' => urldecode(get_url_param('branch')),
            'booking_category' => urldecode(get_url_param('booking_category')),
            'year_completed' => urldecode(get_url_param('year_completed')),
            'month_completed' => urldecode(get_url_param('month_completed')),
        ];
        $kpi = $this->upload->getKpiCompleteSppd($filters);

        $this->render_json($kpi);
    }

    /**
     * Get transaction sppd in billing
     */
    public function kpi_sppd_in_billing()
    {
        $filters = [
            'summary' => get_url_param('summary'),
            'author' => urldecode(get_url_param('author')),
            'branch' => urldecode(get_url_param('branch')),
            'booking_category' => urldecode(get_url_param('booking_category')),
            'year_confirm' => urldecode(get_url_param('year_confirm')),
            'month_confirm' => urldecode(get_url_param('month_confirm')),
        ];
        $kpi = $this->upload->getKpiSppdInBilling($filters);
        $this->render_json($kpi);
    }

    /**
     * Get transaction bpn sppb
     */
    public function kpi_bpn_sppb()
    {
        $filters = [
            'summary' => get_url_param('summary'),
            'author' => urldecode(get_url_param('author')),
            'branch' => urldecode(get_url_param('branch')),
            'booking_category' => urldecode(get_url_param('booking_category')),
            'year_bpn' => urldecode(get_url_param('year_bpn')),
            'month_bpn' => urldecode(get_url_param('month_bpn')),
        ];
        $kpi = $this->upload->getKpiBpnSppb($filters);

        $this->render_json($kpi);
    }

    /**
     * Get summary data.
     */
    public function kpi_summary()
    {
        $type = get_url_param('summary_type', 'author');
        $summaryType = in_array($type, ['author', 'branch']) ? $type : '';

        $draftFilters = [
            'summary' => true,
            'summary_type' => $summaryType,
            'booking_category' => urldecode(get_url_param('booking_category')),
            'year' => urldecode(get_url_param('year')),
            'month' => urldecode(get_url_param('month')),
        ];
        $draftRevision = $this->upload->getKpiDraftRevision($draftFilters);

        $confirmFilters = [
            'summary' => true,
            'summary_type' => $summaryType,
            'booking_category' => urldecode(get_url_param('booking_category')),
            'year_confirm' => urldecode(get_url_param('year')),
            'month_confirm' => urldecode(get_url_param('month')),
        ];
        $confirmSppb = $this->upload->getKpiConfirmSppb($confirmFilters);

        $sppbFilters = [
            'summary' => true,
            'summary_type' => $summaryType,
            'booking_category' => urldecode(get_url_param('booking_category')),
            'year_sppb' => urldecode(get_url_param('year')),
            'month_sppb' => urldecode(get_url_param('month')),
        ];
        $sppbCooReceipt = $this->upload->getKpiSppbCooReceipt($sppbFilters);

        $completedFilters = [
            'summary' => true,
            'summary_type' => $summaryType,
            'booking_category' => urldecode(get_url_param('booking_category')),
            'year_completed' => urldecode(get_url_param('year')),
            'month_completed' => urldecode(get_url_param('month')),
        ];
        $completeSppd = $this->upload->getKpiCompleteSppd($completedFilters);

        $confirmSppdInFilters = [
            'summary' => true,
            'summary_type' => $summaryType,
            'booking_category' => urldecode(get_url_param('booking_category')),
            'year_confirm' => urldecode(get_url_param('year')),
            'month_confirm' => urldecode(get_url_param('month')),
        ];
        $sppdInBilling = $this->upload->getKpiSppdInBilling($confirmSppdInFilters);

        $bpnFilters = [
            'summary' => true,
            'summary_type' => $summaryType,
            'booking_category' => urldecode(get_url_param('booking_category')),
            'year_bpn' => urldecode(get_url_param('year')),
            'month_bpn' => urldecode(get_url_param('month')),
        ];
        $bpnSppb = $this->upload->getKpiBpnSppb($bpnFilters);

        $groups = array_merge(array_column($draftRevision, $summaryType), array_column($confirmSppb, $summaryType), array_column($sppbCooReceipt, $summaryType), array_column($completeSppd, $summaryType), array_column($sppdInBilling, $summaryType), array_column($bpnSppb, $summaryType));
        $groups = array_unique($groups);

        $summaries = [];
        foreach ($groups as $group) {
            foreach ($draftRevision as $data) {
                if ($data[$summaryType] == $group) {
                    $summaries[$group]['draft_revision_score'] = $data['draft_revision_score'];
                    $summaries[$group]['draft_revision_docs'] = $data['draft_revision_docs'];
                    $summaries[$group]['draft_revision_percent'] = $data['draft_revision_percent'];
                }
            }

            foreach ($confirmSppb as $data) {
                if ($data[$summaryType] == $group) {
                    $summaries[$group]['confirm_sppb_score'] = $data['confirm_sppb_score'];
                    $summaries[$group]['confirm_sppb_docs'] = $data['confirm_sppb_docs'];
                    $summaries[$group]['confirm_sppb_percent'] = $data['confirm_sppb_percent'];
                }
            }

            foreach ($sppbCooReceipt as $data) {
                if ($data[$summaryType] == $group) {
                    $summaries[$group]['sppb_coo_receipt_score'] = $data['sppb_coo_receipt_score'];
                    $summaries[$group]['sppb_coo_receipt_docs'] = $data['sppb_coo_receipt_docs'];
                    $summaries[$group]['sppb_coo_receipt_percent'] = $data['sppb_coo_receipt_percent'];
                }
            }

            foreach ($completeSppd as $data) {
                if ($data[$summaryType] == $group) {
                    $summaries[$group]['complete_sppd_score'] = $data['complete_sppd_score'];
                    $summaries[$group]['complete_sppd_docs'] = $data['complete_sppd_docs'];
                    $summaries[$group]['complete_sppd_percent'] = $data['complete_sppd_percent'];
                }
            }

            foreach ($sppdInBilling as $data) {
                if ($data[$summaryType] == $group) {
                    $summaries[$group]['sppdIn_billing_score'] = $data['sppdIn_billing_score'];
                    $summaries[$group]['sppdIn_billing_docs'] = $data['sppdIn_billing_docs'];
                    $summaries[$group]['sppdIn_billing_percent'] = $data['sppdIn_billing_percent'];
                }
            }

            foreach ($bpnSppb as $data) {
                if ($data[$summaryType] == $group) {
                    $summaries[$group]['bpn_sppb_score'] = $data['bpn_sppb_score'];
                    $summaries[$group]['bpn_sppb_docs'] = $data['bpn_sppb_docs'];
                    $summaries[$group]['bpn_sppb_percent'] = $data['bpn_sppb_percent'];
                }
            }
        }

        foreach ($groups as $group) {
            if (!key_exists('draft_revision_score', $summaries[$group])) {
                $summaries[$group]['draft_revision_score'] = 0;
                $summaries[$group]['draft_revision_docs'] = 0;
                $summaries[$group]['draft_revision_percent'] = 0;
            }
            if (!key_exists('confirm_sppb_score', $summaries[$group])) {
                $summaries[$group]['confirm_sppb_score'] = 0;
                $summaries[$group]['confirm_sppb_docs'] = 0;
                $summaries[$group]['confirm_sppb_percent'] = 0;
            }
            if (!key_exists('sppb_coo_receipt_score', $summaries[$group])) {
                $summaries[$group]['sppb_coo_receipt_score'] = 0;
                $summaries[$group]['sppb_coo_receipt_docs'] = 0;
                $summaries[$group]['sppb_coo_receipt_percent'] = 0;
            }
            if (!key_exists('complete_sppd_score', $summaries[$group])) {
                $summaries[$group]['complete_sppd_score'] = 0;
                $summaries[$group]['complete_sppd_docs'] = 0;
                $summaries[$group]['complete_sppd_percent'] = 0;
            }
            if (!key_exists('sppdIn_billing_score', $summaries[$group])) {
                $summaries[$group]['sppdIn_billing_score'] = 0;
                $summaries[$group]['sppdIn_billing_docs'] = 0;
                $summaries[$group]['sppdIn_billing_percent'] = 0;
            }
            if (!key_exists('bpn_sppb_score', $summaries[$group])) {
                $summaries[$group]['bpn_sppb_score'] = 0;
                $summaries[$group]['bpn_sppb_docs'] = 0;
                $summaries[$group]['bpn_sppb_percent'] = 0;
            }

        }

        $result = [];
        foreach ($summaries as $key => $data) {
            $result[] = array_merge([$summaryType => $key], $data);
        }

        $this->render_json($result);
    }
}
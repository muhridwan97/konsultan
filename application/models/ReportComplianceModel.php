<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ReportComplianceModel extends MY_Model
{
    protected $table = 'uploads';

    /**
     * UploadModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel', 'user');
        $this->load->model('UploadDocumentModel', 'uploadDocument');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('UploadModel', 'upload');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('BookingTypeModel', 'bookingType');
    }
    public function getAll($filters = [], $withTrashed = false)
    {
        $date = date('Y-m-d');
        $tgl_pertama = date('Y-m-d', 1);
        $tgl_terakhir = date('Y-m-t', strtotime($date));
        unset($filters['filter_report_compliance']);
        
        if (empty($filters['date_from'])) {
            $filters['date_from'] = $tgl_pertama;
        } else {
            $filters['date_from'] = date('Y-m-d', strtotime($filters['date_from']));
        }
        if (empty($filters['date_to'])) {
            $filters['date_to'] = $tgl_terakhir;
        } else {
            $filters['date_to'] = date('Y-m-d', strtotime($filters['date_to']));
        }

        $filtersUser = $filters;
        $filtersUpload = $filters;
        if (empty($filters['pic'])) {
            $users = $this->user->getBy(['user_type' => 'INTERNAL']);
        } else {
            $filtersUser['prv_users.id'] = $filtersUser['pic'];
            unset($filtersUser['pic']);
            unset($filtersUser['customers']);
            unset($filtersUser['date_from']);
            unset($filtersUser['date_to']);
            unset($filtersUser['branch']);
            unset($filtersUser['export']);
            unset($filtersUser['doc_type']);
            $users = $this->user->getBy($filtersUser);
        }
        if (empty($filters['customers'])) {
            if (empty($filters['branch'])) {
                $uploads = $this->upload->getAll(['branch' => null,'dashboard_status'=>1]);
            } else {
                $uploads = $this->upload->getAll(['branch' => $filters['branch'],'dashboard_status'=>1]);
            }
        } else {
            $filtersUpload['id_person'] = $filtersUpload['customers'];
            $filtersUpload['ref_branches.dashboard_status'] = '1';
            if (!empty($filters['branch'])) {
                $filtersUpload['ref_branches.id'] = $filtersUpload['branch'];
            }
            unset($filtersUpload['branch']);
            unset($filtersUpload['pic']);
            unset($filtersUpload['customers']);
            unset($filtersUpload['user_type']);
            unset($filtersUpload['date_from']);
            unset($filtersUpload['date_to']);
            unset($filtersUpload['export']);
            unset($filtersUpload['doc_type']);
            $uploads = $this->upload->getBy($filtersUpload);
        }
                
        $data_reports = [];
        $i = 0;
        foreach ($uploads as $upload) {
            $documents = $this->uploadDocument->getDocumentsByUpload($upload['id']);
            foreach ($documents as $document) {

                if (date('Y-m-d', strtotime($document['created_at'])) >= $filters['date_from'] && date('Y-m-d', strtotime($document['created_at'])) <= $filters['date_to']) {

                    if ($upload['id_document_type'] == $document['id_document_type']) {

                        foreach ($users as $user) {
                            $document['name_customer'] = '';
                            $document['no_upload'] = '';
                            $document['branch_name'] = '';
                            $document['draft_service_time'] = '';
                            $document['confirm_service_time'] = '';
                            $document['total_items'] = '';
                            unset($document['id']);
                            unset($document['subtype']);
                            unset($document['description']);
                            unset($document['expired_date']);
                            unset($document['freetime_date']);
                            unset($document['is_response']);
                            unset($document['is_valid']);
                            unset($document['validated_by']);
                            unset($document['is_check']);
                            unset($document['checked_by']);
                            unset($document['checked_at']);
                            unset($document['service_time_document']);
                            unset($document['is_void']);
                            unset($document['is_deleted']);
                            // unset($document['created_at']);
                            unset($document['updated_at']);
                            unset($document['updated_by']);
                            unset($document['deleted_at']);
                            unset($document['deleted_by']);
                            unset($document['directory']);
                            unset($document['total_file']);
                            
                            if ($document['created_by'] == $user['id']&&$document['created_at']!='') {
                                if (!isset($data_reports[$i])) {
                                    $data_reports[$i] = $document;
                                }
                                
                                // search draft data
                                $input_draft = "draft";
                                $search_draft = array_filter($documents, function($item_draft) use($input_draft) {
                                    if (stripos($item_draft['document_type'], $input_draft) !== false) {
                                        return true;
                                    }
                                    return false;
                                });
                                $draft_data = array_shift($search_draft);
                                $draft_detail = $this->uploadDocumentFile->getFilesByDocument($draft_data['id']);
                                $count_draft_detail = array_column($draft_detail, 'created_at');
                                array_multisort($count_draft_detail, SORT_ASC, $draft_detail);
                                $oldest_draft_detail = [];
                                if(!empty($draft_detail)){
                                    $oldest_draft_detail = min($draft_detail);
                                }

                                // get draft response time
                                if(!empty($oldest_draft_detail) && !empty($upload)){
                                    $draft_service_time = round((strtotime($oldest_draft_detail['created_at']) - strtotime($upload['created_at']))/3600, 1);
                                }else{
                                    $draft_service_time = 0;
                                }

                                // search confirm data
                                $input_confirm = "confirm";
                                $search_confirm = array_filter($documents, function($item_confirm) use($input_confirm) {
                                    if (stripos($item_confirm['document_type'], $input_confirm) !== false) {
                                        return true;
                                    }
                                    return false;
                                });
                                $confirm_data = array_shift($search_confirm);

                                //search old revise data
                                $search_old_revise = array_filter($draft_detail, function($item_draft_detail) {
                                    if (!is_null($item_draft_detail['description_date'])) {
                                        return true;
                                    }
                                    return false;
                                });
                                $old_revise_data = [];
                                if(!empty($search_old_revise)){
                                    $old_revise_data = min($search_old_revise);
                                }

                                // get confirm response time
                                if(!empty($confirm_data) && !empty($old_revise_data)){
                                    $confirm_service_time = round((strtotime($confirm_data['created_at']) - strtotime($old_revise_data['description_date']))/3600, 1);
                                }else{
                                    $confirm_service_time = 0;
                                }

                                if(!empty($upload['id_booking']) && !is_null($upload['id_booking'])){
                                    $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($upload['id_booking']);
                                    $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($upload['id_booking']);
                                    $totalGoods = count(array_unique(array_column($bookingGoods, 'id')));
                                    $totalContainers = count(array_unique(array_column($bookingContainers, 'id')));
                                    $totalItems = $totalGoods+$totalContainers;
                                }else{
                                    $totalItems = "not yet booked";    
                                }

                                $upload_date = $upload['created_at'];
                                $data_reports[$i]['name_customer'] = $upload['name'];
                                $data_reports[$i]['no_upload'] = $upload['no_upload'];
                                $data_reports[$i]['branch_name'] = $upload['branch_name'];
                                $data_reports[$i]['draft_service_time'] = $draft_service_time;
                                $data_reports[$i]['confirm_service_time'] = $confirm_service_time;
                                $data_reports[$i]['total_items'] = $totalItems;
                                $data_reports[$i]['jenis_doc'] = 'SPPB';
                                $i++;
                            }
                        }
                    }
                    
                    if ($upload['main_docs_name'].' Draft' == $document['document_type']) {

                        foreach ($users as $user) {
                            $document['name_customer'] = '';
                            $document['no_upload'] = '';
                            $document['branch_name'] = '';
                            $document['draft_service_time'] = '';
                            $document['confirm_service_time'] = '';
                            $document['total_items'] = '';
                            unset($document['id']);
                            unset($document['subtype']);
                            unset($document['description']);
                            unset($document['expired_date']);
                            unset($document['freetime_date']);
                            unset($document['is_response']);
                            unset($document['is_valid']);
                            unset($document['validated_by']);
                            unset($document['is_check']);
                            unset($document['checked_by']);
                            unset($document['checked_at']);
                            unset($document['service_time_document']);
                            unset($document['is_void']);
                            unset($document['is_deleted']);
                            // unset($document['created_at']);
                            unset($document['updated_at']);
                            unset($document['updated_by']);
                            unset($document['deleted_at']);
                            unset($document['deleted_by']);
                            unset($document['directory']);
                            unset($document['total_file']);
                            
                            if ($document['created_by'] == $user['id']&&$document['created_at']!='') {
                                if (!isset($data_reports[$i])) {
                                    $data_reports[$i] = $document;
                                }
                                
                                // search draft data
                                $input_draft = "draft";
                                $search_draft = array_filter($documents, function($item_draft) use($input_draft) {
                                    if (stripos($item_draft['document_type'], $input_draft) !== false) {
                                        return true;
                                    }
                                    return false;
                                });
                                $draft_data = array_shift($search_draft);
                                $draft_detail = $this->uploadDocumentFile->getFilesByDocument($draft_data['id']);
                                $count_draft_detail = array_column($draft_detail, 'created_at');
                                array_multisort($count_draft_detail, SORT_ASC, $draft_detail);
                                $oldest_draft_detail = [];
                                if(!empty($draft_detail)){
                                    $oldest_draft_detail = min($draft_detail);
                                }

                                // get draft response time
                                if(!empty($oldest_draft_detail) && !empty($upload)){
                                    $draft_service_time = round((strtotime($oldest_draft_detail['created_at']) - strtotime($upload['created_at']))/3600, 1);
                                }else{
                                    $draft_service_time = 0;
                                }

                                // search confirm data
                                $input_confirm = "confirm";
                                $search_confirm = array_filter($documents, function($item_confirm) use($input_confirm) {
                                    if (stripos($item_confirm['document_type'], $input_confirm) !== false) {
                                        return true;
                                    }
                                    return false;
                                });
                                $confirm_data = array_shift($search_confirm);

                                //search old revise data
                                $search_old_revise = array_filter($draft_detail, function($item_draft_detail) {
                                    if (!is_null($item_draft_detail['description_date'])) {
                                        return true;
                                    }
                                    return false;
                                });
                                $old_revise_data = [];
                                if(!empty($search_old_revise)){
                                    $old_revise_data = min($search_old_revise);
                                }

                                // get confirm response time
                                if(!empty($confirm_data) && !empty($old_revise_data)){
                                    $confirm_service_time = round((strtotime($confirm_data['created_at']) - strtotime($old_revise_data['description_date']))/3600, 1);
                                }else{
                                    $confirm_service_time = 0;
                                }

                                if(!empty($upload['id_booking']) && !is_null($upload['id_booking'])){
                                    $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($upload['id_booking']);
                                    $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($upload['id_booking']);
                                    $totalGoods = count(array_unique(array_column($bookingGoods, 'id')));
                                    $totalContainers = count(array_unique(array_column($bookingContainers, 'id')));
                                }
                                $totalItems = $document['total_item'];

                                $upload_date = $upload['created_at'];
                                $data_reports[$i]['name_customer'] = $upload['name'];
                                $data_reports[$i]['no_upload'] = $upload['no_upload'];
                                $data_reports[$i]['branch_name'] = $upload['branch_name'];
                                $data_reports[$i]['draft_service_time'] = $draft_service_time;
                                $data_reports[$i]['confirm_service_time'] = $confirm_service_time;
                                $data_reports[$i]['total_items'] = (integer)$totalItems;
                                $data_reports[$i]['jenis_doc'] = 'DRAFT';
                                $i++;
                            }
                        }
                    }
                }
            }
        }
        if (!empty($filters['doc_type'])) {
            $data_reportsFilterDoc=[];
            $i=0;
            foreach ($data_reports as $data_report) {
                if (strtolower($data_report['jenis_doc'])==$filters['doc_type']) {
                    $data_reportsFilterDoc[$i] = $data_report;
                    $i++;
                }
            }
            if ($filters['doc_type']=='all') {
                return $data_reports;
            }
            return $data_reportsFilterDoc;
        }
        return $data_reports;
    }
    public function getAllWithoutFilter()
    {
        $users = $this->user->getBy(['user_type' => 'INTERNAL']);

        $uploads = $this->upload->getAll(['branch' => null,'dashboard_status'=>1]);
        $data_reports = [];
        $i = 0;
        foreach ($uploads as $upload) {
            $documents = $this->uploadDocument->getDocumentsByUpload($upload['id']);
            foreach ($documents as $document) {
                if ($upload['id_document_type'] == $document['id_document_type']) {

                    foreach ($users as $user) {
                        $document['name_customer'] = '';
                        $document['no_upload'] = '';
                        $document['branch_name'] = '';
                        $document['draft_service_time'] = '';
                        $document['confirm_service_time'] = '';
                        $document['total_items'] = '';
                        unset($document['id']);
                        unset($document['subtype']);
                        unset($document['description']);
                        unset($document['expired_date']);
                        unset($document['freetime_date']);
                        unset($document['is_response']);
                        unset($document['is_valid']);
                        unset($document['validated_by']);
                        unset($document['is_check']);
                        unset($document['checked_by']);
                        unset($document['checked_at']);
                        unset($document['service_time_document']);
                        unset($document['is_void']);
                        unset($document['is_deleted']);
                        // unset($document['created_at']);
                        unset($document['updated_at']);
                        unset($document['updated_by']);
                        unset($document['deleted_at']);
                        unset($document['deleted_by']);
                        unset($document['directory']);
                        unset($document['total_file']);

                        if ($document['created_by'] == $user['id']&&$document['created_at']!='') {

                            if (!isset($data_reports[$i])) {
                                $data_reports[$i] = $document;
                            }
                            $data_reports[$i]['name_customer'] = $upload['name'];
                            $data_reports[$i]['no_upload'] = $upload['no_upload'];

                            $i++;
                        }
                    }
                }
            }
        }
        return $data_reports;
    }
}

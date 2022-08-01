<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Security_photo_type
 * @property SecurityCheckPhotoTypeModel $securityCheckPhotoType
 */
class Security_photo_type extends MY_Controller
{
    /**
     * Security_photo_type constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('SecurityCheckPhotoTypeModel', 'securityCheckPhotoType');
    }

    /**
     * Show security category list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        $data = [
            'title' => "Security Setting",
            'subtitle' => "Photo check configurations",
            'page' => "security_photo_type/index",
            'inbound' => $this->securityCheckPhotoType->getSecurityCheckCategories('INBOUND'),
            'outbound' => $this->securityCheckPhotoType->getSecurityCheckCategories('OUTBOUND'),
            'emptyContainer' => $this->securityCheckPhotoType->getSecurityCheckCategories('EMPTY CONTAINER'),
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * View category security photo type.
     *
     * @param $category
     */
    public function view($category)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        $data = [
            'title' => "Security Setting",
            'subtitle' => "Photo check configurations",
            'page' => "security_photo_type/view",
            'category' => $category,
            'securityPhotos' => $this->securityCheckPhotoType->getBy([
                'category' => $category
            ]),
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Edit category security photo type.
     *
     * @param $category
     */
    public function edit($category)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        $category = urldecode($category);
        $securityPhotoStarts = $this->securityCheckPhotoType->getBy([
            'category' => $category,
            'type' => SecurityCheckPhotoTypeModel::TYPE_START
        ]);
        $securityPhotoStops = $this->securityCheckPhotoType->getBy([
            'category' => $category,
            'type' => SecurityCheckPhotoTypeModel::TYPE_STOP
        ]);
        $data = [
            'title' => "Security Setting",
            'subtitle' => "Photo check configurations",
            'page' => "security_photo_type/edit",
            'category' => urldecode($category),
            'securityPhotoStarts' => $securityPhotoStarts,
            'securityPhotoStops' => $securityPhotoStops,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Save settings.
     * @param $category
     */
    public function update($category)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        if ($this->validate(['photo_starts[]' => 'required', 'photo_stops[]' => 'required'])) {
            $photoStarts = $this->input->post('photo_starts') ?: [];
            $photoStops = $this->input->post('photo_stops') ?: [];
            $category = urldecode($category);

            $this->db->trans_start();

            $this->securityCheckPhotoType->delete(['category' => $category, 'type' => SecurityCheckPhotoTypeModel::TYPE_START]);
            $this->securityCheckPhotoType->delete(['category' => $category, 'type' => SecurityCheckPhotoTypeModel::TYPE_STOP]);

            foreach ($photoStarts as $photoStart) {
                $this->securityCheckPhotoType->create([
                    'category' => $category,
                    'type' => SecurityCheckPhotoTypeModel::TYPE_START,
                    'photo_title' => $photoStart
                ]);
            }
            foreach ($photoStops as $photoStop) {
                $this->securityCheckPhotoType->create([
                    'category' => $category,
                    'type' => SecurityCheckPhotoTypeModel::TYPE_STOP,
                    'photo_title' => $photoStop
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Security photo {$category} successfully updated", 'security-photo-type');
            } else {
                flash('danger', "Update security photo type failed, try again or contact administrator");
            }
        }

        $this->edit($category);
    }

}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Handling_type
 * @property HandlingTypeModel $handlingType
 * @property ComponentModel $component
 * @property AttachmentPhotoModel $attachmentPhoto
 * @property HandlingTypeComponentModel $handlingTypeComponent
 * @property HandlingTypePhotoModel $handlingTypePhoto
 * @property Exporter $exporter
 */
class Handling_type extends CI_Controller
{
    /**
     * Handling type constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('HandlingTypeModel', 'handlingType');
        $this->load->model('ComponentModel', 'component');
        $this->load->model('HandlingTypeComponentModel', 'handlingTypeComponent');
        $this->load->model('AttachmentPhotoModel', 'attachmentPhoto');
        $this->load->model('HandlingTypePhotoModel', 'handlingTypePhoto');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show handling type data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_TYPE_VIEW);

        $handlingTypes = $this->handlingType->getAllhandlingTypes();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Handling types", $handlingTypes);
        } else {
            $data = [
                'title' => "Handling Type",
                'subtitle' => "Data handling type",
                'page' => "handling_type/index",
                'handlingTypes' => $handlingTypes
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Show detail handling type.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_TYPE_VIEW);

        $handlingType = $this->handlingType->getHandlingTypeById($id);
        $components = $this->component->getByHandlingType($id);
        $handlingTypePhotos = $this->attachmentPhoto->getByHandlingType($id);
        $data = [
            'title' => "Handling Type",
            'subtitle' => "View handling type",
            'page' => "handling_type/view",
            'handlingType' => $handlingType,
            'components' => $components,
            'handlingTypePhotos' => $handlingTypePhotos
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show create handling type form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_TYPE_CREATE);

        $data = [
            'title' => "Handling Type",
            'subtitle' => "Create handling type",
            'page' => "handling_type/create",
            'components' => $this->component->getAll(),
            'attachmentPhotos' => $this->attachmentPhoto->getAll(),
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Save new handling type.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_TYPE_CREATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('handling_type', 'Handling type', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('handling_code', 'Handling Code', 'trim|required|max_length[2]|is_unique[ref_handling_types.handling_code]');
            $this->form_validation->set_rules('category', 'Handling category', 'trim|required');
            $this->form_validation->set_rules('container_multiplier', 'Container multiplier', 'trim|required');
            $this->form_validation->set_rules('goods_multiplier', 'Goods multiplier', 'trim|required');
            $this->form_validation->set_rules('description', 'Handling type description', 'trim|required|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $handlingType = $this->input->post('handling_type');
                $handlingCode = $this->input->post('handling_code');
                $handlingCategory = $this->input->post('category');
                $containerMultiplier = $this->input->post('container_multiplier');
                $goodsMultiplier = $this->input->post('goods_multiplier');
                $description = $this->input->post('description');
                $duration = $this->input->post('duration');
                $photo = $this->input->post('photo');

                // handling components
                $branch = $this->input->post('branch');
                $components = $this->input->post('components');
                $defaultValues = $this->input->post('default_values');
                $descriptions = $this->input->post('descriptions');

                // handling attachment photo
                $attachmentPhotos = $this->input->post('attachmentPhotos');
                $conditions = $this->input->post('conditions');
                $descriptionPhotos = $this->input->post('descriptionPhotos');

                $this->db->trans_start();

                $this->handlingType->createHandlingType([
                    'handling_type' => $handlingType,
                    'handling_code' => strtoupper($handlingCode),
                    'category' => $handlingCategory,
                    'multiplier_container' => $containerMultiplier,
                    'multiplier_goods' => $goodsMultiplier,
                    'duration' => $duration,
                    'photo' => $photo,
                    'description' => $description,
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);
                $handlingTypeId = $this->db->insert_id();
                $componentData = [];
                for ($i = 0; $i < count($components); $i++) {
                    if (!empty($components[$i])) {
                        $componentRecord['id_handling_type'] = $handlingTypeId;
                        $componentRecord['id_component'] = $components[$i];
                        $componentRecord['id_branch'] = $branch;
                        $componentRecord['default_value'] = $defaultValues[$i];
                        $componentRecord['description'] = $descriptions[$i];
                        $componentRecord['created_by'] = UserModel::authenticatedUserData('id');
                        $componentData[] = $componentRecord;
                    }
                }

                if (count($components)) {
                    $this->handlingTypeComponent->insertHandlingTypeComponents($componentData);
                }

                //handling attachment photo
                $attachmentPhotoData = [];
                for ($i = 0; $i < count($attachmentPhotos); $i++) {
                    if (!empty($attachmentPhotos[$i])) {
                        $photoRecord['id_handling_type'] = $handlingTypeId;
                        $photoRecord['id_attachment_photo'] = $attachmentPhotos[$i];
                        $photoRecord['condition'] = $conditions[$i];
                        $photoRecord['description'] = $descriptionPhotos[$i];
                        $photoRecord['created_by'] = UserModel::authenticatedUserData('id');
                        $attachmentPhotoData[] = $photoRecord;
                    }
                }

                if (count($attachmentPhotos)) {
                    $this->handlingTypePhoto->insertHandlingTypePhotos($attachmentPhotoData);
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Handling type <strong>{$handlingType}</strong> successfully created", 'handling_type');
                } else {
                    flash('danger', "Save handling type <strong>{$handlingType}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->create();
    }

    /**
     * Show edit handling type form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_TYPE_EDIT);

        $handlingType = $this->handlingType->getHandlingTypeById($id);
        $handlingTypeComponents = $this->component->getByHandlingType($id);
        $components = $this->component->getAll();
        $handlingTypePhotos = $this->attachmentPhoto->getByHandlingType($id);
        $attachmentPhotos = $this->attachmentPhoto->getAll();
        $data = [
            'title' => "Handling Type",
            'subtitle' => "Edit handling type",
            'page' => "handling_type/edit",
            'handlingType' => $handlingType,
            'handlingTypeComponents' => $handlingTypeComponents,
            'components' => $components,
            'handlingTypePhotos' => $handlingTypePhotos,
            'attachmentPhotos' => $attachmentPhotos,
        ];
        // print_r($handlingType);
        $this->load->view('template/layout', $data);
    }

    /**
     * Update data handling type by id.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_TYPE_EDIT);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $handlingType = $this->handlingType->getHandlingTypeById($id);
            $this->form_validation->set_rules('handling_type', 'Handling type', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('handling_code', 'Handling Code', 'trim|required|max_length[2]|callback_code_exists[' . $handlingType['handling_code'] . ']');
            $this->form_validation->set_rules('category', 'Handling Category', 'trim|required');
            $this->form_validation->set_rules('container_multiplier', 'Container multiplier', 'trim|required');
            $this->form_validation->set_rules('goods_multiplier', 'Goods multiplier', 'trim|required');
            $this->form_validation->set_rules('description', 'Handling type description', 'trim|required|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $handlingType = $this->input->post('handling_type');
                $handlingCode = $this->input->post('handling_code');
                $handlingCategory = $this->input->post('category');
                $containerMultiplier = $this->input->post('container_multiplier');
                $goodsMultiplier = $this->input->post('goods_multiplier');
                $description = $this->input->post('description');
                $photo = $this->input->post('photo');
                $duration = $this->input->post('duration');

                // handling components
                $branch = $this->input->post('branch');
                $components = $this->input->post('components');
                $defaultValues = $this->input->post('default_values');
                $descriptions = $this->input->post('descriptions');
                if(!isset($components)){
                    $components = [];
                }
                // handling attachment photo
                $attachmentPhotos = $this->input->post('attachmentPhotos');
                $conditions = $this->input->post('conditions');
                $descriptionPhotos = $this->input->post('descriptionPhotos');
                if(!isset($attachmentPhotos)){
                    $attachmentPhotos = [];
                }

                $this->db->trans_start();

                $this->handlingType->updateHandlingType([
                    'handling_type' => $handlingType,
                    'handling_code' => strtoupper($handlingCode),
                    'category' => $handlingCategory,
                    'multiplier_container' => $containerMultiplier,
                    'multiplier_goods' => $goodsMultiplier,
                    'photo' => $photo,
                    'duration' => $duration,
                    'description' => $description,
                    'updated_by' => UserModel::authenticatedUserData('id'),
                    'updated_at' => date('Y-m-d H:i:s')
                ], $id);

                $this->handlingTypeComponent->deleteHandlingTypeComponentByHandlingType($id, false);
                $componentData = [];
                for ($i = 0; $i < count($components); $i++) {
                    if (!empty($components[$i])) {
                        $componentRecord['id_handling_type'] = $id;
                        $componentRecord['id_component'] = $components[$i];
                        $componentRecord['id_branch'] = $branch;
                        $componentRecord['default_value'] = $defaultValues[$i];
                        $componentRecord['description'] = $descriptions[$i];
                        $componentRecord['created_by'] = UserModel::authenticatedUserData('id');
                        $componentData[] = $componentRecord;
                    }
                }
                if (count($components)) {
                    $this->handlingTypeComponent->insertHandlingTypeComponents($componentData);
                }

                //attachment photo
                $this->handlingTypePhoto->deleteHandlingTypePhotoByHandlingType($id, false);
                $photoData = [];
                for ($i = 0; $i < count($attachmentPhotos); $i++) {
                    if (!empty($attachmentPhotos[$i])) {
                        $photoRecord['id_handling_type'] = $id;
                        $photoRecord['id_attachment_photo'] = $attachmentPhotos[$i];
                        $photoRecord['condition'] = $conditions[$i];
                        $photoRecord['description'] = $descriptionPhotos[$i];
                        $photoRecord['created_by'] = UserModel::authenticatedUserData('id');
                        $photoData[] = $photoRecord;
                    }
                }
                if (count($attachmentPhotos)) {
                    $this->handlingTypePhoto->insertHandlingTypePhotos($photoData);
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Handling type <strong>{$handlingType}</strong> successfully updated", 'handling_type');
                } else {
                    flash('danger', "Update handling type <strong>{$handlingType}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit($id);
    }

    /**
     * Perform deleting handling type data.
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_TYPE_DELETE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $handlingType = $this->handlingType->getHandlingTypeById($id);

            $this->db->trans_start();

            $this->handlingType->deleteHandlingType($id);
            $this->handlingTypeComponent->deleteHandlingTypeComponentByHandlingType($id);
            $this->handlingTypePhoto->deleteHandlingTypePhotoByHandlingType($id);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('warning', "Handling type <strong>{$handlingType['handling_type']}</strong> successfully deleted");
            } else {
                flash('danger', "Delete handling type <strong>{$handlingType['handling_type']}</strong> failed, try again or contact administrator");
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('handling_type');
    }

    /**
     * Check given handling code is exist or not.
     * @param $newCode
     * @param $oldCode
     * @return bool
     */
    public function code_exists($newCode, $oldCode)
    {
        $codeExist = $this->db->from('ref_handling_types')
            ->where([
                'handling_code' => $newCode,
                'handling_code !=' => $oldCode,
            ])
            ->count_all_results();
        if ($codeExist) {
            $this->form_validation->set_message('code_exists', 'The %s has been registered before, try another');
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get all branches by Customer .
     */
    public function ajax_get_customer_handling_types()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $customerId = $this->input->get('id_customer');
            if (strpos(get_active_branch('branch'), 'TPP') !== false) {
                $handlingTypes = $this->handlingType->getAllHandlingTypes();
            } else {
                $handlingTypes = $this->handlingType->getHandlingTypesByCustomer($customerId);
            }
            header('Content-Type: application/json');
            echo json_encode($handlingTypes);
        }
    }

      /**
     * Ajax get all handling type data
     */
    public function ajax_get_handling_types()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $search = $this->input->get('q');
            $page = $this->input->get('page');

            $handlingTypes = $this->handlingType->getAjaxHandlingTypeById($search, $page);
            
            echo json_encode($handlingTypes);
        }
    }

    /**
     * Get attachment photo by handling type .
     */
    public function ajax_get_photo_handling_types()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $handlingTypeId = $this->input->get('id_handling_type');
            $condition = $this->input->get('condition');
            $param = [
                'id_handling_type' => $handlingTypeId,
                'condition' => $condition,
            ];
            $handlingTypePhotos = $this->attachmentPhoto->getByCondition($param);
            header('Content-Type: application/json');
            echo json_encode($handlingTypePhotos);
        }
    }

}
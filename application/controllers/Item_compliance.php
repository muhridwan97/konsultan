<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Item compliance
 * @property ItemComplianceModel $itemCompliance
 * @property ItemCompliancePhotoModel $itemCompliancePhoto
 * @property PeopleModel $people
 * @property UploadModel $upload
 * @property Exporter $exporter
 */
class Item_compliance extends MY_Controller
{
    /**
     * Item compliance constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ItemComplianceModel', 'itemCompliance');
        $this->load->model('ItemCompliancePhotoModel', 'itemCompliancePhoto');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('UploadModel', 'upload');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'data' => 'GET',
            'ajax_get_item' => 'GET',
            'ajax_save' => 'POST',
            'ajax_get_photo_files' => 'GET',
        ]);
    }

    /**
     * Show list of item compliance.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ITEM_COMPLIANCE_VIEW);

        $filters = get_url_param('filter_item_compliance') ? $_GET : [];
        $selectedCustomers = key_exists('customer', $filters) ? $filters['customer'] : [0];
        $customers = $this->people->getById($selectedCustomers);

        if (get_url_param('export')) {
            $this->exporter->exportLargeResourceFromArray("Item compliance", $this->itemCompliance->getAll());
        } else {
            $this->render('item_compliance/index', compact('customers'));
        }
    }

    /**
     * Get ajax datatable.
     */
    public function data()
    {
        $filters = array_merge(get_url_param('filter_item_compliance') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);

        if (get_url_param('filter_item_compliance')) {
            $itemCompliances = $this->itemCompliance->getAll($filters);
        } else {
            $itemCompliances = ['draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ];
        }
        $this->render_json($itemCompliances);
    }

    /**
     * View single item compliance by id.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ITEM_COMPLIANCE_VIEW);

        $itemCompliance = $this->itemCompliance->getById($id);
        $photos = $this->itemCompliancePhoto->getFilesByItem($id);
        $uploads = $this->upload->getUploadsByItemCompliance($id);

        $this->render('item_compliance/view', compact('itemCompliance', 'photos', 'uploads'));
    }

    /**
     * Show create item compliance form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ITEM_COMPLIANCE_CREATE);

        $this->render('item_compliance/create');
    }

    /**
     * Show edit form item compliance.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ITEM_COMPLIANCE_EDIT);

        $itemCompliance = $this->itemCompliance->getById($id);

        $this->render('item_compliance/edit', compact('itemCompliance'));
    }

    /**
     * Set item compliance data validation.
     *
     * @param array $params
     * @return array
     */
    protected function _validation_rules(...$params)
    {
        $id = isset($params[0]) ? $params[0] : 0;
        return [
            'item_name' => 'trim|required|max_length[500]',
            'no_hs' => 'trim|max_length[8]|min_length[8]',
            'unit' => 'trim|required|max_length[50]',
            'customer' => 'trim|required|max_length[50]',
            'description' => 'trim|max_length[500]',
        ];
    }

    /**
     * Save data item compliance.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ITEM_COMPLIANCE_CREATE);
        
        $idCustomer = $this->input->post('customer');
        if ($this->validate($this->_validation_rules($idCustomer))) {
            $itemName = $this->input->post('item_name');
            $noHS = $this->input->post('no_hs');
            $unit = $this->input->post('unit');
            $description = $this->input->post('description');

            $save = $this->itemCompliance->create([
                'id_customer' => $idCustomer,
                'item_name' => $itemName,
                'no_hs' => $noHS,
                'unit' => $unit,
                'description' => $description
            ]);

            if ($save) {
                flash('success', "Item {$itemName} successfully created", 'item-compliance');
            } else {
                flash('danger', "Save Item {$itemName} failed");
            }
        }
        $this->create();
    }

    /**
     * Save item compliance by ajax.
     */
    public function ajax_save()
    {
        $authorized = AuthorizationModel::mustAuthorized(PERMISSION_ITEM_COMPLIANCE_CREATE, 'plain-text');
        $data = [];
        if (empty($authorized)) {

            $idCustomer = $this->input->post('customer');
            if (!$this->validate($this->_validation_rules($idCustomer))) {
                $data = [
                    'status' => 'invalid',
                    'message' => validation_errors(),
                ];
            } else {
                $itemName = $this->input->post('item_name');
                $noHS = $this->input->post('no_hs');
                $unit = $this->input->post('unit');
                $description = $this->input->post('description');

                $save = $this->itemCompliance->create([
                    'id_customer' => $idCustomer,
                    'item_name' => $itemName,
                    'no_hs' => $noHS,
                    'unit' => $unit,
                    'description' => $description
                ]);
                $itemCompliance = $this->itemCompliance->getById($this->db->insert_id());
                if ($save) {
                    $data = [
                        'status' => 'success',
                        'message' => 'Item was successfully added',
                        'itemCompliance' => $itemCompliance
                    ];
                } else {
                    $data = [
                        'status' => 'error',
                        'message' => 'Something went wrong, try again or contact your administrator',
                    ];
                }
            }
        } else {
            $data['status'] = 'unauthorized';
            $data['message'] = $authorized;
        }

        $this->render_json($data);
    }

    /**
     * Update item compliance data.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ITEM_COMPLIANCE_EDIT);

        $idCustomer = $this->input->post('customer');
        if ($this->validate($this->_validation_rules($idCustomer))) {
            $itemName = $this->input->post('item_name');
            $noHS = $this->input->post('no_hs');
            $unit = $this->input->post('unit');
            $description = $this->input->post('description');

            $update = $this->itemCompliance->update([
                'id_customer' => $idCustomer,
                'item_name' => $itemName,
                'no_hs' => $noHS,
                'unit' => $unit,
                'description' => $description
            ], $id);

            if ($update) {
                $data = [
                    'filter_item_compliance' => 1,
                    'customer' => [
                        $idCustomer
                    ]
                ];
                $param = http_build_query($data);
                flash('success', "Item {$itemName} successfully updated", 'item-compliance?'.$param);
            } else {
                flash('danger', "Update item {$itemName} failed");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting data item compliance.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ITEM_COMPLIANCE_DELETE);

        $itemCompliance = $this->itemCompliance->getById($id);

        if ($this->itemCompliance->delete($id)) {
            flash('warning', "Item {$itemCompliance['item_name']} successfully deleted");
        } else {
            flash('danger', "Delete Item {$itemCompliance['item_name']} failed");
        }

        redirect('item-compliance');
    }

    /**
     * Ajax get all item data
     */
    public function ajax_get_item()
    {
        $search = $this->input->get('q');
        $page = $this->input->get('page');
        $owner = $this->input->get('owner');

        $itemCompliances = $this->itemCompliance->getByItem($search, $page, $owner);

        $this->render_json($itemCompliances);
    }

    /**
     * Get photo files.
     */
    public function ajax_get_photo_files()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $itemPhotoId = $this->input->get('id_photo');
            $files = $this->itemCompliancePhoto->getFilesByItem($itemPhotoId);
            header('Content-Type: application/json');
            echo json_encode($files);
        }
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Container
 * @property ContainerModel $container
 * @property PeopleModel $people
 * @property Exporter $exporter
 */
class Container extends MY_Controller
{
    /**
     * Container constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ContainerModel', 'container');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'data' => 'GET',
            'ajax_save' => 'POST',
            'ajax_get_container_by_no' => 'GET',
            'ajax_get_container' => 'GET',
        ]);
    }

    /**
     * Show container data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CONTAINER_VIEW);

        if (get_url_param('export')) {
            $this->exporter->exportLargeResourceFromArray("Containers", $this->container->getAll());
        } else {
            $this->render('container/index');
        }
    }

    /**
     * Get ajax datatable.
     */
    public function data()
    {
        $filters = [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ];

        $containers = $this->container->getAll($filters);

        $this->render_json($containers);
    }

    /**
     * Show view container form.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CONTAINER_VIEW);

        $container = $this->container->getById($id);

        $this->render('container/view', compact('container'));
    }

    /**
     * Show create container form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CONTAINER_CREATE);

        $shippingLines = $this->people->getByType(PeopleModel::$TYPE_SHIPPING_LINE);

        $this->render('container/create', compact('shippingLines'));
    }

    /**
     * Get base validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'no_container' => 'trim|required|max_length[15]',
            'size' => 'trim|required|max_length[50]',
            'type' => 'trim|required|max_length[50]',
            'description' => 'trim|required|max_length[500]',
        ];
    }

    /**
     * Save new container.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CONTAINER_CREATE);

        if ($this->validate()) {
            $shippingLine = $this->input->post('shipping_line');
            $noContainer = $this->input->post('no_container');
            $size = $this->input->post('size');
            $type = $this->input->post('type');
            $description = $this->input->post('description');

            $save = $this->container->create([
                'id_shipping_line' => $shippingLine,
                'no_container' => $noContainer,
                'size' => $size,
                'type' => $type,
                'description' => $description
            ]);

            if ($save) {
                flash('success', "Container {$noContainer} successfully created", 'container');
            } else {
                flash('danger', "Save container {$noContainer} failed");
            }
        }
        $this->create();
    }

    /**
     * Save container by ajax.
     */
    public function ajax_save()
    {
        $authorized = AuthorizationModel::mustAuthorized(PERMISSION_CONTAINER_CREATE, 'plain-text');
        $data = [];
        if (empty($authorized)) {
            $this->form_validation->set_rules('no_container', 'Container Number', 'trim|required|max_length[15]|callback_container_exists[' . if_empty($this->input->post('id'), 0) . ']');
            $this->form_validation->set_rules('size', 'Container Size', 'trim|required');
            $this->form_validation->set_rules('type', 'Container Type', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $data = [
                    'status' => 'invalid',
                    'message' => validation_errors(),
                ];
            } else {
                $id = $this->input->post('id');
                $shippingLine = $this->input->post('shipping_line');
                $noContainer = $this->input->post('no_container');
                $size = $this->input->post('size');
                $type = $this->input->post('type');
                $description = $this->input->post('description');

                if (empty($id)) {
                    $save = $this->container->create([
                        'id_shipping_line' => $shippingLine,
                        'no_container' => $noContainer,
                        'size' => $size,
                        'type' => $type,
                        'description' => $description
                    ]);
                    $container = $this->container->getById($this->db->insert_id());
                } else {
                    $save = $this->container->update([
                        'id_shipping_line' => $shippingLine,
                        'no_container' => $noContainer,
                        'size' => $size,
                        'type' => $type,
                        'description' => $description
                    ], $id);
                    $container = $this->container->getById($id);
                }
                if ($save) {
                    $data = [
                        'status' => 'success',
                        'message' => 'Container was successfully added',
                        'container' => $container
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
     * Show edit container form.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CONTAINER_EDIT);

        $container = $this->container->getById($id);
        $shippingLines = $this->people->getByType(PeopleModel::$TYPE_SHIPPING_LINE);

        $this->render('container/edit', compact('container', 'shippingLines'));
    }

    /**
     * Update data container by id.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CONTAINER_EDIT);

        if ($this->validate()) {
            $shippingLine = $this->input->post('shipping_line');
            $no_container = $this->input->post('no_container');
            $size = $this->input->post('size');
            $type = $this->input->post('type');
            $description = $this->input->post('description');

            $update = $this->container->update([
                'id_shipping_line' => $shippingLine,
                'no_container' => $no_container,
                'size' => $size,
                'type' => $type,
                'description' => $description
            ], $id);

            if ($update) {
                flash('success', "Container {$no_container} successfully updated", 'container');
            } else {
                flash('danger', "Update container {$no_container} failed");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting container data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CONTAINER_DELETE);

        $container = $this->container->getById($id);

        if ($this->container->delete($id)) {
            flash('warning', "Container {$container['no_container']} successfully deleted");
        } else {
            flash('danger', "Delete container {$container['no_container']} failed");
        }
        redirect('container');
    }

    /**
     * Check given container is exist or not.
     *
     * @param $noContainer
     * @param $id
     * @return bool
     */
    public function container_exists($noContainer, $id)
    {
        $exist = $this->container->getBy([
            'ref_containers.no_container' => $noContainer,
            'ref_containers.id!=' => $id
        ]);
        if (!$exist) {
            return true;
        } else {
            $this->form_validation->set_message('container_exists', 'The %s has been created before');
            return false;
        }
    }

    /**
     * Ajax get all container data
     */
    public function ajax_get_container_by_no()
    {
        $search = $this->input->get('q');
        $page = $this->input->get('page');
        $owner = $this->input->get('owner');

        $containers = $this->container->getByNo($search, $page, $owner);

        $this->render_json($containers);
    }

    /**
     * Ajax get container data
     */
    public function ajax_get_container()
    {
        $id = $this->input->get('id');

        $container = $this->container->getById($id);

        $this->render_json($container);
    }

}
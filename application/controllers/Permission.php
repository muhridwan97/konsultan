<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Permission
 * @property PermissionModel $permission
 * @property Exporter $exporter
 */
class Permission extends MY_Controller
{
    /**
     * Permission constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('PermissionModel', 'permission');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show Permissions data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PERMISSION_VIEW);

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Permissions", $this->permission->getAll());
        } else {
            $this->render('permission/index');
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

        $permissions = $this->permission->getAll($filters);

        $this->render_json($permissions);
    }

    /**
     * Show view permission form.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PERMISSION_VIEW);

        $permission = $this->permission->getById($id);

        $this->render('permission/view', compact('permission'));
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Safe_conduct_group
 * @property SafeConductGroupModel $safeConductGroup
 * @property SafeConductModel $safeConduct
 */
class Safe_conduct_group extends MY_Controller
{
    /**
     * Safe_conduct_group constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('SafeConductGroupModel', 'safeConductGroup');
        $this->load->model('SafeConductModel', 'safeConduct');

        $this->setFilterMethods([
            'data' => 'GET'
        ]);
    }
    /**
     * Show deliveryTracking data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_IN_VIEW);
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_OUT_VIEW);

        $this->render('safe_conduct_group/index');
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

        $safeConductGroups = $this->safeConductGroup->getAll($filters);

        $this->render_json($safeConductGroups);
    }

    /**
     * Show detail safe conduct group.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AUCTION_VIEW);

        $safeConductGroup = $this->safeConductGroup->getById($id);
        $safeConducts = $this->safeConduct->getBy(['id_safe_conduct_group' => $id]);

        $this->render('safe_conduct_group/view', compact('safeConductGroup', 'safeConducts'));
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Logs
 * @property LogModel $logHistory
 * @property Exporter $exporter
 */
class Logs extends MY_Controller
{
    /**
     * Report constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('LogModel', 'logHistory');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'logs_data' => 'GET',
        ]);
    }

    /**
     * Show logs data
     */
    public function index()
    {
        $export = get_url_param('export');
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        if (!empty($export)) {
            $logs = $this->logHistory->getAll();
            $this->exporter->exportFromArray('Logs', $logs);
        } else {
            $this->render('logs/index', [], 'Log Histories');
        }
    }

    /**
     * Get ajax datatable.
     */
    public function logs_data()
    {
        $filters = [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ];

        $logs = $this->logHistory->getAll($filters);
        $this->render_json($logs);
    }

      /**
     * Show log data.
     *
     * @param $id
     */
    public function view($id)
    {
        $log = $this->logHistory->getById($id);
        $data = json_decode($log['data'], true);
        if (is_array($data)) {
            $log['data'] = $data;
        }
        $this->render('logs/view', compact('log'));
    }

}
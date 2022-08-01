<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Service_hour
 * @property ServiceHourModel $serviceHour
 * @property Exporter $exporter
 */
class Service_hour extends MY_Controller
{
    /**
     * Service_hour constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ServiceHourModel', 'serviceHour');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'ajax_get_data' => 'GET',
            'print_service_hour' => 'GET',
        ]);
    }

    /**
     * Show service hour data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SERVICE_HOUR_VIEW);

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Service hours", $this->serviceHour->getAll());
        } else {
            $this->render('service_hour/index');
        }
    }

    /**
     * Get ajax datatable.
     */
    public function ajax_get_data()
    {
        $filters = [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir'],
            'view_all' => $this->input->get('view_all')
        ];

        $serviceHours = $this->serviceHour->getAll($filters);

        $this->render_json($serviceHours);
    }

    /**
     * Show view service hour form.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SERVICE_HOUR_VIEW);

        $serviceHour = $this->serviceHour->getById($id);
        $relatedServiceHours = $this->serviceHour->getAll([
            'view_all' => true,
            'related_service_day' => $serviceHour['service_day'],
            'related_service_except' => $serviceHour['id'],
        ]);

        $this->render('service_hour/view', compact('serviceHour', 'relatedServiceHours'));
    }

    /**
     * Print service hour.
     *
     * @param $id
     */
    public function print_service_hour($id)
    {
        $serviceHour = $this->serviceHour->getById($id);
        $relatedServiceHours = $this->serviceHour->getAll([
            'view_all' => true,
            'related_service_day' => $serviceHour['service_day'],
            'related_service_except' => $serviceHour['id'],
        ]);

        $page = $this->load->view('service_hour/print', compact('serviceHour', 'relatedServiceHours'), true);

        $this->exporter->exportToPdf('Service hour ' . url_title($serviceHour['service_day'] . '-' . $serviceHour['effective_date']), $page);
    }

    /**
     * Show create service hour form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SERVICE_HOUR_CREATE);

        $this->render('service_hour/create');
    }

    /**
     * Get base validation rules.
     *
     * @param array $params
     * @return array
     */
    protected function _validation_rules(...$params)
    {
        $id = isset($params[0]) ? $params[0] : 0;
        return [
            'service_day' => 'trim|required|max_length[50]',
            'service_time_start' => 'trim|required|max_length[8]',
            'service_time_end' => 'trim|required|max_length[8]',
            'effective_date' => [
                'trim', 'required', ['date_exist', function ($date) use ($id) {
                    $serviceHourExist = $this->serviceHour->getBy([
                        'ref_service_hours.service_day' => $this->input->post('service_day'),
                        'ref_service_hours.effective_date' => format_date($date),
                        'ref_service_hours.id!=' => $id,
                    ]);
                    if (!empty($serviceHourExist)) {
                        $this->form_validation->set_message('date_exist', 'The %s has been exist');
                        return false;
                    }
                    return true;
                }]
            ],
            'description' => 'trim|max_length[500]',
        ];
    }

    /**
     * Save new service hour.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SERVICE_HOUR_CREATE);

        if ($this->validate()) {
            $branchId = if_empty($this->input->post('branch'), get_active_branch_id());
            $serviceDay = $this->input->post('service_day');
            $serviceTimeStart = $this->input->post('service_time_start');
            $serviceTimeEnd = $this->input->post('service_time_end');
            $effectiveDate = $this->input->post('effective_date');
            $description = $this->input->post('description');

            $save = $this->serviceHour->create([
                'id_branch' => $branchId,
                'service_day' => $serviceDay,
                'service_time_start' => $serviceTimeStart,
                'service_time_end' => $serviceTimeEnd,
                'effective_date' => format_date($effectiveDate),
                'description' => $description
            ]);

            if ($save) {
                flash('success', "Service hour {$serviceDay} successfully created", 'service-hour');
            } else {
                flash('danger', "Save service hour {$serviceDay} failed");
            }
        }
        $this->create();
    }

    /**
     * Show edit service hour form.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SERVICE_HOUR_EDIT);

        $serviceHour = $this->serviceHour->getById($id);

        $this->render('service_hour/edit', compact('serviceHour'));
    }

    /**
     * Update data service hour by id.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SERVICE_HOUR_EDIT);

        if ($this->validate($this->_validation_rules($id))) {
            $branchId = if_empty($this->input->post('branch'), get_active_branch_id());
            $serviceDay = $this->input->post('service_day');
            $serviceTimeStart = $this->input->post('service_time_start');
            $serviceTimeEnd = $this->input->post('service_time_end');
            $effectiveDate = $this->input->post('effective_date');
            $description = $this->input->post('description');

            $serviceHour = $this->serviceHour->getById($id);

            $update = $this->serviceHour->update([
                'id_branch' => $branchId,
                'service_day' => $serviceDay,
                'service_time_start' => $serviceTimeStart,
                'service_time_end' => $serviceTimeEnd,
                'effective_date' => format_date($effectiveDate),
                'description' => $description
            ], $id);

            if ($update) {
                flash('success', "Service hour {$serviceHour['service_day']} {$serviceHour['effective_date']} successfully updated", 'service-hour');
            } else {
                flash('danger', "Update service hour {$serviceHour['service_day']} failed");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting service hour data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SERVICE_HOUR_DELETE);

        $serviceHour = $this->serviceHour->getById($id);

        if ($this->serviceHour->delete($id)) {
            flash('warning', "Service hour {$serviceHour['service_day']} {$serviceHour['effective_date']} successfully deleted");
        } else {
            flash('danger', "Delete service hour {$serviceHour['service_day']} failed");
        }
        redirect('service-hour');
    }

}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Complain_kpi
 * @property ComplainKpiModel $complainKpi
 * @property ComplainKpiWhatsappModel $complainKpiWhatsapp
 * @property ComplainKpiReminderModel $complainKpiReminder
 * @property DepartmentModel $department
 * @property DepartmentContactGroupModel $departmentContact
 * @property BranchModel $branch
 * @property Exporter $exporter
 */
class Complain_kpi extends MY_Controller
{
    /**
     * Complain_kpi constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ComplainKpiModel', 'complainKpi');
        $this->load->model('ComplainKpiWhatsappModel', 'complainKpiWhatsapp');
        $this->load->model('ComplainKpiReminderModel', 'complainKpiReminder');
        $this->load->model('DepartmentModel', 'department');
        $this->load->model('DepartmentContactGroupModel', 'departmentContact');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show list of complain categories.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_KPI_VIEW);

        $complain_kpi = $this->complainKpi->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Complain KPI", $complain_kpi);
        } else {
            $this->render('complain_kpi/index', compact('complain_kpi'));
        }
    }

    /**
     * View single complain category by id.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_KPI_VIEW);

        $complain_kpi = $this->complainKpi->getById($id);

        $this->render('complain_kpi/view', compact('complain_kpi'));
    }

    /**
     * Show edit form complain category.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_KPI_EDIT);

        $complain_kpi = $this->complainKpi->getById($id);
        $whatsappGroups = $this->complainKpiWhatsapp->getBy(['id_kpi' => $id]);
        $departments = $this->department->getAll();
        $departmentContacts = $this->departmentContact->getAll();
        $branches = $this->branch->getAll();
        $reminderDetails = $this->complainKpiReminder->getBy(['id_kpi' => $id]);

        $data = [
            'complain_kpi' => $complain_kpi,
            'whatsappGroups' => $whatsappGroups,
            'departments' => $departments,
            'departmentContacts' => $departmentContacts,
            'branches' => $branches,
            'reminderDetails' => $reminderDetails,
        ];

        $this->render('complain_kpi/edit', $data);
    }

    /**
     * Set complain category data validation.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'category' => 'trim|max_length[500]',
            'value_type' => 'trim',
        ];
    }

    /**
     * Update complain category data.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_CATEGORY_EDIT);

      
        $major = $this->input->post('major');
        $minor = $this->input->post('minor');
        $recur_day = $this->input->post('recur_day');
        $reminder = $this->input->post('reminder');
        $description = $this->input->post('description');
        $groups = $this->input->post('groups');
        $branches = $this->input->post('branches');
        $whatsapp_groups = $this->input->post('whatsapp_groups');
        $reminders = $this->input->post('reminders');

        $this->db->trans_start();
        $this->complainKpi->update([
            'major' => $major,
            'minor' => $minor,
            'recur_day' => $recur_day,
            'reminder' => $reminder.":00",
            'description' => $description,
        ], $id);
        $kpi = $this->complainKpi->getById($id);
        $this->complainKpiWhatsapp->deleteWhatsappByComplainKpi($id);
        $this->complainKpiReminder->deleteReminderByComplainKpi($id);
        $newReminder = [];
        foreach ($reminders as $key => $hour) {
            $data['id_kpi'] = $id   ;
            $data['reminder_time'] = $hour.":00";
            $newReminder [] = $data;

        }
        if (count($newReminder)) {
            $this->complainKpiReminder->insertReminderGroup($newReminder);
        }

        $groupData = [];
        for ($i = 0; $i < count($groups); $i++) {
            if($id == 1){
                if (!empty($groups[$i])) {
                    $componentRecord['id_kpi'] = $id;
                    $componentRecord['group'] = $groups[$i];
                    $componentRecord['id_branch_warehouse'] = $branches[$i];
                    $componentRecord['id_contact_group'] = $whatsapp_groups[$i];
                    $componentRecord['created_by'] = UserModel::authenticatedUserData('id');
                    $groupData[] = $componentRecord;
                }
            }else{
                if (!empty($groups[$i])) {
                    $componentRecord['id_kpi'] = $id;
                    $componentRecord['group'] = $groups[$i];
                    $componentRecord['whatsapp_group'] = $whatsapp_groups[$i];
                    $componentRecord['created_by'] = UserModel::authenticatedUserData('id');
                    $groupData[] = $componentRecord;
                }
            }
            
        }
        if (count($groups)) {
            $this->complainKpiWhatsapp->insertWhatsappGroup($groupData);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            flash('success', "Complain KPI {$kpi['kpi']} updated", 'complain-kpi');
        } else {
            flash('danger', "Update KPI category {$kpi['kpi']} failed");
        }
        $this->edit($id);
    }

}
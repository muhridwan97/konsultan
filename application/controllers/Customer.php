<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Customer
 * @property CustomerModel $customer
 * @property PeopleContactModel $peopleContact
 * @property PeopleBranchModel $peopleBranch
 * @property PeopleHandlingTypeModel $peopleHandlingType
 * @property BranchModel $branch
 * @property UserModel $user
 * @property HandlingTypeModel $handlingType
 * @property DocumentTypeModel $documentType
 * @property Mailer $mailer
 * @property BookingTypeModel $bookingType
 * @property PeopleBookingTypeModel $peopleBookingType
 * @property PeopleUserModel $peopleUser
 * @property CustomerStorageCapacityModel $customerStorageCapacity
 * @property NotificationModel $notification
 * @property PeopleBranchMentionModel $peopleBranchMention
 * @property Exporter $exporter
 */
class Customer extends MY_Controller
{
    /**
     * People constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('CustomerModel', 'customer');
        $this->load->model('PeopleContactModel', 'peopleContact');
        $this->load->model('PeopleBranchModel', 'peopleBranch');
        $this->load->model('PeopleHandlingTypeModel', 'peopleHandlingType');
        $this->load->model('PeopleDocumentTypeReminderModel', 'peopleDocumentTypeReminder');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('UserModel', 'user');
        $this->load->model('HandlingTypeModel', 'handlingType');
        $this->load->model('LogModel', 'logHistory');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('PeopleBookingTypeModel', 'peopleBookingType');
        $this->load->model('PeopleUserModel', 'peopleUser');
        $this->load->model('CustomerStorageCapacityModel', 'customerStorageCapacity');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('PeopleBranchMentionModel', 'peopleBranchMention');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'data' => 'GET',
            'ajax_get_people' => 'GET',
            'ajax_get_people_branch' => 'GET', 
            'ajax_get_people_all_branch' => 'GET',
            'edit_notification' => 'GET',
            'update_notification' => 'POST',
        ]);
    }

    /**
     * Show all list people.
     */
    public function index()
    {
        $permissions = [PERMISSION_CUSTOMER_VIEW];
        AuthorizationModel::checkAuthorizedAll($permissions, false);

        if (get_url_param('export')) {
            $this->exporter->exportLargeResourceFromArray("Customer", $this->customer->getAll());
        } else {
            $this->render('customer/index');
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

        $customer = $this->customer->getAll($filters);

        $this->render_json($customer);
    }

    /**
     * View person data by id.
     * @param $id
     */
    public function view($id)
    {
        $permissions = [PERMISSION_CUSTOMER_VIEW];
        AuthorizationModel::checkAuthorizedAll($permissions, false);

        $customer = $this->customer->getById($id);

        $this->render('customer/view', compact('customer'));
    }

    /**
     * Show create form person.
     */
    public function create()
    {
        $permissions = [PERMISSION_CUSTOMER_CREATE];
        AuthorizationModel::checkAuthorizedAll($permissions, false);

        $this->render('customer/create', );
    }

    /**
     * Show edit form person.
     *
     * @param $id
     */
    public function edit($id)
    {
        $permissions = [PERMISSION_CUSTOMER_EDIT];
        AuthorizationModel::checkAuthorizedAll($permissions, false);

        $customer = $this->customer->getById($id);
        $data = [
            'genders' => [
                'NONE' => CustomerModel::$GENDER_NONE . ' (Company)',
                'MALE' => CustomerModel::$GENDER_MALE,
                'FEMALE' => CustomerModel::$GENDER_FEMALE
            ],
            'customer' => $customer,
        ];

        $this->render('customer/edit', $data);
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
        $noPerson = isset($params[1]) ? $params[1] : '';

        return [
            'name' => 'trim|required|max_length[50]',
            'identity_number' => 'trim|required|max_length[50]|regex_match[/^[0-9]{16}$/]|is_unique[ref_customers.identity_number]',
            'gender' => 'trim|required',
            'birthday' => 'trim|max_length[50]',
            'address' => 'trim|max_length[300]',
            'contact' => 'trim|max_length[50]',
            'email' => 'trim|max_length[50]|valid_email',
            'tax_number' => 'trim|max_length[50]',
        ];
    }

    /**
     * Save new person.
     */
    public function save()
    {
        $permissions = [PERMISSION_CUSTOMER_CREATE];
        AuthorizationModel::checkAuthorizedAll($permissions, false);

        if ($this->validate()) {
            $identityNumber = $this->input->post('identity_number');
            $name = $this->input->post('name');
            $gender = $this->input->post('gender');
            $birthday = sql_date_format($this->input->post('birthday'));
            $address = $this->input->post('address');
            $contact = $this->input->post('contact');
            $email = $this->input->post('email');
            $taxNumber = $this->input->post('tax_number');
       
            $this->db->trans_start();

            $this->customer->create([
                'identity_number' => $identityNumber,
                'name' => $name,
                'gender' => $gender,
                'birthday' => if_empty($birthday, null),
                'address' => $address,
                'contact' => $contact,
                'email' => $email,
                'tax_number' => $taxNumber,
                'created_by' => UserModel::authenticatedUserData('id')
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Customer {$name} successfully created", 'customer');
            } else {
                flash('danger', "Save customer {$name} failed");
            }
        }
        $this->create();
    }

    /**
     * Update data person by id.
     *
     * @param $id
     */
    public function update($id)
    {
        $permissions = [PERMISSION_CUSTOMER_EDIT];
        AuthorizationModel::checkAuthorizedAll($permissions, false);

        if ($this->validate($this->_validation_rules($id, $this->input->post('identity_number')))) {
            $identityNumber = $this->input->post('identity_number');
            $name = $this->input->post('name');
            $gender = $this->input->post('gender');
            $birthday = sql_date_format($this->input->post('birthday'));
            $address = $this->input->post('address');
            $contact = $this->input->post('contact');
            $email = $this->input->post('email');
            $taxNumber = $this->input->post('tax_number');

            $this->db->trans_start();

            $this->customer->update([
                'identity_number' => $identityNumber,
                'name' => $name,
                'gender' => $gender,
                'birthday' => if_empty($birthday, null),
                'address' => $address,
                'contact' => $contact,
                'email' => $email,
                'tax_number' => $taxNumber,
                'updated_by' => UserModel::authenticatedUserData('id'),
                'updated_at' => date('Y-m-d H:i:s')
            ], $id);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Customer {$name} successfully updated", 'customer');
            } else {
                flash('danger', "Update customer {$name} failed");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting person data.
     * @param $id
     */
    public function delete($id)
    {
        $permissions = [PERMISSION_CUSTOMER_DELETE];
        AuthorizationModel::checkAuthorizedAll($permissions, false);

        $customer = $this->customer->getById($id);

        if ($this->customer->delete($id)) {
            flash('warning', "Customer {$customer['name']} successfully deleted");
        } else {
            flash('danger', "Delete customer {$customer['name']} failed");
        }
        redirect('customer');
    }

    /**
     * Show edit whatsapp notification form person.
     *
     * @param $id
     */
    public function edit_notification($id)
    {
        $permissions = [PERMISSION_PEOPLE_EDIT_NOTIFICATION];
        AuthorizationModel::checkAuthorizedAll($permissions, false);


        $person = $this->customer->getById($id);
        $mentions = $this->peopleBranchMention->getMentionByPersonBranch($person['id_person_branch']);
        $data = [
            'url' => 'dialog',
            'method' => 'GET',
            'payload' => [
                'chatId' => detect_chat_id($person['whatsapp_group']),
            ]
        ];
        $results = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
        $participantResult = [];
        $tempParticipant = [];
        if(isset($results['metadata'])){
            if(!empty($results['metadata']['participantsInfo'])){
                $participants = $results['metadata']['participantsInfo'];
                foreach ($participants as $key => $participant) {
                    $tempParticipant = [
                        'id' => $participant['id'],
                        'name' => $participant['name'],
                        'number' => invert_chat_id($participant['id']),
                    ];
                    $participantResult [] = $tempParticipant;
                }
            }else{
                $participants = $results['metadata']['participants'];
                foreach ($participants as $key => $participant) {
                    $tempParticipant = [
                        'id' => $participant,
                        'name' => 'unknown',
                        'number' => invert_chat_id($participant),
                    ];
                    $participantResult [] = $tempParticipant;
                }
            }
        }
        $complianceMentions = [];
        $operationalMentions = [];
        $externalMentions = [];
        foreach ($mentions as $key => $mention) {
            if($mention['type'] == 'compliance'){
                $complianceMentions[] = $mention;
                continue;
            }
            if($mention['type'] == 'operational'){
                $operationalMentions[] = $mention;
                continue;
            }
            if($mention['type'] == 'external'){
                $externalMentions[] = $mention;
                continue;
            }
        }
        // print_debug($participants);
        $data = [
            'person' => $person,
            'participants' => $participantResult,
            'complianceMentions' => $complianceMentions,
            'operationalMentions' => $operationalMentions,
            'externalMentions' => $externalMentions,
        ];

        $this->render('people/edit_notification', $data);
    }

    /**
     * Update data mention whatsapp by id.
     *
     * @param $id
     */
    public function update_notification($id)
    {
        $permissions = [PERMISSION_PEOPLE_EDIT_NOTIFICATION];
        AuthorizationModel::checkAuthorizedAll($permissions, false);

        $whatsapp_group = $this->input->post('whatsapp_group');
        $compliances = $this->input->post('compliances');
        $operationals = $this->input->post('operationals');
        $externals = $this->input->post('externals');

        $person = $this->customer->getById($id);

        $this->db->trans_start();
        $branch = get_active_branch('id');

        $idPeopleBranch = $this->peopleBranch->getIdByIdCustomerIdBranch($id, $branch);
        
        if ($person['type'] == CustomerModel::$TYPE_CUSTOMER) {
            if (empty($idPeopleBranch)) {
                $this->peopleBranch->createPeopleBranch([
                    'id_customer' => $id,
                    'id_branch' => $branch,
                    'whatsapp_group' => $whatsapp_group,
                ]);
                $peopleBranchId = $this->db->insert_id();
            }else{
                $idPeopleBranch = $idPeopleBranch[0]['id'];
                $this->peopleBranch->update([
                    'whatsapp_group' => $whatsapp_group,
                ],$idPeopleBranch);
                $peopleBranchId = $idPeopleBranch; 
            }
        }

        $this->peopleBranchMention->delete(['id_person_branch' => $peopleBranchId]);
        
        foreach ($compliances as $key => $compliance) {
            $this->peopleBranchMention->create([
                'id_person_branch' => $peopleBranchId,
                'type' => 'compliance',
                'whatsapp' => $compliance,
            ]);
        }

        foreach ($operationals as $key => $operational) {
            $this->peopleBranchMention->create([
                'id_person_branch' => $peopleBranchId,
                'type' => 'operational',
                'whatsapp' => $operational,
            ]);
        }

        foreach ($externals as $key => $external) {
            $this->peopleBranchMention->create([
                'id_person_branch' => $peopleBranchId,
                'type' => 'external',
                'whatsapp' => $external,
            ]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
           
            flash('success', "Person {$person['name']} successfully updated", 'people');
        } else {
            flash('danger', "Update person {$person['name']} failed");
        }
        $this->edit($id);
    }

    /**
     * Ajax get all people data per branch
     */
    public function ajax_get_people()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $search = $this->input->get('q');
            $page = $this->input->get('page');
            $type = $this->input->get('type');

            $customer = $this->customer->getPersonByName($search, $type, $page);

            echo json_encode($customer);
        }
    }

     /**
     * Ajax get all people data all branch
     */
    public function ajax_get_people_all_branch()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $search = $this->input->get('q');
            $page = $this->input->get('page');
            $type = $this->input->get('type');

            $customer = $this->customer->getPersonByNameAllBranch($search, $type, $page);

            echo json_encode($customer);
        }
    }

    /**
     * Ajax get all people data
     */
    public function ajax_get_people_branch()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $search = $this->input->get('q');
            $page = $this->input->get('page');
            $type = $this->input->get('type');

            $customer = $this->customer->getPersonByNamePerBranch($search, $type, $page);

            echo json_encode($customer);
        }
    }
}

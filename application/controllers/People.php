<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class People
 * @property PeopleModel $people
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
class People extends MY_Controller
{
    /**
     * People constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('PeopleModel', 'people');
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
        $permissions = [PERMISSION_PEOPLE_VIEW, PERMISSION_CUSTOMER_VIEW, PERMISSION_SUPPLIER_VIEW];
        AuthorizationModel::checkAuthorizedAll($permissions, false);

        if (get_url_param('export')) {
            $this->exporter->exportLargeResourceFromArray("People", $this->people->getAll());
        } else {
            $this->render('people/index');
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

        $people = $this->people->getAll($filters);

        $this->render_json($people);
    }

    /**
     * View person data by id.
     * @param $id
     */
    public function view($id)
    {
        $permissions = [PERMISSION_PEOPLE_VIEW, PERMISSION_CUSTOMER_VIEW, PERMISSION_SUPPLIER_VIEW];
        AuthorizationModel::checkAuthorizedAll($permissions, false);

        $person = $this->people->getById($id);
        $contacts = $this->peopleContact->getContactByPerson($id);
        $branches = $this->branch->getByCustomer($id);
        $handlingTypes = $this->handlingType->getHandlingTypesByCustomer($id);
        $documentTypes = $this->documentType->getDocumentTypeRemindersByCustomer($id);
        $customerStorageCapacities = $this->customerStorageCapacity->getBy(['ref_customer_storage_capacities.id_customer' => $id]);
        $members = $this->people->getBy(['ref_people.id_parent' => $id]);
        $peopleUser = $this->peopleUser->getUserByPerson($id);
        $userId=[];
        foreach ($peopleUser as $key => $user) {
            $userId[]=$user['id_user'];
        }
        $person['id_user'] = $userId;

        $this->render('people/view', compact('person', 'branches', 'contacts', 'handlingTypes', 'documentTypes', 'customerStorageCapacities', 'members'));
    }

    /**
     * Show create form person.
     */
    public function create()
    {
        $permissions = [PERMISSION_PEOPLE_CREATE, PERMISSION_CUSTOMER_CREATE, PERMISSION_SUPPLIER_CREATE];
        AuthorizationModel::checkAuthorizedAll($permissions, false);

        $types = [];

        if (AuthorizationModel::isAuthorized(PERMISSION_PEOPLE_CREATE)) {
            $types = [
                'EMPLOYEE' => PeopleModel::$TYPE_EMPLOYEE,
                'DRIVER' => PeopleModel::$TYPE_DRIVER,
                'EXPEDITION' => PeopleModel::$TYPE_EXPEDITION,
                'TPS' => PeopleModel::$TYPE_TPS,
                'SHIPPING LINE' => PeopleModel::$TYPE_SHIPPING_LINE,
                'CUSTOMS' => PeopleModel::$TYPE_CUSTOMS,
            ];
        }

        if (AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_CREATE)) {
            $types = ['CUSTOMER' => PeopleModel::$TYPE_CUSTOMER] + $types;
        }

        if (AuthorizationModel::isAuthorized(PERMISSION_SUPPLIER_CREATE)) {
            $types = ['SUPPLIER' => PeopleModel::$TYPE_SUPPLIER] + $types;
        }

        $genders = [
            'NONE' => PeopleModel::$GENDER_NONE . ' (Company)',
            'MALE' => PeopleModel::$GENDER_MALE,
            'FEMALE' => PeopleModel::$GENDER_FEMALE
        ];
        $branches = $this->branch->getAll();
        $documentTypes = $this->documentType->getBy(['is_reminder' => 1]);
        $handlingTypes = $this->handlingType->getAllHandlingTypes();
        $bookingTypes = $this->bookingType->getAllBookingTypes();
        $users = $this->user->getUnattachedProfile();
        $userTypes = [
            'USER', 'NON USER'
        ];
        $parent = $this->people->getById($this->input->post('parent'));

        $this->render('people/create', compact('types', 'userTypes', 'genders', 'branches', 'handlingTypes', 'users', 'bookingTypes', 'documentTypes', 'parent'));
    }

    /**
     * Show edit form person.
     *
     * @param $id
     */
    public function edit($id)
    {
        $permissions = [PERMISSION_PEOPLE_EDIT, PERMISSION_CUSTOMER_EDIT, PERMISSION_SUPPLIER_EDIT];
        AuthorizationModel::checkAuthorizedAll($permissions, false);

        $types = [];

        if (AuthorizationModel::isAuthorized(PERMISSION_PEOPLE_EDIT)) {
            $types = [
                'EMPLOYEE' => PeopleModel::$TYPE_EMPLOYEE,
                'DRIVER' => PeopleModel::$TYPE_DRIVER,
                'EXPEDITION' => PeopleModel::$TYPE_EXPEDITION,
                'TPS' => PeopleModel::$TYPE_TPS,
                'SHIPPING LINE' => PeopleModel::$TYPE_SHIPPING_LINE,
                'CUSTOMS' => PeopleModel::$TYPE_CUSTOMS,
            ];
        }

        if (AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_EDIT)) {
            $types = ['CUSTOMER' => PeopleModel::$TYPE_CUSTOMER] + $types;
        }

        if (AuthorizationModel::isAuthorized(PERMISSION_SUPPLIER_EDIT)) {
            $types = ['SUPPLIER' => PeopleModel::$TYPE_SUPPLIER] + $types;
        }

        $person = $this->people->getById($id);
        if ($person['type'] == peopleModel::$TYPE_SUPPLIER) {
            $person_code = $person['no_person'];
        } else {
            $person_code = substr($person['no_person'], 0, 3);
        }
        
        switch ($person['type']) {
            case PeopleModel::$TYPE_CUSTOMER:
                if (!AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_EDIT)) {
                    flash('danger', 'You are not authorized to edit customer', 'people');
                }
                break;
            case PeopleModel::$TYPE_SUPPLIER:
                if (!AuthorizationModel::isAuthorized(PERMISSION_SUPPLIER_EDIT)) {
                    flash('danger', 'You are not authorized to edit supplier', 'people');
                }
                break;
            default:
                if (!AuthorizationModel::isAuthorized(PERMISSION_PEOPLE_EDIT)) {
                    flash('danger', 'You are not authorized to edit person', 'people');
                }
                break;
        }

        $peopleUser = $this->peopleUser->getUserByPerson($id);
        $userId=[];
        foreach ($peopleUser as $key => $user) {
            $userId[]=$user['id_user'];
        }
        $person['id_user'] = $userId;

        $branches = $this->branch->getAll();
        $handlingTypes = $this->handlingType->getAllHandlingTypes();
        $users = $this->user->getUnattachedProfile($person['id_user']);
        $userBranches = $this->branch->getByCustomer($id);
        $userHandlingTypes = $this->handlingType->getHandlingTypesByCustomer($id);
        $userTypes = ['USER', 'NON USER'];
        $bookingTypes = $this->bookingType->getAllBookingTypes();
        $userBookingTypes = $this->bookingType->getBookingTypesByCustomer($id);
        $documentTypes = $this->documentType->getBy(['is_reminder' => 1]);
        $userDocumentTypes = $this->documentType->getDocumentTypeRemindersByCustomer($id);

        $currentCustomerStorage = $this->customerStorageCapacity->getBy([
            'ref_customer_storage_capacities.id_branch' => get_active_branch_id(),
            'ref_customer_storage_capacities.id_customer' => $person['id'],
            'status' => CustomerStorageCapacityModel::STATUS_ACTIVE,
        ], true);
        if (empty($currentCustomerStorage)) {
            $currentCustomerStorage = $this->customerStorageCapacity->getBy([
                'ref_customer_storage_capacities.id_branch' => get_active_branch_id(),
                'ref_customer_storage_capacities.id_customer' => $person['id'],
                'status' => CustomerStorageCapacityModel::STATUS_PENDING,
            ], true);
        }
        $parent = $this->people->getById(if_empty($this->input->post('parent'), $person['id_parent']));
        $data = [
            'url' => 'dialog',
            'method' => 'GET',
            'payload' => [
                'chatId' => '6281803281009-1529833244@g.us'
            ]
        ];
        $results = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
        $participantsInfo = $results['metadata']['participantsInfo'];
        // print_debug($participantsInfo);
        $data = [
            'types' => $types,
            'genders' => [
                'NONE' => PeopleModel::$GENDER_NONE . ' (Company)',
                'MALE' => PeopleModel::$GENDER_MALE,
                'FEMALE' => PeopleModel::$GENDER_FEMALE
            ],
            'person' => $person,
            'person_code' => $person_code,
            'currentCustomerStorage' => $currentCustomerStorage,
            'branches' => $branches,
            'handlingTypes' => $handlingTypes,
            'users' => $users,
            'userTypes' => $userTypes,
            'userBranches' => $userBranches,
            'userHandlingTypes' => $userHandlingTypes,
            'bookingTypes' => $bookingTypes,
            'userBookingTypes' => $userBookingTypes,
            'documentTypes' => $documentTypes,
            'userDocumentTypes' => $userDocumentTypes,
            'parent' => $parent,
        ];

        $this->render('people/edit', $data);
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
        $ruleNoPerson = '';
        if (!empty($noPerson)) {
            $person = $this->people->getById($id);
            if ($person['no_person'] != $noPerson) {
                $ruleNoPerson = '|is_unique[ref_people.no_person]';
            }
        }

        return [
            'type' => 'trim|required',
            'name' => 'trim|required|max_length[50]',
            'no_person' => 'trim|required|max_length[50]|regex_match[/^[A-Z]{3}$/]' . $ruleNoPerson,
            'gender' => 'trim|required',
            'birthday' => 'trim|max_length[50]',
            'address' => 'trim|max_length[300]',
            'contact' => 'trim|max_length[50]',
            'email' => 'trim|max_length[50]|valid_email',
            'website' => 'trim|max_length[50]',
            'tax_number' => 'trim|max_length[50]',
            'whatsapp_group' => 'trim|max_length[50]',
            'user[]' => 'trim|max_length[50]',
        ];
    }

    /**
     * Save new person.
     */
    public function save()
    {
        $permissions = [PERMISSION_PEOPLE_CREATE, PERMISSION_CUSTOMER_CREATE, PERMISSION_SUPPLIER_CREATE];
        AuthorizationModel::checkAuthorizedAll($permissions, false);

        if ($this->validate()) {
            $branch = $this->input->post('branch');
            $parent = $this->input->post('parent');
            $type = $this->input->post('type');
            $outboundType = $this->input->post('outbound_type');
            $noPerson = $this->input->post('no_person');
            $name = $this->input->post('name');
            $gender = $this->input->post('gender');
            $birthday = sql_date_format($this->input->post('birthday'));
            $address = $this->input->post('address');
            $region = $this->input->post('region');
            $contact = $this->input->post('contact');
            $email = $this->input->post('email');
            $confirmEmailSource = $this->input->post('confirm_email_source');
            $website = $this->input->post('website');
            $taxNumber = $this->input->post('tax_number');
            // $whatsapp_group = $this->input->post('whatsapp_group');
            $user = $this->input->post('user');
            $branches = $this->input->post('branches');
            $userType = $this->input->post('type_user');
            $handlingTypes = $this->input->post('handling_types');
            $bookingTypes = $this->input->post('booking_types');
            $documentTypes = $this->input->post('document_types');
            $contract = $this->input->post('contract');
            $max_time_request = $this->input->post('max_time_request');

            switch ($type) {
                case PeopleModel::$TYPE_CUSTOMER:
                    if (!AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_CREATE)) {
                        flash('danger', 'You are not authorized to edit customer', 'people');
                    }
                    break;
                case PeopleModel::$TYPE_SUPPLIER:
                    if (!AuthorizationModel::isAuthorized(PERMISSION_SUPPLIER_CREATE)) {
                        flash('danger', 'You are not authorized to edit supplier', 'people');
                    }
                    break;
                default:
                    if (!AuthorizationModel::isAuthorized(PERMISSION_PEOPLE_CREATE)) {
                        flash('danger', 'You are not authorized to edit person', 'people');
                    }
                    break;
            }

            // separate branch per customer
            if ($type != PeopleModel::$TYPE_CUSTOMER) {
                $branch = null;
            }

            if (empty($branches)) {
                $branches = [$branch];
            }

            //Autonumber
            $generate_number = $this->people->getAutoNumberPerson($noPerson);
            $format_person_number = $noPerson.$generate_number;
       
            $this->db->trans_start();

            $this->people->create([
                'id_branch' => $branch,
                'id_parent' => if_empty($parent, null),
                'type' => $type,
                'outbound_type' => $type == 'CUSTOMER' && !empty($outboundType) ? ($outboundType != 'NOT SET' ? $outboundType : null) : null,
                'no_person' => $format_person_number,
                'name' => $name,
                'gender' => $gender,
                'birthday' => if_empty($birthday, null),
                'address' => $address,
                'region' => $region,
                'contact' => $contact,
                'email' => $email,
                'confirm_email_source' => $confirmEmailSource,
                'website' => $website,
                'tax_number' => $taxNumber,
                'max_time_request' => if_empty($max_time_request, NULL),
                'type_user' => if_empty($userType, 'NON USER'),
                //'id_user' => if_empty($user, null),
                'created_by' => UserModel::authenticatedUserData('id')
            ]);
            $personId = $this->db->insert_id();

            foreach ($user as $value) {
                $this->peopleUser->insertUser([
                    'id_people' => $personId,
                    'id_user' => $value,
                ]);
            }

            if ($type == PeopleModel::$TYPE_CUSTOMER) {
                foreach ($branches as $branch) {
                    $this->peopleBranch->createPeopleBranch([
                        'id_customer' => $personId,
                        'id_branch' => $branch,
                        // 'whatsapp_group' => $whatsapp_group,
                        'contract' => $contract,
                    ]);
                }

                $warehouseCapacity = $this->input->post('warehouse_capacity');
                $yardCapacity = $this->input->post('yard_capacity');
                $coveredYardCapacity = $this->input->post('covered_yard_capacity');
                $effectiveDate = $this->input->post('effective_date');
                $expiredDate = $this->input->post('expired_date');
                if (!empty($warehouseCapacity) || !empty($yardCapacity) || !empty($coveredYardCapacity)) {
                    $this->customerStorageCapacity->create([
                        'id_branch' => get_active_branch_id(),
                        'id_customer' => $personId,
                        'warehouse_capacity' => $warehouseCapacity,
                        'yard_capacity' => $yardCapacity,
                        'covered_yard_capacity' => $coveredYardCapacity,
                        'effective_date' => if_empty(format_date($effectiveDate), date('Y-m-d')),
                        'expired_date' => if_empty(format_date($expiredDate), date('Y-m-d')),
                        'description' => 'Initial customer storage'
                    ]);
                }
            }

            if (!empty($handlingTypes)) {
                foreach ($handlingTypes as $handlingType) {
                    $this->peopleHandlingType->createPeopleHandlingType([
                        'id_customer' => $personId,
                        'id_handling_type' => $handlingType
                    ]);
                }
            }
            if (!empty($bookingTypes)) {
                foreach ($bookingTypes as $bookingType) {
                    $this->peopleBookingType->createPeopleBookingType([
                        'id_customer' => $personId,
                        'id_booking_type' => $bookingType
                    ]);
                }
            }
            if (!empty($documentTypes)) {
                foreach ($documentTypes as $documentType) {
                    $this->peopleDocumentTypeReminder->createPeopleDocumentTypeReminder([
                        'id_customer' => $personId,
                        'id_document_type' => $documentType
                    ]);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                if ($type == 'CUSTOMER' && !empty($outboundType)) {
                    $emailTo = get_setting('email_finance');
                    $emailTitle = "New customer " . $name . ' is set to outbound type ' . $outboundType;
                    $emailTemplate = 'emails/basic';
                    $emailData = [
                        'title' => 'Customer Outbound Type',
                        'name' => get_setting('admin_finance'),
                        'email' => $emailTo,
                        'content' => 'A new customer ' . $name . ' has been created with outbound type ' . $outboundType . '. Please review the setting whether it set properly.'
                    ];
                    $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
                }

                flash('success', "People {$name} successfully created", 'people');
            } else {
                flash('danger', "Save people {$name} failed");
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
        $permissions = [PERMISSION_PEOPLE_EDIT, PERMISSION_CUSTOMER_EDIT, PERMISSION_SUPPLIER_EDIT];
        AuthorizationModel::checkAuthorizedAll($permissions, false);

        if ($this->validate($this->_validation_rules($id, $this->input->post('no_person')))) {
            $branch = $this->input->post('branch');
            $parent = $this->input->post('parent');
            $type = $this->input->post('type');
            $outboundType = $this->input->post('outbound_type');
            $noPerson = $this->input->post('no_person');
            $name = $this->input->post('name');
            $gender = $this->input->post('gender');
            $birthday = sql_date_format($this->input->post('birthday'));
            $address = $this->input->post('address');
            $region = $this->input->post('region');
            $contact = $this->input->post('contact');
            $email = $this->input->post('email');
            $confirmEmailSource = $this->input->post('confirm_email_source');
            $website = $this->input->post('website');
            $taxNumber = $this->input->post('tax_number');
            // $whatsapp_group = $this->input->post('whatsapp_group');
            $user = $this->input->post('user');
            $userType = $this->input->post('type_user');
            $handlingTypes = $this->input->post('handling_types');
            $bookingTypes = $this->input->post('booking_types');
            $documentTypes = $this->input->post('document_types');
            $contract = $this->input->post('contract');
            $max_time_request = $this->input->post('max_time_request');

            $person = $this->people->getById($id);

            //Autonumber
            if($noPerson !== substr($person['no_person'], 0, 3)){
                $generate_number = $this->people->getAutoNumberPerson($noPerson);
                $format_person_number = $noPerson.$generate_number;
            }else{
                $format_person_number = $person['no_person'];
            }
            

            switch ($type) {
                case PeopleModel::$TYPE_CUSTOMER:
                    if (!AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_EDIT)) {
                        flash('danger', 'You are not authorized to edit customer', 'people');
                    }
                    break;
                case PeopleModel::$TYPE_SUPPLIER:
                    if (!AuthorizationModel::isAuthorized(PERMISSION_SUPPLIER_EDIT)) {
                        flash('danger', 'You are not authorized to edit supplier', 'people');
                    }
                    break;
                default:
                    if (!AuthorizationModel::isAuthorized(PERMISSION_PEOPLE_EDIT)) {
                        flash('danger', 'You are not authorized to edit person', 'people');
                    }
                    break;
            }

            // separate branch per customer
            if ($type != PeopleModel::$TYPE_CUSTOMER) {
                $branch = null;
            }

            if (empty($branches)) {
                $branches = [$branch];
            }

            $this->db->trans_start();

            $this->people->update([
                'id_branch' => $branch,
                'id_parent' => if_empty($parent, null),
                'type' => $type,
                'outbound_type' => $type == 'CUSTOMER' && !empty($outboundType) ? ($outboundType != 'NOT SET' ? $outboundType : null) : null,
                'no_person' => $format_person_number,
                'name' => $name,
                'gender' => $gender,
                'birthday' => if_empty($birthday, null),
                'address' => $address,
                'region' => $region,
                'contact' => $contact,
                'email' => $email,
                'confirm_email_source' => $confirmEmailSource,
                'website' => $website,
                'tax_number' => $taxNumber,
                'max_time_request' => if_empty($max_time_request, NULL),
                // 'id_user' => if_empty($user, null),
                'type_user' => if_empty($userType, 'NON USER'),
                'updated_by' => UserModel::authenticatedUserData('id'),
                'updated_at' => date('Y-m-d H:i:s')
            ], $id);
            $idPeopleBranch = $this->peopleBranch->getIdByIdCustomerIdBranch($id, $branch);

            $deleteUser = $this->peopleUser->delete(['id_people' => $id]);
            if($deleteUser){
                foreach ($user as $value) {
                    $this->peopleUser->insertUser([
                        'id_people' => $id,
                        'id_user' => $value,
                    ]);
                }
            }
            
            if ($type == PeopleModel::$TYPE_CUSTOMER) {
                if (empty($idPeopleBranch)) {
                    $this->peopleBranch->createPeopleBranch([
                        'id_customer' => $id,
                        'id_branch' => $branch,
                        // 'whatsapp_group' => $whatsapp_group,
                        'contract' => $contract,
                    ]);
                }else{
                    $idPeopleBranch=$idPeopleBranch[0]['id'];
                    $this->peopleBranch->update([
                        // 'whatsapp_group' => $whatsapp_group,
                        'contract' => $contract,
                    ],$idPeopleBranch);
                }
            }

            $this->peopleHandlingType->deletePeopleHandlingTypeByCustomer($id);
            $this->peopleBookingType->deletePeopleBookingTypeByCustomer($id);
            $this->peopleDocumentTypeReminder->deletePeopleDocumentTypeReminderByCustomer($id);
            if (!empty($handlingTypes)) {
                foreach ($handlingTypes as $handlingType) {
                    $this->peopleHandlingType->createPeopleHandlingType([
                        'id_customer' => $id,
                        'id_handling_type' => $handlingType
                    ]);
                }
            }
            if (!empty($bookingTypes)) {
                foreach ($bookingTypes as $bookingType) {
                    $this->peopleBookingType->createPeopleBookingType([
                        'id_customer' => $id,
                        'id_booking_type' => $bookingType
                    ]);
                }
            }
            if (!empty($documentTypes)) {
                foreach ($documentTypes as $documentType) {
                    $this->peopleDocumentTypeReminder->createPeopleDocumentTypeReminder([
                        'id_customer' => $id,
                        'id_document_type' => $documentType
                    ]);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                if ($type == 'CUSTOMER' && !empty($outboundType) && $person['outbound_type'] != $outboundType) {
                    $emailTo = get_setting('email_finance');
                    $emailTitle = "Customer " . $name . ' is updated from outbound type ' . if_empty($person['outbound_type'], 'NOT SET') . ' to ' . $outboundType;
                    $emailTemplate = 'emails/basic';
                    $emailData = [
                        'title' => 'Customer Outbound Type',
                        'name' => get_setting('admin_finance'),
                        'email' => $emailTo,
                        'content' => 'Customer ' . $name . ' has been updated with outbound type ' . $outboundType . '. Please review the setting whether it set properly.'
                    ];
                    $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
                }
                flash('success', "Person {$name} successfully updated", 'people');
            } else {
                flash('danger', "Update person {$name} failed");
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
        $permissions = [PERMISSION_PEOPLE_DELETE, PERMISSION_CUSTOMER_DELETE, PERMISSION_SUPPLIER_DELETE];
        AuthorizationModel::checkAuthorizedAll($permissions, false);

        $person = $this->people->getById($id);

        switch ($person['type']) {
            case PeopleModel::$TYPE_CUSTOMER:
                if (!AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_DELETE)) {
                    flash('danger', 'You are not authorized to delete customer', 'people');
                }
                break;
            case PeopleModel::$TYPE_SUPPLIER:
                if (!AuthorizationModel::isAuthorized(PERMISSION_SUPPLIER_DELETE)) {
                    flash('danger', 'You are not authorized to delete supplier', 'people');
                }
                break;
            default:
                if (!AuthorizationModel::isAuthorized(PERMISSION_PEOPLE_DELETE)) {
                    flash('danger', 'You are not authorized to delete person', 'people');
                }
                break;
        }

        if ($this->people->delete($id)) {
            flash('warning', "Person {$person['name']} successfully deleted");
        } else {
            flash('danger', "Delete person {$person['name']} failed");
        }
        redirect('people');
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


        $person = $this->people->getById($id);
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

        $person = $this->people->getById($id);

        $this->db->trans_start();
        $branch = get_active_branch('id');

        $idPeopleBranch = $this->peopleBranch->getIdByIdCustomerIdBranch($id, $branch);
        
        if ($person['type'] == PeopleModel::$TYPE_CUSTOMER) {
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

            $customer = $this->people->getPersonByName($search, $type, $page);

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

            $customer = $this->people->getPersonByNameAllBranch($search, $type, $page);

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

            $customer = $this->people->getPersonByNamePerBranch($search, $type, $page);

            echo json_encode($customer);
        }
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Component_price
 * @property ComponentModel $component
 * @property ComponentPriceModel $componentPrice
 * @property PeopleModel $people
 * @property BranchModel $branch
 * @property UnitModel $unit;
 * @property HandlingTypeModel $handlingType
 * @property Exporter $exporter
 */
class Component_price extends MY_Controller
{
    /**
     * Component price constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ComponentModel', 'component');
        $this->load->model('ComponentPriceModel', 'componentPrice');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('UnitModel', 'unit');
        $this->load->model('HandlingTypeModel', 'handlingType');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'component_price_data' => 'GET',
            'validate_price' => 'POST|PUT|PATCH',
        ]);
    }

    /**
     * Component price data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPONENT_PRICE_VIEW);

        $customerId = get_url_param('customer', 'ALL');
        $customer = $this->people->getById($customerId);

        if (get_url_param('export')) {
            $this->exporter->exportLargeResourceFromArray("Conversion", $this->componentPrice->getAllComponentPrices(get_url_param('customer')));
        } else {
            $this->render('component_price/index', compact('customer'));
        }
    }

    /**
     * Get ajax datatable invoice.
     */
    public function component_price_data()
    {
        $customerId = get_url_param('customer');
        $startData = $this->input->get('start');
        $lengthData = $this->input->get('length');
        $searchQuery = $this->input->get('search')['value'];
        $orderColumn = $this->input->get('order')[0]['column'];
        $orderColumnOrder = $this->input->get('order')[0]['dir'];

        $componentPrices = $this->componentPrice->getAllComponentPrices($customerId, $startData, $lengthData, $searchQuery, $orderColumn, $orderColumnOrder);

        $no = $startData + 1;
        foreach ($componentPrices['data'] as &$row) {
            $row['no'] = $no++;
        }

        $this->render_json($componentPrices);
    }

    /**
     * Show detail component price.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPONENT_PRICE_VIEW);

        $componentPrice = $this->componentPrice->getComponentPriceById($id);

        $this->render('component_price/view', compact('componentPrice'));
    }

    /**
     * Show create component price form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPONENT_PRICE_CREATE);

        $data = [
            'branches' => $this->branch->getAll(),
            'units' => $this->unit->getAll(),
            'handlingTypes' => $this->handlingType->getAllHandlingTypes(),
            'components' => $this->component->getAll(),
        ];

        $this->render('component_price/create', $data);
    }

    /**
     * Save new component price.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPONENT_PRICE_CREATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('branch', 'Branch', 'trim|required');
            $this->form_validation->set_rules('effective_date', 'Effective Date', 'trim|required');
            $this->form_validation->set_rules('price_type', 'Price Type', 'trim|required');
            $this->form_validation->set_rules('prices[]', 'Component Price', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $branch = $this->input->post('branch');
                $customer = if_empty($this->input->post('customer'), null);
                $effectiveDate = sql_date_format($this->input->post('effective_date'));
                $handlingType = if_empty($this->input->post('handling_type'), null);
                $component = if_empty($this->input->post('component'), null);
                $priceType = $this->input->post('price_type');
                $priceSubtype = $this->input->post('price_subtype');
                $rules = if_empty($this->input->post('rules'), []);
                $concatRules = implode(',', $rules);
                if ($priceSubtype == 'ACTIVITY') {
                    $concatRules = 'PER_ACTIVITY' . (empty($concatRules) ? '' : (',' . $concatRules));
                } elseif ($priceSubtype == 'CONTAINER') {
                    $concatRules = 'PER_CONTAINER' . (empty($concatRules) ? '' : (',' . $concatRules));
                } elseif ($priceSubtype == 'GOODS') {
                    $concatRules = 'PER_GOODS' . (empty($concatRules) ? '' : (',' . $concatRules));
                }
                $description = if_empty($this->input->post('description'), null);
                $prices = $this->input->post('prices');

                $this->db->trans_start();

                foreach ($prices as $price) {
                    $minValue = 0;
                    if (key_exists('PER_TONNAGE', $price)) {
                        $minValue = $price['PER_TONNAGE'];
                    } elseif (key_exists('PER_VOLUME', $price)) {
                        $minValue = $price['PER_VOLUME'];
                    }
                    if (!empty(extract_number($price['PRICE']))) {
                        $this->componentPrice->createComponentPrice([
                            'id_branch' => $branch,
                            'id_customer' => $customer,
                            'id_handling_type' => $handlingType,
                            'id_component' => $component,
                            'effective_date' => $effectiveDate,
                            'price' => extract_number($price['PRICE']),
                            'price_type' => $priceType,
                            'price_subtype' => $priceSubtype,
                            'rule' => rtrim($concatRules, ','),
                            'goods_unit' => key_exists('PER_UNIT', $price) ? if_empty($price['PER_UNIT'], null) : null,
                            'min_weight' => $minValue,
                            'container_type' => key_exists('PER_TYPE', $price) ? if_empty($price['PER_TYPE'], null) : null,
                            'container_size' => key_exists('PER_SIZE', $price) ? if_empty($price['PER_SIZE'], null) : null,
                            'status_danger' => key_exists('PER_DANGER', $price) ? if_empty($price['PER_DANGER'], null) : null,
                            'status_empty' => key_exists('PER_EMPTY', $price) ? ($price['PER_EMPTY'] == 'FULL' ? '0' : '1') : null,
                            'status_condition' => key_exists('PER_CONDITION', $price) ? if_empty($price['PER_CONDITION'], null) : null,
                            'description' => $description,
                            'status' => ComponentPriceModel::STATUS_PENDING,
                            'created_by' => UserModel::authenticatedUserData('id')
                        ]);
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Component price of <strong>{$priceType}</strong> successfully created", 'component_price');
                } else {
                    flash('danger', "Save component price of <strong>{$priceType}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->create();
    }

    /**
     * Show edit component price form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPONENT_PRICE_EDIT);

        $containerSizes = [20, 40, 45];
        $containerTypes = ['STD', 'HC', 'OT', 'FR'];
        $customers = $this->people->getByType(PeopleModel::$TYPE_CUSTOMER);
        $branches = $this->branch->getAll();
        $handlingTypes = $this->handlingType->getAllHandlingTypes();
        $components = $this->component->getAll();
        $units = $this->unit->getAll();
        $componentPrice = $this->componentPrice->getComponentPriceById($id);
        $data = [
            'title' => "Component Price",
            'subtitle' => "Edit component price",
            'page' => "component_price/edit",
            'customers' => $customers,
            'branches' => $branches,
            'units' => $units,
            'handlingTypes' => $handlingTypes,
            'components' => $components,
            'containerSizes' => $containerSizes,
            'containerTypes' => $containerTypes,
            'componentPrice' => $componentPrice
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Update data component price by id.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPONENT_PRICE_EDIT);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('prices', 'Component Price', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $price = $this->input->post('prices');
                $componentPrice = $this->componentPrice->getComponentPriceById($id);

                $update = $this->componentPrice->updateComponentPrice([
                    'price' => extract_number($price),
                    'updated_by' => UserModel::authenticatedUserData('id'),
                    'updated_at' => date('Y-m-d H:i:s')
                ], $id);

                $componentPriceMessage = $componentPrice['price_type'] . "-" . $componentPrice['price_subtype'] . "-" . $componentPrice['rule'];

                if ($update) {
                    flash('success', "Component price <strong>{$componentPriceMessage}</strong> successfully updated", 'component_price');
                } else {
                    flash('danger', "Update component price <strong>{$componentPriceMessage}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit($id);
    }

    /**
     * Validate document data.
     *
     * @param $type
     * @param $id
     */
    public function validate_price($type, $id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPONENT_PRICE_VALIDATE);

        if ($this->validate(['type' => 'in_list[approve,reject]'], ['type' => $type])) {

            $this->db->trans_start();

            $document = $this->componentPrice->getComponentPriceById($id);

            if ($type == 'approve') {
                $type = ComponentPriceModel::STATUS_APPROVED;
            } else {
                $type = ComponentPriceModel::STATUS_REJECTED;
            }

            $this->componentPrice->updateComponentPrice([
                'status' => $type,
                'validated_by' => UserModel::authenticatedUserData('id'),
                'validated_at' => sql_date_format('now')
            ], $id);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Component price {$document['handling_component']} is successfully {$type}");
            } else {
                flash('danger', 'Validating component price failed');
            }
        }
        redirect('component-price');
    }

    /**
     * Perform deleting component price data.
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPONENT_PRICE_DELETE);

        $componentPrice = $this->componentPrice->getComponentPriceById($id);

        if ($this->componentPrice->deleteComponentPrice($id)) {
            flash('warning', "Component price {$componentPrice['handling_component']} is successfully deleted");
        } else {
            flash('danger', "Delete component price {$componentPrice['handling_component']} failed");
        }
        redirect('component-price');
    }

}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Goods
 * @property GoodsModel $goods
 * @property AssemblyGoodsModel $assemblyGoods
 * @property AssemblyModel $assembly
 * @property PeopleModel $people
 * @property UnitModel $unit
 * @property Exporter $exporter
 */
class Goods extends MY_Controller
{
    /**
     * Goods constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('GoodsModel', 'goods');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('AssemblyGoodsModel', 'assemblyGoods');
        $this->load->model('AssemblyModel', 'assembly');
        $this->load->model('UnitModel', 'unit');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'data' => 'GET',
            'ajax_save' => 'POST',
            'ajax_get_goods_by_name' => 'GET',
            'ajax_get_goods' => 'GET'
        ]);
    }

    /**
     * Show goods data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_GOODS_VIEW);

        if (get_url_param('export')) {
            $this->exporter->exportLargeResourceFromArray("Goods", $this->goods->getAll());
        } else {
            $this->render('goods/index');
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

        $goods = $this->goods->getAll($filters);

        $this->render_json($goods);
    }

    /**
     * Show create item form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_GOODS_CREATE);

        $customer = $this->people->getById($this->input->post('customer'));

        $this->render('goods/create', compact('customer'));
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
            'customer' => 'trim|required',
            'no_goods' => 'trim|required|max_length[50]|callback_goods_exists[' . $id . ']',
            'no_hs' => 'trim|required|max_length[50]',
            'name' => 'trim|required|max_length[100]|regex_match[/^((?!,|\)|\(|-).)*$/]',
            'type_goods' => 'trim|required|max_length[200]',
            'shrink_tolerance' => 'trim|greater_than[-1]',
            'unit_weight' => 'trim|max_length[50]',
            'unit_gross_weight' => 'trim|max_length[50]',
            'unit_length' => 'trim|max_length[50]',
            'unit_width' => 'trim|max_length[50]',
            'unit_height' => 'trim|max_length[50]',
            'unit_volume' => 'trim|max_length[50]',
            'description' => 'trim|max_length[500]',
        ];
    }

    /**
     * Save new item.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_GOODS_CREATE);

        if ($this->validate()) {
            $customer = $this->input->post('customer');
            $no_goods = $this->input->post('no_goods');
            $no_hs = $this->input->post('no_hs');
            $wheyNumber = $this->input->post('whey_number');
            $name = $this->input->post('name');
            $typeGoods = $this->input->post('type_goods');
            $shrinkTolerance = $this->input->post('shrink_tolerance');
            $unitWeight = $this->input->post('unit_weight');
            $unitGrossWeight = $this->input->post('unit_gross_weight');
            $unitLength = $this->input->post('unit_length');
            $unitWidth = $this->input->post('unit_width');
            $unitHeight = $this->input->post('unit_height');
            $unitVolume = $this->input->post('unit_volume');
            $description = $this->input->post('description');

            $save = $this->goods->create([
                'id_customer' => $customer,
                'no_goods' => $no_goods,
                'no_hs' => $no_hs,
                'whey_number' => $wheyNumber,
                'name' => $name,
                'type_goods' => $typeGoods,
                'shrink_tolerance' => $shrinkTolerance,
                'unit_weight' => extract_number($unitWeight),
                'unit_gross_weight' => extract_number($unitGrossWeight),
                'unit_length' => extract_number($unitLength),
                'unit_width' => extract_number($unitWidth),
                'unit_height' => extract_number($unitHeight),
                'unit_volume' => extract_number($unitVolume),
                'description' => $description
            ]);

            if ($save) {
                flash('success', "Item {$name} successfully created", 'goods');
            } else {
                flash('danger', "Save item {$name} failed");
            }
        }
        $this->create();
    }


    /**
     * Save goods by ajax.
     */
    public function ajax_save()
    {
        $authorized = AuthorizationModel::mustAuthorized(PERMISSION_GOODS_CREATE, 'plain-text');
        if (empty($authorized)) {
            $this->form_validation->set_rules('customer', 'Customer', 'trim|required');
            $this->form_validation->set_rules('no_goods', 'Item code', 'trim|required|max_length[50]|callback_goods_exists[' . if_empty($this->input->post('id'), 0) . ']');
            $this->form_validation->set_rules('no_hs', 'HS code', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('whey_number', 'Whey number', 'trim|max_length[50]');
            $this->form_validation->set_rules('name', 'Item name', 'trim|required|max_length[100]|regex_match[/^((?!,|\)|\(|-).)*$/]');
            $this->form_validation->set_rules('type_goods', 'Item type', 'trim|required|max_length[200]');
            $this->form_validation->set_rules('unit_weight', 'Unit weight', 'trim|max_length[50]');
            $this->form_validation->set_rules('unit_gross_weight', 'Unit gross weight', 'trim|max_length[50]');
            $this->form_validation->set_rules('unit_length', 'Unit length', 'trim|max_length[50]');
            $this->form_validation->set_rules('unit_width', 'Unit width', 'trim|max_length[50]');
            $this->form_validation->set_rules('unit_height', 'Unit height', 'trim|max_length[50]');
            $this->form_validation->set_rules('unit_volume', 'Unit volume', 'trim|max_length[50]');
            $this->form_validation->set_rules('shrink_tolerance', 'Shrink tolerance', 'trim|greater_than[-1]');

            if ($this->form_validation->run() == FALSE) {
                $data = [
                    'status' => 'invalid',
                    'message' => validation_errors(),
                ];
            } else {
                $id = $this->input->post('id');
                $customer = $this->input->post('customer');
                $noGoods = $this->input->post('no_goods');
                $noHS = $this->input->post('no_hs');
                $wheyNumber = $this->input->post('whey_number');
                $name = $this->input->post('name');
                $shrinkTolerance = $this->input->post('shrink_tolerance');
                $typeGoods = $this->input->post('type_goods');
                $unitWeight = $this->input->post('unit_weight');
                $unitGrossWeight = $this->input->post('unit_gross_weight');
                $unitLength = $this->input->post('unit_length');
                $unitWidth = $this->input->post('unit_width');
                $unitHeight = $this->input->post('unit_height');
                $unitVolume = $this->input->post('unit_volume');
                $description = $this->input->post('description');

                if (empty($id)) {
                    $save = $this->goods->create([
                        'id_customer' => $customer,
                        'no_goods' => $noGoods,
                        'no_hs' => $noHS,
                        'whey_number' => if_empty($wheyNumber, null),
                        'name' => $name,
                        'shrink_tolerance' => $shrinkTolerance,
                        'type_goods' => $typeGoods,
                        'unit_weight' => extract_number($unitWeight),
                        'unit_gross_weight' => extract_number($unitGrossWeight),
                        'unit_length' => extract_number($unitLength),
                        'unit_width' => extract_number($unitWidth),
                        'unit_height' => extract_number($unitHeight),
                        'unit_volume' => extract_number($unitVolume),
                        'description' => $description
                    ]);
                    $goods = $this->goods->getById($this->db->insert_id());
                } else {
                    $save = $this->goods->update([
                        'id_customer' => $customer,
                        'no_goods' => $noGoods,
                        'no_hs' => $noHS,
                        'whey_number' => if_empty($wheyNumber, null),
                        'name' => $name,
                        'shrink_tolerance' => $shrinkTolerance,
                        'type_goods' => $typeGoods,
                        'unit_weight' => extract_number($unitWeight),
                        'unit_gross_weight' => extract_number($unitGrossWeight),
                        'unit_length' => extract_number($unitLength),
                        'unit_width' => extract_number($unitWidth),
                        'unit_height' => extract_number($unitHeight),
                        'unit_volume' => extract_number($unitVolume),
                        'description' => $description
                    ], $id);
                    $goods = $this->goods->getById($id);
                }

                if ($save) {
                    $data = [
                        'status' => 'success',
                        'message' => 'Goods was successfully updated',
                        'goods' => $goods
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
     * Show edit item form.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_GOODS_EDIT);

        $item = $this->goods->getById($id);
        $customer = $this->people->getById($item['id_customer']);
        $assembly = $this->assembly->getById($item['id_assembly']);
        $assemblyGoods = $this->assemblyGoods->getBy(['ref_assembly_goods.id_assembly' => $assembly['id']]);
        $units = $this->unit->getAll();

        $this->render('goods/edit', compact('item', 'customer', 'assembly', 'assemblyGoods', 'units'));
    }

    /**
     * Update goods data.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_GOODS_EDIT);

        if ($this->validate($this->_validation_rules($id))) {
            $customerId = $this->input->post('customer');
            $noGoods = $this->input->post('no_goods');
            $noHS = $this->input->post('no_hs');
            $wheyNumber = $this->input->post('whey_number');
            $name = $this->input->post('name');
            $typeGoods = $this->input->post('type_goods');
            $shrink_tolerance = $this->input->post('shrink_tolerance');
            $unitWeight = $this->input->post('unit_weight');
            $unitGrossWeight = $this->input->post('unit_gross_weight');
            $unitLength = $this->input->post('unit_length');
            $unitWidth = $this->input->post('unit_width');
            $unitHeight = $this->input->post('unit_height');
            $unitVolume = $this->input->post('unit_volume');
            $description = $this->input->post('description');
            $assemblyId = $this->input->post('id_assembly');
            $assemblyGoods = $this->input->post('assembly_goods');
            $assemblyQuantity = $this->input->post('qty_assembly_goods');
            $assemblyUnit = $this->input->post('units');

            $goods = $this->goods->getById($id);
            $assembly = $this->assembly->getById($goods['id_assembly']);

            if (empty($assemblyGoods) && !empty($assembly)) {
                flash('danger', "Update item {$name} failed, please select item!");
            } else {
                $this->db->trans_start();

                $this->goods->update([
                    'id_customer' => $customerId,
                    'no_goods' => $noGoods,
                    'no_hs' => $noHS,
                    'whey_number' => $wheyNumber,
                    'name' => $name,
                    'type_goods' => $typeGoods,
                    'shrink_tolerance' => $shrink_tolerance,
                    'unit_weight' => extract_number($unitWeight),
                    'unit_gross_weight' => extract_number($unitGrossWeight),
                    'unit_length' => extract_number($unitLength),
                    'unit_width' => extract_number($unitWidth),
                    'unit_height' => extract_number($unitHeight),
                    'unit_volume' => extract_number($unitVolume),
                    'description' => $description
                ], $id);

                $this->assemblyGoods->delete(['id_assembly' => $assemblyId], false);
                if(!empty($assemblyGoods)) {
                    foreach ($assemblyGoods as $key => $assemblyGoodsId) {
                        $getAssemblyGoods = $this->assemblyGoods->getAssemblyGoodsByAssemblyIdByassemblyGoods($assemblyId, $assemblyGoodsId, $assemblyUnit[$key]);

                        if (!empty($assemblyGoodsId)) {
                            if (empty($getAssemblyGoods)) {
                                $this->assemblyGoods->create([
                                    'id_assembly' => $assemblyId,
                                    'id_unit' => $assemblyUnit[$key],
                                    'assembly_goods' => $assemblyGoodsId,
                                    'quantity_assembly' => $assemblyQuantity[$key],
                                ]);
                            } else {
                                $this->assemblyGoods->update([
                                    'quantity_assembly' => $getAssemblyGoods['quantity_assembly'] + extract_number($assemblyQuantity[$key]),
                                ], $getAssemblyGoods['id']);
                            }
                            // update goods's parent
                            $this->goods->update([
                                'id_goods_parent' => $id
                            ], $assemblyGoodsId);
                        }
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Item {$name} successfully updated", 'goods');
                } else {
                    flash('danger', "Update item {$name} failed");
                }
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting item data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_GOODS_DELETE);

        $itemData = $this->goods->getById($id);
        if ($this->goods->delete($id)) {
            flash('warning', "Item {$itemData['name']} successfully deleted");
        } else {
            flash('danger', "Delete item {$itemData['name']} failed");
        }
        redirect('goods');
    }

    /**
     * Show view item form.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_GOODS_VIEW);

        $item = $this->goods->getById($id);
        $package = $this->goods->getById($item['id_goods_parent']);
        if (!empty($package)) {
            $package['total_items'] = $this->goods->getBy(['id_goods_parent' => $package['id']], 'COUNT');
        }
        $assemblies = $this->assemblyGoods->getBy(['ref_assembly_goods.id_assembly' => $item['id_assembly']]);

        $this->render('goods/view', compact('item', 'package', 'assemblies'));
    }

    /**
     * Check given username is exist or not.
     *
     * @param $no_goods
     * @param $id
     * @return bool
     */
    public function goods_exists($no_goods, $id)
    {
        $customer = $this->input->post('customer');
        if ($this->goods->isUniqueGoods($customer, $no_goods, $id)) {
            return true;
        } else {
            $this->form_validation->set_message('goods_exists', 'The %s has been created before for current customer, try another');
            return false;
        }
    }

    /**
     * Ajax get all goods data
     */
    public function ajax_get_goods_by_name()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $search = $this->input->get('q');
            $page = $this->input->get('page');

            $goods = $this->goods->getGoodsByName($search, $page);

            echo json_encode($goods);
        }
    }

    /**
     * Ajax get goods data
     */
    public function ajax_get_goods()
    {
        $id = $this->input->get('id');

        $goods = $this->goods->getById($id);

        $this->render_json($goods);
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Assembly Goods
 * @property AssemblyGoodsModel $assemblyGoods
 * @property AssemblyModel $assembly
 * @property GoodsModel $goods
 * @property PeopleModel $people
 * @property UnitModel $unit
 */
class Assembly_goods extends MY_Controller
{
    /**
     * Assembly_goods constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('AssemblyGoodsModel', 'assemblyGoods');
        $this->load->model('AssemblyModel', 'assembly');
        $this->load->model('GoodsModel', 'goods');
        $this->load->model('UnitModel', 'unit');

        $this->setFilterMethods([
            'data' => 'GET',
            'ajax_save' => 'POST',
            'ajax_get_goods_by_name' => 'GET'
        ]);
    }

    /**
     * Show goods data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ASSEMBLY_GOODS_VIEW);

        $this->render('assembly_goods/index');
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

        $assemblyGoods = $this->assemblyGoods->getAll($filters);

        $this->render_json($assemblyGoods);
    }

    /**
     * Show view item form.
     *
     * @param $id
     */
    public function view()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ASSEMBLY_GOODS_VIEW);

        $goods = $this->goods->getById($_GET['goods']);
        $assembly = $this->assembly->getById($goods['id_assembly']);
        $assemblyGoods = $this->assemblyGoods->getBy(['ref_assembly_goods.id_assembly' => $assembly['id']]);

        $this->render('assembly_goods/view', compact('goods','assembly', 'assemblyGoods'));
    }

    /**
     * Show create item form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ASSEMBLY_GOODS_CREATE);

        $goods = $this->goods->getById($_GET['goods']);
        $assemblyGoods = $this->goods->getById($this->input->post('assembly_goods'));
        $units = $this->unit->getAll();

        $this->render('assembly_goods/create', compact('goods','assemblyGoods','units'));
    }

    /**
     * Save new item.
     * @param $id
     */
    public function save($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ASSEMBLY_GOODS_CREATE);
        
        $assemblyGoods = $this->input->post('assembly_goods[]');
        $qty = $this->input->post('qty_assembly_goods[]');
        $units = $this->input->post('units[]');
        $goodsData = $this->goods->getById($id);


        if(empty($assemblyGoods)){
            flash('danger', "Save item {$goodsData['name']} failed, Please selected item !", 'goods');
        }else{

            $this->db->trans_start();

            $no_assembly = $this->assembly->getAutoNumberAssembly();
            $this->assembly->create([
                'no_assembly' => $no_assembly,
            ]);

            $assemblyId = $this->db->insert_id();
            $this->goods->update([
                'id_assembly' => $assemblyId,
            ], $id);

            foreach($assemblyGoods as $key => $assemblyGoodsId){
                $Check = $this->assemblyGoods->getAssemblyGoodsByAssemblyIdByassemblyGoods($assemblyId, $assemblyGoods[$key], $units[$key]);

                if(!empty($assemblyGoodsId)){
                    if (empty($Check)){
                        $this->assemblyGoods->create([
                            'id_assembly' => $assemblyId,
                            'id_unit' => $units[$key],
                            'assembly_goods' => $assemblyGoods[$key],
                            'quantity_assembly' => extract_number($qty[$key]),
                        ]);
                    }else{
                         $this->assemblyGoods->update([
                            'quantity_assembly' => $Check['quantity_assembly'] + extract_number($qty[$key]),
                        ], $Check['id']);
                    }
                    // update goods's parent
                    $this->goods->update([
                        'id_goods_parent' => $id
                    ], $assemblyGoodsId);
                }
            }

            $this->db->trans_complete();

            if ( $this->db->trans_status()) {
                flash('success', "Item {$goodsData['name']} successfully created", 'goods');
            } else {
                flash('danger', "Save item {$goodsData['name']} failed", 'goods');
            }
        }
    }

}
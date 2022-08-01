<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Calculator
 * @property ConversionModel $conversion
 * @property GoodsModel $goods
 * @property UnitModel $unit
 */
class Calculator extends CI_Controller
{
    /**
     * Calculator constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ConversionModel', 'conversion');
        $this->load->model('GoodsModel', 'goods');
        $this->load->model('UnitModel', 'unit');
    }

    /**
     * Show converter or calculator form.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        $goods = $this->goods->getAll();
        $units = $this->unit->getAll();
        $data = [
            'title' => "Calculator",
            'subtitle' => "Goods converter",
            'page' => "calculator/index",
            'goods' => $goods,
            'units' => $units
        ];
        $this->load->view('template/layout', $data);
    }

    public function convert()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        $goodsId = isset($_GET['goods']) ? $_GET['goods'] : '';
        $quantity = isset($_GET['quantity']) ? $_GET['quantity'] : '';
        $unitFromId = isset($_GET['unit_from']) ? $_GET['unit_from'] : '';
        $unitToId = isset($_GET['unit_to']) ? $_GET['unit_to'] : '';
        $result = $this->conversion->convert($goodsId, $unitFromId, $unitToId, $quantity);

        $goods = $this->goods->getAll();
        $units = $this->unit->getAll();
        $data = [
            'title' => "Calculator",
            'subtitle' => "Goods converter",
            'page' => "calculator/index",
            'goods' => $goods,
            'units' => $units,
            'result' => is_null($result) ? 'No conversion rule' : $result
        ];
        $this->load->view('template/layout', $data);
    }

}
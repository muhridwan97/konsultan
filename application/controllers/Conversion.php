<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Conversion
 * @property ConversionModel $conversion
 * @property GoodsModel $goods
 * @property UnitModel $unit
 * @property Exporter $exporter
 */
class Conversion extends MY_Controller
{
    /**
     * Conversion constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ConversionModel', 'conversion');
        $this->load->model('GoodsModel', 'goods');
        $this->load->model('UnitModel', 'unit');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show all conversion list data.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CONVERSION_VIEW);

        $conversions = $this->conversion->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Conversions", $conversions);
        } else {
            $this->render('conversion/index', compact('conversions'));
        }
    }

    /**
     * View conversion by id.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CONVERSION_VIEW);

        $conversion = $this->conversion->getById($id);

        $this->render('conversion/view', compact('conversion'));
    }

    /**
     * Show create form conversion.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CONVERSION_CREATE);

        $goods = $this->goods->getAll();
        $units = $this->unit->getAll();

        $this->render('conversion/create', compact('goods', 'units'));
    }

    /**
     * Edit conversion data.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CONVERSION_EDIT);

        $conversion = $this->conversion->getById($id);
        $goods = $this->goods->getAll();
        $units = $this->unit->getAll();

        $this->render('conversion/edit', compact('conversion', 'goods', 'units'));
    }

    /**
     * Get base validation rule.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        $unitFrom = $this->input->post('unit_from');
        $unitTo = $this->input->post('unit_to');
        return [
            'goods' => ['trim', 'required', 'integer',
                'callback_unique_conversion[' . $unitFrom . ',' . $unitTo . ']'
            ],
            'unit_from' => 'trim|required|integer',
            'value' => 'trim|required|integer|greater_than[0]',
            'unit_to' => 'trim|required|integer|callback_conversion_check[' . $unitFrom . ']',
        ];
    }

    /**
     * Save conversion data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CONVERSION_CREATE);

        if ($this->validate()) {
            $goods = $this->input->post('goods');
            $unitFrom = $this->input->post('unit_from');
            $value = $this->input->post('value');
            $unitTo = $this->input->post('unit_to');

            $save = $this->conversion->create([
                'id_goods' => $goods,
                'id_unit_from' => $unitFrom,
                'value' => $value,
                'id_unit_to' => $unitTo
            ]);

            if ($save) {
                flash('success', 'Conversion successfully created', 'conversion');
            } else {
                flash('danger', 'Save conversion failed');
            }
        }
        $this->create();
    }

    /**
     * Update conversion data.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CONVERSION_EDIT);

        if ($this->validate()) {
            $goods = $this->input->post('goods');
            $unitFrom = $this->input->post('unit_from');
            $value = $this->input->post('value');
            $unitTo = $this->input->post('unit_to');

            $update = $this->conversion->update([
                'id_goods' => $goods,
                'id_unit_from' => $unitFrom,
                'value' => $value,
                'id_unit_to' => $unitTo
            ], $id);

            if ($update) {
                flash('success', 'Conversion successfully updated', 'conversion');
            } else {
                flash('danger', 'Update conversion failed');
            }
        }
        $this->edit($id);
    }

    /**
     * Delete conversion data.
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CONVERSION_DELETE);

        if ($this->conversion->delete($id)) {
            flash('warning', 'Conversion successfully deleted');
        } else {
            flash('danger', 'Delete conversion failed');
        }

        redirect('conversion');
    }

    /**
     * Conversion check if unit same validation callback.
     * @param $idUnit1
     * @param $idUnit2
     * @return bool
     */
    public function conversion_check($idUnit1, $idUnit2)
    {
        if ($idUnit1 == $idUnit2) {
            $this->form_validation->set_message('conversion_check', 'Both unit cannot be the same');
            return false;
        }
        return true;
    }

    /**
     * Conversion unique of row validation callback.
     *
     * @param $idGoods
     * @param $unit
     * @return bool
     */
    public function unique_conversion($idGoods, $unit)
    {
        $idUnit = explode(',', $unit);
        if ($this->conversion->isConversionExist($idGoods, $idUnit[0], $idUnit[1])) {
            $this->form_validation->set_message('unique_conversion', 'This conversion is already exist');
            return false;
        }
        return true;
    }
}
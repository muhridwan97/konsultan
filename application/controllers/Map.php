<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Map
 * @property ReportStockModel $reportStock
 */
class Map extends CI_Controller
{
    /**
     * Map constructor.
     */
    public function __construct()
    {
        parent::__construct();

        AuthorizationModel::mustLoggedIn();

        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('LogModel', 'logHistory');
    }

    /**
     * Show field map.
     */
    public function field()
    {
        $stockContainers = $this->reportStock->getStockContainers([
            'data' => 'stock'
        ]);
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        $data = [
            'title' => "Field Map",
            'subtitle' => "Container map",
            'page' => "map/field",
            'containers' => $stockContainers
        ];

        $this->load->view('template/layout', $data);
    }

    /**
     * Show warehouse map.
     */
    public function warehouse()
    {
        $stockGoods = $this->reportStock->getStockGoods([
            'data' => 'stock'
        ]);
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        $this->logHistory->create([
            'id_branch' => $branch['id'],
            'type' => 'WAREHOUSE MAP',
            'data' => [
                'user_id' => $userData['id'],
                'name' => $userData['name'],
                'username' => $userData['username'],
                'access' => $branch['branch'],
            ]
        ]);

        $data = [
            'title' => "Warehouse Map",
            'subtitle' => "Goods map",
            'page' => "map/warehouse",
            'goods' => $stockGoods
        ];

        $this->load->view('template/layout', $data);
    }

}
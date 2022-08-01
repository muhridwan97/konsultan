<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Safe_conduct
 * @property SafeConductModel $safeConduct
 * @property SafeConductContainerModel $safeConductContainer
 * @property SafeConductGoodsModel $safeConductGoods
 */
class Cargo_manifest extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('SafeConductContainerModel', 'safeConductContainer');
        $this->load->model('SafeConductGoodsModel', 'safeConductGoods');

        $this->setFilterMethods([
            'safe_conducts' => 'GET',
            'safe_conduct_detail' => 'GET'
        ]);
    }

    /**
     * Get safe conduct data.
     */
    public function safe_conducts()
    {
        $filters = [
            'type' => get_url_param('type'),
            'min_id' => get_url_param('min_id', 0),
            'manifest_number' => get_url_param('manifest_number'),
            'min_date' => get_url_param('min_date'),
        ];
        $safeConducts = $this->safeConduct->getSafeConductData($filters);

        $withDetail = get_url_param('with_detail', false);

        if ($withDetail) {
            foreach ($safeConducts as &$safeConduct) {
                $safeConductContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($safeConduct['id']);
                foreach ($safeConductContainers as &$container) {
                    $containerGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConductContainer($container['id']);
                    $container['goods'] = $containerGoods;
                }
                $safeConductGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($safeConduct['id'], true);

                $safeConduct['containers'] = $safeConductContainers;
                $safeConduct['goods'] = $safeConductGoods;
            }
        }

        $this->render_json($safeConducts);
    }

    /**
     * Get safe conduct loading.
     *
     * @param $id
     */
    public function safe_conduct_detail($id)
    {
        $safeConductContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($id);
        foreach ($safeConductContainers as &$container) {
            $containerGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConductContainer($container['id']);
            $container['goods'] = $containerGoods;
        }
        $safeConductGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($id, true);
        $this->render_json(['containers' => $safeConductContainers, 'goods' => $safeConductGoods]);
    }
}
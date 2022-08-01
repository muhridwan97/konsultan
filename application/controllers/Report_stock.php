<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Report_stock
 * @property BookingModel $booking
 * @property PeopleModel $people
 * @property GoodsModel $goods
 * @property WarehouseModel $warehouse
 * @property PositionModel $position
 * @property PositionBlockModel $positionBlock
 * @property ReportStockModel $reportStock
 * @property Exporter $exporter
 */
class Report_stock extends MY_Controller
{
    /**
     * Report_tracking constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BookingModel', 'booking');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('GoodsModel', 'goods');
        $this->load->model('WarehouseModel', 'warehouse');
        $this->load->model('PositionModel', 'position');
        $this->load->model('PositionBlockModel', 'positionBlock');
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'stock_location' => 'GET',
        ]);
    }

    /**
     * Show stock location report
     */
    public function stock_location()
    {
        $warehouses = $this->warehouse->getAll();
        $owners = $this->people->getBy(['ref_people.id' => get_url_param('owner')]);
        $bookings = $this->booking->getBy(['bookings.id' => get_url_param('booking')]);

        // get current active
        $warehouseId = get_url_param('warehouse');
        $warehouse = $this->warehouse->getById($warehouseId);
        $positions = $this->position->getBy(['ref_positions.id_warehouse' => $warehouseId]);
        $positionBlocks = $this->positionBlock->getBy(['ref_positions.id_warehouse' => $warehouseId]);

        // build block map by grouping in position id
        $positionMaps = [];
        foreach ($positionBlocks as $block) {
            $positionMaps[$block['id_position']][] = $block;
        }

        $stockPositions = [];
        $otherResults = [];
        if (!empty($warehouseId)) {
            // get all stock position
            $filters = $_GET;
            if (!key_exists('data', $filters)) {
                $filters['data'] = 'all';
            }
            unset($filters['warehouse']);
            $stockPositionAll = $this->reportStock->getStockPosition($filters);

            // get stock current selected warehouse
            $stockPositions = array_filter($stockPositionAll, function ($stockPosition) use ($warehouseId) {
                return $stockPosition['id_warehouse'] == $warehouseId;
            });

            // for saving resource, manually check position in warehouse that no have stock (no any transaction)
            foreach ($positions as $position) {
                $isFound = false;
                foreach ($stockPositions as $stockPosition) {
                    if ($stockPosition['id_position'] == $position['id']) {
                        $isFound = true;
                        break;
                    }
                }
                if (!$isFound) {
                    $stockPositions[] = [
                        'stock_quantity' => 0,
                        'id_position' => $position['id'],
                        'position' => $position['position'],
                        'id_warehouse' => $position['id_warehouse'],
                        'warehouse' => $position['warehouse'],
                    ];
                }
            }

            // get stock other warehouse except selected in filter, group by id_warehouse
            foreach ($stockPositionAll as $stockPosition) {
                if ($stockPosition['id_warehouse'] != $warehouseId) {
                    if (key_exists($stockPosition['id_warehouse'], $otherResults)) {
                        $otherResults[$stockPosition['id_warehouse']]['stock_quantity'] += $stockPosition['stock_quantity'];
                    } else {
                        $otherResults[$stockPosition['id_warehouse']] = [
                            'id_warehouse' => $stockPosition['id_warehouse'],
                            'warehouse' => $stockPosition['warehouse'],
                            'stock_quantity' => $stockPosition['stock_quantity'],
                        ];
                    }
                }
            }

            // remove empty stock in other result to reduce the list
            $otherResults = array_filter($otherResults, function ($otherResult) {
                return $otherResult['stock_quantity'] > 0;
            });

            // change key to position id to implement hash-map (faster access by key)
            $stockPositionMaps = [];
            foreach ($stockPositions as $stockPosition) {
                $stockPositionMaps[$stockPosition['id_position']] = $stockPosition;
            }
            $stockPositions = $stockPositionMaps;
        }

        $this->render('report_stock/stock_location', compact('warehouses', 'owners', 'bookings', 'warehouse', 'positionBlocks', 'positionMaps', 'stockPositions', 'otherResults'));
    }
}

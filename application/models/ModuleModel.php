<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModuleModel extends CI_Model
{
    private $table = 'ref_modules';
    private $connection;
    private $module;

    /**
     * ModuleModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set current connection by module data.
     * @param $module
     * @return null
     */
    public function setConnectionByModule($module)
    {
        $this->module = $module;
        $config = [
            'hostname' => $module['hostname'],
            'username' => $module['username'],
            'password' => $module['password'],
            'database' => $module['database'],
            'port' => $module['port'],
            'dbdriver' => 'mysqli',
        ];
        $this->connection = $this->load->database($config, true);
        return $this->connection;
    }

    /**
     * Get all modules reference.
     * @return array
     */
    public function getAllModules()
    {
        $modules = $this->db->order_by('id', 'desc')->get($this->table)->result_array();
        return $modules;
    }

    /**
     * Get single module data.
     * @param $moduleId
     * @return mixed
     */
    public function getModuleById($moduleId)
    {
        $module = $this->db->get_where($this->table, ['id' => $moduleId]);
        return $module->row_array();
    }

    /**
     * Get module by type.
     * @param $type
     * @return mixed
     */
    public function getModulesByType($type)
    {
        $module = $this->db->get_where($this->table, ['type' => $type]);
        return $module->result_array();
    }

    /**
     * Get schema table list by module.
     * @param $moduleId
     * @return mixed
     */
    public function getTablesByModule($moduleId)
    {
        $module = $this->getModuleById($moduleId);
        $this->setConnectionByModule($module);
        $schema = $this->db->query("
          SELECT table_name, table_rows 
          FROM INFORMATION_SCHEMA.TABLES 
          WHERE TABLE_SCHEMA = '{$module['database']}'
         ");
        return $schema->result_array();
    }

    /**
     * Get schema table content by module.
     * @param $moduleId
     * @param $tableName
     * @return mixed
     */
    public function getTableContentByModule($moduleId, $tableName)
    {
        $module = $this->getModuleById($moduleId);
        $this->setConnectionByModule($module);
        $schema = $this->connection->get($tableName);
        return $schema->result_array();
    }

    /**
     * Get table fields.
     * @param $moduleId
     * @param $tableName
     * @return mixed
     */
    public function getTableFieldsByModule($moduleId, $tableName)
    {
        $module = $this->getModuleById($moduleId);
        $this->setConnectionByModule($module);
        $schema = $this->connection->list_fields($tableName);
        return $schema;
    }

    /**
     * Get list module header data.
     * @param $moduleId
     * @return array|string
     */
    public function getModuleDataListByModule($moduleId)
    {
        $module = $this->getModuleById($moduleId);
        $this->setConnectionByModule($module);

        $defaultDatabaseName = $this->db->database;

        $header = $this->connection->select($module['table_header'] . '.*')->distinct()
            ->from($module['table_header'])
            ->join($defaultDatabaseName . '.bookings', $module['table_header'] . '.NOMOR_AJU = ' . $defaultDatabaseName . '.bookings.no_reference', 'left')
            ->where($module['table_header_title'] . ' IS NOT NULL')
            ->where($module['table_header_subtitle'] . ' IS NOT NULL')
            ->where($defaultDatabaseName . '.bookings.id' . ' IS NULL')
            ->get()->result_array();

        $data = [];
        foreach ($header as $row) {
            $data[] = [
                'id' => $row[$module['table_header_id']],
                'title' => $row[$module['table_header_title']] . ' (' . $row[$module['table_header_subtitle']] . ')'
            ];
        }
        return $data;
    }

    /**
     * Get module data.
     * @param $moduleId
     * @param $dataId
     * @return array
     */
    public function getModuleDataByModuleHeader($moduleId, $dataId)
    {
        $module = $this->getModuleById($moduleId);
        $this->setConnectionByModule($module);

        $header = $this->connection->get_where($module['table_header'], ['id' => $dataId])->row_array();
        $containers = $this->connection->get_where($module['table_container'], [$module['table_container_ref'] => $header[$module['table_header_id']]])->result_array();
        $goods = $this->connection->get_where($module['table_goods'], [$module['table_goods_ref'] => $header[$module['table_header_id']]])->result_array();

        return [
            'header' => $header,
            'containers' => $containers,
            'goods' => $goods
        ];
    }

    /**
     *
     * @param $bookingTypeModule
     * @param $headerId
     * @return array
     */
    public function getModuleHeaderExtension($bookingTypeModule, $headerId)
    {
        if (!empty($bookingTypeModule['target_table'])) {
            $sourceData = $this->connection->get_where($bookingTypeModule['target_table'], [
                $this->module['table_header_id'] => $headerId
            ])->row_array();

            if ($bookingTypeModule['is_reference']) {
                $targetData = $this->db->get_where($bookingTypeModule['table'], [
                    $bookingTypeModule['field'] => $sourceData[$bookingTypeModule['target_field']]
                ])->row_array();
                if (empty($targetData)) {
                    $is_new = true;
                    $targetData = $sourceData[$bookingTypeModule['target_field']];
                } else {
                    $is_new = false;
                }
                $reference = $sourceData[$bookingTypeModule['target_field']];
            } else {
                $is_new = true;
                $targetData = $sourceData[$bookingTypeModule['target_field']];
                $reference = '';
            }
        } else {
            $is_new = true;
            $targetData = '';
            $reference = '';
        }

        return [
            'is_new' => $is_new,
            'data' => $targetData,
            'reference' => $reference
        ];
    }

    /**
     * Get module container data.
     * @param $bookingTypeId
     * @param $headerId
     * @return array
     */
    public function getModuleContainers($bookingTypeId, $headerId)
    {
        // get setting container reference
        $containerReferenceData = $this->db->get_where('ref_booking_type_modules', [
            'id_booking_type' => $bookingTypeId,
            'id_module' => $this->module['id'],
            'type' => 'CONTAINER',
            'is_reference' => 1
        ])->row_array();

        // get setting container support field
        $containerSupportData = $this->db->get_where('ref_booking_type_modules', [
            'id_booking_type' => $bookingTypeId,
            'id_module' => $this->module['id'],
            'type' => 'CONTAINER',
            'is_reference' => 0
        ])->result_array();

        $tableContainer = $this->module['table_container'];
        $tableContainerRef = $this->module['table_container_ref'];

        $containers = $this->connection->get_where($tableContainer, [
            $tableContainerRef => $headerId
        ])->result_array();
        $containerData = [];
        foreach ($containers as $container) {
            $containerMasterData = $this->db->get_where('ref_containers', [
                'no_container' => $container[$containerReferenceData['target_field']]
            ])->row_array();
            if (empty($containerMasterData)) {
                $typeRow = array_search('type', array_column($containerSupportData, 'category'));
                $sizeRow = array_search('size', array_column($containerSupportData, 'category'));
                $descriptionRow = array_search('container_description', array_column($containerSupportData, 'category'));
                $containerData[] = [
                    'id' => 0,
                    'no_container' => $container[$containerReferenceData['target_field']],
                    'type' => ($typeRow !== false && !empty($containerSupportData[$typeRow]['target_field'])) ? $container[$containerSupportData[$typeRow]['target_field']] : '',
                    'size' => ($sizeRow !== false && !empty($containerSupportData[$sizeRow]['target_field'])) ? $container[$containerSupportData[$sizeRow]['target_field']] : '',
                    'description' => ($descriptionRow !== false) ? $container[$containerSupportData[$descriptionRow]['target_field']] : '',
                    'is_new' => true
                ];
            } else {
                $containerData[] = [
                    'id' => $containerMasterData['id'],
                    'no_container' => $containerMasterData['no_container'],
                    'type' => $containerMasterData['type'],
                    'size' => $containerMasterData['size'],
                    'description' => $containerMasterData['description'],
                    'is_new' => false
                ];
            }
        }

        return $containerData;
    }

    /**
     * Get module goods data.
     * @param $bookingTypeId
     * @param $headerId
     * @param int $customerId
     * @return array
     */
    public function getModuleGoods($bookingTypeId, $headerId, $customerId = 0)
    {
        // get setting goods reference
        $goodsReferenceData = $this->db->get_where('ref_booking_type_modules', [
            'id_booking_type' => $bookingTypeId,
            'id_module' => $this->module['id'],
            'type' => 'GOODS',
            'category' => 'no_goods',
            'is_reference' => 1
        ])->row_array();

        // get setting goods unit reference
        $unitReferenceData = $this->db->get_where('ref_booking_type_modules', [
            'id_booking_type' => $bookingTypeId,
            'id_module' => $this->module['id'],
            'type' => 'GOODS',
            'category' => 'unit',
            'is_reference' => 1
        ])->row_array();

        // get setting good support field
        $goodsSupportData = $this->db->get_where('ref_booking_type_modules', [
            'id_booking_type' => $bookingTypeId,
            'id_module' => $this->module['id'],
            'type' => 'GOODS',
            'is_reference' => 0
        ])->result_array();

        $tableGoods = $this->module['table_goods'];
        $tableGoodsRef = $this->module['table_goods_ref'];

        $goods = $this->connection->get_where($tableGoods, [
            $tableGoodsRef => $headerId
        ])->result_array();

        $goodsData = [];
        foreach ($goods as $item) {
            $goodsMasterData = $this->db->get_where('ref_goods', [
                'no_goods' => $item[$goodsReferenceData['target_field']],
                'id_customer' => $customerId
            ])->row_array();

            $unitMasterData = $this->db->get_where('ref_units', [
                'unit' => $item[$unitReferenceData['target_field']]
            ])->row_array();

            $noHSRow = array_search('no_hs', array_column($goodsSupportData, 'category'));
            $nameRow = array_search('name', array_column($goodsSupportData, 'category'));
            $quantityRow = array_search('quantity', array_column($goodsSupportData, 'category'));
            $volumeRow = array_search('volume', array_column($goodsSupportData, 'category'));
            $tonnageRow = array_search('tonnage', array_column($goodsSupportData, 'category'));
            $descriptionRow = array_search('goods_description', array_column($goodsSupportData, 'category'));

            if (empty($unitMasterData)) {
                $unitData = $item[$unitReferenceData['target_field']];
                $unitId = 0;
                $unitNew = true;
            } else {
                $unitData = $unitMasterData['unit'];
                $unitId = $unitMasterData['id'];
                $unitNew = false;
            }

            if (empty($goodsMasterData)) {
                $goodsData[] = [
                    'id' => 0,
                    'unit' => $unitData,
                    'no_goods' => $item[$goodsReferenceData['target_field']],
                    'no_hs' => ($noHSRow !== false && !empty($goodsSupportData[$noHSRow]['target_field'])) ? $item[$goodsSupportData[$noHSRow]['target_field']] : '',
                    'name' => ($nameRow !== false && !empty($goodsSupportData[$nameRow]['target_field'])) ? $item[$goodsSupportData[$nameRow]['target_field']] : '',
                    'quantity' => ($quantityRow !== false && !empty($goodsSupportData[$quantityRow]['target_field'])) ? $item[$goodsSupportData[$quantityRow]['target_field']] : '',
                    'volume' => ($volumeRow !== false && !empty($goodsSupportData[$volumeRow]['target_field'])) ? $item[$goodsSupportData[$volumeRow]['target_field']] : '',
                    'tonnage' => ($tonnageRow !== false && !empty($goodsSupportData[$tonnageRow]['target_field'])) ? $item[$goodsSupportData[$tonnageRow]['target_field']] : '',
                    'description' => ($descriptionRow !== false && !empty($goodsSupportData[$descriptionRow]['target_field'])) ? $item[$goodsSupportData[$descriptionRow]['target_field']] : '',
                    'is_new' => true,
                    'id_unit' => $unitId,
                    'is_new_unit' => $unitNew
                ];
            } else {
                $goodsData[] = [
                    'id' => $goodsMasterData['id'],
                    'no_goods' => $goodsMasterData['no_goods'],
                    'no_hs' => $goodsMasterData['no_hs'],
                    'name' => $goodsMasterData['name'],
                    'unit' => $unitData,
                    'quantity' => ($quantityRow !== false && !empty($goodsSupportData[$quantityRow]['target_field'])) ? $item[$goodsSupportData[$quantityRow]['target_field']] : '',
                    'volume' => ($volumeRow !== false && !empty($goodsSupportData[$volumeRow]['target_field'])) ? $item[$goodsSupportData[$volumeRow]['target_field']] : '',
                    'tonnage' => ($tonnageRow !== false && !empty($goodsSupportData[$tonnageRow]['target_field'])) ? $item[$goodsSupportData[$tonnageRow]['target_field']] : '',
                    'description' => ($descriptionRow !== false && !empty($goodsSupportData[$descriptionRow]['target_field'])) ? $item[$goodsSupportData[$descriptionRow]['target_field']] : '',
                    'is_new' => false,
                    'id_unit' => $unitId,
                    'is_new_unit' => $unitNew
                ];
            }
        }

        return $goodsData;
    }

    /**
     * Create new module.
     * @param $data
     * @return bool
     */
    public function createModule($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update module.
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateModule($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete module.
     * @param $id
     * @return bool
     */
    public function deleteModule($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }
}
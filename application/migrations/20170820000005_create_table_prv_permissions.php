<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_table_prv_permissions extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'module' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'submodule' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'permission' => ['type' => 'VARCHAR', 'constraint' => '50', 'unique' => true],
            'description' => ['type' => 'VARCHAR', 'constraint' => '500', 'null' => true],
            'is_void' => ['type' => 'INT', 'constraint' => 1, 'default' => 0],
            'is_deleted' => ['type' => 'INT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'updated_at' => ['type' => 'TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'null' => true],
            'updated_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => true],
            'deleted_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => true]
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('prv_permissions');
        echo 'Migrate Migration_Create_table_prv_permissions<br>';

        $this->db->insert_batch('prv_permissions', [

            // seeding setting
            ['module' => 'setting', 'submodule' => 'setting', 'permission' => PERMISSION_ACCOUNT_EDIT],
            ['module' => 'setting', 'submodule' => 'setting', 'permission' => PERMISSION_SETTING_EDIT],

            // seeding master permission
            ['module' => 'master', 'submodule' => 'permission', 'permission' => PERMISSION_PERMISSION_VIEW],
            ['module' => 'master', 'submodule' => 'permission', 'permission' => PERMISSION_PERMISSION_CREATE],
            ['module' => 'master', 'submodule' => 'permission', 'permission' => PERMISSION_PERMISSION_EDIT],
            ['module' => 'master', 'submodule' => 'permission', 'permission' => PERMISSION_PERMISSION_DELETE],
            ['module' => 'master', 'submodule' => 'permission', 'permission' => PERMISSION_PERMISSION_PRINT],

            // seeding master role
            ['module' => 'master', 'submodule' => 'role', 'permission' => PERMISSION_ROLE_VIEW],
            ['module' => 'master', 'submodule' => 'role', 'permission' => PERMISSION_ROLE_CREATE],
            ['module' => 'master', 'submodule' => 'role', 'permission' => PERMISSION_ROLE_EDIT],
            ['module' => 'master', 'submodule' => 'role', 'permission' => PERMISSION_ROLE_DELETE],
            ['module' => 'master', 'submodule' => 'role', 'permission' => PERMISSION_ROLE_PRINT],

            // seeding master user
            ['module' => 'master', 'submodule' => 'user', 'permission' => PERMISSION_USER_VIEW],
            ['module' => 'master', 'submodule' => 'user', 'permission' => PERMISSION_USER_CREATE],
            ['module' => 'master', 'submodule' => 'user', 'permission' => PERMISSION_USER_EDIT],
            ['module' => 'master', 'submodule' => 'user', 'permission' => PERMISSION_USER_DELETE],
            ['module' => 'master', 'submodule' => 'user', 'permission' => PERMISSION_USER_PRINT],

            // seeding master branch
            ['module' => 'master', 'submodule' => 'branch', 'permission' => PERMISSION_BRANCH_VIEW],
            ['module' => 'master', 'submodule' => 'branch', 'permission' => PERMISSION_BRANCH_CREATE],
            ['module' => 'master', 'submodule' => 'branch', 'permission' => PERMISSION_BRANCH_EDIT],
            ['module' => 'master', 'submodule' => 'branch', 'permission' => PERMISSION_BRANCH_DELETE],
            ['module' => 'master', 'submodule' => 'branch', 'permission' => PERMISSION_BRANCH_PRINT],

            // seeding master warehouse
            ['module' => 'master', 'submodule' => 'warehouse', 'permission' => PERMISSION_WAREHOUSE_VIEW],
            ['module' => 'master', 'submodule' => 'warehouse', 'permission' => PERMISSION_WAREHOUSE_CREATE],
            ['module' => 'master', 'submodule' => 'warehouse', 'permission' => PERMISSION_WAREHOUSE_EDIT],
            ['module' => 'master', 'submodule' => 'warehouse', 'permission' => PERMISSION_WAREHOUSE_DELETE],
            ['module' => 'master', 'submodule' => 'warehouse', 'permission' => PERMISSION_WAREHOUSE_PRINT],

            // seeding master container
            ['module' => 'master', 'submodule' => 'container', 'permission' => PERMISSION_CONTAINER_VIEW],
            ['module' => 'master', 'submodule' => 'container', 'permission' => PERMISSION_CONTAINER_CREATE],
            ['module' => 'master', 'submodule' => 'container', 'permission' => PERMISSION_CONTAINER_EDIT],
            ['module' => 'master', 'submodule' => 'container', 'permission' => PERMISSION_CONTAINER_DELETE],
            ['module' => 'master', 'submodule' => 'container', 'permission' => PERMISSION_CONTAINER_PRINT],

            // seeding master goods
            ['module' => 'master', 'submodule' => 'goods', 'permission' => PERMISSION_GOODS_VIEW],
            ['module' => 'master', 'submodule' => 'goods', 'permission' => PERMISSION_GOODS_CREATE],
            ['module' => 'master', 'submodule' => 'goods', 'permission' => PERMISSION_GOODS_EDIT],
            ['module' => 'master', 'submodule' => 'goods', 'permission' => PERMISSION_GOODS_DELETE],
            ['module' => 'master', 'submodule' => 'goods', 'permission' => PERMISSION_GOODS_PRINT],

            // seeding master people
            ['module' => 'master', 'submodule' => 'people', 'permission' => PERMISSION_PEOPLE_VIEW],
            ['module' => 'master', 'submodule' => 'people', 'permission' => PERMISSION_PEOPLE_CREATE],
            ['module' => 'master', 'submodule' => 'people', 'permission' => PERMISSION_PEOPLE_EDIT],
            ['module' => 'master', 'submodule' => 'people', 'permission' => PERMISSION_PEOPLE_DELETE],
            ['module' => 'master', 'submodule' => 'people', 'permission' => PERMISSION_PEOPLE_PRINT],

            // seeding master position
            ['module' => 'master', 'submodule' => 'position', 'permission' => PERMISSION_POSITION_VIEW],
            ['module' => 'master', 'submodule' => 'position', 'permission' => PERMISSION_POSITION_CREATE],
            ['module' => 'master', 'submodule' => 'position', 'permission' => PERMISSION_POSITION_EDIT],
            ['module' => 'master', 'submodule' => 'position', 'permission' => PERMISSION_POSITION_DELETE],
            ['module' => 'master', 'submodule' => 'position', 'permission' => PERMISSION_POSITION_PRINT],

            // seeding master unit
            ['module' => 'master', 'submodule' => 'unit', 'permission' => PERMISSION_UNIT_VIEW],
            ['module' => 'master', 'submodule' => 'unit', 'permission' => PERMISSION_UNIT_CREATE],
            ['module' => 'master', 'submodule' => 'unit', 'permission' => PERMISSION_UNIT_EDIT],
            ['module' => 'master', 'submodule' => 'unit', 'permission' => PERMISSION_UNIT_DELETE],
            ['module' => 'master', 'submodule' => 'unit', 'permission' => PERMISSION_UNIT_PRINT],

            // seeding master conversion
            ['module' => 'master', 'submodule' => 'conversion', 'permission' => PERMISSION_CONVERSION_VIEW],
            ['module' => 'master', 'submodule' => 'conversion', 'permission' => PERMISSION_CONVERSION_CREATE],
            ['module' => 'master', 'submodule' => 'conversion', 'permission' => PERMISSION_CONVERSION_EDIT],
            ['module' => 'master', 'submodule' => 'conversion', 'permission' => PERMISSION_CONVERSION_DELETE],
            ['module' => 'master', 'submodule' => 'conversion', 'permission' => PERMISSION_CONVERSION_PRINT],

            // seeding master document type
            ['module' => 'master', 'submodule' => 'document type', 'permission' => PERMISSION_DOCUMENT_TYPE_VIEW],
            ['module' => 'master', 'submodule' => 'document type', 'permission' => PERMISSION_DOCUMENT_TYPE_CREATE],
            ['module' => 'master', 'submodule' => 'document type', 'permission' => PERMISSION_DOCUMENT_TYPE_EDIT],
            ['module' => 'master', 'submodule' => 'document type', 'permission' => PERMISSION_DOCUMENT_TYPE_DELETE],
            ['module' => 'master', 'submodule' => 'document type', 'permission' => PERMISSION_DOCUMENT_TYPE_PRINT],

            // seeding master booking type
            ['module' => 'master', 'submodule' => 'booking type', 'permission' => PERMISSION_BOOKING_TYPE_VIEW],
            ['module' => 'master', 'submodule' => 'booking type', 'permission' => PERMISSION_BOOKING_TYPE_CREATE],
            ['module' => 'master', 'submodule' => 'booking type', 'permission' => PERMISSION_BOOKING_TYPE_EDIT],
            ['module' => 'master', 'submodule' => 'booking type', 'permission' => PERMISSION_BOOKING_TYPE_DELETE],
            ['module' => 'master', 'submodule' => 'booking type', 'permission' => PERMISSION_BOOKING_TYPE_PRINT],

            // seeding master extension field
            ['module' => 'master', 'submodule' => 'extension field', 'permission' => PERMISSION_EXTENSION_FIELD_VIEW],
            ['module' => 'master', 'submodule' => 'extension field', 'permission' => PERMISSION_EXTENSION_FIELD_CREATE],
            ['module' => 'master', 'submodule' => 'extension field', 'permission' => PERMISSION_EXTENSION_FIELD_EDIT],
            ['module' => 'master', 'submodule' => 'extension field', 'permission' => PERMISSION_EXTENSION_FIELD_DELETE],
            ['module' => 'master', 'submodule' => 'extension field', 'permission' => PERMISSION_EXTENSION_FIELD_PRINT],

            // seeding master handling type
            ['module' => 'master', 'submodule' => 'handling type', 'permission' => PERMISSION_HANDLING_TYPE_VIEW],
            ['module' => 'master', 'submodule' => 'handling type', 'permission' => PERMISSION_HANDLING_TYPE_CREATE],
            ['module' => 'master', 'submodule' => 'handling type', 'permission' => PERMISSION_HANDLING_TYPE_EDIT],
            ['module' => 'master', 'submodule' => 'handling type', 'permission' => PERMISSION_HANDLING_TYPE_DELETE],
            ['module' => 'master', 'submodule' => 'handling type', 'permission' => PERMISSION_HANDLING_TYPE_PRINT],

            // seeding upload document
            ['module' => 'upload', 'submodule' => 'upload', 'permission' => PERMISSION_UPLOAD_VIEW],
            ['module' => 'upload', 'submodule' => 'upload', 'permission' => PERMISSION_UPLOAD_CREATE],
            ['module' => 'upload', 'submodule' => 'upload', 'permission' => PERMISSION_UPLOAD_EDIT],
            ['module' => 'upload', 'submodule' => 'upload', 'permission' => PERMISSION_UPLOAD_DELETE],
            ['module' => 'upload', 'submodule' => 'upload', 'permission' => PERMISSION_UPLOAD_PRINT],
            ['module' => 'upload', 'submodule' => 'upload', 'permission' => PERMISSION_UPLOAD_VALIDATE],

            // seeding booking
            ['module' => 'booking', 'submodule' => 'booking in', 'permission' => PERMISSION_BOOKING_IN_VIEW],
            ['module' => 'booking', 'submodule' => 'booking in', 'permission' => PERMISSION_BOOKING_IN_CREATE],
            ['module' => 'booking', 'submodule' => 'booking in', 'permission' => PERMISSION_BOOKING_IN_EDIT],
            ['module' => 'booking', 'submodule' => 'booking in', 'permission' => PERMISSION_BOOKING_IN_DELETE],
            ['module' => 'booking', 'submodule' => 'booking in', 'permission' => PERMISSION_BOOKING_IN_PRINT],

            ['module' => 'booking', 'submodule' => 'booking out', 'permission' => PERMISSION_BOOKING_OUT_VIEW],
            ['module' => 'booking', 'submodule' => 'booking out', 'permission' => PERMISSION_BOOKING_OUT_CREATE],
            ['module' => 'booking', 'submodule' => 'booking out', 'permission' => PERMISSION_BOOKING_OUT_EDIT],
            ['module' => 'booking', 'submodule' => 'booking out', 'permission' => PERMISSION_BOOKING_OUT_DELETE],
            ['module' => 'booking', 'submodule' => 'booking out', 'permission' => PERMISSION_BOOKING_OUT_PRINT],

            // seeding delivery order
            ['module' => 'delivery', 'submodule' => 'delivery order', 'permission' => PERMISSION_DELIVERY_ORDER_VIEW],
            ['module' => 'delivery', 'submodule' => 'delivery order', 'permission' => PERMISSION_DELIVERY_ORDER_CREATE],
            ['module' => 'delivery', 'submodule' => 'delivery order', 'permission' => PERMISSION_DELIVERY_ORDER_EDIT],
            ['module' => 'delivery', 'submodule' => 'delivery order', 'permission' => PERMISSION_DELIVERY_ORDER_DELETE],
            ['module' => 'delivery', 'submodule' => 'delivery order', 'permission' => PERMISSION_DELIVERY_ORDER_PRINT],

            ['module' => 'delivery', 'submodule' => 'safe conduct', 'permission' => PERMISSION_SAFE_CONDUCT_IN_VIEW],
            ['module' => 'delivery', 'submodule' => 'safe conduct', 'permission' => PERMISSION_SAFE_CONDUCT_IN_CREATE],
            ['module' => 'delivery', 'submodule' => 'safe conduct', 'permission' => PERMISSION_SAFE_CONDUCT_IN_EDIT],
            ['module' => 'delivery', 'submodule' => 'safe conduct', 'permission' => PERMISSION_SAFE_CONDUCT_IN_DELETE],
            ['module' => 'delivery', 'submodule' => 'safe conduct', 'permission' => PERMISSION_SAFE_CONDUCT_IN_PRINT],

            ['module' => 'delivery', 'submodule' => 'safe conduct', 'permission' => PERMISSION_SAFE_CONDUCT_OUT_VIEW],
            ['module' => 'delivery', 'submodule' => 'safe conduct', 'permission' => PERMISSION_SAFE_CONDUCT_OUT_CREATE],
            ['module' => 'delivery', 'submodule' => 'safe conduct', 'permission' => PERMISSION_SAFE_CONDUCT_OUT_EDIT],
            ['module' => 'delivery', 'submodule' => 'safe conduct', 'permission' => PERMISSION_SAFE_CONDUCT_OUT_DELETE],
            ['module' => 'delivery', 'submodule' => 'safe conduct', 'permission' => PERMISSION_SAFE_CONDUCT_OUT_PRINT],

            // seeding security
            ['module' => 'check point', 'submodule' => 'security', 'permission' => PERMISSION_SECURITY_CHECK_IN],
            ['module' => 'check point', 'submodule' => 'security', 'permission' => PERMISSION_SECURITY_CHECK_OUT],
            ['module' => 'check point', 'submodule' => 'security', 'permission' => PERMISSION_SECURITY_UPDATE_DATA],

            // seeding gate
            ['module' => 'check point', 'submodule' => 'gate', 'permission' => PERMISSION_GATE_CHECK_IN],
            ['module' => 'check point', 'submodule' => 'gate', 'permission' => PERMISSION_GATE_CHECK_OUT],
            ['module' => 'check point', 'submodule' => 'gate', 'permission' => PERMISSION_GATE_UPDATE_DATA],

            // seeding handling
            ['module' => 'warehouse', 'submodule' => 'handling', 'permission' => PERMISSION_HANDLING_VIEW],
            ['module' => 'warehouse', 'submodule' => 'handling', 'permission' => PERMISSION_HANDLING_CREATE],
            ['module' => 'warehouse', 'submodule' => 'handling', 'permission' => PERMISSION_HANDLING_EDIT],
            ['module' => 'warehouse', 'submodule' => 'handling', 'permission' => PERMISSION_HANDLING_DELETE],
            ['module' => 'warehouse', 'submodule' => 'handling', 'permission' => PERMISSION_HANDLING_PRINT],
            ['module' => 'warehouse', 'submodule' => 'handling', 'permission' => PERMISSION_HANDLING_VALIDATE],

            // seeding work order
            ['module' => 'warehouse', 'submodule' => 'job', 'permission' => PERMISSION_WORKORDER_TAKE_JOB],
            ['module' => 'warehouse', 'submodule' => 'job', 'permission' => PERMISSION_WORKORDER_VIEW],
            ['module' => 'warehouse', 'submodule' => 'job', 'permission' => PERMISSION_WORKORDER_CREATE],
            ['module' => 'warehouse', 'submodule' => 'job', 'permission' => PERMISSION_WORKORDER_EDIT],
            ['module' => 'warehouse', 'submodule' => 'job', 'permission' => PERMISSION_WORKORDER_DELETE],
            ['module' => 'warehouse', 'submodule' => 'job', 'permission' => PERMISSION_WORKORDER_PRINT],

            // seeding ownership
            ['module' => 'warehouse', 'submodule' => 'ownership', 'permission' => PERMISSION_OWNERSHIP_VIEW],
            ['module' => 'warehouse', 'submodule' => 'ownership', 'permission' => PERMISSION_OWNERSHIP_CREATE],
            ['module' => 'warehouse', 'submodule' => 'ownership', 'permission' => PERMISSION_OWNERSHIP_EDIT],
            ['module' => 'warehouse', 'submodule' => 'ownership', 'permission' => PERMISSION_OWNERSHIP_DELETE],
            ['module' => 'warehouse', 'submodule' => 'ownership', 'permission' => PERMISSION_OWNERSHIP_PRINT],

            // seeding report
            ['module' => 'report', 'submodule' => 'report', 'permission' => PERMISSION_REPORT_GENERAL],
            ['module' => 'report', 'submodule' => 'report', 'permission' => PERMISSION_REPORT_IN],
            ['module' => 'report', 'submodule' => 'report', 'permission' => PERMISSION_REPORT_OUT],
            ['module' => 'report', 'submodule' => 'report', 'permission' => PERMISSION_REPORT_STOCK],
        ]);
        echo '--Seeding Migration_Create_table_prv_permissions<br>';
    }

    public function down()
    {
        $this->dbforge->drop_table('prv_permissions');
        echo 'Rollback Migration_Create_table_prv_permissions<br>';
    }
}
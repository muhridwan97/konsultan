<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_table_prv_roles extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'role' => ['type' => 'VARCHAR', 'constraint' => '50', 'unique' => true],
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
        $this->dbforge->create_table('prv_roles');
        echo 'Migrate Migration_Create_table_prv_roles<br>';

        $this->db->insert_batch('prv_roles', [
            [
                'role' => 'Administrator',
                'description' => 'Top level user',
                'created_by' => 1,
            ], [
                'role' => 'Customer',
                'description' => 'Regular user level',
                'created_by' => 1,
            ], [
                'role' => 'Operational',
                'description' => 'Admin system level user',
                'created_by' => 1,
            ], [
                'role' => 'Tally',
                'description' => 'Warehouse checker level user',
                'created_by' => 1,
            ], [
                'role' => 'Security',
                'description' => 'Security post checker level user',
                'created_by' => 1,
            ], [
                'role' => 'Gate',
                'description' => 'Gate post checker level user',
                'created_by' => 1,
            ]
        ]);
        echo '--Seeding Migration_Create_table_prv_roles<br>';
    }

    public function down()
    {
        $this->dbforge->drop_table('prv_roles');
        echo 'Rollback Migration_Create_table_prv_roles<br>';
    }
}
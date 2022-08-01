<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_table_prv_users extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'name' => ['type' => 'VARCHAR', 'constraint' => '50'],
            'username' => ['type' => 'VARCHAR', 'constraint' => '50', 'unique' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => '50', 'unique' => true],
            'password' => ['type' => 'VARCHAR', 'constraint' => '200'],
            'status' => ['type' => 'ENUM("PENDING", "ACTIVATED", "SUSPENDED")', 'default' => 'PENDING'],
            'id_person' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => '11', 'null' => true],
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
        $this->dbforge->create_table('prv_users');
        echo 'Migrating Migration_Create_table_prv_users<br>';

        $this->db->insert_batch('prv_users', [
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@transcon-indonesia.com',
                'password' => password_hash('admin', PASSWORD_BCRYPT),
                'status' => 'ACTIVATED'
            ],[
                'name' => 'Customer',
                'username' => 'customer',
                'email' => 'customer@transcon-indonesia.com',
                'password' => password_hash('customer', PASSWORD_BCRYPT),
                'status' => 'ACTIVATED'
            ],[
                'name' => 'Operational',
                'username' => 'operational',
                'email' => 'operational@transcon-indonesia.com',
                'password' => password_hash('operational', PASSWORD_BCRYPT),
                'status' => 'ACTIVATED'
            ],[
                'name' => 'Tally',
                'username' => 'tally',
                'email' => 'tally@transcon-indonesia.com',
                'password' => password_hash('tally', PASSWORD_BCRYPT),
                'status' => 'ACTIVATED'
            ],[
                'name' => 'Security',
                'username' => 'security',
                'email' => 'security@transcon-indonesia.com',
                'password' => password_hash('security', PASSWORD_BCRYPT),
                'status' => 'ACTIVATED'
            ],[
                'name' => 'Gate',
                'username' => 'gate',
                'email' => 'gate@transcon-indonesia.com',
                'password' => password_hash('gate', PASSWORD_BCRYPT),
                'status' => 'ACTIVATED'
            ]
        ]);
        echo '--Seeding Migration_Create_table_prv_users<br>';
    }

    public function down()
    {
        $this->dbforge->drop_table('prv_users');
        echo 'Rollback Migration_Create_table_prv_users<br>';
    }
}
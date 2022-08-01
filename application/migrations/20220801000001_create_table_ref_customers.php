<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_table_ref_customers extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'identity_number' => ['type' => 'VARCHAR', 'constraint' => '50', 'unique' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => '50'],
            'address' => ['type' => 'VARCHAR', 'constraint' => '300', 'null' => true],
            'gender' => ['type' => 'ENUM("NONE", "MALE", "FEMALE")', 'default' => 'NONE'],
            'birthday' => ['type' => 'DATE', 'null' => true],
            'photo' => ['type' => 'VARCHAR', 'constraint' => '300', 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'contact' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'tax_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'description' => ['type' => 'VARCHAR', 'constraint' => '500', 'null' => true],
            'is_deleted' => ['type' => 'INT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'updated_at' => ['type' => 'TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'null' => true],
            'updated_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => true],
            'deleted_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => true]
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('ref_customers');
        echo 'Migrate Migration_Create_table_ref_customers' . PHP_EOL;
    }

    public function down()
    {
        $this->dbforge->drop_table('ref_customers');
        echo 'Rollback Migration_Create_table_ref_customers' . PHP_EOL;;
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_table_prv_user_roles extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'id_user' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'id_role' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
        ))
            ->add_field('CONSTRAINT fk_user_role_user FOREIGN KEY (id_user) REFERENCES prv_users(id)')
            ->add_field('CONSTRAINT fk_user_role_role FOREIGN KEY (id_role) REFERENCES prv_roles(id)');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('prv_user_roles');
        echo 'Migrate Migration_Create_table_prv_user_roles<br>';

        $this->db->insert_batch('prv_user_roles', [
            ['id_user' => 1, 'id_role' => 1],
            ['id_user' => 2, 'id_role' => 2],
            ['id_user' => 3, 'id_role' => 3],
            ['id_user' => 4, 'id_role' => 4],
            ['id_user' => 5, 'id_role' => 5],
            ['id_user' => 6, 'id_role' => 6],
        ]);
        echo '--Seeding Migration_Create_table_prv_user_roles<br>';
    }

    public function down()
    {
        $this->dbforge->drop_table('prv_user_roles');
        echo 'Rollback Migration_Create_table_prv_user_roles<br>';
    }
}
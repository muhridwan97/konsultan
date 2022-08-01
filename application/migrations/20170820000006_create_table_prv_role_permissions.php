<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_table_prv_role_permissions extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'id_role' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'id_permission' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
        ))
            ->add_field('CONSTRAINT fk_role_permission_role FOREIGN KEY (id_role) REFERENCES prv_roles(id)')
            ->add_field('CONSTRAINT fk_role_permission_permission FOREIGN KEY (id_permission) REFERENCES prv_permissions(id)');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('prv_role_permissions');
        echo 'Migrate Migration_Create_table_prv_role_permissions<br>';
    }

    public function down()
    {
        $this->dbforge->drop_table('prv_role_permissions');
        echo 'Rollback Migration_Create_table_prv_role_permissions<br>';
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_table_prv_user_tokens extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'email' => ['type' => 'VARCHAR', 'constraint' => '50'],
            'token' => ['type' => 'VARCHAR', 'constraint' => '200'],
            'type' => ['type' => 'ENUM("REGISTRATION", "PASSWORD", "OTP", "OAUTH")', 'default' => 'REGISTRATION'],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
        ))->add_field('CONSTRAINT fk_user_token_user FOREIGN KEY (email) REFERENCES prv_users(email)');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('prv_user_tokens');
        echo 'Migrating Migration_Create_table_prv_user_tokens<br>';
    }

    public function down()
    {
        $this->dbforge->drop_table('prv_user_tokens');
        echo 'Rollback Migration_Create_table_prv_user_tokens<br>';
    }
}
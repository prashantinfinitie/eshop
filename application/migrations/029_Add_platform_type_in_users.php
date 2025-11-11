<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_platform_type_in_users extends CI_Migration
{

    public function up()
    {
        $fields = array(
            'platform_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
                'after' => 'fcm_id',
            ),
        );
        $this->dbforge->add_column('users', $fields);
        // Revert the platform_type column 
        $this->db->query("
            UPDATE users
            SET platform_type = 'ios'
            WHERE platform_type IS NULL OR platform_type = '';
        ");
    }

    public function down()
    {
        $this->dbforge->drop_column('users', 'platform_type');
        // Revert the platform_type column 
        $this->db->query("
            UPDATE users
            SET platform_type = 'ios'
            WHERE platform_type IS NULL OR platform_type = '';
        ");
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_user_id_to_user_fcm extends CI_Migration {

    public function up() {
        $fields = array(
            'user_id' => array(
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => TRUE,
                'default'    => NULL,
                'after'      => 'id'
            ),
            'platform_type' => array(
                'type'       => 'VARCHAR',
                'constraint' => 256,
                'null'       => FALSE,
                'default'    => 'ios',
                'after'      => 'fcm_id'
            )
        );
        $this->dbforge->add_column('user_fcm', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('user_fcm', 'user_id');
        $this->dbforge->drop_column('user_fcm', 'platform_type');
    }
}

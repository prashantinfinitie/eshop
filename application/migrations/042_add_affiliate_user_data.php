<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_affiliate_user_data extends CI_Migration
{
    public function up()
    {
        $fields = array(
            'is_affiliate_user' => array(
                'type'       => 'TINYINT',
                'constraint' => '4',
                'null'       => FALSE,
                'default'    => 0.00,
                'after'      => 'status'
            )
        );
        $this->dbforge->add_column('users', $fields);

        // Define fields
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => FALSE,
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'unsigned' => TRUE,
            ],
            'website_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            ],
            'mobile_app_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '0 = pending, 1 = approved, 2 = rejected'
            ],
            'affiliate_wallet_balance' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE,
                'default' => 0
            ],
            'commission_type' => [
                'type' => 'ENUM("percentage","fixed")',
                'null' => FALSE,
                'default' => 'percentage',
            ],
            'default_commission_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '0.00',
                'null' => TRUE,
            ],
            'created_at TIMESTAMP',
            'updated_at TIMESTAMP default CURRENT_TIMESTAMP',
            'deleted_at TIMESTAMP default CURRENT_TIMESTAMP'
        ]);

        $this->dbforge->add_key('id', TRUE); // Primary Key
        $this->dbforge->create_table('affiliates', TRUE);
    }

    public function down()
    {
        $this->dbforge->drop_column('users', 'is_affiliate_user');
        $this->dbforge->drop_table('affiliates', TRUE);
    }
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_affiliate_tracking extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => TRUE,
                'auto_increment' => TRUE
            ],
            'affiliate_id' => [
                'type'     => 'BIGINT',
                'unsigned' => TRUE,
            ],
            'product_id' => [
                'type'     => 'BIGINT',
                'unsigned' => TRUE,
                'null'     => TRUE,
            ],
            'category_id' => [
                'type'     => 'BIGINT',
                'unsigned' => TRUE,
                'null'     => TRUE,
            ],
            'category_commission' => [
                'type'     => 'double(10,2)',
                'unsigned' => TRUE,
                'default'  => 0.00,
                'null'     => TRUE,
            ],
            'token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'unique'     => TRUE,
            ],
            'usage_count' => [
                'type'       => 'INT',
                'default'    => 0,
                'comment' => 'token used count'
            ],
            'commission_earned' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'total_order_value' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'created_at TIMESTAMP',
            'revoked_at TIMESTAMP default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP()'
        ]);

        $this->dbforge->add_key('id', TRUE); // Primary Key
        $this->dbforge->add_key('token', TRUE); // Unique

        // Create table
        $this->dbforge->create_table('affiliate_tracking');
    }

    public function down()
    {
        $this->dbforge->drop_table('affiliate_tracking');
    }
}

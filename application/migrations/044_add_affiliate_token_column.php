<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_affiliate_token_column extends CI_Migration
{
    public function up()
    {
        $fields = array(
            'affiliate_id' => [
                'type'     => 'BIGINT',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'seller_commission_amount',
            ],
            'affiliate_token' => array(
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => TRUE,
                'default'    => NULL,
                'after'     => "affiliate_id"
            ),
            'affiliate_commission' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => TRUE,
                'default'    => 0.00,
                'after'      => 'affiliate_token',
            ],
            'affiliate_commission_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => TRUE,
                'default'    => 0.00,
                'after'      => 'affiliate_commission',
            ],
            'is_affiliate_commission_settled' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
                'default'    => 0,
                'after'      => 'affiliate_commission_amount',
            ],

        );
        $this->dbforge->add_column('order_items', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('order_items', 'affiliate_id');
        $this->dbforge->drop_column('order_items', 'affiliate_token');
        $this->dbforge->drop_column('order_items', 'affiliate_commission');
        $this->dbforge->drop_column('order_items', 'is_affiliate_commission_settled');
        $this->dbforge->drop_column('order_items', 'affiliate_commission_amount');
    }
}

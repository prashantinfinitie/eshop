<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_low_stock_limit_columns extends CI_Migration
{
    public function up()
    {
        $settings = get_settings('system_settings', true);
        $low_stock_limit = isset($settings['low_stock_limit']) ? $settings['low_stock_limit'] : 5;

        $low_stock_limit_query = "UPDATE `seller_data` SET `low_stock_limit`='$low_stock_limit'";

        // Add `low_stock_limit` to `seller_data`
        if (!$this->db->field_exists('low_stock_limit', 'seller_data')) {
            $this->dbforge->add_column('seller_data', [
                'low_stock_limit' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                    'default' => 0,
                    'after' => 'commission'
                ]
            ]);
        }

        // Add `low_stock_limit` to `products`
        if (!$this->db->field_exists('low_stock_limit', 'products')) {
            $this->dbforge->add_column('products', [
                'low_stock_limit' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                    'default' => 0,
                    'after' => 'attribute_order'
                ]
            ]);
        }
        // Execute the queries
        $this->db->query($low_stock_limit_query);

    }

    public function down()
    {
        // Remove the columns if rolling back
        if ($this->db->field_exists('low_stock_limit', 'seller_data')) {
            $this->dbforge->drop_column('seller_data', 'low_stock_limit');
        }

        if ($this->db->field_exists('low_stock_limit', 'products')) {
            $this->dbforge->drop_column('products', 'low_stock_limit');
        }
    }
}

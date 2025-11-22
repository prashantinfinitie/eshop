<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_shipping_company_columns extends CI_Migration
{

    public function up()
    {
        // Add shipping_company_id to order_items
        if (!$this->db->field_exists('shipping_company_id', 'order_items')) {
            $this->dbforge->add_column('order_items', [
                'shipping_company_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE,
                    'default' => NULL,
                    'after' => 'delivery_boy_id'
                ]
            ]);
        }

        // Add shipping_company_id to consignments
        if (!$this->db->field_exists('shipping_company_id', 'consignments')) {
            $this->dbforge->add_column('consignments', [
                'shipping_company_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE,
                    'default' => NULL,
                    'after' => 'delivery_boy_id'
                ]
            ]);
        }

        // Create indexes (important for filtering from shipping panel)
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_order_items_shipping_company_id ON order_items (shipping_company_id)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_consignments_shipping_company_id ON consignments (shipping_company_id)");
    }

    public function down()
    {



        // Drop the columns only if they exist
        if ($this->db->field_exists('shipping_company_id', 'order_items')) {
            $this->dbforge->drop_column('order_items', 'shipping_company_id');
        }

        if ($this->db->field_exists('shipping_company_id', 'consignments')) {
            $this->dbforge->drop_column('consignments', 'shipping_company_id');
        }
    }
}

<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_shipping_columns_to_orders extends CI_Migration
{

    public function up()
    {
        // shipping_quote_snapshot (JSON)
        $fields1 = [
            'shipping_quote_snapshot' => [
                'type' => 'LONGTEXT',
                'null' => true
            ],
        ];
        $this->dbforge->add_column('orders', $fields1);

        // shipping_company_id (INT)
        $fields2 = [
            'shipping_company_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true
            ],
        ];
        $this->dbforge->add_column('orders', $fields2);

        // selected_quote_id (INT)
        $fields3 = [
            'selected_quote_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true
            ],
        ];
        $this->dbforge->add_column('orders', $fields3);
    }

    public function down()
    {
        $this->dbforge->drop_column('orders', 'shipping_quote_snapshot');
        $this->dbforge->drop_column('orders', 'shipping_company_id');
        $this->dbforge->drop_column('orders', 'selected_quote_id');
    }
}

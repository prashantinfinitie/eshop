<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_columns_in_order_items_table extends CI_Migration {

    public function up()
    {
         // Define the fields to be added
         $fields = array(
            'return_reason' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'deliveryboy_otp_setting_on'  // Add after 'seo_meta_description'
            ),
            'return_item_image' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'return_reason'  // Add after 'seo_meta_description'
            ),
        );

        // Add columns to the 'seller_data' table
        $this->dbforge->add_column('order_items', $fields);

    }

    public function down()
    {
        // Drop the column if it exists
        $this->dbforge->drop_column('return_reason', 'order_items');
        $this->dbforge->drop_column('return_item_image', 'order_items');

    }
}

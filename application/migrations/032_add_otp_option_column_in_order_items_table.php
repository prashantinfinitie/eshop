<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_otp_option_column_in_order_items_table extends CI_Migration {

    public function up()
    {

        // Define the fields to be added
        $fields = array(
            'deliveryboy_otp_setting_on' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'product_is_cancelable'  // Add after 'seo_meta_description'
            ),
        );

        // Add columns to the 'seller_data' table
        $this->dbforge->add_column('order_items', $fields);
    }

    public function down()
    {
        // Drop the table if it exists
        $this->dbforge->drop_column('deliveryboy_otp_setting_on', 'order_items');

    }
}

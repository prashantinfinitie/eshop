<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_coulms_in_order_items_table extends CI_Migration {

    public function up()
    {

        // Define the fields to be added
        $fields = array(
            'product_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'product_name'  // Add after the 'product_name' column
            ),
            'product_image' => array(
                'type' => 'VARCHAR',  // Changed from VARCHAR to TEXT
                'constraint' => '256',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'product_type'  // Add after 'seo_page_title'
            ),
            'product_is_cancelable' => array(
                'type' => 'INT',  // Changed from VARCHAR to TEXT
                'constraint' => '11',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'product_image'  // Add after 'seo_meta_keywords'
            ),
            'product_is_returnable' => array(
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
        $this->dbforge->drop_column('product_type', 'order_items');
        $this->dbforge->drop_column('product_image', 'order_items');
        $this->dbforge->drop_column('product_is_cancelable', 'order_items');
        $this->dbforge->drop_column('product_is_returnable', 'order_items');

    }
}

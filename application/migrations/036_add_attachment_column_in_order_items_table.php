<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_attachment_column_in_order_items_table extends CI_Migration {

    public function up()
    {
         // Define the fields to be added
         $fields = array(
            'attachment' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'variant_name'  // Add after 'seo_meta_description'
            ),
        );

        // Add columns to the 'seller_data' table
        $this->dbforge->add_column('order_items', $fields);

    }

    public function down()
    {
        // Drop the column if it exists
        $this->dbforge->drop_column('attachment', 'order_items');;

    }
}

<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_columns_in_return_request_table extends CI_Migration {

    public function up()
    {
         // Define the fields to be added
         $fields = array(
            'return_reason' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'order_item_id'  // Add after 'order_item_id'
            ),
            'return_item_image' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'return_reason'  // Add after 'return_reason'
            ),
        );

        // Add columns to the 'seller_data' table
        $this->dbforge->add_column('return_requests', $fields);

    }

    public function down()
    {
        // Drop the column if it exists
        $this->dbforge->drop_column('return_reason', 'return_requests');
        $this->dbforge->drop_column('return_item_image', 'return_requests');

    }
}

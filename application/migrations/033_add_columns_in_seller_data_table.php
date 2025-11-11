<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_columns_in_seller_data_table extends CI_Migration {

    public function up()
    {

        // Define the fields to be added
        $fields = array(
            'deliverable_zipcode_type' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'category_ids'  // Add after 'category_ids'
            ),
            'deliverable_city_type' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'deliverable_zipcode_type' // Add after 'deliverable_zipcode_type'
            ),
        );

        // Add columns to the 'seller_data' table
        $this->dbforge->add_column('seller_data', $fields);
    }

    public function down()
    {
        // Drop the table if it exists
        $this->dbforge->drop_column('deliverable_zipcode_type', 'seller_data');
        $this->dbforge->drop_column('deliverable_city_type', 'seller_data');

    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Fields_In_Seller_Data extends CI_Migration {

    public function up()
    {
        // Step 1: Add a new brand_id column
        $fields = array(
            'serviceable_zipcodes' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => TRUE,
                'after' => 'category_ids'
            ),
            'serviceable_cities' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => TRUE,
                'after' => 'serviceable_zipcodes'
            ),
        );
        $this->dbforge->add_column('seller_data', $fields);

    }

    public function down()
    {
        $this->dbforge->drop_column('seller_data', 'serviceable_zipcodes');
        $this->dbforge->drop_column('seller_data', 'serviceable_cities');

    }
}

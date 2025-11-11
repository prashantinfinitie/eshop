<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_taxed_price extends CI_Migration {

    public function up() {
        $fields = array(
            'final_taxed_price' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
        );
        $this->dbforge->add_column('product_variants', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('product_variants', 'final_taxed_price');
    }
}
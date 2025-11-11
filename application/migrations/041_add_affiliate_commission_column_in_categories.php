<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_affiliate_commission_column_in_categories extends CI_Migration
{
        public function up() {
        $fields = array(
            'is_in_affiliate' => array(
                'type'       => 'tinyint',
                'constraint' => '4',
                'null'       => FALSE,
                'default'    => 0,
                'after'      => 'status'
            ),
            'affiliate_commission' => array(
                'type'       => 'double',
                'constraint' => '10,2',
                'null'       => FALSE,
                'default'    => 0.00,
                'after'      => 'is_in_affiliate'
            )
        );
        $this->dbforge->add_column('categories', $fields);

        $fields = array(
            'is_in_affiliate' => array(
                'type'       => 'tinyint',
                'constraint' => '4',
                'null'       => FALSE,
                'default'    => 1,
                'after'      => 'status'
            )
        );
        $this->dbforge->add_column('products', $fields);
        
    }

    public function down() {
        $this->dbforge->drop_column('categories', 'is_in_affiliate');
        $this->dbforge->drop_column('categories', 'affiliate_commission');
        $this->dbforge->drop_column('products', 'is_in_affiliate');
    }
}
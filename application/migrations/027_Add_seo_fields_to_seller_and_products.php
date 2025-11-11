<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_seo_fields_to_seller_and_products extends CI_Migration
{

    public function up()
    {
        // Define the fields to be added
        $fields = array(
            'seo_page_title' => array(
                'type' => 'TEXT',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'status'  // Add after the 'status' column
            ),
            'seo_meta_keywords' => array(
                'type' => 'TEXT',  // Changed from VARCHAR to TEXT
                'null' => TRUE,
                'default' => NULL,
                'after' => 'seo_page_title'  // Add after 'seo_page_title'
            ),
            'seo_meta_description' => array(
                'type' => 'VARCHAR',  // Changed from VARCHAR to TEXT
                'constraint' => '1024',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'seo_meta_keywords'  // Add after 'seo_meta_keywords'
            ),
            'seo_og_image' => array(
                'type' => 'TEXT',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'seo_meta_description'  // Add after 'seo_meta_description'
            ),
        );

        // Add columns to the 'seller_data' table
        $this->dbforge->add_column('seller_data', $fields);

        $fields = array(
            'seo_page_title' => array(
                'type' => 'VARCHAR',
                'constraint' => '1024',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'attribute_order'  // Add after the 'attribute_order' column
            ),
            'seo_meta_keywords' => array(
                'type' => 'TEXT',  // Changed from VARCHAR to TEXT
                'null' => TRUE,
                'default' => NULL,
                'after' => 'seo_page_title'  // Add after 'seo_page_title'
            ),
            'seo_meta_description' => array(
                'type' => 'TEXT',  // Changed from VARCHAR to TEXT
                'null' => TRUE,
                'default' => NULL,
                'after' => 'seo_meta_keywords'  // Add after 'seo_meta_keywords'
            ),
            'seo_og_image' => array(
                'type' => 'TEXT',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'seo_meta_description'  // Add after 'seo_meta_description'
            ),
        );

        // Add columns to the 'products' table
        $this->dbforge->add_column('products', $fields);
    }

    public function down()
    {
        // Drop the fields if rolling back the migration
        $this->dbforge->drop_column('seller_data', 'seo_page_title');
        $this->dbforge->drop_column('seller_data', 'seo_meta_keywords');
        $this->dbforge->drop_column('seller_data', 'seo_meta_description');
        $this->dbforge->drop_column('seller_data', 'seo_og_image');


        // Drop the fields if rolling back the migration
        $this->dbforge->drop_column('products', 'seo_page_title');
        $this->dbforge->drop_column('products', 'seo_meta_keywords');
        $this->dbforge->drop_column('products', 'seo_meta_description');
        $this->dbforge->drop_column('products', 'seo_og_image');
    }
}

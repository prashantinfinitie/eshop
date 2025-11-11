<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_seo_fields_to_featured_sections extends CI_Migration {

    public function up() {
        // Define the fields to be added
        $fields = array(
            'seo_page_title' => array(
                'type' => 'VARCHAR',
                'constraint' => '1024',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'product_type'  // Add after the 'product_type' column
            ),
            'seo_meta_keywords' => array(
                'type' => 'VARCHAR',
                'constraint' => '10274',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'seo_page_title'  // Add after 'seo_page_title'
            ),
            'seo_meta_description' => array(
                'type' => 'VARCHAR',
                'constraint' => '1024',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'seo_meta_keywords'  // Add after 'seo_meta_keywords'
            ),
            'seo_og_image' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'seo_meta_description'  // Add after 'seo_meta_description'
            ),
        );

        // Add columns to the 'product_type' table
        $this->dbforge->add_column('sections', $fields);
    }

    public function down() {
        // Drop the fields if rolling back the migration
        $this->dbforge->drop_column('sections', 'seo_page_title');
        $this->dbforge->drop_column('sections', 'seo_meta_keywords');
        $this->dbforge->drop_column('sections', 'seo_meta_description');
        $this->dbforge->drop_column('sections', 'seo_og_image');
    }
}

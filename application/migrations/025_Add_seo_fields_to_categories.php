<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_seo_fields_to_categories extends CI_Migration {

    public function up() {
        // Define the fields to be added
        $fields = array(
            'seo_page_title' => array(
                'type' => 'VARCHAR',
                'constraint' => '1024',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'clicks'  // Add after the 'clicks' column
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

        // Add columns to the 'categories' table
        $this->dbforge->add_column('categories', $fields);
    }

    public function down() {
        // Drop the fields if rolling back the migration
        $this->dbforge->drop_column('categories', 'seo_page_title');
        $this->dbforge->drop_column('categories', 'seo_meta_keywords');
        $this->dbforge->drop_column('categories', 'seo_meta_description');
        $this->dbforge->drop_column('categories', 'seo_og_image');
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Store_Id_Of_Brand_Product extends CI_Migration {

    public function up()
    {
        // Step 1: Add a new brand_id column
        $fields = array(
            'brand_id' => array(
                'type' => 'INT',
                'null' => TRUE,
                'after' => 'category_id'
            ),
        );
        $this->dbforge->add_column('products', $fields);

        // Step 2: Update the brand_id column based on the current brand name
        $this->db->query("
            UPDATE products p
            JOIN brands b ON p.brand = b.name
            SET p.brand_id = b.id
        ");

        // Step 3: Drop the old brand column
        $this->dbforge->drop_column('products', 'brand');

        // Step 4: Rename brand_id column to brand (optional)
        $fields = array(
            'brand_id' => array(
                'name' => 'brand',
                'type' => 'INT',
            ),
        );
        $this->dbforge->modify_column('products', $fields);
    }

    public function down()
    {
        // Rollback: Add the old brand column back
        $fields = array(
            'brand' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
        );
        $this->dbforge->add_column('products', $fields);

        // Revert the brand_id column back to brand name
        $this->db->query("
            UPDATE products p
            JOIN brands b ON p.brand = b.id
            SET p.brand = b.name
        ");

        // Remove the brand_id column
        $this->dbforge->drop_column('products', 'brand');
    }
}

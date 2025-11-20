<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_shipping_company_quotes extends CI_Migration
{
    public function up()
    {
        // Define fields
        $fields = [
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE
            ],
            'shipping_company_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => TRUE,
            ],
            'zipcode' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
            ],
            'eta_text' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'cod_available' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'additional_charges' => [
                'type' => 'TEXT',
                'null' => TRUE,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => TRUE,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => TRUE,
            ],
        ];

        // Add fields
        $this->dbforge->add_field($fields);

        // Primary Key
        $this->dbforge->add_key('id', TRUE);

        // Add index on shipping_company_id for faster queries
        $this->dbforge->add_key('shipping_company_id');

        // Add index on zipcode for faster lookups
        $this->dbforge->add_key('zipcode');

        // Create table
        $this->dbforge->create_table('shipping_company_quotes', TRUE);

        // Set default timestamp (CI dbforge lacks default CURRENT_TIMESTAMP)
        $this->db->query("
            ALTER TABLE `shipping_company_quotes`
            MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ");

    }

    public function down()
    {
        // Drop table
        $this->dbforge->drop_table('shipping_company_quotes', TRUE);
    }
}

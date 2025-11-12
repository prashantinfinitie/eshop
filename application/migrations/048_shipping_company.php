<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_shipping_company extends CI_Migration
{

    public function up()
    {
        $fields = [];

        // KYC documents (store JSON array of filenames or URLs)
        if (! $this->db->field_exists('kyc_documents', 'users')) {
            $fields['kyc_documents'] = [
                'type' => 'LONGTEXT',
                'null' => TRUE,
                'default' => NULL
            ];
        }

        // Flag for shipping company
        if (! $this->db->field_exists('is_shipping_company', 'users')) {
            $fields['is_shipping_company'] = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => FALSE,
                'default' => '0',
                'after' => 'type'
            ];
        }

        if (!empty($fields)) {
            $this->dbforge->add_column('users', $fields);
        }

        // Add an index for faster queries
        $this->db->query("ALTER TABLE `users` ADD INDEX (`is_shipping_company`)");
    }

    public function down()
    {
        $cols = ['kyc_documents', 'is_shipping_company'];

        foreach ($cols as $col) {
            if ($this->db->field_exists($col, 'users')) {
                $this->dbforge->drop_column('users', $col);
            }
        }

        // drop the index if it exists (silent)
        $this->db->query("ALTER TABLE `users` DROP INDEX `is_shipping_company`");
    }
}

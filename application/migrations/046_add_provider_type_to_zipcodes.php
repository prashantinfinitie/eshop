<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_provider_type_to_zipcodes extends CI_Migration
{

    public function up()
    {
        // Safety: check column doesn't already exist
        if (!$this->db->field_exists('provider_type', 'zipcodes')) {
            // Using raw SQL for reliable ENUM creation
            $sql = "ALTER TABLE `zipcodes`
                    ADD COLUMN `provider_type` ENUM('company','delivery_boy') NOT NULL DEFAULT 'delivery_boy' AFTER `delivery_charges`;";
            $this->db->query($sql);
        }
    }

    public function down()
    {
        // Rollback: drop the column if present
        if ($this->db->field_exists('provider_type', 'zipcodes')) {
            $sql = "ALTER TABLE `zipcodes` DROP COLUMN `provider_type`;";
            $this->db->query($sql);
        }
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_affiliate_group extends CI_Migration
{
    public function up()
    {
        // Insert affiliate group
        $data = [
            'id' => 5, // Optional, if auto-increment is used, skip this line
            'name' => 'affiliate',
            'description' => 'Affiliate Users'
        ];

        $this->db->insert('groups', $data);
    }

    public function down()
    {
        // Remove affiliate group
        $this->db->where('name', 'affiliate')->delete('groups');
    }
}
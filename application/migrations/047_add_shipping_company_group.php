<?php

class Migration_add_shipping_company_group extends CI_Migration
{
    public function up()
    {
        // check if exists
        $exists = $this->db->get_where('groups', ['name' => 'shipping_company'])->row();
        if (!$exists) {
            $data = [
                'name' => 'shipping_company',
                'description' => 'Shipping company users',
            ];
            $this->db->insert('groups', $data);
        }
    }

    public function down()
    {
        $this->db->delete('groups', ['name' => 'shipping_company']);
    }
}

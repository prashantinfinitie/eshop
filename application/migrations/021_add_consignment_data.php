<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_consignment_data extends CI_Migration {

    public function up() {
        // Step 1: Insert into consignments
        $consignments_query = "
            INSERT INTO consignments (order_id, delivery_boy_id, name, status, active_status, otp, delivery_charge, created_at, updated_at)
            SELECT 
                oi.order_id, 
                oi.delivery_boy_id, 
                oi.product_name, 
                oi.status, 
                oi.active_status, 
                oi.otp, 
                o.delivery_charge AS delivery_charge,  
                oi.date_added AS created_at,
                oi.date_added AS updated_at
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id
            WHERE oi.active_status IN ('processed', 'shipped');
        ";

        // Step 2: Insert into consignment_items
        $consignment_items_query = "
            INSERT INTO consignment_items (consignment_id, order_item_id, product_variant_id, unit_price, quantity, created_at, updated_at)
            SELECT 
                c.id AS consignment_id, 
                oi.id AS order_item_id, 
                oi.product_variant_id, 
                oi.price AS unit_price, 
                oi.quantity, 
                oi.date_added AS created_at, 
                oi.date_added AS updated_at
            FROM consignments c
            JOIN order_items oi ON oi.order_id = c.order_id
            WHERE oi.active_status IN ('processed', 'shipped');
        ";

        // Execute the queries
        $this->db->query($consignments_query);
        $this->db->query($consignment_items_query);

        $this->load->model(['Setting_model']);
        $this->Setting_model->update_json_configurations();
    }

    public function down() {
        // Rollback logic if needed. For example, delete the consignment and consignment_items entries.
        $this->db->query("DELETE FROM consignment_items WHERE consignment_id IN (SELECT id FROM consignments);");
        $this->db->query("DELETE FROM consignments;");
    }
}

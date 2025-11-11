<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Consignment_tables extends CI_Migration
{

    public function up()
    {
        // Create consignments table
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'order_id' => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'delivery_boy_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => '1024',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'active_status' => [
                'type' => 'VARCHAR',
                'constraint' => '1024',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'otp' => [
                'type' => 'INT',
                'constraint' => 6
            ],
            'delivery_charge' => [
                'type' => 'DOUBLE',
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('consignments');

        // Create consignment_items table
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'consignment_id' => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'order_item_id' => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'product_variant_id' => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'unit_price' => [
                'type' => 'DOUBLE'
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('consignment_items');


        // Create refer_and_earn table
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'referal_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
            ),
            'identifier' => array(
                'type' => 'VARCHAR',
                'constraint' => 256,
                'null' => TRUE,
            ),
            'referal_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => FALSE,
            ),
            'is_reffral_settled' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'default' => '0',
                'null' => FALSE,
            ),
            'is_user_cashback_settled' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'default' => '0',
                'null' => FALSE,
            ),
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ]
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('refer_and_earn');

        // Add delivered_quantity column to order_items table
        $this->dbforge->add_column('order_items', [
            'delivered_quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0
            ]
        ]);
        $this->dbforge->add_column('order_tracking', [
            'consignment_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ]
        ]);
        $this->dbforge->add_column('orders', [
            'is_shiprocket_order' => [
                'type' => 'TINYINT',
                'constraint' => 4,
                'null' => FALSE,
                'default' => '0',
                'after' => 'is_pos_order'
            ]
        ]);
        $this->dbforge->add_column('orders', [
            'is_cod_collected' => [
                'type' => 'TINYINT',
                'constraint' => 4,
                'null' => FALSE,
                'default' => '0',
                'after' => 'payment_method'
            ]
        ]);
        
        // ALTER TABLE `cities` ADD `minimum_free_delivery_order_amount` DOUBLE NOT NULL DEFAULT '0' AFTER `name`;
        $this->dbforge->add_column('cities', [
            'minimum_free_delivery_order_amount' => [
                'type' => 'DOUBLE',
                'null' => FALSE,
                'default' => '0',
                'after' => 'name'
            ]
        ]);

        // ALTER TABLE `cities` ADD `delivery_charges` DOUBLE NOT NULL DEFAULT '0' AFTER `minimum_free_delivery_order_amount`;
        $this->dbforge->add_column('cities', [
            'delivery_charges' => [
                'type' => 'DOUBLE',
                'null' => FALSE,
                'default' => '0',
                'after' => 'minimum_free_delivery_order_amount'
            ]
        ]);
        
        //ALTER TABLE `order_items` ADD `tax_ids` VARCHAR(256) NULL AFTER `discounted_price`;
        $this->dbforge->add_column('order_items', [
            'tax_ids' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => TRUE,
                'after' => 'discounted_price'
            ]
        ]);

        $this->dbforge->modify_column('products', [
            'tax' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => false,
                'default' => NONE
            ]
        ]);

        $this->dbforge->modify_column('notifications', [
            'users_id' => [
                'type' => 'TEXT',
                'null' => TRUE,
                'default' => NULL
            ]
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_table('consignments');
        $this->dbforge->drop_table('consignment_items');
        $this->dbforge->drop_table('refer_and_earn');
        $this->dbforge->drop_column('order_items', 'tax_ids');
        $this->dbforge->drop_column('order_items', 'delivered_quantity');
        $this->dbforge->drop_column('order_tracking', 'consignment_id');
        $this->dbforge->drop_column('consignments', 'status');
        $this->dbforge->drop_column('orders', 'is_shiprocket_order');
        $this->dbforge->drop_column('orders', 'is_cod_collected');
        $this->dbforge->drop_column('cities', 'delivery_charges');
        $this->dbforge->drop_column('cities', 'minimum_free_delivery_order_amount');
        $this->dbforge->drop_column('products', 'tax');
        $this->dbforge->drop_column('notifications', 'users_id');
    }
}

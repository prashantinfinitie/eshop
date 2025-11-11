<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_affiliate_wallet_transactions extends CI_Migration
{
        public function up()
    {
        // Define table fields
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'auto_increment' => TRUE,
                'unsigned' => TRUE
            ],
            'user_id' => [
                'type' => 'INT',
                'unsigned' => TRUE,
                'null' => FALSE
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => FALSE,
                'default' => 0.00
            ],
            'type' => [
                'type' => 'ENUM("credit", "debit")',
                'default' => 'credit'
            ],
            'reference_type' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
                'default' => 'credit',
                'comment' => 'order(get commission), withdraw(withdrawal amount)'
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'created_at TIMESTAMP default CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP()'
        ]);

        // Set primary key
        $this->dbforge->add_key('id', TRUE);

        // Create table
        $this->dbforge->create_table('affiliate_wallet_transactions');
    }

    public function down()
    {
        $this->dbforge->drop_table('affiliate_wallet_transactions');
    }
}

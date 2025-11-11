<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_welcome_wallet_users extends CI_Migration {

    public function up()
    {

        // Define the table schema
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'mobile' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
        ));

        $this->dbforge->add_key('id', TRUE); // Set 'id' as primary key
        $this->dbforge->create_table('welcome_wallet_users'); // Create the 'users' table

        $emails = fetch_details(table: "users", fields: ["type","email", "mobile"], where: ["type" => "google"] );
        if(count($emails) > 0){
            $this->db->insert_batch("welcome_wallet_users", $emails);
        }
    }

    public function down()
    {
        // Drop the table if it exists
        $this->dbforge->drop_table('welcome_wallet_users');
    }
}

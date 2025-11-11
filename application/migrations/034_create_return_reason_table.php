<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_return_reason_table extends CI_Migration {

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
            'return_reason' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'message' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'image' => array(
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
        $this->dbforge->create_table('return_reasons'); // Create the 'users' table

    }

    public function down()
    {
        // Drop the table if it exists
        $this->dbforge->drop_table('return_reasons');
    }
}

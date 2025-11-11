<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_fields_for_remaining_cart extends CI_Migration
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database(); // Ensure database is loaded
    }

    public function up()
    {
        // Add new column to 'languages' table
        $fields = array(
            'notification_sended' => array(
                'type'           => 'INT',
                'constraint'     => '11',
                'NULL'           => FALSE,
                'default'        => '0',
                'after'          => 'is_saved_for_later',
                'comment' => '0:not send | 1:sended'
            ),
            'added_timestamp' => array(
                'type'           => 'INT',
                'constraint'     => '11',
                'after'          => 'date_created'
            ),
        );
        $this->dbforge->add_column('cart', $fields);

    }

    public function down()
    {
        $this->dbforge->drop_column('cart', 'notification_sended');
        $this->dbforge->drop_column('cart', 'added_timestamp');
    }

}

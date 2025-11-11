<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_slug_to_feature_section extends CI_Migration
{

    public function up()
    {
        // Step 1: Add the 'slug' field
        $fields = array(
            'slug' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
                'after' => 'title'  // Adds the field after 'title' if needed
            )
        );
        $this->dbforge->add_column('sections', $fields);

        // Step 2: Populate the 'slug' field based on 'title' without using a helper
        $query = $this->db->get('sections');
        $records = $query->result();

        foreach ($records as $record) {
            // Generate slug manually
            $slug = strtolower($record->title);         // Convert to lowercase
            $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);  // Remove non-alphanumeric characters
            $slug = preg_replace('/[\s-]+/', '-', $slug);       // Replace spaces and hyphens with a single hyphen

            // Update the 'slug' field for the current record
            $this->db->where('id', $record->id);
            $this->db->update('sections', ['slug' => $slug]);
        }
    }

    public function down()
    {
        // Remove the 'slug' field if rolling back the migration
        $this->dbforge->drop_column('sections', 'slug');
    }
}

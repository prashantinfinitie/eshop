<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_missing_serviceable_cities extends CI_Migration
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database(); // Ensure database is loaded
    }

    public function up()
    {
        // Add new column to 'languages' table
        $this->dbforge->add_column('languages', [
            'is_default' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
                'after' => 'is_rtl'
            ]
        ]);

        // Add new column to 'products' table
        $this->dbforge->add_column('products', [
            'attribute_order' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => TRUE,
                'after' => 'status'
            ]
        ]);

        // Call your custom method to convert webp files to png
        $this->convertAllWebpToPng();
    }

    public function down()
    {
        // Drop added columns when rolling back migration
        $this->dbforge->drop_column('languages', 'is_default');
        $this->dbforge->drop_column('products', 'attribute_order');
    }

    private function convertAllWebpToPng()
    {

        $t = &get_instance();
        $allFiles = $t->db->where([
            "extension" => "webp"
        ])->get('media')->result_array();


        $basePath = FCPATH;

        foreach ($allFiles as $row) {
            $target_path = $basePath . $row["sub_directory"];
            $rowFileName = $row["name"];

            $arr = explode(".", $rowFileName);
            if (in_array("webp", $arr)) {
                $title = $rowFileName;
                $arr[count($arr) - 1] = "png";
                $newName = $target_path . implode(".", $arr);
                $title = rtrim($title, ".webp");
                $im = imagecreatefromwebp($target_path . $rowFileName);

                if (file_exists($newName)) {
                    $fileName = $arr[count($arr) - 2];
                    $fileName1 = $arr[count($arr) - 2];
                    $temp = explode("_", $fileName);

                    // Check if the filename has any underscore separators
                    if (count($temp) == 1) {
                        $temp = explode("_", $fileName . "_1");
                    }

                    if (count($temp) != 1) {
                        // Check if the last part of the filename is a number
                        if (is_numeric(end($temp))) {
                            // Check if a file with the same name and a numeric suffix exists
                            if (file_exists($target_path . $fileName1 . "_1.png")) {
                                $temp[count($temp) - 1] = (int) end($temp) + 1;
                            } else {
                                $temp[count($temp) - 1] = (int) end($temp);
                            }
                        }
                        $fileName = implode("_", $temp);
                        $title = $fileName;
                    }

                    $arr[count($arr) - 2] = $fileName;
                    $newName = $target_path . implode(".", $arr);
                }

                $array = [
                    'name' => $title . ".png",
                    'title' => $title,
                    "extension" => "png"
                ];

                update_details($array, ['name' => $rowFileName], "media");
                unlink($target_path . $rowFileName);
                imagepng($im, $newName);
                imagedestroy($im);
            }
        }
        return true;
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Robots extends CI_Controller {

    public function index()
    {
        header("Content-Type: text/plain");
        echo "User-agent: *\n";
        echo "Disallow: /admin\n";
        echo "Disallow: /seller\n";
        echo "Allow: /sellers\n";
    }
}

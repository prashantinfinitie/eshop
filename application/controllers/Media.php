<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
defined('BASEPATH') or exit('No direct script access allowed');


class Media extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->library(['Jwt', 'Key', 'upload','pagination']);
        $this->load->model(['address_model', 'category_model', 'product_model', 'brand_model', 'cart_model', 'faq_model', 'blog_model', 'ion_auth_model']);
        $this->data['is_logged_in'] = ($this->ion_auth->logged_in()) ? 1 : 0;
        $this->data['user'] = ($this->ion_auth->logged_in()) ? $this->ion_auth->user()->row() : array();
        $this->data['settings'] = get_settings('system_settings', true);
        $this->data['web_settings'] = get_settings('web_settings', true);
        $this->data['auth_settings'] = get_settings('authentication_settings', true);
        $this->data['web_logo'] = get_settings('web_logo');
        $this->response['csrfName'] = $this->security->get_csrf_token_name();
        $this->response['csrfHash'] = $this->security->get_csrf_hash();
    }

    public function image()
    {

        try {
            // Get input parameters
            $path = $this->input->get("path");
            $width = $this->input->get("width");
            $height = $this->input->get("height");
            $quality = $this->input->get("quality") ? $this->input->get("quality") : '100';

            $segment = explode(".", $path);

            $ext = end($segment);
            
            if(in_array(strtolower($ext), $this->config->config["excluded_resize_extentions"])){
                header('Content-Type: image/gif');
                $gifFile = $path;
                readfile($gifFile);
                die;
            }


            // Check if any input parameter is missing
            if (!$path || !$width) {
                throw new Exception("Missing required input parameters");
            }

            // Load image library
            $this->load->library("image_lib");

            // Resize the original image
            $config['image_library'] = 'gd2';
            $config['maintain_ratio'] = true;
            $config['create_thumb'] = FALSE;
            $config['source_image'] =  $path;
            $config['dynamic_output'] = true;
            $config['quality'] = $quality;
            $config['width'] = $width;
            $config['height'] = $height;

            $this->image_lib->initialize($config);

            if (!$this->image_lib->resize()) {
                throw new Exception($this->image_lib->display_errors());
            }
            $this->image_lib->clear();

            // If everything is successful, return success message
        } catch (Exception $e) {
            // If an exception occurred, return error message
            echo "Error: " . $e->getMessage();
        }
    }
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Category extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation', 'upload', 'pagination']);
        $this->load->helper(['url', 'language', 'file', 'security']);
        $this->load->model(['product_model', 'category_model', 'affiliate_model']);
    }
    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {
            $this->data['main_page'] = TABLES . 'manage-categories';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Categories | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Categories |' . $settings['app_name'];

            $affiliate_categories = fetch_details('categories', ['is_in_affiliate' => 1], '*');

            $this->data['affiliate_categories'] = $affiliate_categories;


            $this->load->view('affiliate/template', $this->data);
        } else {
            redirect('affiliate/login', 'refresh');
        }
    }
}

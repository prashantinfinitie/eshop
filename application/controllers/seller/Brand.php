<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Brand extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation', 'upload']);
        $this->load->helper(['url', 'language', 'file']);
        $this->load->model(['Brand_model']);
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller() && ($this->ion_auth->seller_status() == 1 || $this->ion_auth->seller_status() == 0)) {
            $this->data['main_page'] = TABLES . 'manage-brands';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Brands | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Brands | ' . $settings['app_name'];
            if (isset($id) && !empty($id)) {
                $this->data['base_brand_url'] = base_url() . 'seller/brand/brand_list?id=' . $id;
            } else {
                $this->data['base_brand_url']  = base_url() . 'seller/brand/brand_list';
            }
            $this->load->view('seller/template', $this->data);
        } else {
            redirect('seller/login', 'refresh');
        }
    }

    public function brand_list()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller() && ($this->ion_auth->seller_status() == 1 || $this->ion_auth->seller_status() == 0)) {
            return $this->Brand_model->get_brand_list();
        }
    }
}

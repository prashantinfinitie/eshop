<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Policy extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model('Setting_model');
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller()) {
            $this->data['main_page'] = VIEW . 'privacy-policy';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Privacy Policy | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Privacy Policy | ' . $settings['app_name'];
            $this->data['privacy_policy'] = get_settings('seller_privacy_policy');
            $this->load->view('seller/template', $this->data);
        } else {
            redirect('seller/login', 'refresh');
        }
    }
    public function terms_conditions()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller()) {
            $this->data['main_page'] = VIEW . 'terms-conditions';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Terms And Conditions | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Terms And Conditions | ' . $settings['app_name'];
            $this->data['terms_n_condition'] = get_settings('seller_terms_conditions');
            $this->load->view('seller/template', $this->data);
        } else {
            redirect('seller/login', 'refresh');
        }
    }
}
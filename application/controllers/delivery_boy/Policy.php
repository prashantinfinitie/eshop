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
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_delivery_boy()) {
            $this->data['main_page'] = TABLES . 'privacy-policy';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Privacy Policy | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Privacy Policy | ' . $settings['app_name'];
            $this->data['privacy_policy'] = get_settings('delivery_boy_privacy_policy');
            $this->load->view('delivery_boy/template', $this->data);
        } else {
            redirect('delivery_boy/login', 'refresh');
        }
    }
    public function terms_conditions()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_delivery_boy()) {
            $this->data['main_page'] = TABLES . 'terms-conditions';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Terms And Conditions | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Terms And Conditions | ' . $settings['app_name'];
            $this->data['terms_n_condition'] = get_settings('delivery_boy_terms_conditions');
            $this->load->view('delivery_boy/template', $this->data);
        } else {
            redirect('delivery_boy/login', 'refresh');
        }
    }
}
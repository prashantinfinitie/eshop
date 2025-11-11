<?php
defined('BASEPATH') or exit('No direct script access allowed');

class System_health extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model('Setting_model');

        if (!has_permissions('read', 'settings')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        } else {
            $this->session->set_flashdata('authorize_flag', "");
        }
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = VIEW . 'system_health';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'System Health | ' . $settings['app_name'];
            $this->data['meta_description'] = 'System Health  | ' . $settings['app_name'];
            $this->data['currency'] = get_settings('currency');
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

}

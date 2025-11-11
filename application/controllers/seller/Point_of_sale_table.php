<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Point_of_sale_table extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation', 'upload', 'pagination']);
        $this->load->helper(['url', 'language', 'file']);
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->load->model(['point_of_sale_model', 'customer_model', 'ion_auth_model', 'transaction_model', 'order_model']);
    }
    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller() && ($this->ion_auth->seller_status() == 1 || $this->ion_auth->seller_status() == 0)) {
            $this->data['main_page'] = TABLES . 'manage-point_of_sale_orders';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Point of Sale | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Point of Sale |' . $settings['app_name'];

            $seller_id = $this->session->userdata('user_id');
            
            $this->data['categories'] = json_decode(json_encode($this->category_model->get_seller_categories($seller_id)), 1);
            $this->data['csrfName'] = $this->security->get_csrf_token_name();
            $this->data['csrfHash'] = $this->security->get_csrf_hash();
            $this->load->view('seller/template', $this->data);
        } else {
            redirect('seller/login', 'refresh');
        }
    }



    public function point_of_sale_orders()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller() && ($this->ion_auth->seller_status() == 1 || $this->ion_auth->seller_status() == 0)) {

            $seller_id = $this->ion_auth->get_user_id();
            
            return $this->point_of_sale_model->get_pos_orders(NULL, 0, 10, 'oi.id', 'DESC', $seller_id, 1);
        } else {
            redirect('seller/login', 'refresh');
        }
    }

}

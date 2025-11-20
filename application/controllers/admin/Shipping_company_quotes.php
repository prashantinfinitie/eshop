<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Shipping_company_quotes extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Shipping_company_quotes_model', 'quotes_model');
        $this->load->library('form_validation');
        $this->load->helper(['function_helper']);
        if (!has_permissions('read', 'shipping_company_quotes')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        }
    }

    public function index()
    {
        $this->data['main_page'] = TABLES . 'manage-shipping-company-quotes';
        $settings = get_settings('system_settings', true);
        $this->data['title'] = 'Manage Shipping Company Quotes | ' . $settings['app_name'];
        $this->data['meta_description'] = 'Manage Shipping Company Quotes';
        $this->load->view('admin/template', $this->data);
    }

    // ajax listing for admin table (bootstrap-table)
    public function ajax_list()
    {
        if (!has_permissions('read', 'shipping_company_quotes')) {
            echo json_encode(['total' => 0, 'rows' => []]);
            exit;
        }

        $offset = $this->input->get('offset') ?: 0;
        $limit = $this->input->get('limit') ?: 10;
        $search = $this->input->get('search') ?: '';
        $sort = $this->input->get('sort') ?: 'id';
        $order = $this->input->get('order') ?: 'DESC';

        // optional filters
        $filters = [
            'company_id' => $this->input->get('filter_company') ?: null,
            'zipcode' => $this->input->get('filter_zipcode') ?: null,
            'is_active' => $this->input->get('filter_is_active') !== null ? $this->input->get('filter_is_active') : ''
        ];

        $data = $this->quotes_model->list_for_admin($offset, $limit, $search, $filters, $sort, $order);
        echo json_encode($data);
    }

    // toggle active (admin)
    public function toggle_active()
    {
        if (print_msg(!has_permissions('update', 'shipping_company_quotes'), PERMISSION_ERROR_MSG, 'shipping_company_quotes')) {
            return false;
        }
        $this->form_validation->set_rules('id', 'ID', 'trim|required|numeric');
        $this->form_validation->set_rules('is_active', 'Active', 'trim|required|in_list[0,1]');
        if (!$this->form_validation->run()) {
            echo json_encode(['error' => true, 'message' => validation_errors()]);
            return;
        }
        $id = $this->input->post('id', true);
        $is_active = (int)$this->input->post('is_active', true);
        $ok = $this->db->where('id', $id)->update('shipping_company_quotes', ['is_active' => $is_active]);
        echo json_encode(['error' => !$ok, 'message' => $ok ? 'Updated' : 'Update failed']);
    }

    // admin create/update/delete can mirror the company endpoints with permission checks
    // ... implement similarly to company controller if you need admin create/edit
}

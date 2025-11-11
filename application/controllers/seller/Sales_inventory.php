<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sales_inventory extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model(['Sales_inventory_model', 'Order_model', 'Product_model']);
        $this->session->set_flashdata('authorize_flag', "");
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller()) {
            $this->data['main_page'] = TABLES . 'sales-inventory';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Sales Inventory Report Management |' . $settings['app_name'];
            $this->data['meta_description'] = 'eShop - Multivendor | Sales Inventory Report Management';
            $this->load->view('seller/template', $this->data);
        } else {
            redirect('seller/login', 'refresh');
        }
    }

    public function get_seller_sales_inventory_list()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller()) {
            return $this->Sales_inventory_model->get_seller_sales_inventory_list();
        } else {
            redirect('seller/login', 'refresh');
        }
    }

    public function top_selling_products()
    {
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        $user_id = $this->session->userdata('user_id');

        $this->db->select('p.name as name, SUM(oi.quantity) as total_sales')
            ->join('product_variants pv', 'pv.id=oi.product_variant_id', 'inner')
            ->join('products p', 'p.id=pv.product_id', 'inner')
            ->where('oi.seller_id', $user_id)
            ->where('p.status', 1);

        if (!empty($start_date) && !empty($end_date)) {
            $this->db->where('DATE(oi.date_added) >=', $start_date);
            $this->db->where('DATE(oi.date_added) <=', $end_date);
        }

        $res = $this->db->group_by('p.id')
            ->order_by('total_sales', 'DESC')
            ->limit(10)
            ->get('order_items oi')
            ->result_array();

        // Format for Google Charts
        $result = array();
        $result[0][] = 'Product';
        $result[0][] = 'Sales';
        array_walk($res, function ($v, $k) use (&$result) {
            $result[$k + 1][] = $v['name'];
            $result[$k + 1][] = intval($v['total_sales']);
        });

        echo json_encode(array_values($result));
    }
}

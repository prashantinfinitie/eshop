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
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = TABLES . 'sales-inventory';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Sales Inventory Report Management |' . $settings['app_name'];
            $this->data['meta_description'] = 'eShop - Multivendor | Sales Inventory Report Management';
            $this->data['sellers'] = $this->db->select(' u.username as seller_name, u.id as seller_id')
                ->join('users_groups ug', ' ug.user_id = u.id ')
                ->where(['ug.group_id' => '4'])
                ->get('users u')->result_array();
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function get_sales_inventory_list()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            return $this->Sales_inventory_model->get_sales_inventory_list();
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function top_selling_products()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $start_date = $this->input->get('start_date');
            $end_date = $this->input->get('end_date');
            $seller_id = $this->input->get('seller_id');

            $this->db->select('p.name as name, SUM(oi.quantity) as total_sales')
                ->join('product_variants pv', 'pv.id=oi.product_variant_id', 'inner')
                ->join('products p', 'p.id=pv.product_id', 'inner')
                ->where('p.status', 1);

            if (!empty($seller_id)) {
                $this->db->where('oi.seller_id', $seller_id);
            }

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
        } else {
            redirect('admin/login', 'refresh');
        }
    }
}
?>
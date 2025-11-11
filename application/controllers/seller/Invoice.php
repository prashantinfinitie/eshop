<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Invoice extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model(['Invoice_model', 'Order_model']);
        $this->session->set_flashdata('authorize_flag', "");
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller() && ($this->ion_auth->seller_status() == 1 || $this->ion_auth->seller_status() == 0)) {
            $this->data['main_page'] = VIEW . 'invoice';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Invoice Management |' . $settings['app_name'];
            $this->data['meta_description'] = 'Ekart | Invoice Management';
            if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {
                $seller_id = $this->session->userdata('user_id');
                $s_user_data = fetch_details('users', ['id' => $seller_id], 'email,mobile,address,country_code');
                $seller_data = fetch_details('seller_data', ['user_id' => $seller_id], 'store_name,pan_number,tax_name,tax_number,authorized_signature');
                $res = $this->Order_model->get_order_details(['o.id' => $_GET['edit_id'], 'oi.seller_id' => $seller_id], true);
                if (!empty($res)) {
                    
                    $items = [];
                    $promo_code = [];
                    if (!empty($res[0]['promo_code'])) {
                        $promo_code = fetch_details('promo_codes', ['promo_code' => trim($res[0]['promo_code'])]);
                    }
                    foreach ($res as $row) {
                        $temp['product_id'] = $row['product_id'];
                        $temp['seller_id'] = $row['seller_id'];
                        $temp['product_variant_id'] = $row['product_variant_id'];
                        $temp['pname'] = $row['pname'];
                        $temp['quantity'] = $row['quantity'];
                        $temp['discounted_price'] = $row['discounted_price'];
                        $temp['tax_ids'] = $row['tax_ids'];
                        $temp['tax_percent'] = $row['tax_percent'];
                        $temp['tax_amount'] = $row['tax_amount'];
                        $temp['price'] = $row['price'];
                        $temp['product_price'] = $row['product_price'];
                        $temp['delivery_boy'] = $row['delivery_boy'];
                        $temp['mobile_number'] = $row['mobile_number'];
                        $temp['active_status'] = $row['oi_active_status'];
                        $temp['hsn_code'] = $row['hsn_code'];
                        $temp['is_prices_inclusive_tax'] = $row['is_prices_inclusive_tax'];
                        array_push($items, $temp);
                    }
                    $this->data['order_detls'] = $res;
                    $this->data['items'] = $items;
                    $this->data['s_user_data'] = $s_user_data;
                    $this->data['seller_data'] = $seller_data;
                    $this->data['promo_code'] = $promo_code;
                    $this->data['settings'] = get_settings('system_settings', true);
                    $this->load->view('seller/template', $this->data);
                } else {
                    redirect('seller/orders/', 'refresh');
                }
            } else {
                redirect('seller/orders/', 'refresh');
            }
        } else {
            redirect('seller/login', 'refresh');
        }
    }
    public function sales_invoice()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller() && ($this->ion_auth->seller_status() == 1 || $this->ion_auth->seller_status() == 0)) {
            $this->data['main_page'] = TABLES . 'sales-invoice';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Sales Invoice |' . $settings['app_name'];
            $this->data['meta_description'] = 'Ekart';
            $this->load->view('seller/template', $this->data);
        } else {
            redirect('seller/login', 'refresh');
        }
    }
    public function get_sales_list()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller() && ($this->ion_auth->seller_status() == 1 || $this->ion_auth->seller_status() == 0)) {
            return $this->Invoice_model->get_sales_list();
        } else {
            redirect('seller/login', 'refresh');
        }
    }
    public function consignment_invoice()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller() && ($this->ion_auth->seller_status() == 1 || $this->ion_auth->seller_status() == 0)) {
            $this->data['main_page'] = VIEW . 'consignment-invoice';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Cosignment Invoice |' . $settings['app_name'];
            $this->data['meta_description'] = 'Cosignment Invoice';
            if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {
                $seller_id = $this->session->userdata('user_id');
                $s_user_data = fetch_details('users', ['id' => $seller_id], 'email,mobile,address,country_code');
                $seller_data = fetch_details('seller_data', ['user_id' => $seller_id], 'store_name,pan_number,tax_name,tax_number,authorized_signature');
                $consignments = fetch_details('consignments', ['id' => $_GET['edit_id']]);
                $consignment_items = fetch_details('consignment_items', ['consignment_id' => $_GET['edit_id']]);
                $orders = fetch_details('order_items', ['order_id' => $consignments[0]['order_id']]);
                $consignment_details = view_all_consignments(consignment_id: $_GET['edit_id']);
                $res = $this->Order_model->get_order_details(['o.id' => $consignments[0]['order_id']], true);

                if (!empty($res)) {
                    $item_details = [];
                    foreach ($consignment_items as $key => $row) {
                        foreach ($orders as $pro) {
                            if ($pro['id'] == $row['order_item_id']) {
                                $consignment_items[$key]['pname'] = $pro['product_name'];
                                $consignment_items[$key]['price'] = $pro['price'];
                                $consignment_items[$key]['product_id'] = $pro['product_id'];
                                $consignment_items[$key]['discounted_price'] = $pro['discounted_price'];
                                $consignment_items[$key]['tax_ids'] = $pro['tax_ids'];
                                $consignment_items[$key]['tax_percent'] = $pro['tax_percent'];
                                $consignment_items[$key]['tax_amount'] = $pro['tax_amount'];
                                $consignment_items[$key]['delivery_boy'] = $pro['delivery_boy'];
                                $consignment_items[$key]['active_status'] = $pro['oi_active_status'];
                                $consignment_items[$key]['hsn_code'] = $pro['hsn_code'];
                                $consignment_items[$key]['is_prices_inclusive_tax'] = $pro['is_prices_inclusive_tax'];
                            }
                        }
                    }
                    $this->data['consignments'] = $consignments;
                    $this->data['consignment_items'] = $consignment_items;
                    $this->data['consignment_details'] = $consignment_details['data'][0] ?? [];
                    $this->data['order_detls'] = $res;
                    $this->data['items'] = $item_details;
                    $this->data['s_user_data'] = $s_user_data;
                    $this->data['seller_data'] = $seller_data;
                    $this->data['settings'] = get_settings('system_settings', true);
                    $this->load->view('seller/template', $this->data);
                } else {
                    redirect('seller/orders/', 'refresh');
                }
            } else {
                redirect('seller/orders/', 'refresh');
            }
        } else {
            redirect('seller/login', 'refresh');
        }
    }
}

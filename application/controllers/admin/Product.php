<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation', 'upload']);
        $this->load->helper(['url', 'language', 'file']);
        $this->load->model(['product_model', 'category_model', 'rating_model', 'product_faqs_model', 'affiliate_model']);

        if (!has_permissions('read', 'product')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        }
    }
    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = TABLES . 'manage-product';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Product Management | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Product Management |' . $settings['app_name'];
            if (isset($_GET['edit_id'])) {
                $this->data['fetched_data'] = fetch_details('product_faqs', ['id' => $_GET['edit_id']]);
            }
            $this->data['categories'] = $this->category_model->get_categories();
            $this->data['sellers'] = $this->db->select(' u.username as seller_name,u.id as seller_id,sd.category_ids,sd.id as seller_data_id  ')
                ->join('users_groups ug', ' ug.user_id = u.id ')
                ->join('seller_data sd', ' sd.user_id = u.id ')
                ->where(['ug.group_id' => '4'])
                ->where(['sd.status' => 1])
                ->get('users u')->result_array();

            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function create_product()
    {

        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'product';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Add Product | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Add Product | ' . $settings['app_name'];
            $this->data['taxes'] = fetch_details('taxes', null, '*');
            $this->data['countries'] = fetch_details('countries', null, 'name,id');
            $this->data['shipping_method'] = get_settings('shipping_method', true);
            $this->data['payment_method'] = get_settings('payment_method', true);
            $this->data['system_settings'] = get_settings('system_settings', true);
            $this->data['cities'] = fetch_details('cities', "", 'name,id', '5');

            if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {
                $seller_id = fetch_details('products', ['id' => $_GET['edit_id']], 'seller_id')[0]['seller_id'];
                $this->data['shipping_data'] = fetch_details('pickup_locations', ['status' => 1, 'seller_id' => $seller_id], 'id,pickup_location');
            } else {
                $this->data['shipping_data'] = fetch_details('pickup_locations', ['status' => 1], 'id,pickup_location');
            }

            if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {
                $this->data['seller'] = $this->db->select(' u.username as seller_name,u.id as seller_id,sd.category_ids,sd.store_name,sd.id as seller_data_id  ')
                    ->join('users_groups ug', ' ug.user_id = u.id ')
                    ->join('seller_data sd', ' sd.user_id = u.id ')
                    ->where(['ug.group_id' => '4'])
                    ->where(['u.id' => $seller_id])
                    ->where(['sd.status' => '1'])
                    ->get('users u')->result_array();
                $this->data['title'] = 'Update Product | ' . $settings['app_name'];
                $this->data['meta_description'] = 'Update Product | ' . $settings['app_name'];
                $product_details = fetch_details('products', ['id' => $_GET['edit_id']], '*');
                $this->data['brands'] = fetch_details('brands', ['id' => $product_details[0]['brand']], 'name,id');

                $countries = fetch_details('countries', ['name' => $product_details[0]['made_in']], 'name');
                $this->data['tax_details'] = $this->db->where_in('id', explode(',', $product_details[0]['tax']))->get('	taxes')->result_array();

                if (!empty($product_details)) {
                    $this->data['product_details'] = $product_details;
                    $this->data['product_variants'] = get_variants_values_by_pid($_GET['edit_id']);
                    $product_attributes = fetch_details('product_attributes', ['product_id' => $_GET['edit_id']]);
                    if (!empty($product_attributes) && !empty($product_details)) {
                        $this->data['product_attributes'] = $product_attributes;
                    }
                } else {
                    redirect('admin/product/create_product', 'refresh');
                }
            }


            $attributes = $this->db->select('attr_val.id,attr.name as attr_name ,attr_set.name as attr_set_name,attr_val.value')
                ->join('attributes attr', 'attr.id=attr_val.attribute_id')
                ->join('attribute_set attr_set', 'attr_set.id=attr.attribute_set_id')
                ->where(['attr.status' => 1, 'attr_set.status' => 1, 'attr_val.status' => 1])
                ->get('attribute_values attr_val')->result_array();

            $attributes_refind = array();

            for ($i = 0; $i < count($attributes); $i++) {
                if (!array_key_exists($attributes[$i]['attr_set_name'], $attributes_refind)) {
                    $attributes_refind[$attributes[$i]['attr_set_name']] = array();
                    for ($j = 0; $j < count($attributes); $j++) {
                        if ($attributes[$i]['attr_set_name'] == $attributes[$j]['attr_set_name']) {
                            if (!array_key_exists($attributes[$j]['attr_name'], $attributes_refind[$attributes[$i]['attr_set_name']])) {
                                $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']] = array();
                            }
                            $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']][$j]['id'] = $attributes[$j]['id'];
                            $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']][$j]['text'] = $attributes[$j]['value'];
                            $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']][$j]['data-values'] = $attributes[$j]['value'];
                            $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']] = array_values($attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']]);
                        }
                    }
                }
            }
            $this->data['categories'] = $this->category_model->get_categories();

            $affiliate_categories = fetch_details('categories', ['is_in_affiliate' => 1], 'id');
            $affiliate_categories = array_column($affiliate_categories, 'id');
            $this->data['affiliate_categories'] = implode(',', $affiliate_categories);

            $this->data['attributes_refind'] = $attributes_refind;
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function product_order()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (!has_permissions('read', 'product_order')) {
                $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
                redirect('admin/home', 'refresh');
            }

            $this->data['main_page'] = TABLES . 'products-order';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Product Order | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Product Order | ' . $settings['app_name'];
            $this->data['categories'] = $this->category_model->get_categories();
            $products = $this->db->select('*')->order_by('row_order')->get('products')->result_array();
            $this->data['product_result'] = $products;
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function get_variants_by_id()
    {
        $attr_values = array();
        $final_variant_ids = array();
        $variant_ids = json_decode($this->input->get('variant_ids'));
        $attributes_values = json_decode($this->input->get('attributes_values'));
        foreach ($attributes_values as $a => $b) {
            foreach ($b as $key => $value) {
                array_push($attr_values, $value);
            }
        }
        $res = $this->db->select('id,value')->where_in('id', $attr_values)->get('attribute_values')->result_array();

        for ($i = 0; $i < count($variant_ids); $i++) {
            for ($j = 0; $j < count($variant_ids[$i]); $j++) {
                $k = array_search($variant_ids[$i][$j], array_column($res, 'id'));
                $final_variant_ids[$i][$j] = $res[$k];
            }
        }
        $response['result'] = $final_variant_ids;
        print_r(json_encode($response));
    }

    public function fetch_attributes_by_id()
    {
        $variants = get_variants_values_by_pid($_GET['edit_id']);
        $res['attr_values'] = get_attribute_values_by_pid($_GET['edit_id']);
        $res['pre_selected_variants_names'] = (!empty($variants)) ? $variants[0]['attr_name'] : null;
        $res['pre_selected_variants_ids'] = $variants;
        $response['csrfName'] = $this->security->get_csrf_token_name();
        $response['csrfHash'] = $this->security->get_csrf_hash();
        $response['result'] = $res;
        print_r(json_encode($response));
    }

    public function fetch_attribute_values_by_id($id = NULL)
    {
        if (isset($id) && !empty($id)) {
            $aid = $id;
        } else {
            $aid = $_GET['id'];
        }
        $variant_ids = get_attribute_values_by_id($aid);
        print_r(json_encode($variant_ids));
    }

    public function fetch_variants_values_by_pid()
    {
        $res = get_variants_values_by_pid($_GET['edit_id']);
        $response['result'] = $res;
        print_r(json_encode($response));
    }



    public function update_product_order()
    {

        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('update', 'product_order'), PERMISSION_ERROR_MSG, 'product_order', false)) {
                return false;
            }

            $i = 0;
            $temp = array();
            foreach ($_GET['product_id'] as $row) {
                $temp[$row] = $i;
                $data = [
                    'row_order' => $i
                ];
                $data = escape_array($data);
                $this->db->where(['id' => $row])->update('products', $data);
                $i++;
            }

            $response['error'] = false;
            $response['message'] = 'Product Order Saved !';

            print_r(json_encode($response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }


    public function search_category_wise_products()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            $this->db->select('p.*');
            if ($_GET['cat_id'] == 0) {
                $data = "";
            } else {
                $this->db->where('p.category_id', $_GET['cat_id']);
                $this->db->or_where('c.parent_id', $_GET['cat_id']);
            }
            $product_data = json_encode($this->db->order_by('row_order')->join('categories c', 'p.category_id = c.id')->get('products p')->result_array());
            //this print_r is used for return data so don't remove it 
            print_r($product_data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function delete_product()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('delete', 'product'), PERMISSION_ERROR_MSG, 'product')) {
                return false;
            }
            if (delete_details(['product_id' => $_GET['id']], 'product_variants')) {

                delete_details(['id' => $_GET['id']], 'products');
                delete_details(['product_id' => $_GET['id']], 'product_attributes');
                delete_details(['product_id' => $_GET['id']], 'product_faqs');
                $response['error'] = false;
                $response['message'] = 'Deleted Succesfully';
            } else {
                $response['error'] = true;
                $response['message'] = 'Something Went Wrong';
            }
            print_r(json_encode($response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function add_product()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (isset($_POST['edit_product_id'])) {
                if (print_msg(!has_permissions('update', 'product'), PERMISSION_ERROR_MSG, 'product')) {
                    return false;
                }
            } else {
                if (print_msg(!has_permissions('create', 'product'), PERMISSION_ERROR_MSG, 'product')) {
                    return false;
                }
            }
            $this->form_validation->set_rules('pro_input_name', 'Product Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('short_description', 'Short Description', 'trim|required');
            $this->form_validation->set_rules('category_id', 'Category Id', 'trim|required|xss_clean', array('required' => 'Category is required'));
            $this->form_validation->set_rules('pro_input_tax[]', 'Tax', 'trim|xss_clean');
            $this->form_validation->set_rules('pro_input_image', 'Image', 'trim|required|xss_clean', array('required' => 'Image is required'));
            $this->form_validation->set_rules('made_in', 'Made In', 'trim|xss_clean');
            $this->form_validation->set_rules('brand', 'Brand', 'trim|xss_clean');
            $this->form_validation->set_rules('product_type', 'Product type', 'trim|required|xss_clean');
            $this->form_validation->set_rules('seller_id', 'Seller', 'trim|required|xss_clean');
            $this->form_validation->set_rules('total_allowed_quantity', 'Total Allowed Quantity', 'trim|xss_clean');
            $this->form_validation->set_rules('minimum_order_quantity', 'Minimum Order Quantity', 'trim|xss_clean');
            $this->form_validation->set_rules('quantity_step_size', 'Quantity Step Size', 'trim|xss_clean');
            $this->form_validation->set_rules('warranty_period', 'Warranty Period', 'trim|xss_clean');
            $this->form_validation->set_rules('guarantee_period', 'Guarantee Period', 'trim|xss_clean');
            $this->form_validation->set_rules('hsn_code', 'HSN_Code', 'trim|xss_clean');
            $this->form_validation->set_rules('video', 'Video', 'trim|xss_clean');
            $this->form_validation->set_rules('video_type', 'Video Type', 'trim|xss_clean');
            $this->form_validation->set_rules('deliverable_type', 'Deliverable Type', 'trim|xss_clean');
            $this->form_validation->set_rules('product_identity', 'product_identity', 'trim|xss_clean');


            if (isset($_POST['video_type']) && $_POST['video_type'] != '') {
                if ($_POST['video_type'] == 'youtube' || $_POST['video_type'] == 'vimeo') {
                    $this->form_validation->set_rules('video', 'Video link', 'trim|required|xss_clean', array('required' => " Please paste a %s in the input box. "));
                } else {
                    $this->form_validation->set_rules('pro_input_video', 'Video file', 'trim|required|xss_clean', array('required' => " Please choose a %s to be set. "));
                }
            }
            if (isset($_POST['download_allowed']) && $_POST['download_allowed'] != '' && !empty($_POST['download_allowed']) && $_POST['download_allowed'] == 'on') {
                $this->form_validation->set_rules('download_link_type', 'Download Link Type', 'required|xss_clean');
                if (isset($_POST['download_link_type']) && $_POST['download_link_type'] != '' && !empty($_POST['download_link_type']) && $_POST['download_link_type'] == 'self_hosted') {
                    $this->form_validation->set_rules('pro_input_zip', 'Zip file ', 'required|xss_clean');
                }
                if (isset($_POST['download_link_type']) && $_POST['download_link_type'] != '' && !empty($_POST['download_link_type']) && $_POST['download_link_type'] == 'add_link') {
                    $this->form_validation->set_rules('download_link', 'Digital Product URL/Link', 'required|xss_clean');
                }
            }

            if (((int) $_POST['quantity_step_size'] > (int) $_POST['minimum_order_quantity']) && ((int) $_POST['quantity_step_size'] > (int) $_POST['total_allowed_quantity'])) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'Please enter valid Quantity Step size';
                print_r(json_encode($this->response));
                return true;
            }

            if (isset($_POST['tags']) && $_POST['tags'] != '') {
                $_POST['tags'] = json_decode($_POST['tags'], 1);
                $tags = array_column($_POST['tags'], 'value');
                $_POST['tags'] = implode(",", $tags);
            }

            if (isset($_POST['is_cancelable']) && $_POST['is_cancelable'] == '1') {
                $this->form_validation->set_rules('cancelable_till', 'Till which status', 'trim|required|xss_clean');
            }
            if (isset($_POST['cod_allowed'])) {
                $this->form_validation->set_rules('cod_allowed', 'COD allowed', 'trim|xss_clean');
            }
            if (isset($_POST['is_prices_inclusive_tax'])) {
                $this->form_validation->set_rules('is_prices_inclusive_tax', 'Tax included in prices', 'trim|xss_clean');
            }
            if (isset($_POST['deliverable_type']) && !empty($_POST['deliverable_type']) && ($_POST['deliverable_type'] == INCLUDED || $_POST['deliverable_type'] == EXCLUDED)) {
                $this->form_validation->set_rules('deliverable_zipcodes[]', 'Deliverable Zipcodes', 'trim|required|xss_clean');
            }
            if (isset($_POST['deliverable_city_type']) && !empty($_POST['deliverable_city_type']) && ($_POST['deliverable_city_type'] == INCLUDED || $_POST['deliverable_city_type'] == EXCLUDED)) {
                $this->form_validation->set_rules('deliverable_cities[]', 'Deliverable Cities', 'trim|required|xss_clean');
            }

            // If product type is simple			
            if (isset($_POST['product_type']) && $_POST['product_type'] == 'simple_product' || $_POST['product_type'] == 'digital_product') {

                $this->form_validation->set_rules('simple_price', 'Price', 'trim|required|numeric|greater_than[0]|greater_than_equal_to[' . $this->input->post('simple_special_price') . ']|xss_clean');
                $this->form_validation->set_rules('simple_special_price', 'Special Price', 'trim|numeric|greater_than[0]|less_than_equal_to[' . $this->input->post('simple_price') . ']|xss_clean');

                if ($_POST['product_type'] == 'simple_product') {
                    $this->form_validation->set_rules('weight', 'Weight', 'trim|required|numeric|greater_than[0]|xss_clean');
                    $this->form_validation->set_rules('height', 'Height', 'trim|required|numeric|greater_than[0]|xss_clean');
                    $this->form_validation->set_rules('length', 'Length', 'trim|required|numeric|greater_than[0]|xss_clean');
                    $this->form_validation->set_rules('breadth', 'Breadth', 'trim|required|numeric|greater_than[0]|xss_clean');
                    $this->form_validation->set_rules('low_stock_limit', 'Low Stock Limit', 'trim|numeric|xss_clean');
                }

                if (isset($_POST['simple_product_stock_status']) && in_array($_POST['simple_product_stock_status'], array('0', '1')) && $_POST['product_type'] != 'digital_product') {

                    $this->form_validation->set_rules('product_sku', 'SKU', 'trim|xss_clean');
                    $this->form_validation->set_rules('product_total_stock', 'Total Stock', 'trim|required|numeric|xss_clean');
                    $this->form_validation->set_rules('simple_product_stock_status', 'Stock Status', 'trim|required|numeric|xss_clean');
                }
            } elseif (isset($_POST['product_type']) && $_POST['product_type'] == 'variable_product') { //If product type is variant	

                $this->form_validation->set_rules('weight[]', 'Weight', 'trim|required|numeric|greater_than[0]|xss_clean');
                $this->form_validation->set_rules('height[]', 'Height', 'trim|required|numeric|greater_than[0]|xss_clean');
                $this->form_validation->set_rules('length[]', 'Length', 'trim|required|numeric|greater_than[0]|xss_clean');
                $this->form_validation->set_rules('breadth[]', 'Breadth', 'trim|required|numeric|greater_than[0]|xss_clean');
                $this->form_validation->set_rules('low_stock_limit', 'Low Stock Limit', 'trim|numeric|xss_clean');
                if (isset($_POST['variant_stock_status']) && $_POST['variant_stock_status'] == '0') {
                    if ($_POST['variant_stock_level_type'] == "product_level") {

                        $this->form_validation->set_rules('sku_pro_type', 'SKU', 'trim|xss_clean');
                        $this->form_validation->set_rules('total_stock_variant_type', 'Total Stock', 'trim|required|xss_clean');
                        $this->form_validation->set_rules('variant_stock_status', 'Stock Status', 'trim|required|xss_clean');
                        if (isset($_POST['variant_price']) && isset($_POST['variant_special_price'])) {
                            foreach ($_POST['variant_price'] as $key => $value) {
                                $this->form_validation->set_rules('variant_price[' . $key . ']', 'Price', 'trim|required|numeric|xss_clean|greater_than[0]|greater_than_equal_to[' . $this->input->post('variant_special_price[' . $key . ']') . ']');
                                $this->form_validation->set_rules('variant_special_price[' . $key . ']', 'Special Price', 'trim|numeric|xss_clean|greater_than[0]|less_than_equal_to[' . $this->input->post('variant_price[' . $key . ']') . ']');
                            }
                        } else {
                            $this->form_validation->set_rules('variant_price', 'Price', 'trim|required|numeric|xss_clean|greater_than[0]|greater_than_equal_to[' . $this->input->post('variant_special_price[0]') . ']');
                            $this->form_validation->set_rules('variant_special_price', 'Special Price', 'trim|numeric|xss_clean|greater_than[0]|less_than_equal_to[' . $this->input->post('variant_price') . ']');
                        }
                    } else {
                        if (isset($_POST['variant_price']) && isset($_POST['variant_special_price']) && isset($_POST['variant_sku']) && isset($_POST['variant_total_stock']) && isset($_POST['variant_stock_status'])) {
                            foreach ($_POST['variant_price'] as $key => $value) {
                                $this->form_validation->set_rules('variant_price[' . $key . ']', 'Price', 'trim|required|numeric|xss_clean|greater_than[0]|greater_than_equal_to[' . $this->input->post('variant_special_price[' . $key . ']') . ']');
                                $this->form_validation->set_rules('variant_special_price[' . $key . ']', 'Special Price', 'trim|numeric|xss_clean|greater_than[0]|less_than_equal_to[' . $this->input->post('variant_price[' . $key . ']') . ']');
                                $this->form_validation->set_rules('variant_sku[' . $key . ']', 'SKU', 'trim|xss_clean');
                                $this->form_validation->set_rules('variant_total_stock[' . $key . ']', 'Total Stock asd', 'trim|required|numeric|xss_clean');
                                $this->form_validation->set_rules('variant_level_stock_status[' . $key . ']', 'Stock Status', 'trim|required|numeric|xss_clean');
                            }
                        } else {
                            $this->form_validation->set_rules('variant_price', 'Price', 'trim|required|numeric|xss_clean|greater_than[0]|greater_than_equal_to[' . $this->input->post('variant_special_price[0]') . ']');
                            $this->form_validation->set_rules('variant_special_price', 'Special Price', 'trim|numeric|xss_clean|greater_than[0]|less_than_equal_to[' . $this->input->post('variant_price') . ']');
                            $this->form_validation->set_rules('variant_sku', 'SKU', 'trim|xss_clean');
                            $this->form_validation->set_rules('variant_total_stock', 'Total Stock asd', 'trim|required|numeric|xss_clean');
                            $this->form_validation->set_rules('variant_level_stock_status', 'Stock Status', 'trim|required|numeric|xss_clean');
                        }
                    }
                } else {
                    if (isset($_POST['variant_price']) && isset($_POST['variant_special_price'])) {
                        foreach ($_POST['variant_price'] as $key => $value) {
                            $this->form_validation->set_rules('variant_price[' . $key . ']', 'Price', 'trim|required|numeric|xss_clean|greater_than[0]|greater_than_equal_to[' . $this->input->post('variant_special_price[' . $key . ']') . ']');
                            $this->form_validation->set_rules('variant_special_price[' . $key . ']', 'Special Price', 'trim|numeric|xss_clean|greater_than[0]|less_than_equal_to[' . $this->input->post('variant_price[' . $key . ']') . ']');
                        }
                    } else {
                        $this->form_validation->set_rules('variant_price', 'Price', 'trim|required|numeric|xss_clean|greater_than[0]|greater_than_equal_to[' . $this->input->post('variant_special_price[0]') . ']');
                        $this->form_validation->set_rules('variant_special_price', 'Special Price', 'trim|numeric|xss_clean|greater_than[0]|less_than_equal_to[' . $this->input->post('variant_price') . ']');
                    }
                }
            }

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                if (!empty($_POST['deliverable_zipcodes'])) {
                    $_POST['zipcodes'] = implode(",", $_POST['deliverable_zipcodes']);
                } else {
                    $_POST['zipcodes'] = NULL;
                }

                if (isset($_POST['deliverable_cities']) && !empty($_POST['deliverable_cities'])) {
                    $_POST['cities'] = implode(",", $_POST['deliverable_cities']);
                } else {
                    $_POST['cities'] = NULL;
                }

                if (isset($_POST['deliverable_type']) && !empty($_POST['deliverable_type']) && $_POST['deliverable_type'] == ALL) {
                    $seller_data = fetch_details('seller_data', ['user_id' => $_POST['seller_id']], 'serviceable_zipcodes');
                    $seller_zipcode = $seller_data[0]['serviceable_zipcodes'];
                    $_POST['zipcodes'] = $seller_zipcode;
                }

                if (isset($_POST['deliverable_city_type']) && !empty($_POST['deliverable_city_type']) && $_POST['deliverable_city_type'] == ALL) {
                    $seller_data = fetch_details('seller_data', ['user_id' => $_POST['seller_id']], 'serviceable_cities');
                    $seller_city = $seller_data[0]['serviceable_cities'];
                    $_POST['cities'] = $seller_city;
                }

                $product_data = [];

                if (isset($_POST['seo_meta_keywords']) && $_POST['seo_meta_keywords'] != '') {
                    $_POST['seo_meta_keywords'] = json_decode($_POST['seo_meta_keywords'], 1);
                    $seo_meta_keywords = array_column($_POST['seo_meta_keywords'], 'value');
                    $_POST['seo_meta_keywords'] = implode(",", $seo_meta_keywords);
                }

                $fields = [
                    'edit_product_id',
                    'category_id',
                    'seller_id',
                    'pro_input_name',
                    'short_description',
                    'tags',
                    'pro_input_tax',
                    'indicator',
                    'brand',
                    'made_in',
                    'total_allowed_quantity',
                    'minimum_order_quantity',
                    'quantity_step_size',
                    'warranty_period',
                    'guarantee_period',
                    'deliverable_type',
                    'hsn_code',
                    'pickup_location',
                    'is_prices_inclusive_tax',
                    'cod_allowed',
                    'is_returnable',
                    'is_cancelable',
                    'cancelable_till',
                    'is_attachment_required',
                    'pro_input_image',
                    'video_type',
                    'video',
                    'pro_input_video',
                    'product_type',
                    'simple_product_stock_status',
                    'variant_stock_level_type',
                    'variant_stock_status',
                    'sku_variant_type',
                    'total_stock_variant_type',
                    'simple_price',
                    'simple_special_price',
                    'weight',
                    'height',
                    'breadth',
                    'length',
                    'product_sku',
                    'product_total_stock',
                    'pro_input_description',
                    'extra_input_description',
                    'attribute_values',
                    'variant_status',
                    'zipcodes',
                    'cities',
                    'download_allowed',
                    'download_link',
                    'download_type',
                    'download_link_type',
                    'pro_input_zip',
                    'deliverable_city_type',
                    'deliverable_zipcodes',
                    'deliverable_cities',
                    'deliverable_type',
                    'low_stock_limit',
                    'seo_page_title',
                    'seo_meta_keywords',
                    'seo_meta_description',
                    'seo_og_image'
                ];

                foreach ($fields as $field) {
                    $product_data[$field] = isset($_POST[$field]) ? $_POST[$field] : '';
                }

                $array_fields = [
                    'attribute_id',
                    'attribute_value_ids',
                    'variations',
                    'edit_variant_id',
                    'variants_ids',
                    'variant_images',
                    'variant_price',
                    'variant_special_price',
                    'variant_sku',
                    'other_images',
                    'variant_total_stock',
                    'variant_level_stock_status',
                    'weight',
                    'height',
                    'breadth',
                    'length'
                ];
                foreach ($array_fields as $array_field) {
                    $product_data[$array_field] = isset($_POST[$array_field]) ? $_POST[$array_field] : [];
                }

                $this->product_model->add_product($product_data);
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $message = (isset($_POST['edit_product_id'])) ? 'Product Updated Successfully' : 'Product Added Successfully';
                $this->response['message'] = $message;
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function get_product_data()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $seller_id = (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) ? $this->input->get('seller_id', true) : NULL;
            $status = (isset($_GET['status']) && $_GET['status'] != "") ? $this->input->get('status', true) : '';
            if (isset($_GET['flag']) && !empty($_GET['flag'])) {
                return $this->product_model->get_product_details($_GET['flag'], $seller_id, $status);
            }
            return $this->product_model->get_product_details(null, $seller_id, $status);
        } else {
            redirect('admin/login', 'refresh');
        }
    }
    public function get_product_faq_data()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $seller_id = (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) ? $this->input->get('seller_id', true) : NULL;
            $status = (isset($_GET['status']) && $_GET['status'] != "") ? $this->input->get('status', true) : '1';
            if (isset($_GET['flag']) && !empty($_GET['flag'])) {
                return $this->product_model->get_product_details($_GET['flag'], $seller_id, $status, 1);
            }
            return $this->product_model->get_product_details(null, $seller_id, $status, 1);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function get_digital_product_data()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $seller_id = (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) ? $this->input->get('seller_id', true) : NULL;
            $status = (isset($_GET['status']) && $_GET['status'] != "") ? $this->input->get('status', true) : NULL;
            return $this->product_model->get_digital_product_details(null, $seller_id, $status);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function get_countries_data()
    {
        $search = $this->input->get('search');
        $response = $this->product_model->get_countries($search);
        echo json_encode($response);
    }

    public function get_brands_data()
    {
        $search = $this->input->get('search');
        $response = $this->product_model->get_brands($search);
        echo json_encode($response);
    }

    public function get_product_data_list()
    {

        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            return $this->product_model->get_product_details('low');
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function get_rating_list()
    {

        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            return $this->rating_model->get_rating();
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function fetch_attributes()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $attributes = $this->db->select('attr_val.id,attr.name as attr_name ,attr_set.name as attr_set_name,attr_val.value')->join('attributes attr', 'attr.id=attr_val.attribute_id')->join('attribute_set attr_set', 'attr_set.id=attr_val.attribute_set_id')->where(' attr.status=1 ')->get('attribute_values attr_val')->result_array();
            $attributes_refind = array();
            for ($i = 0; $i < count($attributes); $i++) {

                if (!array_key_exists($attributes[$i]['attr_set_name'], $attributes_refind)) {
                    $attributes_refind[$attributes[$i]['attr_set_name']] = array();

                    for ($j = 0; $j < count($attributes); $j++) {

                        if ($attributes[$i]['attr_set_name'] == $attributes[$j]['attr_set_name']) {

                            if (!array_key_exists($attributes[$j]['attr_name'], $attributes_refind[$attributes[$i]['attr_set_name']])) {

                                $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']] = array();
                            }
                            $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']][$j]['id'] = $attributes[$j]['id'];

                            $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']][$j]['text'] = $attributes[$j]['value'];

                            $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']] = array_values($attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']]);
                        }
                    }
                }
            }
            print_r(json_encode($attributes_refind));
        } else {
            redirect('admin/login', 'refresh');
        }
    }


    public function view_product()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {
                $this->data['main_page'] = VIEW . 'products';
                $settings = get_settings('system_settings', true);
                $this->data['title'] = 'View Product | ' . $settings['app_name'];
                $this->data['meta_description'] = 'View Product | ' . $settings['app_name'];
                $res = fetch_product($user_id = NULL, ["show_only_active_products" => 0], $this->input->get('edit_id', true));
                $this->data['product_details'] = $res['product'];
                $this->data['product_attributes'] = get_attribute_values_by_pid($_GET['edit_id']);
                $this->data['product_variants'] = get_variants_values_by_pid($_GET['edit_id'], [0, 1, 7]);
                $this->data['product_rating'] = $this->rating_model->fetch_rating((isset($_GET['edit_id'])) ? $_GET['edit_id'] : '', '');
                $this->data['currency'] = $settings['currency'];
                $this->data['category_result'] = fetch_details('categories', ['status' => '1'], 'id,name');
                if (!empty($res['product'])) {
                    $this->load->view('admin/template', $this->data);
                } else {
                    redirect('admin/product', 'refresh');
                }
            } else {
                redirect('admin/product', 'refresh');
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }


    public function delete_rating()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (print_msg(!has_permissions('delete', 'product'), PERMISSION_ERROR_MSG, 'product', false)) {
                return false;
            }

            $this->rating_model->delete_rating($_GET['id']);

            $this->response['error'] = false;
            $this->response['message'] = 'Deleted Succesfully';

            print_r(json_encode($this->response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function change_variant_status($id = '', $status = '', $product_id = '')
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (print_msg(!has_permissions('update', 'product'), PERMISSION_ERROR_MSG, 'product', false)) {
                return false;
            }

            $status = (trim($status) != '' && is_numeric(trim($status))) ? trim($status) : "";
            $id = (!empty(trim($id)) && is_numeric(trim($id))) ? trim($id) : "";

            if (empty($id) || $status == '') {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = "Invalid Status or ID value supplied";

                $this->session->set_flashdata('message', $this->response['message']);
                $this->session->set_flashdata('message_type', 'error');
                if (!empty($product_id)) {
                    $callback_url = base_url("admin/product/view-product?edit_id=$product_id");
                    header("location:$callback_url");
                    return false;
                } else {
                    print_r(json_encode($this->response));
                    return false;
                }
            }
            $all_status = [0, 1, 7];
            if (!in_array($status, $all_status)) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = "Invalid Status value supplied";

                $this->session->set_flashdata('message', $this->response['message']);
                $this->session->set_flashdata('message_type', 'error');
                if (!empty($product_id)) {
                    $callback_url = base_url("admin/product/view-product?edit_id=$product_id");
                    header("location:$callback_url");
                    return false;
                } else {
                    print_r(json_encode($this->response));
                    return false;
                }
            }

            /* change variant status to the new status */
            update_details(['status' => $status], ['id' => $id], 'product_variants');

            $this->response['error'] = false;
            $this->response['message'] = 'Variant status changed successfully';
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();

            $this->session->set_flashdata('message', $this->response['message']);
            $this->session->set_flashdata('message_type', 'success');
            if (!empty($product_id)) {
                $callback_url = base_url("admin/product/view-product?edit_id=$product_id");
                header("location:$callback_url");
                return false;
            } else {
                print_r(json_encode($this->response));
                return false;
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function bulk_upload()
    {

        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'bulk-upload';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Bulk Upload | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Bulk Upload | ' . $settings['app_name'];

            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }
    public function process_bulk_upload()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            $this->form_validation->set_rules('bulk_upload', '', 'xss_clean');
            $this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');

            if (empty($_FILES['upload_file']['name'])) {
                $this->form_validation->set_rules('upload_file', 'File', 'trim|required|xss_clean', array('required' => 'Please choose file'));
            }

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {

                $_POST = $this->input->post(NULL, true);

                $type = $_POST['type']; // Assuming this is related to processing logic

                $allowed_mime_type_arr = array(
                    'text/x-comma-separated-values',
                    'text/comma-separated-values',
                    'application/x-csv',
                    'text/x-csv',
                    'text/csv',
                    'application/csv',
                    'text/plain', // Allowing .txt files
                    'application/json',
                    'text/json'
                );

                $mime = get_mime_by_extension($_FILES['upload_file']['name']);

                if (!in_array($mime, $allowed_mime_type_arr)) {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'Invalid file format!';
                    print_r(json_encode($this->response));
                    return false;
                }

                $file_path = $_FILES['upload_file']['tmp_name'];

                // Check if file is JSON
                $extension = pathinfo($_FILES['upload_file']['name'], PATHINFO_EXTENSION);

                if ($extension == 'json' || $extension == 'txt') {
                    // Read JSON file content
                    $file_content = file_get_contents($file_path);
                    if ($file_content === false) {
                        $this->response['error'] = true;
                        $this->response['message'] = 'Error reading the file!';
                        print_r(json_encode($this->response));
                        return false;
                    }

                    // Decode JSON
                    $json_data = json_decode($file_content, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $this->response['error'] = true;
                        $this->response['message'] = 'Invalid JSON format!';
                        print_r(json_encode($this->response));
                        return false;
                    }
                } else {
                    // Convert CSV to JSON
                    $json_data = csvToJsonProduct($file_path, $type);
                    if (!$json_data) {
                        $this->response['error'] = true;
                        $this->response['message'] = 'Error converting CSV to JSON!';
                        print_r(json_encode($this->response));
                        return false;
                    }
                }

                $allowed_status = array("received", "processed", "shipped");
                $video_types = array("youtube", "vimeo", "self_hosted", "Self Hosted");
                $product_types = array("simple_product", "variable_product", "digital_product");
                $this->response['message'] = '';

                if ($type == 'upload') {
                    $errors = [];
                    $pro_data = [];

                    $required_fields = [
                        'category_id',
                        'type',
                        'name',
                        'short_description',
                        'image',
                        'seller_id',
                        'variants',
                    ];

                    for ($i = 0; $i < count($json_data); $i++) {
                        $row = $json_data[$i];
                        $missing_fields = [];

                        // Check for missing required fields
                        foreach ($required_fields as $field) {
                            if (!isset($row[$field]) || empty($row[$field])) {
                                $missing_fields[] = $field;
                            }
                        }

                        // Check if video_type is valid
                        if (isset($row['video_type']) && !empty($row['video_type']) && !in_array(strtolower($row['video_type']), $video_types)) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid video_type: " . $row['video_type'];
                            continue;
                        }
                        if (isset($row['video_type']) && !empty($row['video_type'])) {
                            if (!isset($row['video']) || empty($row['video'])) {
                                $missing_fields[] = 'video';
                            }
                        }

                        //check for valid seller id
                        if (!is_exist(['user_id' => $row['seller_id']], 'seller_data')) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid seller_id: " . $row['seller_id'];
                            continue;
                        }

                        //check for valid category id
                        if (!is_exist(['id' => $row['category_id']], 'categories')) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid category_id: " . $row['category_id'];
                            continue;
                        }

                        //check for valid tax
                        if (isset($row['tax']) && !empty($row['tax']) && !is_exist(['id' => $row['tax']], 'taxes')) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid tax: " . $row['tax'];
                            continue;
                        }

                        //check for valid product type
                        if (!in_array($row['type'], $product_types)) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid product_type : " . $row['type'] . " it should either be one of the following : variable_product, simple_product or digital_product";
                            continue;
                        }

                        if (isset($row['stock_type']) && !empty($row['stock_type']) && !in_array($row['stock_type'], [0, 1, 2])) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid stock_type : " . $row['stock_type'] . " it should either be one of the following : 0, 1 or 2";
                            continue;
                        }
                        if (isset($row['indicator']) && !empty($row['indicator']) && !in_array(intval($row['indicator']), [0, 1, 2])) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid indicator : " . $row['indicator'] . " it should either be one of the following : 0 or 1 or 2";
                            continue;
                        }
                        if (isset($row['cod_allowed']) && !empty($row['cod_allowed']) && !in_array(intval($row['cod_allowed']), [0, 1])) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid cod_allowed : " . $row['cod_allowed'] . " it should either be one of the following : 0 or 1";
                            continue;
                        }
                        if (isset($row['is_prices_inclusive_tax']) && !empty($row['is_prices_inclusive_tax']) && !in_array(intval($row['is_prices_inclusive_tax']), [0, 1])) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid is_prices_inclusive_tax : " . $row['is_prices_inclusive_tax'] . " it should either be one of the following : 0 or 1";
                            continue;
                        }
                        if (isset($row['is_returnable']) && !empty($row['is_returnable']) && !in_array(intval($row['is_returnable']), [0, 1])) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid is_returnable : " . $row['is_returnable'] . " it should either be one of the following : 0 or 1";
                            continue;
                        }

                        if (isset($row['is_cancelable']) && !empty($row['is_cancelable']) && !in_array(intval($row['is_cancelable']), [0, 1])) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid is_cancelable : " . $row['is_cancelable'] . " it should either be one of the following : 0 or 1";
                            continue;
                        }
                        if (isset($row['availability']) && !empty($row['availability']) && !in_array(intval($row['availability']), [0, 1])) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid availability : " . $row['availability'] . " it should either be one of the following : 0 or 1";
                            continue;
                        }
                        if (isset($row['is_attachment_required']) && !empty($row['is_attachment_required']) && !in_array($row['is_attachment_required'], [0, 1])) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid is_attachment_required : " . $row['is_attachment_required'] . " it should either be one of the following : 0 or 1";
                            continue;
                        }

                        if (isset($row['cancelable_till']) && !empty($row['cancelable_till']) && !in_array($row['cancelable_till'], $allowed_status)) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid cancelable_till : " . $row['cancelable_till'] . " it should either be one of the following : received, processed or shipped";
                            continue;
                        }
                        if (isset($row['minimum_order_quantity']) && !empty($row['minimum_order_quantity']) && $row['minimum_order_quantity'] < 0) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid minimum_order_quantity : " . $row['minimum_order_quantity'] . " it should be greater than 0";
                            continue;
                        }
                        if (isset($row['quantity_step_size']) && !empty($row['quantity_step_size']) && $row['quantity_step_size'] < 0) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid quantity_step_size : " . $row['quantity_step_size'] . " it should be greater than 0";
                            continue;
                        }
                        if (isset($row['total_allowed_quantity']) && !empty($row['total_allowed_quantity']) && $row['total_allowed_quantity'] < 0) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid total_allowed_quantity : " . $row['total_allowed_quantity'] . " it should be greater than 0";
                            continue;
                        }
                        if (isset($row['is_cancelable']) && !empty($row['is_cancelable']) && $row['is_cancelable'] == "1") {
                            if (!isset($row['cancelable_till']) || empty($row['cancelable_till'])) {
                                $missing_fields[] = 'cancelable_till';
                            }
                        } else {
                            $row['cancelable_till'] = '';
                            $row['is_cancelable'] = 0;
                        }

                        if (isset($row['type']) && !empty($row['type']) && $row['type'] == "simple_product") {

                            if (!isset($row['variants'][0]['price']) || empty($row['variants'][0]['price'])) {
                                $missing_fields[] = 'price';
                            }
                            if (!isset($row['variants'][0]['special_price']) || empty($row['variants'][0]['special_price'])) {
                                $missing_fields[] = 'special_price';
                            }

                            if (isset($row['stock_type']) && !empty($row['stock_type']) && $row['stock_type'] == "0") {
                                if (!isset($row['sku']) || empty($row['sku'])) {
                                    $missing_fields[] = 'sku';
                                }
                                if (!isset($row['stock']) || empty($row['stock'])) {
                                    $missing_fields[] = 'stock';
                                }
                                if (!isset($row['availability']) || empty($row['availability'])) {
                                    $missing_fields[] = 'availability';
                                }
                            }
                        } else {
                            for ($k = 0; $k < count($row['variants']); $k++) {


                                if (!isset($row['variants'][$k]['price']) || empty($row['variants'][$k]['price'])) {
                                    $missing_fields[] = 'price';
                                }
                                if (!isset($row['variants'][$k]['special_price']) || empty($row['variants'][$k]['special_price'])) {
                                    $missing_fields[] = 'special_price';
                                }
                                if (!isset($row['variants'][$k]['attribute_value_ids']) || empty($row['variants'][$k]['attribute_value_ids'])) {
                                    $missing_fields[] = 'attribute_value_ids';
                                }
                                if (isset($row['stock_type']) && !empty($row['stock_type']) && $row['stock_type'] == "2") {
                                    if (!isset($row['variants'][$k]['sku']) || empty($row['variants'][$k]['sku'])) {
                                        $missing_fields[] = 'sku';
                                    }
                                    if (!isset($row['variants'][$k]['stock']) || empty($row['variants'][$k]['stock'])) {
                                        $missing_fields[] = 'stock';
                                    }
                                    if (!isset($row['variants'][$k]['availability']) || empty($row['variants'][$k]['availability'])) {
                                        $missing_fields[] = 'availability';
                                    }
                                }
                            }
                        }

                        if (!empty($missing_fields)) {
                            $errors[] = "Record " . ($i + 1) . " is missing the following fields: " . implode(', ', $missing_fields);
                            continue;
                        }
                    }

                    // If there are errors, return them
                    if (!empty($errors)) {
                        $this->response['error'] = true;
                        $this->response['message'] = $errors;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        print_r(json_encode($this->response));
                        exit;
                    }

                    for ($i = 0; $i < count($json_data); $i++) {
                        $pro_data = [];
                        $pro_attr_data = [];
                        $row = $json_data[$i];
                        $slug = create_unique_slug($row['name'], 'products');
                        // Prepare valid data

                        $other_images = explode(',', $row['other_images']);
                        $pro_data = [
                            'name' => $row['name'],
                            'short_description' => $row['short_description'],
                            'slug' => $slug,
                            'type' => $row['type'],
                            'tax' => $row['tax'],
                            'category_id' => $row['category_id'],
                            'seller_id' => $row['seller_id'],
                            'made_in' => $row['made_in'],
                            'brand' => $row['brand'],
                            'indicator' => $row['indicator'],
                            'image' => $row['image'],
                            'total_allowed_quantity' => $row['total_allowed_quantity'],
                            'minimum_order_quantity' => $row['minimum_order_quantity'],
                            'quantity_step_size' => $row['quantity_step_size'],
                            'warranty_period' => $row['warranty_period'],
                            'guarantee_period' => $row['guarantee_period'],
                            'other_images' => isset($row['other_images']) && !empty($row['other_images']) ? json_encode($other_images) : "[]",
                            'video_type' => $row['video_type'],
                            'video' => $row['video'],
                            'tags' => $row['tags'],
                            'status' => 1,
                            'description' => $row['description'],
                            'extra_description' => $row['extra_description'],
                            'deliverable_type' => isset($row['deliverable_type']) && !empty($row['deliverable_type']) ? $row['deliverable_type'] : 0,
                            'deliverable_city_type' => isset($row['deliverable_city_type']) && !empty($row['deliverable_city_type']) ? $row['deliverable_city_type'] : 0,
                            'deliverable_zipcodes' => (isset($row['deliverable_type']) && !empty($row['deliverable_type']) && ($row['deliverable_type'] == 1 || $row['deliverable_type'] == 0)) ? NULL : $row['deliverable_zipcodes'],
                            'deliverable_cities' => (isset($row['deliverable_city_type']) && !empty($row['deliverable_city_type']) && ($row['deliverable_city_type'] == 1 || $row['deliverable_city_type'] == 0)) ? NULL : $row['deliverable_cities'],
                            'pickup_location' => $row['pickup_location'],
                            'low_stock_limit' => $row['low_stock_limit'],
                            'is_attachment_required' => $row['is_attachment_required'],
                            'stock_type' => $row['stock_type'],
                            'is_returnable' => $row['is_returnable'],
                            'is_cancelable' => $row['is_cancelable'],
                            'cancelable_till' => $row['cancelable_till'],
                            'cod_allowed' => isset($row['cod_allowed']) && !empty($row['cod_allowed']) ? $row['cod_allowed'] : 0,
                            'is_prices_inclusive_tax' => isset($row['is_prices_inclusive_tax']) && !empty($row['is_prices_inclusive_tax']) ? $row['is_prices_inclusive_tax'] : 0,
                            'seo_page_title' => $row['seo_page_title'] ?? '',
                            'seo_meta_keywords' => $row['seo_meta_keywords'] ?? '',
                            'seo_meta_description' => $row['seo_meta_description'] ?? '',
                            'seo_og_image' => $row['seo_og_image'] ?? '',
                        ];

                        if ($row['type'] == 'simple_product') {
                            $pro_data += [
                                'sku' => $row['sku'],
                                'stock' => $row['stock'],
                                'availability' => $row['availability'],
                            ];
                        }

                        $this->db->insert('products', $pro_data);
                        $p_id = $this->db->insert_id();

                        $attribute_value_ids = '';
                        for ($k = 0; $k < count($row['variants']); $k++) {
                            $pro_variance_data = [];
                            if (isset($row['variants'][$k]['attribute_value_ids']) && !empty($row['variants'][$k]['attribute_value_ids'])) {
                                $attribute_value_ids .= ',' . $row['variants'][$k]['attribute_value_ids'];
                            }

                            $pro_variance_data = [
                                'product_id' => $p_id,
                                'attribute_value_ids' => $row['variants'][$k]['attribute_value_ids'],
                                'price' => $row['variants'][$k]['price'],
                                'special_price' => (isset($row['variants'][$k]['special_price']) && !empty($row['variants'][$k]['special_price'])) ? $row['variants'][$k]['special_price'] : $row['variants'][$k]['price'],
                                'weight' => (isset($row['variants'][$k]['weight'])) ? floatval($row['variants'][$k]['weight']) : 0,
                                'height' => (isset($row['variants'][$k]['height'])) ? $row['variants'][$k]['height'] : 0,
                                'breadth' => (isset($row['variants'][$k]['breadth'])) ? $row['variants'][$k]['breadth'] : 0,
                                'length' => (isset($row['variants'][$k]['length'])) ? $row['variants'][$k]['length'] : 0,

                            ];

                            if ($row['type'] == 'variable_product') {
                                $pro_variance_data += [
                                    'sku' => $row['variants'][$k]['sku'],
                                    'stock' => $row['variants'][$k]['stock'],
                                    'availability' => (isset($row['variants'][$k]['availability']) && !empty($row['variants'][$k]['availability'])) ? $row['variants'][$k]['availability'] : NULL,
                                    'images' => (isset($row['variants'][$k]['images']) && !empty($row['variants'][$k]['images'])) ? json_encode(explode(',', $row['variants'][$k]['images'])) : "[]",
                                ];
                            }
                            $this->db->insert('product_variants', $pro_variance_data);
                        }
                        if (isset($attribute_value_ids) && !empty($attribute_value_ids)) {
                            $product_attributes = explode(',', trim($attribute_value_ids, ','));
                            $attributes_data = implode(',', array_unique($product_attributes));
                            $pro_attr_data = [
                                'product_id' => $p_id,
                                'attribute_value_ids' => strval($attributes_data),
                            ];

                            $this->db->insert('product_attributes', $pro_attr_data);
                        }
                    }

                    $this->response['error'] = false;
                    $this->response['message'] = 'Products inserted successfully.';
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    print_r(json_encode($this->response));
                } else {
                    $errors = [];
                    $pro_data = [];

                    $required_fields = [
                        'product_id',
                        'category_id',
                        'type',
                        'name',
                        'short_description',
                        'image',
                        'seller_id',
                        'variants',
                    ];

                    for ($i = 0; $i < count($json_data); $i++) {

                        $row = $json_data[$i];
                        $missing_fields = [];

                        // Check for missing required fields
                        foreach ($required_fields as $field) {
                            if (!isset($row[$field]) || empty($row[$field])) {
                                $missing_fields[] = $field;
                            }
                        }

                        // Check if video_type is valid
                        if (isset($row['video_type']) && !empty($row['video_type']) && !in_array(strtolower($row['video_type']), $video_types)) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid video_type: " . $row['video_type'];
                            continue;
                        }
                        if (isset($row['video_type']) && !empty($row['video_type'])) {
                            if (!isset($row['video']) || empty($row['video'])) {
                                $missing_fields[] = 'video';
                            }
                        }

                        //check for valid seller id
                        if (!is_exist(['user_id' => $row['seller_id']], 'seller_data')) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid seller_id: " . $row['seller_id'];
                            continue;
                        }

                        //check for valid category id
                        if (!is_exist(['id' => $row['category_id']], 'categories')) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid category_id: " . $row['category_id'];
                            continue;
                        }

                        //check for valid tax
                        if (isset($row['tax']) && !empty($row['tax']) && !is_exist(['id' => $row['tax']], 'taxes')) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid tax: " . $row['tax'];
                            continue;
                        }

                        //check for valid product type
                        if (!in_array($row['type'], $product_types)) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid product_type : " . $row['type'] . " it should either be one of the following : variable_product, simple_product or digital_product";
                            continue;
                        }

                        if (isset($row['stock_type']) && !empty($row['stock_type']) && !in_array($row['stock_type'], [0, 1, 2])) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid stock_type : " . $row['stock_type'] . " it should either be one of the following : 0, 1 or 2";
                            continue;
                        }
                        if (isset($row['indicator']) && !empty($row['indicator']) && !in_array(intval($row['indicator']), [0, 1, 2])) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid indicator : " . $row['indicator'] . " it should either be one of the following : 0 or 1 or 2";
                            continue;
                        }
                        if (isset($row['cod_allowed']) && !empty($row['cod_allowed']) && !in_array(intval($row['cod_allowed']), [0, 1])) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid cod_allowed : " . $row['cod_allowed'] . " it should either be one of the following : 0 or 1";
                            continue;
                        }
                        if (isset($row['is_prices_inclusive_tax']) && !empty($row['is_prices_inclusive_tax']) && !in_array(intval($row['is_prices_inclusive_tax']), [0, 1])) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid is_prices_inclusive_tax : " . $row['is_prices_inclusive_tax'] . " it should either be one of the following : 0 or 1";
                            continue;
                        }
                        if (isset($row['is_returnable']) && !empty($row['is_returnable']) && !in_array(intval($row['is_returnable']), [0, 1])) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid is_returnable : " . $row['is_returnable'] . " it should either be one of the following : 0 or 1";
                            continue;
                        }

                        if (isset($row['is_cancelable']) && !empty($row['is_cancelable']) && !in_array(intval($row['is_cancelable']), [0, 1])) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid is_cancelable : " . $row['is_cancelable'] . " it should either be one of the following : 0 or 1";
                            continue;
                        }
                        if (isset($row['availability']) && !empty($row['availability']) && !in_array(intval($row['availability']), [0, 1])) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid availability : " . $row['availability'] . " it should either be one of the following : 0 or 1";
                            continue;
                        }
                        if (isset($row['is_attachment_required']) && !empty($row['is_attachment_required']) && !in_array($row['is_attachment_required'], [0, 1])) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid is_attachment_required : " . $row['is_attachment_required'] . " it should either be one of the following : 0 or 1";
                            continue;
                        }

                        if (isset($row['cancelable_till']) && !empty($row['cancelable_till']) && !in_array($row['cancelable_till'], $allowed_status)) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid cancelable_till : " . $row['cancelable_till'] . " it should either be one of the following : received, processed or shipped";
                            continue;
                        }
                        if (isset($row['minimum_order_quantity']) && !empty($row['minimum_order_quantity']) && $row['minimum_order_quantity'] < 0) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid minimum_order_quantity : " . $row['minimum_order_quantity'] . " it should be greater than 0";
                            continue;
                        }
                        if (isset($row['quantity_step_size']) && !empty($row['quantity_step_size']) && $row['quantity_step_size'] < 0) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid quantity_step_size : " . $row['quantity_step_size'] . " it should be greater than 0";
                            continue;
                        }
                        if (isset($row['total_allowed_quantity']) && !empty($row['total_allowed_quantity']) && $row['total_allowed_quantity'] < 0) {
                            $errors[] = "Record " . ($i + 1) . " has an invalid total_allowed_quantity : " . $row['total_allowed_quantity'] . " it should be greater than 0";
                            continue;
                        }
                        if (isset($row['is_cancelable']) && !empty($row['is_cancelable']) && $row['is_cancelable'] == "1") {
                            if (!isset($row['cancelable_till']) || empty($row['cancelable_till'])) {
                                $missing_fields[] = 'cancelable_till';
                            }
                        } else {
                            $row['cancelable_till'] = '';
                            $row['is_cancelable'] = 0;
                        }

                        if (isset($row['type']) && !empty($row['type']) && ($row['type'] == "simple_product" || $row['type'] == "digital_product")) {

                            if (!isset($row['variants'][0]['price']) || empty($row['variants'][0]['price'])) {
                                $missing_fields[] = 'price';
                            }
                            if (!isset($row['variants'][0]['special_price']) || empty($row['variants'][0]['special_price'])) {
                                $missing_fields[] = 'special_price';
                            }

                            if (isset($row['stock_type']) && !empty($row['stock_type']) && $row['stock_type'] == "0") {
                                if (!isset($row['sku']) || empty($row['sku'])) {
                                    $missing_fields[] = 'sku';
                                }
                                if (!isset($row['stock']) || empty($row['stock'])) {
                                    $missing_fields[] = 'stock';
                                }
                                if (!isset($row['availability']) || empty($row['availability'])) {
                                    $missing_fields[] = 'availability';
                                }
                            }
                        } else {
                            for ($k = 0; $k < count($row['variants']); $k++) {

                                if (!isset($row['variants'][$k]['variant_id']) || empty($row['variants'][$k]['variant_id'])) {
                                    $missing_fields[] = 'variant_id';
                                }

                                if (!isset($row['variants'][$k]['price']) || empty($row['variants'][$k]['price'])) {
                                    $missing_fields[] = 'price';
                                }
                                if (!isset($row['variants'][$k]['special_price']) || empty($row['variants'][$k]['special_price'])) {
                                    $missing_fields[] = 'special_price';
                                }
                                if (!isset($row['variants'][$k]['attribute_value_ids']) || empty($row['variants'][$k]['attribute_value_ids'])) {
                                    $missing_fields[] = 'attribute_value_ids';
                                }
                                if (isset($row['stock_type']) && !empty($row['stock_type']) && $row['stock_type'] == "2") {
                                    if (!isset($row['variants'][$k]['sku']) || empty($row['variants'][$k]['sku'])) {
                                        $missing_fields[] = 'sku';
                                    }
                                    if (!isset($row['variants'][$k]['stock']) || empty($row['variants'][$k]['stock'])) {
                                        $missing_fields[] = 'stock';
                                    }
                                    if (!isset($row['variants'][$k]['availability']) || empty($row['variants'][$k]['availability'])) {
                                        $missing_fields[] = 'availability';
                                    }
                                }
                            }
                        }

                        if (!empty($missing_fields)) {
                            $errors[] = "Record " . ($i + 1) . " is missing the following fields: " . implode(', ', $missing_fields);
                            continue;
                        }
                    }

                    // If there are errors, return them
                    if (!empty($errors)) {
                        $this->response['error'] = true;
                        $this->response['message'] = $errors;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        print_r(json_encode($this->response));
                        exit;
                    }

                    for ($i = 0; $i < count($json_data); $i++) {
                        $pro_data = [];
                        $pro_attr_data = [];
                        $row = $json_data[$i];
                        $slug = create_unique_slug($row['name'], 'products');
                        // Prepare valid data

                        $other_images = explode(',', $row['other_images']);
                        $pro_data = [
                            'name' => $row['name'],
                            'short_description' => $row['short_description'],
                            'slug' => $slug,
                            'type' => $row['type'],
                            'tax' => $row['tax'],
                            'category_id' => $row['category_id'],
                            'seller_id' => $row['seller_id'],
                            'made_in' => $row['made_in'],
                            'brand' => $row['brand'],
                            'indicator' => $row['indicator'],
                            'image' => $row['image'],
                            'total_allowed_quantity' => $row['total_allowed_quantity'],
                            'minimum_order_quantity' => $row['minimum_order_quantity'],
                            'quantity_step_size' => $row['quantity_step_size'],
                            'warranty_period' => $row['warranty_period'],
                            'guarantee_period' => $row['guarantee_period'],
                            'other_images' => isset($row['other_images']) && !empty($row['other_images']) ? json_encode($other_images) : "[]",
                            'video_type' => $row['video_type'],
                            'video' => $row['video'],
                            'tags' => $row['tags'],
                            'status' => 1,
                            'description' => $row['description'],
                            'extra_description' => $row['extra_description'],
                            'deliverable_type' => isset($row['deliverable_type']) && !empty($row['deliverable_type']) ? $row['deliverable_type'] : 0,
                            'deliverable_city_type' => isset($row['deliverable_city_type']) && !empty($row['deliverable_city_type']) ? $row['deliverable_city_type'] : 0,
                            'deliverable_zipcodes' => (isset($row['deliverable_type']) && !empty($row['deliverable_type']) && ($row['deliverable_type'] == 1 || $row['deliverable_type'] == 0)) ? NULL : $row['deliverable_zipcodes'],
                            'deliverable_cities' => (isset($row['deliverable_city_type']) && !empty($row['deliverable_city_type']) && ($row['deliverable_city_type'] == 1 || $row['deliverable_city_type'] == 0)) ? NULL : $row['deliverable_cities'],
                            'pickup_location' => $row['pickup_location'],
                            'low_stock_limit' => $row['low_stock_limit'],
                            'is_attachment_required' => $row['is_attachment_required'],
                            'stock_type' => $row['stock_type'],
                            'is_returnable' => $row['is_returnable'],
                            'is_cancelable' => $row['is_cancelable'],
                            'cancelable_till' => $row['cancelable_till'],
                            'cod_allowed' => isset($row['cod_allowed']) && !empty($row['cod_allowed']) ? $row['cod_allowed'] : 0,
                            'is_prices_inclusive_tax' => isset($row['is_prices_inclusive_tax']) && !empty($row['is_prices_inclusive_tax']) ? $row['is_prices_inclusive_tax'] : 0,
                            'seo_page_title' => $row['seo_page_title'] ?? '',
                            'seo_meta_keywords' => $row['seo_meta_keywords'] ?? '',
                            'seo_meta_description' => $row['seo_meta_description'] ?? '',
                            'seo_og_image' => $row['seo_og_image'] ?? '',
                        ];

                        if ($row['type'] == 'simple_product') {
                            $pro_data += [
                                'sku' => $row['sku'],
                                'stock' => $row['stock'],
                                'availability' => $row['availability'],
                            ];
                        }
                        $this->db->where('id', $row['product_id'])->update('products', $pro_data);

                        $attribute_value_ids = '';
                        for ($k = 0; $k < count($row['variants']); $k++) {
                            $pro_variance_data = [];
                            if (isset($row['variants'][$k]['attribute_value_ids']) && !empty($row['variants'][$k]['attribute_value_ids'])) {
                                $attribute_value_ids .= ',' . $row['variants'][$k]['attribute_value_ids'];
                            }

                            $pro_variance_data = [
                                'product_id' => $row['product_id'],
                                'attribute_value_ids' => $row['variants'][$k]['attribute_value_ids'],
                                'price' => $row['variants'][$k]['price'],
                                'special_price' => (isset($row['variants'][$k]['special_price']) && !empty($row['variants'][$k]['special_price'])) ? $row['variants'][$k]['special_price'] : $row['variants'][$k]['price'],
                                'weight' => (isset($row['variants'][$k]['weight'])) ? floatval($row['variants'][$k]['weight']) : 0,
                                'height' => (isset($row['variants'][$k]['height'])) ? $row['variants'][$k]['height'] : 0,
                                'breadth' => (isset($row['variants'][$k]['breadth'])) ? $row['variants'][$k]['breadth'] : 0,
                                'length' => (isset($row['variants'][$k]['length'])) ? $row['variants'][$k]['length'] : 0,
                            ];

                            if ($row['type'] == 'variable_product') {
                                $pro_variance_data += [
                                    'sku' => $row['variants'][$k]['sku'],
                                    'stock' => $row['variants'][$k]['stock'],
                                    'availability' => (isset($row['variants'][$k]['availability']) && !empty($row['variants'][$k]['availability'])) ? $row['variants'][$k]['availability'] : NULL,
                                    'images' => (isset($row['variants'][$k]['images']) && !empty($row['variants'][$k]['images'])) ? json_encode(explode(',', $row['variants'][$k]['images'])) : "[]",
                                ];
                            }

                            $this->db->where('id', $row['variants'][$k]['variant_id'])->update('product_variants', $pro_variance_data);
                        }
                        if (isset($attribute_value_ids) && !empty($attribute_value_ids)) {
                            $product_attributes = explode(',', trim($attribute_value_ids, ','));
                            $attributes_data = implode(',', array_unique($product_attributes));
                            $pro_attr_data = [
                                'product_id' => $row['product_id'],
                                'attribute_value_ids' => strval($attributes_data),
                            ];

                            $this->db->where('product_id', $row['product_id'])->update('product_attributes', $pro_attr_data);
                        }
                    }

                    $this->response['error'] = false;
                    $this->response['message'] = 'All records Updated successfully.';
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    print_r(json_encode($this->response));
                }
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }
    public function get_faqs_list()
    {

        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            return $this->product_model->get_faqs();
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function edit_product_faqs()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->form_validation->set_rules('answer', 'Answer', 'trim|required|xss_clean');
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                $this->product_faqs_model->add_product_faqs($_POST);
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $message = (isset($_POST['edit_product_faq'])) ? 'FAQ Updated Successfully' : 'FAQ Added Successfully';
                $this->response['message'] = $message;
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }
    public function delete_product_faq()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (print_msg(!has_permissions('delete', 'product'), PERMISSION_ERROR_MSG, 'product', false)) {
                return false;
            }

            $this->product_model->delete_faq($_GET['id']);

            $this->response['error'] = false;
            $this->response['message'] = 'Deleted Succesfully';

            print_r(json_encode($this->response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function bulk_download()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (!has_permissions('create', 'product')) {
                print_msg(PERMISSION_ERROR_MSG, 'product');
                return;
            }

            $filename = 'products_' . date('Ymd') . '.csv';

            $productsData = $this->product_model->getProductsAndVariants();

            // Determine max number of variants
            $maxVariants = 0;
            foreach ($productsData as $product) {
                $variantCount = isset($product['variants']) ? count($product['variants']) : 0;
                if ($variantCount > $maxVariants) {
                    $maxVariants = $variantCount;
                }
            }

            // Base headers
            $csvHeaders = [
                'product id',
                'category_id',
                'seller_id',
                'tax',
                'type',
                'stock type',
                'name',
                'short_description',
                'indicator',
                'cod_allowed',
                'minimum order quantity',
                'quantity step size',
                'total allowed quantity',
                'is prices inclusive tax',
                'is returnable',
                'is cancelable',
                'cancelable till',
                'is_attachment_required',
                'image',
                'video_type',
                'video',
                'tags',
                'brand',
                'warranty period',
                'guarantee period',
                'pickup_location',
                'low_stock_limit',
                'made in',
                'sku',
                'stock',
                'availability',
                'description',
                'extra_description',
                'deliverable_type',
                'deliverable_zipcodes',
                'deliverable_city_type',
                'deliverable_cities',
                'seo_page_title',
                'seo_meta_keywords',
                'seo_meta_description',
                'seo_og_image'
            ];

            // Add dynamic variant headers
            for ($i = 0; $i < $maxVariants; $i++) {
                $csvHeaders = array_merge($csvHeaders, [
                    "variant id",
                    "attribute value ids",
                    "price",
                    "special price",
                    "sku",
                    "stock",
                    "availability"
                ]);
            }

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename);
            $output = fopen('php://output', 'w');
            fputcsv($output, $csvHeaders);

            // Write product rows
            foreach ($productsData as $product) {
                $product_name = str_replace(['\'', '"'], ' ', $product['name'] ?? '');
                $product_short_description = str_replace(['\'', '"'], ' ', $product['short_description'] ?? '');
                $product_description = str_replace(['\'', '"'], ' ', $product['description'] ?? '');
                $product_extra_description = str_replace(['\'', '"'], ' ', $product['extra_description'] ?? '');
                $product_tags = str_replace(['\'', '"'], ' ', $product['tags'] ?? '');

                $data = [
                    $product['id'],
                    $product['category_id'],
                    $product['seller_id'],
                    $product['tax'],
                    $product['type'],
                    $product['stock_type'],
                    $product_name,
                    $product_short_description,
                    $product['indicator'],
                    $product['cod_allowed'],
                    $product['minimum_order_quantity'],
                    $product['quantity_step_size'],
                    $product['total_allowed_quantity'],
                    $product['is_prices_inclusive_tax'],
                    $product['is_returnable'],
                    $product['is_cancelable'],
                    $product['cancelable_till'],
                    $product['is_attachment_required'],
                    $product['image'],
                    $product['video_type'],
                    $product['video'],
                    $product_tags,
                    $product['brand'],
                    $product['warranty_period'],
                    $product['guarantee_period'],
                    $product['pickup_location'],
                    $product['low_stock_limit'],
                    $product['made_in'],
                    $product['sku'],
                    $product['stock'],
                    $product['availability'],
                    $product_description,
                    $product_extra_description,
                    $product['deliverable_type'],
                    $product['deliverable_zipcodes'],
                    $product['deliverable_city_type'],
                    $product['deliverable_cities'],
                    $product['seo_page_title'],
                    $product['seo_meta_keywords'],
                    $product['seo_meta_description'],
                    $product['seo_og_image'],
                ];

                // Append all variants data
                if (!empty($product['variants'])) {
                    foreach ($product['variants'] as $variant) {
                        $data = array_merge($data, [
                            $variant['id'],
                            $variant['attribute_value_ids'],
                            $variant['price'],
                            $variant['special_price'],
                            $variant['sku'],
                            $variant['stock'],
                            $variant['availability']
                        ]);
                    }
                }

                // Fill empty variant columns if fewer than max
                $variantColsPerSet = 7;
                $existingVariants = count($product['variants'] ?? []);
                $missing = ($maxVariants - $existingVariants) * $variantColsPerSet;
                if ($missing > 0) {
                    $data = array_merge($data, array_fill(0, $missing, ''));
                }

                fputcsv($output, $data);
            }

            fclose($output);
        }
    }

    public function get_sellers_data()
    {
        $search = $this->input->get('search');
        $response = $this->product_model->get_sellers($search);
        echo json_encode($response);
    }

    public function get_seller_delivrability()
    {

        $this->form_validation->set_data($this->input->get());
        $this->form_validation->set_rules('seller_id', 'Seller ID', 'trim|numeric|required|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {

            $seller_id = $this->input->get('seller_id', true);

            $shipping_settings = get_settings('shipping_method', true);
            $seller_data = fetch_details('seller_data', ['user_id' => $this->input->get('seller_id')], 'deliverable_zipcode_type, deliverable_city_type, serviceable_zipcodes, serviceable_cities');

            if (isset($shipping_settings['pincode_wise_deliverability']) && $shipping_settings['pincode_wise_deliverability'] == 1) {
                if ($seller_data[0]['deliverable_zipcode_type'] == 2) {
                    $serviceable_zipcode = explode(',', $seller_data[0]['serviceable_zipcodes']);
                    $data1 = $this->db->select('id,zipcode')->from('zipcodes')->where_in('id', $serviceable_zipcode)->get()->result_array();
                    print_r($data1);
                }
            } else if (isset($shipping_settings['city_wise_deliverability']) && $shipping_settings['city_wise_deliverability'] == 1) {
            }
            $response['data'] = $this->category_model->get_seller_categories($seller_id);
            echo json_encode($response);
            return;
        }
    }

    public function product_bulk_edit()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = TABLES . 'product-bulk-update';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Product Management | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Product Management |' . $settings['app_name'];
            if (isset($_GET['edit_id'])) {
                $this->data['fetched_data'] = fetch_details('product_faqs', ['id' => $_GET['edit_id']]);
            }
            $this->data['categories'] = $this->category_model->get_categories();
            $this->data['sellers'] = $this->db->select(' u.username as seller_name,u.id as seller_id,sd.category_ids,sd.id as seller_data_id  ')
                ->join('users_groups ug', ' ug.user_id = u.id ')
                ->join('seller_data sd', ' sd.user_id = u.id ')
                ->where(['ug.group_id' => '4'])
                ->where(['sd.status' => 1])
                ->get('users u')->result_array();

            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    // for getting affiliate product data list
    public function get_affiliate_product_data_list()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            $affiliate_categories = fetch_details('categories', ['is_in_affiliate' => 1], 'id');
            $affiliate_categories = array_column($affiliate_categories, 'id');

            return $this->affiliate_model->get_product_details(type: 'digital_product', affiliate_categories: $affiliate_categories);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function edit_product_affiliate_status()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->form_validation->set_rules('answer', 'Answer', 'trim|required|xss_clean');
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                $this->product_faqs_model->add_product_faqs($_POST);
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $message = (isset($_POST['edit_product_faq'])) ? 'FAQ Updated Successfully' : 'FAQ Added Successfully';
                $this->response['message'] = $message;
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function update_affiliate_settings()
    {

        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->form_validation->set_data($this->input->post());
            $this->form_validation->set_rules('product_id', 'Product ID', 'trim|numeric|required|xss_clean');
            $this->form_validation->set_rules('is_in_affiliate', 'Is in Affiliate', 'trim|numeric|required|xss_clean');

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['message'] = strip_tags(validation_errors());
                print_r(json_encode($this->response));
                return;
            }

            $data = [
                'is_in_affiliate' => $this->input->post('is_in_affiliate', true)
            ];

            update_details($data, ['id' => $this->input->post('product_id', true)], 'products');

            $this->response['error'] = false;
            $this->response['message'] = 'Affiliate settings updated successfully';
            $this->response['data'] = [];
            print_r(json_encode($this->response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function bulk_update_affiliate()
    {

        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $product_ids = $this->input->post('product_ids');
            $is_in_affiliate = $this->input->post('is_in_affiliate');

            if (!empty($product_ids)) {
                foreach ($product_ids as $id) {
                    $this->db->where('id', $id)->update('products', ['is_in_affiliate' => $is_in_affiliate]);
                }
                // echo json_encode(['status' => true, 'message' => 'Affiliate status updated successfully.']);
                $this->response['error'] = false;
                $this->response['message'] = 'Affiliate settings updated successfully';
                $this->response['data'] = [];
                print_r(json_encode($this->response));
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'No products selected.';
                $this->response['data'] = [];
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }
}

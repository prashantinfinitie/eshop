<?php
defined('BASEPATH') or exit('No direct script access allowed');

class My_account extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation', 'pagination']);
        $this->load->helper(['url', 'language', 'function_helper']);
        $this->load->model(['chat_model', 'media_model', 'cart_model', 'category_model', 'address_model', 'order_model', 'Transaction_model', 'Promo_code_model', 'Customer_model', 'Area_model']);
        $this->lang->load('auth');
        $this->data['is_logged_in'] = ($this->ion_auth->logged_in()) ? 1 : 0;
        $this->data['user'] = ($this->ion_auth->logged_in()) ? $this->ion_auth->user()->row() : array();
        $this->data['settings'] = get_settings('system_settings', true);
        $this->data['web_settings'] = get_settings('web_settings', true);
        $this->data['auth_settings'] = get_settings('authentication_settings', true);
        $this->data['web_logo'] = get_settings('web_logo');
    }


    public function index()
    {
        if ($this->data['is_logged_in']) {
            $this->data['main_page'] = 'dashboard';
            $this->data['title'] = 'Dashboard | ' . $this->data['web_settings']['site_title'];
            $this->data['keywords'] = 'Dashboard, ' . $this->data['web_settings']['meta_keywords'];
            $this->data['description'] = 'Dashboard | ' . $this->data['web_settings']['meta_description'];
            $this->load->view('front-end/' . THEME . '/template', $this->data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function profile()
    {
        if ($this->ion_auth->logged_in()) {
            $identity_column = $this->config->item('identity', 'ion_auth');
            $this->data['users'] = $this->ion_auth->user()->row();
            $this->data['system_settings'] = $this->data['settings'];
            $this->data['identity_column'] = $identity_column;
            $this->data['main_page'] = 'profile';
            $this->data['title'] = 'Profile | ' . $this->data['web_settings']['site_title'];
            $this->data['keywords'] = $this->data['web_settings']['meta_keywords'];
            $this->data['description'] = $this->data['web_settings']['meta_description'];
            $this->load->view('front-end/' . THEME . '/template', $this->data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function orders()
    {
        if ($this->ion_auth->logged_in()) {
            $this->data['main_page'] = 'orders';
            $this->data['title'] = 'Orders | ' . $this->data['web_settings']['site_title'];
            $this->data['keywords'] = 'Orders, ' . $this->data['web_settings']['meta_keywords'];
            $this->data['description'] = 'Orders | ' . $this->data['web_settings']['meta_description'];
            $total = fetch_orders(false, $this->data['user']->id, false, false, 1, NULL, NULL, NULL, NULL);
            $limit = 10;
            $config['base_url'] = base_url('my-account/orders');
            $config['total_rows'] = $total['total'];
            $config['per_page'] = $limit;
            $config['num_links'] = 2;
            $config['use_page_numbers'] = TRUE;
            $config['reuse_query_string'] = TRUE;
            $config['page_query_string'] = FALSE;
            $config['uri_segment'] = 3;
            $config['attributes'] = array('class' => 'page-link');

            $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
            $config['full_tag_close'] = '</ul>';

            $config['first_tag_open'] = '<li class="page-item">';
            $config['first_link'] = 'First';
            $config['first_tag_close'] = '</li>';

            $config['last_tag_open'] = '<li class="page-item">';
            $config['last_link'] = 'Last';
            $config['last_tag_close'] = '</li>';

            $config['prev_tag_open'] = '<li class="page-item">';
            $config['prev_link'] = '<i class="fa fa-arrow-left"></i>';
            $config['prev_tag_close'] = '</li>';

            $config['next_tag_open'] = '<li class="page-item">';
            $config['next_link'] = '<i class="fa fa-arrow-right"></i>';
            $config['next_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link">';
            $config['cur_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li class="page-item">';
            $config['num_tag_close'] = '</li>';

            $page_no = (empty($this->uri->segment(3))) ? 1 : $this->uri->segment(3);
            if (!is_numeric($page_no)) {
                redirect(base_url('my-account/orders'));
            }
            $offset = ($page_no - 1) * $limit;
            $this->pagination->initialize($config);
            $this->data['links'] =  $this->pagination->create_links();
            $this->data['orders'] = fetch_orders(false, $this->data['user']->id, false, false, $limit, $offset, 'date_added', 'DESC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', true);

            $this->data['payment_methods'] = get_settings('payment_method', true);
            $this->load->view('front-end/' . THEME . '/template', $this->data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function order_details()
    {
        if ($this->ion_auth->logged_in()) {
            $bank_transfer = array();
            $this->data['main_page'] = 'order-details';
            $this->data['title'] = 'Orders | ' . $this->data['web_settings']['site_title'];
            $this->data['keywords'] = 'Orders, ' . $this->data['web_settings']['meta_keywords'];
            $this->data['description'] = 'Orders | ' . $this->data['web_settings']['meta_description'];
            $order_id = $this->uri->segment(3);
            $order = fetch_orders($order_id, $this->data['user']->id, false, false, 1, NULL, NULL, NULL, NULL);
            $return_reasons = fetch_details('return_reasons');

            if (!isset($order['order_data']) || empty($order['order_data'])) {
                redirect(base_url('my-account/orders'));
            }
            $this->data['order'] = $order['order_data'][0];
            $this->data['return_reasons'] = $return_reasons;
            if ($order['order_data'][0]['payment_method'] == "Bank Transfer") {
                $bank_transfer = fetch_details('order_bank_transfer', ['order_id' => $order['order_data'][0]['id']]);
            }
            $this->data['bank_transfer'] = $bank_transfer;
            $this->load->view('front-end/' . THEME . '/template', $this->data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function order_invoice($order_id)
    {
        if ($this->ion_auth->logged_in()) {
            $this->data['main_page'] = VIEW . 'api-order-invoice';
            $settings = $this->data['settings'];
            $this->data['title'] = 'Invoice Management |' . $settings['app_name'];
            $this->data['meta_description'] = 'Invoice Management | ' . $this->data['web_settings']['meta_description'];;
            if (isset($order_id) && !empty($order_id)) {
                $res = $this->order_model->get_order_details(['o.id' => $order_id], true);
                if (!empty($res)) {
                    $items = [];
                    $promo_code = [];
                    if (!empty($res[0]['promo_code'])) {
                        $promo_code = fetch_details('promo_codes', ['promo_code' => trim($res[0]['promo_code'])]);
                    }
                    foreach ($res as $row) {
                        $row = output_escaping($row);
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
                    $this->data['promo_code'] = $promo_code;
                    $this->data['print_btn_enabled'] = true;
                    $this->data['settings'] = $this->data['settings'];
                    $this->load->view('admin/invoice-template', $this->data);
                } else {
                    redirect(base_url(), 'refresh');
                }
            } else {
                redirect(base_url(), 'refresh');
            }
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function update_order_item_status()
    {

        $this->form_validation->set_rules('order_item_id', 'Order item id', 'trim|required|numeric|xss_clean');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|in_list[cancelled,returned]');
        if ($_POST['status'] == 'returned') {
            $this->form_validation->set_rules('return_reason', 'Return Reason', 'trim|required|xss_clean');
            if ($_POST['return_reason'] == 'other') {
                $this->form_validation->set_rules('other_reason', 'Other Reason', 'trim|required|xss_clean');
            }
        }
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {

            if ($_POST['status'] == 'returned') {
                if (!file_exists(FCPATH . RETURN_IMAGES)) {
                    mkdir(FCPATH . RETURN_IMAGES, 0777);
                }
                $temp_array = array();
                $files = $_FILES;
                $images_info_error = "";
                $allowed_media_types = 'jpg|png|jpeg';
                $config = [
                    'upload_path' =>  FCPATH . RETURN_IMAGES,
                    'allowed_types' => $allowed_media_types,
                    'max_size' => 8000,
                ];
                if (!empty($_FILES['return_item_images']['name'][0]) && isset($_FILES['return_item_images']['name'])) {
                    $other_image_cnt = count($_FILES['return_item_images']['name']);
                    $other_img = $this->upload;
                    $other_img->initialize($config);

                    for ($i = 0; $i < $other_image_cnt; $i++) {

                        if (!empty($_FILES['return_item_images']['name'][$i])) {

                            $_FILES['temp_image']['name'] = $files['return_item_images']['name'][$i];
                            $_FILES['temp_image']['type'] = $files['return_item_images']['type'][$i];
                            $_FILES['temp_image']['tmp_name'] = $files['return_item_images']['tmp_name'][$i];
                            $_FILES['temp_image']['error'] = $files['return_item_images']['error'][$i];
                            $_FILES['temp_image']['size'] = $files['return_item_images']['size'][$i];
                            if (!$other_img->do_upload('temp_image')) {
                                $images_info_error = 'return_item_images :' . $images_info_error . ' ' . $other_img->display_errors();
                            } else {
                                $temp_array = $other_img->data();
                                resize_review_images($temp_array, FCPATH . RETURN_IMAGES);
                                $images_new_name_arr[$i] = RETURN_IMAGES . $temp_array['file_name'];
                            }
                        } else {
                            $_FILES['temp_image']['name'] = $files['return_item_images']['name'][$i];
                            $_FILES['temp_image']['type'] = $files['return_item_images']['type'][$i];
                            $_FILES['temp_image']['tmp_name'] = $files['return_item_images']['tmp_name'][$i];
                            $_FILES['temp_image']['error'] = $files['return_item_images']['error'][$i];
                            $_FILES['temp_image']['size'] = $files['return_item_images']['size'][$i];
                            if (!$other_img->do_upload('temp_image')) {
                                $images_info_error = $other_img->display_errors();
                            }
                        }
                    }
                    //Deleting Uploaded attachments if any overall error occured
                    if ($images_info_error != NULL || !$this->form_validation->run()) {
                        if (isset($images_new_name_arr) && !empty($images_new_name_arr || !$this->form_validation->run())) {
                            foreach ($images_new_name_arr as $key => $val) {
                                unlink(FCPATH . RETURN_IMAGES . $images_new_name_arr[$key]);
                            }
                        }
                    }
                }
                if ($images_info_error != NULL) {
                    $this->response['error'] = true;
                    $this->response['message'] =  $images_info_error;
                    print_r(json_encode($this->response));
                    return false;
                }


                $attachments = $images_new_name_arr;
                $return_data = $_POST;
                if ($return_data['return_reason'] == 'other') {
                    $return_data['return_reason'] = $return_data['other_reason'];
                }
                $return_data['return_item_image'] = implode(',', $attachments);
            }

            $this->response = $this->order_model->update_order_item($_POST['order_item_id'], trim($_POST['status']), fromapp: true, return_data: $return_data);
            $order_id = fetch_details('order_items', ['id' => $_POST['order_item_id']], 'id, order_id');
            $order_details = fetch_details('orders', ['id' => $order_id[0]['order_id']], 'id, payment_method');

            if (trim($_POST['status']) != 'returned' && $order_details[0]['payment_method'] != 'COD' && $order_details[0]['payment_method'] != 'cod' && $this->response['error'] == false) {
                process_refund($_POST['order_item_id'], trim($_POST['status']), 'order_items');
            }
            if ($this->response['error'] == false && trim($_POST['status']) == 'cancelled') {
                $data = fetch_details('order_items', ['id' => $_POST['order_item_id']], 'product_variant_id,quantity');
                update_stock($data[0]['product_variant_id'], $data[0]['quantity'], 'plus');
            }
        }
        print_r(json_encode($this->response));
    }

    public function update_order()
    {
        $this->form_validation->set_rules('order_id', 'Order id', 'trim|required|xss_clean');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|in_list[cancelled,returned]');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = validation_errors();
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        } else {
            $res = validate_order_status($_POST['order_id'], $_POST['status'], 'orders', '', true);

            if ($res['error']) {
                $this->response['error'] = (isset($res['return_request_flag'])) ? false : true;
                $this->response['message'] = $res['message'];
                $this->response['data'] = $res['data'];
                print_r(json_encode($this->response));
                return false;
            }
            if ($_POST['status'] == 'returned') {
                $_POST['status'] = 'return_request_pending';
            }
            if ($this->order_model->update_order(['status' => $_POST['status']], ['order_id' => $_POST['order_id']], true)) {
                $this->order_model->update_order(['active_status' => $_POST['status']], ['order_id' => $_POST['order_id']], false, 'order_items');
                if ($this->order_model->update_order(['status' => $_POST['status']], ['order_id' => $_POST['order_id']], true, 'order_items')) {

                    $this->order_model->update_order(['active_status' => $_POST['status']], ['order_id' => $_POST['order_id']], false, 'order_items');

                    $order_details = fetch_details('orders', ['id' => $_POST['order_id']], 'id, payment_method');
                    if (trim($_POST['status'] == 'cancelled') && $order_details[0]['payment_method'] != 'COD' && $order_details[0]['payment_method'] != 'cod') {
                        process_refund($_POST['order_id'], $_POST['status'], 'orders');
                        $data = fetch_details('order_items', ['order_id' => $_POST['order_id']], 'product_variant_id,quantity');
                        $product_variant_ids = [];
                        $qtns = [];
                        foreach ($data as $d) {
                            array_push($product_variant_ids, $d['product_variant_id']);
                            array_push($qtns, $d['quantity']);
                        }

                        update_stock($product_variant_ids, $qtns, 'plus');
                    }
                    $this->response['error'] = false;
                    $this->response['message'] = 'Order Updated Successfully';
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }
            }
        }
    }

    public function notifications()
    {
        if ($this->ion_auth->logged_in()) {
            $this->data['main_page'] = 'notifications';
            $this->data['title'] = 'Notification | ' . $this->data['web_settings']['site_title'];
            $this->data['keywords'] = 'Notification, ' . $this->data['web_settings']['meta_keywords'];
            $this->data['description'] = 'Notification | ' . $this->data['web_settings']['meta_description'];
            $this->load->view('front-end/' . THEME . '/template', $this->data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function manage_address()
    {
        if ($this->ion_auth->logged_in()) {
            $this->data['main_page'] = 'address';
            $this->data['title'] = 'Address | ' . $this->data['web_settings']['site_title'];
            $this->data['keywords'] = 'Address, ' . $this->data['web_settings']['meta_keywords'];
            $this->data['description'] = 'Address | ' . $this->data['web_settings']['meta_description'];
            $this->data['cities'] = get_cities();
            $this->data['areas'] = fetch_details('areas', NULL);
            $this->load->view('front-end/' . THEME . '/template', $this->data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }
    public function get_cities()
    {
        $search = $this->input->get('search');
        $response = $this->Area_model->get_cities_list($search);
        echo json_encode($response);
    }
    public function get_zipcodes_list()
    {
        $search = $this->input->get('search');
        $response = $this->Area_model->get_zipcodes($search);
        echo json_encode($response);
    }

    public function wallet()
    {
        if ($this->ion_auth->logged_in()) {
            $this->data['main_page'] = 'wallet';
            $this->data['title'] = 'Wallet | ' . $this->data['web_settings']['site_title'];
            $this->data['keywords'] = 'Wallet, ' . $this->data['web_settings']['meta_keywords'];
            $this->data['description'] = 'Wallet | ' . $this->data['web_settings']['meta_description'];
            $this->load->view('front-end/' . THEME . '/template', $this->data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function transactions()
    {
        if ($this->ion_auth->logged_in()) {
            $this->data['main_page'] = 'transactions';
            $this->data['title'] = 'Transactions | ' . $this->data['web_settings']['site_title'];
            $this->data['keywords'] = 'Transactions, ' . $this->data['web_settings']['meta_keywords'];
            $this->data['description'] = 'Transactions | ' . $this->data['web_settings']['meta_description'];
            $this->load->view('front-end/' . THEME . '/template', $this->data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function add_address()
    {
        if ($this->ion_auth->logged_in()) {
            $this->form_validation->set_rules('type', 'Type', 'trim|xss_clean');
            $this->form_validation->set_rules('country_code', 'Country Code', 'trim|xss_clean');
            $this->form_validation->set_rules('name', 'Name', 'trim|xss_clean|required');
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim|numeric|xss_clean|required');
            $this->form_validation->set_rules('alternate_mobile', 'Alternative Mobile', 'trim|numeric|xss_clean');
            $this->form_validation->set_rules('address', 'Address', 'trim|xss_clean|required');
            $this->form_validation->set_rules('landmark', 'Landmark', 'trim|xss_clean');
            $this->form_validation->set_rules('city_name', 'City', 'trim|xss_clean');
            $this->form_validation->set_rules('area_name', 'Area', 'trim|xss_clean');
            $this->form_validation->set_rules('area_id', 'Area', 'trim|xss_clean');
            $this->form_validation->set_rules('city_id', 'City', 'trim|xss_clean|required');
            $this->form_validation->set_rules('pincode', 'Pincode', 'trim|numeric|xss_clean');
            $this->form_validation->set_rules('pincode_name', 'Pincode', 'trim|numeric|xss_clean');
            $this->form_validation->set_rules('state', 'State', 'trim|xss_clean|required');
            $this->form_validation->set_rules('country', 'Country', 'trim|xss_clean|required');
            $this->form_validation->set_rules('latitude', 'Latitude', 'trim|xss_clean');
            $this->form_validation->set_rules('longitude', 'Longitude', 'trim|xss_clean');


            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['message'] = validation_errors();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }

            $arr = $this->input->post(null, true);
            $arr['user_id'] = $this->data['user']->id;
            $this->address_model->set_address($arr);
            $res = $this->address_model->get_address($this->data['user']->id, false, true);
            $this->response['error'] = false;
            $this->response['message'] = 'Address Added Successfully';
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['data'] = $res;
            print_r(json_encode($this->response));
            return false;
        } else {
            $this->response['error'] = true;
            $this->response['message'] = 'Unauthorized access is not allowed';
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            print_r(json_encode($this->response));
            return false;
        }
    }

    public function edit_address()
    {
        if ($this->ion_auth->logged_in()) {
            $this->form_validation->set_rules('id', 'Id', 'trim|required|numeric|xss_clean');
            $this->form_validation->set_rules('type', 'Type', 'trim|xss_clean');
            $this->form_validation->set_rules('country_code', 'Country Code', 'trim|xss_clean');
            $this->form_validation->set_rules('name', 'Name', 'required|trim|xss_clean');
            $this->form_validation->set_rules('mobile', 'Mobile', 'required|trim|numeric|xss_clean');
            $this->form_validation->set_rules('alternate_mobile', 'Alternative Mobile', 'trim|numeric|xss_clean');
            $this->form_validation->set_rules('address', 'Address', 'trim|xss_clean');
            $this->form_validation->set_rules('landmark', 'Landmark', 'trim|xss_clean');
            $this->form_validation->set_rules('area_id', 'Area', 'trim|xss_clean');
            $this->form_validation->set_rules('city_id', 'City', 'trim|xss_clean');
            $this->form_validation->set_rules('pincode', 'Pincode', 'trim|numeric|xss_clean');
            $this->form_validation->set_rules('state', 'State', 'required|trim|xss_clean');
            $this->form_validation->set_rules('country', 'Country', 'required|trim|xss_clean');

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['message'] = validation_errors();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
            $this->address_model->set_address($_POST);
            $res = $this->address_model->get_address(null, $_POST['id'], true);
            $this->response['error'] = false;
            $this->response['message'] = 'Address updated Successfully';
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['data'] = $res;
            print_r(json_encode($this->response));
            return false;
        } else {
            $this->response['error'] = true;
            $this->response['message'] = 'Unauthorized access is not allowed';
            print_r(json_encode($this->response));
            return false;
        }
    }

    //delete_address
    public function delete_address()
    {
        if ($this->ion_auth->logged_in()) {
            $this->form_validation->set_rules('id', 'Id', 'trim|required|numeric|xss_clean');
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['message'] = validation_errors();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
            $this->address_model->delete_address($_POST);
            $this->response['error'] = false;
            $this->response['message'] = 'Address Deleted Successfully';
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        } else {
            $this->response['error'] = true;
            $this->response['message'] = 'Unauthorized access is not allowed';
            print_r(json_encode($this->response));
            return false;
        }
    }

    //set default_address
    public function set_default_address()
    {
        if ($this->ion_auth->logged_in()) {
            $this->form_validation->set_rules('id', 'Id', 'trim|required|numeric|xss_clean');
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['message'] = validation_errors();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
            $_POST['is_default'] = true;
            $this->address_model->set_address($_POST);
            $this->response['error'] = false;
            $this->response['message'] = 'Set as default successfully';
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        } else {
            $this->response['error'] = true;
            $this->response['message'] = 'Unauthorized access is not allowed';
            print_r(json_encode($this->response));
            return false;
        }
    }

    //get_address
    public function get_address()
    {
        if ($this->ion_auth->logged_in()) {
            $res = $this->address_model->get_address_for_api($this->data['user']->id);
            $is_default_counter = array_count_values(array_column($res, 'is_default'));


            if (!empty($res)) {
                $this->response['error'] = false;
                $this->response['message'] = 'Address Retrieved Successfully';
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['data'] = $res;
            } else {
                $this->response['error'] = true;
                $this->response['message'] = "No Details Found !";
                $this->response['data'] = array();
            }
            print_r(json_encode($this->response));
        } else {
            $this->response['error'] = true;
            $this->response['message'] = 'Unauthorized access is not allowed';
            print_r(json_encode($this->response));
            return false;
        }
    }
    public function get_promo_codes()
    {
        if ($this->ion_auth->logged_in()) {
            $cart_total_data = get_cart_total($this->data['user']->id);

            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'DESC';
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'id';
            $this->response['error'] = false;
            $this->response['message'] = 'Promocodes retrived Successfully !';

            $result = $this->Promo_code_model->get_promo_codes($limit, $offset, $sort, $order, '');
            $this->response['total'] = $result['total'];
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['offset'] = (isset($offset) && !empty($offset)) ? $offset : '0';
            $this->response['promo_codes'] = $result['data'];
            print_r(json_encode($this->response));
            return;
        } else {
            $this->response['error'] = true;
            $this->response['message'] = 'Unauthorized access is not allowed';
            print_r(json_encode($this->response));
            return false;
        }
    }

    public function get_address_list()
    {
        if ($this->ion_auth->logged_in()) {
            return $this->address_model->get_address_list($this->data['user']->id);
        } else {
            $this->response['error'] = true;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = 'Unauthorized access is not allowed';
            print_r(json_encode($this->response));
            return false;
        }
    }

    public function get_areas()
    {
        if ($this->ion_auth->logged_in()) {
            $this->form_validation->set_rules('city_id', 'City Id', 'trim|required|xss_clean');
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
                return false;
            }

            $city_id = $this->input->post('city_id', true);
            $areas = fetch_details('areas', ['city_id' => $city_id]);
            if (empty($areas)) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = "No Areas found for this City.";
                print_r(json_encode($this->response));
                return false;
            }
            $this->response['error'] = false;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['data'] = $areas;
            print_r(json_encode($this->response));
            return false;
        } else {
            $this->response['error'] = true;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = 'Unauthorized access is not allowed';
            print_r(json_encode($this->response));
            return false;
        }
    }
    public function get_zipcode()
    {
        if ($this->ion_auth->logged_in()) {
            $city_id = $this->input->post('city_id', true);

            //if zipcode table is not sync with area table then gte zipcode list from area table 
            if ($this->db->field_exists('minimum_free_delivery_order_amount', 'zipcodes')) {
                $zipcodes = fetch_details('zipcodes', ['city_id' => $city_id], 'zipcode,id');
            } else {
                $array = $this->db->select('z.zipcode,z.id as id')->join('zipcodes z', 'z.id=a.zipcode_id')->where('city_id', $city_id)->get('areas a')->result_array();
                //remove duplicate value from $array
                $zipcodes = array_map("unserialize", array_unique(array_map("serialize", $array)));
            }

            if (empty($zipcodes)) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = "No Zipcodes found for this area.";
                print_r(json_encode($this->response));
                return false;
            }
            $this->response['error'] = false;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['data'] = $zipcodes;
            print_r(json_encode($this->response));
            return false;
        } else {
            $this->response['error'] = true;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = 'Unauthorized access is not allowed';
            print_r(json_encode($this->response));
            return false;
        }
    }

    public function favorites()
    {
        if ($this->ion_auth->logged_in()) {
            $this->data['main_page'] = 'favorites';
            $this->data['title'] = 'Favorite | ' . $this->data['web_settings']['site_title'];
            $this->data['keywords'] = 'Favorite, ' . $this->data['web_settings']['meta_keywords'];
            $this->data['description'] = 'Favorite | ' . $this->data['web_settings']['meta_description'];
            $this->data['products'] = get_favorites($this->data['user']->id);
            $this->data['settings'] = $this->data['settings'];
            $this->load->view('front-end/' . THEME . '/template', $this->data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function manage_favorites()
    {
        if ($this->ion_auth->logged_in()) {
            $this->form_validation->set_rules('product_id', 'Product Id', 'trim|numeric|required|xss_clean');
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['message'] = validation_errors();
                $this->response['data'] = array();
            } else {
                $data = [
                    'user_id' => $this->data['user']->id,
                    'product_id' => $this->input->post('product_id', true),
                ];
                if (is_exist($data, 'favorites')) {
                    $this->db->delete('favorites', $data);
                    $this->response['error']   = false;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = "Product removed from favorite !";
                    print_r(json_encode($this->response));
                    return false;
                }
                $data = escape_array($data);
                $this->db->insert('favorites', $data);
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'Product Added to favorite';
                print_r(json_encode($this->response));
                return false;
            }
        } else {
            $this->response['error'] = true;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = "Login First to Add Products in Favorite List.";
            print_r(json_encode($this->response));
            return false;
        }
    }

    public function get_transactions()
    {
        if ($this->ion_auth->logged_in()) {
            return $this->Transaction_model->get_transactions_list($this->data['user']->id);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function get_wallet_transactions()
    {
        if ($this->ion_auth->logged_in()) {
            return $this->Transaction_model->get_transactions_list($this->data['user']->id, '2', 'wallet');
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function withdraw_money()
    {
        $this->form_validation->set_rules('user_id', 'User Id', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('payment_address', 'Payment Address', 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|xss_clean|numeric|greater_than[0]');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['message'] = array(
                'user_id' => form_error('user_id'),
                'payment_address' => form_error('payment_address'),
                'amount' => form_error('amount'),
            );;
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        } else {
            $user_id = $this->input->post('user_id', true);
            $payment_address = $this->input->post('payment_address', true);
            $amount = $this->input->post('amount', true);
            $userData = fetch_details('users', ['id' => $_POST['user_id']], 'balance');

            if (!empty($userData)) {

                if ($_POST['amount'] <= $userData[0]['balance']) {

                    $data = [
                        'user_id' => $user_id,
                        'payment_address' => $payment_address,
                        'payment_type' => 'customer',
                        'amount_requested' => $amount,
                    ];


                    if (insert_details($data, 'payment_requests')) {
                        $this->Customer_model->update_balance_customer($amount, $user_id, 'deduct');
                        $userData = fetch_details('users', ['id' => $_POST['user_id']], 'balance');
                        $this->response['error'] = false;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['message'] = 'Withdrawal Request Sent Successfully';
                        $this->response['data'] = $userData[0]['balance'];
                    } else {
                        $this->response['error'] = true;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['message'] = 'Cannot sent Withdrawal Request.Please Try again later.';
                        $this->response['data'] = array();
                    }
                } else {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'You don\'t have enough balance to sent the withdraw request.';
                    $this->response['data'] = array();
                }

                print_r(json_encode($this->response));
            }
        }
    }

    public function get_withdrawal_request()
    {
        if ($this->ion_auth->logged_in()) {
            return $this->Transaction_model->get_withdrawal_transactions_list($this->data['user']->id);
        } else {
            redirect(base_url(), 'refresh');
        }
    }


    // ======================== code for chat ====================================

    public function chat()
    {
        if ($this->ion_auth->logged_in()) {
            $this->data['main_page'] = 'chat';
            $this->data['title'] = 'Chat | ' . $this->data['web_settings']['site_title'];
            $this->data['keywords'] = 'Chat, ' . $this->data['web_settings']['meta_keywords'];
            $this->data['description'] = 'Chat | ' . $this->data['web_settings']['meta_description'];

            $this->data['fcm_server_key'] = get_settings('fcm_server_key');
            $users = $this->chat_model->get_chat_history($_SESSION['user_id'], 10, 0);
            $user = array();
            $i = 0;
            $type = 'person';
            $to_id = $this->session->userdata('user_id');

            foreach ($users as $row) {


                $from_id = $row['id'];

                if (isset($from_id) && !empty($from_id)) {
                    $unread_meg = $this->chat_model->get_unread_msg_count($type, $from_id, $to_id);
                }

                $user[$i] = $row;
                $user[$i]['unread_msg'] = $unread_meg;
                $user[$i]['picture']  = $row['username'];

                $date = strtotime('now');
                if ($to_id == $row['id']) {
                    $user[$i]['is_online'] = 1;
                } else {
                    if ($row['last_online'] > $date) {
                        $user[$i]['is_online'] = 1;
                    } else {
                        $user[$i]['is_online'] = 0;
                    }
                }
                $i++;
            }
            $this->data['supporters'] = $this->chat_model->get_supporters();
            $this->data['users'] = $user;
            $this->load->view('front-end/' . THEME . '/template', $this->data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function make_me_online()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {

            $user_id = $this->session->userdata('user_id');
            $date = strtotime('now');
            $date = $date + 60;
            $data = array(
                'last_online' => $date
            );

            if ($this->chat_model->make_me_online($user_id, $data)) {

                $response['error'] = false;
                $response['message'] = 'Successful';
                echo json_encode($response);
            } else {
                $response['error'] = true;
                $response['message'] = 'Not Successful';
                echo json_encode($response);
            }
        }
    }
    public function get_system_settings()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {
            $this->response['response'] = get_settings('firebase_settings');
            $this->response['vap_id_key'] = get_settings('vap_id_key');
            echo json_encode($this->response);
        }
    }

    public function get_online_members()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {
            $user_id = $this->session->userdata('user_id');

            $date = strtotime('now');
            $date = $date + 15;
            $data = array(
                'last_online' => $date
            );

            $this->chat_model->make_me_online($user_id, $data);

            $users = $this->chat_model->get_chat_history($user_id, 20, 0);

            $user_ids = array_column($users, 'opponent_user_id');

            $members = $this->chat_model->get_members($user_ids);
            $member = array();
            $i = 0;

            $type = 'person';
            $to_id = $this->session->userdata('user_id');

            foreach ($members as $row) {

                $from_id = $row['id'];

                $unread_meg = $this->chat_model->get_unread_msg_count($type, $from_id, $to_id);

                $member[$i] = $row;
                $member[$i]['unread_msg'] = $unread_meg;
                $member[$i]['picture']  = isset($row['image']) ? $row['image'] : '';
                $date = strtotime('now');

                if ($row['last_online'] > $date) {
                    $member[$i]['is_online'] = 1;
                } else {
                    $member[$i]['is_online'] = 0;
                }
                $i++;
            }

            $data1['members'] = $member;

            if (!empty($member)) {
                $response['error'] = false;
                $response['data'] = $data1;
                echo json_encode($response);
            } else {
                $response['error'] = true;
                $response['message'] = 'Not Successful';
                echo json_encode($response);
            }
        }
    }

    public function update_web_fcm()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {
            $fcm = $this->input->get('web_fcm');
            $user_id = $this->session->userdata('user_id');
            if ($this->chat_model->update_web_fcm($user_id, $fcm)) {

                $response['error'] = false;
                $response['message'] = 'Successful';
                echo json_encode($response);
            } else {
                $response['error'] = true;
                $response['message'] = 'Not Successful';
                echo json_encode($response);
            }
        }
    }

    public function send_msg()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {
            $user_id = $this->session->userdata('user_id');



            $data = array(
                'type' => $this->input->post('chat_type'),
                'from_id' => $this->session->userdata('user_id'),
                'to_id' => $this->input->post('opposite_user_id'),
                'message' => $this->input->post('chat-input-textarea')
            );
            $msg_id = $this->chat_model->send_msg($data);

            if (!empty($_FILES['documents']['name'])) {

                $year = date('Y');
                $target_path = FCPATH . CHAT_MEDIA_PATH  . '/';
                $sub_directory = CHAT_MEDIA_PATH  . '/';

                if (!file_exists($target_path)) {
                    mkdir($target_path, 0777, true);
                }

                $temp_array = $media_ids = $other_images_new_name = array();
                $files = $_FILES;
                $other_image_info_error = "";
                $allowed_media_types = implode('|', allowed_media_types());
                $config['upload_path'] = $target_path;
                $config['allowed_types'] = $allowed_media_types;
                $other_image_cnt = count($_FILES['documents']['name']);
                $other_img = $this->upload;
                $other_img->initialize($config);
                for ($i = 0; $i < $other_image_cnt; $i++) {
                    if (!empty($_FILES['documents']['name'][$i])) {

                        $_FILES['temp_image']['name'] = $files['documents']['name'][$i];
                        $_FILES['temp_image']['type'] = $files['documents']['type'][$i];
                        $_FILES['temp_image']['tmp_name'] = $files['documents']['tmp_name'][$i];
                        $_FILES['temp_image']['error'] = $files['documents']['error'][$i];
                        $_FILES['temp_image']['size'] = $files['documents']['size'][$i];
                        if (!$other_img->do_upload('temp_image')) {
                            $other_image_info_error = $other_image_info_error . ' ' . $other_img->display_errors();
                        } else {
                            $temp_array = $other_img->data();
                            $temp_array['sub_directory'] = $sub_directory;
                            $media_ids[] = $media_id = $this->media_model->set_media($temp_array); /* set media in database */
                            if (strtolower($temp_array['image_type']) != 'gif')
                                resize_image($temp_array,  $target_path, $media_id);
                            $other_images_new_name[$i] = $temp_array['file_name'];
                        }
                        $data = array(
                            'original_file_name' => $_FILES['temp_image']['name'],
                            'file_name' => $_FILES['temp_image']['tmp_name'],
                            'file_extension' => $_FILES['temp_image']['type'],
                            'file_size' => $_FILES['temp_image']['size'],
                            'user_id' => $this->session->userdata('user_id'),
                            'message_id' => $msg_id
                        );
                        $file_id = $this->chat_model->add_file($data);
                        $this->chat_model->add_media_ids_to_msg($msg_id, $file_id);
                    } else {

                        $_FILES['temp_image']['name'] = $files['documents']['name'][$i];
                        $_FILES['temp_image']['type'] = $files['documents']['type'][$i];
                        $_FILES['temp_image']['tmp_name'] = $files['documents']['tmp_name'][$i];
                        $_FILES['temp_image']['error'] = $files['documents']['error'][$i];
                        $_FILES['temp_image']['size'] = $files['documents']['size'][$i];
                        if (!$other_img->do_upload('temp_image')) {
                            $other_image_info_error = $other_img->display_errors();
                        }
                        $data = array(
                            'original_file_name' => $_FILES['temp_image']['name'],
                            'file_name' => $_FILES['temp_image']['tmp_name'],
                            'file_extension' => $_FILES['temp_image']['type'],
                            'file_size' => $_FILES['temp_image']['size'],
                            'user_id' => $this->session->userdata('user_id'),
                            'message_id' => $msg_id
                        );
                        $file_id = $this->chat_model->add_file($data);
                        $this->chat_model->add_media_ids_to_msg($msg_id, $file_id);
                    }
                }

                // Deleting Uploaded Images if any overall error occured
                if ($other_image_info_error != NULL) {
                    if (isset($other_images_new_name) && !empty($other_images_new_name)) {
                        foreach ($other_images_new_name as $key => $val) {
                            unlink($target_path . $other_images_new_name[$key]);
                        }
                    }
                }
            }


            $messages = $this->chat_model->get_msg_by_id($msg_id, $this->input->post('opposite_user_id'), $this->session->userdata('user_id'), $this->input->post('chat_type'));
            $message = array();
            $i = 0;
            foreach ($messages as $row) {
                $message[$i] = $row;
                $media_files = $this->chat_model->get_media($row['id']);
                $message[$i]['media_files'] = !empty($media_files) ? $media_files : '';
                $message[$i]['text'] = $row['message'];
                $i++;
            }
            $new_msg = $message;

            if (!empty($msg_id)) {

                $to_id = $this->input->post('opposite_user_id');
                $from_id = $this->session->userdata('user_id');

                // single user msg
                if (($this->input->post('chat_type') == 'person') || ($this->input->post('chat_type') == 'supporter')) {
                    // this is the user who going to recive FCM msg
                    $user = fetch_details('users', ['active' => 1, 'id' => $to_id]);

                    // this is the user who going to send FCM msg 
                    $senders_info = fetch_details('users', ['active' => 1, 'id' => $this->session->userdata('user_id')]);

                    $data = $notification = array();
                    $notification['title'] = $senders_info[0]['username'];

                    $notification['senders_name'] = $senders_info[0]['username'];

                    $notification['type'] = 'message';
                    $notification['message_type'] = 'person';
                    $notification['from_id'] = $from_id;
                    $notification['to_id'] = $to_id;
                    $notification['msg_id'] = $msg_id;
                    $notification['new_msg'] = json_encode($new_msg);
                    $notification['body'] = $this->input->post('chat-input-textarea');
                    $notification['base_url'] = base_url('chat');
                    $data['data']['data'] = $notification;
                    $data['data']['webpush']['fcm_options']['link'] = base_url('chat');
                    $data['to'] = $user[0]['web_fcm'];

                    $ch = curl_init();
                    $fcm_key = get_settings('fcm_server_key');


                    $fcm_key = !empty($fcm_key) ? $fcm_key : '';

                    curl_setopt($ch, CURLOPT_POST, 1);
                    $headers = array();
                    $headers[] = "Authorization: key = " . $fcm_key;
                    $headers[] = "Content-Type: application/json";
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                    $result['error'] = false;
                    $result['response'] = curl_exec($ch);
                    if (curl_errno($ch))
                        echo 'Error:' . curl_error($ch);

                    curl_close($ch);
                }

                $response['error'] = false;
                $response['message'] = 'Successful';
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                $response['msg_id'] = $msg_id;
                $response['new_msg'] = $new_msg;

                echo json_encode($response);
            } else {
                $response['error'] = true;
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                $response['message'] = 'Not Successful';
                echo json_encode($response);
            }
        }
    }

    public function mark_msg_read()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {

            $type = $this->input->post('type');
            $to_id = $this->session->userdata('user_id');
            $from_id = $this->input->post('from_id');
            if ($this->chat_model->mark_msg_read($type, $from_id, $to_id)) {
                $response['error'] = false;
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                $response['message'] = 'Successful';
                echo json_encode($response);
            } else {

                $response['error'] = true;
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                $response['message'] = 'Not Successful';
                echo json_encode($response);
            }
        }
    }


    public function delete_msg()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {
            $from_id = $this->session->userdata('user_id');
            $msg_id = $this->uri->segment(3);

            if (empty($msg_id) || !is_numeric($msg_id) || $msg_id < 1) {
                redirect('chat', 'refresh');
                return false;
                exit(0);
            }

            if ($this->chat_model->delete_msg($from_id, $msg_id)) {
                $response['error'] = false;
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                $response['message'] = 'Successful';
                echo json_encode($response);
            } else {
                $response['error'] = true;
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                $response['message'] = 'Not Successful';
                echo json_encode($response);
            }
        }
    }

    public function load_chat()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {
            $user_id = $this->session->userdata('user_id');

            $type = $this->input->post('type');
            $to_id = $this->session->userdata('user_id');
            $from_id = $this->input->post('from_id');

            $offset = (!empty($_POST['offset'])) ? $this->input->post('offset') : 0;
            $limit = (!empty($_POST['limit'])) ? $this->input->post('limit') : 100;

            $sort = (!empty($_POST['sort'])) ? $this->input->post('sort') : 'id';
            $order = (!empty($_POST['order'])) ? $this->input->post('order') : 'DESC';

            $search = (!empty($_POST['search'])) ? $this->input->post('search') : '';

            $message = array();

            $messages = $this->chat_model->load_chat($from_id, $to_id, $type,  $offset, $limit, $sort, $order, $search);
            if ($messages['total_msg'] == 0) {

                $message['error'] = true;
                $message['error_msg'] = 'No Chat OR Msg Found';
                print_r(json_encode($message));
                return false;
            }

            $i = 0;
            $message['total_msg'] = $messages['total_msg'];
            foreach ($messages['msg'] as $row) {
                $message['msg'][$i] = $row;
                $media_files = $this->chat_model->get_media($row['id']);
                $message['msg'][$i]['media_files'] = !empty($media_files) ? $media_files : '';
                $message['msg'][$i]['text'] = $row['message'];
                if ($row['from_id'] == $to_id) {
                    $message['msg'][$i]['position'] = 'right';
                } else {
                    $message['msg'][$i]['position'] = 'left';
                }
                $i++;
            }
            $message['csrfName'] = $this->security->get_csrf_token_name();
            $message['csrfHash'] = $this->security->get_csrf_hash();
            print_r(json_encode($message));
        }
    }

    public function switch_chat()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {
            $type = $this->input->post('type');
            $id = $this->input->post('from_id');
            $users = $this->chat_model->switch_chat($id, $type);

            $user = array();
            $i = 0;
            foreach ($users as $row) {

                $user[$i] = $row;
                if (($type == 'person') || ($type == 'supporter')) {
                    $user[$i]['picture'] = $row['username'];

                    $date = strtotime('now');

                    if ($row['last_online'] > $date) {
                        $user[$i]['is_online'] = 1;
                    } else {
                        $user[$i]['is_online'] = 0;
                    }
                }

                $i++;
            }
            $user['csrfName'] = $this->security->get_csrf_token_name();
            $user['csrfHash'] = $this->security->get_csrf_hash();

            print_r(json_encode($user));
        }
    }

    public function send_fcm()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        } else {

            $to_id = $this->input->post('receiver_id');
            $from_id = $this->session->userdata('user_id');

            if ($to_id == $from_id) {
                return false;
            }

            $title = $this->input->post('title');
            $type = $this->input->post('type');
            $msg = $this->input->post('msg');
            $user = fetch_details('users', ['active' => 1, 'id' => $to_id]);

            $message_type = !empty($this->input->post('message_type')) ? $this->input->post('message_type') : 'other';

            $data = $notification = array();
            $fcmFields = [];

            $fcmMsg = array(
                'content_available' => true,
                'title' => 'test',
                'body' => $msg,
                'type' => $type,
                "from_id" => $from_id,
                "to_id" => $to_id,
                "chat_type" => "person"
            );

            $fcmFields = array(
                'registration_ids' => [$user[0]['web_fcm']],  // expects an array of ids
                'priority' => 'high',
                'notification' => $fcmMsg,
                'data' => $fcmMsg,
            );

            $headers = array(
                'Authorization: key=' . get_settings('fcm_server_key'),
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
            $result = curl_exec($ch);
            curl_close($ch);
            echo $result;


            print_r(json_encode($fcmFields));
        }
    }

    public function floating_chat_classic()
    {
        if ($this->ion_auth->logged_in()) {
            $this->data['title'] = 'Chat | ' . $this->data['web_settings']['site_title'];
            $this->data['keywords'] = 'Chat, ' . $this->data['web_settings']['meta_keywords'];
            $this->data['description'] = 'Chat | ' . $this->data['web_settings']['meta_description'];

            $this->data['fcm_server_key'] = get_settings('fcm_server_key');

            $user = array();
            $users = $this->chat_model->get_chat_history($_SESSION['user_id'], 10, 0);

            $i = 0;
            $type = 'person';
            $to_id = $this->session->userdata('user_id');

            foreach ($users as $row) {


                $from_id = $row['id'];

                if (isset($from_id) && !empty($from_id)) {
                    $unread_meg = $this->chat_model->get_unread_msg_count($type, $from_id, $to_id);
                }

                $user[$i] = $row;
                $user[$i]['unread_msg'] = $unread_meg;
                $user[$i]['picture']  = $row['username'];

                $date = strtotime('now');
                if ($to_id == $row['id']) {
                    $user[$i]['is_online'] = 1;
                } else {
                    if ($row['last_online'] > $date) {
                        $user[$i]['is_online'] = 1;
                    } else {
                        $user[$i]['is_online'] = 0;
                    }
                }
                $i++;
            }
            $this->data['supporters'] = $this->chat_model->get_supporters();
            $this->data['users'] = $user;
            $this->load->view('front-end/classic/pages/floating_chat', $this->data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }
    public function floating_chat_modern()
    {
        if ($this->ion_auth->logged_in()) {
            $this->data['title'] = 'Chat | ' . $this->data['web_settings']['site_title'];
            $this->data['keywords'] = 'Chat, ' . $this->data['web_settings']['meta_keywords'];
            $this->data['description'] = 'Chat | ' . $this->data['web_settings']['meta_description'];

            $this->data['fcm_server_key'] = get_settings('fcm_server_key');

            $user = array();
            $users = $this->chat_model->get_chat_history($_SESSION['user_id'], 10, 0);
            $i = 0;
            $type = 'person';
            $to_id = $this->session->userdata('user_id');

            foreach ($users as $row) {


                $from_id = $row['id'];

                if (isset($from_id) && !empty($from_id)) {
                    $unread_meg = $this->chat_model->get_unread_msg_count($type, $from_id, $to_id);
                }

                $user[$i] = $row;
                $user[$i]['unread_msg'] = $unread_meg;
                $user[$i]['picture']  = $row['username'];

                $date = strtotime('now');
                if ($to_id == $row['id']) {
                    $user[$i]['is_online'] = 1;
                } else {
                    if ($row['last_online'] > $date) {
                        $user[$i]['is_online'] = 1;
                    } else {
                        $user[$i]['is_online'] = 0;
                    }
                }
                $i++;
            }
            $this->data['supporters'] = $this->chat_model->get_supporters();
            $this->data['users'] = $user;
            $this->load->view('front-end/modern/pages/floating_chat', $this->data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function search_user()
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->join('users_groups', 'users_groups.user_id = users.id', 'left');
        $this->db->where("users_groups.group_id != 2 ");
        $this->db->where("users_groups.group_id != 3 ");
        $this->db->where("users.username like '%" . $_GET['search'] . "%'");

        $fetched_records = $this->db->get();
        $users = $fetched_records->result_array();
        // Initialize Array with fetched data
        $data = array();
        foreach ($users as $user) {
            $data[] = array("id" => $user['id'], "text" => $user['username']);
        }
        echo json_encode($data);
    }

    // =================================== end code for chat ==========================================

    public function tickets()
    {
        if ($this->ion_auth->logged_in()) {
            $limits = ($this->input->get('per-page')) ? $this->input->get('per-page', true) : 4;

            $total_row = fetch_details('tickets', ['user_id' => $_SESSION['user_id']], 'id');

            $total = count($total_row);

            $config['base_url'] = base_url('my_account/tickets');
            $config['total_rows'] = $total;
            $config['per_page'] = $limits;
            $config['use_page_numbers'] = TRUE;
            $config['uri_segment'] = 3;
            $config['num_links'] = 7;
            $config['use_page_numbers'] = TRUE;
            $config['reuse_query_string'] = TRUE;
            $config['page_query_string'] = FALSE;

            $config['attributes'] = array('class' => 'page-link');
            $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
            $config['full_tag_close'] = '</ul>';

            $config['first_tag_open'] = '<li class="page-item">';
            $config['first_link'] = 'First';
            $config['first_tag_close'] = '</li>';

            $config['last_tag_open'] = '<li class="page-item">';
            $config['last_link'] = 'Last';
            $config['last_tag_close'] = '</li>';

            $config['prev_tag_open'] = '<li class="page-item">';
            $config['prev_link'] = '<i class="fa fa-arrow-left"></i>';
            $config['prev_tag_close'] = '</li>';

            $config['next_tag_open'] = '<li class="page-item">';
            $config['next_link'] = '<i class="fa fa-arrow-right"></i>';
            $config['next_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link">';
            $config['cur_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li class="page-item">';
            $config['num_tag_close'] = '</li>';
            $page_no = (empty($this->uri->segment(3))) ? 1 : $this->uri->segment(3);
            if (!is_numeric($page_no)) {
                redirect(base_url('my_account/tickets'));
            }
            $offset = ($page_no - 1) * $limits;
            $this->pagination->initialize($config);
            $this->data['links'] =  $this->pagination->create_links();

            $this->data['main_page'] = 'tickets';
            $this->data['title'] = 'Customer Support | ' . $this->data['web_settings']['site_title'];
            $this->data['keywords'] = 'Customer Support, ' . $this->data['web_settings']['meta_keywords'];
            $this->data['description'] = 'Customer Support | ' . $this->data['web_settings']['meta_description'];
            $this->data['meta_description'] = 'Customer Support | ' . $this->data['web_settings']['site_title'];
            $this->data['ticket_types'] = fetch_details('ticket_types');
            $this->data['tickets'] = fetch_details('tickets', ['user_id' => $_SESSION['user_id']], '*', $limits, $offset, 'DESC');
            $this->load->view('front-end/' . THEME . '/template', $this->data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function refer_and_earn()
    {
        if ($this->ion_auth->logged_in()) {
            $_SESSION['user_id'];
            $data = fetch_details('users', ['id' => $_SESSION['user_id']],  'mobile,password');
            if ($this->ion_auth->logged_in()) {
                if ((!empty($this->data['settings']['is_refer_earn_on']) && ($this->data['settings']['is_refer_earn_on'] == 1 || $this->data['settings']['is_refer_earn_on'] == '1'))) {
                    $this->data['main_page'] = 'refer-and-earn';
                    $this->data['title'] = 'Refer and earn | ' . $this->data['web_settings']['site_title'];
                    $this->data['keywords'] = 'Refer and earn, ' . $this->data['web_settings']['meta_keywords'];
                    $this->data['description'] = 'Refer and earn | ' . $this->data['web_settings']['meta_description'];
                    $this->load->view('front-end/' . THEME . '/template', $this->data);
                } else {
                    redirect(base_url('my_account/profile'), 'refresh');
                }
            }
        }
    }

    public function delete_account()
    {

        if ($this->ion_auth->logged_in()) {
            $user_id = $_SESSION['user_id'];
            $user_group = fetch_details('users_groups', ['user_id' => $user_id], 'group_id');

            if ($user_group[0]['group_id'] == '2') {
                $status = 'awaiting,received,processed,shipped';
                $multiple_status = explode(',', $status);
                $orders = fetch_orders('', $user_id, $multiple_status, false, false, false, false, false, false, false, false, false, false, false, false, false, false, 0);

                foreach ($orders['order_data'] as $order) {
                    if ($this->order_model->update_order(['status' => 'cancelled'], ['id' => $order['id']], true)) {
                        $this->order_model->update_order(['active_status' => 'cancelled'], ['id' => $order['id']], false);
                        if ($this->order_model->update_order(['status' => 'cancelled'], ['order_id' => $order['id']], true, 'order_items')) {
                            $this->order_model->update_order(['active_status' => 'cancelled'], ['order_id' => $order['id']], false, 'order_items');
                            process_refund($order['id'], 'cancelled', 'orders');
                            $data = fetch_details('order_items', ['order_id' => $order['id']], 'product_variant_id,quantity');
                            $product_variant_ids = [];
                            $qtns = [];
                            foreach ($data as $d) {
                                array_push($product_variant_ids, $d['product_variant_id']);
                                array_push($qtns, $d['quantity']);
                            }

                            update_stock($product_variant_ids, $qtns, 'plus');
                        }
                    }
                }
                delete_details(['id' => $user_id], 'users');
                delete_details(['user_id' => $user_id], 'users_groups');
                $response['error'] = false;
                $response['message'] = 'User Deleted Succesfully';
                $this->ion_auth->delete_user($user_id);
                $this->session->sess_destroy();
                redirect(base_url(), 'refresh');
            }
        } else {
            redirect(base_url(), 'refresh');
        }
    }
}

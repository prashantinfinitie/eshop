<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation', 'upload', 'pagination']);
        $this->load->helper(['url', 'language', 'file', 'security']);
        $this->load->model(['product_model', 'category_model', 'affiliate_model']);
    }
    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {
            $this->data['main_page'] = TABLES . 'manage-product';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Product Management | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Product Management |' . $settings['app_name'];


            $this->data['categories'] = $this->category_model->get_categories();

            $search = $this->input->get('search', true);

            $affiliate_categories = fetch_details('categories', ['is_in_affiliate' => 1], 'id');
            $affiliate_categories = array_column($affiliate_categories, 'id');

            $limit = ($this->input->get('per-page')) ? $this->input->get('per-page', true) : 12;

            // Pagination config
            $config = [];
            $config['base_url'] = base_url('affiliate/product'); // Change this as needed
            $config['total_rows'] = $this->affiliate_model->count_affiliate_products_by_categories($affiliate_categories, $search);
            $config['per_page'] = $limit;
            $config['num_links'] = 7;
            $config['use_page_numbers'] = TRUE;
            $config['reuse_query_string'] = TRUE;
            $config['page_query_string'] = TRUE;
            $config['query_string_segment'] = 'page';

            $config['attributes'] = array('class' => 'page-link');
            $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
            $config['full_tag_close'] = '</ul>';
            $config['prev_tag_open'] = '<li class="page-item">';
            $config['prev_link'] = '<i class="fa fa-arrow-left"></i>';
            $config['prev_tag_close'] = '</li>';

            $config['next_tag_open'] = '<li class="page-item">';
            $config['next_link'] = '<i class="fa fa-arrow-right"></i>';
            $config['next_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li class="page-item active disabled"><a class="page-link">';
            $config['cur_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li class="page-item">';
            $config['num_tag_close'] = '</li>';

            $page_no = ($this->input->get('page')) ? $this->input->get('page') : 1;

            if (empty($page_no) || !is_numeric($page_no) || $page_no < 1) {
                redirect(base_url('affiliate/product'));
            }
            $offset = ($page_no - 1) * $limit;

            $this->pagination->initialize($config);
            // Current page offset
            $offset = ($page_no - 1) * $limit;

            // Fetch products
            $products = $this->affiliate_model->get_affiliate_products_by_categories($affiliate_categories, $limit, $offset, $search);


            // echo "<pre>";
            // print_R($products);
            // die;

            $this->data['products'] = $products;
            $this->data['affiliate_categories'] = implode(',', $affiliate_categories);
            $this->data['pagination_links'] = $this->pagination->create_links();

            // If it's an AJAX request, return only product HTML
            if ($this->input->is_ajax_request()) {
                ob_start();

                // Same view, but we only render product section
                $this->load->view('affiliate/template', $this->data);
                $html = ob_get_clean();
                $doc = new DOMDocument();
                libxml_use_internal_errors(true);
                $doc->loadHTML($html);

                $finder = new DomXPath($doc);
                $nodes = $finder->query("//*[@id='product-list']");

                if ($nodes->length > 0) {
                    echo $doc->saveHTML($nodes->item(0));
                }
                return;
            }

            $this->load->view('affiliate/template', $this->data);
        } else {
            redirect('affiliate/login', 'refresh');
        }
    }

    public function manage_promoted_products()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {
            $this->data['main_page'] = TABLES . 'promoted-products';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Promoted Products | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Promoted Products | ' . $settings['app_name'];

            $this->load->view('affiliate/template', $this->data);
        } else {
            redirect('affiliate/login', 'refresh');
        }
    }

    public function get_categories_products($category_id = '')
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {
            $this->data['main_page'] = TABLES . 'manage-product';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Promoted Products | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Promoted Products | ' . $settings['app_name'];


            $affiliate_categories = fetch_details('categories', ['is_in_affiliate' => 1], 'id');
            $affiliate_categories = array_column($affiliate_categories, 'id');

            $limit = ($this->input->get('per-page')) ? $this->input->get('per-page', true) : 12;
            // Pagination config
            $config = [];
            $config['base_url'] = base_url('affiliate/product/get_categories_products/' . $category_id); // Change this as needed
            // $config['total_rows'] = $this->affiliate_model->count_affiliate_products_by_categories($affiliate_categories, $search);
            $config['total_rows'] = $this->affiliate_model->count_affiliate_products_by_categories($category_id);
            $config['per_page'] = $limit;
            $config['num_links'] = 7;
            $config['use_page_numbers'] = TRUE;
            $config['reuse_query_string'] = TRUE;
            $config['page_query_string'] = TRUE;
            $config['query_string_segment'] = 'page';

            $config['attributes'] = array('class' => 'page-link');
            $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
            $config['full_tag_close'] = '</ul>';
            $config['prev_tag_open'] = '<li class="page-item">';
            $config['prev_link'] = '<i class="fa fa-arrow-left"></i>';
            $config['prev_tag_close'] = '</li>';

            $config['next_tag_open'] = '<li class="page-item">';
            $config['next_link'] = '<i class="fa fa-arrow-right"></i>';
            $config['next_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li class="page-item active disabled"><a class="page-link">';
            $config['cur_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li class="page-item">';
            $config['num_tag_close'] = '</li>';

            $page_no = ($this->input->get('page')) ? $this->input->get('page') : 1;

            if (empty($page_no) || !is_numeric($page_no) || $page_no < 1) {
                redirect(base_url('affiliate/product'));
            }
            $offset = ($page_no - 1) * $limit;

            $this->pagination->initialize($config);
            // Current page offset
            $offset = ($page_no - 1) * $limit;


            // Fetch products
            $products = $this->affiliate_model->get_affiliate_products_by_categories($category_id, $limit, $offset);

            // Fetch products
            // $products = $this->affiliate_model->get_affiliate_products_by_categories($category_id);
            $category_data = fetch_details('categories', ['id' => $category_id]);

            $this->data['products'] = $products;
            $this->data['category_data'] = $category_data[0];
            $this->data['pagination_links'] = $this->pagination->create_links();

            $this->load->view('affiliate/template', $this->data);
        } else {
            redirect('affiliate/login', 'refresh');
        }
    }

    // for getting affiliate product data list
    public function get_affiliate_product_data_list()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {
            return $this->affiliate_model->get_product_details(is_in_affiliate: 1);
        } else {
            redirect('affiliate/login', 'refresh');
        }
    }
    // for getting affiliate promoted product data list
    public function get_my_promoted_products_list()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {
            $affiliate_id = $_SESSION['user_id'];
            return $this->affiliate_model->get_promoted_product_list(is_in_affiliate: 1, affiliate_id: $affiliate_id);
        } else {
            redirect('affiliate/login', 'refresh');
        }
    }

    // for generating affiliate token
    // This function generates a secure token for affiliate products
    public function get_or_generate_token()
    {

        // print_R($_POST);
        // die;
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {

            $product_id = $this->input->post('product_id', true);
            $product_name = $this->input->post('product_name', true);
            $user_uuid = $this->input->post('user_id', true);
            $user_id = $_SESSION['user_id'];
            $category_id = $this->input->post('category_id', true);
            $affiliate_commission = $this->input->post('affiliate_commission', true);

            // $affiliate_settings = get_settings('affiliate_settings', true);
            // $expiry_days = isset($affiliate_settings['days_of_token_expire']) ? $affiliate_settings['days_of_token_expire'] : 7;
            // $expiry_days = is_numeric($expiry_days) ? (int)$expiry_days : 7;

            // $expired_at = date('Y-m-d H:i:s', strtotime("+$expiry_days days"));
            $timestamp = time();
            $secret = bin2hex(random_bytes(32)); // Use a secure random key for HMAC

            // Check if valid token already exists
            $this->db->where('affiliate_id', $user_id);
            $this->db->where('product_id', $product_id);
            $existing = $this->db->get('affiliate_tracking')->row();

            if ($existing) {
                // Token already exists
                $this->response['error'] = false;
                $this->response['token'] = $existing->token;
                $this->response['message'] = 'Token Already Exists';
            } else {
                // Generate new token
                $raw_string = $product_id . '|' . $product_name . '|' . $user_uuid . '|' . $timestamp;
                $token = hash_hmac('sha256', $raw_string, $secret);

                if (!empty($token)) {
                    $data = [
                        'product_id'           => $product_id,
                        'affiliate_id'         => $user_id,
                        'token'                => $token,
                        'category_id'          => $category_id,
                        'category_commission'  => $affiliate_commission
                    ];

                    $this->affiliate_model->add_affiliate_tracking($data);

                    $this->response['error'] = false;
                    $this->response['token'] = $token;
                    $this->response['message'] = 'Token Generated Successfully';
                } else {
                    $this->response['error'] = true;
                    $this->response['token'] = null;
                    $this->response['message'] = 'Token Generation Failed';
                }
            }

            echo json_encode($this->response);
        } else {
            redirect('affiliate/login', 'refresh');
        }
    }

    // public function check_token_status()
    // {
    //     if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {

    //         $user_id = $this->input->post('user_id', true);
    //         $product_id = $this->input->post('product_id', true);
    //         $timestamp = date('Y-m-d H:i:s');

    //         // Look for an unexpired token for this user and product
    //         $this->db->where('affiliate_id', $user_id);
    //         $this->db->where('product_id', $product_id);
    //         $this->db->where('expire_time >=', $timestamp);
    //         $query = $this->db->get('affiliate_tracking');

    //         if ($query->num_rows() > 0) {
    //             $existing = $query->row();

    //             $this->response['error'] = false;
    //             $this->response['token'] = $existing->token;
    //             $this->response['message'] = 'Token already exists and is valid.';
    //         } else {
    //             $this->response['error'] = true;
    //             $this->response['message'] = 'No valid token found for this product.';
    //         }

    //         echo json_encode($this->response);
    //     } else {
    //         // Redirect to login if not authenticated
    //         redirect('affiliate/login', 'refresh');
    //     }
    // }
}

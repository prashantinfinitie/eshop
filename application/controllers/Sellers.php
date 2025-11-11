<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sellers extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model(['cart_model', 'category_model', 'rating_model', 'Home_model', 'Seller_model', 'Order_model']);
        $this->load->library(['pagination']);
        $this->data['settings'] = get_settings('system_settings', true);
        $this->data['web_settings'] = get_settings('web_settings', true);
        $this->data['auth_settings'] = get_settings('authentication_settings', true);
        $this->data['web_logo'] = get_settings('web_logo');
        $this->data['is_logged_in'] = ($this->ion_auth->logged_in()) ? 1 : 0;
        $this->data['user'] = ($this->ion_auth->logged_in()) ? $this->ion_auth->user()->row() : array();
        $this->response['csrfName'] = $this->security->get_csrf_token_name();
        $this->response['csrfHash'] = $this->security->get_csrf_hash();
    }

    public function index()
    {
        $this->form_validation->set_data($this->input->get(null, true));
        $this->form_validation->set_rules('per-page', 'Per Page', 'trim|numeric|xss_clean');

        if (!empty($_GET) && !$this->form_validation->run()) {
            redirect(base_url('sellers'));
        }
        $sellers = $this->Seller_model->get_sellers();
        $limit = ($this->input->get('per-page')) ? $this->input->get('per-page', true) : 12;
        $sort_by = ($this->input->get('sort')) ? $this->input->get('sort', true) : '';
        $seller_search = ($this->input->get('seller_search')) ? $this->input->get('seller_search', true) : '';
        if (!empty($category_id)) {
            $category_id = explode('|', $category_id);
        }

        //Seller Sorting
        $sort = $order = '';
        if ($sort_by == "top-rated") {
            $sort = 'rating';
            $order = 'DESC';
        } elseif ($sort_by == "date-desc") {
            $sort = 'u.id';
            $order = 'desc';
        } elseif ($sort_by == "date-asc") {
            $sort = 'u.id';
            $order = 'asc';
        }

        $config['base_url'] = base_url('sellers');
        $config['total_rows'] = $sellers['total'];
        $config['per_page'] = $limit;
        $config['num_links'] = 7;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = FALSE;

        $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination justify-content-center overflow-auto">';
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
        $page_no = (empty($this->uri->segment(2))) ? 1 : $this->uri->segment(2);
        if (!is_numeric($page_no)) {
            redirect(base_url('sellers'));
        }
        $offset = ($page_no - 1) * $limit;
        $this->pagination->initialize($config);
        $this->data['links'] =  $this->pagination->create_links();

        $this->data['main_page'] = 'seller-listing';
        $this->data['title'] = 'Seller Listing | ' . $this->data['web_settings']['site_title'];
        $this->data['keywords'] = 'Seller Listing, ' . $this->data['web_settings']['meta_keywords'];
        $this->data['description'] = 'Seller Listing | ' . $this->data['web_settings']['meta_description'];
        $this->data['seller_search'] = $seller_search;
        $sellers = $this->Seller_model->get_sellers("", $limit, $offset, $sort, $order, $seller_search);
        $this->data['sellers'] = $sellers['data'];
        $this->data['page_main_bread_crumb'] = "Seller Listing";

        $this->load->view('front-end/' . THEME . '/template', $this->data);
    }


    public function seller_details($seller_slug = '')
    {
        $this->form_validation->set_data($this->input->get(null, true));
        $this->form_validation->set_rules('per-page', 'Per Page', 'trim|numeric|xss_clean');

        if (!empty($_GET) && !$this->form_validation->run()) {
            redirect(base_url('sellers'));
        }
        $seller_slug = urldecode($seller_slug);
        $seller_data = fetch_details('seller_data', ['slug' => $seller_slug]);
        $seller_details = fetch_details('users', ['id' => $seller_data[0]['user_id']]);


        $total_ord = 0;
        $sellers = $this->Seller_model->get_sellers();
        $total_orders =  fetch_details('order_items', ['seller_id' => $seller_data[0]['user_id']]);
        foreach ($total_orders as $total) {
            $total_ord += $total['quantity'];
        }

        $theme = fetch_details('themes', ['status' => 1], 'name');

        $limit = ($this->input->get('per-page')) ? $this->input->get('per-page', true) : 12;
        $seller_products_count = fetch_product('', '', '', '', '', '', '', '', true, '', $seller_data[0]['user_id']);

        $config['base_url'] = base_url('sellers/seller_details/' . $seller_slug);
        $config['total_rows'] = $seller_products_count;
        $config['per_page'] = $limit;
        $config['num_links'] = 7;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = FALSE;

        $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination justify-content-center overflow-auto">';
        $config['full_tag_close'] = '</ul>';

        if (isset($theme[0]['name']) && strtolower($theme[0]['name']) == 'modern') {

            $config['prev_tag_open'] = '<li class="page-item">';
            $config['prev_link'] = '<i class="uil uil-arrow-left"></i>';
            $config['prev_tag_close'] = '</li>';

            $config['next_tag_open'] = '<li class="page-item">';
            $config['next_link'] = '<i class="uil uil-arrow-right"></i>';
            $config['next_tag_close'] = '</li>';
        } else {
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
        }

        $config['cur_tag_open'] = '<li class="page-item active disabled"><a class="page-link">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $page_no = (empty($this->uri->segment(4))) ? 1 : $this->uri->segment(4);
        if (!is_numeric($page_no)) {
            redirect(base_url('sellers'));
        }
        $offset = ($page_no - 1) * $limit;
        $this->pagination->initialize($config);
        $this->data['links'] =  $this->pagination->create_links();


        $this->data['main_page'] = 'seller-details';

        $this->data['title'] = 'Seller Details | ' . (isset($seller_data[0]['seo_page_title']) && !empty($seller_data[0]['seo_page_title']) ? $seller_data[0]['seo_page_title'] : $this->data['web_settings']['site_title']);
        $this->data['keywords'] = 'Seller Details | ' . (isset($seller_data[0]['seo_meta_keywords']) && !empty($seller_data[0]['seo_meta_keywords']) ? $seller_data[0]['seo_meta_keywords'] : $this->data['web_settings']['meta_keywords']);
        $this->data['description'] = 'Seller Details | '  . (isset($seller_data[0]['seo_meta_description']) && !empty($seller_data[0]['seo_meta_description']) ? $seller_data[0]['seo_meta_description'] : $this->data['web_settings']['meta_description']);
        $this->data['product_image'] = isset($seller_data['seo_og_image']) && !empty($seller_data['seo_og_image']) ? base_url() . $seller_data['seo_og_image'] : '';


        $this->data['sellers'] = $seller_data;
        $this->data['seller_details'] = $seller_details;
        $seller_products = fetch_product('', '', '', '', $limit, $offset, '', '', '', '', $seller_data[0]['user_id']);
        $this->data['seller_products'] = $seller_products['product'];
        $this->data['seller_products_count'] = $seller_products_count;
        $this->data['total_orders'] = $total_ord;
        $this->data['page_main_bread_crumb'] = "Seller Details";

        // Initialize the product JSON-LD array
        $seller_list = array(
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => array(),
        );

        // Create individual product array
        $merchant = array(
            '@type' => 'Product',
            'name' => $seller_data[0]['store_name'],
            'image' => $seller_data[0]['seller_profile'],
            'url' => base_url('sellers'),
            'description' => output_escaping(
                str_replace(
                    '\r\n',
                    '&#13;&#10;',
                    isset($seller_data[0]['store_description']) && !empty($seller_data[0]['store_description'])
                        ? $seller_data[0]['store_description']
                        : $seller_data[0]['store_name']
                )
            ),
        );

        // Add rating if available
        if (isset($seller_data[0]['no_of_ratings']) && isset($seller_data[0]['rating'])) {
            $merchant['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $seller_data[0]['no_of_ratings'] > 0 ? $seller_data[0]['no_of_ratings'] : 0,
                'reviewCount' => $seller_data[0]['rating'] > 0 ? $seller_data[0]['rating'] : 0
            );
        }

        // Add product to itemListElement array
        $seller_list['itemListElement'][] = $merchant;

        // Convert PHP array to JSON-LD
        $this->data['data_json_ld'] = json_encode($seller_list, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $this->load->view('front-end/' . THEME . '/template', $this->data);
    }
}

<?php

defined('BASEPATH') or exit('No direct script access allowed');


class Api extends CI_Controller
{

    /*
---------------------------------------------------------------------------
Defined Methods:-
---------------------------------------------------------------------------

    1. get_categories    
    2. get_areas_by_city_id
    3. get_cities
    4. get_products
    5. get_settings
    6. get_slider_images
    7. validate_promo_code
        
    8. user-order
        -place_order
        -get_orders
        -update_order_item_status
        -get_invoice_html   (not used)

    9. user-rating
        -set_product_rating
        -delete_product_rating
        -get_product_rating        
        -get_product_review_images        

   10. cart
        -get_user_cart
        -remove_from_cart
        -manage_cart(Add/Update)

   11. user-registration
        -login
        -update_fcm
        -reset_password
        -get_login_identity
        -verify_user
        -register_user
        -update_user
        -delete_user
    
   12. favorites
        -add_to_favorites
        -remove_from_favorites
        -get_favorites

   13. user_addresses
        -add_address
        -update_address
        -delete_address
        -get_address
    
   13. sections
        - 
        -get_notifications


   14. make_payments
        -get_paypal_link
        -paypal_transaction_webview
        -app_payment_status
        -ipn

   15. add_transaction   

   16. get_offer_images

   17. get_faqs

   18. stripe_webhook
   19. transactions
   20. generate_paytm_checksum
   21. generate_paytm_txn_token
   22. validate_paytm_checksum
   23. validate_refer_code
   24. flutterwave_webview
   25. flutterwave_payment_response
   26. delete_order
   27. get_ticket_types
   28. add_ticket
   29. edit_ticket
   30. send_message
   31. get_tickets
   32. get_messages
   33. send_bank_transfer_proof
   34. get_zipcodes
   35. is_product_delivarable
   36. check_cart_products_delivarable
   37. get_sellers
   38. get_promo_codes
   39. add_product_faqs
   40. get_product_faqs
   41. send_withdrawal_request
   42. get_withdrawal_request
   43. rozarpay_create_order
   44. update_order_status
---------------------------------------------------------------------------
---------------------------------------------------------------------------

*/

    private $user_details = [];

    protected $excluded_routes =
    [
        "app/v1/api",
        "app/v1/api/login",
        "app/v1/api/get_categories",
        "app/v1/api/get_zipcode_by_city_id",
        "app/v1/api/get_cities",
        "app/v1/api/get_settings",
        "app/v1/api/get_slider_images",
        "app/v1/api/reset_password",
        "app/v1/api/verify_user",
        "app/v1/api/get_login_identity",
        "app/v1/api/register_user",
        "app/v1/api/get_offer_images",
        "app/v1/api/get_faqs",
        "app/v1/api/validate_refer_code",
        "app/v1/api/get_ticket_types",
        "app/v1/api/get_zipcodes",
        "app/v1/api/is_product_delivarable",
        "app/v1/api/check_shiprocket_serviceability",
        "app/v1/api/flutterwave_webhook",
        "app/v1/api/paystack_webhook",
        "app/v1/api/get_brands_data",
        "app/v1/api/get_flash_sale",
        "app/v1/api/midtrans_payment_process",
        "app/v1/api/midtrans_webhook",
        "app/v1/api/get_popup_offer_images",
        "app/v1/api/sign_up",
        "app/v1/api/get_products",
        "app/v1/api/paypal_transaction_webview",
        "app/v1/api/app_payment_status",
        "app/v1/api/ipn",
        "app/v1/api/stripe_webhook",
        "app/v1/api/flutterwave_payment_response",
        "app/v1/api/verify_otp",
        "app/v1/api/resend_otp",
        "app/v1/api/get_sellers",
        "app/v1/api/get_sections",
        "app/v1/api/get_product_faqs",
        "app/v1/api/get_product_rating",
        "app/v1/api/get_blogs",
        "app/v1/api/send_contact_us_email",
        "app/v1/api/access_token",
    ];


    public function __construct()
    {
        parent::__construct();
        header("Content-Type: application/json");
        header("Expires: 0");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // Allow CORS for all domains
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        $this->load->library(['upload', 'jwt', 'ion_auth', 'form_validation', 'paypal_lib', 'Key', 'instamojo']);
        $this->load->model([
            'category_model',
            'order_model',
            'rating_model',
            'Area_model',
            'cart_model',
            'blog_model',
            'address_model',
            'transaction_model',
            'ticket_model',
            'Order_model',
            'notification_model',
            'faq_model',
            'Seller_model',
            'Promo_code_model',
            'media_model',
            'product_model',
            'Product_faqs_model',
            'Customer_model'
        ]);
        $this->load->helper(['language', 'string']);
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
        $response = $temp = $bulkdata = array();
        $this->identity_column = $this->config->item('identity', 'ion_auth');
        // initialize db tables data
        $this->tables = $this->config->item('tables', 'ion_auth');

        $current_uri = uri_string();
        if (!in_array($current_uri, $this->excluded_routes)) {
            $token = verify_app_request();
            if ($token['error']) {
                header('Content-Type: application/json');
                http_response_code($token['status']);
                print_r(json_encode($token));
                die();
            }
            $this->user_details = $token['data'];
        }
    }
    public function index()
    {
        $this->load->helper('file');
        $this->output->set_content_type(get_mime_by_extension(base_url('api-doc.txt')));
        $this->output->set_output(file_get_contents(base_url('api-doc.txt')));
    }
    public function generate_token()
    {
        $payload = [
            'iat' => time(),
            /* issued at time */
            'iss' => 'eshop',
            'exp' => time() + (60 * 60 * 24 * 365), /* expires after 1 minute */
        ];
        $token = $this->jwt->encode($payload, JWT_SECRET_KEY);
        print_r(json_encode($token));
    }

    public function verify_token()
    {
        try {
            $token = $this->jwt->getBearerToken();
        } catch (Exception $e) {
            $response['error'] = true;
            $response['message'] = $e->getMessage();
            print_r(json_encode($response));
            return false;
        }

        if (!empty($token)) {

            $api_keys = fetch_details('client_api_keys', ['status' => 1]);
            if (empty($api_keys)) {
                $response['error'] = true;
                $response['message'] = 'No Client(s) Data Found !';
                print_r(json_encode($response));
                return false;
            }
            JWT::$leeway = 6000000000;
            $flag = true;
            $error = true;
            $message = '';
            try {
                $payload = $this->jwt->decode($token, new Key(JWT_SECRET_KEY, 'HS256'));
                if (isset($payload->iss) && $payload->iss == 'eshop') {
                    $error = false;
                    $flag = false;
                } else {
                    $error = true;
                    $flag = false;
                    $message = 'Invalid Hash';
                }
            } catch (Exception $e) {
                $message = $e->getMessage();
            }


            if ($flag) {
                $response['error'] = true;
                $response['message'] = $message;
                print_r(json_encode($response));
                return false;
            } else {
                if ($error == true) {
                    $response['error'] = true;
                    $response['message'] = $message;
                    print_r(json_encode($response));
                    return false;
                } else {
                    return true;
                }
            }
        } else {
            $response['error'] = true;
            $response['message'] = "Unauthorized access not allowed";
            print_r(json_encode($response));
            return false;
        }
    }

    // 1.get_categories

    public function get_categories()
    {
        /*
            id:15               // optional
            limit:25            // { default - 25 } optional
            offset:0            // { default - 0 } optional
            sort:               id / name
                                // { default -row_id } optional
            order:DESC/ASC      // { default - ASC } optional
            has_child_or_item:false { default - true}  optional
        */


        $this->form_validation->set_rules('id', 'Category Id', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');
        $this->form_validation->set_rules('slug', 'slug', 'trim|xss_clean');
        $this->form_validation->set_rules('has_child_or_item', 'Child or Item', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        }
        $limit = (isset($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
        $offset = (isset($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
        $sort = (isset($_POST['sort(array)']) && !empty(trim($_POST['sort']))) ? $this->input->post('sort', true) : 'row_order';
        $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $this->input->post('order', true) : 'ASC';
        $slug = (isset($_POST['slug']) && !empty(trim($_POST['slug']))) ? $this->input->post('slug', true) : '';
        $has_child_or_item = (isset($_POST['has_child_or_item']) && !empty(trim($_POST['has_child_or_item']))) ? $this->input->post('has_child_or_item', true) : 'true';

        $this->response['message'] = "Category(s) retrieved successfully!";
        $id = (!empty($_POST['id']) && isset($_POST['id'])) ? $_POST['id'] : '';
        $cat_res = $this->category_model->get_categories($id, $limit, $offset, $sort, $order, strval(trim($has_child_or_item)), $slug);

        $popular_categories = $this->category_model->get_categories(NULL, "", "", 'clicks', 'DESC', 'false', "", "", "");

        $this->response['error'] = (empty($cat_res)) ? true : false;
        $this->response['total'] = !empty($cat_res) ? $cat_res[0]['total'] : 0;
        $this->response['message'] = (empty($cat_res)) ? 'Category does not exist' : 'Category retrieved successfully';
        $this->response['data'] = $cat_res;
        $this->response['popular_categories'] = $popular_categories;

        print_r(json_encode($this->response));
    }

    //2. get_areas_by_city_id
    //change API name get_areas_by_city_id to get_zipcode_by_city_id
    public function get_zipcode_by_city_id()
    {
        /*  id:'57' 
                limit:25            // { default - 25 } optional
                offset:0            // { default - 0 } optional
                sort:               // { a.name / a.id } optional
                order:DESC/ASC      // { default - ASC } optional
                search:value        // {optional} 
            */


        $this->form_validation->set_rules('id', 'City Id', 'trim|required|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('search', 'Search keyword', 'trim|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $limit = (isset($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
            $offset = (isset($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $this->input->post('sort', true) : 'z.id';
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $this->input->post('order', true) : 'ASC';
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $id = $this->input->post('id', true);

            $result = $this->Area_model->get_area_by_city($id, $sort, $order, $search, $limit, $offset);
            print_r(json_encode($result));
        }
    }

    //3.get_cities
    public function get_cities()
    {
        /*
           sort:               // { c.name / c.id } optional
           order:DESC/ASC      // { default - ASC } optional
           search:value        // {optional} 
       */
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');


        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $this->input->post('sort', true) : 'c.name';
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $this->input->post('order', true) : 'ASC';
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $id = $this->input->post('id', true);
            $result = $this->Area_model->get_cities($sort, $order, $search, $limit, $offset);
            print_r(json_encode($result));
        }
    }

    /* 4.get_products

        id:101              // optional
        category_id:29      // optional
        user_id:15          // optional
        search:keyword      // optional
        tags:multiword tag1, tag2, another tag      // optional
        attribute_value_ids : 34,23,12 // { Use only for filteration } optional
        limit:25            // { default - 25 } optional
        offset:0            // { default - 0 } optional
        sort:p.id / p.date_added / pv.price
                            // { default - p.id } optional
        order:DESC/ASC      // { default - DESC } optional
        is_similar_products:1 // { default - 0 } optional
        top_rated_product: 1 // { default - 0 } optional
        similar_product_slug:product-slug  //  optional
        category_slug:category-slug  //  optional
        discount: 5             // optional
        min_price:10000          // optional
        max_price:50000          // optional
        seller_id:1255           //{optional}
        zipcode:1           //{optional}
        product_ids: 19,20             // optional
        product_variant_ids: 44,45,40             // optional
        is_detailed_data: 1 //optionaal(if details required)

    */

    public function get_products()
    {

        $this->form_validation->set_rules('id', 'Product ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('product_ids', 'Product IDs', 'trim|xss_clean');
        $this->form_validation->set_rules('product_variant_ids', 'Product variant IDs', 'trim|xss_clean');
        $this->form_validation->set_rules('search', 'Search', 'trim|xss_clean');
        $this->form_validation->set_rules('category_id', 'Category id', 'trim|xss_clean');
        $this->form_validation->set_rules('attribute_value_ids', 'Attr Ids', 'trim|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean|alpha');
        $this->form_validation->set_rules('is_similar_products', 'Similar Products', 'trim|xss_clean|numeric');
        $this->form_validation->set_rules('top_rated_product', ' Top Rated Product ', 'trim|xss_clean|numeric');
        $this->form_validation->set_rules('min_price', ' Min Price ', 'trim|xss_clean|numeric|less_than_equal_to[' . $this->input->post('max_price') . ']');
        $this->form_validation->set_rules('max_price', ' Max Price ', 'trim|xss_clean|numeric|greater_than_equal_to[' . $this->input->post('min_price') . ']');
        $this->form_validation->set_rules('discount', ' Discount ', 'trim|xss_clean|numeric');
        $this->form_validation->set_rules('zipcode', ' zipcode ', 'trim|xss_clean');
        $this->form_validation->set_rules('seller_slug', ' Seller Slug ', 'xss_clean');
        $tags = array();
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {

            $limit = (isset($_POST['limit'])) ? $this->input->post('limit', true) : 25;
            $offset = (isset($_POST['offset'])) ? $this->input->post('offset', true) : 0;
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'ASC';
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'p.row_order';
            // if ($sort == 'pv.special_price') {
            //     $sort = "special_price";
            // }
            $is_detailed_data = (isset($_POST['is_detailed_data']) && $_POST['is_detailed_data'] == 1) ? $_POST['is_detailed_data'] : 0;

            if (isset($_POST['seller_slug']) && !empty($_POST['seller_slug'])) {
                $seller = fetch_details("seller_data", ['slug' => $_POST['seller_slug']], "user_id,store_name");
                $seller_id = (!empty($seller) && isset($seller[0]['user_id'])) ? $seller[0]['user_id'] : "";
            } else {
                $seller_id = (isset($_POST['seller_id']) && !empty(trim($_POST['seller_id']))) ? $this->input->post('seller_id', true) : NULL;
            }
            $filters['search'] = (isset($_POST['search']) && !empty($_POST['search'])) ? $_POST['search'] : '';
            $filters['tags'] = (isset($_POST['tags'])) ? $_POST['tags'] : "";
            $filters['attribute_value_ids'] = (isset($_POST['attribute_value_ids'])) ? $_POST['attribute_value_ids'] : null;
            $filters['is_similar_products'] = (isset($_POST['is_similar_products'])) ? $_POST['is_similar_products'] : null;
            $filters['discount'] = (isset($_POST['discount'])) ? $_POST['discount'] : 0;
            $filters['product_type'] = (isset($_POST['top_rated_product']) && $_POST['top_rated_product'] == 1) ? 'top_rated_product_including_all_products' : null;
            $filters['min_price'] = (isset($_POST['min_price']) && !empty($_POST['min_price'])) ? $this->input->post("min_price", true) : 0;
            $filters['max_price'] = (isset($_POST['max_price']) && !empty($_POST['max_price'])) ? $this->input->post("max_price", true) : 0;
            $filters['brand'] = (isset($_POST['brand']) && !empty($_POST['brand'])) ? $this->input->post("brand", true) : '';
            $filters['slug'] = (isset($_POST['slug']) && !empty($_POST['slug'])) ? $_POST['slug'] : '';
            $filters['similar_product_slug'] = (isset($_POST['similar_product_slug']) && !empty($_POST['similar_product_slug'])) ? $_POST['similar_product_slug'] : '';
            $filters['category_slug'] = (isset($_POST['category_slug']) && !empty($_POST['category_slug'])) ? $_POST['category_slug'] : '';
            $filters['brand_slug'] = (isset($_POST['brand_slug']) && !empty($_POST['brand_slug'])) ? $this->input->post("brand_slug", true) : '';
            $filters['flag'] = (isset($_POST['flag']) && !empty($_POST['flag'])) ? $_POST['flag'] : '';
            $filters['status'] = (isset($_POST['status']) && !empty($_POST['status'])) ? $_POST['status'] : '1';
            $zipcode = (isset($_POST['zipcode']) && !empty($_POST['zipcode'])) ? $this->input->post("zipcode", true) : 0;

            if (isset($_POST['zipcode']) && !empty($_POST['zipcode'])) {
                $zipcode = $this->input->post('zipcode', true);
                $is_pincode = is_exist(['zipcode' => $zipcode], 'zipcodes');
                if ($is_pincode) {
                    $zipcode_id = fetch_details('zipcodes', ['zipcode' => $zipcode], 'id');
                    $zipcode = $zipcode_id[0]['id'];
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Products Not Found !';
                    echo json_encode($this->response);
                    return false;
                }
            }
            $category_id = (isset($_POST['category_id'])) ? $_POST['category_id'] : null;
            $product_id = (isset($_POST['id'])) ? $_POST['id'] : null;
            $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : null;
            $product_ids = (isset($_POST['product_ids'])) ? $_POST['product_ids'] : null;
            $product_variant_ids = (isset($_POST['product_variant_ids']) && !empty($_POST['product_variant_ids'])) ? $this->input->post("product_variant_ids", true) : null;
            if ($product_ids != null) {
                $product_id = explode(",", $product_ids);
            }
            if ($category_id != null) {
                $category_id = explode(",", $category_id);
            }
            if (!empty($filters['brand'])) {
                $filters['brand'] = explode(",", $filters['brand']);
            }
            if ($product_variant_ids != null) {
                $filters['product_variant_ids'] = explode(",", $product_variant_ids);
            }


            $products = fetch_product($user_id, (isset($filters)) ? $filters : null, $product_id, $category_id, $limit, $offset, $sort, $order, null, $zipcode, $seller_id, $is_detailed_data);

            $products_total = fetch_product($user_id, (isset($filters)) ? $filters : null, $product_id, $category_id, '', '', $sort, $order, true, $zipcode, $seller_id, 0);

            foreach ($products['product'] as $key => $product) {
                foreach ($product['variants'] as $index => $product_variant) {
                    $variant_id = $product_variant['id'];
                    $res = get_statistics($variant_id);
                    $product_variant['statistics'] = $res;
                    $products['product'][$key]['variants'][$index] = $product_variant;
                }
            }
            $tags_data = fetch_tags($category_id);
            $allTags = array();
            foreach ($tags_data as $tag_data) {
                foreach ($tag_data as $tag) {
                    $allTags[] = trim($tag);
                }
            }
            // Remove empty values (null, empty strings, etc.)
            $allTags = array_filter($allTags);

            // Reindex the array to reset numeric keys if needed
            $allTags = array_values($allTags);

            $data = suggestionKeyword($_POST['search']);
            // Extract 'suggestion_keyword' values
            $suggestion_words = array_column($data, 'suggestion_keyword');

            if (!empty($products['product'])) {
                $this->response['error'] = false;
                $this->response['message'] = "Products retrieved successfully !";
                $this->response['min_price'] = (isset($products['min_price']) && !empty($products['min_price'])) ? strval($products['min_price']) : 0;
                $this->response['max_price'] = (isset($products['max_price']) && !empty($products['max_price'])) ? strval($products['max_price']) : 0;
                $this->response['search'] = (isset($_POST['search']) && !empty($_POST['search'])) ? $_POST['search'] : '';
                $this->response['filters'] = (isset($products['filters']) && !empty($products['filters'])) ? $products['filters'] : [];
                $this->response['tags'] = (!empty($allTags)) ? $allTags : [];
                $this->response['total'] = (isset($products_total)) ? strval($products_total) : '';
                $this->response['offset'] = (isset($_POST['offset']) && !empty($_POST['offset'])) ? $_POST['offset'] : '0';
                $this->response['suggestion_keywords'] = $suggestion_words;
                $this->response['data'] = $products['product'];
            } else {
                $this->response['error'] = true;
                $this->response['message'] = "Products Not Found !";
                $this->response['data'] = array();
            }
        }
        print_r(json_encode($this->response));
    }


    //5.get_settings
    public function get_settings()
    {
        /*
            type : payment_method // { default : all  } optional            
            user_id:  15 { optional }
            limit:25            // { default - 25 } optional
            offset:0            // { default - 0 } optional
        */

        $type = (isset($_POST['type']) && $_POST['type'] == 'payment_method') ? 'payment_method' : 'all';
        $this->form_validation->set_rules('type', 'Setting Type', 'trim|xss_clean');

        if (isset($_SERVER['HTTP_AUTHORIZATION']) || isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) || isset($_SERVER['Authorization'])) {
            $token = verify_app_request();

            $this->user_details = $token['data'];
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $cart_total_response = get_cart_total($user_id, false, 0);
            $cod_allowed = isset($cart_total_response[0]['is_cod_allowed']) ? $cart_total_response[0]['is_cod_allowed'] : 1;
            $settings_res['is_cod_allowed'] = $cod_allowed;
        } else {
            $settings_res['is_cod_allowed'] = 1;
        }
        if (!$this->form_validation->run()) {

            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        } else {
            $tags = array();

            $general_settings = array();
            if ($type == 'all' || $type == 'payment_method') {
                $limit = (isset($_POST['limit'])) ? $this->input->post('limit', true) : 30;
                $offset = (isset($_POST['offset'])) ? $this->input->post('offset', true) : 0;
                $filter = array('tags' => "a");
                $products = fetch_product(null, $filter, null, null, $limit, $offset, 'p.id', 'DESC', null);
                for ($i = 0; $i < count($products); $i++) {
                    if (!empty($products['product'][$i]['tags'])) {
                        $tags = array_merge($tags, $products['product'][$i]['tags']);
                    }
                }
                $settings = [
                    'logo' => 0,
                    'privacy_policy' => 0,
                    'terms_conditions' => 0,
                    'fcm_server_key' => 0,
                    'contact_us' => 0,
                    'payment_method' => 1,
                    'about_us' => 0,
                    'currency' => 0,
                    'time_slot_config' => 1,
                    'user_data' => 0,
                    'system_settings' => 1,
                    'web_settings' => 1,
                    'affiliate_settings' => 1,
                    'shipping_policy' => 0,
                    'affiliate_privacy_policy' => 0,
                    'affiliate_terms_conditions' => 0,
                    'return_policy' => 0,
                    'shipping_method' => 1,
                    'vap_id_Key' => 0,
                    'authentication_settings' => 1,
                    'send_notification_settings' => 1,
                    'sms_gateway_settings' => 1,
                ];
                if ($type == 'payment_method') {
                    $settings_res['payment_method'] = get_settings($type, $settings[$_POST['type']]);
                    unset($settings_res['payment_method']['phonepe_salt_key']);
                    unset($settings_res['payment_method']['phonepe_salt_index']);

                    $settings_res['payment_method']['app_notes'] =
                        (isset($settings_res['payment_method']['notes']) && !empty($settings_res['payment_method']['notes'])) ?
                        str_replace('\"', '', str_replace('\r\n', '&#13;&#10;', $settings_res['payment_method']['notes'])) : '';
                    $time_slot_config = get_settings('time_slot_config', $settings['time_slot_config']);

                    if (!empty($time_slot_config) && isset($time_slot_config)) {
                        $time_slot_config['delivery_starts_from'] = $time_slot_config['delivery_starts_from'] - 1;
                        $time_slot_config['starting_date'] = date('Y-m-d', strtotime(date('d-m-Y') . ' + ' . intval($time_slot_config['delivery_starts_from']) . ' days'));
                    }
                    $settings_res['time_slot_config'] = $time_slot_config;
                    $time_slots = fetch_details('time_slots', ['status' => '1'], '*', '', '', 'from_time', 'ASC');

                    if (!empty($time_slots)) {
                        for ($i = 0; $i < count($time_slots); $i++) {
                            $datetime = DateTime::createFromFormat("h:i:s a", $time_slots[$i]['from_time']);
                        }
                    }

                    $settings_res['time_slots'] = array_values($time_slots);

                    $general_settings = $settings_res;
                } else {

                    foreach ($settings as $type => $isjson) {
                        if ($type == 'payment_method') {
                            continue;
                        }
                        $general_settings[$type] = [];
                        $settings_res = get_settings($type, $isjson);

                        if ($type == 'logo') {
                            $settings_res = base_url() . $settings_res;
                        }
                        if ($type == 'web_settings') {
                            $settings_res['logo'] = base_url() . $settings_res['logo'];
                            $settings_res['footer_logo'] = base_url() . $settings_res['footer_logo'];
                            $settings_res['favicon'] = base_url() . $settings_res['favicon'];
                        }
                        if ($type == 'user_data' && isset($user_id) && !empty($user_id)) {
                            $cart_total_response = get_cart_total($user_id, false, 0);
                            $res = $this->address_model->get_address($user_id, false, false, true);
                            if (!empty($res)) {
                                $zipcode_id = fetch_details('areas', ['id' => $res[0]['area_id']], 'zipcode_id');
                                if (!empty($zipcode_id)) {
                                    $zipcode = fetch_details('zipcodes', ['id' => $zipcode_id[0]['zipcode_id']], 'zipcode');
                                }
                            }
                            $settings_res = fetch_users($user_id);
                            $settings_res[0]['country_code'] = (isset($settings_res[0]['country_code']) && $settings_res[0]['country_code'] != null) ? $settings_res[0]['country_code'] : '';
                            $settings_res[0]['address'] = (isset($settings_res[0]['address']) && $settings_res[0]['address'] != null) ? $cart_total_response[0]['address'] : '';
                            $settings_res[0]['cities'] = (isset($settings_res[0]['cities']) && $settings_res[0]['cities'] != null) ? $cart_total_response[0]['cities'] : '';
                            $settings_res[0]['street'] = (isset($settings_res[0]['street']) && $settings_res[0]['street'] != null) ? $cart_total_response[0]['street'] : '';
                            $settings_res[0]['area'] = (isset($settings_res[0]['area']) && $settings_res[0]['area'] != null) ? $cart_total_response[0]['area'] : '';
                            $settings_res[0]['image'] = (isset($settings_res[0]['image']) && $settings_res[0]['image'] != null) ? (base_url() . USER_IMG_PATH . $settings_res[0]['image']) : '';
                            $settings_res[0]['cart_total_items'] = (isset($cart_total_response[0]['cart_count']) && $cart_total_response[0]['cart_count'] > 0) ? $cart_total_response[0]['cart_count'] : '0';
                            $settings_res[0]['pincode'] = (!empty($res) && !empty($zipcode)) ? $zipcode[0]['zipcode'] : '';
                            $settings_res = $settings_res[0];
                        } elseif ($type == 'user_data' && !isset($user_id)) {
                            $settings_res = null;
                        }
                        //Strip tags in case of terms_conditions and privacy_policy
                        array_push($general_settings[$type], $settings_res);
                    }
                }
                $this->response['error'] = false;
                $this->response['message'] = 'Settings retrieved successfully';
                $this->response['data'] = $general_settings;
                $this->response['data']['tags'] = $tags;
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Settings Not Found';
                $this->response['data'] = array();
            }
            print_r(json_encode($this->response));
        }
    }

    //6.get_slider_images
    public function get_slider_images()
    {

        $res = fetch_details('sliders', '');
        $i = 0;
        foreach ($res as $row) {

            if ($res[$i]['link'] == null || empty($res[$i]['link'])) {
                $res[$i]['link'] = "";
            }
            $res[$i]['image'] = base_url($res[$i]['image']);

            if (strtolower($res[$i]['type']) == 'categories') {
                $id = (!empty($res[$i]['type_id']) && isset($res[$i]['type_id'])) ? $res[$i]['type_id'] : '';
                $cat_res = $this->category_model->get_categories($id);
                $res[$i]['data'] = $cat_res;
            } else if (strtolower($res[$i]['type']) == 'products') {
                $id = (!empty($res[$i]['type_id']) && isset($res[$i]['type_id'])) ? $res[$i]['type_id'] : '';
                $pro_res = fetch_product(NULL, NULL, $id);
                $res[$i]['data'] = $pro_res['product'];
            } else {
                $res[$i]['data'] = [];
            }

            $i++;
        }
        $this->response['error'] = false;
        $this->response['data'] = $res;
        print_r(json_encode($this->response));
    }

    //7.validate_promo_code
    public function validate_promo_code()
    {
        /*
            promo_code:'NEWOFF10'
            user_id:28
            final_total:'300'

        */

        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('promo_code', 'Promo Code', 'trim|required|xss_clean');
        $this->form_validation->set_rules('final_total', 'Final Total', 'trim|required|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            print_r(json_encode(validate_promo_code($_POST['promo_code'], $user_id, $_POST['final_total'])));
        }
    }

    //8.place_order

    public function place_order()
    {
        /*
            user_id:5
            mobile:9974692496
            email:testmail123@gmail.com // only enter when ordered product is digital product and one of them is not downloadable(download_allowed = 0)
            product_variant_id: 1,2,3
            quantity: 3,3,1
            total:60.0
            delivery_charge:20.0
            tax_amount:10
            tax_percentage:10
            final_total:55
            latitude:40.1451
            longitude:-45.4545
            promo_code:NEW20 {optional}
            payment_method: Paypal / Payumoney / COD / PAYTM
            address_id:17
            delivery_date:10/12/2012
            delivery_time:Today - Evening (4:00pm to 7:00pm)
            is_wallet_used:1 {By default 0}
            wallet_balance_used:1
            active_status:awaiting {optional}
            order_note:text      //{optional}
            app_link_token:is it's affiliate product      //{optional}
      
        */



        if (!$this->verify_token()) {
            return false;
        }


        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('product_variant_id', 'Product Variant Id', 'trim|required|xss_clean');
        $this->form_validation->set_rules('quantity', 'Quantities', 'trim|required|xss_clean');
        $this->form_validation->set_rules('final_total', 'Final Total', 'trim|required|numeric|xss_clean');
        $this->form_validation->set_rules('promo_code', 'Promo Code', 'trim|xss_clean');
        $this->form_validation->set_rules('order_note', 'Order Note', 'trim|xss_clean');

        /*
        ------------------------------
        If Wallet Balance Is Used
        ------------------------------
        */
        $download_allowed = [];
        $product_variant_id = explode(',', $_POST['product_variant_id']);
        $product_variant = $this->db->select('p.type,p.download_allowed')
            ->join('products p ', 'pv.product_id=p.id', 'left')
            ->where_in('pv.id', $product_variant_id)->order_by('FIELD(pv.id,' . $_POST['product_variant_id'] . ')')->get('product_variants pv')->result_array();
        if (!empty($product_variant)) {
            $product_type = array_values(array_unique(array_column($product_variant, "type")));
            $download_allowed = array_values(array_unique(array_column($product_variant, "download_allowed")));
        }

        if (in_array(0, $download_allowed) && $product_type[0] == "digital_product") {
            $this->form_validation->set_rules('email', 'Email ID', 'required|valid_email|trim|xss_clean');
        }

        $this->form_validation->set_rules('is_wallet_used', ' Wallet Balance Used', 'trim|required|numeric|xss_clean');
        if (isset($_POST['is_wallet_used']) && $_POST['is_wallet_used'] == '1') {
            $this->form_validation->set_rules('wallet_balance_used', ' Wallet Balance ', 'trim|required|numeric|xss_clean');
        }
        $this->form_validation->set_rules('latitude', 'Latitude', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('longitude', 'Longitude', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('payment_method', 'Payment Method', 'trim|required|xss_clean');
        $this->form_validation->set_rules('delivery_date', 'Delivery Date', 'trim|xss_clean');
        $this->form_validation->set_rules('delivery_time', 'Delivery time', 'trim|xss_clean');

        if ($product_type[0] == "variable_product" || $product_type[0] == "simple_product") {
            $this->form_validation->set_rules('address_id', 'Address id', 'trim|required|numeric|xss_clean');
        }

        $settings = get_settings('system_settings', true);
        $currency = isset($settings['currency']) && !empty($settings['currency']) ? $settings['currency'] : '';
        if (isset($settings['minimum_cart_amt']) && !empty($settings['minimum_cart_amt'])) {
            $this->form_validation->set_rules('total', 'Total', 'trim|xss_clean|greater_than_equal_to[' . $settings['minimum_cart_amt'] . ']', array('greater_than_equal_to' => 'Total amount should be greater or equal to ' . $currency . $settings['minimum_cart_amt'] . ' total is ' . $currency . $_POST['total'] . ''));
        }
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {

            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';

            $_POST['order_note'] = (isset($_POST['order_note']) && !empty($_POST['order_note'])) ? $this->input->post("order_note", true) : NULL;

            $limit = (isset($_FILES['documents']['name'])) ? count($_FILES['documents']['name']) : 0;

            $images_new_name_arr = array();

            /* checking if any of the product requires the media file or not */
            $product_variant_ids = $this->input->post('product_variant_id', true);
            $product_ids = fetch_details('product_variants', '', 'product_id', '', '', '', '', 'id', $product_variant_ids);
            $product_ids = (!empty($product_ids)) ? array_column($product_ids, 'product_id') : [];
            $product_ids = (!empty($product_ids)) ? implode(",", $product_ids) : "";
            $product_attachments = fetch_details('products', '', 'is_attachment_required', '', '', '', '', 'id', $product_ids);

            $is_attachment_required = false;
            if (!empty($product_attachments))
                foreach ($product_attachments as $attachment) {
                    if ($attachment['is_attachment_required'] == 1) {
                        $is_attachment_required = true;
                        break;
                    }
                }
            /* ends checking if any of the product requires the media file or not */

            if (empty($_FILES['documents']['name']) && $is_attachment_required) {
                $this->response['error'] = true;
                $this->response['message'] = "Some of your products in cart require at least one media file to be uploaded!";
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
                return;
            }
            if ($limit >= 0) {
                if (!file_exists(FCPATH . ORDER_ATTACHMENTS)) {
                    mkdir(FCPATH . ORDER_ATTACHMENTS, 0777);
                }
                $temp_array = array();
                $files = $_FILES;
                $images_info_error = "";
                $allowed_media_types = 'jpg|png|jpeg';
                $config = [
                    'upload_path' =>  FCPATH . ORDER_ATTACHMENTS,
                    'allowed_types' => $allowed_media_types,
                    'max_size' => 8000,
                ];

                $upload = $this->upload;
                $upload->initialize($config);
                // if (!empty($_FILES['documents']['name'][0]) && isset($_FILES['documents']['name'])) {
                //     $other_image_cnt = count($_FILES['documents']['name']);
                //     $other_img = $this->upload;
                //     $other_img->initialize($config);

                //     for ($i = 0; $i < $other_image_cnt; $i++) {

                //         if (!empty($_FILES['documents']['name'][$i])) {

                //             $_FILES['temp_image']['name'] = $files['documents']['name'][$i];
                //             $_FILES['temp_image']['type'] = $files['documents']['type'][$i];
                //             $_FILES['temp_image']['tmp_name'] = $files['documents']['tmp_name'][$i];
                //             $_FILES['temp_image']['error'] = $files['documents']['error'][$i];
                //             $_FILES['temp_image']['size'] = $files['documents']['size'][$i];
                //             if (!$other_img->do_upload('temp_image')) {
                //                 $images_info_error = 'documents :' . $images_info_error . ' ' . $other_img->display_errors();
                //             } else {
                //                 $temp_array = $other_img->data();
                //                 resize_review_images($temp_array, FCPATH . ORDER_ATTACHMENTS);
                //                 $images_new_name_arr[$i] = ORDER_ATTACHMENTS . $temp_array['file_name'];
                //             }
                //         } else {
                //             $_FILES['temp_image']['name'] = $files['documents']['name'][$i];
                //             $_FILES['temp_image']['type'] = $files['documents']['type'][$i];
                //             $_FILES['temp_image']['tmp_name'] = $files['documents']['tmp_name'][$i];
                //             $_FILES['temp_image']['error'] = $files['documents']['error'][$i];
                //             $_FILES['temp_image']['size'] = $files['documents']['size'][$i];
                //             if (!$other_img->do_upload('temp_image')) {
                //                 $images_info_error = $other_img->display_errors();
                //             }
                //         }
                //     }
                //     //Deleting Uploaded attachments if any overall error occured
                //     if ($images_info_error != NULL || !$this->form_validation->run()) {
                //         if (isset($images_new_name_arr) && !empty($images_new_name_arr || !$this->form_validation->run())) {
                //             foreach ($images_new_name_arr as $key => $val) {
                //                 unlink(FCPATH . ORDER_ATTACHMENTS . $images_new_name_arr[$key]);
                //             }
                //         }
                //     }
                // }

                foreach ($_FILES['documents']['name'] as $variant_id => $files_array) {
                    foreach ($files_array as $index => $file_name) {
                        if (!empty($file_name)) {
                            $_FILES['temp_image']['name']     = $file_name;
                            $_FILES['temp_image']['type']     = $_FILES['documents']['type'][$variant_id][$index];
                            $_FILES['temp_image']['tmp_name'] = $_FILES['documents']['tmp_name'][$variant_id][$index];
                            $_FILES['temp_image']['error']    = $_FILES['documents']['error'][$variant_id][$index];
                            $_FILES['temp_image']['size']     = $_FILES['documents']['size'][$variant_id][$index];

                            if (!$upload->do_upload('temp_image')) {
                                $images_info_error .= "Variant ID $variant_id: " . $upload->display_errors();
                            } else {
                                $uploaded_data = $upload->data();
                                resize_review_images($uploaded_data, FCPATH . ORDER_ATTACHMENTS);

                                // Map attachment to product_variant_id
                                $attachments[$variant_id][] = ORDER_ATTACHMENTS . $uploaded_data['file_name'];
                            }

                            //Deleting Uploaded attachments if any overall error occured
                            if ($images_info_error != NULL || !$this->form_validation->run()) {
                                if (isset($attachments) && !empty($attachments || !$this->form_validation->run())) {
                                    foreach ($attachments as $key => $val) {
                                        unlink(FCPATH . ORDER_ATTACHMENTS . $attachments[$key]);
                                    }
                                }
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
            } else {
                $this->response['error'] = true;
                $this->response['message'] = "You Can Not Upload More Then one Images !";
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
                return;
            }

            // $attachments = $images_new_name_arr;
            // $mapped_attachments = [];

            //checking for product availability 
            if (isset($_POST['product_type']) && $_POST['product_type'] != 'digital_product') {

                $area_id = fetch_details('addresses', ['id' => $_POST['address_id']], ['area_id', 'area', 'pincode', 'city', 'city_id']);
                $zipcode = $area_id[0]['pincode'];
                $zipcode_id = fetch_details('zipcodes', ['zipcode' => $zipcode], 'id')[0];

                $city = $area_id[0]['city'];
                $city_id = fetch_details('cities', ['name' => $city], 'id');
                $city_id = $city_id[0]['id'];

                if ((isset($shipping_settings['pincode_wise_deliverability']) && $shipping_settings['pincode_wise_deliverability'] == 1) || (isset($shipping_settings['local_shipping_method']) && isset($shipping_settings['shiprocket_shipping_method']) && $shipping_settings['local_shipping_method'] == 1 && $shipping_settings['shiprocket_shipping_method'] == 1)) {
                    $product_delivarable = check_cart_products_delivarable($_POST['user_id'], $area_id[0]['area_id'], $zipcode, $zipcode_id['id']);
                }
                if (isset($shipping_settings['city_wise_deliverability']) && $shipping_settings['city_wise_deliverability'] == 1 && $shipping_settings['shiprocket_shipping_method'] != 1) {
                    $product_delivarable = check_cart_products_delivarable($_POST['user_id'], $area_id[0]['area_id'], '', '', $city, $city_id);
                }

                if (!empty($product_delivarable)) {
                    $product_not_delivarable = array_filter($product_delivarable, function ($var) {
                        return ($var['is_deliverable'] == false && $var['product_id'] != null);
                    });
                    $product_not_delivarable = array_values($product_not_delivarable);
                    $product_delivarable = array_filter($product_delivarable, function ($var) {
                        return ($var['product_id'] != null);
                    });
                    if (!empty($product_not_delivarable)) {
                        $this->response['error'] = true;
                        $this->response['message'] = "Some of the item(s) are not delivarable on selected address. Try changing address or modify your cart items.";
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['data'] = array();
                        print_r(json_encode($this->response));
                        return;
                    }
                }
            }
            $product_variant_id = explode(',', $_POST['product_variant_id']);


            $_POST['attachments'] = $attachments;



            /* checking for product availability */
            $shipping_method = get_settings('shipping_method', true);
            $product_delivarable = array();
            $address_id = $this->input->post('address_id', true);

            $area_id = fetch_details('addresses', ['id' => $address_id], ['area_id', 'area', 'pincode', 'city']);
            $zipcode = $area_id[0]['pincode'];
            $zipcode_id = fetch_details('zipcodes', ['zipcode' => $zipcode], 'id')[0];

            $city = $area_id[0]['city'];
            $city_id = fetch_details('cities', ['name' => $city], 'id');
            $city_id = $city_id[0]['id'];

            if ((isset($shipping_method['pincode_wise_deliverability']) && $shipping_method['pincode_wise_deliverability'] == 1) || (isset($shipping_method['local_shipping_method']) && isset($shipping_method['shiprocket_shipping_method']) && $shipping_method['local_shipping_method'] == 1 && $shipping_method['shiprocket_shipping_method'] == 1)) {
                $product_delivarable = check_cart_products_delivarable($user_id, $area_id[0]['area_id'], $zipcode, $zipcode_id['id']);
            }

            if (isset($shipping_method['city_wise_deliverability']) && $shipping_method['city_wise_deliverability'] == 1 && $shipping_method['shiprocket_shipping_method'] != 1) {
                $product_delivarable = check_cart_products_delivarable($user_id, $area_id[0]['area_id'], '', '', $city, $city_id);
            }

            $_POST['user_id'] = $user_id;
            if (!empty($product_delivarable) && ($product_type[0] == "variable_product" || $product_type[0] == "simple_product")) {
                $product_not_delivarable = array_filter($product_delivarable, function ($var) {
                    return ($var['is_deliverable'] == false && $var['product_id'] != null);
                });
                $product_not_delivarable = array_values($product_not_delivarable);
                $product_delivarable = array_filter($product_delivarable, function ($var) {
                    return ($var['product_id'] != null);
                });
                if (!empty($product_not_delivarable)) {
                    $this->response['error'] = true;
                    $this->response['message'] = "Some of the item(s) are not delivarable on selected address. Try changing address or modify your cart items.";
                    $this->response['data'] = $product_delivarable;
                    print_r(json_encode($this->response));
                    return;
                } else {
                    $data = array();
                    $_POST['is_delivery_charge_returnable'] = isset($_POST['delivery_charge']) && !empty($_POST['delivery_charge']) && $_POST['delivery_charge'] != '' && $_POST['delivery_charge'] > 0 ? 1 : 0;
                    $delivery_type = $product_delivarable[0]['delivery_by'];
                    $_POST['is_shiprocket_order'] = (isset($product_delivarable) && !empty($product_delivarable) && $delivery_type == 'standard_shipping') ? '1' : '0';

                    // Decode affiliate_ref cookie: Format should be {"product_id": "token"}
                    $affiliate_cookie = isset($_POST['app_link_token']) ? $_POST['app_link_token'] : '';
                    // print_r($affiliate_cookie);
                    // die;
                    if (isset($_POST['app_link_token']) && !empty($_POST['app_link_token'])) {
                        $cart = get_cart_total($_POST['user_id'], false, '0', $_POST['address_id'], token: $affiliate_cookie);
                        // print_r($cart);
                        $affiliate_data = [];

                        foreach ($cart as $item) {
                            if (isset($item['id'], $item['affiliate_id'], $item['affiliate_token'], $item['category_commission'])) {
                                $affiliate_data[$item['id']] = [
                                    'affiliate_id' => $item['affiliate_id'],
                                    'affiliate_token' => $item['affiliate_token'],
                                    'category_commission' => $item['category_commission'],
                                    'affiliate_commission_amount' => $item['affiliate_commission_amount']
                                ];

                                $_POST['affiliate_data'] = $affiliate_data;
                            }
                        }
                    }
                    // print_R($_POST);
                    // die;
                    // $affiliate_data = [];




                    $res = $this->order_model->place_order($_POST);

                    if (!empty($res)) {



                        $transaction_id = rand(11111111, 99999999);
                        if ($_POST['payment_method'] == "phonepe") {
                            $data['status'] = "awaiting";
                            $data['txn_id'] = $transaction_id;
                            $data['message'] = "awaiting";
                            $data['order_id'] = $res['order_id'];
                            $data['user_id'] = $user_id;
                            $data['type'] = $_POST['payment_method'];
                            $data['amount'] = $_POST['final_total'];

                            $this->transaction_model->add_transaction($data);
                        }
                        if ($_POST['payment_method'] == "bank_transfer") {
                            $data['status'] = "awaiting";
                            $data['txn_id'] = null;
                            $data['message'] = null;
                            $data['order_id'] = $res['order_id'];
                            $data['user_id'] = $user_id;
                            $data['type'] = $_POST['payment_method'];
                            $data['amount'] = $_POST['final_total'];

                            $this->transaction_model->add_transaction($data);
                        }
                    }
                    if ($_POST['payment_method'] == "paystack") {
                        $this->load->library(['paystack']);
                        $response = $this->paystack->initialize((float) $_POST['final_total'], $this->user_details["email"], $res["order_id"]);
                        $response = (json_decode($response, true));


                        if (!$response['status'] && !isset($response["data"]['authorization_url'])) {

                            $order = fetch_details("orders", ["id" => $res["order_id"]]);
                            if (count($order) != 0) {
                                $order = $order[0];
                                if ((float) "" . $order["wallet_balance"] > 0) {
                                    $user = (fetch_details("users", ["id" => $order['user_id']]));
                                    if (count($user) > 0) {
                                        $user = $user[0];
                                        print_r($order["wallet_balance"]);
                                        $wallet_balance = (float) ("" . $user["wallet_balance"]) + (float) ("" . $order["wallet_balance"]);
                                        update_details([
                                            "wallet_balance" => $wallet_balance
                                        ], ["id" => $order['user_id']], "users");
                                    }
                                }
                            }
                            update_details(['active_status' => 'failed'], ['order_id' => $res["order_id"]], 'order_items');
                            $status = json_encode(array(array('failed', date("d-m-Y h:i:sa"))));
                            update_details(['status' => $status], ['order_id' => $res["order_id"]], 'order_items', false);
                            update_stock($data[0]['product_variant_id'], $data[0]['quantity'], 'plus');
                            return json_encode([
                                "error" => true,
                                "message" => "something went wrong with paystack please try again with another payment method."
                            ]);
                        }
                        $webviewPayment = [
                            "url" => ($response["data"]["authorization_url"]),
                            "failure_url" => base_url("payment/cancel"),
                            "success_url" => base_url("payment/success")
                        ];
                        $res["webview_payment_options"] = $webviewPayment;
                    }

                    print_r(json_encode($res));
                }
            } else {
                $data = array();
                $_POST['is_delivery_charge_returnable'] = isset($_POST['delivery_charge']) && !empty($_POST['delivery_charge']) && $_POST['delivery_charge'] != '' && $_POST['delivery_charge'] > 0 ? 1 : 0;

                $res = $this->order_model->place_order($_POST);
                if (!empty($res)) {
                    $transaction_id = rand(11111111, 99999999);
                    if ($_POST['payment_method'] == "phonepe") {
                        $data['status'] = "awaiting";
                        $data['txn_id'] = $transaction_id;
                        $data['message'] = "awaiting";
                        $data['order_id'] = $res['order_id'];
                        $data['user_id'] = $user_id;
                        $data['type'] = $_POST['payment_method'];
                        $data['amount'] = $_POST['final_total'];

                        $this->transaction_model->add_transaction($data);
                    }
                    if ($_POST['payment_method'] == "Razorpay") {
                        $data['status'] = "awaiting";
                        $data['txn_id'] = null;
                        $data['message'] = null;
                        $data['order_id'] = $res['order_id'];
                        $data['user_id'] = $user_id;
                        $data['type'] = $_POST['payment_method'];
                        $data['amount'] = $_POST['final_total'];
                        $this->transaction_model->add_transaction($data);
                    }
                }
                if ($_POST['payment_method'] == "paystack") {
                    $this->load->library(['paystack']);
                    $response = $this->paystack->initialize((float) $_POST['final_total'], $this->user_details["email"], $res["order_id"]);
                    $response = (json_decode($response, true));


                    if (!$response['status'] && !isset($response["data"]['authorization_url'])) {

                        $order = fetch_details("orders", ["id" => $res["order_id"]]);
                        if (count($order) != 0) {
                            $order = $order[0];
                            if ((float) "" . $order["wallet_balance"] > 0) {
                                $user = (fetch_details("users", ["id" => $order['user_id']]));
                                if (count($user) > 0) {
                                    $user = $user[0];
                                    print_r($order["wallet_balance"]);
                                    $wallet_balance = (float) ("" . $user["wallet_balance"]) + (float) ("" . $order["wallet_balance"]);
                                    update_details([
                                        "wallet_balance" => $wallet_balance
                                    ], ["id" => $order['user_id']], "users");
                                }
                            }
                        }
                        update_details(['active_status' => 'failed'], ['order_id' => $res["order_id"]], 'order_items');
                        $status = json_encode(array(array('failed', date("d-m-Y h:i:sa"))));
                        update_details(['status' => $status], ['order_id' => $res["order_id"]], 'order_items', false);
                        update_stock($data[0]['product_variant_id'], $data[0]['quantity'], 'plus');
                        return json_encode([
                            "error" => true,
                            "message" => "something went wrong with paystack please try again with another payment method."
                        ]);
                    }
                    $webviewPayment = [
                        "url" => ($response["data"]["authorization_url"]),
                        "failure_url" => base_url("payment/cancel"),
                        "success_url" => base_url("payment/success")
                    ];
                    $res["webview_payment_options"] = $webviewPayment;
                }
                print_r(json_encode($res));
            }
        }
    }

    //get_orders
    public function get_orders()
    {
        // user_id:101
        // offset:0    
        //active_status: received  {received,delivered,cancelled,processed,returned}     // optional
        // limit:25           // { default - 0 } optional
        // sort: id / date_added // { default - id } optional
        // order:DESC/ASC      // { default - DESC } optional        
        // download_invoice:0 // { default - 0 } optional       

        if (!$this->verify_token()) {
            return false;
        }
        $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
        $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
        $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $this->input->post('sort', true) : 'o.id';
        $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $this->input->post('order', true) : 'DESC';
        $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : '';

        $this->form_validation->set_rules('active_status', 'status', 'trim|xss_clean');
        $this->form_validation->set_rules('download_invoice', 'Invoice', 'trim|numeric|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $multiple_status = (isset($_POST['active_status']) && !empty($_POST['active_status'])) ? explode(',', $_POST['active_status']) : false;
            $order_id = (isset($_POST['order_id']) && !empty($_POST['order_id'])) ? $_POST['order_id'] : '';

            $order_details = fetch_orders($order_id, $user_id, $multiple_status, false, $limit, $offset, $sort, $order, false, false, false, $search, NULL, NULL, NULL, '', true);

            if (!empty($order_details)) {

                $this->response['error'] = false;
                $this->response['message'] = 'Data retrieved successfully';
                $this->response['total'] = $order_details['total'];
                $this->response['data'] = $order_details['order_data'];
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'No Order(s) Found !';
                $this->response['data'] = array();
            }
        }
        print_r(json_encode($this->response));
    }

    /*
        status: cancelled / returned
        order_id:1201 //this is order_item_id
    */
    public function update_order_item_status()
    {
        if (!$this->verify_token()) {
            return false;
        }
        $this->form_validation->set_rules('order_id', 'Order item id', 'trim|required|numeric|xss_clean');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|in_list[cancelled,returned]');
        if ($_POST['status'] == 'returned') {
            $this->form_validation->set_rules('return_reason', 'Return Reason', 'trim|xss_clean');
            $this->form_validation->set_rules('return_item_image[]', 'Return Item Image', 'trim|xss_clean');
            if ($_POST['return_reason'] == '') {
                $this->form_validation->set_rules('other_reason', 'Other Reason', 'trim|required|xss_clean');
            }
        }

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        } else {



            if ($_POST['status'] == 'returned') {
                $return_data = $_POST;
                // $return_data['return_item_image'] = (isset($_FILES['return_item_image']['name']) && !empty($_FILES['return_item_image']['name'])) ? $_FILES['return_item_image']['name'] : '';
                if (!file_exists(FCPATH . RETURN_IMAGES)) {
                    mkdir(FCPATH . RETURN_IMAGES, 0777);
                }
                $temp_array = array();
                $files = $_FILES;
                $images_info_error = "";
                $allowed_media_types = 'jpg|png|jpeg';
                $config = [
                    'upload_path' => FCPATH . RETURN_IMAGES,
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


                $return_data['return_item_image'] = implode(',', $images_new_name_arr);

                // update return reason if other
                if ($return_data['return_reason'] == '') {
                    $return_data['return_reason'] = $return_data['other_reason'];
                }
                $return_data['order_item_id'] = $_POST['order_id'];
            }

            // check for bank receipt if available
            $order_item_data = fetch_details('order_items', ['order_id' => $_POST['order_id']], 'order_id');
            $order_method = fetch_details('orders', ['id' => $order_item_data[0]['order_id']], 'payment_method');
            if ($order_method[0]['payment_method'] == 'bank_transfer') {
                $bank_receipt = fetch_details('order_bank_transfer', ['order_id' => $_POST['order_id']]);

                $transaction_status = fetch_details('transactions', ['order_id' => $_POST['order_id']], 'status');
                if ($_POST['status'] != "cancelled" && (empty($bank_receipt) || strtolower($transaction_status[0]['status']) != 'success')) {
                    $this->response['error'] = true;
                    $this->response['message'] = "Order Status can not update, Bank verification is remain from transactions.";
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }
            }

            if (trim($_POST['status']) == 'returned') {
                $this->response = $this->order_model->update_order_item($_POST['order_id'], trim($_POST['status']), fromapp: true, return_data: $return_data);

                // $this->response = $this->order_model->update_order_item($_POST['order_id'], trim($_POST['status']), 0, true);
            } else {
                $this->response = $this->order_model->update_order_item($_POST['order_id'], trim($_POST['status']), '', true);
            }


            $order_id = fetch_details('order_items', ['id' => $_POST['order_id']], 'id, order_id');

            $order_details = fetch_details('orders', ['id' => $order_id[0]['order_id']], 'id, payment_method');
            if (trim($_POST['status']) == 'cancelled' && $order_details[0]['payment_method'] != 'COD' && $order_details[0]['payment_method'] != 'razorpay' && $order_details[0]['payment_method'] != 'Razorpay' && $order_details[0]['payment_method'] != 'cod' && $this->response['error'] == false) {
                process_refund($_POST['order_id'], $_POST['status'], 'order_items');
            }
            if (trim($_POST['status']) == 'cancelled' && $order_details[0]['payment_method'] != 'COD' && $order_details[0]['payment_method'] != 'cod') {
                $data = fetch_details('order_items', ['id' => $_POST['order_id']], 'product_variant_id,quantity');
                update_stock($data[0]['product_variant_id'], $data[0]['quantity'], 'plus');
            }
        }
        print_r(json_encode($this->response));
        return false;
    }

    // get_invoice_html    
    // order_id:214
    public function get_invoice_html()
    {
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('order_id', 'Order id', 'trim|required|xss_clean');


        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        } else {

            $this->data['main_page'] = VIEW . 'api-order-invoice';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Invoice Management |' . $settings['app_name'];
            $this->data['meta_description'] = $settings['app_name'] . ' | Invoice Management';
            if (isset($_POST['order_id']) && !empty($_POST['order_id'])) {
                $res = $this->order_model->get_order_details(['o.id' => $_POST['order_id']]);
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
                        $temp['product_price'] = $row['product_price'];
                        $temp['price'] = $row['price'];
                        $temp['delivery_boy'] = $row['delivery_boy'];
                        $temp['mobile_number'] = $row['mobile_number'];
                        $temp['active_status'] = $row['oi_active_status'];
                        $temp['hsn_code'] = $row['hsn_code'];
                        array_push($items, $temp);
                    }
                    $this->data['order_detls'] = $res;
                    $this->data['items'] = $items;
                    $this->data['promo_code'] = $promo_code;
                    $this->data['settings'] = get_settings('system_settings', true);
                    $response['error'] = false;
                    $response['message'] = 'Invoice Generated Successfully';
                    $response['data'] = $this->load->view('admin/invoice-template', $this->data, TRUE);
                } else {
                    $response['error'] = true;
                    $response['message'] = 'No Order Details Found !';
                    $response['data'] = [];
                }
            } else {
                $response['error'] = true;
                $response['message'] = 'No Order Details Found !';
                $response['data'] = [];
            }
            print_r(json_encode($response));
            return false;
        }
    }

    // 9 set_product_rating
    public function set_product_rating()
    {
        /*
            user_id: 21
            product_id: 33
            rating: 4.2
            comment: 'Done' {optional}
            images[]:[]
      */
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('product_id', 'Product Id', 'trim|numeric|xss_clean|required');
        $this->form_validation->set_rules('rating', 'Rating', 'trim|numeric|xss_clean|greater_than[0]|less_than[6]');
        $this->form_validation->set_rules('comment', 'Comment', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $response['error'] = true;
            $response['message'] = strip_tags(validation_errors());
            $response['data'] = array();
            echo json_encode($response);
        } else {
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            if (!file_exists(FCPATH . REVIEW_IMG_PATH)) {
                mkdir(FCPATH . REVIEW_IMG_PATH, 0777);
            }

            $temp_array = array();
            $files = $_FILES;
            $images_new_name_arr = array();
            $images_info_error = "";
            $config = [
                'upload_path' => FCPATH . REVIEW_IMG_PATH,
                'allowed_types' => 'jpg|png|jpeg|gif',
                'max_size' => 8000,
            ];

            if (!empty($_FILES['images']['name'][0]) && isset($_FILES['images']['name'])) {
                $other_image_cnt = count($_FILES['images']['name']);
                $other_img = $this->upload;
                $other_img->initialize($config);

                for ($i = 0; $i < $other_image_cnt; $i++) {

                    if (!empty($_FILES['images']['name'][$i])) {

                        $_FILES['temp_image']['name'] = $files['images']['name'][$i];
                        $_FILES['temp_image']['type'] = $files['images']['type'][$i];
                        $_FILES['temp_image']['tmp_name'] = $files['images']['tmp_name'][$i];
                        $_FILES['temp_image']['error'] = $files['images']['error'][$i];
                        $_FILES['temp_image']['size'] = $files['images']['size'][$i];
                        if (!$other_img->do_upload('temp_image')) {
                            $images_info_error = 'Images :' . $images_info_error . ' ' . strip_tags($other_img->display_errors());
                        } else {
                            $temp_array = $other_img->data();
                            resize_review_images($temp_array, FCPATH . REVIEW_IMG_PATH);
                            $images_new_name_arr[$i] = REVIEW_IMG_PATH . $temp_array['file_name'];
                        }
                    } else {
                        $_FILES['temp_image']['name'] = $files['images']['name'][$i];
                        $_FILES['temp_image']['type'] = $files['images']['type'][$i];
                        $_FILES['temp_image']['tmp_name'] = $files['images']['tmp_name'][$i];
                        $_FILES['temp_image']['error'] = $files['images']['error'][$i];
                        $_FILES['temp_image']['size'] = $files['images']['size'][$i];
                        if (!$other_img->do_upload('temp_image')) {
                            $images_info_error = strip_tags($other_img->display_errors());
                        }
                    }
                }

                //Deleting Uploaded Images if any overall error occured
                if ($images_info_error != NULL || !$this->form_validation->run()) {
                    if (isset($images_new_name_arr) && !empty($images_new_name_arr || !$this->form_validation->run())) {
                        foreach ($images_new_name_arr as $key => $val) {
                            unlink(FCPATH . REVIEW_IMG_PATH . $images_new_name_arr[$key]);
                        }
                    }
                }
            }

            if ($images_info_error != NULL) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = $images_info_error;
                print_r(json_encode($this->response));
                return;
            }

            $res = $this->db->select('*')->join('product_variants pv', 'pv.id=oi.product_variant_id')->join('products p', 'p.id=pv.product_id')->where(['pv.product_id' => $_POST['product_id'], 'oi.user_id' => $user_id, 'oi.active_status!=' => 'returned'])->limit(1)->get('order_items oi')->result_array();
            if (empty($res)) {
                $response['error'] = true;
                $response['message'] = 'You cannot review as the product is not purchased yet!';
                $response['data'] = array();
                echo json_encode($response);
                return;
            }

            $rating_data = fetch_details('product_rating', ['user_id' => $user_id, 'product_id' => $_POST['product_id']], 'images');
            $rating_images = $images_new_name_arr;
            $_POST['images'] = $rating_images;
            $_POST['user_id'] = $user_id;
            $this->rating_model->set_rating($_POST);
            $rating_data = $this->rating_model->fetch_rating((isset($_POST['product_id'])) ? $_POST['product_id'] : '', '', '25', '0', 'id', 'DESC');
            $rating['product_rating'] = $rating_data['product_rating'];
            $rating['no_of_rating'] = (isset($rating['no_of_rating']) && (!empty($rating['no_of_rating']))) ? $rating_data['rating'][0]['no_of_rating'] : '';
            $response['error'] = false;
            $response['message'] = 'Product Rated Successfully';
            $response['data'] = $rating;
            echo json_encode($response);
            return;
        }
    }

    //  delete_product_rating
    public function delete_product_rating()
    {
        /*
        rating_id:32
        */
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('rating_id', 'Rating Id', 'trim|numeric|required|xss_clean');

        if (!$this->form_validation->run()) {
            $response['error'] = true;
            $response['message'] = strip_tags(validation_errors());
            $response['data'] = array();
            echo json_encode($response);
        } else {
            $this->rating_model->delete_rating($_POST['rating_id']);
            $response['error'] = false;
            $response['message'] = 'Rating Deleted Successfully';
            $response['data'] = array();
            echo json_encode($response);
        }
    }

    // get_product_rating
    /*
    product_id : 12
    user_id : 1 		{optional}
    limit:25                // { default - 25 } optional
    offset:0                // { default - 0 } optional
    sort: type   			// { default - type } optional
    order:DESC/ASC          // { default - DESC } optional
  */
    public function get_product_rating()
    {

        $this->form_validation->set_rules('product_id', 'Product Id', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('user_id', 'User Id', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        }
        $limit = (isset($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
        $offset = (isset($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
        $sort = (isset($_POST['sort(array)']) && !empty(trim($_POST['sort']))) ? $this->input->post('sort', true) : 'id';
        $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $this->input->post('order', true) : 'DESC';
        $has_images = (isset($_POST['has_images']) && !empty(trim($_POST['has_images']))) ? 1 : 0;
        // update category clicks
        $category_id = fetch_details('products', ['id' => $this->input->post('product_id', true)], 'category_id');
        $this->db->set('clicks', 'clicks+1', FALSE);
        $this->db->where('id', $category_id[0]['category_id']);
        $this->db->update('categories');

        $pr_rating = fetch_details('products', ['id' => $this->input->post('product_id', true)], 'rating');


        $rating = $this->rating_model->fetch_rating((isset($_POST['product_id'])) ? $_POST['product_id'] : '', '', $limit, $offset, $sort, $order, '', $has_images);
        if (!empty($rating)) {
            $response['error'] = false;
            $response['message'] = 'Rating retrieved successfully';
            $response['no_of_rating'] = (!empty($rating['rating'][0]['no_of_rating'])) ? $rating['rating'][0]['no_of_rating'] : 0;
            $response['total'] = $rating['total_reviews'];
            $response['star_1'] = $rating['star_1'];
            $response['star_2'] = $rating['star_2'];
            $response['star_3'] = $rating['star_3'];
            $response['star_4'] = $rating['star_4'];
            $response['star_5'] = $rating['star_5'];
            $response['total_images'] = $rating['total_images'];
            $response['product_rating'] = (!empty($pr_rating)) ? $pr_rating[0]['rating'] : "0";
            $response['data'] = $rating['product_rating'];
        } else {
            $response['error'] = true;
            $response['message'] = 'No ratings found !';
            $response['no_of_rating'] = array();
            $response['data'] = array();
        }
        echo json_encode($response);
    }

    //10. get_user_cart
    public function get_user_cart()
    {
        /*
          user_id:2
          delivery_pincode:370001 //optional when standard shipping is on
          only_delivery_charge:0 (default:0)// if 1 it's only returen shiprocket delivery charge OR return all cart information
          address_id:2 // only when only_delivery_charge is 1
          is_saved_for_later: 1 { default:0 }
        */

        if (!$this->verify_token()) {
            return false;
        }


        if (isset($_POST['only_delivery_charge']) && $_POST['only_delivery_charge'] == 1) {
            $this->form_validation->set_rules('address_id', 'Address Id', 'trim|numeric|required|xss_clean');
        } else {
            $this->form_validation->set_rules('delivery_pincode', 'Delivery Pincode', 'trim|numeric|xss_clean');
        }
        $this->form_validation->set_rules('is_saved_for_later', 'Saved for later', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('only_delivery_charge', 'Only Delivery Charge', 'trim|numeric|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $this->load->library(['Shiprocket']);
            $shipping_method = get_settings('shipping_method', true);
            $only_delivery_charge = (isset($_POST['only_delivery_charge'])) ? $_POST['only_delivery_charge'] : 0;
            $is_saved_for_later = (isset($_POST['is_saved_for_later']) && $_POST['is_saved_for_later'] == 1) ? $_POST['is_saved_for_later'] : 0;

            $product_delivarable = array();
            $address_id = (isset($_POST['address_id'])) ? $_POST['address_id'] : 0;

            $area_id = fetch_details('addresses', ['id' => $address_id], ['area_id', 'area', 'pincode', 'city']);
            $zipcode = $area_id[0]['pincode'];
            $zipcode_id = fetch_details('zipcodes', ['zipcode' => $zipcode], 'id')[0];

            $city = $area_id[0]['city'];
            $city_id = fetch_details('cities', ['name' => $city], 'id');
            $city_id = $city_id[0]['id'];

            if ((isset($shipping_method['pincode_wise_deliverability']) && $shipping_method['pincode_wise_deliverability'] == 1) || (isset($shipping_method['local_shipping_method']) && isset($shipping_method['shiprocket_shipping_method']) && $shipping_method['local_shipping_method'] == 1 && $shipping_method['shiprocket_shipping_method'] == 1)) {
                $product_availability = check_cart_products_delivarable($user_id, $area_id[0]['area_id'], $zipcode, $zipcode_id['id']);
            }
            if (isset($shipping_method['city_wise_deliverability']) && $shipping_method['city_wise_deliverability'] == 1 && $shipping_method['shiprocket_shipping_method'] != 1) {
                $product_availability = check_cart_products_delivarable($user_id, $area_id[0]['area_id'], '', '', $city, $city_id);
            }

            if ($only_delivery_charge == 1 && isset($product_availability[0]['is_valid_wight']) && $product_availability[0]['is_valid_wight'] == 0) {

                $this->response['error'] = true;
                $this->response['message'] = $product_availability[0]['message'];
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return;
            }


            $product_availability = is_array($product_availability) ? $product_availability : [];
            $product_not_delivarable = array_filter($product_availability, function ($product) {
                return ($product['is_deliverable'] == false);
            });
            $cart_user_data = $this->cart_model->get_user_cart($user_id, $is_saved_for_later);
            $cart_total = 0.0;
            for ($i = 0; $i < count($cart_user_data); $i++) {
                $cart_total += $cart_user_data[$i]['sub_total'];
                $cart[$i]['delivery_by'] = $product_availability[$i]['delivery_by'];
                $cart[$i]['is_deliverable'] = $product_availability[$i]['is_deliverable'];

                if ($cart[$i]['delivery_by'] == "standard_shipping") {
                    $standard_shipping_cart[] = $cart[$i];
                } else {
                    $local_shipping_cart[] = $cart[$i];
                }
            }

            $cart_total_response = get_cart_total($user_id, '', $is_saved_for_later, $address_id);

            if ($only_delivery_charge == 1) {
                $address_detail = fetch_details('addresses', ['id' => $address_id], 'pincode');
                $delivery_pincode = $address_detail[0]['pincode'];
            } else {
                $delivery_pincode = (isset($_POST['delivery_pincode'])) ? $_POST['delivery_pincode'] : 0;
            }

            $tmp_cart_user_data = $cart_user_data;
            $weight = 0;
            if (!empty($tmp_cart_user_data)) {
                for ($i = 0; $i < count($tmp_cart_user_data); $i++) {

                    $product_data = fetch_details('product_variants', ['id' => $tmp_cart_user_data[$i]['product_variant_id']], 'product_id,availability');
                    if (!empty($product_data[0]['product_id'])) {
                        $pro_details = fetch_product($user_id, null, $product_data[0]['product_id']);
                        if (!empty($pro_details['product'])) {

                            $pro_details['product'][0]['net_amount'] = $cart_user_data[$i]['net_amount'];

                            if (trim($pro_details['product'][0]['availability']) == 0 && $pro_details['product'][0]['availability'] != null) {
                                update_details(['is_saved_for_later' => '1'], $cart_user_data[$i]['id'], 'cart');
                                unset($cart_user_data[$i]);
                            }

                            if (!empty($pro_details['product'])) {
                                $cart_user_data[$i]['product_details'] = $pro_details['product'];
                            } else {
                                delete_details(['id' => $cart_user_data[$i]['id']], 'cart');
                                unset($cart_user_data[$i]);
                                continue;
                            }
                        } else {
                            delete_details(['id' => $cart_user_data[$i]['id']], 'cart');
                            unset($cart_user_data[$i]);
                            continue;
                        }
                    } else {
                        delete_details(['id' => $cart_user_data[$i]['id']], 'cart');
                        unset($cart_user_data[$i]);
                        continue;
                    }
                }

                if (isset($shipping_method['shiprocket_shipping_method']) && $shipping_method['shiprocket_shipping_method'] == 1) {

                    $parcels = make_shipping_parcels($tmp_cart_user_data);

                    $parcels_details = check_parcels_deliveriblity($parcels, $delivery_pincode);
                }
            }


            if (empty($cart_user_data)) {
                $this->response['error'] = true;
                $this->response['message'] = 'Cart Is Empty !';
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return;
            }

            if ($only_delivery_charge == 0) {

                $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
                $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
                $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
                $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'DESC';
                $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'p.id';

                $this->response['error'] = false;
                $this->response['message'] = 'Data Retrieved From Cart !';
                $this->response['total_quantity'] = $cart_total_response['quantity'];
                $this->response['sub_total'] = $cart_total_response['sub_total'];
                if (!empty($local_shipping_cart)) {
                    $delivery_charge = get_delivery_charge($_POST['address_id'], $cart_total_response['sub_total'], $user_id);

                    $this->response['delivery_charge'] = str_replace(",", "", $delivery_charge);
                }

                $this->response['tax_percentage'] = (isset($cart_total_response['tax_percentage'])) ? $cart_total_response['tax_percentage'] : "0";
                $this->response['tax_amount'] = (isset($cart_total_response['tax_amount'])) ? $cart_total_response['tax_amount'] : "0";
                $this->response['overall_amount'] = $cart_total_response['overall_amount'];
                $this->response['total_arr'] = $cart_total_response['total_arr'];
                $this->response['variant_id'] = $cart_total_response['variant_id'];

                if (isset($settings['shiprocket_shipping_method']) && $settings['shiprocket_shipping_method'] == 1) {
                    $this->response['parcels_details'] = $parcels_details;
                }

                $this->response['data'] = array_values($cart_user_data);
                $result = $this->Promo_code_model->get_promo_codes($limit, $offset, $sort, $order, $search);

                $this->response['promo_codes'] = $result['data'];
                print_r(json_encode($this->response));
                return;
            } else {

                // if only_delivery_charge is 1

                if (!empty($standard_shipping_cart)) {

                    $delivery_pincode = fetch_details('addresses', ['id' => $_POST['address_id']], 'pincode');
                    $parcels = make_shipping_parcels($tmp_cart_user_data);

                    $parcels_details = check_parcels_deliveriblity($parcels, $delivery_pincode[0]['pincode']);
                    if ($shipping_method['shiprocket_shipping_method'] == 1 && $shipping_method['standard_shipping_free_delivery'] == 1 && $cart_total > $shipping_method['minimum_free_delivery_order_amount']) {
                        $data['delivery_charge_with_cod'] = 0;
                        $data['delivery_charge_without_cod'] = 0;
                        $data['estimated_delivery_days'] = $parcels_details['estimated_delivery_days'];
                        $data['estimate_date'] = $parcels_details['estimate_date'];
                    } else {
                        $data['delivery_charge_with_cod'] = $parcels_details['delivery_charge_with_cod'];
                        $data['delivery_charge_without_cod'] = $parcels_details['delivery_charge_without_cod'];
                        $data['estimated_delivery_days'] = $parcels_details['estimated_delivery_days'];
                        $data['estimate_date'] = $parcels_details['estimate_date'];
                    }
                }
                $this->response['error'] = false;
                $this->response['message'] = 'Data Retrieved Successfully !';
                $this->response['data'] = $data;
                print_r(json_encode($this->response));
                return;
            }
        }
    }
    public function remove_from_cart()
    {
        /*
            user_id:2           
            product_variant_id:23 
            address_id : 2 // optional   
      */
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('product_variant_id', 'Product Variant', 'trim|required|xss_clean');
        $this->form_validation->set_rules('address_id', 'Address Id', 'trim|xss_clean|numeric');


        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $settings = get_settings('system_settings', true);
            $product_variant_id = $_POST['product_variant_id'];
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $_POST['user_id'] = $user_id;
            $this->cart_model->remove_from_cart($_POST);
            $cart_total_response = get_cart_total($user_id);
            if (isset($_POST['address_id']) && !empty($_POST['address_id'])) {
                $delivery_charge = get_delivery_charge($_POST['address_id'], $cart_total_response['sub_total'], $user_id);
            }
            $this->response['error'] = false;
            $this->response['message'] = 'Removed From Cart !';
            if (!empty($cart_total_response) && isset($cart_total_response)) {
                $this->response['data'] = [
                    'total_quantity' => strval($cart_total_response['quantity']),
                    'delivery_charge' => isset($delivery_charge) && !empty($delivery_charge) ? str_replace(",", "", $delivery_charge) : '',
                    'sub_total' => strval($cart_total_response['sub_total']),
                    'total_items' => (isset($cart_total_response[0]['total_items'])) ? strval($cart_total_response[0]['total_items']) : "0",
                    'max_items_cart' => $settings['max_items_cart']
                ];
            } else {
                $this->response['data'] = [];
            }

            print_r(json_encode($this->response));
            return;
        }
    }

    public function manage_cart()
    {
        /*
        Add/Update
        user_id:2
        product_variant_id:23
        is_saved_for_later: 1 { default:0 }
        qty:2 // pass 0 to remove qty
        address_id : 2 // optional
     */
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('product_variant_id', 'Product Variant', 'trim|required|xss_clean');
        $this->form_validation->set_rules('qty', 'Quantity', 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('address_id', 'Address Id', 'trim|xss_clean|numeric');
        $this->form_validation->set_rules('is_saved_for_later', 'Saved For Later', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $product_variant_id = $this->input->post('product_variant_id', true);
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $settings = get_settings('system_settings', true);
            $weight = 0;

            if (!is_exist(['id' => $product_variant_id], "product_variants")) {
                $this->response['error'] = true;
                $this->response['message'] = 'Product Varient not available.';
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }


            $clear_cart = (isset($_POST['clear_cart']) && $_POST['clear_cart'] != "") ? $this->input->post('clear_cart', true) : 0;
            if ($clear_cart == true) {
                if (!$this->cart_model->remove_from_cart(['user_id' => $user_id])) {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Not able to remove existing seller items please try agian later.';
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }
            }

            if ($settings['is_single_seller_order'] == 1) {
                if (!is_single_seller($product_variant_id, $user_id)) {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Only single seller items are allow in cart.You can remove privious item(s) and add this item.';
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }
            }

            //check for digital or phisical product in cart
            if (!is_single_product_type($product_variant_id, $user_id)) {
                $this->response['error'] = true;
                $this->response['message'] = 'you can only add either digital product or physical product to cart';
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }

            $qty = $this->input->post('qty', true);
            $shipping_settings = get_settings('shipping_method', true);
            $settings = get_settings('system_settings', true);
            $cart_count = get_cart_count($user_id);
            $is_variant_available_in_cart = is_variant_available_in_cart($_POST['product_variant_id'], $user_id);
            $_POST['user_id'] = $user_id;
            if (!$is_variant_available_in_cart) {
                if ($cart_count[0]['total'] >= $settings['max_items_cart']) {

                    $this->response['error'] = true;
                    $this->response['message'] = 'Maximum ' . $settings['max_items_cart'] . ' Item(s) Can Be Added Only!';
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return;
                }
            }

            if (isset($_POST['buy_now']) && !empty($_POST['buy_now']) && $_POST['buy_now'] == 1) {

                $old_cart_data = get_cart_total($user_id);
                $total_old_cart = json_encode($old_cart_data['variant_id']);

                $this->cart_model->old_user_cart($user_id, $total_old_cart);
            }
            $saved_for_later = (isset($_POST['is_saved_for_later']) && $_POST['is_saved_for_later'] != "") ? $this->input->post('is_saved_for_later', true) : 0;
            $check_status = ($qty == 0 || $saved_for_later == 1) ? false : true;
            if (!$this->cart_model->add_to_cart($_POST, $check_status)) {
                $response = get_cart_total($user_id, false);
                $cart_user_data = $this->cart_model->get_user_cart($user_id, 0);
                $product_type = array_values(array_unique(array_column($cart_user_data, "type")));

                $tmp_cart_user_data = $cart_user_data;
                if (!empty($tmp_cart_user_data)) {
                    for ($i = 0; $i < count($tmp_cart_user_data); $i++) {
                        $weight += $tmp_cart_user_data[$i]['weight'] * $tmp_cart_user_data[$i]['qty'];
                        $product_data = fetch_details('product_variants', ['id' => $tmp_cart_user_data[$i]['product_variant_id']], 'product_id,availability');
                        if (!empty($product_data[0]['product_id'])) {
                            $pro_details = fetch_product($user_id, null, $product_data[0]['product_id']);
                            if (!empty($pro_details['product'])) {
                                if (trim($pro_details['product'][0]['availability']) == 0 && $pro_details['product'][0]['availability'] != null) {
                                    update_details(['is_saved_for_later' => '1'], $cart_user_data[$i]['id'], 'cart');
                                    unset($cart_user_data[$i]);
                                }

                                if (!empty($pro_details['product'])) {
                                    $cart_user_data[$i]['product_details'] = $pro_details['product'];
                                } else {
                                    delete_details(['id' => $cart_user_data[$i]['id']], 'cart');
                                    unset($cart_user_data[$i]);
                                    continue;
                                }
                            } else {
                                delete_details(['id' => $cart_user_data[$i]['id']], 'cart');
                                unset($cart_user_data[$i]);
                                continue;
                            }
                        } else {
                            delete_details(['id' => $cart_user_data[$i]['id']], 'cart');
                            unset($cart_user_data[$i]);
                            continue;
                        }
                    }
                }
                if (isset($_POST['address_id']) && !empty($_POST['address_id'])) {
                    $delivery_charge = get_delivery_charge($_POST['address_id'], $response['sub_total'], $user_id);
                }

                $this->response['error'] = false;
                $this->response['message'] = 'Cart Updated !';
                $this->response['cart'] = (isset($cart_user_data) && !empty($cart_user_data)) ? $cart_user_data : [];
                $this->response['data'] = [
                    'total_quantity' => ($_POST['qty'] == 0) ? '0' : strval($_POST['qty']),
                    'delivery_charge' => isset($delivery_charge) && !empty($delivery_charge) ? str_replace(",", "", $delivery_charge) : '0.0',
                    'sub_total' => strval($response['sub_total']),
                    'total_items' => (isset($response[0]['total_items'])) ? strval($response[0]['total_items']) : "0",
                    'tax_percentage' => (isset($response['tax_percentage'])) ? strval($response['tax_percentage']) : "0",
                    'tax_amount' => (isset($response['tax_amount'])) ? strval($response['tax_amount']) : "0",
                    'cart_count' => (isset($response[0]['cart_count'])) ? strval($response[0]['cart_count']) : "0",
                    'max_items_cart' => $settings['max_items_cart'],
                    'overall_amount' => $response['overall_amount'],
                ];
                print_r(json_encode($this->response));
                return;
            }
        }
    }

    public function clear_cart()
    {

        if (!$this->verify_token()) {
            return false;
        }
        $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
        delete_details(['user_id' => $user_id], 'cart');
        $this->response['error'] = false;
        $this->response['message'] = 'Data deleted successfully';
        print_r(json_encode($this->response));
        return;
    }

    //11.login

    public function login()
    {
        /* Parameters to be passed
            mobile: 9874565478
            
            password: 12345678
            fcm_id: FCM_ID
        */

        $identity_column = $this->config->item('identity', 'ion_auth');
        if ($identity_column == 'mobile') {
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim|numeric|required|xss_clean');
        } elseif ($identity_column == 'email') {
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');
        } else {
            $this->form_validation->set_rules('identity', 'Identity', 'trim|required|xss_clean');
        }
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
        $this->form_validation->set_rules('fcm_id', 'FCM ID', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        }

        $login = $this->ion_auth->login($this->input->post('mobile'), $this->input->post('password'), false, 'phone');
        $user_data = fetch_details('users', ['mobile' => $this->input->post('mobile', true)]);

        if ($user_data[0]['active'] == 7) {
            $response['error'] = true;
            $response['message'] = 'User Not Found';
            echo json_encode($response);
            return false;
        }

        if ($login) {
            if (!$this->ion_auth->is_admin() && !$this->ion_auth->is_seller() && !$this->ion_auth->is_delivery_boy()) {

                if (isset($_POST['fcm_id']) && !empty($_POST['fcm_id'])) {
                    update_details(['fcm_id' => $_POST['fcm_id']], ['mobile' => $_POST['mobile']], 'users');
                }

                $data = fetch_details('users', ['mobile' => $this->input->post('mobile', true)]);
                $existing_token = ($data[0]['apikey'] !== null && !empty($data[0]['apikey'])) ? $data[0]['apikey'] : "";
                unset($data[0]['password']);

                if ($existing_token == '') {
                    $token = generate_token($this->input->post('mobile'));
                    update_details(['apikey' => $token], ['mobile' => $this->input->post('mobile')], "users");
                } else if (!empty($existing_token)) {

                    $api_keys = JWT_SECRET_KEY;
                    try {
                        $get_token = $this->jwt->decode($existing_token, new Key($api_keys, 'HS256'));
                        $error = false;
                        $flag = false;
                    } catch (Exception $e) {
                        $token = generate_token($this->input->post('mobile'));
                        update_details(['apikey' => $token], ['mobile' => $this->input->post('mobile')], "users");
                        $error = true;
                        $flag = false;
                        $message = 'Token Expired, new token generated';
                        $status_code = 403;
                    }
                }

                foreach ($data as $row) {
                    $row = output_escaping($row);
                    $tempRow = [];

                    // Define keys to check
                    $keys = [
                        'id',
                        'ip_address',
                        'username',
                        'email',
                        'mobile',
                        'balance',
                        'activation_selector',
                        'activation_code',
                        'forgotten_password_selector',
                        'forgotten_password_code',
                        'forgotten_password_time',
                        'remember_selector',
                        'remember_code',
                        'created_on',
                        'last_login',
                        'active',
                        'company',
                        'address',
                        'bonus',
                        'cash_received',
                        'dob',
                        'country_code',
                        'city',
                        'area',
                        'street',
                        'pincode',
                        'apikey',
                        'referral_code',
                        'friends_code',
                        'fcm_id',
                        'latitude',
                        'longitude',
                        'created_at',
                        'type'
                    ];

                    foreach ($keys as $key) {
                        $tempRow[$key] = isset($row[$key]) && !empty($row[$key]) ? $row[$key] : '';
                    }

                    // Handle image URL
                    $tempRow['image'] = empty($row['image']) || !file_exists(FCPATH . USER_IMG_PATH . $row['image']) ? base_url() . NO_USER_IMAGE : base_url() . USER_IMG_PATH . $row['image'];

                    $rows[] = $tempRow;
                }

                //if the login is successful
                $response['error'] = false;
                $response['message'] = strip_tags($this->ion_auth->messages());
                $response['token'] = $existing_token !== "" ? $existing_token : $token;
                $response['data'] = $rows;
                echo json_encode($response);
                return false;
            } else {
                //if the login is not successful
                $response['error'] = true;
                $response['message'] = "Your mobile number or password is incorrect";
                $response['data'] = [];
                echo json_encode($response);
                return false;
            }
        } else {
            if (!is_exist(['mobile' => $_POST['mobile']], 'users')) {
                $response['error'] = true;
                $response['message'] = 'User does not exists !';
                echo json_encode($response);
                return false;
            }

            // if the login was un-successful
            // just print json message
            $response['error'] = true;
            $response['message'] = strip_tags($this->ion_auth->errors());
            echo json_encode($response);
            return false;
        }
    }

    public function update_fcm()
    {
        /* Parameters to be passed
            user_id:12
            fcm_id: FCM_ID
            device_type: android/ios
            is_logout: 0
        */

        if (!$this->verify_token()) {
            return false;
        }
        $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';

        $this->form_validation->set_rules('fcm_id', 'Fcm Id', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        }

        $is_logout = isset($_POST['is_logout']) && !empty($_POST['is_logout']) ? $_POST['is_logout'] : 0;

        if (isset($_POST['fcm_id']) && $_POST['fcm_id'] != NULL && !empty($_POST['fcm_id']) && $_POST['fcm_id'] != '-') {

            if ($is_logout == 1 || $is_logout == '1') {
                if (is_exist(['fcm_id' => $_POST['fcm_id']], 'user_fcm')) {
                    delete_details(['fcm_id' => $_POST['fcm_id']], 'user_fcm');
                }
            } else {
                if (isset($user_id) && $user_id != NULL) {
                    // delete_details(['fcm_id' => $_POST['fcm_id']], 'user_fcm');
                    $fcm_data = [
                        'fcm_id' => $_POST['fcm_id'],
                        'user_id' => $user_id,
                        'platform_type' => $_POST['device_type'],
                    ];

                    insert_details($fcm_data, 'user_fcm');
                    update_details(['platform_type' => $_POST['device_type']], ['id' => $user_id], 'users');
                    $user_res = update_details(['fcm_id' => $_POST['fcm_id']], ['id' => $user_id], 'users');
                } else {

                    $fcm_data = [
                        'fcm_id' => $_POST['fcm_id'],
                        'platform_type' => $_POST['device_type'],
                    ];

                    $user_res = insert_details($fcm_data, 'user_fcm');
                }
                if ($user_res) {
                    $response['error'] = false;
                    $response['message'] = 'Updated Successfully';
                    $response['data'] = array();
                    echo json_encode($response);
                    return false;
                } else {
                    $response['error'] = true;
                    $response['message'] = 'Updation Failed !';
                    $response['data'] = array();
                    echo json_encode($response);
                    return false;
                }
            }
        }
    }

    public function reset_password()
    {
        /* Parameters to be passed
            mobile_no:7894561235            
            new: pass@123
        */


        $this->form_validation->set_rules('mobile_no', 'Mobile No', 'trim|numeric|required|xss_clean|max_length[16]');
        $this->form_validation->set_rules('new', 'New Password', 'trim|required|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        }

        $identity_column = $this->config->item('identity', 'ion_auth');
        $res = fetch_details('users', ['mobile' => $_POST['mobile_no']]);
        if (!empty($res)) {
            $identity = ($identity_column == 'email') ? $res[0]['email'] : $res[0]['mobile'];
            if (!$this->ion_auth->reset_password($identity, $_POST['new'])) {
                $response['error'] = true;
                $response['message'] = strip_tags($this->ion_auth->messages());
                $response['data'] = array();
                echo json_encode($response);
                return false;
            } else {
                $response['error'] = false;
                $response['message'] = 'Password Reset Successfully';
                $response['data'] = array();
                echo json_encode($response);
                return false;
            }
        } else {
            $response['error'] = true;
            $response['message'] = 'User does not exists !';
            $response['data'] = array();
            echo json_encode($response);
            return false;
        }
    }

    //get_login_identity
    public function get_login_identity()
    {

        $response['error'] = false;
        $response['message'] = 'Data Retrieved Successfully';
        $response['data'] = array('identity' => $this->config->item('identity', 'ion_auth'));
        echo json_encode($response);
        return false;
    }

    //verify-user
    public function verify_user()
    {
        /* Parameters to be passed
            mobile: 9874565478
            email: test@gmail.com 
            is_forgot_password: 1
            country_code:91
        */


        $auth_settings = get_settings('authentication_settings', true);

        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('country_code', 'Country code', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'trim|xss_clean|valid_email');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $mobile = $this->input->post('mobile', true);

            $user_data = fetch_details('users', ['mobile' => $mobile], 'id');

            if (!$this->ion_auth->is_admin($user_data[0]['id']) && !$this->ion_auth->is_seller($user_data[0]['id']) && !$this->ion_auth->is_delivery_boy($user_data[0]['id'])) {

                if (isset($_POST['is_forgot_password']) && ($_POST['is_forgot_password'] == 1) && !is_exist(['mobile' => $_POST['mobile']], 'users')) {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Mobile is not register yet !';
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return;
                }
                if (isset($_POST['mobile']) && is_exist(['mobile' => $_POST['mobile']], 'users') && (($_POST['is_forgot_password'] == 0) && isset($_POST['is_forgot_password']))) {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Mobile is already registered.Please login again !';
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return;
                }

                if (isset($_POST['email']) && is_exist(['email' => $_POST['email']], 'users')) {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Email is already registered.Please login again !';
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return;
                }

                if ($auth_settings['authentication_method'] == "firebase") {
                    $this->response['error'] = false;
                    $this->response['message'] = 'Ready to sent OTP request';
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return;
                } else if ($auth_settings['authentication_method'] == "sms") {

                    $mobile = $_POST['mobile'];
                    $country_code = $_POST['country_code'];
                    $mobile_data = array(
                        'mobile' => $mobile // Replace $mobile with the actual mobile value you want to insert
                    );

                    if (isset($_POST['mobile']) && !is_exist(['mobile' => $_POST['mobile']], 'otps')) {
                        $this->db->insert('otps', $mobile_data);
                    }

                    $otps = fetch_details('otps', ['mobile' => $mobile]);

                    $query = $this->db->select(' * ')->where('id', $otps[0]['id'])->get('otps')->result_array();
                    $otp = random_int(100000, 999999);
                    $data = set_user_otp($mobile, $otp, $country_code);

                    $this->response['error'] = false;
                    $this->response['message'] = 'Ready to sent OTP request';
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return;
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = 'The admin has not enabled any authentication method.';
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return;
                }
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'You are not allowed to verify as a user!';
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return;
            }
        }
    }

    public function register_user()
    {


        $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', 'Mail', 'trim|required|xss_clean|valid_email');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|xss_clean|max_length[16]|numeric|is_unique[users.mobile]', array('is_unique' => ' The mobile number is already registered . Please login'));
        $this->form_validation->set_rules('country_code', 'Country Code', 'trim|required|xss_clean');
        $this->form_validation->set_rules('dob', 'Date of birth', 'trim|xss_clean');
        $this->form_validation->set_rules('city', 'City', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('area', 'Area', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('street', 'Street', 'trim|xss_clean');
        $this->form_validation->set_rules('pincode', 'Pincode', 'trim|xss_clean');
        $this->form_validation->set_rules('fcm_id', 'Fcm Id', 'trim|xss_clean');
        $this->form_validation->set_rules('referral_code', 'Referral code', 'trim|is_unique[users.referral_code]|xss_clean');
        $this->form_validation->set_rules('friends_code', 'Friends code', 'trim|xss_clean');
        $this->form_validation->set_rules('latitude', 'Latitude', 'trim|xss_clean');
        $this->form_validation->set_rules('longitude', 'Longitude', 'trim|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            if (isset($_POST['friends_code']) && !empty($_POST['friends_code'])) {
                $settings = get_settings('system_settings', true);
                $refer_earn_bonus_times = $settings['refer_earn_bonus_times'];
                $used_refer_and_earn = fetch_details('users', ['friends_code' => $_POST['friends_code']]);

                if (count($used_refer_and_earn) < $refer_earn_bonus_times) {

                    if (!is_exist(['referral_code' => $_POST['friends_code']], 'users')) {
                        $response["error"] = true;
                        $response["message"] = "Invalid friends code!";
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }
                } else {
                    $response["error"] = true;
                    $response["message"] = "Code already used for $refer_earn_bonus_times times!";
                    $response["data"] = array();
                    echo json_encode($response);
                    return false;
                }
            }

            $identity_column = $this->config->item('identity', 'ion_auth');
            $email = strtolower($this->input->post('email'));
            $mobile = $this->input->post('mobile');
            $identity = ($identity_column == 'mobile') ? $mobile : $email;
            $password = $this->input->post('password');
            $referral_generate_code = substr(str_shuffle(str_repeat("AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz1234567890", 8)), 0, 8);

            $additional_data = [
                'username' => $this->input->post('name'),
                'mobile' => $this->input->post('mobile'),
                'dob' => $this->input->post('dob'),
                'city' => $this->input->post('city'),
                'area' => $this->input->post('area'),
                'country_code' => $this->input->post('country_code'),
                'pincode' => $this->input->post('pincode'),
                'street' => $this->input->post('street'),
                'fcm_id' => $this->input->post('fcm_id'),
                'friends_code' => $this->input->post('friends_code', true),
                'referral_code' => $referral_generate_code,
                'latitude' => $this->input->post('latitude'),
                'longitude' => $this->input->post('longitude'),
                'active' => 1,
                'type' => 'phone',
            ];

            $res = $this->ion_auth->register($identity, $password, $email, $additional_data, ['2']);

            $token = generate_token($this->input->post('mobile'));
            update_details(['apikey' => $token], ['mobile' => $this->input->post('mobile')], "users");

            update_details(['active' => 1], [$identity_column => $identity], 'users');
            $data = $this->db->select('u.id,u.username,u.email,u.mobile,c.name as city_name,a.name as area_name')->where([$identity_column => $identity])->join('cities c', 'c.id=u.city', 'left')->join('areas a', 'a.city_id=c.id', 'left')->group_by('email')->get('users u')->result_array();
            $referal_id = fetch_details('users', ['referral_code' => $this->input->post('friends_code', true)], 'id,referral_code');

            $referal_code = $this->input->post('friends_code', true);
            if (isset($referal_code) && !empty($referal_code)) {
                $refer_and_earn_data = [
                    'referal_id' => $referal_id[0]['id'],
                    'user_id' => $data[0]['id'],
                    'identifier' => $data[0]['mobile'],
                    'referal_code' => $this->input->post('friends_code', true),
                ];
                insert_details($refer_and_earn_data, 'refer_and_earn');
            }
            foreach ($data as $row) {
                $row = output_escaping($row);
                $tempRow['id'] = (isset($row['id']) && !empty($row['id'])) ? $row['id'] : '';
                $tempRow['username'] = (isset($row['username']) && !empty($row['username'])) ? $row['username'] : '';
                $tempRow['email'] = (isset($row['email']) && !empty($row['email'])) ? $row['email'] : '';
                $tempRow['mobile'] = (isset($row['mobile']) && !empty($row['mobile'])) ? $row['mobile'] : '';
                $tempRow['city_name'] = (isset($row['city_name']) && !empty($row['city_name'])) ? $row['city_name'] : '';
                $tempRow['area_name'] = (isset($row['area_name']) && !empty($row['area_name'])) ? $row['area_name'] : '';

                $rows[] = $tempRow;
            }
            $this->response['error'] = false;
            $this->response['token'] = $token;
            $this->response['message'] = 'Registered Successfully';
            $this->response['data'] = $rows;
        }
        print_r(json_encode($this->response));
    }

    //verify_otp
    public function verify_otp()
    {
        /* 
        otp: 123456
        phone number: 9876543210
        */

        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|xss_clean|max_length[16]|numeric');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            $mobile = $this->input->post('mobile');
            $auth_settings = get_settings('authentication_settings', true);
            if ($auth_settings['authentication_method'] == "sms") {
                $otps = fetch_details('otps', ['mobile' => $mobile]);
                $time = $otps[0]['created_at'];
                $time_expire = checkOTPExpiration($time);
                if (isset($otps) && !empty($otps)) {
                    if ($time_expire['error'] == 1) {
                        $response['error'] = true;
                        $response['message'] = $time_expire['message'];
                        echo json_encode($response);
                        return false;
                    }
                    if (($otps[0]['otp'] != $_POST['otp'])) {
                        $response['error'] = true;
                        $response['message'] = "OTP not valid , check again ";
                        echo json_encode($response);
                        return false;
                    } else {
                        update_details(['varified' => 1], ['mobile' => $mobile], 'otps');
                        $this->response['error'] = false;
                        $this->response['message'] = 'Otp Verified Successfully';
                        $this->response['data'] = array();
                        print_r(json_encode($this->response));
                    }
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = 'OTP not valid , check again ';
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                }
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'The admin has not enabled any authentication method.';
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return;
            }
        }
        print_r(json_encode($this->response));
    }

    //resend_otp
    public function resend_otp()
    {
        /*
        mobile:9876543210
        */

        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|xss_clean|max_length[16]|numeric');
        $this->form_validation->set_rules('country_code', 'Country code', 'trim|required|xss_clean|max_length[16]|numeric');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $mobile = $this->input->post('mobile');
            $country_code = $this->input->post('country_code');
            $auth_settings = get_settings('authentication_settings', true);
            if ($auth_settings['authentication_method'] == "sms") {
                $otps = fetch_details('otps', ['mobile' => $mobile]);

                $query = $this->db->select(' * ')->where('id', $otps[0]['id'])->get('otps')->result_array();

                $otp = random_int(100000, 999999);
                $data = set_user_otp($mobile, $otp, $country_code);
                $this->response['error'] = false;
                $this->response['message'] = 'Ready to sent OTP request from sms!';
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return;
            }
        }
    }

    //update_user
    public function update_user()
    {
        /*
            user_id:34
            username:hiten{optional}
            dob:12/5/1982{optional}
            mobile:7852347890 {optional}
            email:amangoswami@gmail.com	{optional}
            address:Time Square	{optional}
            area:ravalwadi	{optional}
            city:23	{optional}
            pincode:56	    {optional}
            latitude:45.453	{optional}
            longitude:45.453	{optional}
            //file
            image:[]
            //optional parameters
            referral_code:Userscode
            old:12345
            new:345234
        */
        if (!$this->verify_token()) {
            return false;
        }
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            $this->response['error'] = true;
            $this->response['message'] = DEMO_VERSION_MSG;
            echo json_encode($this->response);
            return false;
            exit();
        }

        $identity_column = $this->config->item('identity', 'ion_auth');
        $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';


        $this->form_validation->set_rules('email', 'Email', 'xss_clean|trim|valid_email|edit_unique[users.id.' . $user_id . ']');
        $this->form_validation->set_rules('dob', 'DOB', 'xss_clean|trim');
        $this->form_validation->set_rules('city', 'City', 'xss_clean|numeric|trim');
        $this->form_validation->set_rules('area', 'Area', 'xss_clean|numeric|trim');
        $this->form_validation->set_rules('address', 'Address', 'xss_clean|trim');
        $this->form_validation->set_rules('pincode', 'Pincode', 'xss_clean|trim|numeric');
        $this->form_validation->set_rules('username', 'Username', 'xss_clean|trim');
        $this->form_validation->set_rules('latitude', 'Latitude', 'trim|xss_clean');
        $this->form_validation->set_rules('longitude', 'Longitude', 'trim|xss_clean');
        $this->form_validation->set_rules('referral_code', 'Referral code', 'trim|xss_clean');
        $this->form_validation->set_rules('image', 'Profile Image', 'trim|xss_clean');

        if (!empty($_POST['old']) || !empty($_POST['new'])) {
            $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
            $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[6]');
        }

        $tables = $this->config->item('tables', 'ion_auth');
        if (!$this->form_validation->run()) {
            if (validation_errors()) {
                $response['error'] = true;
                $response['message'] = strip_tags(validation_errors());
                echo json_encode($response);
                return false;
                exit();
            }
        } else {
            if (!empty($_POST['old']) || !empty($_POST['new'])) {
                $identity = ($identity_column == 'mobile') ? 'mobile' : 'email';
                $res = fetch_details('users', ['id' => $user_id], $identity);
                if (!empty($res)) {
                    if (!$this->ion_auth->change_password($res[0][$identity], $this->input->post('old'), $this->input->post('new'))) {
                        // if the login was un-successful
                        $response['error'] = true;
                        $response['message'] = strip_tags($this->ion_auth->errors());
                        echo json_encode($response);
                        return;
                    } else {
                        $user_details = fetch_details('users', ['id' => $user_id], "*");
                        if (empty($user_details[0]['image']) || file_exists(FCPATH . USER_IMG_PATH . $user_details[0]['image']) == FALSE) {
                            $user_details[0]['image'] = base_url() . NO_USER_IMAGE;
                        } else {
                            $user_details[0]['image'] = base_url() . USER_IMG_PATH . $user_details[0]['image'];
                        }
                        unset($user_details[0]['password']);
                        $user_details[0]['image_sm'] = get_image_url(base_url() . USER_IMG_PATH . $user_details[0]['image'], 'thumb', 'sm');
                        $response['error'] = false;
                        $response['message'] = 'Password Update Succesfully';
                        $response['data'] = $user_details;
                        echo json_encode($response);
                        return;
                    }
                } else {
                    $response['error'] = true;
                    $response['message'] = 'User not exists';
                    echo json_encode($response);
                    return;
                }
            }

            $is_updated = false;
            /* update referral_code if it is empty in user's database */
            if (isset($_POST['referral_code']) && !empty($_POST['referral_code'])) {
                $user = fetch_details('users', ['id' => $user_id], "referral_code");
                if (empty($user[0]['referral_code'])) {
                    update_details(['referral_code' => $_POST['referral_code']], ['id' => $user_id], "users");
                    $is_updated = true;
                }
            }

            if (!file_exists(FCPATH . USER_IMG_PATH)) {
                mkdir(FCPATH . USER_IMG_PATH);
            }

            $config = [
                'upload_path' => FCPATH . USER_IMG_PATH,
                'allowed_types' => 'jpeg|gif|jpg|png',
            ];

            $image_new_name = '';
            $image_info_error = '';

            if (!empty($_FILES['image']['name']) && isset($_FILES['image']['name'])) {
                $this->upload->initialize($config);
                if ($this->upload->do_upload('image')) {
                    $image_data = $this->upload->data();
                    $image_new_name = $image_data['file_name'];
                    resize_image($image_data, FCPATH . USER_IMG_PATH);
                } else {
                    $image_info_error = 'Profile Image :' . $this->upload->display_errors();
                }

                if ($image_info_error != NULL || !$this->form_validation->run()) {
                    if (isset($image_new_name) && $image_new_name != NULL) {
                        unlink(FCPATH . USER_IMG_PATH . $image_new_name);
                    }
                }
            }

            if (isset($image_info_error) && !empty($image_info_error)) {
                $response['error'] = true;
                $response['message'] = $image_info_error;
                echo json_encode($response);
                return;
            }
            $allowedFields = ['username', 'email', 'dob', 'mobile', 'address', 'city', 'area', 'pincode', 'latitude', 'longitude'];

            $set = [];

            foreach ($allowedFields as $field) {
                if (isset($_POST[$field]) && !empty($_POST[$field])) {
                    $set[$field] = $this->input->post($field, true);
                }
            }

            if (!empty($_FILES['image']['name'])) {
                $set['image'] = $image_new_name;
            }

            if (!empty($set)) {
                $set = escape_array($set);
                $this->db->set($set)->where('id', $user_id)->update($tables['login_users']);
                $user_details = fetch_details('users', ['id' => $user_id], "*");

                $imageBaseUrl = base_url() . USER_IMG_PATH;

                foreach ($user_details as $row) {
                    $tempRow = [];
                    $row = output_escaping($row);

                    // Define a function for isset and not empty check
                    $check = function ($key) use ($row) {
                        return isset($row[$key]) && $row[$key] !== '';
                    };

                    // Iterate through each key in the row
                    $keys = ['id', 'ip_address', 'username', 'password', 'email', 'mobile', 'balance', 'activation_selector', 'activation_code', 'forgotten_password_selector', 'forgotten_password_code', 'forgotten_password_time', 'remember_selector', 'remember_code', 'created_on', 'last_login', 'active', 'company', 'address', 'bonus', 'cash_received', 'dob', 'country_code', 'city', 'area', 'street', 'pincode', 'serviceable_zipcodes', 'apikey', 'referral_code', 'friends_code', 'fcm_id', 'latitude', 'longitude', 'created_at'];

                    foreach ($keys as $key) {
                        $tempRow[$key] = $check($key) ? $row[$key] : '';
                    }

                    // Handle image URL
                    $tempRow['image'] = !empty($row['image']) && file_exists(FCPATH . USER_IMG_PATH . $row['image']) ? $imageBaseUrl . $row['image'] : base_url() . NO_USER_IMAGE;
                    $tempRow['image_sm'] = get_image_url($imageBaseUrl . $row[0][0]['image'], 'thumb', 'sm');

                    // Push the row to the rows array
                    $rows[] = $tempRow;
                }


                $response['error'] = false;
                $response['message'] = 'Profile Update Succesfully';
                $response['data'] = $rows;
                echo json_encode($response);
                return;
            } else if ($is_updated == true) {
                $user_details = fetch_details('users', ['id' => $user_id], "*");
                $imageBaseUrl = base_url() . USER_IMG_PATH;

                foreach ($user_details as $row) {
                    $row = output_escaping($row);
                    $tempRow = [];

                    // Define a function for isset and not empty check
                    $check = function ($key) use ($row) {
                        return isset($row[$key]) && $row[$key] !== '';
                    };

                    // Iterate through each key in the row
                    $keys = ['id', 'ip_address', 'username', 'password', 'email', 'mobile', 'balance', 'activation_selector', 'activation_code', 'forgotten_password_selector', 'forgotten_password_code', 'forgotten_password_time', 'remember_selector', 'remember_code', 'created_on', 'last_login', 'active', 'company', 'address', 'bonus', 'cash_received', 'dob', 'country_code', 'city', 'area', 'street', 'pincode', 'serviceable_zipcodes', 'apikey', 'referral_code', 'friends_code', 'fcm_id', 'latitude', 'longitude', 'created_at'];

                    foreach ($keys as $key) {
                        $tempRow[$key] = $check($key) ? $row[$key] : '';
                    }

                    // Handle image URL
                    $tempRow['image'] = empty($row[0]['image']) || !file_exists(FCPATH . USER_IMG_PATH . $row[0]['image']) ? base_url() . NO_USER_IMAGE : $imageBaseUrl . $row[0]['image'];
                    $tempRow['image_sm'] = get_image_url($imageBaseUrl . $row[0][0]['image'], 'thumb', 'sm');

                    // Push the row to the rows array
                    $rows[] = $tempRow;
                }


                $response['error'] = false;
                $response['message'] = 'Referel Code Update Succesfully';
                $response['data'] = $rows;
                echo json_encode($response);
                return;
            }
        }
    }

    //delete_user

    public function delete_user()
    {
        /*
            user_id:15
            mobile:9874563214
            password:12345695
        */
        if (!$this->verify_token()) {
            return false;
        }


        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            echo json_encode($this->response);
            return false;
        } else {
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';

            $user_data = fetch_details('users', ['id' => $user_id, 'mobile' => $_POST['mobile']], 'id,username,password,active,mobile');
            if ($user_data) {
                $login = $this->ion_auth->login($this->input->post('mobile'), $this->input->post('password'), false);
                if ($login) {
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
                    } else if ($user_group[0]['group_id'] == '4') {
                        $login = $this->ion_auth->login($this->input->post('mobile'), $this->input->post('password'), false);
                        if ($login) {
                            $delete = array(
                                "media" => 0,
                                "payment_requests" => 0,
                                "products" => 0,
                                "product_attributes" => 0,
                                "order_items" => 0,
                                "orders" => 0,
                                "order_bank_transfer" => 0,
                                "seller_commission" => 0,
                                "seller_data" => 0,
                            );
                            $seller_media = fetch_details('seller_data', ['user_id' => $user_id], 'id,logo,national_identity_card,address_proof');
                            if (!empty($seller_media)) {
                                (unlink(FCPATH . $seller_media[0]['logo']) != null) && !empty(unlink(FCPATH . $seller_media[0]['logo'])) ? unlink(FCPATH . $seller_media[0]['logo']) : "";
                                (unlink(FCPATH . $seller_media[0]['national_identity_card']) != null) && !empty(unlink(FCPATH . $seller_media[0]['national_identity_card'])) ? unlink(FCPATH . $seller_media[0]['national_identity_card']) : "";
                                (unlink(FCPATH . $seller_media[0]['address_proof']) != null) && !empty(unlink(FCPATH . $seller_media[0]['address_proof'])) ? unlink(unlink(FCPATH . $seller_media[0]['address_proof'])) : "";
                            }
                            if (update_details(['seller_id' => 0], ['seller_id' => $user_id], 'media')) {
                                $delete['media'] = 1;
                            }
                            /* check for retur requesst if seller's product have */
                            $return_req = $this->db->where(['p.seller_id' => $user_id])->join('products p', 'p.id=rr.product_id')->get('return_requests rr')->result_array();
                            if (!empty($return_req)) {
                                $this->response['error'] = true;
                                $this->response['message'] = 'Seller could not be deleted.Either found some order items which has return request.Finalize those before deleting it';
                                print_r(json_encode($this->response));
                                return;
                                exit();
                            }
                            $pr_ids = fetch_details("products", ['seller_id' => $user_id], "id");
                            if (delete_details(['seller_id' => $user_id], 'products')) {
                                $delete['products'] = 1;
                            }
                            foreach ($pr_ids as $row) {
                                if (delete_details(['product_id' => $row['id']], 'product_attributes')) {
                                    $delete['product_attributes'] = 1;
                                }
                            }
                            /* check order items */
                            $order_items = fetch_details('order_items', ['seller_id' => $user_id], 'id,order_id');
                            if (delete_details(['seller_id' => $user_id], 'order_items')) {
                                $delete['order_items'] = 1;
                            }
                            if (!empty($order_items)) {
                                $res_order_id = array_values(array_unique(array_column($order_items, "order_id")));
                                for ($i = 0; $i < count($res_order_id); $i++) {
                                    $orders = $this->db->where('oi.seller_id != ' . $user_id . ' and oi.order_id=' . $res_order_id[$i])->join('orders o', 'o.id=oi.order_id', 'right')->get('order_items oi')->result_array();
                                    if (empty($orders)) {
                                        // delete orders
                                        if (delete_details(['seller_id' => $user_id], 'order_items')) {
                                            $delete['order_items'] = 1;
                                        }
                                        if (delete_details(['id' => $res_order_id[$i]], 'orders')) {
                                            $delete['orders'] = 1;
                                        }
                                        if (delete_details(['order_id' => $res_order_id[$i]], 'order_bank_transfer')) {
                                            $delete['order_bank_transfer'] = 1;
                                        }
                                    }
                                }
                            } else {
                                $delete['order_items'] = 1;
                                $delete['orders'] = 1;
                                $delete['order_bank_transfer'] = 1;
                            }
                            if (!empty($res_order_id)) {

                                if (delete_details(['id' => $res_order_id[$i]], 'orders')) {
                                    $delete['orders'] = 1;
                                }
                            } else {
                                $delete['orders'] = 1;
                            }
                            if (delete_details(['seller_id' => $user_id], 'seller_commission')) {
                                $delete['seller_commission'] = 1;
                            }
                            if (delete_details(['user_id' => $user_id], 'seller_data')) {
                                $delete['seller_data'] = 1;
                            }
                            if (isset($delete['seller_data']) && !empty($delete['seller_data']) && isset($delete['seller_commission']) && !empty($delete['seller_commission'])) {
                                $deleted = TRUE;
                            }
                        }
                        delete_details(['id' => $user_id], 'users');
                        delete_details(['user_id' => $user_id], 'users_groups');
                        $response['error'] = false;
                        $response['message'] = 'Seller Deleted Succesfully';
                    } else if ($user_group[0]['group_id'] == '3') {
                        delete_details(['id' => $user_id], 'users');
                        delete_details(['user_id' => $user_id], 'users_groups');
                        $response['error'] = false;
                        $response['message'] = 'Delivery Boy  Deleted Succesfully';
                    } else {
                        $response['error'] = true;
                        $response['message'] = 'Details Does\'s Match';
                    }
                } else {
                    $response['error'] = true;
                    $response['message'] = 'Details Does\'s Match';
                }
            } else {
                $response['error'] = true;
                $response['message'] = 'User Not Found';
            }
            echo json_encode($response);
            return;
        }
    }

    //12. add_to_favorites
    public function add_to_favorites()
    {
        /*
            user_id:15
            product_id:60
        */
        if (!$this->verify_token()) {
            return false;
        }


        $this->form_validation->set_rules('product_id', 'Product Id', 'trim|numeric|required|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';

            if (is_exist(['user_id' => $user_id, 'product_id' => $_POST['product_id']], 'favorites')) {
                $response["error"] = true;
                $response["message"] = "Already added to favorite !";
                $response["data"] = array();
                echo json_encode($response);
                return false;
            }

            $data = [
                'user_id' => $user_id,
                'product_id' => $_POST['product_id'],
            ];
            $data = escape_array($data);
            $this->db->insert('favorites', $data);
            $this->response['error'] = false;
            $this->response['message'] = 'Added to favorite';
            $this->response['data'] = array();
        }
        print_r(json_encode($this->response));
    }

    //remove_from_favorites
    public function remove_from_favorites()
    {
        /*
         user_id:12
         product_id:23 {optional}
        */

        $this->form_validation->set_rules('product_id', 'Product Id', 'trim|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $data = ['user_id' => $user_id];
            if (isset($_POST['product_id'])) {
                $data['product_id'] = $_POST['product_id'];
                if (!is_exist(['user_id' => $user_id, 'product_id' => $_POST['product_id']], 'favorites')) {
                    $response["error"] = true;
                    $response["message"] = "Item not added as favorite !";
                    $response["data"] = array();
                    echo json_encode($response);
                    return false;
                }
            }

            $this->db->delete('favorites', $data);
            $this->response['error'] = false;
            $this->response['message'] = 'Removed from favorite';
            $this->response['data'] = array();
        }
        print_r(json_encode($this->response));
    }

    //get_favorites
    public function get_favorites()
    {
        /*
         user_id:12
         limit : 10 {optional}
         offset: 0 {optional}
        */
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';

            $limit = (isset($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
            $offset = (isset($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;

            $q = $this->db->join('products p', 'p.id=f.product_id')
                ->join('product_variants pv', 'pv.product_id=p.id')
                ->where('f.user_id', $user_id)
                ->where('p.status', 1)
                ->select('(select count(id) from favorites where user_id=' . $user_id . ') as total, ,f.*')
                ->group_by('f.product_id')
                ->limit($limit, $offset)
                ->get('favorites f');
            $total = 0;
            $res1 = array();
            $res = $q->result_array();
            if (!empty($res)) {
                $total = $res[0]['total'];
                for ($i = 0; $i < count($res); $i++) {
                    unset($res[$i]['total']);
                    $pro_details = fetch_product($user_id, null, (isset($res[$i]['product_id'])) ? $res[$i]['product_id'] : null);
                    if (!empty($pro_details)) {
                        $res1[] = $pro_details['product'][0];
                    }
                }
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'No Favourite(s) Product Are Added';
                $this->response['total'] = [];
                $this->response['data'] = [];
                print_r(json_encode($this->response));
                return;
            }
            $this->response['error'] = false;
            $this->response['message'] = 'Data Retrieved Successfully';
            $this->response['total'] = $total;
            $this->response['data'] = $res1;
        }
        print_r(json_encode($this->response));
    }


    //13. add_address
    public function add_address()
    {
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('type', 'Type', 'trim|xss_clean');
        $this->form_validation->set_rules('country_code', 'Country Code', 'trim|xss_clean');
        $this->form_validation->set_rules('name', 'Name', 'trim|xss_clean');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('alternate_mobile', 'Alternative Mobile', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('address', 'Address', 'trim|xss_clean');
        $this->form_validation->set_rules('landmark', 'Landmark', 'trim|xss_clean');
        $this->form_validation->set_rules('general_area_name', 'Area', 'trim|xss_clean');
        $this->form_validation->set_rules('city_id', 'City', 'trim|xss_clean');
        $this->form_validation->set_rules('city_name', 'City', 'trim|xss_clean');
        $this->form_validation->set_rules('area_name', 'Area', 'trim|xss_clean');
        $this->form_validation->set_rules('pincode_name', 'Pincode', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('pincode', 'Pincode', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('state', 'State', 'trim|xss_clean');
        $this->form_validation->set_rules('country', 'Country', 'trim|xss_clean');
        $this->form_validation->set_rules('latitude', 'Latitude', 'trim|xss_clean');
        $this->form_validation->set_rules('longitude', 'Longitude', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $_POST['user_id'] = $user_id;
            $this->address_model->set_address($_POST);
            $res = $this->address_model->get_address($user_id, false, true);
            $this->response['error'] = false;
            $this->response['message'] = 'Address Added Successfully';
            $this->response['data'] = $res;
        }
        print_r(json_encode($this->response));
    }

    //update_address
    public function update_address()
    {
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('id', 'Id', 'trim|required|numeric|xss_clean');
        $this->form_validation->set_rules('type', 'Type', 'trim|xss_clean');
        $this->form_validation->set_rules('country_code', 'Country Code', 'trim|xss_clean');
        $this->form_validation->set_rules('name', 'Name', 'trim|xss_clean');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('alternate_mobile', 'Alternative Mobile', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('address', 'Address', 'trim|xss_clean');
        $this->form_validation->set_rules('landmark', 'Landmark', 'trim|xss_clean');
        $this->form_validation->set_rules('general_area_name', 'Area', 'trim|xss_clean');
        $this->form_validation->set_rules('city_id', 'City', 'trim|xss_clean');
        $this->form_validation->set_rules('city_name', 'City', 'trim|xss_clean');
        $this->form_validation->set_rules('area_name', 'Area', 'trim|xss_clean');
        $this->form_validation->set_rules('pincode', 'Pincode', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('state', 'State', 'trim|xss_clean');
        $this->form_validation->set_rules('country', 'Country', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $this->address_model->set_address($_POST);
            $res = $this->address_model->get_address(null, $_POST['id'], true);
            $this->response['error'] = false;
            $this->response['message'] = 'Address updated Successfully';
            $this->response['data'] = $res;
        }
        print_r(json_encode($this->response));
    }

    //delete_address
    public function delete_address()
    {
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('id', 'Id', 'trim|required|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $this->address_model->delete_address($_POST);
            $this->response['error'] = false;
            $this->response['message'] = 'Address Deleted Successfully';
            $this->response['data'] = array();
        }
        print_r(json_encode($this->response));
    }

    //get_address
    public function get_address()
    {
        /*
            user_id:3    
        */
        if (!$this->verify_token()) {
            return false;
        }
        $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';

        $res = $this->address_model->get_address_for_api($user_id);
        $is_default_counter = array_count_values(array_column($res, 'is_default'));

        if (!isset($is_default_counter['1']) && !empty($res)) {
            update_details(['is_default' => '1'], ['id' => $res[0]['id']], 'addresses');
            $res = $this->address_model->get_address_for_api($user_id);
        }
        if (!empty($res)) {
            $this->response['error'] = false;
            $this->response['message'] = 'Address Retrieved Successfully';
            $this->response['data'] = $res;
        } else {
            $this->response['error'] = true;
            $this->response['message'] = "No Details Found !";
            $this->response['data'] = array();
        }

        print_r(json_encode($this->response));
    }

    //13 get_sections
    public function get_sections()
    {
        /*
                limit:10            // { default - 25 } {optional}
                offset:0            // { default - 0 } {optional}
                user_id:12              {optional}
                section_id:4            {optional}
                attribute_value_ids : 34,23,12 // 
                top_rated_product: 1 // { default - 0 } optional
                p_limit:10          // { default - 10 } {optional}
                p_offset:10         // { default - 0 } {optional}    
                p_sort:pv.price      // { default - pid } {optional}
                p_order:asc         // { default - desc } {optional}
                discount: 5 // { default - 5 } optional
                min_price:10000          // optional
                max_price:50000          // optional
                zipcode:1          // optional

            */

        $this->form_validation->set_rules('limit', 'Limit', 'trim|xss_clean');
        $this->form_validation->set_rules('offset', 'Offset', 'trim|xss_clean');
        $this->form_validation->set_rules('user_id', 'User Id', 'trim|xss_clean');
        $this->form_validation->set_rules('section_id', 'Section Id', 'trim|xss_clean');
        $this->form_validation->set_rules('section_slug', 'Section Slug', 'trim|xss_clean');
        $this->form_validation->set_rules('p_limit', 'Product Limit', 'trim|xss_clean');
        $this->form_validation->set_rules('p_offset', 'Product Offset', 'trim|xss_clean');
        $this->form_validation->set_rules('p_sort', 'Product Sort', 'trim|xss_clean');
        $this->form_validation->set_rules('p_order', 'Product Order', 'trim|xss_clean');
        $this->form_validation->set_rules('discount', ' Discount ', 'trim|xss_clean|numeric');
        $this->form_validation->set_rules('zipcode', ' Zipcode ', 'trim|xss_clean');
        $this->form_validation->set_rules('min_price', ' Min Price ', 'trim|xss_clean|numeric|less_than_equal_to[' . $this->input->post('max_price') . ']');
        $this->form_validation->set_rules('max_price', ' Max Price ', 'trim|xss_clean|numeric|greater_than_equal_to[' . $this->input->post('min_price') . ']');


        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        }

        $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
        $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
        $user_id = (isset($_POST['user_id']) && !empty(trim($_POST['user_id']))) ? $this->input->post('user_id', true) : 0;
        $section_id = (isset($_POST['section_id']) && !empty(trim($_POST['section_id']))) ? $this->input->post('section_id', true) : 0;
        $section_slug = (isset($_POST['section_slug']) && !empty(trim($_POST['section_slug']))) ? $this->input->post('section_slug', true) : 0;
        $filters['attribute_value_ids'] = (isset($_POST['attribute_value_ids'])) ? $_POST['attribute_value_ids'] : null;
        $filters['product_type'] = (isset($_POST['top_rated_product']) && $_POST['top_rated_product'] == 1) ? 'top_rated_product_including_all_products' : null;
        $p_limit = (isset($_POST['p_limit']) && !empty(trim($_POST['p_limit']))) ? $this->input->post('p_limit', true) : 10;
        $p_offset = (isset($_POST['p_offset']) && !empty(trim($_POST['p_offset']))) ? $this->input->post('p_offset', true) : 0;
        $p_order = (isset($_POST['p_order']) && !empty(trim($_POST['p_order']))) ? $_POST['p_order'] : 'DESC';
        $p_sort = (isset($_POST['p_sort']) && !empty(trim($_POST['p_sort']))) ? $_POST['p_sort'] : 'p.id';
        $filters['discount'] = (isset($_POST['discount'])) ? $this->input->post("discount", true) : 0;
        $filters['min_price'] = (isset($_POST['min_price']) && !empty($_POST['min_price'])) ? $this->input->post("min_price", true) : 0;
        $filters['max_price'] = (isset($_POST['max_price']) && !empty($_POST['max_price'])) ? $this->input->post("max_price", true) : 0;
        $zipcode = (isset($_POST['zipcode']) && !empty($_POST['zipcode'])) ? $this->input->post("zipcode", true) : 0;
        if (isset($_POST['zipcode']) && !empty($_POST['zipcode'])) {
            $zipcode = $this->input->post('zipcode', true);
            $is_pincode = is_exist(['zipcode' => $zipcode], 'zipcodes');
            if ($is_pincode) {
                $zipcode_id = fetch_details('zipcodes', ['zipcode' => $zipcode], 'id');
                $zipcode = $zipcode_id[0]['id'];
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Products Not Found !';
                echo json_encode($this->response);
                return false;
            }
        }
        if ($p_sort == 'pv.special_price') {
            $p_sort = "special_price";
        }
        $this->db->select('*');
        if (isset($_POST['section_id']) && !empty($_POST['section_id'])) {
            $this->db->where('id', $section_id);
        }
        if (isset($_POST['section_slug']) && !empty($_POST['section_slug'])) {
            $this->db->where('slug', $section_slug);
        }
        $this->db->limit($limit, $offset);
        $sections = $this->db->order_by('row_order')->get('sections')->result_array();

        if (!empty($sections)) {
            for ($i = 0; $i < count($sections); $i++) {
                $product_ids = explode(',', $sections[$i]['product_ids']);
                $product_ids = array_filter($product_ids);
                $filters['show_only_active_products'] = 1;
                if (isset($_POST['top_rated_product']) && !empty($_POST['top_rated_product'])) {
                    $filters['product_type'] = (isset($_POST['top_rated_product']) && $_POST['top_rated_product'] == 1) ? 'top_rated_product_including_all_products' : null;
                } else {
                    if (isset($sections[$i]['product_type']) && !empty($sections[$i]['product_type'])) {
                        $filters['product_type'] = (isset($sections[$i]['product_type'])) ? $sections[$i]['product_type'] : null;
                    }
                }
                $categories = (isset($sections[$i]['categories']) && !empty($sections[$i]['categories']) && $sections[$i]['categories'] != NULL) ? explode(',', $sections[$i]['categories']) : null;

                $products = fetch_product($user_id, (isset($filters)) ? $filters : null, (isset($product_ids) && !empty($product_ids)) ? $product_ids : null, $categories, $p_limit, $p_offset, $p_sort, $p_order, NULL, $zipcode, NULL);

                if (!empty($products['product'])) {
                    $this->response['error'] = false;
                    $this->response['message'] = "Sections retrived successfully";
                    $this->response['min_price'] = (isset($products['min_price']) && !empty($products['min_price'])) ? strval($products['min_price']) : '0';
                    $this->response['max_price'] = (isset($products['max_price']) && !empty($products['max_price'])) ? strval($products['max_price']) : '0';
                    $sections[$i]['title'] = output_escaping($sections[$i]['title']);
                    $sections[$i]['short_description'] = output_escaping($sections[$i]['short_description']);
                    $sections[$i]['total'] = strval($products['total']);
                    $sections[$i]['slug'] = url_title($sections[$i]['title'], 'dash', true);
                    $sections[$i]['filters'] = (isset($products['filters'])) ? $products['filters'] : [];
                    $sections[$i]['product_details'] = $products['product'];
                    $sections[$i]['product_ids'] = (isset($sections[$i]['product_ids']) && !empty($sections[$i]['product_ids'])) ? $sections[$i]['product_ids'] : '';
                    $sections[$i]['categories'] = (isset($sections[$i]['categories']) && !empty($sections[$i]['categories'])) ? $sections[$i]['categories'] : '';
                    unset($sections[$i]['product_details'][0]['total']);
                } else {
                    $this->response['error'] = false;
                    $this->response['message'] = "Sections retrived successfully";
                    $sections[$i]['total'] = "0";
                    $this->response['min_price'] = (isset($products['min_price']) && !empty($products['min_price'])) ? strval($products['min_price']) : '0';
                    $this->response['max_price'] = (isset($products['max_price']) && !empty($products['max_price'])) ? strval($products['max_price']) : '0';
                    $sections[$i]['product_ids'] = (isset($sections[$i]['product_ids']) && !empty($sections[$i]['product_ids'])) ? $sections[$i]['product_ids'] : '';
                    $sections[$i]['slug'] = url_title($sections[$i]['title'], 'dash', true);
                    $sections[$i]['filters'] = [];
                    $sections[$i]['product_details'] = [];
                }
            }
            $this->response['data'] = $sections;
        } else {
            $this->response['error'] = true;
            $this->response['message'] = "No sections are available";
            $this->response['data'] = array();
        }
        print_r(json_encode($this->response));
    }

    //get_notifications()
    public function get_notifications()
    {
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';

            // print_r($user_id);
            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'DESC';
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'id';
            $res = $this->notification_model->get_notifications($offset, $limit, $sort, $order, $user_id);
            $this->response['error'] = false;
            $this->response['message'] = 'Notification Retrieved Successfully';
            $this->response['total'] = $res['total'];
            $this->response['data'] = $res['data'];
        }
        print_r(json_encode($this->response));
    }

    public function get_paypal_link()
    {
        /*
            user_id : 2
            order_id : 1
            amount : 150
        */

        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('order_id', 'Order ID', 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';

            $order_id = $_POST['order_id'];
            $amount = $_POST['amount'];
            if (!is_numeric($order_id)) {
                $this->response['error'] = false;
                $this->response['message'] = 'Order created for wallet!';
                $this->response['data'] = base_url('app/v1/api/paypal_transaction_webview?' . 'user_id=' . $user_id . '&order_id=' . $order_id . '&amount=' . $amount);
                print_r(json_encode($this->response));
                return false;
            }
            $this->response['error'] = false;
            $this->response['message'] = 'Order Detail Founded !';
            $this->response['data'] = base_url('app/v1/api/paypal_transaction_webview?' . 'user_id=' . $user_id . '&order_id=' . $order_id . '&amount=' . $amount);
        }
        print_r(json_encode($this->response));
    }

    //paypal_transaction_webview()
    public function paypal_transaction_webview()
    {
        /*
            user_id : 2
            order_id : 1
            amount : 1000
        */

        header("Content-Type: html");

        $this->form_validation->set_data($_GET);

        $this->form_validation->set_rules('user_id', 'User ID', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('order_id', 'Order ID', 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        }

        $user_id = $_GET['user_id'];
        $order_id = $_GET['order_id'];
        $amount = $_GET['amount'];

        $q = $this->db->where('id', $user_id)->get('users')->result_array();
        if (empty($q) && !isset($q)) {
            echo "user error update";
            return false;
        }

        $order_res = $this->db->where('id', $order_id)->get('orders')->result_array();
        if (!empty($order_res)) {

            $data['user'] = $q[0];
            $data['order'] = $order_res[0];
            $data['payment_type'] = "paypal";
            // Set variables for paypal form
            $returnURL = base_url() . 'app/v1/api/app_payment_status';
            $cancelURL = base_url() . 'app/v1/api/app_payment_status';
            $notifyURL = base_url() . 'app/v1/api/ipn';
            $txn_id = time() . "-" . rand();
            // Get current user ID from the session
            $userID = $data['user']['id'];
            $order_id = $data['order']['id'];
            $payeremail = $data['user']['email'];
            // Add fields to paypal form
            $this->paypal_lib->add_field('return', $returnURL);
            $this->paypal_lib->add_field('cancel_return', $cancelURL);
            $this->paypal_lib->add_field('notify_url', $notifyURL);
            $this->paypal_lib->add_field('item_name', 'Test');
            $this->paypal_lib->add_field('custom', $userID . '|' . $payeremail);
            $this->paypal_lib->add_field('item_number', $order_id);
            $this->paypal_lib->add_field('amount', $amount);
            // Render paypal form
            $this->paypal_lib->paypal_auto_form();
        } else {
            $data['user'] = $q[0];
            $data['payment_type'] = "paypal";
            // Set variables for paypal form
            $returnURL = base_url() . 'app/v1/api/app_payment_status';
            $cancelURL = base_url() . 'app/v1/api/app_payment_status';
            $notifyURL = base_url() . 'app/v1/api/ipn';
            $txn_id = time() . "-" . rand();
            // Get current user ID from the session
            $userID = $data['user']['id'];
            $order_id = $order_id;
            $payeremail = $data['user']['email'];

            $this->paypal_lib->add_field('return', $returnURL);
            $this->paypal_lib->add_field('cancel_return', $cancelURL);
            $this->paypal_lib->add_field('notify_url', $notifyURL);
            $this->paypal_lib->add_field('item_name', 'Online shopping');
            $this->paypal_lib->add_field('custom', $userID . '|' . $payeremail);
            $this->paypal_lib->add_field('item_number', $order_id);
            $this->paypal_lib->add_field('amount', $amount);
            // Render paypal form
            $this->paypal_lib->paypal_auto_form();
        }
    }

    public function app_payment_status()
    {
        // $paypalInfo = $this->input->get();
        $paypalInfo = $this->input->post();
        log_message('error', 'Paypal api info | INFORMATION --> ' . var_export($paypalInfo, true));

        if (!empty($paypalInfo) && isset($_GET['st']) && strtolower($_GET['st']) == "completed") {
            $response['error'] = false;
            $response['message'] = "Payment Completed Successfully";
            $response['data'] = $paypalInfo;
        } elseif (!empty($paypalInfo) && isset($_GET['st']) && strtolower($_GET['st']) == "authorized") {
            $response['error'] = false;
            $response['message'] = "Your payment is has been Authorized successfully. We will capture your transaction within 30 minutes, once we process your order. After successful capture coins wil be credited automatically.";
            $response['data'] = $paypalInfo;
        } elseif (!empty($paypalInfo) && isset($_GET['st']) && strtolower($_GET['st']) == "Pending") {
            $response['error'] = false;
            $response['message'] = "Your payment is pending and is under process. We will notify you once the status is updated.";
            $response['data'] = $paypalInfo;
        } else {
            $response['error'] = true;
            $response['message'] = "Payment Cancelled / Declined ";
            $response['data'] = (isset($_GET)) ? $this->input->get() : "";
        }
        print_r(json_encode($response));
    }

    public function ipn()
    {
        // Paypal posts the transaction data
        $paypalInfo = $this->input->post();
        if (!empty($paypalInfo)) {
            // Validate and get the ipn response
            $ipnCheck = $this->paypal_lib->validate_ipn($paypalInfo);

            // Check whether the transaction is valid
            if ($ipnCheck) {

                $order_id = $paypalInfo["item_number"];
                /* if its not numeric then it is for the wallet recharge */
                if (
                    $paypalInfo["payment_status"] == 'Completed' &&
                    !is_numeric($order_id) && strpos($order_id, "wallet-refill-user") !== false
                ) {
                    $temp = explode("-", $order_id); /* Order ID format for wallet refill >> wallet-refill-user-{user_id}-{system_time}-{3 random_number}  */
                    if (isset($temp[3]) && is_numeric($temp[3]) && !empty($temp[3] && $temp[3] != '')) {
                        $user_id = $temp[3];
                    } else {
                        $user_id = 0;
                    }
                    $amount = $paypalInfo["mc_gross"];
                    /* IPN for user wallet recharge */
                    $data['transaction_type'] = "wallet";
                    $data['user_id'] = $user_id;
                    $data['order_id'] = $order_id;
                    $data['type'] = "credit";
                    $data['txn_id'] = $paypalInfo["txn_id"];
                    $data['amount'] = $amount;
                    $data['status'] = "success";
                    $data['message'] = "Wallet refill successful";
                    $this->transaction_model->add_transaction($data);

                    $this->load->model('customer_model');
                    if ($this->customer_model->update_balance($amount, $user_id, 'add')) {
                        $response['error'] = false;
                        $response['transaction_status'] = "success";
                        $response['message'] = "Wallet recharged successfully!";
                    } else {
                        $response['error'] = true;
                        $response['transaction_status'] = "success";
                        $response['message'] = "Wallet could not be recharged!";
                        log_message('error', 'Paypal IPN | wallet recharge failure --> ' . var_export($paypalInfo, true));
                    }
                    echo json_encode($response);
                    return false;
                } else {
                    /* IPN for normal Order  */
                    // Insert the transaction data in the database
                    $userData = explode('|', $paypalInfo['custom']);
                    $firebase_project_id = get_settings('firebase_project_id');
                    $service_account_file = get_settings('service_account_file');

                    $data['transaction_type'] = 'Transaction';
                    $data['user_id'] = $userData[0];
                    $data['payer_email'] = $userData[1];
                    $data['order_id'] = $paypalInfo["item_number"];
                    $data['type'] = 'paypal';
                    $data['txn_id'] = $paypalInfo["txn_id"];
                    $data['amount'] = $paypalInfo["mc_gross"];
                    $data['currency_code'] = $paypalInfo["mc_currency"];
                    $data['status'] = 'success';
                    $data['message'] = 'Payment Verified';
                    if ($paypalInfo["payment_status"] == 'Completed') {

                        $user = fetch_details('users', ['id' => $userData[0]]);
                        $system_settings = get_settings('system_settings', true);
                        $orders = fetch_orders($paypalInfo["item_number"], $userData[0], false, false, false, false, false, false);

                        $overall_total = array(
                            'total_amount' => $orders['order_data'][0]['total'],
                            'delivery_charge' => $orders['order_data'][0]['delivery_charge'],
                            'tax_amount' => $orders['order_data'][0]['total_tax_amount'],
                            'tax_percentage' => $orders['order_data'][0]['total_tax_percent'],
                            'discount' => $orders['order_data'][0]['promo_discount'],
                            'wallet' => $orders['order_data'][0]['wallet_balance'],
                            'final_total' => $orders['order_data'][0]['final_total'],
                            'total_payable' => $orders['order_data'][0]['total_payable'],
                            'otp' => $orders['order_data'][0]['otp'],
                            'address' => $orders['order_data'][0]['address'],
                            'payment_method' => $orders['order_data'][0]['payment_method']
                        );

                        $overall_order_data = array(
                            'cart_data' => $orders['order_data'][0]['order_items'],
                            'order_data' => $overall_total,
                            'subject' => 'Order received successfully',
                            'user_data' => $user[0],
                            'system_settings' => $system_settings,
                            'user_msg' => 'Hello, Dear ' . ucfirst($user[0]['username']) . ', We have received your order successfully. Your order summaries are as followed',
                            'otp_msg' => 'Here is your OTP. Please, give it to delivery boy only while getting your order.',
                        );

                        send_mail($userData[1], 'Order received successfully', $this->load->view('admin/pages/view/email-template.php', $overall_order_data, TRUE));

                        $this->transaction_model->add_transaction($data);

                        update_details(['active_status' => 'received'], ['order_id' => $data['order_id']], 'order_items');

                        $status = json_encode(array(array('received', date("d-m-Y h:i:sa"))));
                        update_details(['status' => $status], ['order_id' => $data['order_id']], 'order_items', false);

                        // place order custome notification on payment success

                        $custom_notification = fetch_details('custom_notifications', ['type' => "place_order"], '');
                        $hashtag_order_id = '< order_id >';
                        $string = json_encode($custom_notification[0]['title'], JSON_UNESCAPED_UNICODE);
                        $hashtag = html_entity_decode($string);
                        $data1 = str_replace($hashtag_order_id, $order_id, $hashtag);
                        $title = output_escaping(trim($data1, '"'));
                        $hashtag_application_name = '< application_name >';
                        $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                        $hashtag = html_entity_decode($string);
                        $data2 = str_replace($hashtag_application_name, $system_settings['app_name'], $hashtag);
                        $message = output_escaping(trim($data2, '"'));

                        $fcm_admin_subject = (!empty($custom_notification)) ? $title : 'New order placed ID #' . $order_id;
                        $fcm_admin_msg = (!empty($custom_notification)) ? $message : 'New order received for  ' . $system_settings['app_name'] . ' please process it.';
                        $user_fcm = fetch_details('user_fcm', ['user_id' => $data['user_id']], 'fcm_id,platform_type');
                        // $user_fcm = fetch_details('users', ['id' => $data['user_id']], 'fcm_id,platform_type');
                        // Step 1: Group by platform
                        $groupedByPlatform = [];
                        foreach ($user_fcm as $item) {
                            $platform = $item['platform_type'];
                            $groupedByPlatform[$platform][] = $item['fcm_id'];
                        }

                        // Step 2: Chunk each platform group into arrays of 1000
                        $fcm_ids = [];
                        foreach ($groupedByPlatform as $platform => $fcmIds) {
                            $fcm_ids[$platform] = array_chunk($fcmIds, 1000);
                        }
                        $user_fcm_id[0][] = $fcm_ids;
                        if (!empty($user_fcm_id) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                            $fcmMsg = array(
                                'title' => $fcm_admin_subject,
                                'body' => $fcm_admin_msg,
                                'type' => "place_order"
                            );
                            send_notification($fcmMsg, $user_fcm_id, $fcmMsg);
                        }
                    } else if (
                        $paypalInfo["payment_status"] == 'Expired' || $paypalInfo["payment_status"] == 'Failed'
                        || $paypalInfo["payment_status"] == 'Refunded' || $paypalInfo["payment_status"] == 'Reversed'
                    ) {
                        /* if transaction wasn't completed successfully then cancel the order and transaction */
                        $data['transaction_type'] = 'Transaction';
                        $data['user_id'] = $userData[0];
                        $data['payer_email'] = $userData[1];
                        $data['order_id'] = $paypalInfo["item_number"];
                        $data['type'] = 'paypal';
                        $data['txn_id'] = $paypalInfo["txn_id"];
                        $data['amount'] = $paypalInfo["mc_gross"];
                        $data['currency_code'] = $paypalInfo["mc_currency"];
                        $data['status'] = $paypalInfo["payment_status"];
                        $data['message'] = 'Payment could not be completed due to one or more reasons!';
                        $this->transaction_model->add_transaction($data);
                        update_details(['active_status' => 'cancelled'], ['order_id' => $data['order_id']], 'order_items');

                        $status = json_encode(array(array('cancelled', date("d-m-Y h:i:sa"))));
                        update_details(['status' => $status], ['order_id' => $data['order_id']], 'order_items', false);
                    }
                }
            }
        }
    }

    //15. add_transaction
    public function add_transaction()
    {
        /*
            transaction_type : transaction / wallet  // { optional - default is transaction }
            user_id : 15 
            order_id:  23
            type : COD / stripe / razorpay / paypal / paystack / flutterwave - for transaction | credit / debit - for wallet
            payment_method:razorpay / paystack / flutterwave        // used for waller credit option, required when transaction_type - wallet and type - credit
            txn_id : 201567892154 
            amount : 450
            status : success / failure
            message : Done 
            skip_verify_transaction:false   // { optional }
        */

        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('transaction_type', 'Transaction Type', 'trim|xss_clean');

        $this->form_validation->set_rules('order_id', 'order id', 'trim|required|xss_clean');
        $this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txn_id', 'Txn', 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|numeric|xss_clean');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean');
        $this->form_validation->set_rules('message', 'message', 'trim|required|xss_clean');
        $this->form_validation->set_rules('skip_verify_transaction', 'skip_verify_transaction', 'trim|xss_clean');
        if (isset($_POST['transaction_type']) && $_POST['transaction_type'] == "wallet" && $_POST['type'] == "credit") {
            $this->form_validation->set_rules('payment_method', 'Payment method', 'trim|required|xss_clean');
        }

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';

            $type = (isset($_POST['type']) && !empty(trim($_POST['type']))) ? $this->input->post('type', true) : "";
            $txn_id = (isset($_POST['txn_id']) && !empty(trim($_POST['txn_id']))) ? $this->input->post('txn_id', true) : "";
            $status = (isset($_POST['status']) && !empty(trim($_POST['status']))) ? $this->input->post('status', true) : "";
            $txn_id = $this->input->post('txn_id', true);
            $amount = $this->input->post('amount', true);

            if (isset($_POST['transaction_type']) && $_POST['transaction_type'] == "wallet" && $_POST['type'] == "credit") {
                $payment_method = $this->input->post('payment_method', true);
                $payment_method = strtolower($payment_method);

                $user = fetch_users($user_id);
                if (empty($user)) {
                    $this->response['error'] = true;
                    $this->response['message'] = "User not found!";
                    $this->response['data'] = [];
                    print_r(json_encode($this->response));
                    return false;
                }
                $old_balance = $user[0]['balance'];
                $skip_verify_transaction = (isset($_POST['skip_verify_transaction'])) ? $_POST['skip_verify_transaction'] : false;

                /* check if this transaction has already been added or not in transactions table */
                $transaction = fetch_details('transactions', ['txn_id' => $txn_id]);

                if (empty($transaction) || (isset($transaction[0]['status']) && strtolower($transaction[0]['status']) != 'success')) {

                    if ($skip_verify_transaction == false) {
                        $payment = verify_payment_transaction($txn_id, $payment_method); /* calling all in one verify payment transaction function */

                        if ($payment['error'] == false) {
                            if (empty($payment['amount'])) {
                                $payment['amount'] = "0";
                            }
                            $this->load->model('customer_model');
                            if (!$this->customer_model->update_balance($payment['amount'], $user_id, 'add')) {
                                $this->response['error'] = true;
                                $this->response['message'] = "Wallet could not be recharged due to database operation failure";
                                $this->response['amount'] = $payment['amount'];
                                $this->response['old_balance'] = "$old_balance";
                                $this->response['new_balance'] = "$old_balance";
                                $this->response['data'] = $payment['data'];
                                print_r(json_encode($this->response));
                                return false;
                            }
                            $new_balance = $old_balance + $payment['amount'];

                            $this->response['amount'] = $payment['amount'];
                            $this->response['old_balance'] = "$old_balance";
                            $this->response['new_balance'] = "$new_balance";
                            $_POST['message'] = "$payment_method - Wallet credited on successful payment confirmation.";
                            $_POST['amount'] = $payment['amount'];
                        } else {
                            $new_balance = $old_balance + $payment['amount'];

                            $this->response['error'] = true;
                            $this->response['message'] = "Wallet could not be recharged! " . $payment['message'];
                            $this->response['amount'] = $payment['amount'];
                            $this->response['old_balance'] = "$old_balance";
                            $this->response['new_balance'] = "$new_balance";
                            $this->response['data'] = [];
                            print_r(json_encode($this->response));
                            return false;
                        }
                    } else {
                        $this->response['error'] = false;
                        $this->response['message'] = "Wallet credited on successful payment confirmation.";

                        $this->response['data'] = $skip_verify_transaction;

                        print_r(json_encode($this->response));
                        return false;
                    }
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = "Wallet could not be recharged! Transaction has already been added before";
                    $this->response['amount'] = 0;
                    $this->response['old_balance'] = (string) $old_balance;
                    $this->response['new_balance'] = (string) $old_balance;
                    $this->response['data'] = $transaction;

                    print_r(json_encode($this->response));
                    return false;
                }
            }

            $transaction_type = (isset($_POST['transaction_type']) && !empty($_POST['transaction_type'])) ? $_POST['transaction_type'] : "transaction";

            $order_item_id = fetch_details('order_items', ['order_id' => $_POST['order_id']], 'id,sub_total');


            // $_POST['order_item_id'] = $order_item_id[$i]['id'];
            $trans_data = [
                'transaction_type' => $transaction_type,
                'user_id' => $user_id,
                'order_id' => $_POST['order_id'],
                'type' => $type,
                'txn_id' => $txn_id,
                'amount' => $amount,
                'status' => $status,
                'message' => $this->response['message'],
            ];


            $res = $this->transaction_model->add_transaction($trans_data);


            $this->response['error'] = false;
            $this->response['message'] = ($transaction_type == "wallet") ? 'Wallet Transaction Added Successfully' : 'Transaction Added Successfully';
            $this->response['data'] = $res;
        }
        print_r(json_encode($this->response));
    }

    //16. get_offer_images
    public function get_offer_images()
    {

        $res = fetch_details('offers', '');
        $i = 0;
        foreach ($res as $row) {
            $res[$i]['image'] = base_url($res[$i]['image']);
            if ($res[$i]['link'] == null || empty($res[$i]['link'])) {
                $res[$i]['link'] = '';
            }
            if (strtolower($res[$i]['type']) == 'categories') {
                $id = (!empty($res[$i]['type_id']) && isset($res[$i]['type_id'])) ? $res[$i]['type_id'] : '';
                $cat_res = $this->category_model->get_categories($id);
                $res[$i]['data'] = $cat_res;
            } else if (strtolower($res[$i]['type']) == 'products') {
                $id = (!empty($res[$i]['type_id']) && isset($res[$i]['type_id'])) ? $res[$i]['type_id'] : '';
                $pro_res = fetch_product(NULL, NULL, $id);
                $res[$i]['data'] = $pro_res['product'];
            } else {
                $res[$i]['data'] = [];
            }

            $i++;
        }
        $this->response['error'] = false;
        $this->response['data'] = $res;
        print_r(json_encode($this->response));
    }

    //17. get_faqs
    public function get_faqs()
    {


        /*
            limit:25                // { default - 25 } optional
            offset:0                // { default - 0 } optional
            sort: id   			    // { default - id } optional
            order:DESC/ASC          // { default - DESC } optional
        */

        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'DESC';
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'id';
            $res = $this->faq_model->get_faqs($offset, $limit, $sort, $order);
            $this->response['error'] = false;
            $this->response['message'] = 'FAQ(s) Retrieved Successfully';
            $this->response['total'] = $res['total'];
            $this->response['data'] = $res['data'];
        }

        print_r(json_encode($this->response));
    }

    public function stripe_webhook()
    {
        $this->load->library(['stripe']);
        $system_settings = get_settings('system_settings', true);
        $credentials = $this->stripe->get_credentials();
        $request_body = file_get_contents('php://input');

        $event = json_decode($request_body, FALSE);

        log_message('error', 'Stripe Webhook --> ' . var_export($event, true));

        if (!empty($event->data->object)) {
            $txn_id = (isset($event->data->object->payment_intent)) ? $event->data->object->payment_intent : "";
            if (!empty($txn_id)) {
                $transaction = fetch_details('transactions', ['txn_id' => $txn_id], '*');
                log_message('error', 'transaction --> ' . var_export($transaction, true));

                if (isset($transaction) && !empty($transaction)) {
                    $order_id = $transaction[0]['order_id'];
                    $user_id = $transaction[0]['user_id'];
                } else {
                    $order_id = $event->data->metadata->order_id;
                    $order_data = fetch_orders($order_id);
                    $user_id = $order_data['order_data'][0]['user_id'];
                }
            }
            $amount = $event->data->object->amount;
            $currency = $event->data->object->currency;
            $balance_transaction = $event->data->object->balance_transaction;
        } else {
            $order_id = 0;
            $amount = 0;
            $currency = (isset($event->data->object->currency)) ? $event->data->object->currency : "";
            $balance_transaction = 0;
        }


        /* Wallet refill has unique format for order ID - wallet-refill-user-{user_id}-{system_time}-{3 random_number}  */
        if (empty($order_id)) {
            $order_id = (!empty($event->data->object->metadata) && isset($event->data->object->metadata->order_id)) ? $event->data->object->metadata->order_id : 0;
        }

        if (!is_numeric($order_id) && strpos($order_id, "wallet-refill-user") !== false) {
            $temp = explode("-", $order_id);
            if (isset($temp[3]) && is_numeric($temp[3]) && !empty($temp[3] && $temp[3] != '')) {
                $user_id = $temp[3];
            } else {
                $user_id = 0;
            }
        }

        $http_stripe_signature = isset($_SERVER['HTTP_STRIPE_SIGNATURE']) ? $_SERVER['HTTP_STRIPE_SIGNATURE'] : "";
        $result = $this->stripe->construct_event($request_body, $http_stripe_signature, $credentials['webhook_key']);
        log_message('error', 'Stripe order id --> ' . var_export($result, true));
        log_message('error', 'http_stripe_signature--> ' . var_export($http_stripe_signature, true));

        if ($result == "Matched") {

            if ($event->type == 'charge.succeeded') {
                if (!empty($order_id)) {
                    /* To do the wallet recharge if the order id is set in the above mentioned pattern */
                    if (strpos($order_id, "wallet-refill-user") !== false) {
                        $data['transaction_type'] = "wallet";
                        $data['user_id'] = $user_id;
                        $data['order_id'] = $order_id;
                        $data['type'] = "credit";
                        $data['txn_id'] = $txn_id;
                        $data['amount'] = $amount / 100;
                        $data['status'] = "success";
                        $data['message'] = "Wallet refill successful";
                        log_message('error', 'Stripe order id --> ' . var_export($data, true));

                        $this->transaction_model->add_transaction($data);

                        $this->load->model('customer_model');
                        if ($this->customer_model->update_balance($amount / 100, $user_id, 'add')) {
                            $response['error'] = false;
                            $response['transaction_status'] = $event->type;
                            $response['message'] = "Wallet recharged successfully!";
                        } else {
                            $response['error'] = true;
                            $response['transaction_status'] = $event->type;
                            $response['message'] = "Wallet could not be recharged!";
                            log_message('error', 'Stripe Webhook | wallet recharge failure --> ' . var_export($event, true));
                        }
                        echo json_encode($response);
                        return false;
                        exit();
                    } else {
                        /* process the order and mark it as received */
                        $order = fetch_orders($order_id, false, false, false, false, false, false, false);
                        $firebase_project_id = get_settings('firebase_project_id');
                        $service_account_file = get_settings('service_account_file');
                        if (isset($order['order_data'][0]['user_id'])) {
                            $user = fetch_details('users', ['id' => $order['order_data'][0]['user_id']]);
                            $overall_total = array(
                                'total_amount' => $order['order_data'][0]['total'],
                                'delivery_charge' => $order['order_data'][0]['delivery_charge'],
                                'tax_amount' => $order['order_data'][0]['total_tax_amount'],
                                'tax_percentage' => $order['order_data'][0]['total_tax_percent'],
                                'discount' => $order['order_data'][0]['promo_discount'],
                                'wallet' => $order['order_data'][0]['wallet_balance'],
                                'final_total' => $order['order_data'][0]['final_total'],
                                'total_payable' => $order['order_data'][0]['total_payable'],
                                'otp' => $order['order_data'][0]['otp'],
                                'address' => $order['order_data'][0]['address'],
                                'payment_method' => $order['order_data'][0]['payment_method']
                            );

                            $overall_order_data = array(
                                'cart_data' => $order['order_data'][0]['order_items'],
                                'order_data' => $overall_total,
                                'subject' => 'Order received successfully',
                                'user_data' => $user[0],
                                'system_settings' => $system_settings,
                                'user_msg' => 'Hello, Dear ' . ucfirst($user[0]['username']) . ', We have received your order successfully. Your order summaries are as followed',
                                'otp_msg' => 'Here is your OTP. Please, give it to delivery boy only while getting your order.',
                            );
                            if (isset($user[0]['email']) && !empty($user[0]['email'])) {
                                send_mail($user[0]['email'], 'Order received successfully', $this->load->view('admin/pages/view/email-template.php', $overall_order_data, TRUE));
                            }
                            /* No need to add because the transaction is already added just update the transaction status */
                            if (!empty($transaction)) {
                                $transaction_id = $transaction[0]['id'];
                                update_details(['status' => 'success'], ['txn_id' => $txn_id], 'transactions');
                            } else {
                                /* add transaction of the payment */
                                $amount = ($event->data->object->amount / 100);
                                $data = [
                                    'transaction_type' => 'transaction',
                                    'user_id' => $user_id,
                                    'order_id' => $order_id,
                                    'type' => 'stripe',
                                    'txn_id' => $txn_id,
                                    'amount' => $amount,
                                    'status' => 'success',
                                    'message' => 'order placed successfully',
                                ];
                                $this->transaction_model->add_transaction($data);
                            }
                            update_details(['active_status' => 'received'], ['order_id' => $order_id], 'order_items');

                            $status = json_encode(array(array('received', date("d-m-Y h:i:sa"))));
                            update_details(['status' => $status], ['order_id' => $order_id], 'order_items', false);

                            // place order custome notification on payment success
                            $custom_notification = fetch_details('custom_notifications', ['type' => "place_order"], '');
                            $hashtag_order_id = '< order_id >';
                            $string = json_encode($custom_notification[0]['title'], JSON_UNESCAPED_UNICODE);
                            $hashtag = html_entity_decode($string);
                            $data1 = str_replace($hashtag_order_id, $order_id, $hashtag);
                            $title = output_escaping(trim($data1, '"'));
                            $hashtag_application_name = '< application_name >';
                            $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                            $hashtag = html_entity_decode($string);
                            $data2 = str_replace($hashtag_application_name, $system_settings['app_name'], $hashtag);
                            $message = output_escaping(trim($data2, '"'));

                            $fcm_admin_subject = (!empty($custom_notification)) ? $title : 'New order placed ID #' . $order_id;
                            $fcm_admin_msg = (!empty($custom_notification)) ? $message : 'New order received for  ' . $system_settings['app_name'] . ' please process it.';
                            $user_fcm = fetch_details('user_fcm', ['user_id' => $user_id], 'fcm_id,platform_type');
                            // $user_fcm = fetch_details('users', ['id' => $user_id], 'fcm_id,platform_type');
                            // Step 1: Group by platform
                            $groupedByPlatform = [];
                            foreach ($user_fcm as $item) {
                                $platform = $item['platform_type'];
                                $groupedByPlatform[$platform][] = $item['fcm_id'];
                            }

                            // Step 2: Chunk each platform group into arrays of 1000
                            $fcm_ids = [];
                            foreach ($groupedByPlatform as $platform => $fcmIds) {
                                $fcm_ids[$platform] = array_chunk($fcmIds, 1000);
                            }
                            $user_fcm_id[0][] = $fcm_ids;
                            if (!empty($user_fcm_id) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                                $fcmMsg = array(
                                    'title' => $fcm_admin_subject,
                                    'body' => $fcm_admin_msg,
                                    'type' => "place_order"
                                );
                                send_notification($fcmMsg, $user_fcm_id, $fcmMsg);
                            }
                            notify_event(
                                'place_order',
                                ["customer" => [$user[0]['email']]],
                                ["customer" => [$user[0]['mobile']]],
                                ["orders.id" => $order_id]
                            );
                        }
                    }
                } else {
                    /* No order ID found / sending 304 error to payment gateway so it retries wenhook after sometime*/
                    log_message('error', 'Stripe Webhook | Order id not found --> ' . var_export($event, true));
                    return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(304)
                        ->set_output(json_encode(
                            array(
                                'message' => '304 Not Modified - order/transaction id not found',
                                'error' => true
                            )
                        ));
                }
                $response['error'] = false;
                $response['transaction_status'] = $event->type;
                $response['message'] = "Transaction successfully done";
                echo json_encode($response);
                return false;
                exit();
            } elseif ($event->type == 'charge.failed') {
                $order = fetch_orders($order_id, false, false, false, false, false, false, false);
                if (!empty($order_id)) {
                    update_details(['active_status' => 'cancelled'], ['order_id' => $order_id], 'order_items');
                }
                /* No need to add because the transaction is already added just update the transaction status */
                if (!empty($transaction)) {
                    $transaction_id = $transaction[0]['id'];
                    update_details(['status' => 'failed'], ['id' => $transaction_id], 'transactions');
                }
                $response['error'] = true;
                $response['transaction_status'] = $event->type;
                $response['message'] = "Transaction is failed. ";
                log_message('error', 'Stripe Webhook | Transaction is failed --> ' . var_export($event, true));
                echo json_encode($response);
                return false;
                exit();
            } elseif ($event->type == 'charge.pending') {
                $response['error'] = false;
                $response['transaction_status'] = $event->type;
                $response['message'] = "Waiting for customer to finish transaction ";
                log_message('error', 'Stripe Webhook | Waiting customer to finish transaction --> ' . var_export($event, true));
                echo json_encode($response);
                return false;
                exit();
            } elseif ($event->type == 'charge.expired') {
                if (!empty($order_id)) {
                    update_details(['active_status' => 'cancelled'], ['order_id' => $order_id], 'order_items');
                }
                /* No need to add because the transaction is already added just update the transaction status */
                if (!empty($transaction)) {
                    $transaction_id = $transaction[0]['id'];
                    update_details(['status' => 'expired'], ['id' => $transaction_id], 'transactions');
                }
                $response['error'] = true;
                $response['transaction_status'] = $event->type;
                $response['message'] = "Transaction is expired.";
                log_message('error', 'Stripe Webhook | Transaction is expired --> ' . var_export($event, true));
                echo json_encode($response);
                return false;
                exit();
            } elseif ($event->type == 'charge.refunded') {
                if (!empty($order_id)) {
                    update_details(['active_status' => 'cancelled'], ['order_id' => $order_id], 'order_items');
                }
                /* No need to add because the transaction is already added just update the transaction status */
                if (!empty($transaction)) {
                    $transaction_id = $transaction[0]['id'];
                    update_details(['status' => 'refunded'], ['id' => $transaction_id], 'transactions');
                }
                $response['error'] = true;
                $response['transaction_status'] = $event->type;
                $response['message'] = "Transaction is refunded.";
                log_message('error', 'Stripe Webhook | Transaction is refunded --> ' . var_export($event, true));
                echo json_encode($response);
                return false;
                exit();
            } else {
                $response['error'] = true;
                $response['transaction_status'] = $event->type;
                $response['message'] = "Transaction could not be detected.";
                log_message('error', 'Stripe Webhook | Transaction could not be detected --> ' . var_export($event, true));
                echo json_encode($response);
                return false;
                exit();
            }
        } else {
            log_message('error', 'Stripe Webhook | Invalid Server Signature  --> ' . var_export($result, true));
            log_message('error', 'Stripe Webhook | Order id  --> ' . var_export($order_id, true));
            return false;
            exit();
        }
    }

    public function transactions()
    {
        /*
            user_id:73
            id:1001                // { optional}
            transaction_type:transaction / wallet //razorpay { default - transaction } optional
            type: COD / stripe / razorpay / paypal / paystack /refund/ flutterwave - for transaction | credit / debit - for wallet // { optional }
            search: Search keyword // { optional }
            limit:25                // { default - 25 } optional
            offset:0                // { default - 0 } optional
            sort:id / date_created // { default - id } optional
            order:DESC/ASC          // { default - DESC } optional
        */
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('transaction_type', 'Transaction Type', 'trim|xss_clean');
        $this->form_validation->set_rules('type', 'Type', 'trim|xss_clean');
        $this->form_validation->set_rules('search', 'Search keyword', 'trim|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $id = (isset($_POST['id']) && is_numeric($_POST['id']) && !empty(trim($_POST['id']))) ? $this->input->post('id', true) : "";
            $transaction_type = (isset($_POST['transaction_type']) && !empty(trim($_POST['transaction_type']))) ? $this->input->post('transaction_type', true) : "transaction";
            $type = (isset($_POST['type']) && !empty(trim($_POST['type']))) ? $this->input->post('type', true) : "";
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'DESC';
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'id';
            $res = $this->transaction_model->get_transactions($id, $user_id, $transaction_type, $type, $search, $offset, $limit, $sort, $order);
            $this->response['error'] = false;
            $this->response['message'] = 'Transactions Retrieved Successfully';
            $this->response['total'] = $res['total'];
            $this->response['balance'] = get_user_balance($user_id);
            $this->response['data'] = $res['data'];
        }

        print_r(json_encode($this->response));
    }

    public function generate_paytm_checksum()
    {
        /*
            order_id:1001
            amount:1099
            user_id:73              //{ optional } 
            industry_type:Industry  //{ optional } 
            channel_id:WAP          //{ optional }
            website:website link    //{ optional }
        */

        if (!$this->verify_token()) {
            return false;
        }

        $this->load->library(['paytm']);
        $this->form_validation->set_rules('order_id', 'Order ID', 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('user_id', 'User ID', 'trim|xss_clean');
        $this->form_validation->set_rules('industry_type', 'Industry Type', 'trim|xss_clean');
        $this->form_validation->set_rules('channel_id', 'Channel ID', 'trim|xss_clean');
        $this->form_validation->set_rules('website', 'Website', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        } else {
            $settings = get_settings('payment_method', true);
            $credentials = $this->paytm->get_credentials();

            $paytm_params["MID"] = $settings['paytm_merchant_id'];

            $paytm_params["ORDER_ID"] = $this->input->post('order_id', true);
            $paytm_params["TXN_AMOUNT"] = $this->input->post('amount', true);
            $paytm_params["CUST_ID"] = $this->input->post('user_id', true);
            $paytm_params["WEBSITE"] = $this->input->post('website', true);
            $paytm_params["CALLBACK_URL"] = $credentials['url'] . "theia/paytmCallback?ORDER_ID=" . $paytm_params["ORDER_ID"];

            /**
             * Generate checksum by parameters we have
             * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
             */
            $paytm_checksum = $this->paytm->generateSignature($paytm_params, $settings['paytm_merchant_key']);

            if (!empty($paytm_checksum)) {
                $response['error'] = false;
                $response['message'] = "Checksum created successfully";
                $response['order id'] = $paytm_params["ORDER_ID"];
                $response['data'] = $paytm_params;
                $response['signature'] = $paytm_checksum;
                print_r(json_encode($response));
                return false;
            } else {
                $response['error'] = true;
                $response['message'] = "Data not found!";
                print_r(json_encode($response));
                return false;
            }
        }
    }

    public function generate_paytm_txn_token()
    {
        /*
            amount:100.00
            order_id:102
            user_id:73
            industry_type:      //{optional}
            channel_id:      //{optional}
            website:      //{optional}
        */
        $this->form_validation->set_rules('order_id', 'Order ID', 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('industry_type', 'Industry Type', 'trim|xss_clean');
        $this->form_validation->set_rules('channel_id', 'Channel ID', 'trim|xss_clean');
        $this->form_validation->set_rules('website', 'Website', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        } else {
            $this->load->library('paytm');
            $credentials = $this->paytm->get_credentials();
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';

            $order_id = $_POST['order_id'];
            $amount = $_POST['amount'];
            $paytmParams = array();

            $paytmParams["body"] = array(
                "requestType" => "Payment",
                "mid" => $credentials['paytm_merchant_id'],
                "websiteName" => "WEBSTAGING",
                "orderId" => $order_id,
                "callbackUrl" => $credentials['url'] . "theia/paytmCallback?ORDER_ID=" . $order_id,
                "txnAmount" => array(
                    "value" => $amount,
                    "currency" => "INR",
                ),
                "userInfo" => array(
                    "custId" => $user_id,
                ),
            );

            /*
             * Generate checksum by parameters we have in body
             * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
             */
            $paytm_response = $this->paytm->initiate_transaction($paytmParams);

            if (!empty($paytm_response)) {
                if (isset($paytm_response['body']['resultInfo']['resultMsg']) && ($paytm_response['body']['resultInfo']['resultMsg'] == "Success" || $paytm_response['body']['resultInfo']['resultMsg'] == "Success Idempotent")) {
                    $response['error'] = false;
                    $response['message'] = "Transaction token generated successfully";
                    $response['txn_token'] = $paytm_response['body']['txnToken'];
                    $response['paytm_response'] = $paytm_response;
                } else {
                    $response['error'] = true;
                    $response['message'] = $paytm_response['body']['resultInfo']['resultMsg'];
                    $response['txn_token'] = "";
                    $response['paytm_response'] = $paytm_response;
                }
            } else {
                $response['error'] = true;
                $response['message'] = "Could not generate transaction token. Try again!";
                $response['txn_token'] = "";
                $response['paytm_response'] = $paytm_response;
            }
            print_r(json_encode($response));
        }
    }
    public function validate_paytm_checksum()
    {
        /*
            paytm_checksum:PAYTM_CHECKSUM
            order_id:1001
            amount:1099
            user_id:73              //{ optional } 
            industry_type:Industry  //{ optional } 
            channel_id:WAP          //{ optional }
            website:website link    //{ optional }
        */
        if (!$this->verify_token()) {
            return false;
        }

        $this->load->library(['paytm']);
        $this->form_validation->set_rules('order_id', 'Order ID', 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('user_id', 'User ID', 'trim|xss_clean');
        $this->form_validation->set_rules('industry_type', 'Industry Type', 'trim|xss_clean');
        $this->form_validation->set_rules('channel_id', 'Channel ID', 'trim|xss_clean');
        $this->form_validation->set_rules('website', 'Website', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        } else {
            $settings = get_settings('payment_method', true);
            $credentials = $this->paytm->get_credentials();

            $paytm_checksum = $this->input->post('paytm_checksum', true);

            $paytm_params["MID"] = $settings['paytm_merchant_id'];
            $paytm_params["ORDER_ID"] = $this->input->post('order_id', true);
            $paytm_params["TXN_AMOUNT"] = $this->input->post('amount', true);

            $isVerifySignature = $this->paytm->verifySignature($paytm_params, $settings['paytm_merchant_key'], $paytm_checksum);
            if ($isVerifySignature) {
                $this->response['error'] = false;
                $this->response['message'] = "Checksum Matched";
                print_r(json_encode($this->response));
                return false;
            } else {
                $this->response['error'] = true;
                $this->response['message'] = "Checksum Mismatched";
                print_r(json_encode($this->response));
                return false;
            }
        }
    }

    // validate_refer_code
    public function validate_refer_code()
    {
        /* 
            referral_code:USERS_CODE_TO_BE_VALIDATED
        */


        $this->form_validation->set_rules('referral_code', 'Referral code', 'trim|required|is_unique[users.referral_code]|xss_clean');
        $this->form_validation->set_message('is_unique', 'This %s is already used by some other user.');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return;
        } else {
            $this->response['error'] = false;
            $this->response['message'] = "Referral Code is available to be used";
        }
        print_r(json_encode($this->response));
        return false;
    }

    public function flutterwave_webview()
    {
        /* 
            amount:100
            user_id:73
            order_id:101 (optional)
            reference:eShop-165232013-400  // { optional }
        */
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|numeric|xss_clean');
        $this->form_validation->set_rules('order_id', 'Order ID', 'trim|xss_clean');
        $this->form_validation->set_rules('reference', 'Reference', 'trim|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        } else {
            $this->load->library('Flutterwave');

            $app_settings = get_settings('system_settings', true);
            $payment_settings = get_settings('payment_method', true);
            $logo = base_url() . get_settings('favicon');
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $order_id = $this->input->post('order_id', true);
            $user = fetch_users($user_id);

            if (empty($user) || !isset($user[0]['mobile'])) {
                $response['error'] = true;
                $response['message'] = "User not found!";
                print_r(json_encode($response));
                return false;
            }

            $data['tx_ref'] = (isset($_POST['reference']) && !empty($_POST['reference'])) ? $_POST['reference'] : "order" . "-" . $order_id . "-" . time() . "-" . rand(1000, 9999);
            $data['amount'] = $this->input->post('amount', true);
            $data['currency'] = (isset($payment_settings['flutterwave_currency_code']) && !empty($payment_settings['flutterwave_currency_code'])) ? $payment_settings['flutterwave_currency_code'] : "NGN";
            $data['redirect_url'] = base_url('app/v1/api/flutterwave_payment_response');
            $data['payment_options'] = "card";
            $data['meta']['user_id'] = $user_id;
            $data['meta'] = $order_id;
            $data['customer']['email'] = (!empty($user[0]['email'])) ? $user[0]['email'] : $app_settings['support_email'];
            $data['customer']['phonenumber'] = $user[0]['mobile'];
            $data['customer']['name'] = $user[0]['username'];
            $data['customizations']['title'] = $app_settings['app_name'] . " Payments ";
            $data['customizations']['description'] = "Online payments on " . $app_settings['app_name'];
            $data['customizations']['logo'] = (!empty($logo)) ? $logo : "";

            $payment = $this->flutterwave->create_payment($data);
            if (!empty($payment)) {
                $payment = json_decode($payment, true);
                if (isset($payment['status']) && $payment['status'] == 'success' && isset($payment['data']['link'])) {
                    $response['error'] = false;
                    $response['message'] = "Payment link generated. Follow the link to make the payment!";
                    $response['link'] = $payment['data']['link'];
                    print_r(json_encode($response));
                } else {
                    $response['error'] = true;
                    $response['message'] = "Could not initiate payment. " . $payment['message'];
                    $response['link'] = "";
                    print_r(json_encode($response));
                }
            } else {
                $response['error'] = true;
                $response['message'] = "Could not initiate payment. Try again later! ";
                $response['link'] = "";
                print_r($response);
            }
        }
    }
    public function flutterwave_payment_response()
    {
        if (isset($_GET['transaction_id']) && !empty($_GET['transaction_id'])) {
            $this->load->library('flutterwave');
            $transaction_id = $_GET['transaction_id'];
            $transaction = $this->flutterwave->verify_transaction($transaction_id);
            if (!empty($transaction)) {
                $transaction = json_decode($transaction, true);
                if ($transaction['status'] == 'error') {
                    $response['error'] = true;
                    $response['message'] = $transaction['message'];
                    $response['amount'] = 0;
                    $response['status'] = "failed";
                    $response['currency'] = "NGN";
                    $response['transaction_id'] = $transaction_id;
                    $response['reference'] = "";
                    print_r(json_encode($response));
                    return false;
                }

                if ($transaction['status'] == 'success' || $transaction['data']['status'] == 'successful') {
                    $response['error'] = false;
                    $response['message'] = "Payment has been completed successfully";
                    $response['amount'] = $transaction['data']['amount'];
                    $response['currency'] = $transaction['data']['currency'];
                    $response['status'] = $transaction['data']['status'];
                    $response['transaction_id'] = $transaction['data']['id'];
                    $response['reference'] = $transaction['data']['tx_ref'];
                    print_r(json_encode($response));
                    return false;
                } else if ($transaction['status'] == 'success' || $transaction['data']['status'] != 'successful') {
                    $response['error'] = true;
                    $response['message'] = "Payment is " . $transaction['data']['status'];
                    $response['amount'] = $transaction['data']['amount'];
                    $response['currency'] = $transaction['data']['currency'];
                    $response['status'] = $transaction['data']['status'];
                    $response['transaction_id'] = $transaction['data']['id'];
                    $response['reference'] = $transaction['data']['tx_ref'];
                    print_r(json_encode($response));
                    return false;
                }
            } else {
                $response['error'] = true;
                $response['message'] = "Transaction not found";
                print_r(json_encode($response));
            }
        } else {
            $response['error'] = true;
            $response['message'] = "Invalid request!";
            print_r(json_encode($response));
            return false;
        }
    }

    public function delete_order()
    {
        /*
            order_id:1
        */
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('order_id', 'Order ID', 'trim|required|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $order_id = $_POST['order_id'];
            $order_item = fetch_details('order_items', ['order_id' => $order_id], 'user_id,product_variant_id,quantity');
            $order = fetch_orders($order_id, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, 0);

            foreach ($order_item as $row) {
                $cart_data = [
                    'user_id' => $row['user_id'],
                    'product_variant_id' => $row['product_variant_id'],
                    'qty' => $row['quantity'],
                    'is_saved_for_later' => 0,
                ];
                $this->db->insert('cart', $cart_data);
            }
            if ($order['order_data'][0]['order_items'][0]['status'][0][0] == 'awaiting') {
                update_stock($order['order_data'][0]['order_items'][0]['product_variant_id'], $order['order_data'][0]['order_items'][0]['quantity'], 'plus');
            }
            if (isset($order['order_data'][0]['wallet_balance']) && $order['order_data'][0]['wallet_balance'] != '' && $order['order_data'][0]['wallet_balance'] != 0) {
                update_wallet_balance('credit', $order['order_data'][0]['user_id'], $order['order_data'][0]['wallet_balance'], 'Wallet Amount Credited for Order ID  : ' . $order['order_data'][0]['id']);
            }
            delete_details(['id' => $order_id], 'orders');
            delete_details(['order_id' => $order_id], 'order_items');

            $this->response['error'] = false;
            $this->response['message'] = 'Order deleted successfully';
            $this->response['data'] = array();
        }
        print_r(json_encode($this->response));
    }

    //27. get_ticket_types
    public function get_ticket_types()
    {
        $this->db->select('*');
        $types = $this->db->get('ticket_types')->result_array();
        if (!empty($types)) {
            for ($i = 0; $i < count($types); $i++) {
                $types[$i] = output_escaping($types[$i]);
            }
        }
        $this->response['error'] = false;
        $this->response['message'] = 'Ticket types fetched successfully';
        $this->response['data'] = $types;
        print_r(json_encode($this->response));
    }

    //28. add_ticket
    public function add_ticket()
    {
        /*
            ticket_type_id:1
            subject:product_image not displying
            email:test@gmail.com
            description:its not showing images of products in web
            user_id:1
        */

        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('ticket_type_id', 'Ticket Type', 'trim|required|xss_clean');
        $this->form_validation->set_rules('subject', 'Subject', 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', 'email', 'trim|required|xss_clean');
        $this->form_validation->set_rules('description', 'description', 'trim|required|xss_clean');


        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $ticket_type_id = $this->input->post('ticket_type_id', true);
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $subject = $this->input->post('subject', true);
            $email = $this->input->post('email', true);
            $description = $this->input->post('description', true);
            $user = fetch_users($user_id);
            if (empty($user)) {
                $this->response['error'] = true;
                $this->response['message'] = "User not found!";
                $this->response['data'] = [];
                print_r(json_encode($this->response));
                return false;
            }
            $data = array(
                'ticket_type_id' => $ticket_type_id,
                'user_id' => $user_id,
                'subject' => $subject,
                'email' => $email,
                'description' => $description,
                'status' => PENDING,
            );
            $insert_id = $this->ticket_model->add_ticket($data);
            if (!empty($insert_id)) {
                $result = $this->ticket_model->get_tickets($insert_id, $ticket_type_id, $user_id);
                $this->response['error'] = false;
                $this->response['message'] = 'Ticket Added Successfully';
                $this->response['data'] = $result['data'];
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Ticket Not Added';
                $this->response['data'] = (!empty($this->response['data'])) ? $this->response['data'] : [];
            }
        }
        print_r(json_encode($this->response));
    }
    //29. edit_ticket
    public function edit_ticket()
    {
        /*
            ticket_id:1
            ticket_type_id:1
            subject:product_image not displying
            email:test@gmail.com
            description:its not showing attachments of products in web
            user_id:1
            status:3 or 5 [3 -> resolved, 5 -> reopened]
            [1 -> pending, 2 -> opened, 3 -> resolved, 4 -> closed, 5 -> reopened]
        */

        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('ticket_type_id', 'Ticket Type Id', 'trim|required|xss_clean');
        $this->form_validation->set_rules('ticket_id', 'Ticket Id', 'trim|required|xss_clean');
        $this->form_validation->set_rules('subject', 'Subject', 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
        $this->form_validation->set_rules('description', 'Description', 'trim|required|xss_clean');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean');


        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $status = $this->input->post('status', true);
            $ticket_id = $this->input->post('ticket_id', true);
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $res = fetch_details('tickets', 'id=' . $ticket_id . ' and user_id=' . $user_id, '*');
            if (empty($res)) {
                $this->response['error'] = true;
                $this->response['message'] = "User id is changed you can not udpate the ticket.";
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
            if ($status == RESOLVED && $res[0]['status'] == CLOSED) {
                $this->response['error'] = true;
                $this->response['message'] = "Current status is closed.";
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
            if ($status == REOPEN && ($res[0]['status'] == PENDING || $res[0]['status'] == OPENED)) {
                $this->response['error'] = true;
                $this->response['message'] = "Current status is pending or opened.";
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
            $ticket_type_id = $this->input->post('ticket_type_id', true);
            $subject = $this->input->post('subject', true);
            $email = $this->input->post('email', true);
            $description = $this->input->post('description', true);
            $user = fetch_users($user_id);
            if (empty($user)) {
                $this->response['error'] = true;
                $this->response['message'] = "User not found!";
                $this->response['data'] = [];
                print_r(json_encode($this->response));
                return false;
            }
            $data = array(
                'ticket_type_id' => $ticket_type_id,
                'user_id' => $user_id,
                'subject' => $subject,
                'email' => $email,
                'description' => $description,
                'status' => $status,
                'ticket_id' => $ticket_id,
                'edit_ticket' => $ticket_id
            );
            if (!$this->ticket_model->add_ticket($data)) {
                $result = $this->ticket_model->get_tickets($ticket_id, $ticket_type_id, $user_id);
                $this->response['error'] = false;
                $this->response['message'] = 'Ticket updated Successfully';
                $this->response['data'] = $result['data'];
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Ticket Not Added';
                $this->response['data'] = (!empty($this->response['data'])) ? $this->response['data'] : [];
            }
        }
        print_r(json_encode($this->response));
    }

    //30. send_message
    public function send_message()
    {
        /*
            user_type:user
            user_id:1
            ticket_id:1	
            message:test	
            attachments[]:files  {optional} {type allowed -> image,video,document,spreadsheet,archive}
        */
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('user_type', 'User Type', 'trim|required|xss_clean');
        $this->form_validation->set_rules('ticket_id', 'Ticket id', 'trim|required|numeric|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            $user_type = $this->input->post('user_type', true);
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $ticket_id = $this->input->post('ticket_id', true);
            $message = (isset($_POST['message']) && !empty(trim($_POST['message']))) ? $this->input->post('message', true) : "";


            $user = fetch_users($user_id);
            if (empty($user)) {
                $this->response['error'] = true;
                $this->response['message'] = "User not found!";
                $this->response['data'] = [];
                print_r(json_encode($this->response));
                return false;
            }
            if (!file_exists(FCPATH . TICKET_IMG_PATH)) {
                mkdir(FCPATH . TICKET_IMG_PATH, 0777);
            }

            $temp_array = array();
            $files = $_FILES;
            $images_new_name_arr = array();
            $images_info_error = "";
            $allowed_media_types = implode('|', allowed_media_types());
            $config = [
                'upload_path' => FCPATH . TICKET_IMG_PATH,
                'allowed_types' => $allowed_media_types,
                'max_size' => 8000,
            ];


            if (!empty($_FILES['attachments']['name'][0]) && isset($_FILES['attachments']['name'])) {
                $other_image_cnt = count($_FILES['attachments']['name']);
                $other_img = $this->upload;
                $other_img->initialize($config);

                for ($i = 0; $i < $other_image_cnt; $i++) {

                    if (!empty($_FILES['attachments']['name'][$i])) {

                        $_FILES['temp_image']['name'] = $files['attachments']['name'][$i];
                        $_FILES['temp_image']['type'] = $files['attachments']['type'][$i];
                        $_FILES['temp_image']['tmp_name'] = $files['attachments']['tmp_name'][$i];
                        $_FILES['temp_image']['error'] = $files['attachments']['error'][$i];
                        $_FILES['temp_image']['size'] = $files['attachments']['size'][$i];
                        if (!$other_img->do_upload('temp_image')) {
                            $images_info_error = 'attachments :' . $images_info_error . ' ' . strip_tags($other_img->display_errors());
                        } else {
                            $temp_array = $other_img->data();
                            resize_review_images($temp_array, FCPATH . TICKET_IMG_PATH);
                            $images_new_name_arr[$i] = TICKET_IMG_PATH . $temp_array['file_name'];
                        }
                    } else {
                        $_FILES['temp_image']['name'] = $files['attachments']['name'][$i];
                        $_FILES['temp_image']['type'] = $files['attachments']['type'][$i];
                        $_FILES['temp_image']['tmp_name'] = $files['attachments']['tmp_name'][$i];
                        $_FILES['temp_image']['error'] = $files['attachments']['error'][$i];
                        $_FILES['temp_image']['size'] = $files['attachments']['size'][$i];
                        if (!$other_img->do_upload('temp_image')) {
                            $images_info_error = strip_tags($other_img->display_errors());
                        }
                    }
                }

                //Deleting Uploaded attachments if any overall error occured
                if ($images_info_error != NULL || !$this->form_validation->run()) {
                    if (isset($images_new_name_arr) && !empty($images_new_name_arr || !$this->form_validation->run())) {
                        foreach ($images_new_name_arr as $key => $val) {
                            unlink(FCPATH . TICKET_IMG_PATH . $images_new_name_arr[$key]);
                        }
                    }
                }
            }
            if ($images_info_error != NULL) {
                $this->response['error'] = true;
                $this->response['message'] = $images_info_error;
                print_r(json_encode($this->response));
                return false;
            }
            $data = array(
                'user_type' => $user_type,
                'user_id' => $user_id,
                'ticket_id' => $ticket_id,
                'message' => $message
            );
            if (!empty($_FILES['attachments']['name'][0]) && isset($_FILES['attachments']['name'])) {
                $data['attachments'] = $images_new_name_arr;
            }
            $insert_id = $this->ticket_model->add_ticket_message($data);
            $app_settings = get_settings('system_settings', true);
            $firebase_project_id = get_settings('firebase_project_id');
            $service_account_file = get_settings('service_account_file');
            if (!empty($insert_id)) {
                $data1 = $this->config->item('type');
                $result = $this->ticket_model->get_messages($ticket_id, $user_id, "", "", "1", "", "", $data1, $insert_id);

                $this->response['error'] = false;
                $this->response['message'] = 'Ticket Message Added Successfully!';
                $this->response['data'] = $result['data'][0];
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Ticket Message Not Added';
                $this->response['data'] = (!empty($this->response['data'])) ? $this->response['data'] : [];
            }
        }
        print_r(json_encode($this->response));
    }

    //31. get_tickets
    public function get_tickets()
    {
        /*
        31. get_tickets
            ticket_id: 1001                // { optional}
            ticket_type_id: 1001                // { optional}
            user_id: 1001                // { optional}
            status:   [1 -> pending, 2 -> opened, 3 -> resolved, 4 -> closed, 5 -> reopened]// { optional}
            search : Search keyword // { optional }
            limit:25                // { default - 25 } optional
            offset:0                // { default - 0 } optional
            sort: id | date_created | last_updated                // { default - id } optional
            order:DESC/ASC          // { default - DESC } optional
        */
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('ticket_id', 'Ticket ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('ticket_type_id', 'Ticket Type ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('user_id', 'User ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('status', 'User ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('search', 'Search keyword', 'trim|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            $ticket_id = (isset($_POST['ticket_id']) && is_numeric($_POST['ticket_id']) && !empty(trim($_POST['ticket_id']))) ? $this->input->post('ticket_id', true) : "";
            $ticket_type_id = (isset($_POST['ticket_type_id']) && is_numeric($_POST['ticket_type_id']) && !empty(trim($_POST['ticket_type_id']))) ? $this->input->post('ticket_type_id', true) : "";
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $status = (isset($_POST['status']) && is_numeric($_POST['status']) && !empty(trim($_POST['status']))) ? $this->input->post('status', true) : "";
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 10;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'DESC';
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'id';
            $result = $this->ticket_model->get_tickets($ticket_id, $ticket_type_id, $user_id, $status, $search, $offset, $limit, $sort, $order);
            print_r(json_encode($result));
        }
    }

    //32. get_messages
    public function get_messages()
    {
        /*
        32. get_messages
            ticket_id: 1001            
            user_type: 1001                // { optional}
            user_id: 1001                // { optional}
            search : Search keyword // { optional }
            limit:25                // { default - 25 } optional
            offset:0                // { default - 0 } optional
            sort: id | date_created | last_updated                // { default - id } optional
            order:DESC/ASC          // { default - DESC } optional
        */
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('ticket_id', 'Ticket ID', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('user_id', 'User ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('status', 'User ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('search', 'Search keyword', 'trim|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            $ticket_id = (isset($_POST['ticket_id']) && is_numeric($_POST['ticket_id']) && !empty(trim($_POST['ticket_id']))) ? $this->input->post('ticket_id', true) : "";
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 10;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'DESC';
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'id';
            $data = $this->config->item('type');
            $result = $this->ticket_model->get_messages($ticket_id, '', $search, $offset, $limit, $sort, $order, $data, "");
            print_r(json_encode($result));
        }
    }

    //33. send_bank_transfer_proof
    public function send_bank_transfer_proof()
    {
        /*
        order_id:1
        attachments:file  {optional} {type allowed -> image,video,document,spreadsheet,archive}
      */

        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('order_id', 'Order Id', 'trim|required|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            $order_id = $this->input->post('order_id', true);

            $order = fetch_details('orders', ['id' => $order_id], 'id');
            if (empty($order)) {
                $this->response['error'] = true;
                $this->response['message'] = "Order not found!";
                $this->response['data'] = [];
                print_r(json_encode($this->response));
                return false;
            }
            if (!file_exists(FCPATH . DIRECT_BANK_TRANSFER_IMG_PATH)) {
                mkdir(FCPATH . DIRECT_BANK_TRANSFER_IMG_PATH, 0777);
            }

            $temp_array = array();
            $files = $_FILES;
            $images_new_name_arr = array();
            $images_info_error = "";
            $allowed_media_types = implode('|', allowed_media_types());
            $config = [
                'upload_path' => FCPATH . DIRECT_BANK_TRANSFER_IMG_PATH,
                'allowed_types' => $allowed_media_types,
                'max_size' => 8000,
            ];
            if (!empty($_FILES['attachments']['name'][0]) && isset($_FILES['attachments']['name'])) {
                $other_image_cnt = count($_FILES['attachments']['name']);
                $other_img = $this->upload;
                $other_img->initialize($config);

                for ($i = 0; $i < $other_image_cnt; $i++) {

                    if (!empty($_FILES['attachments']['name'][$i])) {

                        $_FILES['temp_image']['name'] = $files['attachments']['name'][$i];
                        $_FILES['temp_image']['type'] = $files['attachments']['type'][$i];
                        $_FILES['temp_image']['tmp_name'] = $files['attachments']['tmp_name'][$i];
                        $_FILES['temp_image']['error'] = $files['attachments']['error'][$i];
                        $_FILES['temp_image']['size'] = $files['attachments']['size'][$i];
                        if (!$other_img->do_upload('temp_image')) {
                            $images_info_error = 'attachments :' . $images_info_error . ' ' . strip_tags($other_img->display_errors());
                        } else {
                            $temp_array = $other_img->data();
                            resize_review_images($temp_array, FCPATH . DIRECT_BANK_TRANSFER_IMG_PATH);
                            $images_new_name_arr[$i] = DIRECT_BANK_TRANSFER_IMG_PATH . $temp_array['file_name'];
                        }
                    } else {
                        $_FILES['temp_image']['name'] = $files['attachments']['name'][$i];
                        $_FILES['temp_image']['type'] = $files['attachments']['type'][$i];
                        $_FILES['temp_image']['tmp_name'] = $files['attachments']['tmp_name'][$i];
                        $_FILES['temp_image']['error'] = $files['attachments']['error'][$i];
                        $_FILES['temp_image']['size'] = $files['attachments']['size'][$i];
                        if (!$other_img->do_upload('temp_image')) {
                            $images_info_error = strip_tags($other_img->display_errors());
                        }
                    }
                }
                //Deleting Uploaded attachments if any overall error occured
                if ($images_info_error != NULL || !$this->form_validation->run()) {
                    if (isset($images_new_name_arr) && !empty($images_new_name_arr || !$this->form_validation->run())) {
                        foreach ($images_new_name_arr as $key => $val) {
                            unlink(FCPATH . DIRECT_BANK_TRANSFER_IMG_PATH . $images_new_name_arr[$key]);
                        }
                    }
                }
            }
            if ($images_info_error != NULL) {
                $this->response['error'] = true;
                $this->response['message'] = $images_info_error;
                print_r(json_encode($this->response));
                return false;
            }
            $data = array(
                'order_id' => $order_id,
                'attachments' => $images_new_name_arr,
            );
            if ($this->Order_model->add_bank_transfer_proof($data)) {
                //custom message
                $settings = get_settings('system_settings', true);
                $firebase_project_id = get_settings('firebase_project_id');
                $service_account_file = get_settings('service_account_file');
                $app_name = isset($settings['app_name']) && !empty($settings['app_name']) ? $settings['app_name'] : '';
                $user_roles = fetch_details("user_permissions", "", '*', '', '', '', '');
                foreach ($user_roles as $user) {
                    $user_res = fetch_details('users', ['id' => $user['user_id']], 'fcm_id,mobile,email,platform_type');
                    $user_mobile[0][] = $user_res[0]['mobile'];
                    $user_email[0][] = $user_res[0]['email'];

                    if ($user_res[0]['fcm_id'] != '') {
                        // Step 1: Group by platform
                        $groupedByPlatform = [];
                        foreach ($user_res as $item) {
                            $platform = $item['platform_type'];
                            $groupedByPlatform[$platform][] = $item['fcm_id'];
                        }

                        // Step 2: Chunk each platform group into arrays of 1000
                        $fcm_ids = [];
                        foreach ($groupedByPlatform as $platform => $fcmIds) {
                            $fcm_ids[$platform] = array_chunk($fcmIds, 1000);
                        }
                        $fcm_ids[0][] = $fcm_ids;
                    }
                }
                if (!empty($fcm_ids)) {
                    $custom_notification = fetch_details('custom_notifications', ['type' => "bank_transfer_proof"], '');
                    $hashtag_order_id = '< order_id >';
                    $hashtag_application_name = '< application_name >';
                    $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                    $hashtag = html_entity_decode($string);
                    $data = str_replace(array($hashtag_order_id, $hashtag_application_name), array($order_id, $app_name), $hashtag);
                    $message = output_escaping(trim($data, '"'));
                    $customer_msg = (!empty($custom_notification)) ? $message : "Hello Dear Admin you have new order bank transfer proof. Order ID #" . $order_id . ' please take note of it! Thank you. Regards ' . $app_name . '';
                    $fcmMsg = array(
                        'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "You have new order proof",
                        'body' => $customer_msg,
                        'type' => "bank_transfer_proof",
                    );
                    if (isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                        send_notification($fcmMsg, $fcm_ids, $fcmMsg);
                    }
                }
                $this->response['error'] = false;
                $this->response['message'] = 'Bank Transfer Proof Added Successfully!';
                $this->response['data'] = (!empty($data)) ? $data : [];
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Bank Transfer Proof Not Added';
                $this->response['data'] = (!empty($this->response['data'])) ? $this->response['data'] : [];
            }
        }
        print_r(json_encode($this->response));
    }

    //31.get_zipcodes
    public function get_zipcodes()
    {
        /*
              limit:10 {optional}
              offset:0 {optional}
              search:0 {optional}
          */
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('search', 'search', 'trim|xss_clean');


        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
        } else {

            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : '';
            $zipcodes = $this->Area_model->get_zipcodes($search, $limit, $offset);
            print_r(json_encode($zipcodes));
        }
    }

    //32. is_product_delivarable
    public function is_product_delivarable()
    {
        /*
        32. is_product_delivarable
            product_id:10 
            zipcode:132456 {{optional based on type of delivery}}
            city : Ahmedabad {{optional based on type of delivery}}
        */
        $this->form_validation->set_rules('product_id', 'Product Id', 'trim|numeric|xss_clean|required');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            echo json_encode($this->response);
        } else {
            $zipcode = $this->input->post('zipcode', true);
            $city = $this->input->post('city', true);
            if ($zipcode != '' && !empty($zipcode)) {
                $is_pincode = is_exist(['zipcode' => $zipcode], 'zipcodes');
                $product_id = $this->input->post('product_id', true);
                if ($is_pincode) {
                    $zipcode_id = fetch_details('zipcodes', ['zipcode' => $zipcode], 'id');
                    $is_available = is_product_delivarable($type = 'zipcode', $zipcode_id[0]['id'], $product_id);
                    if ($is_available) {
                        $this->response['error'] = false;
                        $this->response['message'] = 'Product is deliverable on ' . $zipcode . '.';
                        echo json_encode($this->response);
                        return false;
                    } else {
                        $this->response['error'] = true;
                        $this->response['message'] = 'Product is not deliverable on ' . $zipcode . '.';
                        echo json_encode($this->response);
                        return false;
                    }
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Product is not deliverable on ' . $zipcode . '.';
                    echo json_encode($this->response);
                    return false;
                }
            } else if ($city != '' && !empty($city)) {
                $is_city = is_exist(['name' => $city], 'cities');
                $product_id = $this->input->post('product_id', true);
                if ($is_city) {
                    $city_id = fetch_details('cities', ['name' => $city], 'id');
                    $is_available = is_product_delivarable($type = 'city', $city_id[0]['id'], $product_id);
                    if ($is_available) {
                        $_SESSION['valid_city'] = $city;
                        $this->response['error'] = false;
                        $this->response['message'] = 'Product is deliverable on ' . $city . '.';
                        echo json_encode($this->response);
                        return false;
                    } else {
                        $this->response['error'] = true;
                        $this->response['message'] = 'Product is not deliverable on ' . $city . '.';
                        echo json_encode($this->response);
                        return false;
                    }
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Product is not deliverable on ' . $city . '.';
                    echo json_encode($this->response);
                    return false;
                }
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'product Cannot deliver.';
                echo json_encode($this->response);
                return false;
            }
        }
    }

    //33. check_cart_products_delivarable
    public function check_cart_products_delivarable()
    {
        /*
        33. check_cart_products_delivarable
            address_id:10 
            user_id:12
        */
        $this->form_validation->set_rules('address_id', 'Address Id', 'trim|numeric|xss_clean|required');


        if (!$this->verify_token()) {
            return false;
        }

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            echo json_encode($this->response);
        } else {
            $settings = get_settings('shipping_method', true);
            $system_settings = get_settings('system_settings', true);
            $product_delivarable = array();
            $address_id = $this->input->post('address_id', true);

            $area_id = fetch_details('addresses', ['id' => $address_id], ['area_id', 'area', 'pincode', 'city']);
            $zipcode = $area_id[0]['pincode'];
            $zipcode_id = fetch_details('zipcodes', ['zipcode' => $zipcode], 'id')[0];

            $city = $area_id[0]['city'];
            $city_id = fetch_details('cities', ['name' => $city], 'id');
            $city_id = $city_id[0]['id'];

            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';

            if (!empty($area_id)) {

                if ((isset($settings['pincode_wise_deliverability']) && $settings['pincode_wise_deliverability'] == 1) || (isset($settings['local_shipping_method']) && isset($settings['shiprocket_shipping_method']) && $settings['local_shipping_method'] == 1 && $settings['shiprocket_shipping_method'] == 1)) {
                    $product_delivarable = check_cart_products_delivarable($user_id, $area_id[0]['area_id'], $zipcode, $zipcode_id['id']);
                }
                if (isset($settings['city_wise_deliverability']) && $settings['city_wise_deliverability'] == 1 && $settings['shiprocket_shipping_method'] != 1) {
                    $product_delivarable = check_cart_products_delivarable($user_id, $area_id[0]['area_id'], '', '', $city, $city_id);
                }
                if (!empty($product_delivarable)) {
                    $product_not_delivarable = array_filter($product_delivarable, function ($var) {
                        return ($var['is_deliverable'] == false && $var['product_id'] != null);
                    });
                    $product_not_delivarable = array_values($product_not_delivarable);
                    $product_delivarable = array_filter($product_delivarable, function ($var) {
                        return ($var['product_id'] != null);
                    });
                    if (!empty($product_not_delivarable)) {
                        $this->response['error'] = true;
                        $this->response['message'] = "Some of the item(s) are not delivarable on selected address. Try changing address or modify your cart items.";
                        $this->response['data'] = $product_delivarable;
                        print_r(json_encode($this->response));
                        return;
                    } else {
                        $this->response['error'] = false;
                        $this->response['message'] = "Product(s) are delivarable.";
                        $this->response['data'] = $product_delivarable;
                        print_r(json_encode($this->response));
                        return;
                    }
                } else {
                    $this->response['error'] = false;
                    $this->response['message'] = "Product(s) are delivarable";
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return;
                }
            } else {
                $this->response['error'] = true;
                $this->response['message'] = "Address not available.";
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return;
            }
        }
    }

    public function get_sellers()
    {
        /*
            zipcode:1  //{optional}
        */


        $this->form_validation->set_rules('zipcode_id', 'Zipcode ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('search', 'Search keyword', 'trim|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            if (isset($_POST['zipcode']) && !empty($_POST['zipcode'])) {
                $zipcode = $this->input->post('zipcode', true);
                $is_pincode = is_exist(['zipcode' => $zipcode], 'zipcodes');
                if ($is_pincode) {
                    $zipcode_ids = fetch_details('zipcodes', ['zipcode' => $zipcode], 'id');
                    $zipcode_id = $zipcode_ids[0]['id'];
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Sellers Not Found!';
                    $this->response['data'] = array();
                    echo json_encode($this->response);
                    return false;
                }
            } else {
                $zipcode_id = "";
            }

            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $filters['slug'] = (isset($_POST['slug']) && !empty($_POST['slug'])) ? $_POST['slug'] : '';
            $filters['top_rated_seller'] = (isset($_POST['top_rated_seller']) && !empty($_POST['top_rated_seller']) && ($_POST['top_rated_seller'] == 1)) ? 'top_rated_seller' : '';
            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'DESC';
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'u.id';

            $data = $this->Seller_model->get_sellers($zipcode_id, $limit, $offset, $sort, $order, $search, $filters);
            print_r(json_encode($data));
        }
    }
    public function get_promo_codes()
    {

        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('search', 'Search keyword', 'trim|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 25;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'DESC';
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'id';

            $this->response['error'] = false;
            $this->response['message'] = 'Promocodes retrived Successfully !';
            $result = $this->Promo_code_model->get_promo_codes($limit, $offset, $sort, $order, $search);
            $this->response['total'] = $result['total'];
            $this->response['offset'] = (isset($offset) && !empty($offset)) ? $offset : '0';
            $this->response['promo_codes'] = $result['data'];
            print_r(json_encode($this->response));
            return;
        }
    }

    /* add_product_faqs */
    public function add_product_faqs()
    {

        if (!$this->verify_token()) {
            return false;
        }


        $this->form_validation->set_rules('product_id', 'Product Id', 'trim|numeric|xss_clean|required');
        $this->form_validation->set_rules('question', 'Question', 'trim|xss_clean|required');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $product_id = $this->input->post('product_id', true);
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $question = $this->input->post('question', true);
            $user = fetch_users($user_id);
            if (empty($user)) {
                $this->response['error'] = true;
                $this->response['message'] = "User not found!";
                $this->response['data'] = [];
                print_r(json_encode($this->response));
                return false;
            }
            $data = array(
                'product_id' => $product_id,
                'user_id' => $user_id,
                'question' => $question,
            );

            $insert_id = $this->Product_faqs_model->add_product_faqs($data);
            if (!empty($insert_id)) {
                $result = $this->product_model->get_product_faqs($insert_id, $product_id, $user_id);
                $this->response['error'] = false;
                $this->response['message'] = 'FAQS added Successfully';
                $this->response['data'] = $result['data'];
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'FAQS Not Added';
                $this->response['data'] = (!empty($this->response['data'])) ? $this->response['data'] : [];
            }
            print_r(json_encode($this->response));
        }
    }

    /*  get_product_faqs */
    public function get_product_faqs()
    {
        /*
            id:2    // {optional}
            product_id:25   // {optional}
            user_id:1       // {optional}
            search : Search keyword // { optional }
            limit:25                // { default - 10 } optional
            offset:0                // { default - 0 } optional
            sort: id                // { default - id } optional
            order:DESC/ASC          // { default - DESC } optional
        */


        $this->form_validation->set_rules('id', 'FAQs ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('product_id', 'Product ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('user_id', 'User ID', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('search', 'Search keyword', 'trim|xss_clean');
        $this->form_validation->set_rules('sort', 'sort', 'trim|xss_clean');
        $this->form_validation->set_rules('limit', 'limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('order', 'order', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            $id = (isset($_POST['id']) && is_numeric($_POST['id']) && !empty(trim($_POST['id']))) ? $this->input->post('id', true) : "";
            $product_id = (isset($_POST['product_id']) && is_numeric($_POST['product_id']) && !empty(trim($_POST['product_id']))) ? $this->input->post('product_id', true) : "";
            $user_id = (isset($_POST['user_id']) && is_numeric($_POST['user_id']) && !empty(trim($_POST['user_id']))) ? $this->input->post('user_id', true) : "";
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) && !empty(trim($_POST['limit']))) ? $this->input->post('limit', true) : 10;
            $offset = (isset($_POST['offset']) && is_numeric($_POST['offset']) && !empty(trim($_POST['offset']))) ? $this->input->post('offset', true) : 0;
            $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $_POST['order'] : 'DESC';
            $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $_POST['sort'] : 'id';

            $result = $this->product_model->get_product_faqs($id, $product_id, $user_id, $search, $offset, $limit, $sort, $order);
            print_r(json_encode($result));
        }
    }

    //41.send_withdrawal_request
    public function send_withdrawal_request()
    {
        /* 
             user_id:15
             payment_address: 12343535
             amount: 560           
         */

        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('payment_address', 'Payment Address', 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|xss_clean|numeric|greater_than[0]');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        } else {
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $payment_address = $this->input->post('payment_address', true);
            $amount = $this->input->post('amount', true);
            $userData = fetch_details('users', ['id' => $user_id], 'balance');

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
                        $userData = fetch_details('users', ['id' => $user_id], 'balance');
                        $this->response['error'] = false;
                        $this->response['message'] = 'Withdrawal Request Sent Successfully';
                        $this->response['data'] = $userData[0]['balance'];
                    } else {
                        $this->response['error'] = true;
                        $this->response['message'] = 'Cannot sent Withdrawal Request.Please Try again later.';
                        $this->response['data'] = array();
                    }
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = 'You don\'t have enough balance to sent the withdraw request.';
                    $this->response['data'] = array();
                }

                print_r(json_encode($this->response));
            }
        }
    }


    public function get_paystack_link() {}

    //42.get_withdrawal_request
    public function get_withdrawal_request()
    {
        /* 
             user_id:15
             limit:10
             offset:10
         */

        if (!$this->verify_token()) {
            return false;
        }


        $this->form_validation->set_rules('limit', 'Limit', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('offset', 'Offset', 'trim|numeric|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        } else {
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $limit = ($this->input->post('limit', true)) ? $this->input->post('limit', true) : null;
            $offset = ($this->input->post('offset', true)) ? $this->input->post('offset', true) : null;
            $userData = fetch_details('payment_requests', ['user_id' => $user_id], '*', $limit, $offset);
            $this->response['error'] = false;
            $this->response['message'] = 'Withdrawal Request Retrieved Successfully';
            $this->response['data'] = $userData;
            $this->response['total'] = strval(count($userData));
            print_r(json_encode($this->response));
        }
    }

    public function paystack_webhook()
    {
        $this->load->library(['paystack']);

        $system_settings = get_settings('system_settings', true);
        $credentials = $this->paystack->get_credentials();

        $secret_key = $credentials['secret_key'];

        $request_body = file_get_contents('php://input');
        $event = json_decode($request_body, true);
        log_message('error', 'paystack Webhook --> ' . var_export($event, true));
        log_message('error', 'paystack Webhook SERVER Variable --> ' . var_export($_SERVER, true));


        if (!empty($event['data'])) {

            $txn_id = (isset($event['data']['reference'])) ? $event['data']['reference'] : "";
            if (isset($txn_id) && !empty($txn_id)) {
                $transaction = fetch_details('transactions', ['txn_id' => $txn_id], '*');
                if (!empty($transaction)) {
                    $order_id = $transaction[0]['order_id'];
                    $user_id = $transaction[0]['user_id'];
                } else {
                    $order_id = $event['data']['metadata']['order_id'];
                    $order_data = fetch_orders($order_id);
                    $user_id = $order_data['order_data'][0]['user_id'];
                }
            }
            $amount = $event['data']['amount'];
            $currency = $event['data']['currency'];
        } else {
            $order_id = 0;
            $amount = 0;
            $currency = (isset($event['data']['currency'])) ? $event['data']['currency'] : "";
        }

        /* Wallet refill has unique format for order ID - wallet-refill-user-{user_id}-{system_time}-{3 random_number}  */
        if (!is_numeric($order_id) && strpos($order_id, "wallet-refill-user") !== false) {

            $temp = explode("-", $order_id);
            if (isset($temp[3]) && is_numeric($temp[3]) && !empty($temp[3] && $temp[3] != '')) {
                $user_id = $temp[3];
            } else {
                $user_id = 0;
            }
        }

        // validate event do all at once to avoid timing attack
        if ($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $request_body, $secret_key)) {
            log_message('error', 'Paystack Webhook - Invalid Signature - JSON DATA --> ' . var_export($event, true));
            log_message('error', 'Paystack Server Variable invalid --> ' . var_export($_SERVER, true));
            exit();
        }

        if ($event['event'] == 'charge.success') {
            if (!empty($order_id)) { /* To do the wallet recharge if the order id is set in the pattern */

                if (strpos($order_id, "wallet-refill-user") !== false) {

                    $data['transaction_type'] = "wallet";
                    $data['user_id'] = $user_id;
                    $data['order_id'] = $order_id;
                    $data['type'] = "credit";
                    $data['txn_id'] = $txn_id;
                    $data['amount'] = $amount;
                    $data['status'] = "success";
                    $data['message'] = "Wallet refill successful";
                    $this->transaction_model->add_transaction($data);

                    $this->load->model('customer_model');
                    if ($this->customer_model->update_balance($amount, $user_id, 'add')) {
                        $response['error'] = false;
                        $response['transaction_status'] = $event['type'];
                        $response['message'] = "Wallet recharged successfully!";
                    } else {
                        $response['error'] = true;
                        $response['transaction_status'] = $event['type'];
                        $response['message'] = "Wallet could not be recharged!";

                        log_message('error', 'Paystack Webhook | wallet recharge failure --> ' . var_export($event, true));
                    }
                    echo json_encode($response);
                    return false;
                } else {

                    /* process the order and mark it as received */
                    $order = fetch_orders($order_id, false, false, false, false, false, false, false);
                    $firebase_project_id = get_settings('firebase_project_id');
                    $service_account_file = get_settings('service_account_file');
                    log_message('error', 'Paystack Webhook | order --> ' . var_export($order, true));

                    if (isset($order['order_data'][0]['user_id'])) {
                        $user = fetch_details('users', ['id' => $order['order_data'][0]['user_id']]);


                        $overall_total = array(
                            'total_amount' => $order['order_data'][0]['total'],
                            'delivery_charge' => $order['order_data'][0]['delivery_charge'],
                            'tax_amount' => $order['order_data'][0]['total_tax_amount'],
                            'tax_percentage' => $order['order_data'][0]['total_tax_percent'],
                            'discount' => $order['order_data'][0]['promo_discount'],
                            'wallet' => $order['order_data'][0]['wallet_balance'],
                            'final_total' => $order['order_data'][0]['final_total'],
                            'total_payable' => $order['order_data'][0]['total_payable'],
                            'otp' => $order['order_data'][0]['otp'],
                            'address' => $order['order_data'][0]['address'],
                            'payment_method' => $order['order_data'][0]['payment_method']
                        );


                        $overall_order_data = array(
                            'cart_data' => $order['order_data'][0]['order_items'],
                            'order_data' => $overall_total,
                            'subject' => 'Order received successfully',
                            'user_data' => $user[0],
                            'system_settings' => $system_settings,
                            'user_msg' => 'Hello, Dear ' . ucfirst($user[0]['username']) . ', We have received your order successfully. Your order summaries are as followed',
                            'otp_msg' => 'Here is your OTP. Please, give it to delivery boy only while getting your order.',
                        );


                        if (isset($user[0]['email']) && !empty($user[0]['email'])) {
                            send_mail($user[0]['email'], 'Order received successfully', $this->load->view('admin/pages/view/email-template.php', $overall_order_data, TRUE));
                        }

                        /* No need to add because the transaction is already added just update the transaction status */
                        if (!empty($transaction)) {
                            $transaction_id = $transaction[0]['id'];
                            update_details(['status' => 'success'], ['id' => $transaction_id], 'transactions');
                        } else {
                            /* add transaction of the payment */
                            $amount = ($event['data']['amount']);
                            $data = [
                                'transaction_type' => 'transaction',
                                'user_id' => $user_id,
                                'order_id' => $order_id,
                                'type' => 'paystack',
                                'txn_id' => $txn_id,
                                'amount' => $amount,
                                'status' => 'success',
                                'message' => 'order placed successfully',
                            ];
                            $this->transaction_model->add_transaction($data);
                        }



                        $status = json_encode(array(array('received', date("d-m-Y h:i:sa"))));
                        update_details(['status' => $status], ['order_id' => $order_id], 'order_items', false);
                        update_details(['active_status' => 'received'], ['order_id' => $order_id], 'order_items');

                        // place order custome notification on payment success

                        $custom_notification = fetch_details('custom_notifications', ['type' => "place_order"], '');
                        $hashtag_order_id = '< order_id >';
                        $string = json_encode($custom_notification[0]['title'], JSON_UNESCAPED_UNICODE);
                        $hashtag = html_entity_decode($string);
                        $data1 = str_replace($hashtag_order_id, $order_id, $hashtag);
                        $title = output_escaping(trim($data1, '"'));
                        $hashtag_application_name = '< application_name >';
                        $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                        $hashtag = html_entity_decode($string);
                        $data2 = str_replace($hashtag_application_name, $system_settings['app_name'], $hashtag);
                        $message = output_escaping(trim($data2, '"'));

                        $fcm_admin_subject = (!empty($custom_notification)) ? $title : 'New order placed ID #' . $order_id;
                        $fcm_admin_msg = (!empty($custom_notification)) ? $message : 'New order received for  ' . $system_settings['app_name'] . ' please process it.';
                        $user_fcm = fetch_details('user_fcm', ['user_id' => $user_id], 'fcm_id,platform_type');
                        // $user_fcm = fetch_details('users', ['id' => $user_id], 'fcm_id,platform_type');
                        // Step 1: Group by platform
                        $groupedByPlatform = [];
                        foreach ($user_fcm as $item) {
                            $platform = $item['platform_type'];
                            $groupedByPlatform[$platform][] = $item['fcm_id'];
                        }

                        // Step 2: Chunk each platform group into arrays of 1000
                        $fcm_ids = [];
                        foreach ($groupedByPlatform as $platform => $fcmIds) {
                            $fcm_ids[$platform] = array_chunk($fcmIds, 1000);
                        }
                        $user_fcm_id[0][] = $fcm_ids;
                        if (!empty($user_fcm_id) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                            $fcmMsg = array(
                                'title' => $fcm_admin_subject,
                                'body' => $fcm_admin_msg,
                                'type' => "place_order"
                            );
                            send_notification($fcmMsg, $user_fcm_id, $fcmMsg);
                        }

                        log_message('error', 'Paystack Webhook inner Success --> ' . var_export($event, true));
                    }
                    log_message('error', 'Paystack Webhook order Success --> ' . var_export($event, true));
                }
            } else {
                /* No order ID found / sending 304 error to payment gateway so it retries wenhook after sometime*/
                log_message('error', 'Paystack Webhook | Order id not found --> ' . var_export($event, true));
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(304)
                    ->set_output(json_encode(
                        array(
                            'message' => '304 Not Modified - order/transaction id not found',
                            'error' => true
                        )
                    ));
            }

            $response['error'] = false;
            $response['transaction_status'] = $event['event'];
            $response['message'] = "Transaction successfully done";
            log_message('error', 'Paystack Transaction Successfully --> ' . var_export($event, true));
            echo json_encode($response);
            return false;
        } else if ($event['event'] == 'charge.dispute.create') {
            if (!empty($order_id) && is_numeric($order_id)) {
                $order = fetch_orders($order_id, false, false, false, false, false, false, false);

                if ($order['order_data']['0']['active_status'] == 'received' || $order['order_data']['0']['active_status'] == 'processed') {
                    update_details(['active_status' => 'awaiting'], ['order_id' => $order_id], 'order_items');
                }

                if (!empty($transaction)) {
                    $transaction_id = $transaction[0]['id'];
                    update_details(['status' => 'pending'], ['id' => $transaction_id], 'transactions');
                }

                log_message('error', 'Paystack Transaction is Pending --> ' . var_export($event, true));
            }
        } else {

            if (!empty($order_id) && is_numeric($order_id)) {
                update_details(['active_status' => 'cancelled'], ['order_id' => $order_id], 'order_items');
            }
            /* No need to add because the transaction is already added just update the transaction status */
            if (!empty($transaction)) {
                $transaction_id = $transaction[0]['id'];
                update_details(['status' => 'failed'], ['id' => $transaction_id], 'transactions');
            }

            $response['error'] = true;
            $response['transaction_status'] = $event['event'];
            $response['message'] = "Transaction could not be detected.";
            log_message('error', 'Paystack Webhook | Transaction could not be detected --> ' . var_export($event, true));
            echo json_encode($response);
            return false;
        }
    }

    public function flutterwave_webhook()
    {
        $this->load->library(['Flutterwave']);
        $system_settings = get_settings('system_settings', true);
        $credentials = $this->flutterwave->get_credentials();

        $local_secret_hash = $credentials['secret_hash'];


        $request_body = file_get_contents('php://input');
        $event = json_decode($request_body, FALSE);

        log_message('error', 'Flutterwave Webhook --> ' . var_export($event, true));
        log_message('error', 'Flutterwave Webhook SERVER Variable --> ' . var_export($_SERVER, true));

        $verify_transaction_id = (isset($event->data->id) && !empty($event->data->id)) ? $event->data->id : '';
        $transaction = $this->flutterwave->verify_transaction($verify_transaction_id);

        if ($transaction) {

            if ((isset($event->data->id) && !empty($event->data->id)) || (isset($event->id) && !empty($event->id))) {

                // $txn_id = (isset($event->data->id) && !empty($event->data->id)) ? $event->data->id : '';
                $txn_id = (isset($event->data->tx_ref) && !empty($event->data->tx_ref)) ? $event->data->tx_ref : '';
                if (!empty($txn_id)) {
                    // $transaction = fetch_details('transactions', ['txn_id' => $txn_id], '*');
                    // if (!empty($transaction)) {
                    //     $order_id = $transaction[0]['order_id'];
                    //     $user_id = $transaction[0]['user_id'];
                    // } else {
                    //     $order_id = $txn_id;
                    // }

                    $temp_order_id = explode("-", $txn_id);
                    if (isset($temp_order_id[1]) && is_numeric($temp_order_id[1]) && !empty($temp_order_id[1] && $temp_order_id[1] != '')) {
                        $order_id = $temp_order_id[1];
                    } else {
                        $order_id = 0;
                    }
                }
                $amount = isset($event->data->amount) ? $event->data->amount : $event->amount;
                $currency = isset($event->data->currency) ? $event->data->currency : $event->currency;
                log_message('error', 'Flutterwave Webhook order_id --> ' . var_export($order_id, true));
            } else {
                $order_id = 0;
                $amount = 0;
                $currency = (isset($event->data->currency)) ? $event->data->currency : "";
                $balance_transaction = 0;
            }
            log_message('error', 'Flutterwave order id before 1 --> ' . var_export($order_id, true));

            /* Wallet refill has unique format for order ID - wallet-refill-user-{user_id}-{system_time}-{3 random_number}  */
            if (empty($order_id)) {
                $user_email = (!empty($event->data->customer->email) && isset($event->data->customer->email)) ? $event->data->customer->email : $event->customer->email;
                $user_id = fetch_details('users', ['email' => $user_email], 'id');
                $user_main_id = $user_id[0]['id'];
                $currTime = date('Y-m-d H:i:s');
                $order_id = 'wallet-refill-user-' . $user_main_id . '-' . $currTime . '-' . rand(10, 1000);
                log_message('error', 'user id --> ' . var_export($order_id, true));
            }

            log_message('error', 'Flutterwave order id before 2--> ' . var_export($order_id, true));
            if (!is_numeric($order_id) && strpos($order_id, "wallet-refill-user") !== false) {
                log_message('error', 'Flutterwave order id after--> ' . var_export($order_id, true));

                $temp = explode("-", $order_id);
                if (isset($temp[3]) && is_numeric($temp[3]) && !empty($temp[3] && $temp[3] != '')) {
                    $user_id = $temp[3];
                } else {
                    $user_id = 0;
                }
            }


            $signature = (isset($_SERVER['HTTP_VERIF_HASH'])) ? $_SERVER['HTTP_VERIF_HASH'] : '';

            /* comparing our local signature with received signature */
            if (empty($signature) || $signature != $local_secret_hash) {
                log_message('error', 'FlutterWave Webhook - Invalid Signature - JSON DATA --> ' . var_export($event, true));
                log_message('error', 'FlutterWave Server Variable invalid --> ' . var_export($_SERVER, true));
            }


            if ((isset($event->event) && $event->event == 'charge.completed' && $event->data->status == 'successful') || ($event->status == 'successful')) {
                if (!empty($order_id)) {
                    /* To do the wallet recharge if the order id is set in the patter */
                    if (strpos($order_id, "wallet-refill-user") !== false) {
                        $data['transaction_type'] = "wallet";
                        $data['user_id'] = $user_id;
                        $data['order_id'] = $order_id;
                        $data['type'] = "credit";
                        $data['txn_id'] = $txn_id;
                        $data['amount'] = $amount;
                        $data['status'] = "success";
                        $data['message'] = "Wallet refill successful";
                        $this->transaction_model->add_transaction($data);
                        log_message('error', ' wallet transaction data --> ' . var_export($data, true));

                        $this->load->model('customer_model');
                        if ($this->customer_model->update_balance($amount, $user_id, 'add')) {
                            $response['error'] = false;
                            $response['transaction_status'] = $event->data->status;
                            $response['message'] = "Wallet recharged successfully!";
                        } else {
                            $response['error'] = true;
                            $response['transaction_status'] = $event->data->status;
                            $response['message'] = "Wallet could not be recharged!";
                            log_message('error', 'Flutterwave Webhook | wallet recharge failure --> ' . var_export($event, true));
                        }
                        echo json_encode($response);
                        return false;
                    } else {

                        /* process the order and mark it as received */
                        $order = fetch_orders($order_id, false, false, false, false, false, false, false);
                        $firebase_project_id = get_settings('firebase_project_id');
                        $service_account_file = get_settings('service_account_file');
                        log_message('error', 'Flutterwave Webhook user id --> ' . var_export($order['order_data'][0]['user_id'], true));

                        if (isset($order['order_data'][0]['user_id'])) {
                            $user = fetch_details('users', ['id' => $order['order_data'][0]['user_id']]);
                            $overall_total = array(
                                'total_amount' => $order['order_data'][0]['total'],
                                'delivery_charge' => $order['order_data'][0]['delivery_charge'],
                                'tax_amount' => $order['order_data'][0]['total_tax_amount'],
                                'tax_percentage' => $order['order_data'][0]['total_tax_percent'],
                                'discount' => $order['order_data'][0]['promo_discount'],
                                'wallet' => $order['order_data'][0]['wallet_balance'],
                                'final_total' => $order['order_data'][0]['final_total'],
                                'total_payable' => $order['order_data'][0]['total_payable'],
                                'otp' => $order['order_data'][0]['otp'],
                                'address' => $order['order_data'][0]['address'],
                                'payment_method' => $order['order_data'][0]['payment_method']
                            );

                            $overall_order_data = array(
                                'cart_data' => $order['order_data'][0]['order_items'],
                                'order_data' => $overall_total,
                                'subject' => 'Order received successfully',
                                'user_data' => $user[0],
                                'system_settings' => $system_settings,
                                'user_msg' => 'Hello, Dear ' . ucfirst($user[0]['username']) . ', We have received your order successfully. Your order summaries are as followed',
                                'otp_msg' => 'Here is your OTP. Please, give it to delivery boy only while getting your order.',
                            );


                            if (isset($user[0]['email']) && !empty($user[0]['email'])) {
                                send_mail($user[0]['email'], 'Order received successfully', $this->load->view('admin/pages/view/email-template.php', $overall_order_data, TRUE));
                            }
                            /* No need to add because the transaction is already added just update the transaction status */
                            // if (!empty($transaction)) {
                            //     $transaction_id = $transaction[0]['id'];
                            //     update_details(['status' => 'success'], ['txn_id' => $txn_id], 'transactions');
                            // }

                            $transaction_data = fetch_details('transactions', ['order_id' => $order_id], '*');
                            log_message('error', 'Transaction Data for Order -  transaction data--> ' . var_export($transaction_data, true));

                            if (empty($transaction_data)) {
                                $data['transaction_type'] = "order";
                                $data['user_id'] = $order['order_data'][0]['user_id'];
                                $data['order_id'] = $order_id;
                                $data['type'] = "flutterwave";
                                $data['txn_id'] = $event->data->id;
                                $data['amount'] = $overall_total['final_total'];
                                $data['status'] = "success";
                                $data['message'] = "Order placed Successful";
                                log_message('error', 'Transaction Data for Order -  transaction data--> ' . var_export($data, true));
                                $this->transaction_model->add_transaction($data);
                            } else {
                                update_details(['status' => 'success'], ['txn_id' => $txn_id], 'transactions');
                            }

                            /* add transaction of the payment */

                            update_details(['active_status' => 'received'], ['order_id' => $order_id], 'order_items');

                            $status = json_encode(array(array('received', date("d-m-Y h:i:sa"))));
                            update_details(['status' => $status], ['order_id' => $order_id], 'order_items', false);

                            // place order custome notification on payment success

                            $custom_notification = fetch_details('custom_notifications', ['type' => "place_order"], '');
                            $hashtag_order_id = '< order_id >';
                            $string = json_encode($custom_notification[0]['title'], JSON_UNESCAPED_UNICODE);
                            $hashtag = html_entity_decode($string);
                            $data1 = str_replace($hashtag_order_id, $order_id, $hashtag);
                            $title = output_escaping(trim($data1, '"'));
                            $hashtag_application_name = '< application_name >';
                            $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                            $hashtag = html_entity_decode($string);
                            $data2 = str_replace($hashtag_application_name, $system_settings['app_name'], $hashtag);
                            $message = output_escaping(trim($data2, '"'));

                            $fcm_admin_subject = (!empty($custom_notification)) ? $title : 'New order placed ID #' . $order_id;
                            $fcm_admin_msg = (!empty($custom_notification)) ? $message : 'New order received for  ' . $system_settings['app_name'] . ' please process it.';
                            $user_fcm = fetch_details('user_fcm', ['user_id' => $user_id], 'fcm_id,platform_type');
                            // $user_fcm = fetch_details('users', ['id' => $user_id], 'fcm_id,platform_type');
                            // Step 1: Group by platform
                            $groupedByPlatform = [];
                            foreach ($user_fcm as $item) {
                                $platform = $item['platform_type'];
                                $groupedByPlatform[$platform][] = $item['fcm_id'];
                            }

                            // Step 2: Chunk each platform group into arrays of 1000
                            $fcm_ids = [];
                            foreach ($groupedByPlatform as $platform => $fcmIds) {
                                $fcm_ids[$platform] = array_chunk($fcmIds, 1000);
                            }
                            $user_fcm_id[0][] = $fcm_ids;
                            if (!empty($user_fcm_id) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                                $fcmMsg = array(
                                    'title' => $fcm_admin_subject,
                                    'body' => $fcm_admin_msg,
                                    'type' => "place_order"
                                );
                                send_notification($fcmMsg, $user_fcm_id, $fcmMsg);
                            }

                            log_message('error', 'Flutterwave Webhook inner Success --> ' . var_export($event, true));
                        }
                        log_message('error', 'Flutterwave Webhook outer Success --> ' . var_export($event, true));
                    }
                } else {
                    /* No order ID found */
                    log_message('error', 'Flutterwave Webhook | No Order ID found --> ' . var_export($event, true));
                    return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(304)
                        ->set_output(json_encode(
                            array(
                                'message' => '304 Not Modified - order/transaction id not found',
                                'error' => true
                            )
                        ));
                }

                $response['error'] = false;
                $response['transaction_status'] = $event->data->status;
                $response['message'] = "Transaction successfully done";
                log_message('error', 'Flutterwave Transaction Successfully --> ' . var_export($event, true));
                echo json_encode($response);
                return false;
            } else {
                $response['error'] = true;
                $response['transaction_status'] = $event->data->status;
                $response['message'] = "Transaction could not be detected.";
                log_message('error', 'Flutterwave Webhook | Transaction could not be detected --> ' . var_export($event, true));
                echo json_encode($response);
                return false;
            }
        } else {
            $response['error'] = true;
            $response['transaction_status'] = $transaction->status;
            $response['message'] =  $transaction->message;
            log_message('error', 'Flutterwave Webhook | Transaction could not be detected --> ' . var_export($event, true));
            echo json_encode($response);
            return false;
        }
    }

    //rozarpay_create_order

    public function razorpay_create_order()
    {
        /* 
             order_id:15
         */

        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('order_id', 'Order ID', 'required|trim|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $order_id = (isset($_POST['order_id'])) ? $_POST['order_id'] : null;
            $order = fetch_orders($order_id, false, false, false, false, false, false, false);
            $settings = get_settings('system_settings', true);

            if (!empty($order) && !empty($settings)) {
                $currency = $settings['supported_locals'];
                $price = $order['order_data'][0]['total_payable'];
                $amount = intval($price * 100);

                $this->load->library(['razorpay']);
                $create_order = $this->razorpay->create_order($amount, $order_id, $currency);
                if (!empty($create_order)) {
                    $this->response['error'] = false;
                    $this->response['message'] = "razorpay order created";
                    $this->response['data'] = $create_order;
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = "razorpay order not created";
                    $this->response['data'] = array();
                }
            } else {
                $this->response['error'] = true;
                $this->response['message'] = "details not found";
                $this->response['data'] = array();
            }
            print_r(json_encode($this->response));
            return;
        }
    }


    /*
        status: cancelled / returned
        order_id:1201 // this is order_item_id
    */
    public function update_order_status()
    {
        if (!$this->verify_token()) {
            return false;
        }
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean');
        $this->form_validation->set_rules('order_id', 'Order item id', 'trim|required|numeric|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {

            $all_status = ['received', 'processed', 'shipped', 'delivered', 'cancelled', 'returned'];
            if (!in_array(strtolower($_POST['status']), $all_status)) {
                $this->response['error'] = true;
                $this->response['message'] = "Invalid Status supplied";
                print_r(json_encode($this->response));
                return false;
            }


            // check for bank receipt if available
            $order_method = fetch_details('orders', ['id' => $_POST['order_id']], 'payment_method');
            if ($order_method[0]['payment_method'] == 'bank_transfer') {
                $bank_receipt = fetch_details('order_bank_transfer', ['order_id' => $_POST['order_id']]);
                $transaction_status = fetch_details('transactions', ['order_id' => $_POST['order_id']], 'status');
                if ($_POST['status'] != "cancelled" && (empty($bank_receipt) || strtolower($transaction_status[0]['status']) != 'success')) {
                    $this->response['error'] = true;
                    $this->response['message'] = "Order Status can not update, Bank verification is remain from transactions.";
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }
            }

            $this->response = $this->order_model->update_order_status($_POST['order_id'], trim($_POST['status']), true);

            $order_details = fetch_details('orders', ['id' => $_POST['order_id']], 'id, payment_method');

            if (trim($_POST['status']) != 'returned' && $order_details[0]['payment_method'] != 'COD' && $order_details[0]['payment_method'] != 'cod' && $this->response['error'] == false) {
                process_refund($_POST['order_id'], $_POST['status'], 'order_items');
            }
            if (trim($_POST['status']) == 'cancelled' && $order_details[0]['payment_method'] != 'COD' && $order_details[0]['payment_method'] != 'cod') {
                $data = fetch_details('order_items', ['id' => $_POST['order_id']], 'product_variant_id,quantity');
                update_stock($data[0]['product_variant_id'], $data[0]['quantity'], 'plus');
            }
        }
        print_r(json_encode($this->response));
        return false;
    }

    public function create_midtrans_transaction()
    {
        if (!$this->verify_token()) {
            return false;
        }
        $this->form_validation->set_rules('order_id', 'Order id', 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|xss_clean');
        $order_id = $_POST['order_id'];
        $amount = $_POST['amount'];
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            $this->load->library(['midtrans']);
            $transaction = $this->midtrans->create_transaction($order_id, $amount);
            if (!empty($transaction)) {
                $this->response['error'] = false;
                $this->response['message'] = "Token generate successfully";
                $this->response['data'] = json_decode($transaction['body'], 1);
            } else {
                $this->response['error'] = true;
                $this->response['message'] = "Token generation Failed";
                $this->response['data'] = array();
            }
        }
        print_r(json_encode($this->response));
    }
    public function get_midtrans_transaction_status()
    {

        if (!$this->verify_token()) {
            return false;
        }
        $this->form_validation->set_rules('order_id', 'Order id', 'trim|required|xss_clean');
        $order_id = $this->input->post('order_id', true);

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {

            $this->load->library(['midtrans']);
            $create_order = $this->midtrans->get_transaction_status($order_id);
            if (!empty($create_order)) {
                $this->response['error'] = false;
                $this->response['message'] = "Transaction Retrived Successfully";
                $this->response['data'] = $create_order;
            } else {
                $this->response['error'] = true;
                $this->response['message'] = "Transaction Not Retrived";
                $this->response['data'] = array();
            }
        }
        print_r(json_encode($this->response));
    }

    public function midtrans_payment_process()
    {

        $midtransInfo = $this->input->get();

        if (!empty($midtransInfo) && isset($_GET['status_code']) && ($_GET['status_code']) == 200 && isset($_GET['transaction_status']) && strtolower($_GET['transaction_status']) == 'capture') {
            $response['error'] = false;
            $response['message'] = "Success, Credit card transaction is successful";
            $response['data'] = $midtransInfo;
        } elseif (!empty($midtransInfo) && isset($_GET['transaction_status']) && strtolower($_GET['transaction_status']) == "pending") {
            $response['error'] = false;
            $response['message'] = "Waiting customer to finish transaction order_id: " . $_GET['order_id'];
            $response['data'] = $midtransInfo;
        } elseif (!empty($midtransInfo) && isset($_GET['transaction_status']) && strtolower($_GET['transaction_status']) == "deny") {
            $response['error'] = false;
            $response['message'] = "Your payment of order_id: " . $_GET['order_id'] . " is denied";
            $response['data'] = $midtransInfo;
        } else {
            $response['error'] = true;
            $response['message'] = "Payment Cancelled / Declined ";
            $response['data'] = (isset($_GET)) ? $this->input->get() : "";
        }
        print_r(json_encode($response));
    }

    public function midtrans_webhook()
    {
        $system_settings = get_settings('system_settings', true);
        $this->form_validation->set_rules('order_id', 'Order id', 'trim|required|xss_clean');
        $order_id = $this->input->post('order_id', true);

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        } else {
            $this->load->library(['midtrans']);
            $transaction_response = $this->midtrans->get_transaction_status($order_id);
            $txn_order_id = ($transaction_response['order_id']) ? $transaction_response['order_id'] : "";
            if (!empty($txn_order_id)) {
                $transaction = fetch_details('transactions', ['order_id' => $txn_order_id], '*');
                if (isset($transaction) && !empty($transaction)) {
                    $order_id = $transaction[0]['order_id'];
                    $user_id = $transaction[0]['user_id'];
                } else {
                    $order_id = $transaction_response['order_id'];
                    $order_data = fetch_orders($order_id);
                    $user_id = $order_data['order_data'][0]['user_id'];
                }
            }



            if ($order_id != $transaction_response['order_id']) {
                $response['error'] = true;
                $response['message'] = "Order id is not matched with transaction order id.";
                echo json_encode($response);
                return false;
            }
            $res = fetch_details('orders', ['id' => $order_id], 'id');

            if (!empty($res) && isset($res[0]['id']) && is_numeric($res[0]['id'])) {
                $db_order_id = $res[0]['id'];
                if ($transaction_response['order_id'] != $db_order_id) {
                    $response['error'] = true;
                    $response['message'] = "Order id is not matched with orders.";
                    echo json_encode($response);
                    return false;
                } else {
                    $item_id = fetch_details('order_items', ['order_id' => $order_id], 'id');
                    $order_item_ids = array_column($item_id, "id");
                }
            }
            $type = $transaction_response['payment_type'];
            $gross_amount = $transaction_response['gross_amount'];
            if ($transaction_response['transaction_status'] == 'capture') {

                if ($transaction_response['fraud_status'] == 'challenge') {
                    $response['error'] = false;
                    $response['transaction_status'] = $transaction_response['fraud_status'];
                    $response['message'] = "Transaction order_id: " . $order_id . " is challenged by FDS";
                    log_message('error', "Transaction order_id: " . $order_id . " is challenged by FDS");
                    return false;
                } else {
                    if (strpos($order_id, "wallet-refill-user") !== false) {

                        if (!is_numeric($order_id) && strpos($order_id, "wallet-refill-user") !== false) {
                            $temp = explode("-", $order_id);
                            if (isset($temp[3]) && is_numeric($temp[3]) && !empty($temp[3] && $temp[3] != '')) {
                                $user_id = $temp[3];
                            } else {
                                $user_id = 0;
                            }
                        }
                        $data['transaction_type'] = "wallet";
                        $data['user_id'] = $user_id;
                        $data['order_id'] = $order_id;
                        $data['type'] = "credit";
                        $data['txn_id'] = '';
                        $data['amount'] = $gross_amount;
                        $data['status'] = "success";
                        $data['message'] = "Wallet refill successful";
                        log_message('error', 'Midtrans user ID -  transaction data--> ' . var_export($data, true));
                        $this->transaction_model->add_transaction($data);
                        log_message('error', 'Midtrans user ID - Add transaction ');

                        $this->load->model('customer_model');
                        if ($this->customer_model->update_balance($gross_amount, $user_id, 'add')) {
                            $response['error'] = false;
                            $response['transaction_status'] = $transaction_response['transaction_status'];
                            $response['message'] = "Wallet recharged successfully!";
                            log_message('error', 'Midtrans user ID - Wallet recharged successfully --> ' . var_export($order_id, true));
                        } else {
                            $response['error'] = true;
                            $response['transaction_status'] = $transaction_response['transaction_status'];
                            $response['message'] = "Wallet could not be recharged!";
                            log_message('error', 'Midtrans Webhook | wallet recharge failure --> ' . var_export($transaction_response['transaction_status'], true));
                        }
                        echo json_encode($response);
                        return false;
                    } else {

                        //update order and mark it as receive
                        $order = fetch_orders($order_id, false, false, false, false, false, false, false);
                        $firebase_project_id = get_settings('firebase_project_id');
                        $service_account_file = get_settings('service_account_file');
                        if (isset($order['order_data'][0]['user_id'])) {
                            $user = fetch_details('users', ['id' => $order['order_data'][0]['user_id']]);

                            $overall_total = array(
                                'total_amount' => $order['order_data'][0]['total'],
                                'delivery_charge' => $order['order_data'][0]['delivery_charge'],
                                'tax_amount' => $order['order_data'][0]['total_tax_amount'],
                                'tax_percentage' => $order['order_data'][0]['total_tax_percent'],
                                'discount' => $order['order_data'][0]['promo_discount'],
                                'wallet' => $order['order_data'][0]['wallet_balance'],
                                'final_total' => $order['order_data'][0]['final_total'],
                                'total_payable' => $order['order_data'][0]['total_payable'],
                                'otp' => $order['order_data'][0]['otp'],
                                'address' => $order['order_data'][0]['address'],
                                'payment_method' => $order['order_data'][0]['payment_method']
                            );
                            $overall_order_data = array(
                                'cart_data' => $order['order_data'][0]['order_items'],
                                'order_data' => $overall_total,
                                'subject' => 'Order received successfully',
                                'user_data' => $user[0],
                                'system_settings' => $system_settings,
                                'user_msg' => 'Hello, Dear ' . ucfirst($user[0]['username']) . ', We have received your order successfully. Your order summaries are as followed',
                                'otp_msg' => 'Here is your OTP. Please, give it to delivery boy only while getting your order.',
                            );

                            if (isset($user[0]['email']) && !empty($user[0]['email'])) {
                                send_mail($user[0]['email'], 'Order received successfully', $this->load->view('admin/pages/view/email-template.php', $overall_order_data, TRUE));
                            }

                            /* No need to add because the transaction is already added just update the transaction status */
                            if (!empty($transaction)) {

                                $transaction_id = $transaction[0]['id'];
                                update_details(['status' => 'success'], ['id' => $transaction_id], 'transactions');
                            } else {

                                /* add transaction of the payment */
                                $amount = ($transaction_response['gross_amount']);
                                $data = [
                                    'transaction_type' => 'transaction',
                                    'user_id' => $user_id,
                                    'order_id' => $order_id,
                                    'type' => 'midtrans',
                                    'txn_id' => '',
                                    'amount' => $amount,
                                    'status' => 'success',
                                    'message' => 'order placed successfully',
                                ];
                                $this->transaction_model->add_transaction($data);
                            }
                            update_details(['active_status' => 'received'], ['order_id' => $order_id], 'order_items');

                            $status = json_encode(array(array('received', date("d-m-Y h:i:sa"))));
                            update_details(['status' => $status], ['order_id' => $order_id], 'order_items', false);
                            // place order custome notification on payment success
                            $custom_notification = fetch_details('custom_notifications', ['type' => "place_order"], '');
                            $hashtag_order_id = '< order_id >';
                            $string = json_encode($custom_notification[0]['title'], JSON_UNESCAPED_UNICODE);
                            $hashtag = html_entity_decode($string);
                            $data1 = str_replace($hashtag_order_id, $order_id, $hashtag);
                            $title = output_escaping(trim($data1, '"'));
                            $hashtag_application_name = '< application_name >';
                            $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                            $hashtag = html_entity_decode($string);
                            $data2 = str_replace($hashtag_application_name, $system_settings['app_name'], $hashtag);
                            $message = output_escaping(trim($data2, '"'));

                            $fcm_admin_subject = (!empty($custom_notification)) ? $title : 'New order placed ID #' . $order_id;
                            $fcm_admin_msg = (!empty($custom_notification)) ? $message : 'New order received for  ' . $system_settings['app_name'] . ' please process it.';
                            $user_fcm = fetch_details('user_fcm', ['user_id' => $user_id], 'fcm_id,platform_type');
                            // $user_fcm = fetch_details('users', ['id' => $user_id], 'fcm_id,platform_type');
                            // Step 1: Group by platform
                            $groupedByPlatform = [];
                            foreach ($user_fcm as $item) {
                                $platform = $item['platform_type'];
                                $groupedByPlatform[$platform][] = $item['fcm_id'];
                            }

                            // Step 2: Chunk each platform group into arrays of 1000
                            $fcm_ids = [];
                            foreach ($groupedByPlatform as $platform => $fcmIds) {
                                $fcm_ids[$platform] = array_chunk($fcmIds, 1000);
                            }
                            $user_fcm_id[0][] = $fcm_ids;
                            if (!empty($user_fcm_id) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                                $fcmMsg = array(
                                    'title' => $fcm_admin_subject,
                                    'body' => $fcm_admin_msg,
                                    'type' => "place_order"
                                );
                                send_notification($fcmMsg, $user_fcm_id, $fcmMsg);
                            }
                        } else {
                            log_message('error', 'Order id not found');
                            /* No order ID found */
                        }
                        $response['error'] = false;
                        $response['transaction_status'] = $transaction_response['transaction_status'];
                        $response['message'] = "Transaction successfully done using " . $type;
                        log_message('error', "Transaction successfully done using: " . $type);
                        echo json_encode($response);
                        return false;
                    }
                }
            } else if ($transaction_response['transaction_status'] == 'pending') {
                $response['error'] = false;
                $response['transaction_status'] = $transaction_response['transaction_status'];
                $response['message'] = "Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;
                log_message('error', "Waiting customer to finish transaction order_id: " . $order_id . " using " . $type);
                echo json_encode($response);
                return false;
            } else if ($transaction_response['transaction_status'] == 'deny') {

                $response['error'] = true;
                $response['transaction_status'] = $transaction_response['transaction_status'];
                $response['message'] = "Payment using " . $type . " for transaction order_id: " . $order_id . " is denied. And" . $transaction_response['status_message'];
                log_message('error', "Payment using " . $type . " for transaction order_id: " . $order_id . " is denied. And" . $transaction_response['status_message']);
                echo json_encode($response);
                return false;
            } else if ($transaction_response['transaction_status'] == 'expire') {
                $response['error'] = true;
                $response['transaction_status'] = $transaction_response['transaction_status'];
                $response['message'] = "Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
                log_message('error', "Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.");
                echo json_encode($response);
                return false;
            } else if ($transaction_response['transaction_status'] == 'cancel') {
                $response['error'] = true;
                $response['transaction_status'] = $transaction_response['transaction_status'];
                $response['message'] = "Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
                log_message('error', "Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.");
                echo json_encode($response);
                return false;
            }
        }
    }

    public function sign_up()
    {

        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'trim|xss_clean|valid_email');
        $this->form_validation->set_rules('fcm_id', 'FCM ID', 'trim|xss_clean');
        $this->form_validation->set_rules('type', 'Type', 'trim|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        }
        $email = (isset($_POST['email']) && (trim($_POST['email'])) != "") ? $this->input->post('email', true) : '';
        $mobile = (isset($_POST['mobile']) && (trim($_POST['mobile'])) != "") ? $this->input->post('mobile', true) : '';
        $type = (isset($_POST['type']) && (trim($_POST['type'])) != "") ? $this->input->post('type', true) : '';
        $where = (!empty($mobile)) ? ['mobile' => $mobile] : ['email' => $email];
        $res = $this->db->select("id,mobile,email,image,username")
            ->where($where)
            ->where_not_in('type', 'phone')
            ->get('`users`')->result_array();

        if (!empty($res)) {
            $is_exist = (!empty($mobile)) ? ['mobile' => $mobile] : ['email' => $email];
            $where = (!empty($mobile)) ? ['mobile' => $mobile] : ['email' => $email];
            if (is_exist($is_exist, 'users')) {
                if (isset($_POST['fcm_id']) && !empty(($_POST['fcm_id']))) {
                    update_details(['fcm_id' => $this->input->post('fcm_id', true)], $where, 'users');
                }
                if (isset($_POST['type']) && !empty(($_POST['type']))) {
                    update_details(['type' => $this->input->post('type', true)], $where, 'users');
                }
                if (isset($_POST['username']) && !empty(($_POST['username']))) {
                    update_details(['username' => $this->input->post('username', true)], $where, 'users');
                }
                $data = $this->db->select("*")
                    ->where($where)
                    ->where('type', $type)
                    ->where_not_in('type', 'phone')
                    ->get('`users`')->result_array();

                $existing_token = ($data[0]['apikey'] !== null && !empty($data[0]['apikey'])) ? $data[0]['apikey'] : "";
                unset($data[0]['password']);

                if ($existing_token == '') {
                    $token = generate_token($this->input->post('mobile'), $this->input->post('email'));
                    update_details(['apikey' => $token], ['email' => $this->input->post('email')], "users");
                } else if (!empty($existing_token)) {

                    $api_keys = JWT_SECRET_KEY;
                    try {
                        $get_token = $this->jwt->decode($existing_token, new Key($api_keys, 'HS256'));
                        $error = false;
                        $flag = false;
                    } catch (Exception $e) {
                        $token = generate_token($this->input->post('mobile'), $this->input->post('email'));
                        update_details(['apikey' => $token], ['email' => $this->input->post('email')], "users");
                        $error = true;
                        $flag = false;
                        $message = 'Token Expired, new token generated';
                        $status_code = 403;
                    }
                }


                if (empty($data[0]['image']) || file_exists(FCPATH . USER_IMG_PATH . $data[0]['image']) == FALSE) {
                    $data[0]['image'] = base_url() . NO_USER_IMAGE;
                } else {
                    $data[0]['image'] = base_url() . USER_IMG_PATH . $data[0]['image'];
                }
                if (!empty($data[0]['username'])) {
                    $data[0]['username'] = $data[0]['username'];
                }
                $data = array_map(function ($value) {
                    return $value === NULL ? "" : $value;
                }, $data[0]);

                if (isset($data['active']) && !empty($data) && $data['active'] == 0) {
                    $response['error'] = true;
                    $response['message'] = 'You are not allowed to login your account is inactive.';
                    $response['data'] = [];
                } else {
                    //if the login is successful
                    $response['error'] = false;
                    $response['message'] = "User login successfully";
                    $response['token'] = $existing_token !== "" ? $existing_token : $token;
                    $response['data'] = $data;
                }
                echo json_encode($response);
                return false;
            } else {
                $response['error'] = true;
                $response['message'] = 'User does not exists !';
                $response['data'] = array();
                echo json_encode($response);
                return false;
            }
        } else {
            //register

            $this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');
            $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('email', 'Mail', 'trim|xss_clean|valid_email');
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim|xss_clean|max_length[16]|numeric|is_unique[users.mobile]', array('is_unique' => ' The mobile number is already registered . Please login'));
            $this->form_validation->set_rules('country_code', 'Country Code', 'trim|xss_clean');
            $this->form_validation->set_rules('fcm_id', 'Fcm Id', 'trim|xss_clean');
            $this->form_validation->set_rules('referral_code', 'Referral code', 'trim|is_unique[users.referral_code]|xss_clean');
            $this->form_validation->set_rules('friends_code', 'Friends code', 'trim|xss_clean');
            $this->form_validation->set_rules('latitude', 'Latitude', 'trim|xss_clean|numeric');
            $this->form_validation->set_rules('longitude', 'Longitude', 'trim|xss_clean|numeric');

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['message'] = strip_tags(validation_errors());
                $this->response['data'] = array();
            } else {
                $settings = get_settings('system_settings', true);
                if (isset($_POST['friends_code']) && !empty($_POST['friends_code'])) {
                    $refer_earn_bonus_times = $settings['refer_earn_bonus_times'];
                    $used_refer_and_earn = fetch_details('users', ['friends_code' => $_POST['friends_code']]);

                    if (count($used_refer_and_earn) < $refer_earn_bonus_times) {

                        if (!is_exist(['referral_code' => $_POST['friends_code']], 'users')) {
                            $response["error"] = true;
                            $response["message"] = "Invalid friends code!";
                            $response["data"] = array();
                            echo json_encode($response);
                            return false;
                        }
                    } else {
                        $response["error"] = true;
                        $response["message"] = "Code already used for $refer_earn_bonus_times times!";
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }
                }

                $wallet_balance = (isset($settings['welcome_wallet_balance_on']) && $settings['welcome_wallet_balance_on'] && isset($settings['wallet_balance_amount']) && $settings['wallet_balance_amount'] != '') ? $settings['wallet_balance_amount'] : '0';
                $referral_generate_code = substr(str_shuffle(str_repeat("AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz1234567890", 8)), 0, 8);
                $additional_data = [
                    'username' => $this->input->post('name', true),
                    'mobile' => $mobile,
                    'email' => $email,
                    'type' => $this->input->post('type', true),
                    'country_code' => $this->input->post('country_code', true),
                    'fcm_id' => $this->input->post('fcm_id', true),
                    'referral_code' => $referral_generate_code,
                    'friends_code' => $this->input->post('friends_code', true),
                    'latitude' => $this->input->post('latitude', true),
                    'longitude' => $this->input->post('longitude', true),
                    'balance' => $wallet_balance,
                    'active' => 1
                ];

                $res = insert_details($additional_data, "users");
                $user_id = $this->db->insert_id();


                $data = $this->db->select('u.id,u.username,u.email,u.mobile,c.name as city_name,a.name as area_name')->where($where)->join('cities c', 'c.id=u.city', 'left')->join('areas a', 'a.city_id=c.id', 'left')->group_by('email')->get('users u')->result_array();

                if ($wallet_balance != 0 && $wallet_balance != '0') {
                    $this->load->model("transaction_model");
                    $transaction_data = [
                        'transaction_type' => "wallet",
                        'user_id' => $data[0]['id'],
                        'order_id' => 0,
                        'type' => "credit",
                        'txn_id' => "",
                        'amount' => $wallet_balance,
                        'status' => "success",
                        'message' => "welcome wallet balance added successfully",
                    ];
                    $this->transaction_model->add_transaction($transaction_data);
                }

                $referal_id = fetch_details('users', ['referral_code' => $this->input->post('friends_code', true)], 'id,referral_code');

                $referal_code = $this->input->post('friends_code', true);
                if (isset($referal_code) && !empty($referal_code)) {
                    $refer_and_earn_data = [
                        'referal_id' => $referal_id[0]['id'],
                        'user_id' => $data[0]['id'],
                        'identifier' => $data[0]['email'],
                        'referal_code' => $this->input->post('friends_code', true),
                    ];
                    insert_details($refer_and_earn_data, 'refer_and_earn');
                }
                $user_details = [
                    'user_id' => $user_id,
                    'group_id' => 2,
                ];

                $token = generate_token($this->input->post('mobile'), $this->input->post('email'));
                update_details(['apikey' => $token], ['email' => $this->input->post('email')], "users");

                insert_details($user_details, "users_groups");
                if ($res != FALSE) {
                    $where = (!empty($mobile)) ? ['mobile' => $mobile] : ['email' => $email];
                    $where = (!empty($user_id)) && isset($user_id) ? ['id' => $user_id] : '';

                    update_details(['active' => 1], $where, 'users');
                    $data = fetch_details('users', $where);

                    unset($data[0]['password']);
                    unset($data[0]['apikey']);

                    $data = array_map(function ($value) {
                        return $value === NULL ? "" : $value;
                    }, $data[0]);

                    $this->response['error'] = false;
                    $this->response['token'] = $token;
                    $this->response['message'] = 'Registered Successfully';
                    $this->response['data'] = $data;
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Registered Failed';
                    $this->response['data'] = array();
                }
            }
            print_r(json_encode($this->response));
        }
    }

    public function download_link_hash()
    {
        /*
           order_item_id : 100
           user_id : 100
        */
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('order_item_id', 'Order Item Id', 'trim|required|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        }
        $order_item_id = (isset($_POST['order_item_id']) && (trim($_POST['order_item_id'])) != "") ? $this->input->post('order_item_id', true) : '';
        $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
        $oreder_item_data = fetch_details('order_items', ['id' => $order_item_id], '*');
        $transaction_data = fetch_details('transactions', ['order_id' => $oreder_item_data[0]['order_id']], 'status');

        if (isset($order_item_id) && $order_item_id != '' && isset($user_id) && $user_id != '') {

            if (isset($oreder_item_data) && !empty($oreder_item_data) && isset($transaction_data) && !empty($transaction_data)) {
                if ($order_item_id == $oreder_item_data[0]['id'] && $user_id == $oreder_item_data[0]['user_id']) {
                    if (strtolower($transaction_data[0]['status']) == 'success' || strtolower($transaction_data[0]['status'] == 'received')) {
                        $file = $oreder_item_data[0]['hash_link'];
                        $file = explode("?", $file);
                        $url = $file[0];
                        if (preg_match('(http:|https:)', $url) === 1) {
                            $file_path = $url;
                        } else {
                            $file_path = base_url($url);
                        }
                        $this->response['error'] = false;
                        $this->response['message'] = 'Download fetch sucessfully';
                        $this->response['data'] = $file_path;
                    } else {
                        $this->response['error'] = true;
                        $this->response['message'] = 'Transaction is not successful for this order';
                    }
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = 'You are not authorized to download this file';
                }
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'No data found for this order';
            }
            print_r(json_encode($this->response));
        }
    }

    public function check_shiprocket_serviceability()
    {
        /* 
            product_variant_id:15
            delivery_pincode:370001
         */

        $this->form_validation->set_rules('product_variant_id', 'Product Variant Id', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('delivery_pincode', 'Delivery Pincode', 'trim|numeric|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $product_variant_id = (isset($_POST['product_variant_id'])) ? $_POST['product_variant_id'] : 0;
            $product_id = fetch_details('product_variants', ['id' => $product_variant_id], 'product_id');
            $product_id = $product_id[0]['product_id'];
            $zipcode = (isset($_POST['delivery_pincode'])) ? $_POST['delivery_pincode'] : 0;
            $settings = get_settings('shipping_method', true);
            $is_pincode = is_exist(['zipcode' => $zipcode], 'zipcodes');
            if ($is_pincode) {
                $zipcode_id = fetch_details('zipcodes', ['zipcode' => $zipcode], 'id');
                $is_available = is_product_delivarable($type = 'zipcode', $zipcode_id[0]['id'], $product_id);
                if ($is_available) {
                    $_SESSION['valid_zipcode'] = $zipcode;
                    $this->response['error'] = false;
                    $this->response['message'] = 'Product is deliverable on ' . $zipcode;
                    echo json_encode($this->response);
                    return false;
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Product is not deliverable on' . $zipcode;
                    echo json_encode($this->response);
                    return false;
                }
            } else {
                $product_variant_id = (isset($_POST['product_variant_id'])) ? $_POST['product_variant_id'] : 0;
                $delivery_pincode = (isset($_POST['delivery_pincode'])) ? $_POST['delivery_pincode'] : 0;
                $settings = get_settings('shipping_method', true);

                if (isset($settings['shiprocket_shipping_method']) && $settings['shiprocket_shipping_method'] == 1) {

                    if (!empty($product_variant_id) && !empty($delivery_pincode)) {
                        $this->load->library(['Shiprocket']);
                        $min_days = $max_days = $delivery_charge_with_cod = $delivery_charge_without_cod = 0;
                        $product_variant_detail = fetch_details('product_variants', ['id' => $product_variant_id], 'product_id,weight');
                        $product_detail = fetch_details('products', ['id' => $product_variant_detail[0]['product_id']], 'pickup_location');

                        if (isset($product_variant_detail[0]['weight']) && $product_variant_detail[0]['weight'] > 15) {
                            $this->response['error'] = true;
                            $this->response['message'] = "More than 15kg weight is not allow";
                            $this->response['data'] = array();
                        } else {

                            $pickup_postcode = fetch_details('pickup_locations', ['pickup_location' => $product_detail[0]['pickup_location']], 'pin_code');
                            $availibility_data = [
                                'pickup_postcode' => $pickup_postcode[0]['pin_code'],
                                'delivery_postcode' => $delivery_pincode,
                                'cod' => 0,
                                'weight' => $product_variant_detail[0]['weight'],
                            ];


                            $check_deliveribility = $this->shiprocket->check_serviceability($availibility_data);

                            $shiprocket_data = shiprocket_recomended_data($check_deliveribility);

                            $availibility_data_with_cod = [
                                'pickup_postcode' => $pickup_postcode[0]['pin_code'],
                                'delivery_postcode' => $delivery_pincode,
                                'cod' => 1,
                                'weight' => $product_variant_detail[0]['weight'],
                            ];

                            $check_deliveribility_with_cod = $this->shiprocket->check_serviceability($availibility_data_with_cod);
                            $shiprocket_data_with_cod = shiprocket_recomended_data($check_deliveribility_with_cod);

                            if (isset($check_deliveribility['status_code']) && $check_deliveribility['status_code'] == 422) {
                                $this->response['error'] = true;
                                $this->response['message'] = 'Invalid Delivery Pincode';
                                echo json_encode($this->response);
                                return false;
                            } else {
                                $estimate_data = [
                                    'pickup_availability' => $shiprocket_data['pickup_availability'],
                                    'courier_name' => $shiprocket_data['courier_name'],
                                    'delivery_charge_with_cod' => $shiprocket_data_with_cod['rate'],
                                    'delivery_charge_without_cod' => $shiprocket_data['rate'],
                                    'estimate_date' => $shiprocket_data['etd'],
                                    'estimate_days' => $shiprocket_data['estimated_delivery_days'],
                                ];

                                if (isset($check_deliveribility['status']) && $check_deliveribility['status'] == 200 && !empty($check_deliveribility['data']['available_courier_companies'])) {
                                    $estimate_date = $check_deliveribility['data']['available_courier_companies'][0]['etd'];
                                    $_SESSION['valid_zipcode'] = $zipcode;
                                    $this->response['error'] = false;
                                    $this->response['message'] = 'Product is deliverable by ' . $estimate_date;
                                    $this->response['data'] = $estimate_data;
                                    print_r(json_encode($this->response));
                                    return false;
                                } else {
                                    $this->response['error'] = true;
                                    $this->response['message'] = 'Product is not deliverable on ' . $zipcode;
                                    echo json_encode($this->response);
                                    return false;
                                }
                            }
                        }
                    } else {
                        $this->response['error'] = true;
                        $this->response['message'] = "details not found";
                        $this->response['data'] = array();
                    }
                    print_r(json_encode($this->response));
                    return;
                }
            }
        }
    }
    public function check_user_status()
    {
        /* 
           user_id:15
           
         */
        if (!$this->verify_token()) {
            return false;
        }

        $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
        $user_data = fetch_details('users', ['id' => $user_id], 'active');
        if (isset($user_data) && !empty($user_data)) {
            $this->response['error'] = false;
            $this->response['message'] = 'Data retrived successfully';
            $this->response['data'] = $user_data;
        } else {
            $this->response['error'] = true;
            $this->response['message'] = 'Data not retrived';
            $this->response['data'] = array();
        }
        print_r(json_encode($this->response));
    }

    /* 
        user_id:15
    */
    public function delete_social_account()
    {

        if (!$this->verify_token()) {
            return false;
        }

        $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';

        $user_data = fetch_details('users', ['id' => $user_id], 'id,username');
        if ($user_data) {
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
            } else if ($user_group[0]['group_id'] == '4') {
                $login = $this->ion_auth->login($this->input->post('mobile'), $this->input->post('password'), false);
                if ($login) {
                    $delete = array(
                        "media" => 0,
                        "payment_requests" => 0,
                        "products" => 0,
                        "product_attributes" => 0,
                        "order_items" => 0,
                        "orders" => 0,
                        "order_bank_transfer" => 0,
                        "seller_commission" => 0,
                        "seller_data" => 0,
                    );
                    $seller_media = fetch_details('seller_data', ['user_id' => $user_id], 'id,logo,national_identity_card,address_proof');
                    if (!empty($seller_media)) {
                        (unlink(FCPATH . $seller_media[0]['logo']) != null) && !empty(unlink(FCPATH . $seller_media[0]['logo'])) ? unlink(FCPATH . $seller_media[0]['logo']) : "";
                        (unlink(FCPATH . $seller_media[0]['national_identity_card']) != null) && !empty(unlink(FCPATH . $seller_media[0]['national_identity_card'])) ? unlink(FCPATH . $seller_media[0]['national_identity_card']) : "";
                        (unlink(FCPATH . $seller_media[0]['address_proof']) != null) && !empty(unlink(FCPATH . $seller_media[0]['address_proof'])) ? unlink(unlink(FCPATH . $seller_media[0]['address_proof'])) : "";
                    }
                    if (update_details(['seller_id' => 0], ['seller_id' => $user_id], 'media')) {
                        $delete['media'] = 1;
                    }
                    /* check for retur requesst if seller's product have */
                    $return_req = $this->db->where(['p.seller_id' => $user_id])->join('products p', 'p.id=rr.product_id')->get('return_requests rr')->result_array();
                    if (!empty($return_req)) {
                        $this->response['error'] = true;
                        $this->response['message'] = 'Seller could not be deleted.Either found some order items which has return request.Finalize those before deleting it';
                        print_r(json_encode($this->response));
                        return;
                        exit();
                    }
                    $pr_ids = fetch_details("products", ['seller_id' => $user_id], "id");
                    if (delete_details(['seller_id' => $user_id], 'products')) {
                        $delete['products'] = 1;
                    }
                    foreach ($pr_ids as $row) {
                        if (delete_details(['product_id' => $row['id']], 'product_attributes')) {
                            $delete['product_attributes'] = 1;
                        }
                    }
                    /* check order items */
                    $order_items = fetch_details('order_items', ['seller_id' => $user_id], 'id,order_id');
                    if (delete_details(['seller_id' => $user_id], 'order_items')) {
                        $delete['order_items'] = 1;
                    }
                    if (!empty($order_items)) {
                        $res_order_id = array_values(array_unique(array_column($order_items, "order_id")));
                        for ($i = 0; $i < count($res_order_id); $i++) {
                            $orders = $this->db->where('oi.seller_id != ' . $user_id . ' and oi.order_id=' . $res_order_id[$i])->join('orders o', 'o.id=oi.order_id', 'right')->get('order_items oi')->result_array();
                            if (empty($orders)) {
                                // delete orders
                                if (delete_details(['seller_id' => $user_id], 'order_items')) {
                                    $delete['order_items'] = 1;
                                }
                                if (delete_details(['id' => $res_order_id[$i]], 'orders')) {
                                    $delete['orders'] = 1;
                                }
                                if (delete_details(['order_id' => $res_order_id[$i]], 'order_bank_transfer')) {
                                    $delete['order_bank_transfer'] = 1;
                                }
                            }
                        }
                    } else {
                        $delete['order_items'] = 1;
                        $delete['orders'] = 1;
                        $delete['order_bank_transfer'] = 1;
                    }
                    if (!empty($res_order_id)) {

                        if (delete_details(['id' => $res_order_id[$i]], 'orders')) {
                            $delete['orders'] = 1;
                        }
                    } else {
                        $delete['orders'] = 1;
                    }
                    if (delete_details(['seller_id' => $user_id], 'seller_commission')) {
                        $delete['seller_commission'] = 1;
                    }
                    if (delete_details(['user_id' => $user_id], 'seller_data')) {
                        $delete['seller_data'] = 1;
                    }
                    if (isset($delete['seller_data']) && !empty($delete['seller_data']) && isset($delete['seller_commission']) && !empty($delete['seller_commission'])) {
                        $deleted = TRUE;
                    }
                }
                delete_details(['id' => $user_id], 'users');
                delete_details(['user_id' => $user_id], 'users_groups');
                $response['error'] = false;
                $response['message'] = 'Seller Deleted Succesfully';
            } else if ($user_group[0]['group_id'] == '3') {
                delete_details(['id' => $user_id], 'users');
                delete_details(['user_id' => $user_id], 'users_groups');
                $response['error'] = false;
                $response['message'] = 'Delivery Boy  Deleted Succesfully';
            } else {
                $response['error'] = true;
                $response['message'] = 'Details Does\'s Match';
            }
        } else {
            $response['error'] = true;
            $response['message'] = 'User Not Found';
        }

        print_r(json_encode($response));
        return;
    }
    public function instamojo_webview()
    {
        /* 
            user_id:73
            order_id:101 
            amount:100 // only pass while wallet refill
        */



        $this->form_validation->set_rules('order_id', 'Order ID', 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|numeric|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        } else {
            $this->load->library('instamojo');
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';

            $order_id = $_POST['order_id'];
            $amount = $_POST['amount'];
            $user = fetch_users($user_id);
            $order = fetch_orders($order_id, $user_id);


            if (empty($user) || !isset($user[0]['mobile'])) {
                $response['error'] = true;
                $response['message'] = "User not found!";
                print_r(json_encode($response));
                return false;
            }

            $data = [
                'purpose' => $order_id,
                'amount' => isset($amount) && !empty($amount) && $amount != 0 ? $amount : $order['order_data'][0]['total_payable'],
                'buyer_name' => $user[0]['username'],
                'email' => isset($user[0]['email']) && !empty($user[0]['email']) ? $user[0]['email'] : 'foo@example.com',
                'phone' => (isset($user[0]['mobile']) && !empty($user[0]['mobile']) && $user[0]['mobile'] != '1020304050') ? $user[0]['mobile'] : '9999999999',
                'redirect_url' => base_url('admin/webhook/instamojo_success_url'),
            ];

            $payment = $this->instamojo->payment_requests($data);

            if ($payment) {
                $response['error'] = false;
                $response['message'] = 'Payment request sent successfully';
                $response['data'] = $payment;
            } else {
                $response['error'] = true;
                $response['message'] = 'Payment request not sent';
                $response['data'] = [];
            }
            print_r(json_encode($response));
            return;
        }
    }

    public function phonepe_app()
    {
        /* 
            type:wallet/cart  //required
            transation_id:741258 //required
            mobile:123456478   // required for wallet
            amount:5200   // required for wallet
            order_id:1642 // required for cart
        */

        if (!$this->verify_token()) {
            return false;
        }


        $this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');
        $this->form_validation->set_rules('transation_id', 'Transation ID', 'trim|required|xss_clean');
        $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
        if ($_POST['type'] == 'wallet') {
            $this->form_validation->set_rules(
                'mobile',
                'Mobile',
                'trim|numeric|required|xss_clean'
            );
            $this->form_validation->set_rules('amount', 'Amount', 'trim|numeric|required|xss_clean');
        } else {
            $this->form_validation->set_rules('order_id', 'order id', 'trim|required|xss_clean');
        }
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        } else {
            $this->load->library('phonepe');

            if ($_POST['type'] == 'wallet') {
                $amount = $_POST['amount'];
                $mobile = $_POST['mobile'];
                $transaction_id = $_POST['transation_id'];
                $data = array(
                    'amount' => $amount * 100,
                    'merchantTransactionId' => $_POST['order_id'],
                    'mobile' => $mobile,
                    'order_id' => $transaction_id
                );
                // $res = $this->phonepe->phonepe_checksum($data);
                $res = $this->phonepe->phonepe_checksum_v2($data);
                $this->response['error'] = false;
                $this->response['data'] = $res;
                print_r(json_encode($this->response));
                return;
            } else {
                $order_details = fetch_orders($this->input->post('order_id'), $this->input->post('user_id'), null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 0);

                if ($order_details['total'] != 0) {
                    $amount = $order_details['order_data'][0]['total_payable'];
                    $mobile = $order_details['order_data'][0]['mobile'];
                    // $transaction_id = $_POST['transation_id'];
                    // $data = array(
                    //     'final_total' => $amount,
                    //     'mobile' => $mobile,
                    //     'order_id' => $transaction_id
                    // );

                    $transaction_id = time() . "" . rand("100", "999");
                    $data = array(
                        // 'merchantTransactionId' => $transaction_id,
                        'merchantTransactionId' => $_POST['order_id'],
                        'merchantUserId' => $user_id,
                        'amount' => $amount * 100,
                        'mobileNumber' => $mobile
                    );
                    $res = $this->phonepe->phonepe_checksum_v2($data);
                    // $res = $this->phonepe->pay_v2($data, 'app');
                    $this->response['error'] = false;
                    $this->response['data'] = $res;
                } else {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Order Not Found';
                }
                print_r(json_encode($this->response));
                return;
            }
        }
    }

    public function get_brands_data()
    {
        $this->form_validation->set_rules('search', 'search', 'trim|xss_clean');
        $this->form_validation->set_rules('offset', 'Offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('limit', 'Limit', 'trim|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        } else {
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $offset = ($this->input->post('offset', true)) ? $this->input->post('offset', true) : 0;
            $limit = ($this->input->post('limit', true)) ? $this->input->post('limit', true) : 25;
            $result = $this->product_model->get_brand_list($search, $offset, $limit);
            print_r(json_encode($result));
        }
    }

    public function get_predefiend_reasons()
    {
        $this->form_validation->set_rules('search', 'search', 'trim|xss_clean');
        $this->form_validation->set_rules('offset', 'Offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('limit', 'Limit', 'trim|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        } else {
            $search = (isset($_POST['search']) && !empty(trim($_POST['search']))) ? $this->input->post('search', true) : "";
            $offset = ($this->input->post('offset', true)) ? $this->input->post('offset', true) : 0;
            $limit = ($this->input->post('limit', true)) ? $this->input->post('limit', true) : 25;
            $result = fetch_details('return_reasons', '', '*', $limit, $offset, $search);

            if (!empty($result)) {
                for ($i = 0; $i < count($result); $i++) {
                    $result[$i]['image'] = base_url() . $result[$i]['image'];
                }
                $this->response['error'] = false;
                $this->response['message'] = "Return reasons fetched successfully";
                $this->response['data'] = $result;
                print_r(json_encode($this->response));
            } else {
                $this->response['error'] = false;
                $this->response['message'] = "No result found";
                $this->response['data'] = array();
                print_r(json_encode($this->response));
            }
        }
    }

    public function get_blogs()
    {
        $this->form_validation->set_rules('search', 'search', 'trim|xss_clean');
        $this->form_validation->set_rules('offset', 'Offset', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('limit', 'Limit', 'trim|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
        } else {
            $offset = ($this->input->post('offset', true)) ? $this->input->post('offset', true) : 0;
            $limit = ($this->input->post('limit', true)) ? $this->input->post('limit', true) : 25;
            $category_id = ($this->input->post('category_id')) ? $this->input->post('category_id', true) : NULL;
            $blog_search = ($this->input->post('blog_search')) ? $this->input->post('blog_search', true) : '';
            $result = $this->blog_model->get_blogs($offset, $limit, null, null, $blog_search, $category_id, 1);

            print_r(json_encode($result));
        }
    }

    public function send_contact_us_email()
    {
        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');
        $this->form_validation->set_rules('subject', 'Subject', 'trim|required|xss_clean');
        $this->form_validation->set_rules('message', 'Message', 'trim|required|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = validation_errors();
            print_r(json_encode($this->response));
            return false;
        }

        $username = $this->input->post('username', true);
        $email = $this->input->post('email', true);
        $subject = $this->input->post('subject', true);
        $message = $this->input->post('message', true);
        $web_settings = get_settings('web_settings', true);
        $to = $web_settings['support_email'];
        $email_message = array(
            'username' => 'Hello, Dear <b>' . ucfirst($username) . '</b>, ',
            'subject' => $subject,
            'email' => 'email : ' . $email,
            'message' => $message
        );
        $mail = send_mail($to, $subject, $this->load->view('admin/pages/view/contact-email-template', $email_message, TRUE));
        if ($mail['error'] == true) {
            $this->response['error'] = true;
            $this->response['message'] = "Cannot send mail. Please try again later.";
            $this->response['data'] = $mail['message'];
            echo json_encode($this->response);
            return false;
        } else {
            $this->response['error'] = false;
            $this->response['message'] = 'Mail sent successfully. We will get back to you soon.';
            $this->response['data'] = array();
            echo json_encode($this->response);
            return false;
        }
    }

    public function phonepe_web()
    {
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|xss_clean');
        $this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');
        $this->form_validation->set_rules('order_id', 'Order id', 'trim|required|xss_clean');
        $this->form_validation->set_rules('redirect_url', 'Redirect URL', 'trim|required|xss_clean');
        if ($_POST['type'] == 'wallet') {
            $this->form_validation->set_rules(
                'mobile',
                'Mobile',
                'trim|numeric|required|xss_clean'
            );
        }

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        } else {

            $this->load->library('phonepe');

            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $redirect_url = $this->input->post('redirect_url');

            if ($_POST['type'] == 'wallet') {
                $overall_amount = $_POST['amount'];
                $amount = $overall_amount;
                $user_id = $this->data['user']->id;
                $settings = get_settings('system_settings', true);
                $order_id = $_POST['order_id'];
                $transation_id = $_POST['order_id'];
                $mobile = $this->data['user']->mobile;

                $data = array(
                    'merchantTransactionId' => $transation_id,
                    'merchantUserId' => $user_id,
                    'amount' => $amount * 100,
                    'redirectUrl' => $redirect_url,
                    'redirectMode' => 'POST',
                    'callbackUrl' => base_url("admin/webhook/phonepe_webhook"),
                    'mobileNumber' => $mobile,
                );
                $res = $this->phonepe->pay($data);
                $this->response['error'] = false;
                $this->response['message'] = 'trasaction initiated successfully';
                $this->response['data'] = $res;
                $this->response['data']['order_id'] = $order_id;
                print_r(json_encode($this->response));
                return;
            } else {
                $_POST['user_id'] = $user_id;
                $order_id = $this->input->post('order_id');
                $cart = fetch_orders($order_id);
                $wallet_balance = fetch_details('users', 'id=' . $user_id, 'balance');
                $wallet_balance = $wallet_balance[0]['balance'];
                $overall_amount = $cart['order_data'][0]['total_payable'];
                $mobile = $cart['mobile'];
                if ($_POST['wallet_used'] == 1 && $wallet_balance > 0) {
                    $overall_amount = $overall_amount - $wallet_balance;
                }
                if (!empty($_POST['promo_code'])) {
                    $validate = validate_promo_code($_POST['promo_code'], $user_id, $cart['total_arr']);
                    if ($validate['error']) {
                        $this->response['error'] = true;
                        $this->response['message'] = $validate['message'];
                        print_r(json_encode($this->response));
                        return false;
                    } else {
                        $overall_amount = $overall_amount - $validate['data'][0]['final_discount'];
                    }
                }
                $amount = intval($overall_amount);
                $user_id = $user_id;
                $transation_id = time() . "" . rand("100", "999");
                $data = array(
                    'merchantTransactionId' => $transation_id,
                    'merchantUserId' => $user_id,
                    'amount' => $amount * 100,
                    'redirectUrl' => $redirect_url,
                    'redirectMode' => 'POST',
                    'callbackUrl' => base_url("admin/webhook/phonepe_webhook"),
                    'mobileNumber' => $mobile,
                );

                $res = $this->phonepe->pay($data);

                $this->response['error'] = false;
                $this->response['data'] = $res;
                $this->response['transaction_id'] = $transation_id;
                $this->response['message'] = $res['message'];
                $this->response['url'] = ($res['data']['instrumentResponse']['redirectInfo']['url']) ? $res['data']['instrumentResponse']['redirectInfo']['url'] : " ";

                print_r(json_encode($this->response));
                return;
            }
        }
    }

    public function stripe_payment_intent()
    {
        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('address_id', 'Address id', 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|xss_clean');
        $this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');
        $this->form_validation->set_rules('order_id', 'Order id', 'trim|required|xss_clean');
        if ($_POST['type'] == 'wallet') {
            $this->form_validation->set_rules(
                'mobile',
                'Mobile',
                'trim|numeric|required|xss_clean'
            );
        }

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        } else {
            $this->load->library('stripe');
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';

            $user_details = fetch_details('users', ['id' => $user_id], 'username,email');
            $address = fetch_details('addresses', ['user_id' => $user_id, 'is_default' => 1], 'address,pincode,city,state,country');
            if (!empty($address)) {
                $customer_address = $address[0];
            } else {
                $address = fetch_details('addresses', ['user_id' => $user_id], 'address,pincode,city,state,country');
                $customer_address = $address[0];
            }

            $customer_data = [];
            $customer_data['name'] = $user_details[0]['username'];
            $customer_data['email'] = $user_details[0]['email'];
            $customer_data['line1'] = $customer_address['address'];
            $customer_data['postal_code'] = $customer_address['pincode'];
            $customer_data['city'] = $customer_address['city'];
            $customer_data['state'] = $customer_address['state'];
            $customer_data['country'] = $customer_address['country'];
            $cus = $this->stripe->create_customer($customer_data);

            if ($_POST['type'] == 'wallet') {

                $order = $this->stripe->create_payment_intent(array('amount' => ($_POST['amount'] * 100), "metadata[order_id]" => ($_POST['order_id'])), $cus['id']);
                $this->response['client_secret'] = $order['client_secret'];
                $this->response['id'] = $order['id'];
                print_r(json_encode($this->response));
                return false;
            } else {
                $cart = get_cart_total($this->data['user']->id, false, '0', $_POST['address_id']);
                $overall_amount = $cart['overall_amount'];

                $order = $this->stripe->create_payment_intent(array('amount' => ($overall_amount * 100)), $cus['id']);
                $this->response['client_secret'] = $order['client_secret'];
                $this->response['id'] = $order['id'];
                $this->response['error'] = false;
                $this->response['data'] = $order;
                $this->response['message'] = $order['message'];

                print_r(json_encode($this->response));
                return;
            }
        }
    }

    public function my_fatoorah_payment_intent()
    {

        if (!$this->verify_token()) {
            return false;
        }

        $this->form_validation->set_rules('address_id', 'Address id', 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|xss_clean');
        $this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');
        $this->form_validation->set_rules('my_fatoorah_order_id', 'Order id', 'trim|required|xss_clean');
        $this->form_validation->set_rules('redirect_url', 'Redirect URL', 'trim|required|xss_clean');
        if ($_POST['type'] == 'wallet') {
            $this->form_validation->set_rules(
                'mobile',
                'Mobile',
                'trim|numeric|required|xss_clean'
            );
        }

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        } else {
            $this->load->library('my_fatoorah');
            $user_id = isset($this->user_details['id']) && $this->user_details['id'] !== null ? $this->user_details['id'] : '';
            $redirect_url = $this->input->post('redirect_url');


            if ($_POST['type'] == 'wallet') {
                $order_id = $_POST['order_id'];
                $total_payable = $_POST['amount'];

                $order = $this->my_fatoorah->ExecutePayment($total_payable, 2, ["UserDefinedField" => $order_id]);
                if (!empty($order->Data)) {
                    $this->response['error'] = false;
                    $this->response['PaymentURL'] = $order->Data->PaymentURL;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = "success";
                    print_r(json_encode($this->response));
                    return false;
                }
            } else {
                $order_id = $_POST['my_fatoorah_order_id'];
                $amount = fetch_details('orders', ['id' => $order_id], 'total_payable');
                $total_payable = $amount[0]['total_payable'];
                $cart = get_cart_total($this->data['user']->id, false, '0', $_POST['address_id']);
                $user = fetch_details('users', ['id' => $user_id], 'username,email,mobile');

                $wallet_balance = fetch_details('users', 'id=' . $this->data['user']->id, 'balance');
                $wallet_balance = $wallet_balance[0]['balance'];
                $overall_amount = $cart['overall_amount'];

                $order = $this->my_fatoorah->ExecutePayment($overall_amount, 2);
                if (!empty($order->Data)) {
                    $this->response['error'] = false;
                    $this->response['PaymentURL'] = $order->Data->PaymentURL;
                    $this->response['message'] = "success";
                    print_r(json_encode($this->response));
                    return false;
                }
            }
        }
    }
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setting extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model('Setting_model');

        if (!has_permissions('read', 'settings')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        } else {
            $this->session->set_flashdata('authorize_flag', "");
        }
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'settings';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Settings | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Settings  | ' . $settings['app_name'];
            $this->data['timezone'] = timezone_list();
            $this->data['logo'] = get_settings('logo');
            $this->data['favicon'] = get_settings('favicon');
            $this->data['settings'] = get_settings('system_settings', true);
            $this->data['shiprocket_settings'] = get_settings('shipping_method', true);
            
            $this->data['currency'] = get_settings('currency');
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }


    public function update_system_settings()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('update', 'settings'), PERMISSION_ERROR_MSG, 'settings')) {
                return false;
            }
            if (defined('SEMI_DEMO_MODE') && SEMI_DEMO_MODE == 0) {
                $this->response['error'] = true;
                $this->response['message'] = SEMI_DEMO_MODE_MSG;
                echo json_encode($this->response);
                return false;
                exit();
            }
            $this->form_validation->set_rules('app_name', 'App Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('support_number', 'Support number', 'trim|required|numeric|xss_clean');
            $this->form_validation->set_rules('support_email', 'Support Email', 'trim|required|xss_clean|valid_email');
            $this->form_validation->set_rules('current_version', 'Current Version Of Android APP', 'trim|required|xss_clean');
            $this->form_validation->set_rules('current_version_ios', 'Current Version Of IOS APP', 'trim|required|xss_clean');
            $this->form_validation->set_rules('delivery_charge', 'Delivery charge', 'trim|required|numeric|xss_clean');
            $this->form_validation->set_rules('min_amount', 'Minimum amount', 'trim|required|numeric|xss_clean');
            $this->form_validation->set_rules('system_timezone_gmt', 'System GMT timezone', 'trim|required|xss_clean');
            $this->form_validation->set_rules('system_timezone', 'System timezone', 'trim|required|xss_clean');
            $this->form_validation->set_rules('is_version_system_on', 'Version System', 'trim|xss_clean');
            $this->form_validation->set_rules('area_wise_delivery_charge', 'Area Wise Delivery Charges', 'trim|xss_clean');
            $this->form_validation->set_rules('currency', 'Currency', 'trim|required|xss_clean');
            $this->form_validation->set_rules('max_product_return_days', 'Maximum Product Return Day', 'trim|required|numeric|xss_clean');
            $this->form_validation->set_rules('delivery_boy_bonus_percentage', 'Delivery Boy Bonus', 'trim|required|numeric|xss_clean');
            $this->form_validation->set_rules('minimum_cart_amt', 'Minimum Cart Amount', 'trim|required|numeric|xss_clean');
            $this->form_validation->set_rules('low_stock_limit', 'Low stock limit', 'trim|numeric|xss_clean');
            $this->form_validation->set_rules('max_items_cart', 'Max items Allowed In Cart', 'trim|required|numeric|xss_clean');
            $this->form_validation->set_rules('cart_btn_on_list', 'Cart Button on Products List', 'trim|xss_clean');
            $this->form_validation->set_rules('expand_product_images', 'Expand Product Images', 'trim|xss_clean');
            $this->form_validation->set_rules('tax_name', 'Tax Name', 'trim|xss_clean');
            $this->form_validation->set_rules('tax_number', 'Tax Number', 'trim|xss_clean');
            $this->form_validation->set_rules('is_refer_earn_on', 'Refer and Earn system', 'trim|xss_clean');
            $this->form_validation->set_rules('welcome_wallet_balance_on', 'Welcome Wallet Balance', 'trim|xss_clean');
            $this->form_validation->set_rules('logo', 'Logo', 'trim|required|xss_clean', array('required' => 'Logo is required'));
            $this->form_validation->set_rules('favicon', 'Favicon', 'trim|required|xss_clean', array('required' => 'Favicon is required'));
            $this->form_validation->set_rules('supported_locals', 'Supported Locals', 'trim|xss_clean');
            $this->form_validation->set_rules('decimal_point', 'Decimal Point', 'trim|xss_clean');
            $this->form_validation->set_rules('android_app_store_link', 'Android APP store link', 'trim|xss_clean');
            $this->form_validation->set_rules('ios_app_store_link', 'ios APP store link', 'trim|xss_clean');
            $this->form_validation->set_rules('scheme', 'Scheme For APP', 'trim|xss_clean|regex_match[/^[a-zA-Z]+$/]');
            $this->form_validation->set_rules('host', 'Host For APP', 'trim|xss_clean');
            $this->form_validation->set_rules('app_store_id', 'App Store Id', 'trim|numeric|xss_clean');
            $this->form_validation->set_rules('default_country_code', 'Default Country Code', 'trim|xss_clean|regex_match[/^[A-Z]+$/]', array('regex_match' => 'The field may only contain uppercase letters.'));


            if (isset($_POST['is_refer_earn_on']) && $_POST['is_refer_earn_on']) {
                $this->form_validation->set_rules('min_refer_earn_order_amount', 'Minimum Refer & Earn Order Amount', 'trim|required|numeric|xss_clean');
                $this->form_validation->set_rules('refer_earn_bonus_for_referal', 'Refer & Earn Bonus For Referal', 'trim|required|numeric|xss_clean');
                $this->form_validation->set_rules('refer_earn_method_for_user', 'Refer Earn method For User', 'trim|required|xss_clean');
                $this->form_validation->set_rules('refer_earn_method_for_referal', 'Refer Earn method For Referal', 'trim|required|xss_clean');
                $this->form_validation->set_rules('max_refer_earn_amount_for_user', 'Maximum Refer & Earn Bonus For User', 'trim|required|xss_clean');
                $this->form_validation->set_rules('refer_earn_bonus_times', 'Refer & Earn Bonus times', 'trim|required|xss_clean');
            }
            if (isset($_POST['welcome_wallet_balance_on']) && $_POST['welcome_wallet_balance_on']) {
                $this->form_validation->set_rules('wallet_balance_amount', 'Welcome Wallet Balance Amount', 'trim|required|numeric|xss_clean');
            }
            if (isset($_POST['refer_earn_method_for_user']) && $_POST['refer_earn_method_for_user'] && $_POST['refer_earn_method_for_user'] == 'percentage') {
                $this->form_validation->set_rules('refer_earn_bonus_for_user', 'Refer & Earn Bonus For User', 'trim|required|numeric|xss_clean|less_than_equal_to[99]');
            }
            if (isset($_POST['refer_earn_method_for_user']) && $_POST['refer_earn_method_for_user'] && $_POST['refer_earn_method_for_user'] == 'amount') {
                $this->form_validation->set_rules('refer_earn_bonus_for_user', 'Refer & Earn Bonus For User', 'trim|required|numeric|xss_clean');
            }

            if (!$this->form_validation->run()) {

                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {

                $fields = [
                    'system_configurations',
                    'system_timezone_gmt',
                    'system_configurations_id',
                    'app_name',
                    'support_number',
                    'support_email',
                    'copyright_details',
                    'logo',
                    'favicon',
                    'current_version',
                    'current_version_ios',
                    'is_version_system_on',
                    'area_wise_delivery_charge',
                    'delivery_charge',
                    'min_amount',
                    'system_timezone',
                    'minimum_cart_amt',
                    'max_items_cart',
                    'low_stock_limit',
                    'max_product_return_days',
                    'delivery_boy_bonus_percentage',
                    'is_delivery_boy_otp_setting_on',
                    'cart_btn_on_list',
                    'tax_name',
                    'tax_number',
                    'google_login',
                    'apple_login',
                    'whatsapp_number',
                    'android_app_store_link',
                    'ios_app_store_link',
                    'scheme',
                    'host',
                    'app_store_id',
                    'default_country_code',
                    'is_refer_earn_on',
                    'min_refer_earn_order_amount',
                    'refer_earn_bonus_times',
                    'refer_earn_method_for_user',
                    'refer_earn_bonus_for_user',
                    'max_refer_earn_amount_for_user',
                    'refer_earn_method_for_referal',
                    'refer_earn_bonus_for_referal',
                    'welcome_wallet_balance_on',
                    'wallet_balance_amount',
                    'pincode_wise_deliverability',
                    'supported_locals',
                    'currency',
                    'decimal_point',
                    'message_for_customer_app',
                    'message_for_seller_app',
                    'message_for_delivery_boy_app',
                    'message_for_web',
                    'city_wise_deliverability',
                    'update_seller_flow',
                    'is_delivery_boy_app_under_maintenance',
                    'is_seller_app_under_maintenance',
                    'is_customer_app_under_maintenance',
                    'is_web_under_maintenance',
                    'is_single_seller_order',
                    'whatsapp_status','expand_product_images'
                ];

                foreach ($fields as $field) {
                    $system_settings[$field] = $this->input->post($field, true) ?? "";
                }
                $system_settings['system_timezone_gmt'] = preg_replace('/\s+/', '', $_POST['system_timezone_gmt']);
                $system_settings['system_timezone_gmt'] = ($_POST['system_timezone_gmt'] == '00:00') ? "+" . $_POST['system_timezone_gmt'] : $_POST['system_timezone_gmt'];


                $this->Setting_model->update_system_setting($system_settings);
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'System Setting Updated Successfully';
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function web()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'web-settings';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Settings | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Settings  | ' . $settings['app_name'];
            $this->data['web_settings'] = get_settings('web_settings', true);
            $this->data['logo'] = get_settings('web_logo');
            $this->data['favicon'] = get_settings('web_favicon');
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function update_web_settings()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('update', 'settings'), PERMISSION_ERROR_MSG, 'settings')) {
                return false;
            }
            if (defined('SEMI_DEMO_MODE') && SEMI_DEMO_MODE == 0) {
                $this->response['error'] = true;
                $this->response['message'] = SEMI_DEMO_MODE_MSG;
                echo json_encode($this->response);
                return false;
                exit();
            }
            $this->form_validation->set_rules('site_title', 'Site Title', 'trim|required|xss_clean');
            $this->form_validation->set_rules('support_number', 'Support number', 'trim|required|numeric|xss_clean');
            $this->form_validation->set_rules('support_email', 'Support Email', 'trim|required|xss_clean|valid_email');
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                $this->Setting_model->update_web_setting($this->input->post(null, true));
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'System Setting Updated Successfully';
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }
    public function get_themes()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->Setting_model->get_theme_list();
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function set_default_theme()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                $this->response['error'] = true;
                $this->response['message'] = DEMO_VERSION_MSG;
                echo json_encode($this->response);
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                return false;
                exit();
            }
            if (print_msg(!has_permissions('update', 'settings'), PERMISSION_ERROR_MSG, 'settings')) {
                return false;
            }
            $this->form_validation->set_rules('theme_id', 'Theme', 'trim|required|xss_clean|numeric');
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
                return false;
            }
            $theme_id = $this->input->post('theme_id', true);
            $theme = $this->db->where('id', $theme_id)->get('themes')->row_array();
            if (empty($theme)) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = "No theme found.";
                $this->response['test'] = $theme;
                print_r(json_encode($this->response));
                return false;
            }

            if ($theme['status'] == 0) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = "You can not set Inactive theme as default.";
                print_r(json_encode($this->response));
                return false;
            }
            $this->db->trans_start();

            $this->db->set('is_default', 0);
            $this->db->update('themes');

            $this->db->set('is_default', 1);
            $this->db->where('id', $theme_id)->update('themes');

            $this->db->trans_complete();
            $error = true;
            if ($this->db->trans_status() === true) {
                $error = false;
            }
            $this->response['error'] = $error;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = "Default Theme Updated.";
            print_r(json_encode($this->response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function update_seller_flow()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (isset($_POST['update_seller_flow']) && !empty($_POST['update_seller_flow'])) {
                $shipping_data = get_settings('shipping_method', true);
                if ($shipping_data['pincode_wise_deliverability'] == '1') {
                    $sellers_data = fetch_details('seller_data', ['serviceable_zipcodes' => NULL], 'id,serviceable_zipcodes,serviceable_cities');
                    $has_error = false;

                    foreach ($sellers_data as $seller_data) {
                        if (empty($seller_data['serviceable_zipcodes'])) {
                            $has_error = true; // Set the flag to true if any seller has empty serviceable_zipcodes
                            break; // Exit the loop early as we only need one error response
                        }
                    }

                    // Check if any error was found
                    if ($has_error) {
                        $this->response['error'] = true;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['message'] = "Please add serviceable zipcodes for all sellers.";
                        print_r(json_encode($this->response));
                    }else{
                        $this->response['error'] = false;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['message'] = "serviceable zipcodes already added all sellers.";
                        print_r(json_encode($this->response));
                    }
                } elseif ($shipping_data['city_wise_deliverability'] == '1') {
                    $sellers_data = fetch_details('seller_data', ['serviceable_cities' => NULL], 'id,serviceable_zipcodes,serviceable_cities');
                    $has_error = false;

                    foreach ($sellers_data as $seller_data) {
                        if (empty($seller_data['serviceable_cities'])) {
                            $has_error = true; // Set the flag to true if any seller has empty serviceable_zipcodes
                            break; // Exit the loop early as we only need one error response
                        }
                    }
                    // Check if any error was found
                    if ($has_error) {
                        $this->response['error'] = true;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['message'] = "Please add serviceable cities for all sellers.";
                        print_r(json_encode($this->response));
                    }else{
                        $this->response['error'] = false;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['message'] = "Serviceable Cities already added for all sellers.";
                        print_r(json_encode($this->response));
                    }
                }
            }
            die;
        } else {
            redirect('admin/login', 'refresh');
        }
    }
}

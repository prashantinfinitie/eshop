<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper', 'sms_helper', 'function_helper']);
        $this->load->model('Order_model');
        $this->data['firebase_project_id'] = get_settings('firebase_project_id');
        $this->data['service_account_file'] = get_settings('service_account_file');
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_shipping_company()) {
            $this->data['main_page'] = TABLES . 'manage-orders';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'View Orders | ' . $settings['app_name'];
            $this->data['meta_description'] = 'View Order | ' . $settings['app_name'];
            $this->data['about_us'] = get_settings('about_us');
            $this->data['curreny'] = get_settings('currency');
            $this->load->view('shipping_company/template', $this->data);
        } else {
            redirect('shipping_company/login', 'refresh');
        }
    }

    public function view_orders()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_shipping_company()) {
            $shipping_company_id = $this->ion_auth->get_user_id();
            return $this->Order_model->get_shipping_company_orders($shipping_company_id);
        } else {
            redirect('shipping_company/login', 'refresh');
        }
    }

    public function edit_orders()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_shipping_company()) {
            $shipping_company = $this->ion_auth->user()->row();
            $this->data['main_page'] = FORMS . 'edit-order-item';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'View Order | ' . $settings['app_name'];
            $this->data['meta_description'] = 'View Order | ' . $settings['app_name'];

            $order_id = $_GET['edit_id'];

            // Verify this order is assigned to this shipping company
            $order = $this->Order_model->get_order_by_id($order_id);

            if (empty($order) || $order[0]['shipping_company_id'] != $shipping_company->id) {
                redirect('shipping_company/orders/', 'refresh');
            }

            // Use the items we already fetched from get_order_by_id()
            if (!isset($order[0]['items']) || empty($order[0]['items'])) {
                redirect('shipping_company/orders/', 'refresh');
            }

            // ✅ FETCH USER DETAILS
            $user_details = fetch_details('users', ['id' => $order[0]['user_id']], 'username,email,mobile');

            // Add user details to order data
            if (!empty($user_details)) {
                $order[0]['uname'] = $user_details[0]['username'];
                $order[0]['email'] = $user_details[0]['email'];
                $order[0]['mobile'] = !empty($order[0]['mobile']) ? $order[0]['mobile'] : $user_details[0]['mobile'];
            } else {
                $order[0]['uname'] = 'N/A';
                $order[0]['email'] = 'N/A';
            }

            $order_items_raw = $order[0]['items'];

            $total = 0;
            $items = [];

            foreach ($order_items_raw as $row) {
                $updated_username = fetch_details('users', ['id' => $row['updated_by']], 'username');

                $temp['id'] = $row['id'];
                $temp['product_id'] = isset($row['product_id']) ? $row['product_id'] : '';
                $temp['product_variant_id'] = $row['product_variant_id'];
                $temp['product_type'] = $row['product_type'];
                $temp['pname'] = $row['product_name'];
                $temp['quantity'] = $row['quantity'];
                $temp['tax_amount'] = $row['tax_amount'];
                $temp['discounted_price'] = isset($row['discounted_price']) ? $row['discounted_price'] : '';
                $temp['price'] = $row['price'];
                $temp['active_status'] = $row['active_status'];
                $temp['product_image'] = base_url($row['product_image']);
                $temp['updated_by'] = !empty($updated_username) ? $updated_username[0]['username'] : 'N/A';
                $temp['seller_id'] = $row['seller_id'];
                $temp['delivery_boy_id'] = isset($row['delivery_boy_id']) ? $row['delivery_boy_id'] : '';

                array_push($items, $temp);
                $total += $row['sub_total'];
            }

            if (empty($items)) {
                redirect('shipping_company/orders/', 'refresh');
            }

            $promo_discount = isset($order[0]['promo_discount']) ? $order[0]['promo_discount'] : 0;
            $wallet_balance = isset($order[0]['wallet_balance']) ? $order[0]['wallet_balance'] : 0;

            $total_discount_percentage = 0;
            $order_total = isset($order[0]['total']) ? $order[0]['total'] : 0;

            if ($total > 0 && $order_total > 0) {
                $total_discount_percentage = calculatePercentage(part: $total, total: $order_total);
            }

            if ($promo_discount != 0 && $total_discount_percentage > 0) {
                $promo_discount = calculatePrice($total_discount_percentage, $promo_discount);
            }
            if ($wallet_balance != 0 && $total_discount_percentage > 0) {
                $wallet_balance = calculatePrice($total_discount_percentage, $wallet_balance);
            }

            $this->data['order_detls'] = $order[0];
            $this->data['items'] = $items;
            $this->data['settings'] = $settings;
            $this->data['total'] = $total;
            $this->data['promo_discount'] = $promo_discount;
            $this->data['wallet_balance'] = $wallet_balance;

            $this->load->view('shipping_company/template', $this->data);
        } else {
            redirect('shipping_company/orders/', 'refresh');
        }
    }

    public function update_order_status()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_shipping_company()) {
            $shipping_company_id = $this->ion_auth->get_user_id();
            $order_item_id = $_GET['id'];
            $status = $_GET['status'];

            // Get order_id and current status from order_item
            $order_item = fetch_details('order_items', ['id' => $order_item_id], 'order_id,status');

            if (empty($order_item)) {
                $this->response['error'] = true;
                $this->response['message'] = 'Order item not found!';
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }

            // Verify order belongs to this shipping company via orders table
            $order = fetch_details('orders', ['id' => $order_item[0]['order_id']], 'shipping_company_id');

            if (empty($order) || $order[0]['shipping_company_id'] != $shipping_company_id) {
                $this->response['error'] = true;
                $this->response['message'] = 'Unauthorized access!';
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }

            // Validate the status transition
            $res = validate_order_status($order_item_id, $status, 'order_items');

            if ($res['error']) {
                $this->response['error'] = true;
                $this->response['message'] = $res['message'];
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }

            $order_id = $order_item[0]['order_id'];
            $system_settings = get_settings('system_settings', true);

            // ✅ BUILD THE STATUS JSON ARRAY
            // Decode existing status (handle double-encoded JSON)
            $existing_status = $order_item[0]['status'];

            // Try to decode - if it's double-encoded, decode twice
            $decoded = json_decode($existing_status, true);
            if (is_string($decoded)) {
                // It was double-encoded, decode again
                $decoded = json_decode($decoded, true);
            }

            if (!is_array($decoded)) {
                $decoded = [];
            }

            // Add new status with timestamp
            $decoded[] = [$status, date("d-m-Y h:i:sa")];

            // Encode to JSON - use JSON_UNESCAPED_SLASHES to prevent extra escaping
            $status_json = json_encode($decoded, JSON_UNESCAPED_SLASHES);

            // ✅ UPDATE DIRECTLY USING CodeIgniter's DB methods to avoid double escaping
            $this->db->where('id', $order_item_id);
            $this->db->update('order_items', [
                'status' => $status_json,
                'active_status' => $status,
                'updated_by' => $_SESSION['user_id']
            ]);

            if ($this->db->affected_rows() > 0) {
                // Send notifications
                $order_details = fetch_orders($order_id);
                if (!empty($order_details)) {
                    $user_id = $order_details['order_data'][0]['user_id'];
                    $user_res = fetch_details('users', ['id' => $user_id], 'username,fcm_id,email,mobile,platform_type');

                    $firebase_project_id = $this->data['firebase_project_id'];
                    $service_account_file = $this->data['service_account_file'];

                    if (!empty($user_res[0]['fcm_id']) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                        $fcmMsg = array(
                            'title' => "Order status updated",
                            'body' => "Your order #" . $order_id . " status has been updated to " . str_replace('_', ' ', $status),
                            'type' => "order",
                        );

                        $groupedByPlatform = [];
                        foreach ($user_res as $item) {
                            $platform = $item['platform_type'];
                            $groupedByPlatform[$platform][] = $item['fcm_id'];
                        }

                        $fcm_ids = [];
                        foreach ($groupedByPlatform as $platform => $fcmIds) {
                            $fcm_ids[$platform] = array_chunk($fcmIds, 1000);
                        }

                        send_notification($fcmMsg, $fcm_ids, $fcmMsg);
                    }

                    // Email notification
                    notify_event(
                        "customer_order_" . $status,
                        ["customer" => [$user_res[0]['email']]],
                        ["customer" => [$user_res[0]['mobile']]],
                        ["orders.id" => $order_id]
                    );
                }

                $this->response['error'] = false;
                $this->response['message'] = 'Status Updated Successfully';
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return true;
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Failed to update status';
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
        } else {
            $this->response['error'] = true;
            $this->response['message'] = 'Unauthorized access not allowed!';
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        }
    }
}

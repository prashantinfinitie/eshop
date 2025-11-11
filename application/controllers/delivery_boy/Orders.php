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
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_delivery_boy()) {
            $this->data['main_page'] = TABLES . 'manage-orders';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'View Orders | ' . $settings['app_name'];
            $this->data['meta_description'] = ' View Order  | ' . $settings['app_name'];
            $this->data['about_us'] = get_settings('about_us');
            $this->data['curreny'] = get_settings('currency');
            $this->load->view('delivery_boy/template', $this->data);
        } else {
            redirect('delivery_boy/login', 'refresh');
        }
    }

    public function view_orders()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_delivery_boy()) {
            $deliveryBoyId = $this->ion_auth->get_user_id();
            return $this->Order_model->get_return_order_items_list($deliveryBoyId, $_GET['offset']);
        } else {
            redirect('delivery_boy/login', 'refresh');
        }
    }

    public function consignment_view()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_delivery_boy()) {
            $delivery_boy_id = $this->ion_auth->get_user_id();
            return $this->Order_model->consignment_view(delivery_boy_id: $delivery_boy_id);
        } else {
            redirect('delivery_boy/login', 'refresh');
        }
    }

    public function edit_orders()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_delivery_boy()) {
            $delivery_boy = $this->ion_auth->user()->row();
            $this->data['main_page'] = FORMS . 'edit-orders';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'View Order | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Eshop  | View Order | ' . $settings['app_name'];
            $consignment_id = $_GET['edit_id'];
            $res = view_all_consignments(consignment_id: $consignment_id, in_detail: false);

            if (empty($res['data'])) {
                redirect('delivery_boy/orders/', 'refresh');
            }
            $consignment_items = $res['data'][0]['consignment_items'];
            $order_item_ids = array_map(function ($item) {
                return $item['order_item_id'];
            }, $consignment_items);
            $order_items = fetch_order_items(order_item_id: $order_item_ids);
            if (isset($order_items['order_data']) && empty($order_items['order_data'])) {
                redirect('delivery_boy/orders/', 'refresh');
            }
            $order_items = $order_items['order_data'];
            $total = 0;
            if ($delivery_boy->id == $order_items[0]['delivery_boy_id'] && isset($_GET['edit_id']) && !empty($_GET['edit_id']) && !empty($res) && is_numeric($_GET['edit_id'])) {
                $items = [];
                foreach ($order_items as $row) {
                    if ($delivery_boy->id == $row['delivery_boy_id']) {
                        $multipleWhere = ['seller_id' => $row['seller_id'], 'order_id' => $row['id']];
                        $order_charge_data = $this->db->where($multipleWhere)->get('order_charges')->result_array();
                        $updated_username = fetch_details('users', 'id =' . $row['updated_by'], 'username');
                        $temp['id'] = $row['id'];
                        $temp['product_id'] = $row['product_id'];
                        $temp['product_variant_id'] = $row['product_variant_id'];
                        $temp['product_type'] = $row['type'];
                        $temp['pname'] = $row['name'];
                        $temp['quantity'] = $row['quantity'];
                        $temp['tax_amount'] = $row['tax_amount'];
                        $temp['discounted_price'] = $row['discounted_price'];
                        $temp['price'] = $row['price'];
                        $temp['active_status'] = $row['active_status'];
                        $temp['product_image'] = $row['image_sm'];
                        $temp['updated_by'] = $updated_username[0]['username'];
                        $temp['seller_otp'] = $order_charge_data[0]['otp'];
                        $temp['seller_id'] = $row['seller_id'];
                        array_push($items, $temp);
                        $total += $row['sub_total'];
                    }
                }
                if ($total > 0 && $order_items[0]['subtotal_of_order_items'] > 0) {
                    $total_discount_percentage = calculatePercentage(part: $total, total: $order_items[0]['subtotal_of_order_items']);
                }

                $promo_discount = $order_items[0]['promo_discount'] ?? 0;

                $wallet_balance = $order_items[0]['wallet_balance'] ?? 0;
                if ($promo_discount != 0) {
                    $promo_discount = calculatePrice($total_discount_percentage, $promo_discount);
                }
                if ($wallet_balance != 0) {
                    $wallet_balance = calculatePrice($total_discount_percentage, $wallet_balance);
                }
                $total_order_items = $this->db->select('COUNT(DISTINCT(order_items.id)) as total')->from('order_items')->where('order_id', $order_items[0]['order_id'])->get()->result_array();
                $items_count = count($order_item_ids);
                $total_order_items = $total_order_items[0]['total'] > 0 ? $total_order_items[0]['total'] : 1;
                $res['data']['consignment_id'] = $consignment_id;
                $res['data']['order_id'] = $order_items[0]['order_id'];
                $res['data']['delivery_boy_name'] = $order_items[0]['username'];
                $res['data']['delivery_boy_mobile'] = $order_items[0]['mobile'];
                $res['data']['delivery_boy_email'] = $order_items[0]['email'];
                $res['data']['notes'] = $order_items[0]['notes'];
                $res['data']['payment_method'] = $order_items[0]['payment_method'];
                $res['data']['address'] = $order_items[0]['user_address'];
                $res['data']['delivery_charge'] = $order_items[0]['delivery_charge'] / $total_order_items * $items_count;
                $res['data']['total_promo_discount'] = $order_items[0]['promo_discount'];
                $res['data']['promo_discount'] = ($promo_discount);
                $res['data']['wallet_balance'] = $wallet_balance;
                $res['data']['total_payable'] = $order_items[0]['total_payable'];
                $res['data']['delivery_boy_id'] = $order_items[0]['delivery_boy_id'];
                $res['data']['delivery_date'] = $order_items[0]['delivery_date'];
                $res['data']['delivery_time'] = $order_items[0]['delivery_time'];
                $res['data']['is_cod_collected'] = $order_items[0]['is_cod_collected'];
                $this->data['order_detls'] = $res['data'];
                $this->data['items'] = $items;
                $this->data['settings'] = get_settings('system_settings', true);
                $this->load->view('delivery_boy/template', $this->data);
            } else {
                redirect('delivery_boy/orders/', 'refresh');
            }
        } else {
            redirect('delivery_boy/login', 'refresh');
        }
    }


    public function edit_return_orders()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_delivery_boy()) {
            $delivery_boy = $this->ion_auth->user()->row();
            $this->data['main_page'] = FORMS . 'edit-order-item';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'View Order | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Eshop  | View Order | ' . $settings['app_name'];
            $res = $this->Order_model->get_order_item_details(['oi.id' => $_GET['edit_id'], 'oi.delivery_boy_id' => $delivery_boy->id]);

            if ($delivery_boy->id == $res[0]['delivery_boy_id'] && isset($_GET['edit_id']) && !empty($_GET['edit_id']) && !empty($res) && is_numeric($_GET['edit_id'])) {
                $items = [];
                foreach ($res as $row) {

                    if ($delivery_boy->id == $row['delivery_boy_id']) {
                        $multipleWhere = ['seller_id' => $row['seller_id'], 'order_id' => $row['id']];
                        $order_charge_data = $this->db->where($multipleWhere)->get('order_charges')->result_array();
                        $updated_username = fetch_details('users', 'id =' . $row['updated_by'], 'username');
                        $temp['id'] = $row['order_item_id'];
                        $temp['product_id'] = $row['product_id'];
                        $temp['product_variant_id'] = $row['product_variant_id'];
                        $temp['product_type'] = $row['type'];
                        $temp['pname'] = $row['pname'];
                        $temp['quantity'] = $row['quantity'];
                        $temp['tax_amount'] = $row['tax_amount'];
                        $temp['discounted_price'] = $row['discounted_price'];
                        $temp['price'] = $row['price'];
                        $temp['active_status'] = $row['oi_active_status'];
                        $temp['product_image'] = base_url() . $row['product_image'];
                        $temp['updated_by'] = $updated_username[0]['username'];
                        $temp['seller_otp'] = $order_charge_data[0]['otp'];
                        $temp['seller_id'] = $row['seller_id'];
                        $temp['username'] = $row['uname'];
                        $temp['email'] = $row['email'];
                        $temp['mobile'] = $row['mobile_number'];
                        array_push($items, $temp);
                    }
                }

                $this->data['order_detls'] = $res[0];
                $this->data['items'] = $items;
                $this->data['settings'] = get_settings('system_settings', true);
                $this->load->view('delivery_boy/template', $this->data);
            } else {
                redirect('delivery_boy/orders/', 'refresh');
            }
        } else {
            redirect('delivery_boy/login', 'refresh');
        }
    }

    /* To update the status of particular order item */
    public function update_order_status()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_delivery_boy()) {
            $res = validate_order_status($_GET['id'], $_GET['status'], 'consignments');
            $system_settings = get_settings('system_settings', true);
            // $otp_system = $system_settings['is_delivery_boy_otp_setting_on'];

            if ($res['error']) {
                $this->response['error'] = true;
                $this->response['message'] = $res['message'];
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
            $consignment = fetch_details('consignments', ['id' => $_GET['id']], '*');
            $consignment_items = fetch_details('consignment_items', ['consignment_id' => $consignment[0]['id']], '*');

            if (empty($consignment) && empty($consignment_items)) {
                $this->response['error'] = true;
                $this->response['message'] = "Consignment Not Found.";
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
            $order_item_ids = array_column($consignment_items, 'order_item_id');
            $order_id = $consignment[0]['order_id'];

            $order_item_res = $this->db->select('oi.*, oi.id AS order_item_id,(SELECT COUNT(id) FROM order_items WHERE order_id = oi.order_id) AS order_counter,(SELECT COUNT(active_status) FROM order_items WHERE active_status = "cancelled" AND order_id = oi.order_id) AS order_cancel_counter,(SELECT COUNT(active_status) FROM order_items WHERE active_status = "returned" AND order_id = oi.order_id) AS order_return_counter,(SELECT COUNT(active_status) FROM order_items WHERE active_status = "delivered" AND order_id = oi.order_id) AS order_delivered_counter,(SELECT COUNT(active_status) FROM order_items WHERE active_status = "processed" AND order_id = oi.order_id) AS order_processed_counter,(SELECT COUNT(active_status) FROM order_items WHERE active_status = "shipped" AND order_id = oi.order_id) AS order_shipped_counter,(SELECT status FROM orders WHERE id = oi.order_id) AS order_status')
                ->from('order_items oi')
                ->where_in('oi.id', $order_item_ids)
                ->get()
                ->result_array();

            $otp_system = $order_item_res[0]['deliveryboy_otp_setting_on'];
            if ($_GET['status'] == 'delivered') {
                if ($otp_system == 1) {

                    if (!validate_otp(otp: $_GET['otp'], consignment_id: $_GET['id'])) {
                        $this->response['error'] = true;
                        $this->response['message'] = 'Invalid OTP supplied!';
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['data'] = array();
                        print_r(json_encode($this->response));
                        return false;
                    }
                }
            }

            $order_method = fetch_details('orders', ['id' => $order_id], 'payment_method');
            $firebase_project_id = $this->data['firebase_project_id'];
            $service_account_file = $this->data['service_account_file'];
            if ($order_method[0]['payment_method'] == 'bank_transfer') {
                $bank_receipt = fetch_details('order_bank_transfer', ['order_id' => $order_id]);
                $transaction_status = fetch_details('transactions', ['order_id' => $order_id], 'status');
                if (empty($bank_receipt) || strtolower($transaction_status[0]['status']) != 'success') {
                    $this->response['error'] = true;
                    $this->response['message'] = "Order Status can not update, Bank verification is remain from transactions.";
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }
            }
            if ($this->Order_model->update_order(['status' => $_GET['status']], ['id' => $_GET['id']], true, 'consignments')) {
                $this->Order_model->update_order(['active_status' => $_GET['status']], ['id' => $_GET['id']], false, 'consignments');

                foreach ($consignment_items as $item) {
                    $this->Order_model->update_order(['status' => $_GET['status']], ['id' => $item['order_item_id']], true, 'order_items');
                    $this->Order_model->update_order(['active_status' => $_GET['status']], ['id' => $item['order_item_id']], false, 'order_items');

                    // Update login id in order_item table
                    update_details(['updated_by' => $_SESSION['user_id']], ['id' =>  $item['order_item_id']], 'order_items');

                }
                if (($order_item_res[0]['order_counter'] == intval($order_item_res[0]['order_cancel_counter']) + 1 && $_GET['status'] == 'cancelled') ||  ($order_item_res[0]['order_counter'] == intval($order_item_res[0]['order_return_counter']) + 1 && $_GET['status'] == 'returned') || ($order_item_res[0]['order_counter'] == intval($order_item_res[0]['order_delivered_counter']) + 1 && $_GET['status'] == 'delivered') || ($order_item_res[0]['order_counter'] == intval($order_item_res[0]['order_processed_counter']) + 1 && $_GET['status'] == 'processed') || ($order_item_res[0]['order_counter'] == intval($order_item_res[0]['order_shipped_counter']) + 1 && $_GET['status'] == 'shipped')) {

                    $user = fetch_details('orders', ['id' => $order_id], 'user_id');
                    $seller = fetch_details('order_items', ['order_id' => $order_id], 'seller_id');
                    $user_id = $user[0]['user_id'];
                    $seller_id = $seller[0]['seller_id'];
                    $settings = get_settings('system_settings', true);
                    $app_name = isset($settings['app_name']) && !empty($settings['app_name']) ? $settings['app_name'] : '';
                    $user_res = fetch_details('users', ['id' => $user_id], 'username,fcm_id,email,mobile,platform_type');
                    $seller_res = fetch_details('users', ['id' => $seller_id], 'username,fcm_id,email,mobile,platform_type');
                    $fcm_ids = array();
                    //custom message
                    if ($_GET['status'] == 'received') {
                        $type = ['type' => "customer_order_received"];
                    } elseif ($_GET['status'] == 'processed') {
                        $type = ['type' => "customer_order_processed"];
                    } elseif ($_GET['status'] == 'shipped') {
                        $type = ['type' => "customer_order_shipped"];
                    } elseif ($_GET['status'] == 'delivered') {
                        $type = ['type' => "customer_order_delivered"];
                    } elseif ($_GET['status'] == 'cancelled') {
                        $type = ['type' => "customer_order_cancelled"];
                    } elseif ($_GET['status'] == 'returned') {
                        $type = ['type' => "customer_order_returned"];
                    }
                    $custom_notification = fetch_details('custom_notifications', $type, '');
                    $hashtag_cutomer_name = '< cutomer_name >';
                    $hashtag_order_id = '< order_item_id >';
                    $hashtag_application_name = '< application_name >';
                    $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                    $hashtag = html_entity_decode($string);
                    $data = str_replace(array($hashtag_cutomer_name, $hashtag_order_id, $hashtag_application_name), array($user_res[0]['username'], $order_id, $app_name), $hashtag);
                    $message = output_escaping(trim($data, '"'));
                    $customer_msg = (!empty($custom_notification)) ? $message :  'Hello Dear ' . $user_res[0]['username'] . 'Order status updated to' . $_GET['status'] . ' for your order ID #' . $order_id . ' please take note of it! Thank you for shopping with us. Regards ' . $app_name . '';

                    //user get notification for order 
                    if (!empty($user_res[0]['fcm_id']) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                        $fcmMsg = array(
                            'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Order status updated",
                            'body' => $customer_msg,
                            'type' => "order",
                        );
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
                        send_notification($fcmMsg, $fcm_ids, $fcmMsg);
                    }

                    //seller get notification for order 
                    if (!empty($seller_res[0]['fcm_id']) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                        $fcmMsg = array(
                            'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Order status updated",
                            'body' => $customer_msg,
                            'type' => "order",
                        );
                        // Step 1: Group by platform
                        $groupedByPlatform = [];
                        foreach ($seller_res as $item) {
                            $platform = $item['platform_type'];
                            $groupedByPlatform[$platform][] = $item['fcm_id'];
                        }

                        // Step 2: Chunk each platform group into arrays of 1000
                        $seller_fcm_ids = [];
                        foreach ($groupedByPlatform as $platform => $fcmIds) {
                            $seller_fcm_ids[$platform] = array_chunk($fcmIds, 1000);
                        }

                        $seller_fcm_ids[0][] = $seller_fcm_ids;
                        send_notification($fcmMsg, $seller_fcm_ids, $fcmMsg);
                    }
                    notify_event(
                        $type['type'],
                        ["customer" => [$user_res[0]['email']]],
                        ["customer" => [$user_res[0]['mobile']]],
                        ["orders.id" => $order_id]
                    );
                }
            }
            $this->response['error'] = false;
            $this->response['message'] = 'Status Updated Successfully';
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
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

    //  To update the return status of order items
    function update_return_order_item_status()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_delivery_boy()) {

            // Get POST data
            $order_item_id = $this->input->post('order_item_id');
            $new_status = $this->input->post('status');

            // Check if the new status is 'return_pickedup'
            if ($new_status !== 'return_pickedup') {
                $this->response['error'] = true;
                $this->response['message'] = 'Invalid Status';
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return;
            }

            // Fetch the current status from the database
            $order_item = $this->Order_model->getOrderItemById($order_item_id);
            $current_status = json_decode($order_item->status, true);

            // Check if the current status is a valid array, otherwise initialize it
            if (!is_array($current_status)) {
                $current_status = [];
            }

            // Check if the last status is return_pickedup
            $last_status = end($current_status);
            if ($last_status[0] === 'return_pickedup') {
                $this->response['error'] = true;
                $this->response['message'] = 'Status already updated';
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return;
            }
            if ($last_status[0] == 'returned') {
                $this->response['error'] = true;
                $this->response['message'] = 'Status is already returned you can not set it as pickedup.';
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return;
            }
            // Create new status entry with timestamp
            $current_time = date("Y-m-d H:i:s"); // Use desired date format
            $new_entry = [$new_status, $current_time];

            // Append new status entry to the array
            $current_status[] = $new_entry;

            // Encode the updated status array back to JSON
            $updated_status = json_encode($current_status);

            // Prepare data for update
            $update_data = [
                'active_status' => $new_status,
                'status' => $updated_status
            ];

            // Update the status and active_status in the database
            $result = $this->Order_model->updateOrderItemStatus($order_item_id, $update_data);


            // Send response
            if ($result) {
                $this->response['error'] = false;
                $this->response['message'] = 'Status Updated Successfully';
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['data'] = $result;
                print_r(json_encode($this->response));
            } else {
                $this->response['error'] = True;
                $this->response['message'] = 'Status Not Updated';
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
            }
        }
    }
}

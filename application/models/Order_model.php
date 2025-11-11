<?php
error_reporting(1);
defined('BASEPATH') or exit('No direct script access allowed');

class Order_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['sms_helper', 'function_helper']);
    }

    public function update_order($set, $where, $isjson = false, $table = 'order_items', $fromuser = false, $is_escape_array = true, $is_digital_product = 0)
    {
        if ($is_escape_array == true) {
            $set = escape_array($set);
        }
        $response = FALSE;

        if ($isjson == true) {

            $field = array_keys($set); // active_status
            $current_status = $set[$field[0]]; //processed

            $res = fetch_details($table, $where, '*');
            if ($is_digital_product == 1) {
                $priority_status = [
                    'received' => 0,
                    'delivered' => 1,
                ];
            } else {
                if ($set['status'] != 'return_request_decline') {
                    $priority_status = [
                        'received' => 0,
                        'processed' => 1,
                        'shipped' => 2,
                        'delivered' => 3,
                        'return_request_pending' => 4,
                        'return_request_approved' => 5,
                        'cancelled' => 6,
                        'returned' => 7,
                    ];
                } else {
                    $priority_status = [
                        'received' => 0,
                        'processed' => 1,
                        'shipped' => 2,
                        'delivered' => 3,
                        'return_request_pending' => 4,
                        'return_request_decline' => 5,
                        'cancelled' => 6,
                        'returned' => 7,
                    ];
                }
            }
            if (count($res) >= 1) {
                $i = 0;

                foreach ($res as $row) {
                    $set = array();
                    $temp = array();
                    $active_status = array();
                    $active_status[$i] = json_decode($row['status'], 1);
                    $current_selected_status = end($active_status[$i]);
                    $temp = $active_status[$i];
                    $cnt = count($temp);
                    $currTime = date('Y-m-d H:i:s');
                    $min_value = (!empty($temp)) ? $priority_status[$current_selected_status[0]] : -1;
                    $max_value = $priority_status[$current_status];
                    if ($current_status == 'returned' || $current_status == 'cancelled') {
                        $temp[$cnt] = [$current_status, $currTime];
                    } else {
                        foreach ($priority_status as $key => $value) {
                            if ($value > $min_value && $value <= $max_value) {
                                $temp[$cnt] = [$key, $currTime];
                            }
                            ++$cnt;
                        }
                    }
                    $set = [$field[0] => json_encode(array_values($temp))];
                    $this->db->trans_start();
                    $this->db->set($set)->where(['id' => $row['id']])->update($table);
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === TRUE) {
                        $response = TRUE;
                    }
                    /* give commission to the delivery boy if the order is delivered */

                    $order_item_ids = [];
                    if ($current_status == 'delivered') {
                        if ($table == "consignments") {
                            $consignment_items = fetch_details('consignment_items', ['consignment_id' => $where['id']]);
                            $order_item_ids = array_map(function ($item) {
                                return $item['order_item_id'];
                            }, $consignment_items);
                            $order = fetch_details(table: 'order_items', fields: 'delivery_boy_id,order_id,sub_total,id,seller_id', where_in_key: 'id', where_in_value: $order_item_ids);
                        } else {
                            $order = fetch_details('order_items', $where, 'delivery_boy_id,order_id,sub_total,id,seller_id');
                        }
                        $item_seller_id = $order[0]['seller_id'];
                        $order_item_id = $order[0]['id'];
                        $order_id = $row['order_id'];
                        $total_order_items = $this->db->select('COUNT(DISTINCT(order_items.id)) as total')->from('order_items')->where('order_id', $order_id)->get()->result_array();
                        $total_order_items = $total_order_items[0]['total'] > 0 ? $total_order_items[0]['total'] : 1;
                        $order_final_total = fetch_details('orders', 'id=' . $order[0]['order_id'], 'delivery_charge,total,final_total,payment_method,promo_discount,is_cod_collected,wallet_balance');
                        $delivery_charges = intval($order_final_total[0]['delivery_charge']);
                        $order_item_delivery_charges = $delivery_charges / $total_order_items * count($order_item_ids);
                        $total_discount_percentage = 0;
                        $total = 0;
                        $final_total = 0;
                        if ($table == "consignments") {
                            if (!empty($order)) {
                                $delivery_boy_id = $order[0]['delivery_boy_id'];
                                $subtotal_of_products = $order_final_total[0]['total'];
                                if ($delivery_boy_id > 0) {
                                    $commission = 0;
                                    $delivery_boy = fetch_details('users', ['id' => $delivery_boy_id], 'bonus,bonus_type');
                                    if (isset($delivery_boy) && !empty($delivery_boy)) {
                                        foreach ($order as $value) {
                                            $final_total = $total += $value['sub_total'];
                                        }
                                        $settings = get_settings('system_settings', true);
                                        // get bonus_type
                                        if ($delivery_boy[0]['bonus_type'] == "fixed_amount_per_order_item") {
                                            $commission = (isset($delivery_boy[0]['bonus']) && $delivery_boy[0]['bonus'] > 0) ? $delivery_boy[0]['bonus'] : $settings['delivery_boy_bonus_percentage'];
                                        }
                                        if ($delivery_boy[0]['bonus_type'] == "percentage_per_order_item") {
                                            $commission = (isset($delivery_boy[0]['bonus']) && $delivery_boy[0]['bonus'] > 0) ? $delivery_boy[0]['bonus'] : $settings['delivery_boy_bonus_percentage'];
                                            $commission = $final_total * ($commission / 100);
                                            if ($commission > $final_total) {
                                                $commission = $final_total;
                                            }
                                        }
                                    }
                                    if ($total > 0 && $subtotal_of_products > 0) {
                                        $total_discount_percentage = calculatePercentage(part: $total, total: $subtotal_of_products);
                                    }
                                    $wallet_balance = $order_final_total[0]['wallet_balance'] ?? 0;
                                    $promo_discount = $order_final_total[0]['promo_discount'] ?? 0;
                                    if ($promo_discount != 0) {
                                        $promo_discount = calculatePrice($total_discount_percentage, $promo_discount);
                                    }
                                    if ($wallet_balance != 0) {
                                        $wallet_balance = calculatePrice($total_discount_percentage, $wallet_balance);
                                    }
                                    $total_amount_payable = intval($final_total + $order_item_delivery_charges - $wallet_balance - $promo_discount);
                                    /* commission must be greater then zero to be credited into the account */
                                    $this->load->model("transaction_model");
                                    if ($commission > 0) {
                                        $transaction_data = [
                                            'transaction_type' => "wallet",
                                            'user_id' => $delivery_boy_id,
                                            'order_id' => $row['order_id'],
                                            'order_item_id' => is_array($order_item_ids) ? implode(", ", $order_item_ids) : $order_item_ids,
                                            'type' => "credit",
                                            'txn_id' => "",
                                            'amount' => $commission,
                                            'status' => "success",
                                            'message' => "Order delivery bonus for order item ID: #" . implode(", ", $order_item_ids),
                                        ];
                                        $this->transaction_model->add_transaction($transaction_data);
                                        $this->load->model('customer_model');
                                        $this->customer_model->update_balance($commission, $delivery_boy_id, 'add');
                                    }
                                    if (strtolower($order_final_total[0]['payment_method']) == "cod") {
                                        $transaction_data = [
                                            'transaction_type' => "transaction",
                                            'user_id' => $delivery_boy_id,
                                            'order_id' => $row['order_id'],
                                            'order_item_id' => implode(", ", $order_item_ids),
                                            'type' => "delivery_boy_cash",
                                            'txn_id' => "",
                                            'amount' => $total_amount_payable,
                                            'status' => "1",
                                            'message' => "Delivery boy collected COD",
                                        ];
                                        $this->transaction_model->add_transaction($transaction_data);
                                        $this->load->model('customer_model');
                                        update_cash_received($total_amount_payable, $delivery_boy_id, "add");
                                    }
                                }
                            }
                        }
                    }
                    ++$i;
                }
                return $response;
            }
        } else {
            $this->db->trans_start();

            $this->db->set($set)->where($where)->update($table);

            $this->db->trans_complete();
            $response = FALSE;
            if ($this->db->trans_status() === TRUE) {
                $response = TRUE;
            }

            return $response;
        }
    }

    public function delete_draft_orders()
    {
        $status = "draft";
        $products = fetch_details('orders', ['status' => $status], 'id');
        foreach ($products as $order_id) {
            $order = fetch_orders($order_id['id'], false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, 0);
            $added_date = $order['order_data'][0]['order_items'][0]['date_added'];

            $added_date_time = new DateTime($added_date);
            $current_time = new DateTime();
            $time_diff = $current_time->diff($added_date_time);

            if ($time_diff->h >= 1 || $time_diff->days > 0) {
                $user_id = $order['order_data'][0]['user_id'];
                $returnable_amount = $order['order_data'][0]['wallet_balance'];
                update_wallet_balance('credit', $user_id, $returnable_amount, 'Wallet Amount Credited for Order ID  : ' . $order['order_data'][0]['id']);
                update_stock($order['order_data'][0]['order_items'][0]['product_variant_id'], $order['order_data'][0]['order_items'][0]['quantity'], 'plus');
                delete_details(['id' => $order['order_data'][0]['id']], 'orders');
                delete_details(['order_id' => $order['order_data'][0]['id']], 'order_items');
                delete_details(['order_id' => $order['order_data'][0]['id']], 'transactions');

                $response['error'] = false;
                $response['message'] = 'Order deleted successfully';
                $response['data'] = array();
            }
        }
        print_r(json_encode($response));
    }

    public function update_order_item($id, $status, $return_request = 0, $fromapp = false, $return_data = [])
    {

        $firebase_project_id = $this->data['firebase_project_id'];
        $service_account_file = $this->data['service_account_file'];
        $system_settings = get_settings('system_settings', true);
        if ($return_request == 0) {
            $res = validate_order_status($id, $status, 'order_items', '', true);

            if ($res['error']) {

                $response['error'] = (isset($res['return_request_flag'])) ? false : true;
                $response['message'] = $res['message'];
                $response['data'] = $res['data'];

                return $response;
            }
        }
        if ($fromapp == true) {
            if ($status == 'returned') {
                $status = 'return_request_pending';
            }
        }
        $order_item_details = fetch_details('order_items', ['id' => $id], 'order_id,seller_id');
        $order_details = fetch_orders($order_item_details[0]['order_id']);
        $order_tracking_data = get_shipment_id($id, $order_item_details[0]['order_id']);

        if (!empty($order_details) && !empty($order_item_details)) {
            $order_details = $order_details['order_data'];
            $order_items_details = $order_details[0]['order_items'];
            $key = array_search($id, array_column($order_items_details, 'id'));
            $order_id = $order_details[0]['id'];
            $user_id = $order_details[0]['user_id'];
            $order_counter = $order_items_details[$key]['order_counter'];
            $order_cancel_counter = $order_items_details[$key]['order_cancel_counter'];
            $order_return_counter = $order_items_details[$key]['order_return_counter'];
            $seller_res = fetch_details('users', ['id' => $order_item_details[0]['seller_id']], 'fcm_id,username,mobile,email,platform_type');

            $groupedByPlatform = [];
            foreach ($seller_res as $item) {
                $platform = $item['platform_type'];
                $groupedByPlatform[$platform][] = $item['fcm_id'];
            }

            // Step 2: Chunk each platform group into arrays of 1000
            $fcm_ids = [];
            foreach ($groupedByPlatform as $platform => $fcmIds) {
                $fcm_ids[$platform] = array_chunk($fcmIds, 1000);
            }

            $registrationIDs_chunks = $fcm_ids;

            if ($this->update_order(['status' => $status], ['id' => $id], true, 'order_items')) {
                $this->order_model->update_order(['active_status' => $status], ['id' => $id], false, 'order_items');
                if (isset($return_data) && !empty($return_data) && $return_data != []) {
                    unset($return_data['order_item_id']);
                    unset($return_data['order_id']);

                    unset($return_data['ci_csrf_token']);
                    unset($return_data['other_reason']);
                    unset($return_data['status']);
                    update_details($return_data, ['id' => $id], 'order_items');
                    update_details($return_data, ['order_item_id' => $id], 'return_requests');
                }

                $firebase_project_id = get_settings('firebase_project_id');
                $service_account_file = get_settings('service_account_file');

                //send notification while order cancelled
                if ($status == 'cancelled') {
                    $fcm_admin_subject = 'Order cancelled';
                    $fcm_admin_msg = 'Hello ' . $seller_res[0]['username'] . 'order of order item id ' . $order_id . ' is cancelled.';
                    if (!empty($fcm_ids) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                        $fcmMsg = array(
                            'title' => $fcm_admin_subject,
                            'body' => $fcm_admin_msg,
                            'type' => "place_order"
                        );

                        send_notification($fcmMsg, $registrationIDs_chunks, $fcmMsg);
                        (notify_event(
                            "customer_order_cancelled",
                            ["seller" => [$seller_res[0]['email']]],
                            ["seller" => [$seller_res[0]['mobile']]],
                            ["orders.id" => $order_id]
                        ));
                    }
                    if (isset($order_tracking_data) && !empty($order_tracking_data) && $order_tracking_data != null) {
                        cancel_shiprocket_order($order_tracking_data[0]['shiprocket_order_id']);
                    }
                }
                //send notification to  while order return request pending 
                if ($status == 'return_request_pending') {
                    $fcm_admin_subject = 'New return request get for order ID #' . $order_id;
                    $fcm_admin_msg = 'New return request get in ' . $system_settings['app_name'] . ' please process it.';

                    $admin_notifi = array(
                        'title' => $fcm_admin_subject,
                        'message' => $fcm_admin_msg,
                        'type' => "return_request",
                        'type_id' => $order_id
                    );
                    insert_details($admin_notifi, 'system_notification');
                }
            }

            $response['error'] = false;
            $response['message'] = 'Status Updated Successfully';
            $response['data'] = array();
            return $response;
        }
    }

    public function place_order($data)
    {
        // print_R($data);
        // die;
        $payment_settings = get_settings("payment_method", true);


        if (isset($data['is_pos_order']) && !empty($data['is_pos_order']) && $data['is_pos_order'] != 1) {
            if ($data["payment_method"] === "COD") {
                if ($data["final_total"] < $payment_settings["min_cod_amount"] || $data["final_total"] > $payment_settings["max_cod_amount"]) {
                    return [
                        "error" => true,
                        "message" => "Order amount for COD should be between " . $payment_settings["min_cod_amount"] . " and " . $payment_settings["max_cod_amount"]
                    ];
                }
            }
        }

        $data = escape_array($data);

        $CI = &get_instance();
        $CI->load->model('Address_model');
        $response = array();
        $user = fetch_details('users', ['id' => $data['user_id']]);
        $product_variant_id = explode(',', $data['product_variant_id']);
        $quantity = explode(',', $data['quantity']);

        $check_current_stock_status = validate_stock($product_variant_id, $quantity);

        if (isset($check_current_stock_status['error']) && $check_current_stock_status['error'] == true) {
            return ($check_current_stock_status);
        }
        /* Calculating Final Total */
        $total = 0;
        $product_variant = $this->db->select('pv.*,GROUP_CONCAT(tax.percentage) as tax_percentage ,GROUP_CONCAT(tax.id) as tax_ids,GROUP_CONCAT(tax.title) as tax_name,p.seller_id,p.name as product_name,p.type as product_type,p.is_prices_inclusive_tax,p.is_attachment_required,p.download_link, p.image, p.is_cancelable, p.is_returnable')
            ->join('products p ', 'pv.product_id=p.id', 'left')
            ->join('categories c', 'p.category_id = c.id', 'left')
            ->join('taxes tax', 'FIND_IN_SET(tax.id, p.tax) > 0', 'LEFT')
            ->where_in('pv.id', $product_variant_id)->group_by('p.id, pv.id')->order_by('FIELD(pv.id,' . $data['product_variant_id'] . ')')->get('product_variants pv')->result_array();


        if (!empty($product_variant)) {
            $system_settings = get_settings('system_settings', true);
            $seller_ids = array_values(array_unique(array_column($product_variant, "seller_id")));

            /* check for single seller permission */
            if ($system_settings['is_single_seller_order'] == '1') {
                if (isset($seller_ids) && count($seller_ids) > 1) {
                    $response['error'] = true;
                    $response['message'] = 'Only one seller products are allow in one order.';
                    return $response;
                }
            }
            $delivery_charge = isset($data['delivery_charge']) && !empty($data['delivery_charge']) ? $data['delivery_charge'] : 0;

            $discount = isset($data['discount']) && !empty($data['discount']) ? $data['discount'] : 0;
            $gross_total = 0;
            $cart_data = [];
            for ($i = 0; $i < count($product_variant); $i++) {

                $pv_price[$i] = ($product_variant[$i]['special_price'] > 0 && $product_variant[$i]['special_price'] != null) ? $product_variant[$i]['special_price'] : $product_variant[$i]['price'];
                $tax_percentage[$i] = (isset($product_variant[$i]['tax_percentage']) && intval($product_variant[$i]['tax_percentage']) > 0 && $product_variant[$i]['tax_percentage'] != null) ? $product_variant[$i]['tax_percentage'] : '0';
                $tax_ids[$i] = (isset($product_variant[$i]['tax_ids']) && $product_variant[$i]['tax_percentage'] != null) ? $product_variant[$i]['tax_ids'] : '0';

                if ((isset($product_variant[$i]['is_prices_inclusive_tax']) && $product_variant[$i]['is_prices_inclusive_tax'] == 0) || (!isset($product_variant[$i]['is_prices_inclusive_tax'])) && $tax_percentage[$i] > 0) {
                    $pv_price_without_tax[$i] = $pv_price[$i];
                    $pv_price[$i] = calculatePriceWithTax($tax_percentage[$i], $pv_price[$i]);
                }

                $subtotal[$i] = ($pv_price[$i]) * $quantity[$i];
                $subtotal_without_tax[$i] = ($pv_price_without_tax[$i]) * $quantity[$i];

                $pro_name[$i] = $product_variant[$i]['product_name'];
                $variant_info = get_variants_values_by_id($product_variant[$i]['id']);
                $product_variant[$i]['variant_name'] = (isset($variant_info[0]['variant_values']) && !empty($variant_info[0]['variant_values'])) ? $variant_info[0]['variant_values'] : "";

                $tax_percentage[$i] = (!empty($product_variant[$i]['tax_percentage'])) ? $product_variant[$i]['tax_percentage'] : 0;
                if ($tax_percentage[$i] != NUll && $tax_percentage[$i] > 0) {

                    //calculate multiple tax
                    $tax_perctg[$i] = explode(',', $tax_percentage[$i]);
                    $total_tax[$i] = array_sum($tax_perctg[$i]);

                    if ((isset($product_variant[$i]['is_prices_inclusive_tax']) && $product_variant[$i]['is_prices_inclusive_tax'] == 1)) {

                        $pv_price_tax_amount[$i] = $pv_price[$i] - ($pv_price[$i] * (100 / (100 + $total_tax[$i])));
                        $pv_price_without_tax[$i] = $pv_price[$i] - $pv_price_tax_amount[$i];

                        $subtotal_without_tax[$i] = ($pv_price_without_tax[$i]) * $quantity[$i];
                    }

                    $tax_amount[$i] = ($subtotal_without_tax[$i] * $total_tax[$i]) / 100;
                } else {
                    $tax_amount[$i] = 0;
                    $tax_percentage[$i] = 0;
                }

                $gross_total += $subtotal[$i];
                $total += $subtotal[$i];
                $total = round($total, 2);
                $gross_total = round($gross_total, 2);

                array_push($cart_data, array(
                    'name' => $pro_name[$i],
                    'tax_amount' => $tax_amount[$i],
                    'qty' => $quantity[$i],
                    'sub_total' => $subtotal[$i],
                ));
            }

            /* Calculating Promo Discount */
            if (isset($data['promo_code']) && !empty($data['promo_code'])) {

                $promo_code = validate_promo_code($data['promo_code'], $data['user_id'], $gross_total);

                if ($promo_code['error'] == false) {

                    if ($promo_code['data'][0]['discount_type'] == 'percentage') {
                        $promo_code_discount = (isset($promo_code['data'][0]['is_cashback']) && $promo_code['data'][0]['is_cashback'] == 0) ? floatval($total * $promo_code['data'][0]['discount'] / 100) : 0;
                    } else {
                        $promo_code_discount = (isset($promo_code['data'][0]['is_cashback']) && $promo_code['data'][0]['is_cashback'] == 0) ? $promo_code['data'][0]['discount'] : 0;
                    }
                    if ($promo_code_discount <= $promo_code['data'][0]['max_discount_amount']) {
                        $total = (isset($promo_code['data'][0]['is_cashback']) && $promo_code['data'][0]['is_cashback'] == 0) ? floatval($total) - $promo_code_discount : floatval($total);
                    } else {
                        $total = (isset($promo_code['data'][0]['is_cashback']) && $promo_code['data'][0]['is_cashback'] == 0) ? floatval($total) - $promo_code['data'][0]['max_discount_amount'] : floatval($total);
                        $promo_code_discount = $promo_code['data'][0]['max_discount_amount'];
                    }
                } else {
                    return $promo_code;
                }
            }
            //create parcel seller wise

            $parcels = array();

            for ($i = 0; $i < count($product_variant_id); $i++) {
                $product_variant[$i]['qty'] = $quantity[$i];
            }

            foreach ($product_variant as $product) {

                $prctg = (isset($product['tax_percentage']) && intval($product['tax_percentage']) > 0 && $product['tax_percentage'] != null) ? $product['tax_percentage'] : '0';
                if ((isset($product['is_prices_inclusive_tax']) && $product['is_prices_inclusive_tax'] == 0) || (!isset($product['is_prices_inclusive_tax'])) && $prctg > 0) {
                    $price_tax_amount = $product['price'] * ($prctg / 100);
                    $special_price_tax_amount = $product['special_price'] * ($prctg / 100);
                } else {
                    $price_tax_amount = 0;
                    $special_price_tax_amount = 0;
                }

                if (floatval($product['special_price']) > 0) {
                    $product['total'] = floatval($product['special_price'] + $special_price_tax_amount) * $product['qty'];
                } else {
                    $product['total'] = floatval($product['price'] + $price_tax_amount) * $product['qty'];
                }

                $parcels[$product['seller_id']]['variant_id'] .= (isset($parcels[$product['seller_id']][$product['id']]) && !empty($product['id'])) ? $parcels[$product['seller_id']] : $product['id'] . ',';
                $parcels[$product['seller_id']]['total'] += (isset($parcels[$product['seller_id']][$product['total']]) && !empty($product['total'])) ? $parcels[$product['seller_id']] : $product['total'];
            }

            $parcel_sub_total = 0.0;
            foreach ($parcels as $seller_id => $parcel) {

                $parcel_sub_total += $parcel['total'];
            }

            //end of parcels making

            $final_total = $total + intval($delivery_charge) - $discount;
            $final_total = round($final_total, 2);

            /* Calculating Wallet Balance */
            $total_payable = $final_total;
            if ($data['is_wallet_used'] == '1' && $data['wallet_balance_used'] <= $final_total) {

                $wallet_balance = update_wallet_balance('debit', $data['user_id'], $data['wallet_balance_used'], "Used against Order Placement");
                if ($wallet_balance['error'] == false) {
                    $total_payable -= $data['wallet_balance_used'];
                    $Wallet_used = true;
                } else {
                    $response['error'] = true;
                    $response['message'] = $wallet_balance['message'];
                    return $response;
                }
            } else {
                if ($data['is_wallet_used'] == 1) {
                    $response['error'] = true;
                    $response['message'] = 'Wallet Balance should not exceed the total amount';
                    return $response;
                }
            }
            //upload attachments
            $status = (isset($data['active_status']) && !empty($data['active_status'])) ? $data['active_status'] : 'received';

            if (isset($data['wallet_balance_used']) && $data['wallet_balance_used'] == $final_total) {
                $status = 'received';
            }

            $order_data = [
                'user_id' => $data['user_id'],
                'mobile' => (isset($data['mobile']) && !empty($data['mobile']) && $data['mobile'] != '' && $data['mobile'] != 'NULL') ? $data['mobile'] : '',
                'total' => $gross_total,
                'promo_discount' => (isset($promo_code_discount) && $promo_code_discount != NULL) ? $promo_code_discount : '0',
                'total_payable' => $total_payable,
                'delivery_charge' => intval($delivery_charge),
                'is_delivery_charge_returnable' => isset($data['is_delivery_charge_returnable']) && !empty($data['is_delivery_charge_returnable']) ? $data['is_delivery_charge_returnable'] : 0,
                'wallet_balance' => (isset($Wallet_used) && $Wallet_used == true) ? $data['wallet_balance_used'] : '0',
                'final_total' => $final_total,
                'discount' => $discount,
                'payment_method' => $data['payment_method'],
                'attachments' => '',
                'promo_code' => (isset($data['promo_code']) && !empty($data['promo_code'])) ? $data['promo_code'] : ' ',
                'email' => isset($data['email']) && !empty($data['email']) ? $data['email'] : ' ',
                'is_pos_order' => isset($data['is_pos_order']) && !empty($data['is_pos_order']) ? $data['is_pos_order'] : 0,
                'is_shiprocket_order' => isset($data['is_shiprocket_order']) ? $data['is_shiprocket_order'] : 0
            ];

            if ($data['payment_method'] == "phonepe") {
                $order_data['status'] = $status;
            }
            $order_data['address_id'] = (isset($data['address_id']) && !empty($data['address_id']) ? $data['address_id'] : '');

            if (isset($data['delivery_date']) && !empty($data['delivery_date']) && !empty($data['delivery_time']) && isset($data['delivery_time'])) {
                $order_data['delivery_date'] = date('Y-m-d', strtotime($data['delivery_date']));
                $order_data['delivery_time'] = $data['delivery_time'];
            }
            if (isset($data['address_id']) && !empty($data['address_id'])) {
                $address_data = $CI->address_model->get_address('', $data['address_id'], true);
                if (!empty($address_data)) {
                    $order_data['latitude'] = $address_data[0]['latitude'];
                    $order_data['longitude'] = $address_data[0]['longitude'];
                    $order_data['address'] = (!empty($address_data[0]['address']) && $address_data[0]['address'] != 'NULL') ? $address_data[0]['address'] . ', ' : '';
                    $order_data['address'] .= (!empty($address_data[0]['landmark']) && $address_data[0]['landmark'] != 'NULL') ? $address_data[0]['landmark'] . ', ' : '';
                    $order_data['address'] .= (!empty($address_data[0]['area']) && $address_data[0]['area'] != 'NULL') ? $address_data[0]['area'] . ', ' : '';
                    $order_data['address'] .= (!empty($address_data[0]['city']) && $address_data[0]['city'] != 'NULL') ? $address_data[0]['city'] . ', ' : '';
                    $order_data['address'] .= (!empty($address_data[0]['state']) && $address_data[0]['state'] != 'NULL') ? $address_data[0]['state'] . ', ' : '';
                    $order_data['address'] .= (!empty($address_data[0]['country']) && $address_data[0]['country'] != 'NULL') ? $address_data[0]['country'] . ', ' : '';
                    $order_data['address'] .= (!empty($address_data[0]['pincode']) && $address_data[0]['pincode'] != 'NULL') ? $address_data[0]['pincode'] : '';
                }
            } else {
                $order_data['address'] = "";
            }
            if (!empty($_POST['latitude']) && !empty($_POST['longitude'])) {
                $order_data['latitude'] = $_POST['latitude'];
                $order_data['longitude'] = $_POST['longitude'];
            }
            $order_data['notes'] = $data['order_note'];

            $this->db->insert('orders', $order_data);
            $last_order_id = $this->db->insert_id();
            if (isset($data['is_pos_order']) && $data['is_pos_order'] == 1) {
                // Define the input array
                $statuses = [
                    ["received", date("d-m-Y h:i:sa")],
                    ["processed", date("d-m-Y h:i:sa")],
                    ["shipped", date("d-m-Y h:i:sa")],
                    ["delivered", date("d-m-Y h:i:sa")]
                ];

                $output = [];

                // Loop through each status and time pair
                foreach ($statuses as $all_status) {
                    // Add the formatted status and time to the output array
                    $output[] = [$all_status[0], $all_status[1]];
                }

                // Convert the output array to a JSON string
                $jsonOutput = json_encode($output);
            }

            $attachments = (isset($data['attachments']) && !empty($data['attachments'])) ? $data['attachments'] : '';
            $affiliate_data = (isset($data['affiliate_data']) && !empty($data['affiliate_data'])) ? $data['affiliate_data'] : '';
            // print_r($affiliate_data);
            // die;

            for ($i = 0; $i < count($product_variant); $i++) {

                $variant_id = $product_variant[$i]['id']; // Get the product variant ID

                $product_variant_data[$i] = [
                    'user_id' => $data['user_id'],
                    'order_id' => $last_order_id,
                    'seller_id' => $product_variant[$i]['seller_id'],
                    'product_name' => $product_variant[$i]['product_name'],
                    'product_type' => $product_variant[$i]['product_type'],
                    'product_image' => $product_variant[$i]['image'],
                    'deliveryboy_otp_setting_on' => $system_settings['is_delivery_boy_otp_setting_on'],
                    'product_is_cancelable' => $product_variant[$i]['is_cancelable'],
                    'product_is_returnable' => $product_variant[$i]['is_returnable'],
                    'variant_name' => $product_variant[$i]['variant_name'],
                    'product_variant_id' => $product_variant[$i]['id'],
                    'quantity' => $quantity[$i],
                    'price' => $pv_price[$i],
                    'tax_ids' => $tax_ids[$i],
                    'tax_percent' => $total_tax[$i],
                    'tax_amount' => $tax_amount[$i],
                    'sub_total' => $subtotal[$i],
                    'status' => (isset($data['is_pos_order']) && $data['is_pos_order'] == 1) ? (json_encode($output)) : json_encode(array(array($status, date("d-m-Y h:i:sa")))),
                    'active_status' => $status,
                    'otp' => 0,
                    'attachment' => isset($attachments[$variant_id]) ? implode(',', $attachments[$variant_id]) : '',
                    'affiliate_id' => isset($affiliate_data[$variant_id]['affiliate_id']) ? $affiliate_data[$variant_id]['affiliate_id'] : '',
                    'affiliate_token' => isset($affiliate_data[$variant_id]['affiliate_token']) ? $affiliate_data[$variant_id]['affiliate_token'] : '',
                    'affiliate_commission' => isset($affiliate_data[$variant_id]['category_commission']) ? $affiliate_data[$variant_id]['category_commission'] : '',
                    'affiliate_commission_amount' => isset($affiliate_data[$variant_id]['affiliate_commission_amount']) ? $affiliate_data[$variant_id]['affiliate_commission_amount'] : '',
                ];

                $this->db->insert('order_items', $product_variant_data[$i]);
                $order_item_id = $this->db->insert_id();
                if (isset($product_variant[$i]['download_link']) && !empty($product_variant[$i]['download_link'])) {
                    $hash_link = $product_variant[$i]['download_link'] . '?' . $order_item_id;
                    $hash_link_data['hash_link'] = $hash_link;
                    $this->db->where('id', $order_item_id)->update('order_items', $hash_link_data);
                }
            }

            //make order_charges_parcel and insert in table
            $discount_percentage = 0.00;
            foreach ($parcels as $seller_id => $parcel) {
                $discount_percentage = ($parcel['total'] * 100) / $parcel_sub_total;

                $seller_promocode_discount = ($promo_code_discount * $discount_percentage) / 100;

                $seller_delivery_charge = ($delivery_charge * $discount_percentage) / 100;

                $otp = mt_rand(100000, 999999);

                $order_item_ids = '';
                $varient_ids = explode(',', trim($parcel['variant_id'], ','));
                $parcel_total = $parcel['total'] + intval($parcel['delivery_charge']) - $seller_promocode_discount;
                $parcel_total = round($parcel_total, 2);
                foreach ($varient_ids as $ids) {
                    $order_item_ids .= fetch_details('order_items', ['seller_id' => $seller_id, 'product_variant_id' => $ids, 'order_id' => $last_order_id], 'id')[0]['id'] . ',';
                }
                $order_item_id = explode(',', trim($order_item_ids, ','));
                foreach ($order_item_id as $ids) {
                    update_details(['otp' => $otp], ['id' => $ids], 'order_items');
                }
                $order_parcels = [
                    'seller_id' => $seller_id,
                    'product_variant_ids' => trim($parcel['variant_id'], ','),
                    'order_id' => $last_order_id,
                    'order_item_ids' => trim($order_item_ids, ','),
                    'delivery_charge' => round($seller_delivery_charge, 2),
                    'promo_code' => $data['promo_code'],
                    'promo_discount' => round($seller_promocode_discount, 2),
                    'sub_total' => $parcel['total'],
                    'total' => $parcel_total,
                    'otp' => ($system_settings['is_delivery_boy_otp_setting_on'] == '1') ? $otp : 0,
                ];
                $this->db->insert('order_charges', $order_parcels);
            }

            //end
            $product_variant_ids = explode(',', $data['product_variant_id']);

            $qtns = explode(',', $data['quantity'] ?? '');

            update_stock($product_variant_ids, $qtns);

            $overall_total = array(
                'total_amount' => array_sum($subtotal),
                'delivery_charge' => $delivery_charge,
                'discount' => $discount,
                'tax_amount' => array_sum($tax_amount),
                'tax_percentage' => array_sum($tax_percentage),
                'discount' => $order_data['promo_discount'],
                'wallet' => $order_data['wallet_balance'],
                'final_total' => $order_data['final_total'],
                'total_payable' => $order_data['total_payable'],
                'otp' => $otp,
                'address' => (isset($order_data['address'])) ? $order_data['address'] : '',
                'payment_method' => $data['payment_method']
            );

            //send custom notifications
            $custom_notification = fetch_details('custom_notifications', ['type' => "place_order"], '');
            $hashtag_order_id = '< order_id >';
            $string = json_encode($custom_notification[0]['title'], JSON_UNESCAPED_UNICODE);
            $hashtag = html_entity_decode($string);
            $data1 = str_replace($hashtag_order_id, $last_order_id, $hashtag);
            $title = output_escaping(trim($data1, '"'));
            $hashtag_application_name = '< application_name >';

            //for message user
            $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
            $hashtag = html_entity_decode($string);
            $data1 = str_replace(array($hashtag_order_id, $hashtag_application_name), array($last_order_id, $system_settings['app_name']), $hashtag);
            $message = output_escaping(trim($data1, '"'));

            //title for user
            $custom_notification_seller = fetch_details('custom_notifications', ['type' => "seller_place_order"], '');
            $string_seller = json_encode($custom_notification_seller[0]['title'], JSON_UNESCAPED_UNICODE);
            $hashtag_seller = html_entity_decode($string_seller);
            $data1_seller = str_replace($hashtag_order_id, $last_order_id, $hashtag_seller);
            $title_seller = output_escaping(trim($data1_seller, '"'));

            // messgae for user
            $string_seller = json_encode($custom_notification_seller[0]['message'], JSON_UNESCAPED_UNICODE);
            $hashtag_seller = html_entity_decode($string_seller);
            $data1_seller = str_replace(array($hashtag_order_id, $hashtag_application_name), array($last_order_id, $system_settings['app_name']), $hashtag_seller);
            $message_seller = output_escaping(trim($data1_seller, '"'));



            $fcm_admin_subject = (!empty($custom_notification)) ? $title : 'New order placed ID #' . $last_order_id;
            $fcm_admin_msg = (!empty($custom_notification)) ? $message : 'New order received for  ' . $system_settings['app_name'] . ' please process it.';


            $fcm_seller_subject = (!empty($custom_notification_seller)) ? $title_seller : 'New order placed ID #' . $last_order_id;
            $fcm_seller_msg = (!empty($custom_notification_seller)) ? $message_seller : 'New order received for  ' . $system_settings['app_name'] . ' please process it.';

            if (trim(strtolower($data['payment_method'])) != 'paypal' || trim(strtolower($data['payment_method'])) != 'stripe') {
                $overall_order_data = array(
                    'rows' => $cart_data,
                    'order_id' => $last_order_id,
                    'order_data' => $overall_total,
                    'subject' => $fcm_admin_subject,
                    'user_data' => $user[0],
                    'system_settings' => $system_settings,
                    'user_msg' => $fcm_admin_msg,
                    'otp_msg' => 'Here is your OTP. Please, give it to delivery boy only while getting your order.',
                );

                $system_settings = get_settings('system_settings', true);
                $sellerEmail = [];
                $sellerPhone = [];

                if (isset($system_settings['support_email']) && !empty($system_settings['support_email'])) {
                    send_mail($system_settings['support_email'], $fcm_admin_subject, $fcm_admin_msg);
                }
                for ($i = 0; $i < count($seller_ids); $i++) {
                    $seller_email = fetch_details('users', ['id' => $seller_ids[$i]]);
                    $sellerPhone[] = $seller_email[0]['mobile'];
                    $sellerEmail[] = $seller_email[0]['email'];
                    $seller_store_name = fetch_details('seller_data', ['user_id' => $seller_ids[$i]], 'store_name');
                    if (isset($_POST['active_status']) && $_POST['active_status'] != 'awaiting') {
                        send_mail($seller_email[0]['email'], $fcm_admin_subject, $fcm_admin_msg);
                    }
                }

                $user_fcm = fetch_details('user_fcm', ['user_id' => $data['user_id']], 'fcm_id,platform_type');
                // $user_fcm = fetch_details('users', ['id' => $data['user_id']], 'fcm_id,platform_type');

                // Step 1: Group by platform
                $groupedByPlatform = [];
                foreach ($user_fcm as $item) {
                    $platform = $item['platform_type'];
                    $groupedByPlatform[$platform][] = $item['fcm_id'];
                }

                // Step 2: Chunk each platform group into arrays of 1000
                $fcm_ids_user = [];
                foreach ($groupedByPlatform as $platform => $fcmIds) {
                    $fcm_ids_user[$platform] = array_chunk($fcmIds, 1000);
                }

                foreach ($parcels as $seller_id => $parcel) {
                    $seller_fcm = fetch_details('users', ['id' => $seller_id], 'fcm_id, platform_type');
                    // Step 1: Group by platform
                    $groupedByPlatform = [];
                    foreach ($seller_fcm as $item) {
                        $platform = $item['platform_type'];
                        $groupedByPlatform[$platform][] = $item['fcm_id'];
                    }

                    // Step 2: Chunk each platform group into arrays of 1000
                    $fcm_ids_seller = [];
                    foreach ($groupedByPlatform as $platform => $fcmIds) {
                        $fcm_ids_seller[$platform] = array_chunk($fcmIds, 1000);
                    }

                    $seller_fcm_id[0] = $fcm_ids_seller[0]['fcm_id'];
                }
                $registrationIDs_chunks = $fcm_ids_seller;
                $registrationIDs_chunks_user = $fcm_ids_user;

                if (!empty($registrationIDs_chunks) || !empty($registrationIDs_chunks_user)) {

                    $fcmMsg = array(
                        'title' => $fcm_admin_subject,
                        'body' => $fcm_admin_msg,
                        'type' => "place_order",
                        'order_id' => (string) $last_order_id,
                    );
                    $fcmMsg_seller = array(
                        'title' => $fcm_seller_subject,
                        'body' => $fcm_seller_msg,
                        'type' => "place_order",
                        'order_id' => (string) $last_order_id,
                    );
                    $firebase_project_id = get_settings('firebase_project_id');
                    $service_account_file = get_settings('service_account_file');

                    if (isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                        if (isset($_POST['active_status']) && $_POST['active_status'] != 'awaiting') {
                            send_notification($fcmMsg, $registrationIDs_chunks_user, $fcmMsg);
                            send_notification($fcmMsg_seller, $registrationIDs_chunks, $fcmMsg_seller);
                        }
                    }
                }
                $admin_notifi = array(
                    'title' => $fcm_admin_subject,
                    'message' => $fcm_admin_msg,
                    'type' => "place_order",
                    'type_id' => (string) $last_order_id
                );
                insert_details($admin_notifi, 'system_notification');
                if (isset($_POST['active_status']) && $_POST['active_status'] != 'awaiting') {
                    for ($i = 0; $i < count($seller_ids); $i++) {
                        $sellers = fetch_details('users', ['id' => $seller_ids[$i]], ['email', 'mobile']);

                        // for customer place order
                        send_mail($user[0]['email'], $fcm_admin_subject, $this->load->view('admin/pages/view/email-template.php', $overall_order_data, TRUE));

                        (notify_event(
                            "place_order",
                            ["customer" => []],
                            ["customer" => [$user[0]['mobile']]],
                            ["orders.id" => $last_order_id]
                        ));

                        //for seller place order
                        (notify_event(
                            "seller_place_order",
                            ["seller" => [$sellers[0]['email']]],
                            ["seller" => [$sellers[0]['mobile']]],
                            ["orders.id" => $last_order_id]
                        ));
                    }
                }
            }

            setcookie('affiliate_ref', '', time() - 3600, '/');

            $this->cart_model->remove_from_cart($data);
            $user_balance = fetch_details('users', ['id' => $data['user_id']], 'balance');

            $response['error'] = false;
            $response['message'] = 'Order Placed Successfully';
            $response['order_id'] = $last_order_id;
            $response['order_item_data'] = $product_variant_data;
            $response['balance'] = $user_balance;
            return $response;
        } else {
            $user_balance = fetch_details('users', ['id' => $data['user_id']], 'balance');

            $response['error'] = true;
            $response['message'] = "Product(s) Not Found!";
            $response['balance'] = $user_balance;
            return $response;
        }
    }

    public function get_order_details($where = NULL, $status = false, $seller_id = NULL)
    {
        $res = $this->db->select('oi.*,ot.courier_agency,ot.tracking_id,ot.url,oi.otp as item_otp,a.name as user_name,oi.id as order_item_id, 
        oi.seller_id as order_seller_id, p.*,v.product_id,o.*,o.email as user_email,o.id as order_id,o.total as order_total,o.wallet_balance,
        oi.active_status as oi_active_status,u.email,u.username as uname, u.country_code as country_code,oi.status as order_status,p.id as product_id,
        p.pickup_location as pickup_location,p.slug as product_slug,p.sku as product_sku,v.sku, v.price as product_price,v.special_price as product_special_price ,
        p.name as pname,p.type,p.image as product_image,p.is_prices_inclusive_tax,
        (SELECT username FROM users db where db.id=oi.delivery_boy_id ) as delivery_boy ,
        (SELECT mobile FROM addresses a where a.id=o.address_id ) as mobile_number ')
            ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
            ->join('products p ', ' p.id = v.product_id ', 'left')
            ->join('users u ', ' u.id = oi.user_id', 'left')
            ->join('orders o ', 'o.id=oi.order_id', 'left')
            ->join('order_tracking ot ', 'ot.order_item_id=oi.id', 'left')
            ->join('addresses a', 'a.id=o.address_id', 'left')
            ->group_by('oi.id');

        if (isset($where) && $where != NULL) {
            $res->where($where);
            if ($status == true) {
                $res->group_Start()
                    ->where_not_in(' `oi`.active_status ', array('cancelled', 'returned'))
                    ->group_End();
            }
        }
        if (!isset($where) && $status == true) {
            $res->where_not_in(' `oi`.active_status ', array('cancelled', 'returned'));
        }
        $order_result = $res->get(' `order_items` oi')->result_array();
        if (!empty($order_result)) {
            for ($i = 0; $i < count($order_result); $i++) {
                $order_result[$i] = output_escaping($order_result[$i]);
            }
        }
        return $order_result;
    }

    public function get_order_item_details($where = NULL, $status = false, $seller_id = NULL)
    {
        $res = $this->db->select('oi.*, ot.courier_agency, ot.tracking_id, ot.url, oi.otp as item_otp, a.name as user_name, oi.id as order_item_id, oi.seller_id as order_seller_id, p.*, v.product_id, o.*, o.email as user_email, o.id as order_id, o.total as order_total, o.wallet_balance, oi.active_status as oi_active_status, u.email, u.username as uname, u.country_code as country_code, oi.status as order_status, p.id as product_id, p.pickup_location as pickup_location, p.slug as product_slug, p.sku as product_sku, v.sku, v.price as product_price, v.special_price as product_special_price, p.name as pname, p.type, p.image as product_image, p.is_prices_inclusive_tax, (SELECT username FROM users db WHERE db.id = oi.delivery_boy_id) as delivery_boy, (SELECT mobile FROM addresses a WHERE a.id = o.address_id) as mobile_number')
            ->join('product_variants v', 'oi.product_variant_id = v.id', 'left')
            ->join('products p', 'p.id = v.product_id', 'left')
            ->join('users u', 'u.id = oi.user_id', 'left')
            ->join('orders o', 'o.id = oi.order_id', 'left')
            ->join('order_tracking ot', 'ot.order_item_id = oi.id', 'left')
            ->join('addresses a', 'a.id = o.address_id', 'left');

        // Check if a specific condition is provided and is not null
        if (isset($where) && $where != NULL) {
            $res->where($where);

            if ($status == true) {
                $res->group_Start()
                    ->where_not_in('oi.active_status', array('cancelled', 'returned'))
                    ->group_End();
            }
        }

        // If no specific condition but status check is true
        if (!isset($where) && $status == true) {
            $res->where_not_in('oi.active_status', array('cancelled', 'returned'));
        }

        // Fetch the result from order_items table
        $order_result = $res->get('order_items oi')->result_array();

        // Process the result if not empty
        if (!empty($order_result)) {
            for ($i = 0; $i < count($order_result); $i++) {
                $order_result[$i] = output_escaping($order_result[$i]);
            }
        }

        return $order_result;
    }


    public function get_orders_list(
        $delivery_boy_id = NULL,
        $offset = 0,
        $limit = 10,
        $sort = " o.id ",
        $order = 'ASC'
    ) {

        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];

            $filters = [
                'u.username' => $search,
                'db.username' => $search,
                'u.email' => $search,
                'o.id' => $search,
                'o.mobile' => $search,
                'o.address' => $search,
                'o.wallet_balance' => $search,
                'o.total' => $search,
                'o.final_total' => $search,
                'o.total_payable' => $search,
                'o.payment_method' => $search,
                'o.delivery_charge' => $search,
                'o.delivery_time' => $search,
                'oi.status' => $search,
                'oi.active_status' => $search,
                'o.date_added' => $search
            ];
        }

        $count_res = $this->db->select(' COUNT(DISTINCT(o.id)) as `total` ')
            ->join(' `users` u', 'u.id= o.user_id', 'left')
            ->join(' `order_items` oi', 'oi.order_id= o.id', 'left')
            ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
            ->join('products p ', ' p.id = v.product_id ', 'left')
            ->join('users db ', ' db.id = oi.delivery_boy_id', 'left');
        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {

            $count_res->where(" DATE(o.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $count_res->where(" DATE(o.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $this->db->group_Start();
            $count_res->or_like($filters);
            $this->db->group_End();
        }

        if (isset($delivery_boy_id)) {
            $count_res->where("oi.delivery_boy_id", $delivery_boy_id);
        }
        if (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) {
            $count_res->where("oi.seller_id", $_GET['seller_id']);
        }
        if (isset($_GET['user_id']) && $_GET['user_id'] != null) {
            $count_res->where("o.user_id", $_GET['user_id']);
        }
        // Filter By payment
        if (isset($_GET['payment_method']) && !empty($_GET['payment_method'])) {
            $count_res->where('payment_method', $_GET['payment_method']);
        }
        // Filter By order type
        if (isset($_GET['order_type']) && !empty($_GET['order_type']) && $_GET['order_type'] == 'physical_order') {
            $count_res->where('p.type!=', 'digital_product');
        }
        if (isset($_GET['order_type']) && !empty($_GET['order_type']) && $_GET['order_type'] == 'digital_order') {
            $count_res->where('p.type', 'digital_product');
        }
        if (!empty($_GET['order_status']) && !empty($_GET['order_status'])) {

            $count_res->where(" oi.active_status ", $_GET['order_status']);
        }


        $product_count = $count_res->get('`orders` o')->result_array();

        foreach ($product_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' o.* , u.username , u.country_code as country_code, db.username as delivery_boy')
            ->join(' `users` u', 'u.id= o.user_id', 'left')
            ->join(' `order_items` oi', 'oi.order_id= o.id', 'left')
            ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
            ->join('products p ', ' p.id = v.product_id ', 'left')
            ->join('users db ', ' db.id = oi.delivery_boy_id', 'left');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $search_res->where(" DATE(o.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $search_res->where(" DATE(o.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $search_res->group_Start();
            $search_res->or_like($filters);
            $search_res->group_End();
        }

        if (isset($delivery_boy_id)) {
            $search_res->where("oi.delivery_boy_id", $delivery_boy_id);
        }

        if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $search_res->where("o.user_id", $_GET['user_id']);
        }

        if (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) {
            $search_res->where("oi.seller_id", $_GET['seller_id']);
        }
        if (!empty($_GET['order_status']) && !empty($_GET['order_status'])) {

            $count_res->where(" oi.active_status ", $_GET['order_status']);
        }
        // Filter By payment
        if (isset($_GET['payment_method']) && !empty($_GET['payment_method'])) {
            $count_res->where('payment_method', $_GET['payment_method']);
        }

        // Filter By order type
        if (isset($_GET['order_type']) && !empty($_GET['order_type']) && $_GET['order_type'] == 'physical_order') {
            $search_res->where('p.type!=', 'digital_product');
        }
        if (isset($_GET['order_type']) && !empty($_GET['order_type']) && $_GET['order_type'] == 'digital_order') {
            $search_res->where('p.type', 'digital_product');
        }

        $user_details = $search_res->group_by('o.id')->order_by($sort, "DESC")->limit($limit, $offset)->get('`orders` o')->result_array();

        $i = 0;
        foreach ($user_details as $row) {


            $user_details[$i]['items'] = $this->db->select('oi.*,p.name as name,p.id as product_id,p.type,p.download_allowed, u.username as uname, us.username as seller ')
                ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
                ->join('products p ', ' p.id = v.product_id ', 'left')
                ->join('users u ', ' u.id = oi.user_id', 'left')
                ->join('users us ', ' us.id = oi.seller_id', 'left')
                ->where('oi.order_id', $row['id'])
                ->get(' `order_items` oi  ')->result_array();

            ++$i;
        }
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $tota_amount = 0;
        $final_tota_amount = 0;
        $currency_symbol = get_settings('currency');
        foreach ($user_details as $row) {

            if (!empty($row['items'])) {

                $items = $row['items'];
                $items1 = '';
                $temp = '';
                $total_amt = $total_qty = 0;
                $seller = implode(",", array_values(array_unique(array_column($items, "seller"))));

                // foreach ($items as $item) {
                //     $product_variants = get_variants_values_by_id($item['product_variant_id']);
                //     $variants = isset($product_variants[0]['variant_values']) && !empty($product_variants[0]['variant_values']) ? str_replace(',', ' | ', $product_variants[0]['variant_values']) : '-';
                //     $temp .= "<b>ID :</b>" . $item['id'] . "<b> Product Variant Id :</b> " . $item['product_variant_id'] . "<b> Variants :</b> " . $variants . "<b> Name : </b>" . $item['name'] . " <b>Price : </b>" . $item['price'] . " <b>QTY : </b>" . $item['quantity'] . " <b>Subtotal : </b>" . $item['quantity'] * $item['price'] . "<br>------<br>";
                //     $total_amt += $item['sub_total'];
                //     $total_qty += $item['quantity'];
                // }

                foreach ($items as $item) {
                    $product_variants = get_variants_values_by_id($item['product_variant_id']);
                    $variants = isset($product_variants[0]['variant_values']) && !empty($product_variants[0]['variant_values']) ? str_replace(',', ' | ', $product_variants[0]['variant_values']) : '-';

                    $temp .= "<b>ID :</b>" . $item['id'] .
                        "<b> Product Variant Id :</b> " . $item['product_variant_id'] .
                        "<b> Variants :</b> " . $variants .
                        "<b> Name : </b>" . $item['name'] .
                        " <b>Price : </b>" . $item['price'] .
                        " <b>QTY : </b>" . $item['quantity'] .
                        " <b>Subtotal : </b>" . $item['quantity'] * $item['price'] . "<br>------<br>";

                    $total_amt += $item['sub_total'];
                    $total_qty += $item['quantity'];
                }

                $items1 = $temp;
                $discounted_amount = $row['total'] * $row['items'][0]['discount'] / 100;
                $final_total = $row['total'] - $discounted_amount;
                $discount_in_rupees = $row['total'] - $final_total;
                $discount_in_rupees = floor($discount_in_rupees);
                $tempRow['id'] = $row['id'];
                $tempRow['user_id'] = $row['user_id'];
                $tempRow['name'] = $row['items'][0]['uname'];
                if (isset($row['mobile']) && !empty($row['mobile']) && $row['mobile'] != "" && $row['mobile'] != " ") {
                    $tempRow['mobile'] = (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($row['mobile']) - 3) . substr($row['mobile'], -3) : $row['mobile'];
                } else {
                    $tempRow['mobile'] = "";
                }
                $tempRow['delivery_charge'] = $currency_symbol . ' ' . $row['delivery_charge'];
                $tempRow['items'] = $items1;
                $tempRow['sellers'] = $seller;
                $tempRow['total'] = $currency_symbol . ' ' . $row['total'];
                $tota_amount += $row['total'];
                $tempRow['wallet_balance'] = $currency_symbol . ' ' . $row['wallet_balance'];
                $tempRow['discount'] = $currency_symbol . ' ' . $discount_in_rupees . '(' . $row['items'][0]['discount'] . '%)';
                $tempRow['promo_discount'] = $currency_symbol . ' ' . $row['promo_discount'];
                $tempRow['promo_code'] = $row['promo_code'];
                $tempRow['notes'] = $row['notes'];
                $tempRow['qty'] = $total_qty;
                $tempRow['final_total'] = $currency_symbol . ' ' . $row['total_payable'];
                // $final_total = $row['final_total'] - $row['wallet_balance'] - $row['discount'];
                // $tempRow['final_total'] = $currency_symbol . ' ' . $final_total;
                $final_tota_amount += $row['final_total'];
                $tempRow['deliver_by'] = $row['delivery_boy'];
                $tempRow['payment_method'] = ($row['payment_method'] == "COD" && $row['is_pos_order'] == 1) ? 'cash Payment' : str_replace('_', ' ', $row['payment_method']);
                $updated_username = fetch_details('users', 'id =' . $row['items'][0]['updated_by'], 'username');
                $tempRow['updated_by'] = $updated_username[0]['username'];
                $tempRow['address'] = output_escaping(str_replace('\r\n', '</br>', $row['address']));
                $tempRow['delivery_date'] = $row['delivery_date'];
                $tempRow['delivery_time'] = $row['delivery_time'];
                $tempRow['date_added'] = date('d-m-Y', strtotime($row['date_added']));
                $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['id'] . '" class="btn action-btn btn-primary btn-xs mr-1 ml-1 mb-1" title="View" ><i class="fa fa-eye"></i></a>';
                if (!$this->ion_auth->is_delivery_boy()) {
                    $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['id'] . ' class="btn action-btn btn-primary btn-xs ml-1 mr-1 mb-1" title="View" ><i class="fa fa-eye"></i></a>';
                    $operate .= '<a href="javascript:void(0)" class="delete-orders btn btn-danger action-btn btn-xs ml-1 mr-1 mb-1" data-id=' . $row['id'] . ' title="Delete" ><i class="fa fa-trash"></i></a>';
                    $operate .= '<a href="' . base_url() . 'admin/invoice?edit_id=' . $row['id'] . '" class="btn action-btn btn-info btn-xs  ml-1 mb-1" title="Invoice" ><i class="fa fa-file"></i></a>';
                    $operate .= '<a href="https://api.whatsapp.com/send?phone=' . $row['country_code'] . $tempRow['mobile'] . '&amp;text=Hello, ' . $row['items'][0]['uname'] . ' Your order with ID : ' . $row['items'][0]['order_id'] . ' and is ' . $row['items'][0]['active_status'] . '. Please take a note of it. If you have further queries feel free to contact us. Thank you." target="_blank" title="Send Whatsapp Notification" class="btn btn-xs ml-1 mr-1 mb-1 btn-success"><i class="fa fa-phone-alt" style="font-size: 16px;color:white"></i></a>';
                    if ($row['items'][0]['type'] != 'digital_product') {
                        $operate .= ' <a href="javascript:void(0)" class="edit_order_tracking btn action-btn btn-success btn-xs ml-1 mr-1 mb-1" title="Order Tracking" data-order_id="' . $row['id'] . '"  data-target="#order-tracking-modal" data-toggle="modal"><i class="fa fa-map-marker-alt"></i></a>';
                    }
                    if ($row['items'][0]['type'] == 'digital_product' && $row['items'][0]['download_allowed'] != 1) {
                        $operate .= ' <a href="javascript:void(0)" class="edit_digital_order_mails action-btn btn btn-warning btn-xs mr-1 ml-1 mb-1" title="Digital Order Mails" data-order_id="' . $row['id'] . '"  data-target="#digital-order-mails" data-toggle="modal"><i class="far fa-envelope-open"></i></a>';
                    }
                } else {
                    $operate = '<a href=' . base_url('delivery_boy/orders/edit_orders') . '?edit_id=' . $row['id'] . ' class="btn action-btn btn-primary btn-xs ml-1 mr-1 mb-1" title="View"><i class="fa fa-eye"></i></a>';
                }
                $tempRow['operate'] = $operate;
                $rows[] = $tempRow;
            }
        }
        if (!empty($user_details)) {
            $tempRow['id'] = '-';
            $tempRow['user_id'] = '-';
            $tempRow['name'] = '-';
            $tempRow['mobile'] = '-';
            $tempRow['delivery_charge'] = '-';
            $tempRow['items'] = '-';
            $tempRow['sellers'] = '-';
            $tempRow['total'] = '<span class="badge badge-danger">' . $currency_symbol . ' ' . $tota_amount . '</span>';
            $tempRow['wallet_balance'] = '-';
            $tempRow['discount'] = '-';
            $tempRow['qty'] = '-';
            $tempRow['final_total'] = '<span class="badge badge-danger">' . $currency_symbol . ' ' . $final_tota_amount . '</span>';
            $tempRow['deliver_by'] = '-';
            $tempRow['payment_method'] = '-';
            $tempRow['address'] = '-';
            $tempRow['delivery_time'] = '-';
            $tempRow['status'] = '-';
            $tempRow['active_status'] = '-';
            $tempRow['wallet_balance'] = '-';
            $tempRow['date_added'] = '-';
            $tempRow['operate'] = '-';
            array_push($rows, $tempRow);
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    public function get_order_items_list($delivery_boy_id = NULL, $offset = 0, $limit = 10, $sort = " oi.id ", $order = 'ASC', $seller_id = NULL)
    {
        $customer_privacy = false;
        if (isset($seller_id) && $seller_id != "") {
            $customer_privacy = get_seller_permission($seller_id, 'customer_privacy');
        }

        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];

            $filters = [
                'un.username' => $search,
                'u.username' => $search,
                'us.username' => $search,
                'un.email' => $search,
                'oi.id' => $search,
                'o.mobile' => $search,
                'o.address' => $search,
                'o.payment_method' => $search,
                'oi.sub_total' => $search,
                'o.delivery_time' => $search,
                'oi.active_status' => $search,
                'oi.date_added' => $search
            ];
        }

        $count_res = $this->db->select(' COUNT(o.id) as `total` ')
            ->join(' `users` u', 'u.id= oi.delivery_boy_id', 'left')
            ->join('users us ', ' us.id = oi.seller_id', 'left')
            ->join(' `orders` o', 'o.id= oi.order_id')
            ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
            ->join('products p ', ' p.id = v.product_id ', 'left')
            ->join('users un ', ' un.id = o.user_id', 'left');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {

            $count_res->where(" DATE(oi.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $count_res->where(" DATE(oi.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $this->db->group_Start();
            $count_res->or_like($filters);
            $this->db->group_End();
        }

        if (isset($delivery_boy_id)) {
            $count_res->where("oi.delivery_boy_id", $delivery_boy_id);
        }

        if (isset($seller_id) && $seller_id != "") {
            $count_res->where("oi.seller_id", $seller_id);
            $count_res->where("oi.active_status != 'awaiting'");
        }

        if (isset($_GET['user_id']) && $_GET['user_id'] != null) {
            $count_res->where("o.user_id", $_GET['user_id']);
        }

        if (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) {
            $count_res->where("oi.seller_id", $_GET['seller_id']);
        }

        if (isset($_GET['order_status']) && !empty($_GET['order_status'])) {
            $count_res->where('oi.active_status', $_GET['order_status']);
        }
        // Filter By payment
        if (isset($_GET['payment_method']) && !empty($_GET['payment_method'])) {
            $count_res->where('payment_method', $_GET['payment_method']);
        }
        // Filter By order type
        if (isset($_GET['order_type']) && !empty($_GET['order_type']) && $_GET['order_type'] == 'physical_order') {
            $count_res->where('p.type!=', 'digital_product');
        }
        if (isset($_GET['order_type']) && !empty($_GET['order_type']) && $_GET['order_type'] == 'digital_order') {
            $count_res->where('p.type', 'digital_product');
        }

        $product_count = $count_res->get('order_items oi')->result_array();
        foreach ($product_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' o.id as order_id,oi.id as order_item_id,o.*,oi.*,ot.courier_agency,ot.tracking_id,ot.url,t.status as transaction_status, u.username as delivery_boy, un.username as username,us.username as seller_name,p.type,p.download_allowed')
            ->join('users u', 'u.id= oi.delivery_boy_id', 'left')
            ->join('users us ', ' us.id = oi.seller_id', 'left')
            ->join('order_tracking ot ', ' ot.order_item_id = oi.id', 'left')
            ->join('orders o', 'o.id= oi.order_id', 'left')
            ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
            ->join('products p ', ' p.id = v.product_id ', 'left')
            ->join('transactions t ', ' t.order_item_id = oi.id ', 'left')
            ->join('users un ', ' un.id = o.user_id', 'left')
            ->group_by('oi.order_id');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $search_res->where(" DATE(oi.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $search_res->where(" DATE(oi.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $search_res->group_Start();
            $search_res->or_like($filters);
            $search_res->group_End();
        }

        if (isset($delivery_boy_id)) {
            $search_res->where("oi.delivery_boy_id", $delivery_boy_id);
        }

        if (isset($seller_id) && $seller_id != "") {
            $search_res->where("oi.seller_id", $seller_id);
            $search_res->where("oi.active_status != 'awaiting'");
        }

        if (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) {
            $count_res->where("oi.seller_id", $_GET['seller_id']);
        }

        if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $search_res->where("o.user_id", $_GET['user_id']);
        }

        if (isset($_GET['order_status']) && !empty($_GET['order_status'])) {
            $search_res->where('oi.active_status', $_GET['order_status']);
        }
        // Filter By payment
        if (isset($_GET['payment_method']) && !empty($_GET['payment_method'])) {
            $count_res->where('payment_method', $_GET['payment_method']);
        }

        // Filter By order type
        if (isset($_GET['order_type']) && !empty($_GET['order_type']) && $_GET['order_type'] == 'physical_order') {
            $search_res->where('p.type!=', 'digital_product');
        }
        if (isset($_GET['order_type']) && !empty($_GET['order_type']) && $_GET['order_type'] == 'digital_order') {
            $search_res->where('p.type', 'digital_product');
        }


        $user_details = $search_res->order_by($sort, "DESC")->limit($limit, $offset)->get('order_items oi')->result_array();



        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $tota_amount = 0;
        $final_tota_amount = 0;
        $currency_symbol = get_settings('currency');
        $count = 1;
        foreach ($user_details as $row) {

            $temp = '';
            if (!empty($row['items'][0]['order_status'])) {
                $status = json_decode($row['items'][0]['order_status'], 1);
                foreach ($status as $st) {
                    $temp .= @$st[0] . " : " . @$st[1] . "<br>------<br>";
                }
            }

            if (trim($row['active_status']) == 'awaiting') {
                $active_status = '<label class="badge badge-secondary">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'received') {
                $active_status = '<label class="badge badge-primary">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'processed') {
                $active_status = '<label class="badge badge-info">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'shipped') {
                $active_status = '<label class="badge badge-warning">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'delivered') {
                $active_status = '<label class="badge badge-success">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'returned' || $row['active_status'] == 'cancelled') {
                $active_status = '<label class="badge badge-danger">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'return_request_decline') {
                $active_status = '<label class="badge badge-danger">' . str_replace('_', ' ', $row['active_status']) . '</label>';
            }
            if ($row['active_status'] == 'return_request_approved') {
                $active_status = '<label class="badge badge-success">' . str_replace('_', ' ', $row['active_status']) . '</label>';
            }
            if ($row['active_status'] == 'return_request_pending') {
                $active_status = '<label class="badge badge-secondary">' . str_replace('_', ' ', $row['active_status']) . '</label>';
            }
            if ($row['type'] == 'digital_product' && $row['download_allowed'] == 0) {
                if ($row['is_sent'] == 1) {
                    $mail_status = '<label class="badge badge-success">SENT </label>';
                } else if ($row['is_sent'] == 0) {
                    $mail_status = '<label class="badge badge-danger">NOT SENT</label>';
                } else {
                    $mail_status = '';
                }
            } else {
                $mail_status = '';
            }

            if ($row['transaction_status'] == 0 || $row['transaction_status'] == 'awaiting') {

                $transaction_status = '<label class="badge badge-primary">Awaiting</label>';
            }
            if ($row['transaction_status'] == 1 || $row['transaction_status'] == 'success') {
                $transaction_status = '<label class="badge badge-success">Success</label>';
            } else {
                $transaction_status = '<label class="badge badge-warning">' . $row['transaction_status'] . '</label>';
            }

            $status = $temp;
            $tempRow['id'] = $count;
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['order_item_id'] = $row['order_item_id'];
            $tempRow['user_id'] = $row['user_id'];
            $tempRow['seller_id'] = $row['seller_id'];
            $tempRow['notes'] = (isset($row['notes']) && !empty($row['notes'])) ? $row['notes'] : "";
            $tempRow['username'] = $row['username'];
            $tempRow['seller_name'] = $row['seller_name'];
            $tempRow['is_credited'] = ($row['is_credited']) ? '<label class="badge badge-success">Credited</label>' : '<label class="badge badge-danger">Not Credited</label>';
            $tempRow['product_name'] = $row['product_name'];
            $tempRow['product_name'] .= (!empty($row['variant_name'])) ? '(' . $row['variant_name'] . ')' : "";
            if (isset($row['mobile']) && !empty($row['mobile']) && $row['mobile'] != "" && $row['mobile'] != " ") {
                $tempRow['mobile'] = (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($row['mobile']) - 3) . substr($row['mobile'], -3) : $row['mobile'];
            } else {
                $tempRow['mobile'] = "";
            }
            $tempRow['sub_total'] = $currency_symbol . ' ' . $row['sub_total'];
            $tempRow['quantity'] = $row['quantity'];
            $final_tota_amount += intval($row['sub_total']);
            $tempRow['delivery_boy'] = $row['delivery_boy'];
            $tempRow['payment_method'] = $row['payment_method'];
            $tempRow['delivery_boy_id'] = $row['delivery_boy_id'];
            $tempRow['product_variant_id'] = $row['product_variant_id'];
            $tempRow['delivery_date'] = $row['delivery_date'];
            $tempRow['delivery_time'] = $row['delivery_time'];
            $tempRow['courier_agency'] = (isset($row['courier_agency']) && !empty($row['courier_agency'])) ? $row['courier_agency'] : "";
            $tempRow['tracking_id'] = (isset($row['tracking_id']) && !empty($row['tracking_id'])) ? $row['tracking_id'] : "";
            $tempRow['url'] = (isset($row['url']) && !empty($row['url'])) ? $row['url'] : "";
            $updated_username = fetch_details('users', 'id =' . $row['updated_by'], 'username');
            $tempRow['updated_by'] = $updated_username[0]['username'];
            $tempRow['status'] = $status;
            $tempRow['transaction_status'] = $transaction_status;
            $tempRow['active_status'] = $active_status;
            $tempRow['mail_status'] = $mail_status;
            $tempRow['date_added'] = date('d-m-Y', strtotime($row['date_added']));
            $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['order_id'] . '" class="btn action-btn btn-primary btn-xs mr-1 ml-1 mb-1" title="View" ><i class="fa fa-eye"></i></a>';

            if ($this->ion_auth->is_delivery_boy()) {
                $operate = '<a href=' . base_url('delivery_boy/orders/edit_orders') . '?edit_id=' . $row['order_id'] . ' class="btn action-btn btn-primary btn-xs mr-1 mb-1 ml-1" title="View"><i class="fa fa-eye"></i></a>';
            } else if ($this->ion_auth->is_seller()) {
                $operate = '<a href=' . base_url('seller/orders/edit_orders') . '?edit_id=' . $row['order_id'] . ' class="btn action-btn btn-primary btn-xs mr-1 ml-1 mb-1" title="View"><i class="fa fa-eye"></i></a>';
                $operate .= '<a href="' . base_url() . 'seller/invoice?edit_id=' . $row['order_id'] . '" class="btn btn-info action-btn btn-xs ml-1 mb-1" title="Invoice" ><i class="fa fa-file"></i></a>';
                if ($row['type'] != 'digital_product') {

                    $operate .= ' <a href="javascript:void(0)" class="edit_order_tracking btn btn-success btn-xs action-btn ml-1  mb-1" title="Order Tracking" data-order_id="' . $row['order_id'] . '" data-order_item_id="' . $row['order_item_id'] . '" data-seller_id="' . $row['seller_id'] . '" data-courier_agency="' . $row['courier_agency'] . '"  data-tracking_id="' . $row['tracking_id'] . '" data-url="' . $row['url'] . '" data-target="#transaction_modal" data-toggle="modal"><i class="fa fa-map-marker-alt"></i></a>';
                }
                if ($row['download_allowed'] == 0 && $row['type'] == 'digital_product') {
                    $operate .= '<a href="javascript:void(0)" class="sendMailBtn btn action-btn btn-primary btn-xs mr-1 mb-1 ml-1" data-target="#ManageOrderSendMailModal" data-toggle="modal" title="Edit" data-email="' . $row['email'] . '" data-id="' . $row['order_item_id'] . '" data-url="seller/orders/"><i class="fas fa-paper-plane"></i></a>';
                    $operate .= '<a href="https://mail.google.com/mail/?view=cm&fs=1&tf=1&to=' . $row['email'] . '" class="btn action-btn btn-danger btn-xs ml-1 mr-1 mb-1" target="_blank"><i class="fab fa-google"></i></a>';
                    $operate .= ' <a href="javascript:void(0)" class="edit_digital_order_mails action-btn btn btn-warning btn-xs ml-1 mb-1" title="Digital Order Mails" data-order_item_id="' . $row['order_item_id'] . '"  data-target="#digital-order-mails" data-toggle="modal"><i class="far fa-envelope-open"></i></a>';
                }
            } else if ($this->ion_auth->is_admin()) {
                $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['order_id'] . ' class="btn action-btn btn-primary btn-xs mr-1 mb-1" title="View" ><i class="fa fa-eye"></i></a>';
                $operate .= '<a href="javascript:void(0)" class="delete-order-items btn action-btn btn-danger btn-xs mr-1 mb-1" data-id=' . $row['order_item_id'] . ' title="Delete" ><i class="fa fa-trash"></i></a>';
                $operate .= '<a href="' . base_url() . 'admin/invoice?edit_id=' . $row['order_id'] . '" class="btn action-btn btn-info btn-xs mr-1 mb-1" title="Invoice" ><i class="fa fa-file"></i></a>';

                if ($row['download_allowed'] == 0 && $row['type'] == 'digital_product') {
                    $operate .= '<a href="javascript:void(0)" class="edit_btn btn action-btn btn-primary btn-xs mr-1 mb-1" title="Edit" data-id="' . $row['order_item_id'] . '" data-url="admin/orders/"><i class="fas fa-paper-plane"></i></a>';
                    $operate .= '<a href="javascript:void(0)" class="btn sendMailBtn action-btn btn-primary btn-xs mr-1 mb-1" data-target="#ManageOrderSendMailModal" data-toggle="modal" title="Edit" data-email="' . $row['email'] . '" data-id="' . $row['order_item_id'] . '" data-url="admin/orders/"><i class="fas fa-paper-plane"></i></a>';
                    $operate .= '<a href="https://mail.google.com/mail/?view=cm&fs=1&tf=1&to=' . $row['email'] . '" class="btn action-btn btn-danger btn-xs mb-1" target="_blank"><i class="fab fa-google"></i></a>';
                    $operate .= ' <a href="javascript:void(fa fa-map-marker-alt0)" class="edit_digital_order_mails btn btn-warning action-btn btn-xs mr-1 mb-1" title="Digital Order Mails" data-order_item_id="' . $row['order_item_id'] . '"  data-target="#digital-order-mails" data-toggle="modal"><i class="far fa-envelope-open"></i></a>';
                }
            } else {
                $operate = "";
            }
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    public function get_return_order_items_list($delivery_boy_id = NULL, $offset = 0, $limit = 10, $sort = " oi.id ", $order = 'ASC', $seller_id = NULL, $from_app = '0', $order_item_id = '', $is_print = '0')
    {

        $customer_privacy = false;
        if (isset($seller_id) && $seller_id != "") {
            $customer_privacy = get_seller_permission($seller_id, 'customer_privacy');
        }

        if (isset($_POST['offset'])) {
            $offset = $_POST['offset'];
        }
        if (isset($_POST['limit'])) {
            $limit = $_POST['limit'];
        }

        if (isset($_POST['search']) and $_POST['search'] != '') {
            $search = $_POST['search'];

            $filters = [
                'un.username' => $search,
                'u.username' => $search,
                'us.username' => $search,
                'un.email' => $search,
                'oi.id' => $search,
                'o.mobile' => $search,
                'o.address' => $search,
                'o.payment_method' => $search,
                'oi.sub_total' => $search,
                'o.delivery_time' => $search,
                'oi.active_status' => $search,
                'oi.product_name' => $search,
                'oi.date_added' => $search
            ];
        }

        $count_res = $this->db->select(' COUNT(DISTINCT(o.id)) as `total` ')
            ->join(' `users` u', 'u.id= oi.delivery_boy_id', 'left')
            ->join('users us ', ' us.id = oi.seller_id', 'left')
            ->join(' `orders` o', 'o.id= oi.order_id')
            ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
            ->join('products p ', ' p.id = v.product_id ', 'left')
            ->join('users un ', ' un.id = o.user_id', 'left');

        if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {

            $count_res->where(" DATE(oi.date_added) >= DATE('" . $_POST['start_date'] . "') ");
            $count_res->where(" DATE(oi.date_added) <= DATE('" . $_POST['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $this->db->group_Start();
            $count_res->or_like($filters);
            $this->db->group_End();
        }

        if (isset($delivery_boy_id)) {
            $count_res->where("oi.delivery_boy_id", $delivery_boy_id);
            $count_res->where_in("oi.active_status", ['return_pickedup', 'return_request_approved', 'returned']);
        }

        if (isset($seller_id) && $seller_id != "") {
            $count_res->where("oi.seller_id", $seller_id);
            $count_res->where("oi.active_status != 'awaiting'");
        }

        if (isset($_POST['user_id']) && $_POST['user_id'] != null) {
            $count_res->where("o.user_id", $_POST['user_id']);
        }
        if (isset($_POST['order_item_id']) && $_POST['order_item_id'] != null) {
            $count_res->where("oi.id", $order_item_id);
        }

        if (isset($_POST['seller_id']) && !empty($_POST['seller_id'])) {
            $count_res->where("oi.seller_id", $_POST['seller_id']);
        }

        if (isset($_POST['order_status']) && !empty($_POST['order_status'])) {
            $count_res->where('oi.active_status', $_POST['order_status']);
        }
        // Filter By payment
        if (isset($_POST['payment_method']) && !empty($_POST['payment_method'])) {
            $count_res->where('payment_method', $_POST['payment_method']);
        }
        // Filter By order type
        if (isset($_POST['order_type']) && !empty($_POST['order_type']) && $_POST['order_type'] == 'physical_order') {
            $count_res->where('p.type!=', 'digital_product');
        }
        if (isset($_POST['order_type']) && !empty($_POST['order_type']) && $_POST['order_type'] == 'digital_order') {
            $count_res->where('p.type', 'digital_product');
        }

        $product_count = $count_res->get('order_items oi')->result_array();

        foreach ($product_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select('o.id as order_id, oi.id as order_item_id, o.*, oi.*, ot.courier_agency, ot.tracking_id, ot.url, t.status as transaction_status, u.username as delivery_boy, un.username as username, us.username as seller_name, p.type, p.image, p.download_allowed, a.*') // Select relevant fields from addresses table (a.*)
            ->join('users u', 'u.id= oi.delivery_boy_id', 'left')
            ->join('users us ', 'us.id = oi.seller_id', 'left')
            ->join('order_tracking ot ', 'ot.order_item_id = oi.id', 'left')
            ->join('orders o', 'o.id= oi.order_id', 'left')
            ->join('product_variants v ', 'oi.product_variant_id = v.id', 'left')
            ->join('products p ', 'p.id = v.product_id', 'left')
            ->join('transactions t ', 't.order_item_id = oi.id', 'left')
            ->join('users un ', 'un.id = o.user_id', 'left')
            ->join('addresses a', 'a.id = o.address_id', 'left')  // Add this line for addresses table
            ->group_by('oi.order_id');


        if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
            $search_res->where(" DATE(oi.date_added) >= DATE('" . $_POST['start_date'] . "') ");
            $search_res->where(" DATE(oi.date_added) <= DATE('" . $_POST['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $search_res->group_Start();
            $search_res->or_like($filters);
            $search_res->group_End();
        }

        if (isset($delivery_boy_id)) {
            $search_res->where("oi.delivery_boy_id", $delivery_boy_id);
            $search_res->where_in("oi.active_status", ['return_pickedup', 'return_request_approved', 'returned']);
        }

        if (isset($seller_id) && $seller_id != "") {
            $search_res->where("oi.seller_id", $seller_id);
            $search_res->where("oi.active_status != 'awaiting'");
        }

        if (isset($_POST['seller_id']) && !empty($_POST['seller_id'])) {
            $search_res->where("oi.seller_id", $_POST['seller_id']);
        }

        if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
            $search_res->where("o.user_id", $_POST['user_id']);
        }
        if (isset($_POST['order_item_id']) && $_POST['order_item_id'] != null) {
            $search_res->where("oi.id", $order_item_id);
        }

        if (isset($_POST['order_status']) && !empty($_POST['order_status'])) {
            $search_res->where('oi.active_status', $_POST['order_status']);
        }
        // Filter By payment
        if (isset($_POST['payment_method']) && !empty($_POST['payment_method'])) {
            $search_res->where('payment_method', $_POST['payment_method']);
        }

        // Filter By order type
        if (isset($_POST['order_type']) && !empty($_POST['order_type']) && $_POST['order_type'] == 'physical_order') {
            $search_res->where('p.type!=', 'digital_product');
        }
        if (isset($_POST['order_type']) && !empty($_POST['order_type']) && $_POST['order_type'] == 'digital_order') {
            $search_res->where('p.type', 'digital_product');
        }

        $user_details = $search_res->order_by($sort, "DESC")->limit($limit, $offset)->get('order_items oi')->result_array();

        $bulkData = array();

        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $tota_amount = 0;
        $final_tota_amount = 0;
        $currency_symbol = get_settings('currency');
        $count = 1;
        foreach ($user_details as $row) {

            $admin_groups = fetch_details('users_groups', ['group_id' => 1]);
            $admin_data = fetch_details('users', ['id' => $admin_groups[0]['user_id']], 'id,latitude,longitude,address,mobile');

            $temp = '';
            if (!empty($row['items'][0]['order_status'])) {
                $status = json_decode($row['items'][0]['order_status'], 1);
                foreach ($status as $st) {
                    $temp .= @$st[0] . " : " . @$st[1] . "<br>------<br>";
                }
            }
            if ($from_app == '1') {

                $active_status = $row['active_status'];
                $mail_status = $row['is_sent'];
                $transaction_status = !empty($row['transaction_status']) ? $row['transaction_status'] : '';
            } else {

                if (trim($row['active_status']) == 'awaiting') {
                    $active_status = '<label class="badge badge-secondary">' . $row['active_status'] . '</label>';
                }
                if ($row['active_status'] == 'received') {
                    $active_status = '<label class="badge badge-primary">' . $row['active_status'] . '</label>';
                }
                if ($row['active_status'] == 'processed') {
                    $active_status = '<label class="badge badge-info">' . $row['active_status'] . '</label>';
                }
                if ($row['active_status'] == 'shipped') {
                    $active_status = '<label class="badge badge-warning">' . $row['active_status'] . '</label>';
                }
                if ($row['active_status'] == 'delivered') {
                    $active_status = '<label class="badge badge-success">' . $row['active_status'] . '</label>';
                }
                if ($row['active_status'] == 'returned' || $row['active_status'] == 'cancelled') {
                    $active_status = '<label class="badge badge-danger">' . $row['active_status'] . '</label>';
                }
                if ($row['active_status'] == 'return_request_decline') {
                    $active_status = '<label class="badge badge-danger">' . str_replace('_', ' ', $row['active_status']) . '</label>';
                }
                if ($row['active_status'] == 'return_request_approved') {
                    $active_status = '<label class="badge badge-success">' . str_replace('_', ' ', $row['active_status']) . '</label>';
                }
                if ($row['active_status'] == 'return_request_pending') {
                    $active_status = '<label class="badge badge-secondary">' . str_replace('_', ' ', $row['active_status']) . '</label>';
                }
                if ($row['active_status'] == 'return_pickedup') {
                    $active_status = '<label class="badge badge-secondary">' . str_replace('_', ' ', $row['active_status']) . '</label>';
                }


                if ($row['type'] == 'digital_product' && $row['download_allowed'] == 0) {
                    if ($row['is_sent'] == 1) {
                        $mail_status = '<label class="badge badge-success">SENT </label>';
                    } else if ($row['is_sent'] == 0) {
                        $mail_status = '<label class="badge badge-danger">NOT SENT</label>';
                    } else {
                        $mail_status = '';
                    }
                } else {
                    $mail_status = '';
                }
                if ($row['transaction_status'] == 0 || $row['transaction_status'] == 'awaiting') {

                    $transaction_status = '<label class="badge badge-primary">Awaiting</label>';
                }
                if ($row['transaction_status'] == 1 || $row['transaction_status'] == 'success') {
                    $transaction_status = '<label class="badge badge-success">Success</label>';
                } else {
                    $transaction_status = '<label class="badge badge-warning">' . $row['transaction_status'] . '</label>';
                }
            }

            $status = $temp;
            $tempRow['id'] = (string) $count;
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['order_item_id'] = $row['order_item_id'];
            $tempRow['user_id'] = $row['user_id'];
            $tempRow['seller_id'] = $row['seller_id'];
            $tempRow['address_id'] = $row['address_id'];
            $tempRow['user_address'] = $row['address'] . ', ' . $row['name'] . ', ' . $row['city'] . ', ' . $row['state'] . ', ' . $row['country'] . ', ' . $row['pincode'];
            $tempRow['user_latitude'] = !empty($row['latitude']) ? $row['latitude'] : '';
            $tempRow['user_longitude'] = !empty($row['longitude']) ? $row['longitude'] : '';
            $tempRow['admin_mobile'] = !empty($admin_data[0]['mobile']) ? $admin_data[0]['mobile'] : '';
            $tempRow['admin_address'] = !empty($admin_data[0]['address']) ? $admin_data[0]['address'] : '';
            $tempRow['admin_latitude'] = !empty($admin_data[0]['latitude']) ? $admin_data[0]['latitude'] : '';
            $tempRow['admin_longitude'] = !empty($admin_data[0]['longitude']) ? $admin_data[0]['longitude'] : '';
            $tempRow['notes'] = (isset($row['notes']) && !empty($row['notes'])) ? $row['notes'] : "";
            $tempRow['username'] = !empty($row['username']) ? $row['username'] : '';
            $tempRow['seller_name'] = $row['seller_name'];
            if ($from_app == '1') {
                $tempRow['is_credited'] = $row['is_credited'];
            } else {
                $tempRow['is_credited'] = ($row['is_credited']) ? '<label class="badge badge-success">Credited</label>' : '<label class="badge badge-danger">Not Credited</label>';
            }
            $tempRow['product_name'] = $row['product_name'];
            $tempRow['product_image'] = base_url() . $row['image'];
            $tempRow['product_name'] .= (!empty($row['variant_name'])) ? '(' . $row['variant_name'] . ')' : "";
            if (isset($row['mobile']) && !empty($row['mobile']) && $row['mobile'] != "" && $row['mobile'] != " ") {
                $tempRow['mobile'] = (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($row['mobile']) - 3) . substr($row['mobile'], -3) : $row['mobile'];
            } else {
                $tempRow['mobile'] = "";
            }
            $tempRow['sub_total'] = $currency_symbol . ' ' . $row['sub_total'];
            $tempRow['quantity'] = $row['quantity'];
            $final_tota_amount += intval($row['sub_total']);
            $tempRow['delivery_boy'] = $row['delivery_boy'];
            $tempRow['payment_method'] = $row['payment_method'];
            $tempRow['delivery_boy_id'] = $row['delivery_boy_id'];
            $tempRow['product_variant_id'] = $row['product_variant_id'];
            $tempRow['delivery_date'] = !empty($row['delivery_date']) ? $row['delivery_date'] : '';
            $tempRow['delivery_time'] = !empty($row['delivery_time']) ? $row['delivery_time'] : '';
            $tempRow['courier_agency'] = (isset($row['courier_agency']) && !empty($row['courier_agency'])) ? $row['courier_agency'] : "";
            $tempRow['tracking_id'] = (isset($row['tracking_id']) && !empty($row['tracking_id'])) ? $row['tracking_id'] : "";
            $tempRow['url'] = (isset($row['url']) && !empty($row['url'])) ? $row['url'] : "";
            $updated_username = fetch_details('users', 'id =' . $row['updated_by'], 'username');
            $tempRow['updated_by'] = $updated_username[0]['username'];
            $tempRow['status'] = $status;
            $tempRow['transaction_status'] = $transaction_status;
            $tempRow['active_status'] = $active_status;
            $tempRow['mail_status'] = $mail_status;
            $tempRow['date_added'] = date('d-m-Y', strtotime($row['date_added']));
            $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['order_id'] . '" class="btn action-btn btn-primary btn-xs mr-1 ml-1 mb-1" title="View" ><i class="fa fa-eye"></i></a>';

            if ($this->ion_auth->is_delivery_boy()) {
                $operate = '<a href=' . base_url('delivery_boy/orders/edit_return_orders') . '?edit_id=' . $row['order_item_id'] . ' class="btn action-btn btn-primary btn-xs mr-1 mb-1 ml-1" title="View"><i class="fa fa-eye"></i></a>';
            } else if ($this->ion_auth->is_seller()) {
                $operate = '<a href=' . base_url('seller/orders/edit_orders') . '?edit_id=' . $row['order_id'] . ' class="btn action-btn btn-primary btn-xs mr-1 ml-1 mb-1" title="View"><i class="fa fa-eye"></i></a>';
                $operate .= '<a href="' . base_url() . 'seller/invoice?edit_id=' . $row['order_id'] . '" class="btn btn-info action-btn btn-xs ml-1 mb-1" title="Invoice" ><i class="fa fa-file"></i></a>';
                if ($row['type'] != 'digital_product') {

                    $operate .= ' <a href="javascript:void(0)" class="edit_order_tracking btn btn-success btn-xs action-btn ml-1  mb-1" title="Order Tracking" data-order_id="' . $row['order_id'] . '" data-order_item_id="' . $row['order_item_id'] . '" data-seller_id="' . $row['seller_id'] . '" data-courier_agency="' . $row['courier_agency'] . '"  data-tracking_id="' . $row['tracking_id'] . '" data-url="' . $row['url'] . '" data-target="#transaction_modal" data-toggle="modal"><i class="fa fa-map-marker-alt"></i></a>';
                }
                if ($row['download_allowed'] == 0 && $row['type'] == 'digital_product') {
                    $operate .= '<a href="javascript:void(0)" class="sendMailBtn btn action-btn btn-primary btn-xs mr-1 mb-1 ml-1" data-target="#ManageOrderSendMailModal" data-toggle="modal" title="Edit" data-email="' . $row['email'] . '" data-id="' . $row['order_item_id'] . '" data-url="seller/orders/"><i class="fas fa-paper-plane"></i></a>';
                    $operate .= '<a href="https://mail.google.com/mail/?view=cm&fs=1&tf=1&to=' . $row['email'] . '" class="btn action-btn btn-danger btn-xs ml-1 mr-1 mb-1" target="_blank"><i class="fab fa-google"></i></a>';
                    $operate .= ' <a href="javascript:void(0)" class="edit_digital_order_mails action-btn btn btn-warning btn-xs ml-1 mb-1" title="Digital Order Mails" data-order_item_id="' . $row['order_item_id'] . '"  data-target="#digital-order-mails" data-toggle="modal"><i class="far fa-envelope-open"></i></a>';
                }
            } else if ($this->ion_auth->is_admin()) {
                $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['order_id'] . ' class="btn action-btn btn-primary btn-xs mr-1 mb-1" title="View" ><i class="fa fa-eye"></i></a>';
                $operate .= '<a href="javascript:void(0)" class="delete-order-items btn action-btn btn-danger btn-xs mr-1 mb-1" data-id=' . $row['order_item_id'] . ' title="Delete" ><i class="fa fa-trash"></i></a>';
                $operate .= '<a href="' . base_url() . 'admin/invoice?edit_id=' . $row['order_id'] . '" class="btn action-btn btn-info btn-xs mr-1 mb-1" title="Invoice" ><i class="fa fa-file"></i></a>';

                if ($row['download_allowed'] == 0 && $row['type'] == 'digital_product') {
                    $operate .= '<a href="javascript:void(0)" class="btn sendMailBtn action-btn btn-primary btn-xs mr-1 mb-1" data-target="#ManageOrderSendMailModal" data-toggle="modal" title="Edit" data-email="' . $row['email'] . '" data-id="' . $row['order_item_id'] . '" data-url="admin/orders/"><i class="fas fa-paper-plane"></i></a>';
                    $operate .= '<a href="https://mail.google.com/mail/?view=cm&fs=1&tf=1&to=' . $row['email'] . '" class="btn action-btn btn-danger btn-xs mb-1" target="_blank"><i class="fab fa-google"></i></a>';
                    $operate .= ' <a href="javascript:void(0)" class="edit_digital_order_mails btn btn-warning action-btn btn-xs mr-1 mb-1" title="Digital Order Mails" data-order_item_id="' . $row['order_item_id'] . '"  data-target="#digital-order-mails" data-toggle="modal"><i class="far fa-envelope-open"></i></a>';
                }
            } else {
                $operate = "";
            }
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        if ($from_app == '1') {
            $bulkData['data'] = $rows;
            if ($is_print == '1') {
                return $bulkData['data'];
            } else {
                print_r(json_encode($bulkData));
            }
        } else {
            $bulkData['rows'] = $rows;
            print_r(json_encode($bulkData));
        }
    }

    public function getOrderItemById($order_item_id)
    {
        return $this->db->where('id', $order_item_id)->get('order_items')->row();
    }

    public function updateOrderItemStatus($order_item_id, $update_data)
    {
        $return_status = ['status' => '8'];
        $this->db->where('id', $order_item_id)->update('order_items', $update_data);
        $this->db->where('order_item_id', $order_item_id)->update('return_requests', $return_status);
        $fcm_admin_subject = 'New retun orequest get ID #' . $order_item_id;
        $fcm_admin_msg = 'New return request received for order item id #' . $order_item_id . '  please process it.';
        $admin_notifi = array(
            'title' => $fcm_admin_subject,
            'message' => $fcm_admin_msg,
            'type' => "return_order_item",
            'type_id' => $order_item_id
        );
        insert_details($admin_notifi, 'system_notification');

        return $update_data;
    }

    public function get_seller_order_items_list($delivery_boy_id = NULL, $offset = 0, $limit = 10, $sort = " oi.id ", $order = 'ASC', $seller_id = NULL)
    {
        $customer_privacy = false;
        if (isset($seller_id) && $seller_id != "") {
            $customer_privacy = get_seller_permission($seller_id, 'customer_privacy');
        }

        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];

            $filters = [
                'un.username' => $search,
                'u.username' => $search,
                'us.username' => $search,
                'un.email' => $search,
                'oi.id' => $search,
                'o.mobile' => $search,
                'o.address' => $search,
                'o.payment_method' => $search,
                'oi.sub_total' => $search,
                'o.delivery_time' => $search,
                'oi.active_status' => $search,
                'oi.date_added' => $search
            ];
        }

        $count_res = $this->db->select(' COUNT(o.id) as `total` ')
            ->join(' `users` u', 'u.id= oi.delivery_boy_id', 'left')
            ->join('users us ', ' us.id = oi.seller_id', 'left')
            ->join(' `orders` o', 'o.id= oi.order_id')
            ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
            ->join('products p ', ' p.id = v.product_id ', 'left')
            ->join('users un ', ' un.id = o.user_id', 'left');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {

            $count_res->where(" DATE(oi.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $count_res->where(" DATE(oi.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $this->db->group_Start();
            $count_res->or_like($filters);
            $this->db->group_End();
        }

        if (isset($delivery_boy_id)) {
            $count_res->where("oi.delivery_boy_id", $delivery_boy_id);
        }

        if (isset($seller_id) && $seller_id != "") {
            $count_res->where("oi.seller_id", $seller_id);
        }

        if (isset($_GET['user_id']) && $_GET['user_id'] != null) {
            $count_res->where("o.user_id", $_GET['user_id']);
        }

        if (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) {
            $count_res->where("oi.seller_id", $_GET['seller_id']);
        }

        if (isset($_GET['order_status']) && !empty($_GET['order_status'])) {
            $count_res->where('oi.active_status', $_GET['order_status']);
        }
        // Filter By payment
        if (isset($_GET['payment_method']) && !empty($_GET['payment_method'])) {
            $count_res->where('payment_method', $_GET['payment_method']);
        }
        // Filter By order type
        if (isset($_GET['order_type']) && !empty($_GET['order_type']) && $_GET['order_type'] == 'physical_order') {
            $count_res->where('p.type!=', 'digital_product');
        }
        if (isset($_GET['order_type']) && !empty($_GET['order_type']) && $_GET['order_type'] == 'digital_order') {
            $count_res->where('p.type', 'digital_product');
        }

        $product_count = $count_res->get('order_items oi')->result_array();

        foreach ($product_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(
            ' o.id as order_id,oi.id as order_item_id,
            oi.seller_id,oi.quantity, oi.is_credited, oi.variant_name, oi.product_name,oi.sub_total, oi.updated_by,o.is_pos_order,
             oi.active_status, o.payment_method, o.notes, o.mobile, o.delivery_date, o.delivery_time, o.user_id,o.date_added,
            ot.courier_agency,ot.tracking_id,ot.url,
            t.status as transaction_status, u.username as delivery_boy, un.username as username,us.username as seller_name,p.type,p.download_allowed'
        )
            ->join('users u', 'u.id= oi.delivery_boy_id', 'left')
            ->join('users us ', ' us.id = oi.seller_id', 'left')
            ->join('order_tracking ot ', ' ot.order_item_id = oi.id', 'left')
            ->join('orders o', 'o.id= oi.order_id', 'left')
            ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
            ->join('products p ', ' p.id = v.product_id ', 'left')
            ->join('transactions t ', ' t.order_item_id = oi.id ', 'left')
            ->join('users un ', ' un.id = o.user_id', 'left')->group_by('oi.id');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $search_res->where(" DATE(oi.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $search_res->where(" DATE(oi.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $search_res->group_Start();
            $search_res->or_like($filters);
            $search_res->group_End();
        }

        if (isset($delivery_boy_id)) {
            $search_res->where("oi.delivery_boy_id", $delivery_boy_id);
        }

        if (isset($seller_id) && $seller_id != "") {
            $search_res->where("oi.seller_id", $seller_id);
        }

        if (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) {
            $count_res->where("oi.seller_id", $_GET['seller_id']);
        }

        if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $search_res->where("o.user_id", $_GET['user_id']);
        }

        if (isset($_GET['order_status']) && !empty($_GET['order_status'])) {
            $search_res->where('oi.active_status', $_GET['order_status']);
        }
        // Filter By payment
        if (isset($_GET['payment_method']) && !empty($_GET['payment_method'])) {
            $count_res->where('payment_method', $_GET['payment_method']);
        }

        // Filter By order type
        if (isset($_GET['order_type']) && !empty($_GET['order_type']) && $_GET['order_type'] == 'physical_order') {
            $search_res->where('p.type!=', 'digital_product');
        }
        if (isset($_GET['order_type']) && !empty($_GET['order_type']) && $_GET['order_type'] == 'digital_order') {
            $search_res->where('p.type', 'digital_product');
        }

        $user_details = $search_res->order_by($sort, "DESC")->limit($limit, $offset)->get('order_items oi')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $tota_amount = 0;
        $final_tota_amount = 0;
        $currency_symbol = get_settings('currency');
        $count = 1;
        foreach ($user_details as $row) {

            $temp = '';
            if (!empty($row['items'][0]['order_status'])) {
                $status = json_decode($row['items'][0]['order_status'], 1);
                foreach ($status as $st) {
                    $temp .= @$st[0] . " : " . @$st[1] . "<br>------<br>";
                }
            }

            if (trim($row['active_status']) == 'awaiting') {
                $active_status = '<label class="badge badge-secondary">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'received') {
                $active_status = '<label class="badge badge-primary">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'processed') {
                $active_status = '<label class="badge badge-info">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'shipped') {
                $active_status = '<label class="badge badge-warning">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'delivered') {
                $active_status = '<label class="badge badge-success">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'returned' || $row['active_status'] == 'cancelled') {
                $active_status = '<label class="badge badge-danger">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'return_request_decline') {
                $active_status = '<label class="badge badge-danger">' . str_replace('_', ' ', $row['active_status']) . '</label>';
            }
            if ($row['active_status'] == 'return_request_approved') {
                $active_status = '<label class="badge badge-success">' . str_replace('_', ' ', $row['active_status']) . '</label>';
            }
            if ($row['active_status'] == 'return_request_pending') {
                $active_status = '<label class="badge badge-secondary">' . str_replace('_', ' ', $row['active_status']) . '</label>';
            }

            if ($row['is_shiprocket_order'] == 1) {
                $active_status = '<label class="badge badge-secondary">' . str_replace('_', ' ', $row['active_status']) . '</label>';
            }

            if ($row['type'] == 'digital_product' && $row['download_allowed'] == 0) {
                if ($row['is_sent'] == 1) {
                    $mail_status = '<label class="badge badge-success">SENT </label>';
                } else if ($row['is_sent'] == 0) {
                    $mail_status = '<label class="badge badge-danger">NOT SENT</label>';
                } else {
                    $mail_status = '';
                }
            } else {
                $mail_status = '';
            }

            if ($row['transaction_status'] == 0 || $row['transaction_status'] == 'awaiting') {

                $transaction_status = '<label class="badge badge-primary">Awaiting</label>';
            }
            if ($row['transaction_status'] == 1 || $row['transaction_status'] == 'success') {
                $transaction_status = '<label class="badge badge-success">Success</label>';
            } else {
                $transaction_status = '<label class="badge badge-warning">' . $row['transaction_status'] . '</label>';
            }

            $status = $temp;
            $tempRow['id'] = $count;
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['order_item_id'] = $row['order_item_id'];
            $tempRow['user_id'] = $row['user_id'];
            $tempRow['seller_id'] = $row['seller_id'];
            $tempRow['notes'] = (isset($row['notes']) && !empty($row['notes'])) ? $row['notes'] : "";
            $tempRow['username'] = $row['username'];
            $tempRow['seller_name'] = $row['seller_name'];
            $tempRow['is_credited'] = ($row['is_credited']) ? '<label class="badge badge-success">Credited</label>' : '<label class="badge badge-danger">Not Credited</label>';
            $tempRow['product_name'] = $row['product_name'];
            $tempRow['product_name'] .= (!empty($row['variant_name'])) ? '(' . $row['variant_name'] . ')' : "";
            if (isset($row['mobile']) && !empty($row['mobile']) && $row['mobile'] != "" && $row['mobile'] != " ") {
                $tempRow['mobile'] = (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($row['mobile']) - 3) . substr($row['mobile'], -3) : $row['mobile'];
            } else {
                $tempRow['mobile'] = "";
            }
            $tempRow['sub_total'] = $currency_symbol . ' ' . $row['sub_total'];
            $tempRow['quantity'] = $row['quantity'];
            $final_tota_amount += $row['sub_total'];
            $tempRow['delivery_boy'] = $row['delivery_boy'];
            $tempRow['payment_method'] = ($row['payment_method'] == "COD" && $row['is_pos_order'] == 1) ? 'cash Payment' : str_replace('_', ' ', $row['payment_method']);
            $tempRow['delivery_boy_id'] = $row['delivery_boy_id'];
            $tempRow['product_variant_id'] = $row['product_variant_id'];
            $tempRow['delivery_date'] = $row['delivery_date'];
            $tempRow['delivery_time'] = $row['delivery_time'];
            $tempRow['courier_agency'] = (isset($row['courier_agency']) && !empty($row['courier_agency'])) ? $row['courier_agency'] : "";
            $tempRow['tracking_id'] = (isset($row['tracking_id']) && !empty($row['tracking_id'])) ? $row['tracking_id'] : "";
            $tempRow['url'] = (isset($row['url']) && !empty($row['url'])) ? $row['url'] : "";
            $updated_username = fetch_details('users', 'id =' . $row['updated_by'], 'username');
            $tempRow['updated_by'] = $updated_username[0]['username'];
            $tempRow['status'] = $status;
            $tempRow['transaction_status'] = $transaction_status;
            $tempRow['active_status'] = $active_status;
            $tempRow['mail_status'] = $mail_status;
            $tempRow['date_added'] = date('d-m-Y', strtotime($row['date_added']));
            $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['order_id'] . '" class="btn action-btn btn-primary btn-xs mr-1 ml-1 mb-1" title="View" ><i class="fa fa-eye"></i></a>';

            if ($this->ion_auth->is_delivery_boy()) {
                $operate = '<a href=' . base_url('delivery_boy/orders/edit_orders') . '?edit_id=' . $row['order_id'] . ' class="btn action-btn btn-success btn-xs mr-1 mb-1 ml-1" title="View"><i class="fa fa-edit"></i></a>';
            } else if ($this->ion_auth->is_seller()) {
                $operate = '<a href=' . base_url('seller/orders/edit_orders') . '?edit_id=' . $row['order_id'] . ' class="btn action-btn btn-success btn-xs mr-1 ml-1 mb-1" title="View"><i class="fa fa-edit"></i></a>';

                if ($row['download_allowed'] == 0 && $row['type'] == 'digital_product') {
                    $operate .= '<a href="javascript:void(0)" class="sendMailBtn btn action-btn btn-primary btn-xs mr-1 mb-1 ml-1" data-target="#ManageOrderSendMailModal" data-toggle="modal" title="Edit" data-email="' . $row['email'] . '" data-id="' . $row['order_item_id'] . '" data-url="seller/orders/"><i class="fas fa-paper-plane"></i></a>';
                    $operate .= '<a href="https://mail.google.com/mail/?view=cm&fs=1&tf=1&to=' . $row['email'] . '" class="btn action-btn btn-danger btn-xs ml-1 mr-1 mb-1" target="_blank"><i class="fab fa-google"></i></a>';
                    $operate .= ' <a href="javascript:void(0)" class="edit_digital_order_mails action-btn btn btn-warning btn-xs ml-1 mb-1" title="Digital Order Mails" data-order_item_id="' . $row['order_item_id'] . '"  data-target="#digital-order-mails" data-toggle="modal"><i class="far fa-envelope-open"></i></a>';
                }
            } else if ($this->ion_auth->is_admin()) {
                $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['order_id'] . ' class="btn action-btn btn-primary btn-xs mr-1 mb-1" title="View" ><i class="fa fa-eye"></i></a>';
                $operate .= '<a href="javascript:void(0)" class="delete-order-items btn action-btn btn-danger btn-xs mr-1 mb-1" data-id=' . $row['order_item_id'] . ' title="Delete" ><i class="fa fa-trash"></i></a>';
                $operate .= '<a href="' . base_url() . 'admin/invoice?edit_id=' . $row['order_id'] . '" class="btn action-btn btn-info btn-xs mr-1 mb-1" title="Invoice" ><i class="fa fa-file"></i></a>';
                if ($row['type'] != 'digital_product') {
                    $operate .= ' <a href="javascript:void(0)" class="edit_order_tracking btn btn-success action-btn btn-xs mr-1 mb-1" title="Order Tracking" data-order_id="' . $row['order_id'] . '" data-order_item_id="' . $row['order_item_id'] . '" data-seller_id="' . $row['seller_id'] . '" data-courier_agency="' . $row['courier_agency'] . '"  data-tracking_id="' . $row['tracking_id'] . '" data-url="' . $row['url'] . '" data-target="#transaction_modal" data-toggle="modal"><i class="fa fa-map-marker-alt"></i></a>';
                }
                if ($row['download_allowed'] == 0 && $row['type'] == 'digital_product') {
                    $operate .= '<a href="javascript:void(0)" class="btn sendMailBtn action-btn btn-primary btn-xs mr-1 mb-1" data-target="#ManageOrderSendMailModal" data-toggle="modal" title="Edit" data-email="' . $row['email'] . '" data-id="' . $row['order_item_id'] . '" data-url="admin/orders/"><i class="fas fa-paper-plane"></i></a>';
                    $operate .= '<a href="https://mail.google.com/mail/?view=cm&fs=1&tf=1&to=' . $row['email'] . '" class="btn action-btn btn-danger btn-xs mb-1" target="_blank"><i class="fab fa-google"></i></a>';
                    $operate .= ' <a href="javascript:void(0)" class="edit_digital_order_mails btn btn-warning action-btn btn-xs mr-1 mb-1" title="Digital Order Mails" data-order_item_id="' . $row['order_item_id'] . '"  data-target="#digital-order-mails" data-toggle="modal"><i class="far fa-envelope-open"></i></a>';
                }
            } else {
                $operate = "";
            }
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        if (!empty($user_details)) {
            $tempRow['id'] = '-';
            $tempRow['order_id'] = '-';
            $tempRow['order_item_id'] = '-';
            $tempRow['user_id'] = '-';
            $tempRow['seller_id'] = '-';
            $tempRow['username'] = '-';
            $tempRow['seller_name'] = '-';
            $tempRow['is_credited'] = '-';
            $tempRow['mobile'] = '-';
            $tempRow['delivery_charge'] = '-';
            $tempRow['product_name'] = '-';
            $tempRow['sub_total'] = '<span class="badge badge-danger">' . $currency_symbol . ' ' . $final_tota_amount . '</span>';
            $tempRow['discount'] = '-';
            $tempRow['quantity'] = '-';
            $tempRow['delivery_boy'] = '-';
            $tempRow['delivery_time'] = '-';
            $tempRow['status'] = '-';
            $tempRow['active_status'] = '-';
            $tempRow['transaction_status'] = '-';
            $tempRow['date_added'] = '-';
            $tempRow['operate'] = '-';
            $tempRow['mail_status'] = '-';
            array_push($rows, $tempRow);
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
    public function get_digital_product_orders_list(
        $delivery_boy_id = NULL,
        $offset = 0,
        $limit = 10,
        $sort = " o.id ",
        $order = 'ASC'
    ) {

        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];

            $filters = [
                'u.username' => $search,
                'db.username' => $search,
                'u.email' => $search,
                'o.id' => $search,
                'o.mobile' => $search,
                'o.address' => $search,
                'o.wallet_balance' => $search,
                'o.total' => $search,
                'o.final_total' => $search,
                'o.total_payable' => $search,
                'o.payment_method' => $search,
                'o.delivery_charge' => $search,
                'o.delivery_time' => $search,
                'oi.status' => $search,
                'oi.active_status' => $search,
                'o.date_added' => $search
            ];
        }

        $count_res = $this->db->select(' COUNT(o.id) as `total` ,p.type')
            ->join(' `users` u', 'u.id= o.user_id', 'left')
            ->join(' `order_items` oi', 'oi.order_id= o.id', 'left')
            ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
            ->join('products p ', ' p.id = v.product_id ', 'left')
            ->join('users db ', ' db.id = oi.delivery_boy_id', 'left');
        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {

            $count_res->where(" DATE(o.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $count_res->where(" DATE(o.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $this->db->group_Start();
            $count_res->or_like($filters);
            $this->db->group_End();
        }
        $count_res->where("p.type", 'digital_product');

        if (isset($delivery_boy_id)) {
            $count_res->where("oi.delivery_boy_id", $delivery_boy_id);
        }

        if (isset($_GET['user_id']) && $_GET['user_id'] != null) {
            $count_res->where("o.user_id", $_GET['user_id']);
        }
        // Filter By payment
        if (isset($_GET['payment_method']) && !empty($_GET['payment_method'])) {
            $count_res->where('payment_method', $_GET['payment_method']);
        }
        $product_count = $count_res->get('`orders` o')->result_array();

        foreach ($product_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' o.* , u.username, db.username as delivery_boy,p.type')
            ->join(' `users` u', 'u.id= o.user_id', 'left')
            ->join(' `order_items` oi', 'oi.order_id= o.id', 'left')
            ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
            ->join('products p ', ' p.id = v.product_id ', 'left')
            ->join('users db ', ' db.id = oi.delivery_boy_id', 'left');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $search_res->where(" DATE(o.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $search_res->where(" DATE(o.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $search_res->group_Start();
            $search_res->or_like($filters);
            $search_res->group_End();
        }

        if (isset($delivery_boy_id)) {
            $search_res->where("oi.delivery_boy_id", $delivery_boy_id);
        }
        $search_res->where("p.type", 'digital_product');

        if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $search_res->where("o.user_id", $_GET['user_id']);
        }

        if (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) {
            $search_res->where("oi.seller_id", $_GET['seller_id']);
        }
        // Filter By payment
        if (isset($_GET['payment_method']) && !empty($_GET['payment_method'])) {
            $count_res->where('payment_method', $_GET['payment_method']);
        }
        $user_details = $search_res->group_by('o.id')->order_by($sort, "DESC")->limit($limit, $offset)->get('`orders` o')->result_array();

        $i = 0;
        foreach ($user_details as $row) {


            $user_details[$i]['items'] = $this->db->select('oi.*,p.name as name,p.id as product_id,p.type,p.download_allowed, u.username as uname, us.username as seller ')
                ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
                ->join('products p ', ' p.id = v.product_id ', 'left')
                ->join('users u ', ' u.id = oi.user_id', 'left')
                ->join('users us ', ' us.id = oi.seller_id', 'left')
                ->where('oi.order_id', $row['id'])
                ->where('p.type', 'digital_product')
                ->get(' `order_items` oi  ')->result_array();

            ++$i;
        }

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $tota_amount = 0;
        $final_tota_amount = 0;
        $currency_symbol = get_settings('currency');
        foreach ($user_details as $row) {

            if (!empty($row['items'])) {
                $items = $row['items'];
                $items1 = '';
                $temp = '';
                $total_amt = $total_qty = 0;
                $seller = implode(",", array_values(array_unique(array_column($items, "seller"))));

                foreach ($items as $item) {
                    $product_variants = get_variants_values_by_id($item['product_variant_id']);
                    $variants = isset($product_variants[0]['variant_values']) && !empty($product_variants[0]['variant_values']) ? str_replace(',', ' | ', $product_variants[0]['variant_values']) : '-';
                    $temp .= "<b>ID :</b>" . $item['id'] . "<b> Product Variant Id :</b> " . $item['product_variant_id'] . "<b> Variants :</b> " . $variants . "<b> Name : </b>" . $item['name'] . " <b>Price : </b>" . $item['price'] . " <b>QTY : </b>" . $item['quantity'] . " <b>Subtotal : </b>" . $item['quantity'] * $item['price'] . "<br>------<br>";
                    $total_amt += $item['sub_total'];
                    $total_qty += $item['quantity'];
                }

                $items1 = $temp;
                $discounted_amount = $row['total'] * $row['items'][0]['discount'] / 100;
                $final_total = $row['total'] - $discounted_amount;
                $discount_in_rupees = $row['total'] - $final_total;
                $discount_in_rupees = floor($discount_in_rupees);
                $tempRow['id'] = $row['id'];
                $tempRow['user_id'] = $row['user_id'];
                $tempRow['name'] = $row['items'][0]['uname'];
                if (isset($row['mobile']) && !empty($row['mobile']) && $row['mobile'] != "" && $row['mobile'] != " ") {
                    $tempRow['mobile'] = (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($row['mobile']) - 3) . substr($row['mobile'], -3) : $row['mobile'];
                } else {
                    $tempRow['mobile'] = "";
                }
                $tempRow['delivery_charge'] = $currency_symbol . ' ' . $row['delivery_charge'];
                $tempRow['items'] = $items1;
                $tempRow['sellers'] = $seller;
                $tempRow['total'] = $currency_symbol . ' ' . $row['total'];
                $tota_amount += intval($row['total']);
                $tempRow['wallet_balance'] = $currency_symbol . ' ' . $row['wallet_balance'];
                $tempRow['discount'] = $currency_symbol . ' ' . $discount_in_rupees . '(' . $row['items'][0]['discount'] . '%)';
                $tempRow['promo_discount'] = $currency_symbol . ' ' . $row['promo_discount'];
                $tempRow['promo_code'] = $row['promo_code'];
                $tempRow['notes'] = $row['notes'];
                $tempRow['qty'] = $total_qty;
                $tempRow['final_total'] = $currency_symbol . ' ' . $row['total_payable'];
                $final_total = $row['final_total'] - $row['wallet_balance'] - $row['discount'];
                $tempRow['final_total'] = $currency_symbol . ' ' . $final_total;
                $final_tota_amount += intval($row['final_total']);
                $tempRow['deliver_by'] = $row['delivery_boy'];
                $tempRow['payment_method'] = $row['payment_method'];
                $updated_username = fetch_details('users', 'id =' . $row['items'][0]['updated_by'], 'username');
                $tempRow['updated_by'] = $updated_username[0]['username'];
                $tempRow['address'] = output_escaping(str_replace('\r\n', '</br>', $row['address']));
                $tempRow['delivery_date'] = $row['delivery_date'];
                $tempRow['delivery_time'] = $row['delivery_time'];
                $tempRow['date_added'] = date('d-m-Y', strtotime($row['date_added']));
                $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['id'] . '" class="btn btn-primary btn-xs mr-1 mb-1" title="View" ><i class="fa fa-eye"></i></a>';
                if (!$this->ion_auth->is_delivery_boy()) {
                    $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['id'] . ' class="btn btn-primary btn-xs mr-1 mb-1" title="View" ><i class="fa fa-eye"></i></a>';
                    $operate .= '<a href="javascript:void(0)" class="delete-orders btn btn-danger btn-xs mr-1 mb-1" data-id=' . $row['id'] . ' title="Delete" ><i class="fa fa-trash"></i></a>';
                    $operate .= '<a href="' . base_url() . 'admin/invoice?edit_id=' . $row['id'] . '" class="btn btn-info btn-xs mr-1 mb-1" title="Invoice" ><i class="fa fa-file"></i></a>';
                } else {
                    $operate = '<a href=' . base_url('delivery_boy/orders/edit_orders') . '?edit_id=' . $row['id'] . ' class="btn btn-primary btn-xs mr-1 mb-1" title="View"><i class="fa fa-eye"></i></a>';
                }
                $tempRow['operate'] = $operate;
                $rows[] = $tempRow;
            }
        }
        if (!empty($user_details)) {
            $tempRow['id'] = '-';
            $tempRow['user_id'] = '-';
            $tempRow['name'] = '-';
            $tempRow['mobile'] = '-';
            $tempRow['delivery_charge'] = '-';
            $tempRow['items'] = '-';
            $tempRow['sellers'] = '-';
            $tempRow['total'] = '<span class="badge badge-danger">' . $currency_symbol . ' ' . $tota_amount . '</span>';
            $tempRow['wallet_balance'] = '-';
            $tempRow['discount'] = '-';
            $tempRow['qty'] = '-';
            $tempRow['final_total'] = '<span class="badge badge-danger">' . $currency_symbol . ' ' . $final_tota_amount . '</span>';
            $tempRow['deliver_by'] = '-';
            $tempRow['payment_method'] = '-';
            $tempRow['address'] = '-';
            $tempRow['delivery_time'] = '-';
            $tempRow['status'] = '-';
            $tempRow['active_status'] = '-';
            $tempRow['wallet_balance'] = '-';
            $tempRow['date_added'] = '-';
            $tempRow['operate'] = '-';
            array_push($rows, $tempRow);
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
    public function get_digital_product_order_items_list($delivery_boy_id = NULL, $offset = 0, $limit = 10, $sort = " o.id ", $order = 'ASC', $seller_id = NULL)
    {
        $customer_privacy = false;
        if (isset($seller_id) && $seller_id != "") {
            $customer_privacy = get_seller_permission($seller_id, 'customer_privacy');
        }

        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];

            $filters = [
                'un.username' => $search,
                'u.username' => $search,
                'us.username' => $search,
                'un.email' => $search,
                'oi.id' => $search,
                'o.mobile' => $search,
                'o.address' => $search,
                'o.payment_method' => $search,
                'oi.sub_total' => $search,
                'o.delivery_time' => $search,
                'oi.active_status' => $search,
                'oi.date_added' => $search
            ];
        }

        $count_res = $this->db->select(' COUNT(o.id) as `total` ,p.type')
            ->join(' `users` u', 'u.id= oi.delivery_boy_id', 'left')
            ->join('users us ', ' us.id = oi.seller_id', 'left')
            ->join(' `orders` o', 'o.id= oi.order_id')
            ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
            ->join('products p ', ' p.id = v.product_id ', 'left')
            ->join('users un ', ' un.id = o.user_id', 'left');
        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {

            $count_res->where(" DATE(oi.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $count_res->where(" DATE(oi.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $this->db->group_Start();
            $count_res->or_like($filters);
            $this->db->group_End();
        }
        $count_res->where("p.type", 'digital_product');

        if (isset($delivery_boy_id)) {
            $count_res->where("oi.delivery_boy_id", $delivery_boy_id);
        }

        if (isset($seller_id) && $seller_id != "") {
            $count_res->where("oi.seller_id", $seller_id);
            $count_res->where("oi.active_status != 'awaiting'");
        }

        if (isset($_GET['user_id']) && $_GET['user_id'] != null) {
            $count_res->where("o.user_id", $_GET['user_id']);
        }

        if (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) {
            $count_res->where("oi.seller_id", $_GET['seller_id']);
        }

        if (isset($_GET['order_status']) && !empty($_GET['order_status'])) {
            $count_res->where('oi.active_status', $_GET['order_status']);
        }
        // Filter By payment
        if (isset($_GET['payment_method']) && !empty($_GET['payment_method'])) {
            $count_res->where('payment_method', $_GET['payment_method']);
        }

        $product_count = $count_res->get('order_items oi')->result_array();
        foreach ($product_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' o.id as order_id,oi.id as order_item_id,o.*,oi.*,ot.courier_agency,ot.tracking_id,ot.url, u.username as delivery_boy,p.type,p.download_allowed, un.username as username,us.username as seller_name')
            ->join('users u', 'u.id= oi.delivery_boy_id', 'left')
            ->join('users us ', ' us.id = oi.seller_id', 'left')
            ->join('order_tracking ot ', ' ot.order_item_id = oi.id', 'left')
            ->join('orders o', 'o.id= oi.order_id')
            ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
            ->join('products p ', ' p.id = v.product_id ', 'left')
            ->join('users un ', ' un.id = o.user_id', 'left');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $search_res->where(" DATE(oi.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $search_res->where(" DATE(oi.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $search_res->group_Start();
            $search_res->or_like($filters);
            $search_res->group_End();
        }
        $search_res->where("p.type", 'digital_product');
        if (isset($delivery_boy_id)) {
            $search_res->where("oi.delivery_boy_id", $delivery_boy_id);
        }

        if (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) {
            $count_res->where("oi.seller_id", $_GET['seller_id']);
        }

        if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $search_res->where("o.user_id", $_GET['user_id']);
        }

        if (isset($_GET['order_status']) && !empty($_GET['order_status'])) {
            $search_res->where('oi.active_status', $_GET['order_status']);
        }
        // Filter By payment
        if (isset($_GET['payment_method']) && !empty($_GET['payment_method'])) {
            $count_res->where('payment_method', $_GET['payment_method']);
        }
        $user_details = $search_res->order_by($sort, "DESC")->limit($limit, $offset)->get('order_items oi')->result_array();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $tota_amount = 0;
        $final_tota_amount = 0;
        $currency_symbol = get_settings('currency');
        $count = 1;
        foreach ($user_details as $row) {

            $temp = '';
            if (!empty($row['items'][0]['order_status'])) {
                $status = json_decode($row['items'][0]['order_status'], 1);
                foreach ($status as $st) {
                    $temp .= @$st[0] . " : " . @$st[1] . "<br>------<br>";
                }
            }

            if (trim($row['active_status']) == 'awaiting') {
                $active_status = '<label class="badge badge-secondary">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'received') {
                $active_status = '<label class="badge badge-primary">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'processed') {
                $active_status = '<label class="badge badge-info">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'shipped') {
                $active_status = '<label class="badge badge-warning">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'delivered') {
                $active_status = '<label class="badge badge-success">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'returned' || $row['active_status'] == 'cancelled') {
                $active_status = '<label class="badge badge-danger">' . $row['active_status'] . '</label>';
            }

            $status = $temp;
            $tempRow['id'] = $count;
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['order_item_id'] = $row['order_item_id'];
            $tempRow['user_id'] = $row['user_id'];
            $tempRow['seller_id'] = $row['seller_id'];
            $tempRow['notes'] = (isset($row['notes']) && !empty($row['notes'])) ? $row['notes'] : "";
            $tempRow['username'] = $row['username'];
            $tempRow['seller_name'] = $row['seller_name'];
            $tempRow['is_credited'] = ($row['is_credited']) ? '<label class="badge badge-success">Credited</label>' : '<label class="badge badge-danger">Not Credited</label>';
            $tempRow['product_name'] = $row['product_name'];
            $tempRow['product_name'] .= (!empty($row['variant_name'])) ? '(' . $row['variant_name'] . ')' : "";
            if (isset($row['mobile']) && !empty($row['mobile']) && $row['mobile'] != "" && $row['mobile'] != " ") {
                $tempRow['mobile'] = (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($row['mobile']) - 3) . substr($row['mobile'], -3) : $row['mobile'];
            } else {
                $tempRow['mobile'] = "";
            }
            $tempRow['sub_total'] = $currency_symbol . ' ' . $row['sub_total'];
            $tempRow['quantity'] = $row['quantity'];
            $final_tota_amount += intval($row['sub_total']);
            $tempRow['delivery_boy'] = $row['delivery_boy'];
            $tempRow['payment_method'] = $row['payment_method'];
            $tempRow['delivery_boy_id'] = $row['delivery_boy_id'];
            $tempRow['product_variant_id'] = $row['product_variant_id'];
            $tempRow['delivery_date'] = $row['delivery_date'];
            $tempRow['delivery_time'] = $row['delivery_time'];
            $tempRow['courier_agency'] = (isset($row['courier_agency']) && !empty($row['courier_agency'])) ? $row['courier_agency'] : "";
            $tempRow['tracking_id'] = (isset($row['tracking_id']) && !empty($row['tracking_id'])) ? $row['tracking_id'] : "";
            $tempRow['url'] = (isset($row['url']) && !empty($row['url'])) ? $row['url'] : "";
            $updated_username = fetch_details('users', 'id =' . $row['updated_by'], 'username');
            $tempRow['updated_by'] = $updated_username[0]['username'];
            $tempRow['status'] = $status;
            $tempRow['active_status'] = $active_status;
            $tempRow['date_added'] = date('d-m-Y', strtotime($row['date_added']));
            $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['order_id'] . '" class="btn btn-primary btn-xs mr-1 mb-1" title="View" ><i class="fa fa-eye"></i></a>';
            if ($this->ion_auth->is_delivery_boy()) {
                $operate = '<a href=' . base_url('delivery_boy/orders/edit_orders') . '?edit_id=' . $row['order_id'] . ' class="btn btn-primary btn-xs mr-1 mb-1" title="View"><i class="fa fa-eye"></i></a>';
            } else if ($this->ion_auth->is_seller()) {
                $operate = '<a href=' . base_url('seller/orders/edit_orders') . '?edit_id=' . $row['order_id'] . ' class="btn btn-primary btn-xs mr-1 mb-1" title="View"><i class="fa fa-eye"></i></a>';
                $operate .= '<a href="' . base_url() . 'seller/invoice?edit_id=' . $row['order_id'] . '" class="btn btn-info btn-xs mr-1 mb-1" title="Invoice" ><i class="fa fa-file"></i></a>';
                if ($row['download_allowed'] == 0) {
                    $send_mail = '<a href="javascript:void(0)" class="edit_btn btn btn-primary btn-xs mr-1 mb-1" title="Edit" data-id="' . $row['order_id'] . '" data-url="seller/orders/digital_product_orders/"><i class="fas fa-paper-plane"></i></a>';
                    $send_mail .= '<a href="https://mail.google.com/mail/?view=cm&fs=1&tf=1&to=' . $row['email'] . '" class="btn btn-danger btn-xs mr-1 mb-1" target="_blank"><i class="fab fa-google"></i></a>';
                }
            } else if ($this->ion_auth->is_admin()) {
                $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['order_id'] . ' class="btn btn-primary btn-xs mr-1 mb-1" title="View" ><i class="fa fa-eye"></i></a>';
                $operate .= '<a href="javascript:void(0)" class="delete-order-items btn btn-danger btn-xs mr-1 mb-1" data-id=' . $row['order_item_id'] . ' title="Delete" ><i class="fa fa-trash"></i></a>';
                $operate .= '<a href="' . base_url() . 'admin/invoice?edit_id=' . $row['order_id'] . '" class="btn btn-info btn-xs mr-1 mb-1" title="Invoice" ><i class="fa fa-file"></i></a>';
                if ($row['download_allowed'] == 0) {
                    $send_mail = '<a href="javascript:void(0)" class="edit_btn btn btn-primary btn-xs mr-1 mb-1" title="Edit" data-id="' . $row['order_id'] . '" data-url="admin/orders/digital_product_orders/"><i class="fas fa-paper-plane"></i></a>';
                    $send_mail .= '<a href="https://mail.google.com/mail/?view=cm&fs=1&tf=1&to=' . $row['email'] . '" class="btn btn-danger btn-xs mr-1 mb-1" target="_blank"><i class="fab fa-google"></i></a>';
                }
            } else {
                $operate = "";
            }
            $tempRow['operate'] = $operate;
            $tempRow['send_mail'] = $send_mail;

            $rows[] = $tempRow;
            $count++;
        }
        if (!empty($user_details)) {
            $tempRow['id'] = '-';
            $tempRow['order_id'] = '-';
            $tempRow['order_item_id'] = '-';
            $tempRow['user_id'] = '-';
            $tempRow['seller_id'] = '-';
            $tempRow['username'] = '-';
            $tempRow['seller_name'] = '-';
            $tempRow['is_credited'] = '-';
            $tempRow['mobile'] = '-';
            $tempRow['delivery_charge'] = '-';
            $tempRow['product_name'] = '-';
            $tempRow['sub_total'] = '<span class="badge badge-danger">' . $currency_symbol . ' ' . $final_tota_amount . '</span>';
            $tempRow['discount'] = '-';
            $tempRow['quantity'] = '-';
            $tempRow['delivery_boy'] = '-';
            $tempRow['delivery_time'] = '-';
            $tempRow['status'] = '-';
            $tempRow['active_status'] = '-';
            $tempRow['date_added'] = '-';
            $tempRow['operate'] = '-';
            $tempRow['send_mail'] = '-';
            array_push($rows, $tempRow);
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    public function add_bank_transfer_proof($data)
    {
        $data = escape_array($data);
        for ($i = 0; $i < count($data['attachments']); $i++) {
            $order_data = [
                'order_id' => $data['order_id'],
                'attachments' => $data['attachments'][$i],
            ];
            $this->db->insert('order_bank_transfer', $order_data);
        }
        return true;
    }

    public function get_order_tracking_list($seller_id = '')
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $multipleWhere = '';
        $where = [];

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            $sort = ($_GET['sort'] == 'id') ? "order_tracking.id" : $_GET['sort'];
        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) && $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                'order_tracking.id' => $search,
                'order_tracking.order_id' => $search,
                'order_tracking.tracking_id' => $search,
                'order_tracking.courier_agency' => $search,
                'order_tracking.order_item_id' => $search,
                'order_tracking.url' => $search,
                'order_items.product_name' => $search, // added join field
            ];
        }

        if (isset($_GET['order_id']) && $_GET['order_id'] != '') {
            $where = ['order_tracking.order_id' => $_GET['order_id']];
        }

        // Count query with JOIN
        $count_res = $this->db->select('COUNT(DISTINCT order_tracking.id) as total');
        $this->db->from('order_tracking');
        $this->db->join('order_items', "FIND_IN_SET(order_items.id, order_tracking.order_item_id)", 'left');

        if (isset($seller_id) && !empty($seller_id) && $seller_id != NULL) {
            $count_res->where('order_items.seller_id', $seller_id);
        }

        if (!empty($multipleWhere)) {
            $this->db->group_start();
            $this->db->or_like($multipleWhere);
            $this->db->group_end();
        }
        if (!empty($where)) {
            $this->db->where($where);
        }

        $txn_count = $count_res->get()->result_array();


        $total = isset($txn_count[0]['total']) ? $txn_count[0]['total'] : 0;

        // Search result query with JOIN
        $search_res = $this->db->select('order_tracking.*, order_items.seller_id');
        $this->db->from('order_tracking');
        $this->db->join('order_items', "FIND_IN_SET(order_items.id, order_tracking.order_item_id)", 'left');

        if (isset($seller_id) && !empty($seller_id) && $seller_id != NULL) {
            $search_res->where('order_items.seller_id', $seller_id);
        }

        if (!empty($multipleWhere)) {
            $this->db->group_start();
            $this->db->or_like($multipleWhere);
            $this->db->group_end();
        }
        if (!empty($where)) {
            $this->db->where($where);
        }

        $txn_search_res = $search_res->group_by('order_tracking.id')->order_by($sort, $order)->limit($limit, $offset)->get()->result_array();


        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($txn_search_res as $row) {
            $row = output_escaping($row);

            if ($this->ion_auth->is_seller()) {
                $operate = '<a href=' . base_url('seller/orders/edit_orders') . '?edit_id=' . $row['order_id'] . ' class="btn btn-primary btn-xs action-btn mr-1 mb-1 ml-1" title="View Order" ><i class="fa fa-eye"></i></a>';
            } else {
                $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['order_id'] . ' class="btn btn-primary btn-xs action-btn mr-1 mb-1 ml-1" title="View Order" ><i class="fa fa-eye"></i></a>';
            }

            $tempRow['id'] = $row['id'];
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['order_item_id'] = $row['order_item_id'];
            $tempRow['product_name'] = $row['product_name']; // from order_items
            $tempRow['courier_agency'] = $row['courier_agency'];
            $tempRow['tracking_id'] = $row['tracking_id'];
            $tempRow['url'] = $row['url'];
            $tempRow['date'] = date('d-m-Y', strtotime($row['date_created']));
            $tempRow['operate'] = $operate;

            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    public function get_digital_order_mail_list($from_app = false)
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $multipleWhere = '';
        $where = [];

        //$_GET used for admin/seller panel data and $_POST is used for seller API

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];
        if (isset($_POST['offset']))
            $offset = $_POST['offset'];
        if (isset($_POST['limit']))
            $limit = $_POST['limit'];



        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_POST['sort']))
            if ($_POST['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_POST['sort'];
            }
        if (isset($_GET['order']))
            $order = $_GET['order'];
        if (isset($_POST['order']))
            $order = $_POST['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['`id`' => $search, '`order_id`' => $search, '`order_item_id`' => $search, 'subject' => $search, 'message' => $search, 'file_url' => $search];
        }
        if (isset($_POST['search']) and $_POST['search'] != '') {
            $search = $_POST['search'];
            $multipleWhere = ['`id`' => $search, '`order_id`' => $search, '`order_item_id`' => $search, 'subject' => $search, 'message' => $search, 'file_url' => $search];
        }
        if (isset($_GET['order_id']) and $_GET['order_id'] != '') {
            $where = ['order_id' => $_GET['order_id']];
        }
        if (isset($_POST['order_id']) and $_POST['order_id'] != '') {
            $where = ['order_id' => $_POST['order_id']];
        }
        if (isset($_GET['order_item_id']) and $_GET['order_item_id'] != '') {
            $where = ['order_item_id' => $_GET['order_item_id']];
        }
        if (isset($_POST['order_item_id']) and $_POST['order_item_id'] != '') {
            $where = ['order_item_id' => $_POST['order_item_id']];
        }

        $count_res = $this->db->select(' COUNT(id) as `total` ');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $count_res->or_like($multipleWhere);
            $this->db->group_End();
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }


        $txn_count = $count_res->get('digital_orders_mails')->result_array();

        foreach ($txn_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' * ');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $search_res->or_like($multipleWhere);
            $this->db->group_End();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $txn_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('digital_orders_mails')->result_array();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($txn_search_res as $row) {
            $row = output_escaping($row);

            $tempRow['id'] = $row['id'];
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['order_item_id'] = $row['order_item_id'];
            $tempRow['subject'] = $row['subject'];
            $tempRow['message'] = description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', $row['message'])));
            $tempRow['file_url'] = $row['file_url'];
            $tempRow['date_added'] = $row['date_added'];
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        if ($from_app == true) {
            return $bulkData;
        } else {
            print_r(json_encode($bulkData));
        }
    }


    public function get_seller_order_tracking_list()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $multipleWhere = '';
        $where = [];

        if (isset($_POST['offset']))
            $offset = $_POST['offset'];
        if (isset($_POST['limit']))
            $limit = $_POST['limit'];

        if (isset($_POST['sort']))
            if ($_POST['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_POST['sort'];
            }
        if (isset($_POST['order']))
            $order = $_POST['order'];

        if (isset($_POST['search']) and $_POST['search'] != '') {
            $search = $_POST['search'];
            $multipleWhere = ['`id`' => $search, '`order_id`' => $search, '`tracking_id`' => $search, 'courier_agency' => $search, 'order_item_id' => $search, 'url' => $search];
        }
        if (isset($_POST['order_id']) and $_POST['order_id'] != '') {
            $where = ['order_id' => $_POST['order_id']];
        }

        $count_res = $this->db->select(' COUNT(id) as `total` ');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $count_res->or_like($multipleWhere);
            $this->db->group_End();
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }


        $txn_count = $count_res->get('order_tracking')->result_array();

        foreach ($txn_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' * ');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $search_res->or_like($multipleWhere);
            $this->db->group_End();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $txn_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('order_tracking')->result_array();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($txn_search_res as $row) {
            $row = output_escaping($row);

            $tempRow['id'] = $row['id'];
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['order_item_id'] = $row['order_item_id'];
            $tempRow['courier_agency'] = $row['courier_agency'];
            $tempRow['tracking_id'] = $row['tracking_id'];
            $tempRow['url'] = $row['url'];
            $tempRow['shiprocket_order_id'] = $row['shiprocket_order_id'];
            $tempRow['shipment_id'] = $row['shipment_id'];
            $tempRow['courier_company_id'] = $row['courier_company_id'];
            $tempRow['awb_code'] = $row['awb_code'];
            $tempRow['pickup_status'] = $row['pickup_status'];
            $tempRow['pickup_scheduled_date'] = $row['pickup_scheduled_date'];
            $tempRow['pickup_token_number'] = $row['pickup_token_number'];
            $tempRow['status'] = $row['status'];
            $tempRow['others'] = $row['others'];
            $tempRow['pickup_generated_date'] = $row['pickup_generated_date'];
            $tempRow['data'] = $row['data'];
            $tempRow['is_canceled'] = $row['is_canceled'];
            $tempRow['manifest_url'] = $row['manifest_url'];
            $tempRow['label_url'] = $row['label_url'];
            $tempRow['invoice_url'] = $row['invoice_url'];
            $tempRow['date'] = date('d-m-Y', strtotime($row['created_at']));
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    public function get_order_tracking($limit = "", $offset = '', $sort = 'id', $order = 'DESC', $search = NULL)
    {
        $multipleWhere = '';

        if (isset($search) and $search != '') {
            $multipleWhere = ['id' => $search, 'order_id' => $search, 'tracking_id' => $search, 'courier_agency' => $search, 'order_item_id' => $search, 'url' => $search];
        }
        $count_res = $this->db->select(' COUNT(oi.id) as `total` ')
            ->from('order_tracking  ot')
            ->join('order_items oi', 'ot.order_item_id = oi.id', 'left')
            ->where('oi.seller_id=' . $_POST['seller_id'])
            ->get()->result_array();
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->or_like($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }
        foreach ($count_res as $row) {
            $total = $row['total'];
        }
        $city_search_res = $this->db->select('ot.*,oi.seller_id')
            ->from('order_tracking  ot')
            ->join('order_items oi', 'ot.order_item_id = oi.id', 'left')
            ->where('oi.seller_id=' . $_POST['seller_id'])
            ->order_by($sort, $order)->limit($limit, $offset)
            ->get()->result_array();
        $bulkData = array();
        $bulkData['error'] = (empty($city_search_res)) ? true : false;
        $bulkData['message'] = (empty($city_search_res)) ? 'Order Tracking details does not exist' : 'Order Tracking details are retrieve successfully';
        $bulkData['total'] = (empty($city_search_res)) ? 0 : $total;
        $rows = $tempRow = array();

        foreach ($city_search_res as $row) {
            $tempRow['id'] = $row['id'];
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['order_item_id'] = $row['order_item_id'];
            $tempRow['courier_agency'] = $row['courier_agency'];
            $tempRow['tracking_id'] = $row['tracking_id'];
            $tempRow['url'] = $row['url'];
            $tempRow['date'] = date('d-m-Y', strtotime($row['created_at']));
            $rows[] = $tempRow;
        }
        $bulkData['data'] = $rows;
        print_r(json_encode($bulkData));
    }

    // only use for webhook api
    public function update_order_status($id, $status, $fromAPP = false)
    {
        $order_item_details = fetch_details('orders', ['id' => $id], 'id');

        $order_details = fetch_orders($order_item_details[0]['id'], null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 0);

        if (!empty($order_details) && !empty($order_item_details)) {

            $order_details = $order_details['order_data'];
            $order_items_details = $order_details[0]['order_items'];

            $user_id = $order_details[0]['user_id'];
            $user_res = fetch_details('users', ['id' => $user_id], 'fcm_id,username');
            $fcm_ids = array();
            if (!empty($user_res[0]['fcm_id'])) {
                $fcm_ids[0][] = $user_res[0]['fcm_id'];
            }
            for ($i = 0; $i < count($order_items_details); $i++) {
                if ($this->update_order(['status' => $status], ['id' => $order_items_details[$i]['id']], true, 'order_items')) {
                    $this->order_model->update_order(['active_status' => $status], ['id' => $order_items_details[$i]['id']], false, 'order_items');
                }
            }

            $response['error'] = false;
            $response['message'] = 'Status Updated Successfully';
            $response['data'] = array();

            return $response;
        }
    }

    public function send_digital_product($data)
    {
        $message = str_replace('\r\n', '&#13;&#10;', $data['message']);
        $data = escape_array($data);
        $attachment = base_url($data['pro_input_file']);
        $to = $data['email'];
        $subject = $data['subject'];
        $email_message = array(
            'username' => 'Hello, Dear <b>' . ucfirst($data['username']) . '</b>, ',
            'subject' => $subject,
            'email' => '',
            'message' => $message
        );
        $mail = send_digital_product_mail($to, $subject, $this->load->view('admin/pages/view/contact-email-template', $email_message, TRUE), $attachment);
        return $mail;
    }
    public function get_shiprocket_order($shiprocket_order_id)
    {

        $this->load->library(['Shiprocket']);
        $res = $this->shiprocket->get_specific_order($shiprocket_order_id);
        return $res;
    }

    public function consignment_view($order_id = NULL, $seller_id = NULL, $delivery_boy_id = NULL)
    {
        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        // Map allowed sort keys to actual DB columns
        $sort_whitelist = [
            'id' => 'c.id',
            'order_id' => 'c.order_id',
            'name' => 'c.name',
            'status' => 'c.active_status',
            'created_date' => 'c.created_at',
            'order_date' => 'o.date_added',
        ];

        $sort_key = isset($_GET['sort']) ? $_GET['sort'] : 'id';
        $sort = isset($sort_whitelist[$sort_key]) ? $sort_whitelist[$sort_key] : 'c.id';
        $order = isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC']) ? $_GET['order'] : 'ASC';

        if (isset($_GET['payment_method'])) {
            $payment_method = $_GET['payment_method'];
        }

        if (isset($_GET['order_status'])) {
            $order_status = $_GET['order_status'];
        }

        if (isset($_GET['search']) && $_GET['search'] != '') {
            $search = $_GET['search'];
            $filters = [
                'c.id' => $search,
                'c.order_id ' => $search,
                'c.name' => $search,
                'c.status' => $search,
                'c.created_at' => $search
            ];
        }

        $count_res = $this->db->select('COUNT(DISTINCT(c.id)) as total')
            ->join('consignment_items ci', 'ci.consignment_id = c.id')
            ->join('orders o', 'c.order_id = o.id')
            ->join('order_items oi', 'oi.id = ci.order_item_id')
            ->join('users u', 'u.id = o.user_id');

        if (isset($order_id)) {
            $count_res->where("o.id", $order_id);
        } else {
            if (isset($delivery_boy_id)) {
                $count_res->where("c.delivery_boy_id", $delivery_boy_id);
            }
        }
        if (isset($seller_id)) {
            $count_res->where("oi.seller_id", $seller_id);
        }
        if (!empty($order_status)) {
            $count_res->where("c.active_status", $order_status);
        }
        if (!empty($payment_method)) {
            if ($payment_method == "online-payment") {
                $count_res->where("o.payment_method !=", "COD");
            } else {
                $count_res->where("o.payment_method", $payment_method);
            }
        }
        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $count_res->where("DATE(o.date_added) >= ", $_GET['start_date']);
            $count_res->where("DATE(o.date_added) <= ", $_GET['end_date']);
        }
        if (isset($filters) && !empty($filters)) {
            $this->db->group_Start();
            $count_res->or_like($filters);
            $this->db->group_End();
        }

        $consignment = $count_res->get('consignments c')->result_array();
        $total = isset($consignment[0]['total']) ? $consignment[0]['total'] : 0;

        $search_res = $this->db->select('DISTINCT(c.id) as id, c.order_id as order_id, c.name as name, c.active_status as status, c.created_at')
            ->join('consignment_items ci', 'ci.consignment_id = c.id')
            ->join('orders o', 'c.order_id = o.id')
            ->join('order_items oi', 'oi.id = ci.order_item_id')
            ->join('users u', 'u.id = o.user_id', 'left');

        if (isset($order_id)) {
            $search_res->where("o.id", $order_id);
        } else {
            if (isset($delivery_boy_id)) {
                $search_res->where("c.delivery_boy_id", $delivery_boy_id);
            }
        }
        if (isset($seller_id)) {
            $search_res->where("oi.seller_id", $seller_id);
        }
        if (!empty($order_status)) {
            $search_res->where("c.active_status", $order_status);
        }
        if (!empty($payment_method)) {
            if ($payment_method == "online-payment") {
                $search_res->where("o.payment_method !=", "COD");
            } else {
                $search_res->where("o.payment_method", $payment_method);
            }
        }
        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $search_res->where("DATE(o.date_added) >=", $_GET['start_date']);
            $search_res->where("DATE(o.date_added) <=", $_GET['end_date']);
        }
        if (isset($filters) && !empty($filters)) {
            $search_res->group_Start();
            $search_res->or_like($filters);
            $search_res->group_End();
        }

        $search_res->group_by('id');
        $consignment_list = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('consignments c')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        foreach ($consignment_list as $row) {
            $item_detail = $this->db->select("oi.*,u.username,c.active_status as active_status,c.delivery_boy_id, ci.*,sd.store_name,o.payment_method,o.mobile, p.image, p.pickup_location")
                ->join('order_items oi', 'oi.id = ci.order_item_id', "left")
                ->join('product_variants pv', 'pv.id = ci.product_variant_id', "left")
                ->join('products p', 'p.id = pv.product_id', "left")
                ->join('orders o', 'o.id = oi.order_id', "left")
                ->join('users u', 'u.id = oi.user_id', "left")
                ->join('consignments c', 'c.id = ci.consignment_id', "left")
                ->join('seller_data sd', 'sd.user_id = oi.seller_id', "left")
                ->where('ci.consignment_id', $row['id'])
                ->get('consignment_items ci')->result_array();

            if ($this->ion_auth->is_delivery_boy()) {
                $order_link = "<a href='" . base_url('delivery_boy/orders/edit_orders?edit_id=' . $row['id']) . "' target='_blank'>" . $row['id'] . "</a>";
            } else {
                $order_link = "<a href='" . base_url((($this->ion_auth->is_seller()) ? "seller" : "admin") . "/orders/edit_orders?edit_id=" . $row['id']) . "' target='_blank'>" . $row['id'] . "</a>";
            }

            $product_name = [];
            $qtys = [];
            foreach ($item_detail as $key => $details) {
                array_push($product_name, $details['product_name']);
                array_push($qtys, $details['quantity']);
                $item_detail[$key]['image'] = base_url($details['image']);
            }

            $tempRow = [];
            $tempRow['id'] = $order_link;
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['seller_id'] = $row['seller_id'];
            $tempRow['username'] = $item_detail[0]['username'];
            $tempRow['mobile'] = $item_detail[0]['mobile'];
            $tempRow['product_name'] = implode(', ', $product_name);
            $tempRow['quantity'] = implode(', ', $qtys);
            $tempRow['name'] = $row['name'];
            $tempRow['payment_method'] = str_replace("_", " ", $item_detail[0]['payment_method']);
            $tempRow['status'] = '<p class="m-0 text-capitalize badge badge-dark">' . str_replace("_", " ", $row['status']) . "</p>";
            if (!isset($delivery_boy_id)) {
                $tempRow['otp'] = $row['otp'];
                $operate = '<button type="button" class="btn btn-dark btn-xs mx-1 view_consignment_items" data-toggle="modal" data-target="#view_consignment_items_modal" data-items=\'' . htmlspecialchars(json_encode($item_detail), ENT_QUOTES, 'UTF-8') . '\' data-id=' . $row['id'] . '><i class="fa fa-eye"></i></button>';
                $operate .= '<a href="' . base_url((($this->ion_auth->is_seller()) ? "seller" : "admin") . '/invoice/consignment_invoice?edit_id=' . $row['id']) . '" onclick="consignment_invoice(' . $row['id'] . ')" class="btn btn-info mx-1 btn-xs" data-id="' . $row['id'] . '" title="Invoice" ><i class="fa fa-file"></i></a>';
            } else {
                $operate = '<a href="' . base_url("delivery_boy/orders/edit_orders?edit_id=" . $row['id']) . '" class="btn btn-dark btn-xs mx-1 view_consignment_items"><i class="fa fa-eye"></i></a>';
            }
            $tempRow['order_date'] = $item_detail[0]['date_added'];
            $tempRow['created_date'] = $item_detail[0]['date_added'];

            $order_tracking_data = fetch_details('order_tracking', ['consignment_id' => $row['id'], 'shipment_id' => ""], 'courier_agency,tracking_id,url');
            if ($this->ion_auth->is_seller() || $this->ion_auth->is_admin()) {
                $operate .= '<button type="button" class="btn btn-success mx-1 btn-xs" data-id="' . $row['id'] . '" data-consignment-name="' . $row['name'] . '" data-status="' . $row['status'] . '" data-items=\'' . htmlspecialchars(json_encode($item_detail), ENT_QUOTES, 'UTF-8') . '\' data-toggle="modal" data-target="#consignment_status_modal"><i class="fa fa-pen"></i></button>';
                $operate .= '<a href="javascript:void(0)" class="btn btn-danger mx-1 btn-xs delete_consignment" data-id="' . $row['id'] . '" onclick="delete_consignment(' . $row['id'] . ')" title="Delete" ><i class="fa fa-trash"></i></a>';
                $operate .= '<a href="javascript:void(0)" class="edit_order_tracking btn btn-success mx-1 btn-xs" title="Order Tracking" data-id=' . $row['id'] . '  data-tracking-data=\'' . json_encode($order_tracking_data) . '\' data-target="#transaction_modal" data-toggle="modal"><i class="fa fa-map-marker-alt"></i></a>';
            }

            $tempRow['operate'] = $operate;
            $tempRow['consignment_items'] = fetch_details('consignment_items', ['consignment_id' => $row['id']]);
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }


    public function check_order_exists($order_id, $seller_id)
    {
        $this->db->from('orders');
        $this->db->where('id', $order_id);
        // $this->db->where('seller_id', $seller_id);
        $query = $this->db->get();

        return $query->num_rows() > 0;
    }
}

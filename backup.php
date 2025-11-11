backup

 public function update_order_status()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller() && ($this->ion_auth->seller_status() == 1 || $this->ion_auth->seller_status() == 0)) {

            if (isset($_POST['status']) && !empty($_POST['status']) && $_POST['status'] != '' && ($_POST['status'] == 'cancelled' || $_POST['status'] == 'returned')) {
                $this->form_validation->set_rules('order_item_id[]', 'Order Item ID', 'trim|required|xss_clean', array('required' => "Please select atleast one item of seller for order cancelation or return."));
            }
            if (isset($_POST['deliver_by']) && !empty($_POST['deliver_by']) && $_POST['deliver_by'] != '') {
                $this->form_validation->set_rules('deliver_by', 'Delvery Boy Id', 'trim|numeric|xss_clean');
            }
            if (isset($_POST['status']) && !empty($_POST['status']) && $_POST['status'] != '') {
                $this->form_validation->set_rules('status', 'Status', 'trim|xss_clean|in_list[received,processed,shipped,delivered,cancelled,returned]');
            }
            if (empty($_POST['status']) && empty($_POST['deliver_by'])) {
                $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean', array('required' => "Please select status or delivery boy for updation."));
            }
            print_r($_POST);
            die;
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['message'] = validation_errors();
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
                return false;
            }

            $order_itam_id = [];
            $order_itam_ids = [];
            if ($_POST['status'] == 'cancelled' || $_POST['status'] == 'returned') {
                $order_itam_ids = $_POST['order_item_id'];
            } else {
                $order_itam_id = fetch_details('order_items', ['order_id' => $_POST['order_id'], 'seller_id' => $_POST['seller_id'], 'active_status !=' => 'cancelled'], 'id');
                foreach ($order_itam_id as $ids) {
                    array_push($order_itam_ids, $ids['id']);
                }
            }
            if (empty($order_itam_ids)) {
                $this->response['error'] = true;
                $this->response['message'] = 'You can not assign delivery boy of cancelled order.';
                $this->response['data'] = array();
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
                return false;
            }

            $s = [];
            foreach ($order_itam_ids as $ids) {
                $order_detail = fetch_details('order_items', ['id' => $ids], 'is_sent,hash_link');
                if (empty($order_detail[0]['hash_link']) || $order_detail[0]['hash_link'] == '' || $order_detail[0]['hash_link'] == null) {
                    array_push($s, $order_detail[0]['is_sent']);
                }
            }
            $order_data = fetch_details('order_items', ['id' => $order_itam_ids[0]], 'product_variant_id')[0]['product_variant_id'];
            $product_id = fetch_details('product_variants', ['id' => $order_data], 'product_id')[0]['product_id'];
            $product_type = fetch_details('products', ['id' => $product_id], 'type')[0]['type'];
            if ($product_type == 'digital_product' && in_array(0, $s)) {
                $this->response['error'] = true;
                $this->response['message'] = 'Some of the items have not been sent yet,Please send digital items before mark it as delivered.';
                $this->response['data'] = array();
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
                return false;
            }
            $order_items = fetch_details('order_items', "",  '*', "", "", "", "", "id", $order_itam_ids);
            if (isset($_POST['status']) && !empty($_POST['status']) && $_POST['status'] == 'delivered') {
                if (!get_seller_permission($order_items[0]['seller_id'], "view_order_otp")) {
                    $this->response['error'] = true;
                    $this->response['message'] = 'You are not allowed to update delivered status on the item.';
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }
            }
            if (empty($order_items)) {
                $this->response['error'] = true;
                $this->response['message'] = 'No Order Item Found';
                $this->response['data'] = array();
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
                return false;
            }

            if (count($order_itam_ids) != count($order_items)) {
                $this->response['error'] = true;
                $this->response['message'] = 'Some item was not found on status update';
                $this->response['data'] = array();
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
                return false;
            }
            $current_status = fetch_details('order_items', ['seller_id' => $_POST['seller_id'], 'order_id' => $_POST['order_id']], 'active_status,delivery_boy_id');
            $awaitingPresent = false;

            foreach ($current_status as $item) {
                if ($item['active_status'] === 'awaiting') {
                    $awaitingPresent = true;
                    break;
                }
            }

            // delivery boy update here
            $message = '';
            $delivery_boy_updated = 0;
            $delivery_boy_id = (isset($_POST['deliver_by']) && !empty(trim($_POST['deliver_by']))) ? $this->input->post('deliver_by', true) : 0;

            // assign delivery boy when status is processed

            // print_r($_POST['status']);
            // print_r($delivery_boy_id);
            // die;
            if (isset($_POST['status']) && !empty($_POST['status']) && $_POST['status'] == 'processed') {
                if (!isset($delivery_boy_id) || empty($delivery_boy_id) || $delivery_boy_id == 0) {
                    $this->response['error'] = true;
                    $this->response['message'] = "Please select delivery boy to mark this order as processed.";
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }
            }

            // validate delivery boy when status is shipped
            if (isset($_POST['status']) && !empty($_POST['status']) && $_POST['status'] == 'shipped') {
                if ((!isset($current_status[0]['delivery_boy_id']) || empty($current_status[0]['delivery_boy_id']) || $current_status[0]['delivery_boy_id'] == 0) && (empty($_POST['deliver_by']) || $_POST['deliver_by'] == '')) {
                    $this->response['error'] = true;
                    $this->response['message'] = "Please select delivery boy to mark this order as shipped.";
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }
            }

            if (!empty($delivery_boy_id)) {
                if ($awaitingPresent) {
                    $this->response['error'] = true;
                    $this->response['message'] = "Delivery Boy can't assign to awaiting orders ! please confirm the order first.";
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                } else {
                    $delivery_boy = fetch_details('users', ['id' => trim($delivery_boy_id)], '*');
                    if (empty($delivery_boy)) {
                        $this->response['error'] = true;
                        $this->response['message'] = "Invalid Delivery Boy";
                        $this->response['data'] = array();
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        print_r(json_encode($this->response));
                        return false;
                    } else {
                        $current_delivery_boys = fetch_details('order_items', "",  '*', "", "", "", "", "id", $order_itam_ids);
                        $settings = get_settings('system_settings', true);
                        $app_name = isset($settings['app_name']) && !empty($settings['app_name']) ? $settings['app_name'] : '';
                        $firebase_project_id = $this->data['firebase_project_id'];
                        $service_account_file = $this->data['service_account_file'];
                        if (isset($current_delivery_boys[0]['delivery_boy_id']) && !empty($current_delivery_boys[0]['delivery_boy_id'])) {
                            $user_res = fetch_details('users', "",  'fcm_id,username,email,mobile', "", "", "", "", "id", array_column($current_delivery_boys, "delivery_boy_id"));
                        } else {
                            $user_res = fetch_details('users', ['id' => $delivery_boy_id], 'fcm_id,username');
                        }

                        $fcm_ids = array();
                        //custom message
                        if (isset($user_res[0]) && !empty($user_res[0])) {
                            $current_delivery_boy = array_column($current_delivery_boys, "delivery_boy_id");
                            if ($_POST['status'] == 'received') {
                                $type = ['type' => "customer_order_received"];
                            } elseif ($_POST['status'] == 'processed') {
                                $type = ['type' => "customer_order_processed"];
                            } elseif ($_POST['status'] == 'shipped') {
                                $type = ['type' => "customer_order_shipped"];
                            } elseif ($_POST['status'] == 'delivered') {
                                $type = ['type' => "customer_order_delivered"];
                            } elseif ($_POST['status'] == 'cancelled') {
                                $type = ['type' => "customer_order_cancelled"];
                            } elseif ($_POST['status'] == 'returned') {
                                $type = ['type' => "customer_order_returned"];
                            }
                            $custom_notification = fetch_details('custom_notifications', $type, '');
                            $hashtag_cutomer_name = '< cutomer_name >';
                            $hashtag_order_id = '< order_item_id >';
                            $hashtag_application_name = '< application_name >';
                            $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                            $hashtag = html_entity_decode($string);
                            $data = str_replace(array($hashtag_cutomer_name, $hashtag_order_id, $hashtag_application_name), array($user_res[0]['username'], $order_items[0]['order_id'], $app_name), $hashtag);
                            $message = output_escaping(trim($data, '"'));
                            if (!empty($current_delivery_boy[0]) && count($current_delivery_boy) > 1) {
                                for ($i = 0; $i < count($current_delivery_boys); $i++) {
                                    $customer_msg = (!empty($custom_notification)) ? $message :  'Hello Dear ' . $user_res[$i]['username'] . 'Order status updated to' . $_POST['val'] . ' for order ID #' . $order_items[0]['order_id'] . ' please take note of it! Thank you. Regards ' . $app_name . '';
                                    $fcmMsg = array(
                                        'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Order status updated",
                                        'body' => $customer_msg,
                                        'type' => "order",
                                        'order_id' => $order_items[0]['order_id'],
                                    );
                                    if (!empty($user_res[$i]['fcm_id'])) {
                                        $fcm_ids[0][] = $user_res[$i]['fcm_id'];
                                    }
                                    try {
                                        notify_event(
                                            $type['type'],
                                            ["delivery_boy" => [$user_res[0]['email']]],
                                            ["delivery_boy" => [$user_res[0]['mobile']]],
                                            ["orders.id" => $order_items[0]['order_id']]
                                        );
                                    } catch (\Throwable $th) {
                                    }
                                }
                                $message = 'Delivery Boy Updated.';
                                $delivery_boy_updated = 1;
                            } else {
                                if (isset($current_delivery_boys[0]['delivery_boy_id']) && $current_delivery_boys[0]['delivery_boy_id'] == $_POST['deliver_by']) {
                                    $customer_msg = (!empty($custom_notification)) ? $message :  'Hello Dear ' . $user_res[0]['username'] . 'Order status updated to' . $_POST['val'] . ' for order ID #' . $order_items[0]['order_id'] . '  please take note of it! Thank you. Regards ' . $app_name . '';
                                    $fcmMsg = array(
                                        'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Order status updated",
                                        'body' => $customer_msg,
                                        'type' => "order",
                                        'order_id' => $order_items[0]['order_id'],
                                    );
                                    try {
                                        notify_event(
                                            $type['type'],
                                            ["delivery_boy" => [$user_res[0]['email']]],
                                            ["delivery_boy" => [$user_res[0]['mobile']]],
                                            ["orders.id" => $order_items[0]['order_id']]
                                        );
                                    } catch (\Throwable $th) {
                                    }
                                    $message = 'Delivery Boy Updated';
                                    $delivery_boy_updated = 1;
                                } else {
                                    $custom_notification =  fetch_details('custom_notifications',  ['type' => "delivery_boy_order_deliver"], '');
                                    $customer_msg = (!empty($custom_notification)) ? $message : 'Hello Dear ' . $user_res[0]['username'] . 'you have new order to be deliver order ID #' . $order_items[0]['order_id'] . ' please take note of it! Thank you. Regards ' . $app_name . '';
                                    $fcmMsg = array(
                                        'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "You have new order to deliver",
                                        'body' =>  $customer_msg,
                                        'type' => "order",
                                        'order_id' => (string)$order_items[0]['order_id'],
                                    );
                                    try {
                                        notify_event(
                                            $type['type'],
                                            ["delivery_boy" => [$user_res[0]['email']]],
                                            ["delivery_boy" => [$user_res[0]['mobile']]],
                                            ["orders.id" => $order_items[0]['order_id']]
                                        );
                                    } catch (\Throwable $th) {
                                    }
                                    $message = 'Delivery Boy Updated.';
                                    $delivery_boy_updated = 1;
                                }
                                if (!empty($user_res[0]['fcm_id'])) {
                                    $fcm_ids[0][] = $user_res[0]['fcm_id'];
                                }
                            }
                        }
                        if (!empty($fcm_ids) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                            send_notification($fcmMsg, $fcm_ids, $fcmMsg);
                        }

                        if ($this->Order_model->update_order(['delivery_boy_id' => $delivery_boy_id], $order_itam_ids, false, 'order_items')) {
                            $delivery_error = false;
                        }
                    }
                }
            }

            $item_ids = implode(",", $order_itam_ids);

            if (isset($_POST['status']) && !empty($_POST['status']) && $_POST['status'] != '') {
                $res = validate_order_status($item_ids, $_POST['status']);


                if ($res['error']) {
                    $this->response['error'] = $delivery_boy_updated == 1 ? false : true;
                    $this->response['message'] = (isset($_POST['status']) && !empty($_POST['status'])) ? $message . $res['message'] :  $message;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }
            }

            if (!empty($order_items)) {
                for ($j = 0; $j < count($order_items); $j++) {
                    $order_item_id = $order_items[$j]['id'];
                    /* velidate bank transfer method status */
                    $order_method = fetch_details('orders', ['id' => $order_items[$j]['order_id']], 'payment_method');
                    if ($order_method[0]['payment_method'] == 'bank_transfer') {
                        $bank_receipt = fetch_details('order_bank_transfer', ['order_id' => $order_items[$j]['order_id']]);
                        $transaction_status = fetch_details('transactions', ['order_id' => $order_items[$j]['order_id']], 'status');
                        if (empty($bank_receipt) || strtolower($transaction_status[0]['status']) != 'success' || $bank_receipt[0]['status'] == "0" || $bank_receipt[0]['status'] == "1") {
                            $this->response['error'] = true;
                            $this->response['message'] = "Order item status can not update, Bank verification is remain from transactions for this order.";
                            $this->response['csrfName'] = $this->security->get_csrf_token_name();
                            $this->response['csrfHash'] = $this->security->get_csrf_hash();
                            $this->response['data'] = array();
                            print_r(json_encode($this->response));
                            return false;
                        }
                    }

                    // processing order items
                    $order_item_res = $this->db->select(' * , (Select count(id) from order_items where order_id = oi.order_id ) as order_counter ,(Select count(active_status) from order_items where active_status ="cancelled" and order_id = oi.order_id ) as order_cancel_counter , (Select count(active_status) from order_items where active_status ="returned" and order_id = oi.order_id ) as order_return_counter,(Select count(active_status) from order_items where active_status ="delivered" and order_id = oi.order_id ) as order_delivered_counter , (Select count(active_status) from order_items where active_status ="processed" and order_id = oi.order_id ) as order_processed_counter , (Select count(active_status) from order_items where active_status ="shipped" and order_id = oi.order_id ) as order_shipped_counter , (Select status from orders where id = oi.order_id ) as order_status ')
                        ->where(['id' => $order_item_id])
                        ->get('order_items oi')->result_array();

                    if ($this->Order_model->update_order(['status' => $_POST['status']], ['id' => $order_item_res[0]['id']], true, 'order_items')) {
                        $this->Order_model->update_order(['active_status' => $_POST['status']], ['id' => $order_item_res[0]['id']], false, 'order_items');
                        process_refund($order_item_res[0]['id'], $_POST['status'], 'order_items');
                        if (trim($_POST['status']) == 'cancelled' || trim($_POST['status']) == 'returned') {
                            $data = fetch_details('order_items', ['id' => $order_item_id], 'product_variant_id,quantity');
                            update_stock($data[0]['product_variant_id'], $data[0]['quantity'], 'plus');
                        }
                        if (($order_item_res[0]['order_counter'] == intval($order_item_res[0]['order_cancel_counter']) + 1 && $_POST['status'] == 'cancelled') ||  ($order_item_res[0]['order_counter'] == intval($order_item_res[0]['order_return_counter']) + 1 && $_POST['status'] == 'returned') || ($order_item_res[0]['order_counter'] == intval($order_item_res[0]['order_delivered_counter']) + 1 && $_POST['status'] == 'delivered') || ($order_item_res[0]['order_counter'] == intval($order_item_res[0]['order_processed_counter']) + 1 && $_POST['status'] == 'processed') || ($order_item_res[0]['order_counter'] == intval($order_item_res[0]['order_shipped_counter']) + 1 && $_POST['status'] == 'shipped')) {
                            /* process the refer and earn */
                            $user = fetch_details('orders', ['id' => $order_item_res[0]['order_id']], 'user_id');
                            $user_id = $user[0]['user_id'];
                            $response = process_referral_bonus($user_id, $order_item_res[0]['order_id'], $_POST['status']);
                        }
                    }
                    //Update login id in order_item table
                    update_details(['updated_by' => $order_items[0]['seller_id']], ['order_id' => $order_item_res[0]['order_id'], 'seller_id' => $order_item_res[0]['seller_id']], 'order_items');
                }
                $settings = get_settings('system_settings', true);
                $app_name = isset($settings['app_name']) && !empty($settings['app_name']) ? $settings['app_name'] : '';
                $user_res = fetch_details('users', ['id' => $user_id], 'username,fcm_id,mobile,email');
                $fcm_ids = array();
                //custom message
                if (!empty($user_res[0]['fcm_id'])) {
                    if ($_POST['status'] == 'received') {
                        $type = ['type' => "customer_order_received"];
                    } elseif ($_POST['status'] == 'processed') {
                        $type = ['type' => "customer_order_processed"];
                    } elseif ($_POST['status'] == 'shipped') {
                        $type = ['type' => "customer_order_shipped"];
                    } elseif ($_POST['status'] == 'delivered') {
                        $type = ['type' => "customer_order_delivered"];
                    } elseif ($_POST['status'] == 'cancelled') {
                        $type = ['type' => "customer_order_cancelled"];
                    } elseif ($_POST['status'] == 'returned') {
                        $type = ['type' => "customer_order_returned"];
                    }
                    $custom_notification = fetch_details('custom_notifications', $type, '');
                    $hashtag_cutomer_name = '< cutomer_name >';
                    $hashtag_order_id = '< order_item_id >';
                    $hashtag_application_name = '< application_name >';
                    $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                    $hashtag = html_entity_decode($string);
                    $data = str_replace(array($hashtag_cutomer_name, $hashtag_order_id, $hashtag_application_name), array($user_res[0]['username'], $order_items[0]['order_id'], $app_name), $hashtag);
                    $message = output_escaping(trim($data, '"'));
                    $customer_msg = (!empty($custom_notification)) ? $message :  'Hello Dear ' . $user_res[0]['username'] . 'Order status updated to' . $_POST['val'] . ' for order ID #' . $order_items[0]['order_id'] . ' please take note of it! Thank you. Regards ' . $app_name . '';
                    $fcmMsg = array(
                        'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Order status updated",
                        'body' => $customer_msg,
                        'type' => "order"
                    );
                    notify_event(
                        $type['type'],
                        ["customer" => [$user_res[0]['email']]],
                        ["customer" => [$user_res[0]['mobile']]],
                        ["orders.id" => $order_items[0]['order_id']]
                    );

                    $fcm_ids[0][] = $user_res[0]['fcm_id'];
                    if (isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                        send_notification($fcmMsg, $fcm_ids, $fcmMsg);
                    }
                }

                $this->response['error'] = false;
                $this->response['message'] = 'Status Updated Successfully';
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
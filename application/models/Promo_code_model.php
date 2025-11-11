<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Promo_code_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper', 'sms_helper']);
    }

    public function get_promo_code_list($offset = 0, $limit = 10, $sort = 'id', $order = 'ASC')
    {
        $multipleWhere = '';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['p.`id`' => $search, 'p.`promo_code`' => $search, 'p.`message`' => $search, 'p.`start_date`' => $search, 'p.`end_date`' => $search, 'p.`discount`' => $search, 'p.`repeat_usage`' => $search, 'p.`max_discount_amount`' => $search];
        }

        $count_res = $this->db->select(' COUNT(p.id) as `total` ');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->or_where($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $sc_count = $count_res->get('promo_codes p')->result_array();

        foreach ($sc_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' p.`id` as id , p.`promo_code`, p.`image` , p.`message` , p.`start_date` , p.`end_date`, p.`discount` , p.`repeat_usage` ,p.`minimum_order_amount` ,p.`no_of_users` ,p.`discount_type` , p.`max_discount_amount`, p.`no_of_repeat_usage` , p.`status`,p.`is_cashback`,p.`list_promocode`');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->or_like($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $sc_search_res = $search_res->order_by($sort, "desc")->limit($limit, $offset)->get('promo_codes p')->result_array();

        $bulkData = array();
        $bulkData['total'] = count($sc_search_res);
        $rows = array();
        $tempRow = array();

        foreach ($sc_search_res as $row) {
            $row = output_escaping($row);

            $operate = '<a href="javascript:void(0)" class="view_btn btn btn-primary action-btn btn-xs mr-1 mb-1 ml-1"  title="view" data-id="' . $row['id'] . '" data-url="admin/promo_code" ><i class="fa fa-eye" ></i></a>';
            $operate .= ' <a href="' . base_url('admin/promo_code/manage_promo_code?edit_id=' . $row['id']) . '" class="btn btn-success edit_promocode action-btn btn-xs ml-1 mr-1 mb-1"  title="Edit" data-id="' . $row['id'] . '" data-target="#add_promocode" data-toggle="modal"><i class="fa fa-pen"></i></a>';
            $operate .= '<a class="btn btn-danger action-btn btn-xs ml-1 mr-1 mb-1" href="javascript:void(0)" id="delete-promo-code" title="Delete" data-id="' . $row['id'] . '" ><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row['id'];
            $tempRow['promo_code'] = $row['promo_code'];
            $tempRow['message'] = $row['message'];
            $tempRow['start_date'] = $row['start_date'];
            $tempRow['end_date'] = $row['end_date'];
            $tempRow['discount'] = $row['discount'];
            $tempRow['repeat_usage'] = ($row['repeat_usage'] == '1') ? 'Allowed' : 'Not Allowed';
            $tempRow['min_order_amt'] = $row['minimum_order_amount'];
            $tempRow['no_of_users'] = $row['no_of_users'];
            $tempRow['discount_type'] = $row['discount_type'];
            $tempRow['max_discount_amt'] = $row['max_discount_amount'];
            $row['image'] = (isset($row['image']) && !empty($row['image'])) ? base_url() . $row['image'] :  base_url() . NO_IMAGE;
            $tempRow['image'] = '<div class="image-box-100"><a href=' . $row['image'] . ' data-toggle="lightbox" data-gallery="gallery"><img src=' . $row['image'] . ' class="rounded"></a></div>';
            $tempRow['no_of_repeat_usage'] = $row['no_of_repeat_usage'];
            if ($row['end_date'] < date('Y-m-d')) {
                $tempRow['status'] = '<span class="badge badge-danger" >Expired</span>';
            } else {
                $tempRow['status'] = '<span class="badge badge-success" >Active</span>';
            }
            $tempRow['is_cashback'] = ($row['is_cashback'] == '1') ? '<span class="badge badge-info" >ON</span>' : '<span class="badge badge-warning">OFF</span>';
            $tempRow['list_promocode'] = ($row['list_promocode'] == '1') ? '<span class="badge badge-primary" >SHOW</span>' : '<span class="badge badge-secondary">HIDDEN</span>';
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
    public function get_promo_codes($limit = "", $offset = '', $sort = 'u.id', $order = 'DESC', $search = NULL)
    {
        $multipleWhere = '';
        if (isset($search) and $search != '') {
            $multipleWhere = ['p.`id`' => $search, 'p.`promo_code`' => $search, 'p.`message`' => $search, 'p.`start_date`' => $search, 'p.`end_date`' => $search, 'p.`discount`' => $search, 'p.`repeat_usage`' => $search, 'p.`max_discount_amount`' => $search];
        }

        $count_res = $this->db->select(' COUNT(p.id) as `total` ');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->or_where($multipleWhere);
        }

        $where = "(CURDATE() between start_date AND end_date) and status = 1 and list_promocode = 1";
        $count_res->where($where);
        $sc_count = $count_res->get('promo_codes p')->result_array();

        foreach ($sc_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' p.`id` as id ,datediff(end_date, start_date ) as remaining_days, p.`promo_code`, p.`image` , p.`message` , p.`start_date` , p.`end_date`, p.`discount` , p.`repeat_usage` ,p.`minimum_order_amount` ,p.`no_of_users` ,p.`discount_type` , p.`max_discount_amount`, p.`no_of_repeat_usage` , p.`status`,p.`is_cashback`,p.`list_promocode`');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->or_like($multipleWhere);
        }
        $where = "(CURDATE() between start_date AND end_date) and status = 1 and list_promocode = 1";

        $search_res->where($where);

        $sc_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('promo_codes p')->result_array();

        $bulkData = array();
        $bulkData['error'] = (empty($sc_search_res)) ? true : false;
        $bulkData['message'] = (empty($sc_search_res)) ? 'Promo code(s) does not exist' : 'Promo code(s) retrieved successfully';
        $bulkData['total'] = (empty($sc_search_res)) ? 0 : $total;
        $rows = array();
        $tempRow = array();

        foreach ($sc_search_res as $row) {
            $row = output_escaping($row);
            $tempRow['id'] = $row['id'];
            $tempRow['promo_code'] = $row['promo_code'];
            $tempRow['message'] = $row['message'];
            $tempRow['start_date'] = $row['start_date'];
            $tempRow['end_date'] = $row['end_date'];
            $tempRow['discount'] = $row['discount'];
            $tempRow['repeat_usage'] = ($row['repeat_usage'] == '1') ? 'Allowed' : 'Not Allowed';
            $tempRow['min_order_amt'] = $row['minimum_order_amount'];
            $tempRow['no_of_users'] = $row['no_of_users'];
            $tempRow['discount_type'] = $row['discount_type'];
            $tempRow['max_discount_amt'] = $row['max_discount_amount'];
            $tempRow['image'] = (isset($row['image']) && !empty($row['image'])) ? base_url() . $row['image'] :  base_url() . NO_IMAGE;
            $tempRow['no_of_repeat_usage'] = $row['no_of_repeat_usage'];
            $tempRow['status'] = $row['status'];
            $tempRow['is_cashback'] = $row['is_cashback'];
            $tempRow['list_promocode'] = $row['list_promocode'];
            $tempRow['remaining_days'] =   $row['remaining_days'];
            $rows[] = $tempRow;
        }
        $bulkData['data'] = $rows;
        if (!empty($bulkData)) {
            return $bulkData;
        } else {
            return $bulkData = [];
        }
    }

    public function add_promo_code_details($data)
    {

        $data = escape_array($data);

        $promo_data = [
            'promo_code' => $data['promo_code'],
            'message' => $data['message'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'no_of_users' => $data['no_of_users'],
            'minimum_order_amount' => $data['minimum_order_amount'],
            'discount' => $data['discount'],
            'discount_type' => $data['discount_type'],
            'max_discount_amount' => ($data['discount_type'] == 'percentage') ? $data['max_discount_amount'] : $data['discount'],
            'repeat_usage' => $data['repeat_usage'],
            'status' => $data['status'],
            'image' => $data['image'],
            'is_cashback' => (isset($data['is_cashback']) && $data['is_cashback'] == 'on') ? '1' : '0',
            'list_promocode' => (isset($data['list_promocode']) && $data['list_promocode'] == 'on') ? '1' : '0'
        ];
        
        if ($data['repeat_usage'] == '1') {
            $promo_data['no_of_repeat_usage'] = $data['no_of_repeat_usage'];
        }
        if (isset($data['edit_promo_code']) && !empty($data['edit_promo_code'])) {
            $this->db->set($promo_data)->where('id', $data['edit_promo_code'])->update('promo_codes');
        } else {
            $this->db->insert('promo_codes', $promo_data);
        }
    }

    public function deactivate_expired_promo_codes()
    {
        $this->db->where('end_date <', date('Y-m-d'));
        $this->db->set('status', '0');
        $this->db->update('promo_codes'); // Replace with your actual table name
    }

    function settle_cashback_discount()
    {
        $return = false;
        $date = date('Y-m-d');
        $settings = get_settings('system_settings', true);
        $returnable_where = "oi.active_status='delivered' AND o.promo_code != '' AND o.promo_discount <= 0 GROUP BY `o`.`id` HAVING date = '" . $date . "'";
        $returnable_data = $this->db->select("o.id,o.date_added,o.total,o.final_total,o.promo_code,o.user_id,p.is_returnable,(date_format(o.date_added,'%Y-%m-%d')) as date ")
            ->join('order_items oi', 'oi.order_id=o.id', 'left')
            ->join('product_variants pv', 'oi.product_variant_id=pv.id', 'left')
            ->join('products p', 'p.id=pv.product_id', 'left')
            ->where($returnable_where)
            ->get('orders o')->result_array();
        foreach ($returnable_data as $result) {
            $res =  $this->db->select('oi.id as item_id, oi.order_id,p.is_returnable')
                ->join('product_variants pv', 'oi.product_variant_id = pv.id', 'left')
                ->join('products p', 'p.id = pv.product_id')
                ->where("oi.order_id", $result['id'])
                ->where_in('p.is_returnable', [0, 1])
                ->get('order_items oi')->result_array();
            $returnable_status = array_column($res, 'is_returnable');
            if (in_array("1", $returnable_status)) {
                $return = true;
            } else {
                $return = false;
            }
        }
        if ($return == true) {
            $select = "DATE_ADD(date_format(o.date_added,'%Y-%m-%d'), INTERVAL " . $settings['max_product_return_days'] . " DAY) as date";
        } elseif ($return == false) {
            $select = "(date_format(o.date_added,'%Y-%m-%d')) as date";
        } else {
            $select = "(date_format(o.date_added,'%Y-%m-%d')) as date";
        }
        $where = "oi.active_status='delivered' AND o.promo_code != '' AND o.promo_discount <= 0 GROUP BY `o`.`id` HAVING date = '" . $date . "'";
        $data = $this->db->select("o.id,o.date_added,o.total,o.final_total,o.promo_code,o.user_id,$select ")
            ->join('order_items oi', 'oi.order_id=o.id', 'left')
            ->where($where)
            ->get('orders o')->result_array();
        $wallet_updated = false;
        if (!empty($data)) {
            foreach ($data as $row) {
                $promo_code = $row['promo_code'];
                $user_id = $row['user_id'];
                $final_total = $row['final_total'];

                $res = validate_promo_code($promo_code, $user_id, $final_total);
                $response = update_wallet_balance('credit', $user_id, $res['data'][0]['final_discount'], 'Discounted Amount Credited for Order Item ID  : ' . $row['id']);

                if ($response['error'] == false && $response['error'] == '') {
                    update_details(['total_payable' => $res['data'][0]['final_total'], 'final_total' => $res['data'][0]['final_total'], 'promo_discount' => $res['data'][0]['final_discount']], ['id' => $row['id']], 'orders');
                    $wallet_updated = true;
                    $response_data['error'] = false;
                    $response_data['message'] = 'Discount Added Successfully...';
                } else {
                    $wallet_updated = false;
                    $response_data['error'] =  true;
                    $response_data['message'] =  'Discount not Added';
                }
            }
            if ($wallet_updated == true) {
                $user_ids = array_values(array_unique(array_column($data, "user_id")));
                foreach ($user_ids as $user) {
                    $settings = get_settings('system_settings', true);
                    $firebase_project_id = get_settings('firebase_project_id');
                    $service_account_file = get_settings('service_account_file');
                    //custom message
                    $app_name = isset($settings['app_name']) && !empty($settings['app_name']) ? $settings['app_name'] : '';
                    $user_res = fetch_details('users', ['id' => $user], 'username,fcm_id,email,mobile,platform_type');
                    $custom_notification =  fetch_details('custom_notifications', ['type' => "settle_cashback_discount"], '');
                    $hashtag_cutomer_name = '< cutomer_name >';
                    $hashtag_application_name = '< application_name >';
                    $string = json_encode(isset($custom_notification[0]['message']) ? $custom_notification[0]['message'] : '', JSON_UNESCAPED_UNICODE);
                    $hashtag = html_entity_decode($string);
                    $data = str_replace(array($hashtag_cutomer_name, $hashtag_application_name), array($user_res[0]['username'], $app_name), $hashtag);
                    $message = output_escaping(trim($data, '"'));
                    $customer_title = (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Discounted Amount Credited";
                    $customer_msg = (!empty($custom_notification)) ? $message :  'Hello Dear ' . $user_res[0]['username'] . 'Discounted Amount Credited, which orders are delivered. Please take note of it! Regards' . $app_name . '';
                    send_mail($user_res[0]['email'], $customer_title,  $customer_msg);
                    (notify_event(
                        "settle_cashback_discount",
                        ["customer" => [$user_res[0]['email']]],
                        ["customer" => [$user_res[0]['mobile']]],
                        ["users.mobile" => $user_res[0]['mobile']]
                    ));
                    $fcm_ids = array();
                    if (!empty($user_res[0]['fcm_id']) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                        $fcmMsg = array(
                            'title' => $customer_title,
                            'body' => $customer_msg,
                            'type' => "Discounted",
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
                }
            } else {
                $response_data['error'] =  true;
                $response_data['message'] =  'Discounted not Added';
            }
        } else {
            $response_data['error'] =  true;
            $response_data['message'] =  'Orders Not Found';
        }
        print_r(json_encode($response_data));
    }
    function settle_referal_cashback_discount()
    {
        $return = false;
        $date = date('Y-m-d');
        $settings = get_settings('system_settings', true);
        $max_retun_day = $settings['max_product_return_days'];

        $is_refer_earn_on = $settings['is_refer_earn_on'];
        $min_refer_earn_order_amount = $settings['min_refer_earn_order_amount'];
        $refer_earn_bonus_for_user = $settings['refer_earn_bonus_for_user'];
        $refer_earn_method_for_user = $settings['refer_earn_method_for_user'];
        $refer_earn_method_for_referal = $settings['refer_earn_method_for_referal'];
        $max_refer_earn_amount_for_user = $settings['max_refer_earn_amount_for_user'];
        $refer_earn_amount_for_referal = $settings['refer_earn_amount_for_referal'];

        if ($is_refer_earn_on == '1') {
            // for referal cashback discount 
            $this->db->select('*');
            $this->db->from('refer_and_earn');
            $this->db->where('is_user_cashback_settled', 0);
            $user_query = $this->db->get();
            $user_ids = array_column($user_query->result_array(), 'user_id'); // Extract user IDs into an array
            $referal_ids = array_column($user_query->result_array(), 'referal_id');

            if (!empty($user_ids)) {

                // Subquery to get the first order_id for each user
                $this->db->select('user_id, MIN(id) as first_order_id');
                $this->db->from('orders');
                $this->db->where_in('user_id', $user_ids);
                $this->db->where('total_payable >', $min_refer_earn_order_amount);
                $this->db->group_by('user_id');
                $subquery = $this->db->get_compiled_select();

                // Main query to get order and order items using the first order_id from the subquery
                $this->db->select('o.*, oi.*, pv.id as product_variant_id, pv.product_id as product_id, p.id, p.is_returnable,
                 DATE_ADD(date_format(o.date_added,"%Y-%m-%d"), INTERVAL ' . $max_retun_day . ' DAY) as date');
                $this->db->from('orders o');
                $this->db->join('order_items oi', 'o.id = oi.order_id', 'inner');
                $this->db->join('product_variants pv', 'oi.product_variant_id = pv.id', 'inner');
                $this->db->join('products p', 'pv.product_id = p.id', 'inner');
                $this->db->join("($subquery) as first_orders", 'o.id = first_orders.first_order_id', 'inner');

                // Filter to get only order items with status 'delivered'
                $this->db->where('oi.active_status', 'delivered');

                // Filter to get only products that are returnable
                $this->db->where('p.is_returnable', 1);
                // Execute the query and get the result as an array
                $order_query = $this->db->get();
                $first_delivered_returnable_orders = $order_query->result_array();

                if (!empty($first_delivered_returnable_orders)) {
                    // Iterate over each order
                    foreach ($first_delivered_returnable_orders as $order) {
                        if (in_array($order['user_id'], $user_ids)) {

                            // Calculate the return amount for each product
                            if ($refer_earn_method_for_user == 'amount') {
                                $return_amount = $refer_earn_bonus_for_user;
                                if ($return_amount > $max_refer_earn_amount_for_user) {
                                    $return_amount = update_wallet_balance('credit', $order['user_id'], $max_refer_earn_amount_for_user, 'Referal amount credited successfully.');

                                    update_details(['is_user_cashback_settled' => 1], ['is_user_cashback_settled' => 0, 'user_id' => $order['user_id']], 'refer_and_earn');
                                    $wallet_updated = true;
                                    $response_data['error'] = false;
                                    $response_data['message'] = 'Referal amount credited successfully.';
                                } else {
                                    $return_amount = update_wallet_balance('credit', $order['user_id'], $return_amount, 'Referal amount credited successfully.');

                                    update_details(['is_user_cashback_settled' => 1], ['is_user_cashback_settled' => 0, 'user_id' => $order['user_id']], 'refer_and_earn');
                                    $wallet_updated = true;
                                    $response_data['error'] = false;
                                    $response_data['message'] = 'Referal amount credited successfully.';
                                }
                            } else if ($refer_earn_method_for_user == 'percentage') {
                                $return_amount = $order['total_payable'] * ($refer_earn_bonus_for_user / 100);
                                if ($return_amount > $max_refer_earn_amount_for_user) {
                                    $return_amount = update_wallet_balance('credit', $order['user_id'], $max_refer_earn_amount_for_user, 'Referal amount credited successfully.');

                                    update_details(['is_user_cashback_settled' => 1], ['is_user_cashback_settled' => 0, 'user_id' => $order['user_id']], 'refer_and_earn');
                                    $wallet_updated = true;
                                    $response_data['error'] = false;
                                    $response_data['message'] = 'Referal amount credited successfully.';
                                } else {
                                    $return_amount = update_wallet_balance('credit', $order['user_id'], $return_amount, 'Referal amount credited successfully.');

                                    update_details(['is_user_cashback_settled' => 1], ['is_user_cashback_settled' => 0, 'user_id' => $order['user_id']], 'refer_and_earn');
                                    $wallet_updated = true;
                                    $response_data['error'] = false;
                                    $response_data['message'] = 'Referal amount credited successfully.';
                                }
                            }
                        }
                    }
                } else {
                    $response_data['error'] =  true;
                    $response_data['message'] =  'Orders not found for settle referal amount.';
                }
            } else {
                $response_data['error'] =  true;
                $response_data['message'] =  'Users not found for settle referal amount.';
            }

            print_r(json_encode($response_data));
        }
    }
    function settle_referal_cashback_discount_for_referal()
    {
        $return = false;
        $date = date('Y-m-d');
        $settings = get_settings('system_settings', true);
        $max_retun_day = $settings['max_product_return_days'];

        $is_refer_earn_on = $settings['is_refer_earn_on'];
        $min_refer_earn_order_amount = $settings['min_refer_earn_order_amount'];
        $refer_earn_bonus = $settings['refer_earn_bonus'];
        $refer_earn_method_for_user = $settings['refer_earn_method_for_user'];
        $refer_earn_method_for_referal = $settings['refer_earn_method_for_referal'];
        $max_refer_earn_amount_for_user = $settings['max_refer_earn_amount_for_user'];
        $refer_earn_amount_for_referal = $settings['refer_earn_bonus_for_referal'];

        if ($is_refer_earn_on == '1') {
            // for referal cashback discount 
            $this->db->select('*');
            $this->db->from('refer_and_earn');
            $this->db->where('is_reffral_settled', 0);
            $user_query = $this->db->get();
            $user_ids = array_column($user_query->result_array(), 'user_id'); // Extract user IDs into an array
            $referal_ids = array_column($user_query->result_array(), 'referal_id');

            if (!empty($user_ids)) {

                // Subquery to get the first order_id for each user
                $this->db->select('user_id, MIN(id) as first_order_id');
                $this->db->from('orders');
                $this->db->where_in('user_id', $user_ids);
                $this->db->where('total_payable >', $min_refer_earn_order_amount);
                $this->db->group_by('user_id');
                $subquery = $this->db->get_compiled_select();

                // Main query to get order and order items using the first order_id from the subquery
                $this->db->select('o.*, oi.*, pv.id as product_variant_id, pv.product_id as product_id, p.id, p.is_returnable,
                 DATE_ADD(date_format(o.date_added,"%Y-%m-%d"), INTERVAL ' . $max_retun_day . ' DAY) as date');
                $this->db->from('orders o');
                $this->db->join('order_items oi', 'o.id = oi.order_id', 'inner');
                $this->db->join('product_variants pv', 'oi.product_variant_id = pv.id', 'inner');
                $this->db->join('products p', 'pv.product_id = p.id', 'inner');
                $this->db->join("($subquery) as first_orders", 'o.id = first_orders.first_order_id', 'inner');

                // Filter to get only order items with status 'delivered'
                $this->db->where('oi.active_status', 'delivered');

                // Filter to get only products that are returnable
                $this->db->where('p.is_returnable', 1);
                // Execute the query and get the result as an array
                $order_query = $this->db->get();
                $first_delivered_returnable_orders = $order_query->result_array();

                if (!empty($first_delivered_returnable_orders)) {
                    // Calculate the return amount for each product
                    if ($refer_earn_method_for_referal == 'amount') {
                        foreach ($referal_ids as $referal_id) {

                            $return_amount = update_wallet_balance('credit', $referal_id, $refer_earn_amount_for_referal, 'Referal amount credited successfully.');

                            if ($return_amount['error'] == false && $return_amount['error'] == '') {
                                update_details(['is_reffral_settled' => 1], ['is_reffral_settled' => 0, 'referal_id' => $referal_id], 'refer_and_earn');
                                $wallet_updated = true;
                                $response_data['error'] = false;
                                $response_data['message'] = 'Referal amount credited successfully.';
                            } else {
                                $wallet_updated = false;
                                $response_data['error'] =  true;
                                $response_data['message'] =  'Discount not Added';
                            }
                        }
                    }
                } else {
                    $response_data['error'] =  true;
                    $response_data['message'] =  'Orders not found for settle referal amount.';
                }
            } else {
                $response_data['error'] =  true;
                $response_data['message'] =  'Users not found for settle referal amount.';
            }

            print_r(json_encode($response_data));
        }
    }
}

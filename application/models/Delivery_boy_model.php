<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Delivery_boy_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    function update_delivery_boy($data)
    {
        $data = escape_array($data);
        $bonus = ($data['bonus_type'] == 'fixed_amount_per_order_item') ? $data['bonus_amount'] : $data['bonus_percentage'];
        $delivery_boy_data = [
            'username' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'address' => $data['address'],
            'bonus_type' => $data['bonus_type'],
            'bonus' => $bonus,
            'serviceable_zipcodes' => $data['serviceable_zipcodes'],
            'serviceable_cities' => $data['serviceable_cities'],
            'driving_license' => $data['driving_license'],
            'status' => $data['status'],
        ];
        $this->db->set($delivery_boy_data)->where('id', $data['edit_delivery_boy'])->update('users');
    }

    function get_delivery_boys_list($get_delivery_boy_status = "")
    {
        $offset = 0;
        $limit = 10;
        $sort = 'u.id';
        $order = 'ASC';
        $multipleWhere = '';
        $where = ['u.active' => 1];

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "u.id";
            } else if ($_GET['sort'] == 'date') {
                $sort = 'created_at';
            } else {
                $sort = $_GET['sort'];
            }


        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['u.`id`' => $search, 'u.`username`' => $search, 'u.`email`' => $search, 'u.`mobile`' => $search, 'u.`address`' => $search, 'u.`balance`' => $search];
        }

        $count_res = $this->db->select(' COUNT(u.id) as `total` ')->join('users_groups ug', ' ug.user_id = u.id ');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_start();
            $count_res->or_like($multipleWhere);
            $count_res->group_end();
        }
        if (isset($where) && !empty($where)) {
            $where['ug.group_id'] = '3';
            $count_res->where($where);
        }
        if ($get_delivery_boy_status == "approved") {
            $count_res->where('u.status', '1');
        }
        if ($get_delivery_boy_status == "not_approved") {
            $count_res->where('u.status', '0');
        }

        $offer_count = $count_res->get('users u')->result_array();

        foreach ($offer_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' u.* ')->join('users_groups ug', ' ug.user_id = u.id ');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_start();
            $search_res->or_like($multipleWhere);
            $search_res->group_end();
        }
        if (isset($where) && !empty($where)) {
            $where['ug.group_id'] = '3';
            $search_res->where($where);
        }
        if ($get_delivery_boy_status == "approved") {
            $search_res->where('u.status', '1');
        }
        if ($get_delivery_boy_status == "not_approved") {
            $search_res->where('u.status', '0');
        }

        $offer_search_res = $search_res->order_by($sort, "asc")->limit($limit, $offset)->get('users u')->result_array();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($offer_search_res as $row) {
            $row = output_escaping($row);
            $operate = '<a href="javascript:void(0)" class="edit_btn btn action-btn btn-primary btn-xs mr-1 ml-1 mb-1" title="Edit" data-id="' . $row['id'] . '" data-url="admin/delivery_boys/"><i class="fa fa-pen"></i></a>';
            $operate .= '<a  href="javascript:void(0)" class="btn btn-danger action-btn btn-xs mr-1 mb-1 ml-1" title="Delete" id="delete-delivery-boys"  data-id="' . $row['id'] . '" ><i class="fa fa-trash"></i></a>';
            $operate .= '<a href="javascript:void(0)" class=" fund_transfer action-btn btn btn-info btn-xs mr-1 mb-1 ml-1" title="Fund Transfer" data-target="#fund_transfer_delivery_boy"   data-toggle="modal" data-id="' . $row['id'] . '" ><i class="fa fa-arrow-alt-circle-right"></i></a>';

            $tempRow['id'] = $row['id'];
            $tempRow['name'] = $row['username'];

            if (isset($row['email']) && !empty($row['email']) && $row['email'] != "" && $row['email'] != " ") {
                $tempRow['email'] = (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($row['email']) - 3) . substr($row['email'], -3) : ucfirst($row['email']);
            } else {
                $tempRow['email'] = "";
            }
            if (isset($row['mobile']) && !empty($row['mobile']) && $row['mobile'] != "" && $row['mobile'] != " ") {
                $tempRow['mobile'] =  (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($row['mobile']) - 3) . substr($row['mobile'], -3) : $row['mobile'];
            } else {
                $tempRow['mobile'] = "";
            }

            // delivery boy status
            if ($row['status'] == 0) {
                $tempRow['status'] = "<label class='badge badge-warning'>Not-Approved</label>";
            } else if ($row['status'] == 1) {
                $tempRow['status'] = "<label class='badge badge-success'>Approved</label>";
            }
            // delivery boy status
            if ($row['bonus_type'] == 'fixed_amount_per_order_item') {
                $tempRow['bonus_type'] = "Fixed amount per Parcel";
            } else if ($row['bonus_type'] == 'percentage_per_order_item') {
                $tempRow['bonus_type'] = "Percentage per Parcel";
            }

            $tempRow['address'] = $row['address'];
            $tempRow['bonus'] = $row['bonus'];
            $tempRow['balance'] = number_format($row['balance'], 2);
            $tempRow['cash_received'] = $row['cash_received'];
            $tempRow['balance'] =  $row['balance'] == null || $row['balance'] == 0 || empty($row['balance']) ? "0" : number_format($row['balance'], 2);
            $tempRow['date'] = date('d-m-Y', strtotime($row['created_at']));
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    function update_balance($amount, $delivery_boy_id, $action)
    {
        /**
         * @param
         * action = deduct / add
         */

        if ($action == "add") {
            $this->db->set('balance', 'balance+' . $amount, FALSE);
        } elseif ($action == "deduct") {
            $this->db->set('balance', 'balance-' . $amount, FALSE);
        }
        return $this->db->where('id', $delivery_boy_id)->update('users');
    }
    public function get_delivery_boys($id, $search, $offset, $limit, $sort, $order, $get_delivery_boy_status, $seller_id, $user_address_id = '')
    {

        $shipping_settings = get_settings('shipping_method', true);
        $system_settings = get_settings('system_settings', true);

        $multipleWhere = '';
        $where['ug.group_id'] =  3;
        if (!empty($search)) {
            $multipleWhere = [
                '`u.id`' => $search,
                '`u.username`' => $search,
                '`u.email`' => $search,
                '`u.mobile`' => $search,
                '`u.street`' => $search
            ];
        }

        if (!empty($id)) {
            $where['u.id'] = $id;
        }

        if (isset($system_settings['update_seller_flow']) && $system_settings['update_seller_flow'] == '1') {

            if (isset($seller_id) && !empty($seller_id)) {
                // Fetch seller's serviceable cities
                $seller_data = fetch_details('seller_data', ['user_id' => $seller_id], 'id,user_id,serviceable_zipcodes,serviceable_cities');
                $seller_serviceable_zipcodes = explode(',', $seller_data[0]['serviceable_zipcodes']);
                $seller_serviceable_cities = explode(',', $seller_data[0]['serviceable_cities']);

                if (isset($shipping_settings['city_wise_deliverability']) && $shipping_settings['city_wise_deliverability'] == 1) {

                    // Add filtering based on seller's serviceable cities
                    $this->db->group_start(); // Begin grouping conditions
                    foreach ($seller_serviceable_cities as $city) {
                        $this->db->or_like('u.serviceable_cities', $city);
                    }
                    $this->db->group_end(); // End grouping conditions
                }
                if (isset($shipping_settings['pincode_wise_deliverability']) && $shipping_settings['pincode_wise_deliverability'] == 1) {

                    // Add filtering based on seller's serviceable cities
                    $this->db->group_start(); // Begin grouping conditions
                    foreach ($seller_serviceable_zipcodes as $zipcode) {
                        $this->db->or_like('u.serviceable_zipcodes', $zipcode);
                    }
                    $this->db->group_end(); // End grouping conditions
                }
            }

            if (isset($user_address_id) && !empty($user_address_id)) {
                $address = fetch_details('addresses', ['id' => $user_address_id], 'id,pincode,city,city_id');
                $pincode = $address[0]['pincode'];
                $pincode_data = fetch_details('zipcodes', ['zipcode' => $pincode]);
                $pincode_id = $pincode_data[0]['id'];
                $user_city_id = $address[0]['city_id']; // Fetch city_id from user address

                if (isset($shipping_settings['city_wise_deliverability']) && $shipping_settings['city_wise_deliverability'] == 1) {
                    $this->db->group_start(); // Begin grouping conditions
                    $this->db->like('u.serviceable_cities', $user_city_id); // Check if city_id is serviceable
                    $this->db->group_end(); // End grouping conditions
                }

                if (isset($shipping_settings['pincode_wise_deliverability']) && $shipping_settings['pincode_wise_deliverability'] == 1) {
                    $this->db->group_start(); // Begin grouping conditions
                    $this->db->like('u.serviceable_zipcodes', $pincode_id); // Check if city_id is serviceable
                    $this->db->group_end(); // End grouping conditions
                }
            }
        }
        $count_res = $this->db->select('COUNT(u.id) as `total`');



        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_start();
            $count_res->or_like($multipleWhere);
            $count_res->group_end();
        }

        if ($get_delivery_boy_status == "approved") {
            $count_res->where('u.status', '1');
        }
        if ($get_delivery_boy_status == "not_approved") {
            $count_res->where('u.status', '0');
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $count_res->join('`users_groups` `ug`', '`u`.`id` = `ug`.`user_id`');

        $cat_count = $count_res->get('users u')->result_array();
        foreach ($cat_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select('u.id as user_id, u.username, u.email, u.mobile, u.balance, u.image, u.status, u.serviceable_zipcodes, u.serviceable_cities, u.active, u.created_at');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_start();
            $search_res->or_like($multipleWhere);
            $search_res->group_end();
        }

        if ($get_delivery_boy_status == "approved") {
            $search_res->where('u.status', '1');
        }
        if ($get_delivery_boy_status == "not_approved") {
            $search_res->where('u.status', '0');
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }
        if (isset($system_settings['update_seller_flow']) && $system_settings['update_seller_flow'] == '1') {
            if (isset($shipping_settings['pincode_wise_deliverability']) && $shipping_settings['pincode_wise_deliverability'] == 1) {

                if (isset($seller_serviceable_zipcodes)) {
                    $search_res->group_start();
                    foreach ($seller_serviceable_zipcodes as $zipcode) {
                        $search_res->or_like('u.serviceable_zipcodes', $zipcode);
                    }
                    $search_res->group_end();
                }
            }
            if (isset($shipping_settings['city_wise_deliverability']) && $shipping_settings['city_wise_deliverability'] == 1) {

                // Apply seller serviceable cities filter here too
                if (isset($seller_serviceable_cities)) {
                    $search_res->group_start();
                    foreach ($seller_serviceable_cities as $city) {
                        $search_res->or_like('u.serviceable_cities', $city);
                    }
                    $search_res->group_end();
                }
            }

            if (isset($user_address_id) && !empty($user_address_id)) {
                if (isset($shipping_settings['city_wise_deliverability']) && $shipping_settings['city_wise_deliverability'] == 1) {
                    $this->db->group_start(); // Begin grouping conditions
                    $this->db->like('u.serviceable_cities', $user_city_id); // Check if city_id is serviceable
                    $this->db->group_end(); // End grouping conditions
                }
                if (isset($shipping_settings['pincode_wise_deliverability']) && $shipping_settings['pincode_wise_deliverability'] == 1) {
                    $this->db->group_start(); // Begin grouping conditions
                    $this->db->like('u.serviceable_zipcodes', $pincode_id); // Check if city_id is serviceable
                    $this->db->group_end(); // End grouping conditions
                }
            }
        }
        $search_res->join('`users_groups` `ug`', '`u`.`id` = `ug`.`user_id`');

        $cat_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('users u')->result_array();

        $rows = array();
        $tempRow = array();
        $bulkData = array();
        $bulkData['error'] = (empty($cat_search_res)) ? true : false;
        $bulkData['message'] = (empty($cat_search_res)) ? 'Delivery(s) does not exist' : 'Delivery boys retrieved successfully';
        $bulkData['total'] = (empty($cat_search_res)) ? 0 : $total;
        if (!empty($cat_search_res)) {
            foreach ($cat_search_res as $row) {
                $row = output_escaping($row);
                $tempRow['id'] = $row['user_id'];
                $tempRow['name'] = $row['username'];
                $tempRow['mobile'] = $row['mobile'];
                $tempRow['email'] = $row['email'];
                $tempRow['balance'] = $row['balance'];
                $tempRow['image'] = isset($row['image']) && $row['image'] != '' ? base_url(USER_IMG_PATH . '/' . $row['image']) : '';
                if (empty($row['image']) || file_exists(FCPATH . USER_IMG_PATH . $row['image']) == FALSE) {
                    $tempRow['image'] = base_url() . NO_IMAGE;
                } else {
                    $tempRow['image'] = base_url() . USER_IMG_PATH . $row['image'];
                }
                $tempRow['status'] = $row['active'];
                $tempRow['date'] = date('d-m-Y', strtotime($row['created_at']));

                $rows[] = $tempRow;
            }
            $bulkData['data'] = $rows;
        } else {
            $bulkData['data'] = [];
        }
        print_r(json_encode($bulkData));
    }

    function get_cash_collection_list($user_id = '')
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = '';
        $where = [];

        if (isset($_GET['filter_date']) && $_GET['filter_date'] != NULL)
            $where = ['DATE(transactions.transaction_date)' => $_GET['filter_date']];

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
            $multipleWhere = ['`transactions.id`' => $search, '`transactions.amount`' => $search, '`transactions.date_created`' => $search, 'users.username' => $search, 'users.mobile' => $search, 'users.email' => $search, 'transactions.order_id' => $search, 'transactions.type' => $search, 'transactions.status' => $search];
        }
        if (isset($_GET['filter_d_boy']) && !empty($_GET['filter_d_boy']) && $_GET['filter_d_boy'] != NULL) {
            $where = ['users.id' => $_GET['filter_d_boy']];
        }
        if (isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
            $where = ['transactions.type' => $_GET['filter_status']];
        }
        if (!empty($user_id)) {
            $user_where = ['users.id' => $user_id];
        }


        $count_res = $this->db->select(' COUNT(transactions.id) as `total` ')->join('users', ' transactions.user_id = users.id', 'left')->where('transactions.status = 1');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $count_res->where(" DATE(transactions.transaction_date) >= DATE('" . $_GET['start_date'] . "') ");
            $count_res->where(" DATE(transactions.transaction_date) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($_GET['filter_d_boy']) && !empty($_GET['filter_d_boy']) && $_GET['filter_d_boy'] != NULL) {
            $count_res->where('users.id', $_GET['filter_d_boy']);
        }
        if (isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
            $count_res->where('transactions.type', $_GET['filter_status']);
        }


        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $count_res->or_like($multipleWhere);
            $this->db->group_End();
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        if (isset($user_where) && !empty($user_where)) {
            $count_res->where($user_where);
        }

        $txn_count = $count_res->get('transactions')->result_array();

        foreach ($txn_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' transactions.*,users.username as name,users.mobile,users.id as delivery_boy_id,users.cash_received');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $search_res->where(" DATE(transactions.transaction_date) >= DATE('" . $_GET['start_date'] . "') ");
            $search_res->where(" DATE(transactions.transaction_date) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($_GET['filter_d_boy']) && !empty($_GET['filter_d_boy']) && $_GET['filter_d_boy'] != NULL) {
            $search_res->where('users.id', $_GET['filter_d_boy']);
        }
        if (isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
            $search_res->where('transactions.type', $_GET['filter_status']);
        }

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $search_res->or_like($multipleWhere);
            $this->db->group_End();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }
        if (isset($user_where) && !empty($user_where)) {
            $search_res->where($user_where);
        }
        $search_res->join('users', ' transactions.user_id = users.id', 'left')->where('transactions.status = 1');
        $txn_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('transactions')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($txn_search_res as $row) {
            $row = output_escaping($row);

            if ((isset($row['type']) && $row['type'] == "delivery_boy_cash")) {
                $operate = '<a href="javascript:void(0)" class="edit_cash_collection_btn btn action-btn btn-primary btn-xs mr-1 ml-1 mb-1" title="Edit" data-id="' . $row['id'] . '" data-order-id="' . $row['order_id'] . '" data-amount="' . $row['amount'] . '" data-dboy-id="' . $row['delivery_boy_id'] . '"  data-toggle="modal" data-target="#cash_collection_model"><i class="fa fa-pen"></i></a>';
            } else {
                $operate = '';
            }


            $tempRow['id'] = $row['id'];
            $tempRow['name'] = $row['name'];
            $tempRow['mobile'] = $row['mobile'];
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['cash_received'] = $row['cash_received'];
            $tempRow['type'] = (isset($row['type']) && $row['type'] == "delivery_boy_cash") ? '<label class="badge badge-danger">Received</label>' : '<label class="badge badge-success">Collected</label>';
            $tempRow['amount'] = $row['amount'];
            $tempRow['message'] = $row['message'];
            $tempRow['txn_date'] =  date('d-m-Y', strtotime($row['transaction_date']));
            $tempRow['date'] =  date('d-m-Y', strtotime($row['date_created']));
            $tempRow['operate'] = $operate;


            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
    function get_deliveryboy_transactions($user_id = '')
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = '';
        $where = [];

        if (isset($_GET['filter_date']) && $_GET['filter_date'] != NULL)
            $where = ['DATE(transactions.transaction_date)' => $_GET['filter_date']];

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
            $multipleWhere = ['`transactions.id`' => $search, '`transactions.amount`' => $search, '`transactions.date_created`' => $search, 'users.username' => $search, 'users.mobile' => $search, 'users.email' => $search, 'transactions.order_id' => $search, 'transactions.type' => $search, 'transactions.status' => $search];
        }

        if (!empty($user_id)) {
            $user_where = ['users.id' => $user_id];
        }


        $count_res = $this->db->select(' COUNT(transactions.id) as `total` ')
            ->join('users', ' transactions.user_id = users.id', 'left')
            ->where('transactions.type', 'credit')
            ->where('transactions.status', 'success');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $count_res->where(" DATE(transactions.transaction_date) >= DATE('" . $_GET['start_date'] . "') ");
            $count_res->where(" DATE(transactions.transaction_date) <= DATE('" . $_GET['end_date'] . "') ");
        }


        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $count_res->or_like($multipleWhere);
            $this->db->group_End();
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        if (isset($user_where) && !empty($user_where)) {
            $count_res->where($user_where);
        }

        $txn_count = $count_res->get('transactions')->result_array();

        foreach ($txn_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' transactions.*,users.username as name,users.mobile,users.id as delivery_boy_id,users.cash_received');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $search_res->where(" DATE(transactions.transaction_date) >= DATE('" . $_GET['start_date'] . "') ");
            $search_res->where(" DATE(transactions.transaction_date) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $search_res->or_like($multipleWhere);
            $this->db->group_End();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }
        if (isset($user_where) && !empty($user_where)) {
            $search_res->where($user_where);
        }
        $search_res->join('users', ' transactions.user_id = users.id', 'left')->where('transactions.type', 'credit')->where('transactions.status', 'success');
        $txn_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('transactions')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($txn_search_res as $row) {
            $row = output_escaping($row);

            $tempRow['id'] = $row['id'];
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['amount'] = $row['amount'];
            $tempRow['message'] = $row['message'];
            $tempRow['name'] = $row['name'];
            $tempRow['mobile'] = $row['mobile'];
            $tempRow['txn_date'] =  date('d-m-Y', strtotime($row['transaction_date']));
            $tempRow['date'] =  date('d-m-Y', strtotime($row['date_created']));


            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    public function get_delivery_boy_cash_collection($limit = "", $offset = '', $sort = 'transactions.id', $order = 'DESC', $search = NULL, $filter = "")
    {

        $multipleWhere = '';

        if (isset($search) and $search != '') {
            $multipleWhere = ['`transactions.id`' => $search, '`transactions.amount`' => $search, '`transactions.order_id`' => $search, '`transactions.date_created`' => $search, 'users.username' => $search, 'users.mobile' => $search, 'users.email' => $search, 'transactions.transaction_type' => $search, 'transactions.status' => $search];
        }

        if (isset($filter['status']) && !empty($filter['status'])) {
            $where = ['transactions.type' => $filter['status']];
        }
        if (isset($filter['delivery_boy_id']) && !empty($filter['delivery_boy_id'])) {
            $user_where = ['users.id' => $filter['delivery_boy_id']];
        }

        $count_res = $this->db->select(' COUNT(transactions.id) as `total` ')->join('users', ' transactions.user_id = users.id', 'left')->where('transactions.status = 1');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $count_res->or_like($multipleWhere);
            $this->db->group_End();
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        if (isset($user_where) && !empty($user_where)) {
            $count_res->where($user_where);
        }

        $txn_count = $count_res->get('transactions')->result_array();

        foreach ($txn_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' transactions.*,users.username as name,users.mobile,users.cash_received');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $search_res->or_like($multipleWhere);
            $this->db->group_End();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }
        if (isset($user_where) && !empty($user_where)) {
            $search_res->where($user_where);
        }
        $search_res->join('users', ' transactions.user_id = users.id', 'left')->where('transactions.status = 1');
        $txn_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('transactions')->result_array();

        $bulkData = array();
        $bulkData = array();
        $bulkData['error'] = (empty($txn_search_res)) ? true : false;
        $bulkData['message'] = (empty($txn_search_res)) ? 'Cash collection does not exist' : 'Cash collection are retrieve successfully';
        $bulkData['total'] = (empty($txn_search_res)) ? 0 : $total;
        $rows = array();
        $tempRow = array();

        foreach ($txn_search_res as $row) {
            $row = output_escaping($row);
            $tempRow['id'] = $row['id'];
            $tempRow['name'] = $row['name'];
            $tempRow['mobile'] = $row['mobile'];
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['cash_received'] = $row['cash_received'];
            $tempRow['type'] = (isset($row['type']) && $row['type'] == "delivery_boy_cash") ? 'Received' : 'Collected';
            $tempRow['amount'] = $row['amount'];
            $tempRow['message'] = $row['message'];
            $tempRow['transaction_date'] = $row['transaction_date'];
            $tempRow['date'] =  date('d-m-Y', strtotime($row['date_created']));

            $rows[] = $tempRow;
        }
        $bulkData['data'] = $rows;
        return $bulkData;
    }
}

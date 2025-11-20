<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Shipping_company_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    function update_shipping_company($data)
    {
        // sanitize input (you already do this with escape_array above; keep it)
        $data = escape_array($data);
        // prefer 'serviceable_zipcodes' (controller sets this), fallback to 'assign_zipcode'
        $zipcodes = NULL;
        if (isset($data['serviceable_zipcodes']) && $data['serviceable_zipcodes'] !== '') {
            // could be array or comma string
            if (is_array($data['serviceable_zipcodes'])) {
                $zipcodes = implode(',', $data['serviceable_zipcodes']);
            } else {
                $zipcodes = $data['serviceable_zipcodes'];
            }
        } elseif (isset($data['assign_zipcode']) && $data['assign_zipcode'] !== '') {
            if (is_array($data['assign_zipcode'])) {
                $zipcodes = implode(',', $data['assign_zipcode']);
            } else {
                $zipcodes = $data['assign_zipcode'];
            }
        } else {
            $zipcodes = NULL;
        }

        // same for cities if you store them
        $cities = NULL;
        if (isset($data['serviceable_cities']) && $data['serviceable_cities'] !== '') {
            if (is_array($data['serviceable_cities'])) {
                $cities = implode(',', $data['serviceable_cities']);
            } else {
                $cities = $data['serviceable_cities'];
            }
        }

        $company_data = [
            'username' => isset($data['company_name']) ? $data['company_name'] : NULL,
            'email' => isset($data['email']) ? $data['email'] : NULL,
            'mobile' => isset($data['mobile']) ? $data['mobile'] : NULL,
            'address' => isset($data['address']) ? $data['address'] : NULL,
            'serviceable_zipcodes' => $zipcodes,
            'serviceable_cities' => $cities,
            'kyc_documents' => isset($data['kyc_documents']) ? $data['kyc_documents'] : NULL,
            'status' => isset($data['status']) ? $data['status'] : NULL,
        ];

        // remove keys with NULL if you don't want them to overwrite existing DB values
        foreach ($company_data as $k => $v) {
            if ($v === NULL) {
                unset($company_data[$k]);
            }
        }

        // update
        if (isset($data['edit_shipping_company']) && !empty($data['edit_shipping_company'])) {
            $this->db->set($company_data)->where('id', $data['edit_shipping_company'])->update('users');
            return $this->db->affected_rows() !== 0;
        }

        return false;
    }


    function get_shipping_companies_list($get_company_status = "")
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
            $where['ug.group_id'] = '6';
            $count_res->where($where);
        }
        if ($get_company_status == "approved") {
            $count_res->where('u.status', '1');
        }
        if ($get_company_status == "not_approved") {
            $count_res->where('u.status', '0');
        }

        $company_count = $count_res->get('users u')->result_array();

        foreach ($company_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' u.* ')->join('users_groups ug', ' ug.user_id = u.id ');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_start();
            $search_res->or_like($multipleWhere);
            $search_res->group_end();
        }
        if (isset($where) && !empty($where)) {
            $where['ug.group_id'] = '6';
            $search_res->where($where);
        }
        if ($get_company_status == "approved") {
            $search_res->where('u.status', '1');
        }
        if ($get_company_status == "not_approved") {
            $search_res->where('u.status', '0');
        }

        $company_search_res = $search_res->order_by($sort, "asc")->limit($limit, $offset)->get('users u')->result_array();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($company_search_res as $row) {
            $row = output_escaping($row);
            $operate = '<a href="javascript:void(0)" class="edit_btn btn action-btn btn-primary btn-xs mr-1 ml-1 mb-1" title="Edit" data-id="' . $row['id'] . '" data-url="admin/shipping_companies/"><i class="fa fa-pen"></i></a>';
            $operate .= '<a  href="javascript:void(0)" class="btn btn-danger action-btn btn-xs mr-1 mb-1 ml-1" title="Delete" id="delete-shipping-company"  data-id="' . $row['id'] . '" ><i class="fa fa-trash"></i></a>';
            $operate .= '<a href="javascript:void(0)" class=" fund_transfer action-btn btn btn-info btn-xs mr-1 mb-1 ml-1" title="Fund Transfer" data-target="#fund_transfer_shipping_company"   data-toggle="modal" data-id="' . $row['id'] . '" ><i class="fa fa-arrow-alt-circle-right"></i></a>';

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

            // Status
            if ($row['status'] == 0) {
                $tempRow['status'] = "<label class='badge badge-warning'>Not-Approved</label>";
            } else if ($row['status'] == 1) {
                $tempRow['status'] = "<label class='badge badge-success'>Approved</label>";
            }

            $tempRow['address'] = $row['address'];
            // $tempRow['balance'] =  $row['balance'] == null || $row['balance'] == 0 || empty($row['balance']) ? "0" : number_format($row['balance'], 2);
            $tempRow['cash_received'] = $row['cash_received'];
            $tempRow['date'] = date('d-m-Y', strtotime($row['created_at']));
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    function update_balance($amount, $company_id, $action)
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
        return $this->db->where('id', $company_id)->update('users');
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
        if (isset($_GET['filter_company']) && !empty($_GET['filter_company']) && $_GET['filter_company'] != NULL) {
            $where = ['users.id' => $_GET['filter_company']];
        }
        if (isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
            $where = ['transactions.type' => $_GET['filter_status']];
        }
        if (!empty($user_id)) {
            $user_where = ['users.id' => $user_id];
        }

        $count_res = $this->db->select(' COUNT(transactions.id) as `total` ')->join('users', ' transactions.user_id = users.id', 'left')->where('transactions.status = 1')->where('(transactions.type = "shipping_company_cash" OR transactions.type = "shipping_company_cash_collection")');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $count_res->where(" DATE(transactions.transaction_date) >= DATE('" . $_GET['start_date'] . "') ");
            $count_res->where(" DATE(transactions.transaction_date) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($_GET['filter_company']) && !empty($_GET['filter_company']) && $_GET['filter_company'] != NULL) {
            $count_res->where('users.id', $_GET['filter_company']);
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

        $search_res = $this->db->select(' transactions.*,users.username as name,users.mobile,users.id as shipping_company_id,users.cash_received');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $search_res->where(" DATE(transactions.transaction_date) >= DATE('" . $_GET['start_date'] . "') ");
            $search_res->where(" DATE(transactions.transaction_date) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($_GET['filter_company']) && !empty($_GET['filter_company']) && $_GET['filter_company'] != NULL) {
            $search_res->where('users.id', $_GET['filter_company']);
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
        $search_res->join('users', ' transactions.user_id = users.id', 'left')->where('transactions.status = 1')->where('(transactions.type = "shipping_company_cash" OR transactions.type = "shipping_company_cash_collection")');
        $txn_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('transactions')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($txn_search_res as $row) {
            $row = output_escaping($row);

            if ((isset($row['type']) && $row['type'] == "shipping_company_cash")) {
                $operate = '<a href="javascript:void(0)" class="edit_cash_collection_btn btn action-btn btn-primary btn-xs mr-1 ml-1 mb-1" title="Edit" data-id="' . $row['id'] . '" data-order-id="' . $row['order_id'] . '" data-amount="' . $row['amount'] . '" data-company-id="' . $row['shipping_company_id'] . '"  data-toggle="modal" data-target="#cash_collection_model"><i class="fa fa-pen"></i></a>';
            } else {
                $operate = '';
            }

            $tempRow['id'] = $row['id'];
            $tempRow['name'] = $row['name'];
            $tempRow['mobile'] = $row['mobile'];
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['cash_received'] = $row['cash_received'];
            $tempRow['type'] = (isset($row['type']) && $row['type'] == "shipping_company_cash") ? '<label class="badge badge-danger">Received</label>' : '<label class="badge badge-success">Collected</label>';
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

    function get_fund_transfers_list()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = '';
        $where = [];

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
            $multipleWhere = [
                '`transactions.id`' => $search,
                '`transactions.amount`' => $search,
                '`transactions.date_created`' => $search,
                'users.username' => $search,
                'users.mobile' => $search,
                'users.email' => $search,
                'transactions.type' => $search,
                'transactions.status' => $search
            ];
        }

        $count_res = $this->db->select(' COUNT(transactions.id) as `total` ')
            ->join('users', ' transactions.user_id = users.id', 'left')
            ->join('users_groups ug', 'ug.user_id = users.id', 'left')
            ->where('ug.group_id', '6')
            ->where('transactions.transaction_type', 'transaction')
            ->where_in('transactions.type', ['credit', 'debit']);

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $count_res->or_like($multipleWhere);
            $this->db->group_End();
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $txn_count = $count_res->get('transactions')->result_array();

        foreach ($txn_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' transactions.*, users.username as name, users.mobile, users.balance');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $search_res->or_like($multipleWhere);
            $this->db->group_End();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $search_res->join('users', ' transactions.user_id = users.id', 'left')
            ->join('users_groups ug', 'ug.user_id = users.id', 'left')
            ->where('ug.group_id', '6')
            ->where('transactions.transaction_type', 'transaction')
            ->where_in('transactions.type', ['credit', 'debit']);

        $txn_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('transactions')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($txn_search_res as $row) {
            $row = output_escaping($row);
            $tempRow['id'] = $row['id'];
            $tempRow['name'] = $row['name'];
            $tempRow['mobile'] = $row['mobile'];
            $tempRow['opening_balance'] = $row['balance'];

            if ($row['type'] == 'credit') {
                $tempRow['closing_balance'] = floatval($row['balance']) + floatval($row['amount']);
            } else {
                $tempRow['closing_balance'] = floatval($row['balance']) - floatval($row['amount']);
            }

            $tempRow['amount'] = $row['amount'];
            $tempRow['status'] = $row['type'] == 'credit'
                ? '<label class="badge badge-success">Credit</label>'
                : '<label class="badge badge-danger">Debit</label>';
            $tempRow['message'] = $row['message'];
            $tempRow['date_created'] = date('d-m-Y', strtotime($row['date_created']));

            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
}

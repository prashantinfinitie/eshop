<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Affiliate_transaction_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }


    function add_affiliate_wallet_transactions($data)
    {
        $data = escape_array($data);
        $trans_data = [
            'user_id' => $data['user_id'],
            'amount' => $data['amount'],
            'type' => strtolower($data['transaction_type']),
            'reference_type' => strtolower($data['reference_type']),
            'message' => $data['message'],
        ];
        $this->db->insert('affiliate_wallet_transactions', $trans_data);
    }

    function settle_affiliate_commission($is_date = TRUE)
    {
        $date = date('Y-m-d');
        $settings = get_settings('system_settings', true);

        // Step 1: Get eligible order items
        if ($is_date == TRUE) {
            $where = "oi.active_status = 'delivered' 
            AND oi.is_affiliate_commission_settled = 0 
            AND oi.affiliate_token != ''
            AND DATE_ADD(DATE_FORMAT(oi.date_added, '%Y-%m-%d'), INTERVAL {$settings['max_product_return_days']} DAY) = '{$date}'";
        } else {
            $where = "oi.active_status = 'delivered' AND oi.is_affiliate_commission_settled = 0 AND oi.affiliate_token != ''";
        }

        $order_items = $this->db->select('oi.id as order_item_id, oi.product_variant_id, oi.order_id, oi.sub_total, oi.affiliate_id, oi.affiliate_token, pv.product_id')
            ->join('product_variants pv', 'pv.id = oi.product_variant_id', 'left')
            ->where($where)
            ->get('order_items oi')->result_array();

        // echo $this->db->last_query();
        $wallet_updated = false;

        foreach ($order_items as $item) {



            if (!empty($item['affiliate_id']) && !empty($item['affiliate_token']))
                // Step 2: Get commission % from affiliate_tracking
                $affiliate = fetch_details(
                    'affiliate_tracking',
                    [
                        'product_id' => $item['product_id'],
                        'affiliate_id' => $item['affiliate_id'],
                        'token' => $item['affiliate_token'],
                    ],
                    'category_commission'
                );


            if (empty($affiliate)) {
                continue;
            }


            $commission_percent = floatval($affiliate[0]['category_commission']);
            $commission_amount = ($item['sub_total'] * $commission_percent) / 100;

            // Step 3: Credit to affiliate's wallet
            $msg = 'Affiliate Commission for Order Item ID: ' . $item['order_item_id'] . ' and Product ID :' . $item['product_id'];
            $response = update_affiliate_wallet_balance('credit', $item['affiliate_id'], $commission_amount, $item['product_id'], $msg, 'order', $item['sub_total'], $item['affiliate_token']);

            if (!$response['error']) {
                update_details(
                    ['is_affiliate_commission_settled' => 1],
                    ['id' => $item['order_item_id']],
                    'order_items'
                );
                $wallet_updated = true;
            }
        }


        $response_data = [
            'error' => $wallet_updated ? false : true,
            'message' => $wallet_updated ? 'Affiliate Commission Settled Successfully' : 'No affiliate commission settled',
        ];

        print_r(json_encode($response_data));
    }

    public function get_affiliate_commission_summary($affiliate_id)
    {
        $response = [
            'total_profit' => 0,
            'pending' => 0,
            'confirm' => 0,
            'requested' => 0,
            'paid' => 0,
        ];

        // Get total profit (sum of affiliate_commission_amount)
        $this->db->select_sum('affiliate_commission_amount');
        $this->db->where('affiliate_id', $affiliate_id);
        $query1 = $this->db->get('order_items')->row();
        $response['total_profit'] = $query1->affiliate_commission_amount ?? 0;

        // Get pending commission (where commission not settled)
        $this->db->select_sum('affiliate_commission_amount');
        $this->db->where('affiliate_id', $affiliate_id);
        $this->db->where('is_affiliate_commission_settled', 0); // assuming 0 means not settled
        $query2 = $this->db->get('order_items')->row();
        $response['pending'] = $query2->affiliate_commission_amount ?? 0;

        // Get confirm from affiliates table (preferred)
        $this->db->select('affiliate_wallet_balance');
        $this->db->where('user_id', $affiliate_id);
        $query3 = $this->db->get('affiliates')->row();
        $response['confirm'] = $query3->affiliate_wallet_balance ?? 0;

        // Get requested from affiliates table (preferred)
        $this->db->select_sum('amount_requested');
        $this->db->where('user_id', $affiliate_id);
        $this->db->where('payment_type', 'affiliate');
        $this->db->where('status', 0);
        $query3 = $this->db->get('payment_requests')->row();
        $total_requested = $query3->amount_requested;
        $response['requested'] = $total_requested ?? 0;

        // Get Paid from affiliates table (preferred)
        $this->db->select_sum('amount_requested');
        $this->db->where('user_id', $affiliate_id);
        $this->db->where('payment_type', 'affiliate');
        $this->db->where('status', 1);
        $query3 = $this->db->get('payment_requests')->row();
        $total_requested = $query3->amount_requested;
        $response['paid'] = $total_requested ?? 0;


        // echo $this->db->last_query();

        return $response;
    }

    function update_balance($amount, $affiliate_id, $action)
    {
        /**
         * @param
         * action = deduct / add
         */

        if ($action == "add") {
            $this->db->set('affiliate_wallet_balance', 'affiliate_wallet_balance+' . $amount, FALSE);
        } elseif ($action == "deduct") {
            $this->db->set('affiliate_wallet_balance', 'affiliate_wallet_balance-' . $amount, FALSE);
        }
        return $this->db->where('user_id', $affiliate_id)->update('affiliates');
    }

    function get_withdrawal_request_list($user_id = NULL)
    {
        $offset = 0;
        $limit = 10;
        $sort = 'pr.id';
        $order = 'DESC';
        $multipleWhere = '';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "pr.id";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['pr.`id`' => $search, 'u.`username`' => $search, 'u.`email`' => $search, 'u.`mobile`' => $search];
        }
        if (isset($user_id) && !empty($user_id)) {
            $where = ['pr.user_id' => $user_id];
        }

        $count_res = $this->db->select(' COUNT(pr.id) as `total` ')->join('users u', 'u.id=pr.user_id');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $count_res->or_like($multipleWhere);
            $this->db->group_End();
        }

        if (isset($user_id) && !empty($user_id)) {
            $where = ['pr.user_id' => $user_id];
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $request_count = $count_res->get('payment_requests pr')->result_array();

        foreach ($request_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->join('users u', 'u.id=pr.user_id');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $search_res->or_like($multipleWhere);
            $this->db->group_End();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $offer_search_res = $search_res->order_by($sort, "desc")->limit($limit, $offset)->select('u.username,pr.*')->get('payment_requests pr')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $currency_symbol = get_settings('currency');

        foreach ($offer_search_res as $row) {
            // print_R($row);
            $row = output_escaping($row);
            if (!isset($user_id) && empty($user_id)) {
                $operate = '<a href="javascript:void(0)" class="edit_request action-btn btn btn-success btn-xs mr-1 mb-1 ml-1" title="Edit" data-target="#payment_request_modal" data-toggle="modal" ><i class="fa fa-pen"></i></a>';
            }
            $tempRow['id'] = $row['id'];
            $tempRow['user_id'] = $row['user_id'];
            $tempRow['user_name'] = $row['username'];
            // $tempRow['payment_type'] = $row['payment_type'];
            $tempRow['amount_requested'] = $currency_symbol . ' ' . $row['amount_requested'];
            $tempRow['remarks'] = $row['remarks'];
            $tempRow['payment_address'] = $row['payment_address'];
            $tempRow['date_created'] = date('d-m-Y', strtotime($row['date_created']));

            $status = [
                '0' => '<span class="badge badge-success">Pending</span>',
                '1' => '<span class="badge badge-primary">Approved</span>',
                '2' => '<span class="badge badge-danger">Rejected</span>',
            ];

            $tempRow['status_digit'] = $row['status'];
            $tempRow['status'] = $status[$row['status']];
            $tempRow['remarks'] = $row['remarks'];
            if (!isset($user_id) && empty($user_id)) {
                $tempRow['operate'] = $operate;
            }
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
    function get_wallet_transactions_list($user_id = NULL)
    {
        $offset = 0;
        $limit = 10;
        $sort = 'awt.id';
        $order = 'DESC';
        $multipleWhere = '';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "awt.id";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['awt.`id`' => $search, 'awt.`message`' => $search, 'u.`username`' => $search, 'u.`email`' => $search, 'u.`mobile`' => $search];
        }

        $count_res = $this->db->select(' COUNT(awt.id) as `total` ')->join('users u', 'u.id=awt.user_id');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $count_res->or_like($multipleWhere);
            $this->db->group_End();
        }

        if (isset($_GET['transaction_type_filter']) && $_GET['transaction_type_filter'] != '') {
            $count_res->where('awt.reference_type', $_GET['transaction_type_filter']);
        }

        if (isset($user_id) && !empty($user_id)) {
            $count_res->where('awt.user_id', $user_id);
        }

        $request_count = $count_res->get('affiliate_wallet_transactions awt')->result_array();

        foreach ($request_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->join('users u', 'u.id=awt.user_id');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $search_res->or_like($multipleWhere);
            $this->db->group_End();
        }

        if (isset($_GET['transaction_type_filter']) && $_GET['transaction_type_filter'] != '') {
            $search_res->where('awt.reference_type', $_GET['transaction_type_filter']);
        }

        if (isset($user_id) && !empty($user_id)) {
            $search_res->where('awt.user_id', $user_id);
        }

        $offer_search_res = $search_res->order_by($sort, "desc")->limit($limit, $offset)->select('u.username,awt.*')->get('affiliate_wallet_transactions awt')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();


        $currency_symbol = get_settings('currency');

        foreach ($offer_search_res as $row) {
            // print_R($row);
            $row = output_escaping($row);

            $tempRow['id'] = $row['id'];
            $tempRow['user_id'] = $row['user_id'];
            $tempRow['user_name'] = $row['username'];
            $tempRow['payment_type'] = $row['type'];
            $tempRow['amount_requested'] = $currency_symbol . ' ' . $row['amount'];
            $tempRow['reference_type'] = $row['reference_type'];
            $tempRow['message'] = $row['message'];
            // $tempRow['date_created'] = $row['created_at'];
            $tempRow['date_created'] = date('d-m-Y', strtotime($row['created_at']));

            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
}

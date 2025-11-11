<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Payment_request_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->model(['transaction_model', 'affiliate_model', 'affiliate_transaction_model']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    function get_payment_request_list($user_id = NULL)
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
        if (isset($_GET['user_filter']) && $_GET['user_filter'] != '') {
            $where = ['payment_type' => $_GET['user_filter']];
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
        foreach ($offer_search_res as $row) {
            // print_R($row);
            $row = output_escaping($row);
            if (!isset($user_id) && empty($user_id)) {
                $operate = '<a href="javascript:void(0)" class="edit_request action-btn btn btn-success btn-xs mr-1 mb-1 ml-1" title="Edit" data-target="#payment_request_modal" data-toggle="modal" ><i class="fa fa-pen"></i></a>';
            }
            $tempRow['id'] = $row['id'];
            $tempRow['user_id'] = $row['user_id'];
            $tempRow['user_name'] = $row['username'];
            $tempRow['payment_type'] = ucfirst($row['payment_type']);
            $tempRow['amount_requested'] = $row['amount_requested'];
            $tempRow['remarks'] = $row['remarks'];
            $tempRow['payment_address'] = $row['payment_address'];
            $tempRow['date_created'] = $row['date_created'];
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


    function update_payment_request($data)
    {
        // print_R($data);
        // die;

        $data = escape_array($data);
        $request = array(
            'status' => $data['status'],
            'remarks' => (isset($data['update_remarks']) && !empty($data['update_remarks'])) ? $data['update_remarks'] : null,
        );
        $payment_request_data = fetch_details("payment_requests", ['id' => $data['payment_request_id']], "amount_requested,user_id,status");

        $previous_status = $payment_request_data[0]['status'];

        if ($previous_status == '1' && $data['status'] == '1') {
            return [
                'error' => true,
                'message' => "This Request Is Already Approved",
            ];
        }
        if ($previous_status == '2' && $data['status'] == '2') {
            return [
                'error' => true,
                'message' => "This Request Is Already Rejected",
            ];
        }

        if ($previous_status == '2' && $data['status'] == '1') {
            return [
                'error' => true,
                'message' => "You cannot approve a request that has been rejected.",
            ];
        }

        if ($data['status'] == 1 && ($data['payment_type'] == 'affiliate' || $data['payment_type'] == 'Affiliate')) {
            $transaction_data = [
                'transaction_type' => 'debit',
                'user_id' => $payment_request_data[0]['user_id'],
                'amount' => $payment_request_data[0]['amount_requested'],
                'reference_type' => 'withdraw',
                'message' => 'Payment request appoved for id: ' . $data['payment_request_id'] . ' by admin successfully.',
                'transaction_date' => date('Y-m-d H:i:s'),
            ];
            $this->affiliate_transaction_model->add_affiliate_wallet_transactions($transaction_data);
        }

        if ($data['status'] == 2 && ($data['payment_type'] == 'affiliate' || $data['payment_type'] == 'Affiliate')) {
            $transaction_data = [
                'transaction_type' => 'credit',
                'user_id' => $payment_request_data[0]['user_id'],
                'amount' => $payment_request_data[0]['amount_requested'],
                'reference_type' => 'withdraw',
                'transaction_date' => date('Y-m-d H:i:s'),
                'message' => 'Payment request rejected by admin.',
            ];
            $this->affiliate_transaction_model->add_affiliate_wallet_transactions($transaction_data);
            // update_affiliate_wallet_balance('wallet', $payment_request_data[0]['user_id'], $payment_request_data[0]['amount_requested'], reference_type :'add');
            $this->affiliate_transaction_model->update_balance($payment_request_data[0]['amount_requested'], $payment_request_data[0]['user_id'], "add");

            
        }

        if ($data['status'] == 1 && $data['payment_type'] != 'affiliate') {
            $transaction_data = [
                'transaction_type' => 'wallet',
                'type' => 'debit',
                'amount' => $payment_request_data[0]['amount_requested'],
                'transaction_date' => date('Y-m-d H:i:s'),
                'message' => 'Payment request updated by admin successfully.',
                'status' => 'success',
                'user_id' => $payment_request_data[0]['user_id']
            ];
            $this->transaction_model->add_transaction($transaction_data);
        }
        if ($data['status'] == 2) {
            $transaction_data = [
                'transaction_type' => 'wallet',
                'type' => 'credit',
                'amount' => $payment_request_data[0]['amount_requested'],
                'transaction_date' => date('Y-m-d H:i:s'),
                'message' => 'Payment request rejected by admin.',
                'status' => 'success',
                'user_id' => $payment_request_data[0]['user_id']
            ];
            $this->transaction_model->add_transaction($transaction_data);
            update_balance($payment_request_data[0]['amount_requested'], $payment_request_data[0]['user_id'], "add");
        }
        $this->db->where('id', $data['payment_request_id'])->update('payment_requests', $request);
        return [
            'error' => false,
            'message' => "Payment request updated successfully",
        ];
    }
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Transaction extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation', 'upload', 'pagination']);
        $this->load->helper(['url', 'language', 'file', 'security']);
        $this->load->model(['product_model', 'category_model', 'affiliate_model', 'affiliate_transaction_model']);
    }
    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {
            $this->data['main_page'] = TABLES . 'manage-earnings';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Earning Management | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Earning Management |' . $settings['app_name'];
            $this->data['currency'] = $settings['currency'];
            $affiliate_id = $_SESSION['user_id'];

            $earning_data = $this->affiliate_transaction_model->get_affiliate_commission_summary($affiliate_id);
            // echo "<pre>";
            // print_R($settings);
            // die;
            $this->data['earning_data'] = $earning_data;

            $this->load->view('affiliate/template', $this->data);
        } else {
            redirect('affiliate/login', 'refresh');
        }
    }

    public function payment_request()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {
            $this->data['main_page'] = TABLES . 'withdrawal-request';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Affiliate wallet | ' . $settings['app_name'];
            $this->data['meta_description'] = ' Affiliate wallet  | ' . $settings['app_name'];
            $this->data['currency'] = $settings['currency'];

            $affiliate_id = $_SESSION['user_id'];
            $earning_data = $this->affiliate_transaction_model->get_affiliate_commission_summary($affiliate_id);

            $this->data['earning_data'] = $earning_data;

            $this->load->view('affiliate/template', $this->data);
        } else {
            redirect('affiliate/login', 'refresh');
        }
    }

    public function add_withdrawal_request()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {

            $settings = get_settings('affiliate_settings', true);
            $this->form_validation->set_rules('payment_address', 'Payment Address', 'trim|required|xss_clean');
            $this->form_validation->set_rules('withdrawalAmount', 'Amount', 'trim|required|xss_clean|numeric|greater_than[0]');

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['message'] = strip_tags(validation_errors());
                $this->response['data'] = array();
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
            } else {
                $user_id = $this->session->userdata('user_id');
                $payment_address = $this->input->post('payment_address', true);
                $amount = $this->input->post('withdrawalAmount', true);
                $userData = fetch_details('affiliates', ['user_id' => $user_id], 'affiliate_wallet_balance');

                if (!empty($userData)) {
                    if ($amount > $settings['max_amount_for_withwrawal_req']) {
                        $this->response['error'] = true;
                        $this->response['message'] = 'You can sent maximum ' . $settings['max_amount_for_withwrawal_req'] . ' for the withdraw request.';
                        $this->response['data'] = array();
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    } elseif ($amount < $settings['min_amount_for_withwrawal_req']) {
                        $this->response['error'] = true;
                        $this->response['message'] = 'Minimum ' . $settings['min_amount_for_withwrawal_req'] . ' amount is required in wallet.';
                        $this->response['data'] = array();
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    } else {
                        if ($amount <= $userData[0]['affiliate_wallet_balance']) {
                            $data = [
                                'user_id' => $user_id,
                                'payment_address' => $payment_address,
                                'payment_type' => 'affiliate',
                                'amount_requested' => $amount,
                            ];

                            if (insert_details($data, 'payment_requests')) {
                                $this->affiliate_transaction_model->update_balance($amount, $user_id, 'deduct');
                                $userData = fetch_details('affiliates', ['user_id' => $user_id], 'affiliate_wallet_balance');
                                $this->response['error'] = false;
                                $this->response['message'] = 'Withdrawal Request Sent Successfully';
                                $this->response['data'] = $userData[0]['affiliate_wallet_balance'];
                                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                            } else {
                                $this->response['error'] = true;
                                $this->response['message'] = 'Cannot sent Withdrawal Request.Please Try again later.';
                                $this->response['data'] = array();
                                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                            }
                        } else {
                            $this->response['error'] = true;
                            $this->response['message'] = 'You don\'t have enough balance to sent the withdraw request.';
                            $this->response['data'] = array();
                            $this->response['csrfName'] = $this->security->get_csrf_token_name();
                            $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        }
                    }

                    print_r(json_encode($this->response));
                }
            }
        } else {
            redirect('affiliate/login', 'refresh');
        }
    }

    public function view_withdrawal_request_list()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {
            $affiliate_id = $this->session->userdata('user_id');
            return $this->affiliate_transaction_model->get_withdrawal_request_list($affiliate_id);
        } else {
            redirect('seller/login', 'refresh');
        }
    }
    public function view_wallet_transactions_list()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {
            $affiliate_id = $this->session->userdata('user_id');
            return $this->affiliate_transaction_model->get_wallet_transactions_list($affiliate_id);
        } else {
            redirect('seller/login', 'refresh');
        }
    }

    public function affiliate_wallet_transactions()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {
            $this->data['main_page'] = TABLES . 'wallet-transaction';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Affiliate wallet | ' . $settings['app_name'];
            $this->data['meta_description'] = ' Affiliate wallet  | ' . $settings['app_name'];
            $this->data['currency'] = $settings['currency'];

            $affiliate_id = $_SESSION['user_id'];
            $earning_data = $this->affiliate_transaction_model->get_affiliate_commission_summary($affiliate_id);

            $this->data['earning_data'] = $earning_data;

            $this->load->view('affiliate/template', $this->data);
        } else {
            redirect('affiliate/login', 'refresh');
        }
    }
}

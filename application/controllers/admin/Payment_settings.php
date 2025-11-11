<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payment_settings extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model('Setting_model');

        if (!has_permissions('read', 'payment_settings')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        }
    }


    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'payment-settings';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Payment Methods Management | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Payment Methods Management  | ' . $settings['app_name'];
            $this->data['settings'] = get_settings('payment_method', true);
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function update_payment_settings()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('update', 'payment_settings'), PERMISSION_ERROR_MSG, 'payment_settings')) {
                return false;
            }
            if (defined('SEMI_DEMO_MODE') && SEMI_DEMO_MODE == 0) {
                $this->response['error'] = true;
                $this->response['message'] = SEMI_DEMO_MODE_MSG;
                echo json_encode($this->response);
                return false;
                exit();
            }
            $_POST['temp'] = '1';
            $this->form_validation->set_rules('temp', '', 'trim|required|xss_clean');
            if (
                empty($_POST['paypal_payment_method']) && empty($_POST['razorpay_payment_method'])
                && empty($_POST['paystack_payment_method']) && empty($_POST['flutterwave_payment_method']) && empty($_POST['stripe_payment_method'])
                && empty($_POST['paytm_payment_method']) && empty($_POST['midtrans_payment_method']) && empty($_POST['myfaoorah_payment_method'])
                && empty($_POST['myfaoorah_payment_method']) && empty($_POST['instamojo_payment_method']) && empty($_POST['phonepe_payment_method'])
                && empty($_POST['direct_bank_transfer']) && empty($_POST['cod_method'])
            ) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'Please select at least one payment method.';
                print_r(json_encode($this->response));
                return false;
            }
            if (isset($_POST['paypal_payment_method']) && !empty($_POST['paypal_payment_method'])) {
                $this->form_validation->set_rules('paypal_mode', 'paypal Payment Mode', 'trim|required|xss_clean');
                $this->form_validation->set_rules('paypal_business_email', 'Paypal Business Email', 'trim|required|xss_clean|valid_email');
                $this->form_validation->set_rules('paypal_client_id', 'Paypal Client ID', 'trim|required|xss_clean');
                $this->form_validation->set_rules('paypal_secret_key', 'Paypal Secret Key', 'trim|required|xss_clean');
                $this->form_validation->set_rules('currency_code', 'Currency Code', 'trim|required|xss_clean');
            }

            if (isset($_POST['razorpay_payment_method']) && !empty($_POST['razorpay_payment_method'])) {
                $this->form_validation->set_rules('razorpay_key_id', 'Razorpay Key Id', 'trim|required|xss_clean');
                $this->form_validation->set_rules('razorpay_secret_key', 'Razorpay Secret Key', 'trim|required|xss_clean');
                $this->form_validation->set_rules('refund_webhook_secret_key', 'Refund Webhook Secret Key', 'trim|required|xss_clean');
            }

            if (isset($_POST['paystack_payment_method']) && !empty($_POST['paystack_payment_method'])) {
                $this->form_validation->set_rules('paystack_key_id', 'Paystack Key Id', 'trim|required|xss_clean');
                $this->form_validation->set_rules('paystack_secret_key', 'Paystack Secret Key', 'trim|required|xss_clean');
            }

            if (isset($_POST['flutterwave_payment_method']) && !empty($_POST['flutterwave_payment_method'])) {
                $this->form_validation->set_rules('flutterwave_public_key', 'Flutterwave Public Key', 'trim|required|xss_clean');
                $this->form_validation->set_rules('flutterwave_secret_key', 'Flutterwave Secret Key', 'trim|required|xss_clean');
                $this->form_validation->set_rules('flutterwave_encryption_key', 'Flutterwave Encryption Key', 'trim|required|xss_clean');
                $this->form_validation->set_rules('flutterwave_currency_code', 'Flutterwave Currency code', 'trim|required|xss_clean');
            }

            if (isset($_POST['stripe_payment_method']) && !empty($_POST['stripe_payment_method'])) {
                $this->form_validation->set_rules('stripe_publishable_key', 'Stripe Publishable Key', 'trim|required|xss_clean');
                $this->form_validation->set_rules('stripe_secret_key', 'Stripe Secret Key', 'trim|required|xss_clean');
                $this->form_validation->set_rules('stripe_webhook_secret_key', 'Stripe Webhook Secret Key', 'trim|required|xss_clean');
                $this->form_validation->set_rules('stripe_currency_code', 'Stripe Currency Code', 'trim|required|xss_clean');
            }
            if (isset($_POST['paytm_payment_method']) && !empty($_POST['paytm_payment_method'])) {
                $this->form_validation->set_rules('paytm_payment_mode', 'Paytm Payment Mode', 'trim|required|xss_clean');
                $this->form_validation->set_rules('paytm_merchant_key', 'Paytm Merchant Key', 'trim|required|xss_clean');
                $this->form_validation->set_rules('paytm_merchant_id', 'Paytm Merchant ID', 'trim|required|xss_clean');
                if ($_POST['paytm_payment_mode'] == 'production') {
                    $this->form_validation->set_rules('paytm_website', 'Paytm website', 'trim|required|xss_clean');
                    $this->form_validation->set_rules('paytm_industry_type_id', 'Paytm Industry Type ID', 'trim|required|xss_clean');
                }
            }
            if (isset($_POST['midtrans_payment_method']) && !empty($_POST['midtrans_payment_method'])) {
                $this->form_validation->set_rules('midtrans_payment_mode', 'Midtrans Payment Mode', 'trim|required|xss_clean');
                $this->form_validation->set_rules('midtrans_client_key', 'Midtrans Client  Key', 'trim|required|xss_clean');
                $this->form_validation->set_rules('midtrans_merchant_id', 'Midtrans Merchant ID', 'trim|required|xss_clean');
                $this->form_validation->set_rules('midtrans_server_key', 'Midtrans Server Key', 'trim|required|xss_clean');
            }



            if (isset($_POST['myfaoorah_payment_method']) && !empty($_POST['myfaoorah_payment_method'])) {
                $this->form_validation->set_rules('myfatoorah_token', 'Myfatoorah Token', 'trim|required|xss_clean');
                $this->form_validation->set_rules('myfatoorah_payment_mode', 'Myfatoorah Payment Mode ', 'trim|required|xss_clean');
                $this->form_validation->set_rules('myfatoorah_language', 'Myfatoorah Language', 'trim|required|xss_clean');
                $this->form_validation->set_rules('myfatoorah_country', 'Myfatoorah Country', 'trim|required|xss_clean');
                $this->form_validation->set_rules('myfatoorah__secret_key', 'myfatoorah Secret Key', 'trim|required|xss_clean');
            }
            if (isset($_POST['instamojo_payment_method']) && !empty($_POST['instamojo_payment_method'])) {
                $this->form_validation->set_rules('instamojo_payment_mode', 'Instamojo Payment  Mode', 'trim|required|xss_clean');
                $this->form_validation->set_rules('instamojo_client_id', 'Instamojo client id', 'trim|required|xss_clean');
                $this->form_validation->set_rules('instamojo_client_secret', 'Instamojo client secret', 'trim|required|xss_clean');
            }
            if (isset($_POST['phonepe_payment_method']) && !empty($_POST['phonepe_payment_method'])) {
                $this->form_validation->set_rules('phonepe_payment_mode', 'phonepe Payment Mode', 'trim|required|xss_clean');
                // $this->form_validation->set_rules('phonepe_marchant_id', 'phonepe marchant id', 'trim|required|xss_clean');
                $this->form_validation->set_rules('phonepe_client_id', 'phonepe Client ID', 'trim|required|xss_clean');
                $this->form_validation->set_rules('phonepe_client_secret', 'phonepe Client Secret', 'trim|required|xss_clean');
            }
            if (isset($_POST['direct_bank_transfer'])) {
                $this->form_validation->set_rules('account_name', 'Account Name', 'trim|required|xss_clean');
                $this->form_validation->set_rules('account_number', 'Account Number', 'trim|required|xss_clean');
                $this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|required|xss_clean');
                $this->form_validation->set_rules('bank_code', 'Bank Code', 'trim|required|xss_clean');
            }
            if (isset($_POST['cod_method'])) {
                $this->form_validation->set_rules('max_cod_amount', 'Max COD Amount', 'trim|required|xss_clean');
                $this->form_validation->set_rules('min_cod_amount', 'Min COD Amount', 'trim|required|xss_clean');
            }
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                $fields = [
                    'paypal_payment_method', 'paypal_mode', 'paypal_business_email', 'paypal_secret_key', 'paypal_client_id', 'currency_code',
                    'razorpay_payment_method', 'razorpay_key_id', 'razorpay_secret_key', 'refund_webhook_secret_key',
                    'paystack_payment_method', 'paystack_key_id', 'paystack_secret_key',
                    'stripe_payment_method', 'stripe_payment_mode', 'stripe_publishable_key', 'stripe_secret_key',
                    'stripe_webhook_secret_key', 'stripe_currency_code',
                    'flutterwave_payment_method', 'flutterwave_public_key', 'flutterwave_secret_key',
                    'flutterwave_encryption_key', 'flutterwave_currency_code', 'flutterwave_webhook_secret_key',
                    'paytm_payment_method', 'paytm_payment_mode', 'paytm_merchant_key', 'paytm_merchant_id',
                    'paytm_website', 'paytm_industry_type_id', 
                    'midtrans_payment_method', 'midtrans_payment_mode', 'midtrans_client_key', 'midtrans_merchant_id', 'midtrans_server_key', 
                    'myfatoorah_payment_method', 'myfatoorah_token','myfaoorah_payment_method', 'myfatoorah_payment_mode', 'myfatoorah_language', 'myfatoorah__webhook_url',
                    'myfatoorah_country', 'myfatoorah__successUrl', 'myfatoorah__errorUrl', 'myfatoorah__secret_key',
                    'instamojo_payment_method', 'instamojo_payment_mode', 'instamojo_client_id', 'instamojo_client_secret', 'instamojo_webhook_url',
                    'phonepe_payment_method', 'phonepe_payment_mode', 'phonepe_client_id', 'phonepe_client_secret', 'phonepe_webhook_url','phonepe_marchant_id',
                    'direct_bank_transfer', 'account_name', 'account_number', 'bank_name', 'bank_code', 'notes', 'cod_method', 'temp', "min_cod_amount", "max_cod_amount"
                ];
                
                foreach ($fields as $field) {
                    $payment_settings[$field] = $this->input->post($field, true) ?? "";
                }
                $this->Setting_model->update_payment_method($payment_settings);
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'System Setting Updated Successfully';
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }
}

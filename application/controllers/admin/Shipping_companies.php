<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Shipping_companies extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation', 'upload']);
        $this->load->helper(['url', 'language', 'file', 'function_helper']);
        $this->load->model('Shipping_company_model');
        if (!has_permissions('read', 'shipping_company')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        }
    }

    public function index()
    {
        try {
            if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
                $this->data['main_page'] = FORMS . 'shipping-company';

                $settings = get_settings('system_settings', true);
                $this->data['title'] = 'Add Shipping Company | ' . $settings['app_name'];
                $this->data['meta_description'] = 'Add Shipping Company  | ' . $settings['app_name'];

                if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {
                    $this->data['fetched_data'] = $this->db->select('u.*')
                        ->join('users_groups ug', 'ug.user_id = u.id')
                        ->where(['ug.group_id' => '6', 'ug.user_id' => $_GET['edit_id']])
                        ->get('users u')
                        ->result_array();
                }

                $this->data['system_settings'] = get_settings('system_settings', true);

                $this->load->view('admin/template', $this->data);
            } else {
                redirect('admin/login', 'refresh');
            }
        } catch (Exception $e) {
            log_message('error', 'Error in shipping_companies index: ' . $e->getMessage());
            show_error($e->getMessage());
        }
    }

    public function manage_shipping_company()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = TABLES . 'manage-shipping-company';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Shipping Company Management | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Shipping Company Management | ' . $settings['app_name'];
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function view_shipping_companies()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (isset($_GET['shipping_company_status']) && !empty($_GET['shipping_company_status'])) {
                return $this->Shipping_company_model->get_shipping_companies_list($_GET['shipping_company_status']);
            }
            return $this->Shipping_company_model->get_shipping_companies_list();
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function delete_shipping_company()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (print_msg(!has_permissions('delete', 'shipping_company'), PERMISSION_ERROR_MSG, 'shipping_company', false)) {
                return true;
            }

            if (defined('SEMI_DEMO_MODE') && SEMI_DEMO_MODE == 0) {
                $this->response['error'] = true;
                $this->response['message'] = SEMI_DEMO_MODE_MSG;
                echo json_encode($this->response);
                return false;
            }

            if (!isset($_GET['id']) && empty($_GET['id'])) {
                $this->response['error'] = true;
                $this->response['message'] = 'Shipping company id is required';
                print_r(json_encode($this->response));
                return;
            }

            $company_id = $this->input->get('id', true);

            // Check if shipping company has active quotes or orders
            $quotes = fetch_details('shipping_company_quotes', ['shipping_company_id' => $company_id, 'is_active' => 1]);

            if (!empty($quotes)) {
                $this->response['error'] = true;
                $this->response['message'] = 'You cannot delete shipping company with active quotes. Please deactivate all quotes first.';
                print_r(json_encode($this->response));
                return;
            }

            if (delete_details(['user_id' => $_GET['id']], 'users_groups')) {
                // Delete zipcode assignments
                delete_details(['shipping_company_id' => $company_id], 'shipping_company_zipcodes');

                $this->response['error'] = false;
                $this->response['message'] = 'Shipping company removed successfully';
                print_r(json_encode($this->response));
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Something Went Wrong';
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function add_shipping_company()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (isset($_POST['edit_shipping_company'])) {
                if (print_msg(!has_permissions('update', 'shipping_company'), PERMISSION_ERROR_MSG, 'shipping_company')) {
                    return true;
                }
            } else {
                if (print_msg(!has_permissions('create', 'shipping_company'), PERMISSION_ERROR_MSG, 'shipping_company')) {
                    return true;
                }
            }

            $this->form_validation->set_rules('company_name', 'Company Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('email', 'Mail', 'trim|required|xss_clean|valid_email');
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|xss_clean|min_length[5]|max_length[16]');
            $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean');
            $this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean');

            // Zipcode is always required for shipping companies
            $this->form_validation->set_rules('assign_zipcode[]', 'Assigned Zipcodes', 'trim|required|xss_clean');

            if (!isset($_POST['edit_shipping_company'])) {
                $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
                $this->form_validation->set_rules('confirm_password', 'Confirm password', 'trim|required|matches[password]|xss_clean');
            }

            // KYC document validation
            if (!isset($_POST['edit_shipping_company'])) {
                if (!isset($_FILES['kyc_documents']['name'][0]) || empty($_FILES['kyc_documents']['name'][0])) {
                    $this->form_validation->set_rules('kyc_documents', 'KYC Documents', 'trim|required|xss_clean', array('required' => 'Please upload at least one KYC document'));
                }
            }

            if (isset($_POST['edit_shipping_company'])) {
                $company_data = fetch_details('users', ['id' => $_POST['edit_shipping_company']], 'kyc_documents');
                if (isset($company_data[0]['kyc_documents']) && !empty($company_data[0]['kyc_documents'])) {
                    $kyc_documents = explode(',', $company_data[0]['kyc_documents']);
                }
            }

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {

                // Upload KYC documents
                if (!file_exists(FCPATH . SHIPPING_COMPANY_DOCUMENTS_PATH)) {
                    mkdir(FCPATH . SHIPPING_COMPANY_DOCUMENTS_PATH, 0777, true);
                }

                $temp_array = array();
                $files = $_FILES;
                $images_new_name_arr = array();
                $images_info_error = "";
                $allowed_media_types = implode('|', allowed_media_types());
                $config = [
                    'upload_path' => FCPATH . SHIPPING_COMPANY_DOCUMENTS_PATH,
                    'allowed_types' => $allowed_media_types,
                    'max_size' => 8000,
                ];

                if (isset($files['kyc_documents']) && !empty($files['kyc_documents']['name'][0]) && isset($files['kyc_documents']['name'][0])) {
                    $doc_count = count((array)$files['kyc_documents']['name']);
                    $doc_upload = $this->upload;
                    $doc_upload->initialize($config);

                    if (isset($_POST['edit_shipping_company']) && !empty($_POST['edit_shipping_company']) && isset($company_data[0]['kyc_documents']) && !empty($company_data[0]['kyc_documents'])) {
                        $old_docs = explode(',', $company_data[0]['kyc_documents']);
                        foreach ($old_docs as $old_doc) {
                            if (file_exists(FCPATH . $old_doc)) {
                                unlink(FCPATH . $old_doc);
                            }
                        }
                    }

                    for ($i = 0; $i < $doc_count; $i++) {
                        if (!empty($_FILES['kyc_documents']['name'][$i])) {
                            $_FILES['temp_doc']['name'] = $files['kyc_documents']['name'][$i];
                            $_FILES['temp_doc']['type'] = $files['kyc_documents']['type'][$i];
                            $_FILES['temp_doc']['tmp_name'] = $files['kyc_documents']['tmp_name'][$i];
                            $_FILES['temp_doc']['error'] = $files['kyc_documents']['error'][$i];
                            $_FILES['temp_doc']['size'] = $files['kyc_documents']['size'][$i];

                            if (!$doc_upload->do_upload('temp_doc')) {
                                $images_info_error = 'kyc_documents: ' . $images_info_error . ' ' . $doc_upload->display_errors();
                            } else {
                                $temp_array = $doc_upload->data();
                                resize_review_images($temp_array, FCPATH . SHIPPING_COMPANY_DOCUMENTS_PATH);
                                $images_new_name_arr[$i] = SHIPPING_COMPANY_DOCUMENTS_PATH . $temp_array['file_name'];
                            }
                        }
                    }

                    // Delete uploaded files if error occurred
                    if ($images_info_error != NULL || !$this->form_validation->run()) {
                        if (isset($images_new_name_arr) && !empty($images_new_name_arr)) {
                            foreach ($images_new_name_arr as $key => $val) {
                                if (file_exists(FCPATH . $images_new_name_arr[$key])) {
                                    unlink(FCPATH . $images_new_name_arr[$key]);
                                }
                            }
                        }
                    }
                }

                if ($images_info_error != NULL) {
                    $this->response['error'] = true;
                    $this->response['message'] = $images_info_error;
                    print_r(json_encode($this->response));
                    return false;
                }

                if (isset($_POST['edit_shipping_company'])) {
                    if (!edit_unique($this->input->post('email', true), 'users.email.' . $this->input->post('edit_shipping_company', true) . '') || !edit_unique($this->input->post('mobile', true), 'users.mobile.' . $this->input->post('edit_shipping_company', true) . '')) {
                        $response["error"] = true;
                        $response["message"] = "Email or mobile already exists!";
                        $response['csrfName'] = $this->security->get_csrf_token_name();
                        $response['csrfHash'] = $this->security->get_csrf_hash();
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }

                    $assign_zipcode = isset($_POST['assign_zipcode']) && !empty($_POST['assign_zipcode'])
                        ? implode(",", $this->input->post('assign_zipcode', true))
                        : NULL;

                    $_POST['status'] = $this->input->post('status', true);
                    $_POST['assign_zipcode'] = $assign_zipcode;
                    $_POST['kyc_documents'] = isset($images_new_name_arr) && !empty($images_new_name_arr)
                        ? implode(',', (array)$images_new_name_arr)
                        : (isset($company_data[0]['kyc_documents']) ? $company_data[0]['kyc_documents'] : '');

                    $email_settings = get_settings('email_settings', true);


                    $this->Shipping_company_model->update_shipping_company($_POST);



                    if (!empty($_POST['edit_shipping_company']) && $_POST['status'] == 1) {
                        if (isset($email_settings) && !empty($email_settings)) {
                            $company = fetch_details('users', ['id' => $_POST['edit_shipping_company']]);
                            $title = "Congratulations! Your Shipping Company Account Has Been Approved";
                            $mail_admin_msg = 'We are delighted to inform you that your application to become an approved shipping company on our platform has been successful! Congratulations on this significant milestone.';
                            $email_message = array(
                                'username' => 'Hello, Dear <b>' . ucfirst($company[0]['username']) . '</b>, ',
                                'subject' => $title,
                                'email' => $company[0]['email'],
                                'message' => $mail_admin_msg
                            );
                            send_mail($company[0]['email'], $title, $this->load->view('admin/pages/view/contact-email-template', $email_message, TRUE));
                        }
                    }
                } else {
                    if (!$this->form_validation->is_unique($_POST['mobile'], 'users.mobile') || !$this->form_validation->is_unique($_POST['email'], 'users.email')) {
                        $response["error"] = true;
                        $response["message"] = "Email or mobile already exists!";
                        $response['csrfName'] = $this->security->get_csrf_token_name();
                        $response['csrfHash'] = $this->security->get_csrf_hash();
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }

                    $identity_column = $this->config->item('identity', 'ion_auth');
                    $email = strtolower($this->input->post('email'));
                    $mobile = $this->input->post('mobile');
                    $identity = ($identity_column == 'mobile') ? $mobile : $email;
                    $password = $this->input->post('password');

                    $assign_zipcode = isset($_POST['assign_zipcode']) && !empty($_POST['assign_zipcode'])
                        ? implode(",", $this->input->post('assign_zipcode', true))
                        : NULL;

                    $additional_data = [
                        'username' => $this->input->post('company_name'),
                        'address' => $this->input->post('address'),
                        'serviceable_zipcodes' => $assign_zipcode,
                        'type' => 'phone',
                        'kyc_documents' => implode(',', $images_new_name_arr),
                        'status' => $this->input->post('status', true),
                        'is_shipping_company' => 1,
                    ];

                    // $this->ion_auth->register($identity, $password, $email, $additional_data, ['6']);
                    // update_details(['active' => 1], [$identity_column => $identity], 'users');


                    // will implement forgot password later when creating shipping panel
                    // register user
                    $register_id = $this->ion_auth->register($identity, $password, $email, $additional_data, ['6']);

                    // if registration successful
                    if ($register_id) {
                        // activate user right away (as you were doing)
                        update_details(['active' => 1], [$identity_column => $identity], 'users');

                        // fetch the created company user (you use fetch_details elsewhere)
                        $company = fetch_details('users', ['id' => $register_id]);

                        // prepare email content — include credentials (temporary approach)
                        $settings = get_settings('system_settings', true);
                        $app_name = isset($settings['app_name']) ? $settings['app_name'] : 'Your App';
                        $company_name = $this->input->post('company_name', true);

                        $title = 'Your Shipping Company Account on ' . $app_name;
                        $mail_admin_msg  = '<p>Dear <b>' . htmlspecialchars($company_name) . '</b>,</p>';
                        $mail_admin_msg .= '<p>Your shipping company account has been created on <b>' . htmlspecialchars($app_name) . '</b>.</p>';
                        $mail_admin_msg .= '<p><strong>Login email:</strong> ' . htmlspecialchars($email) . '</p>';
                        // include password — temporary; replace later with reset link
                        $mail_admin_msg .= '<p><strong>Password:</strong> ' . htmlspecialchars($password) . '</p>';

                        // login URL
                        $login_url = base_url('admin/login'); // adjust if shipping companies use another login URL
                        $mail_admin_msg .= '<p>You can login here: <a href="' . $login_url . '">' . $login_url . '</a></p>';

                        // optional: mention forgot/reset flow will be available later
                        $mail_admin_msg .= '<p><small>Note: For security, we recommend changing your password after first login. A password reset link feature will be sent in future emails.</small></p>';

                        $email_message = array(
                            'username' => 'Hello, Dear <b>' . ucfirst($company_name) . '</b>, ',
                            'subject' => $title,
                            'email' => $email,
                            'message' => $mail_admin_msg
                        );

                        // send email (uses your existing helper)
                        $send_result = send_mail($email, $title, $this->load->view('admin/pages/view/contact-email-template', $email_message, TRUE));

                        // optional: log if email failed — useful for debugging in dev
                        if (isset($send_result['error']) && $send_result['error'] === true) {
                            log_message('error', 'Shipping company creation: email send failed for user_id ' . $register_id . '. Debug: ' . print_r($send_result, true));
                        }
                    }
                }

                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $message = (isset($_POST['edit_shipping_company'])) ? 'Shipping Company Updated Successfully' : 'Shipping Company Added Successfully';
                $this->response['message'] = $message;
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }


    // Cash Collection

    public function manage_cash()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = TABLES . 'shipping-company-cash-collection';
            $settings = get_settings('system_settings', true);
            $this->data['curreny'] = $settings['currency'];
            $this->data['shipping_companies'] = $this->db->where(['ug.group_id' => '6', 'u.active' => 1])->join('users_groups ug', 'ug.user_id = u.id')->get('users u')->result_array();
            $this->data['title'] = 'View Cash Collection | ' . $settings['app_name'];
            $this->data['meta_description'] = 'View Cash Collection | ' . $settings['app_name'];
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function get_cash_collection()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            return $this->Shipping_company_model->get_cash_collection_list();
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function manage_cash_collection()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('create', 'fund_transfer'), PERMISSION_ERROR_MSG, 'fund_transfer')) {
                return false;
            }

            $this->form_validation->set_rules('shipping_company_id', 'Shipping Company', 'trim|required|xss_clean|numeric');
            $this->form_validation->set_rules('amount', 'Amount', 'trim|required|xss_clean|numeric|greater_than[0]');
            $this->form_validation->set_rules('date', 'Date', 'trim|required|xss_clean');
            $this->form_validation->set_rules('message', 'Message', 'trim|xss_clean');

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                echo json_encode($this->response);
                return false;
            } else {
                $company_id = $this->input->post('shipping_company_id', true);
                if (!is_exist(['id' => $company_id], 'users')) {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Shipping Company does not exist in your database';
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    print_r(json_encode($this->response));
                    return false;
                }

                $res = fetch_details('users', ['id' => $company_id], 'cash_received');
                $amount = $this->input->post('amount', true);
                $order_id = $this->input->post('order_id', true);
                $transaction_id = $this->input->post('transaction_id', true);
                $date = $this->input->post('date', true);
                $message = (isset($_POST['message']) && !empty($_POST['message'])) ? $this->input->post('message', true) : "Shipping company cash collection by admin";

                if ($res[0]['cash_received'] < $amount) {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'Amount must not be greater than cash';
                    echo json_encode($this->response);
                    return false;
                }

                if ($res[0]['cash_received'] > 0 && $res[0]['cash_received'] != null) {
                    update_cash_received($amount, $company_id, "deduct");
                    $this->load->model("transaction_model");
                    $transaction_data = [
                        'transaction_type' => "transaction",
                        'user_id' => $company_id,
                        'order_id' => $order_id,
                        'type' => "shipping_company_cash_collection",
                        'txn_id' => "",
                        'amount' => $amount,
                        'status' => "1",
                        'message' => $message,
                        'transaction_date' => $date,
                    ];
                    update_details($transaction_data, ['id' => $transaction_id], 'transactions');

                    $this->response['error'] = false;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'Amount Successfully Collected';
                } else {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'Cash should be greater than 0';
                }

                echo json_encode($this->response);
                return false;
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    // Fund transfer

    public function fund_transfer()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = TABLES . 'shipping-company-fund-transfers';
            $settings = get_settings('system_settings', true);
            // print_r($settings);
            // die();
            $this->data['title'] = 'View Shipping Company Fund Transfers | ' . $settings['app_name'];
            $this->data['meta_description'] = 'View Shipping Company Fund Transfers | ' . $settings['app_name'];
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function view_fund_transfers()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            return $this->Shipping_company_model->get_fund_transfers_list();
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function add_fund_transfer()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('create', 'fund_transfer'), PERMISSION_ERROR_MSG, 'fund_transfer')) {
                return false;
            }

            $this->form_validation->set_rules('shipping_company_id', 'Shipping Company', 'trim|required|xss_clean|numeric');
            $this->form_validation->set_rules('amount', 'Amount', 'trim|required|xss_clean|numeric|greater_than[0]');
            $this->form_validation->set_rules('date', 'Date', 'trim|required|xss_clean');
            $this->form_validation->set_rules('message', 'Message', 'trim|xss_clean');
            $this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean|in_list[credit,debit]');

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                echo json_encode($this->response);
                return false;
            } else {
                $company_id = $this->input->post('shipping_company_id', true);

                if (!is_exist(['id' => $company_id], 'users')) {
                    $this->response['error'] = true;
                    $this->response['message'] = 'Shipping Company does not exist in your database';
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    print_r(json_encode($this->response));
                    return false;
                }

                $res = fetch_details('users', ['id' => $company_id], 'balance');
                $amount = $this->input->post('amount', true);
                $date = $this->input->post('date', true);
                $type = $this->input->post('type', true);
                $message = (isset($_POST['message']) && !empty($_POST['message']))
                    ? $this->input->post('message', true)
                    : "Fund transfer by admin";

                if ($type == 'debit' && $res[0]['balance'] < $amount) {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'Insufficient balance for debit transaction';
                    echo json_encode($this->response);
                    return false;
                }

                $this->load->model("Shipping_company_model");
                $action = ($type == 'credit') ? 'add' : 'deduct';
                $this->Shipping_company_model->update_balance($amount, $company_id, $action);

                $this->load->model("transaction_model");
                $transaction_data = [
                    'transaction_type' => "transaction",
                    'user_id' => $company_id,
                    'order_id' => '',
                    'type' => $type,
                    'txn_id' => "",
                    'amount' => $amount,
                    'status' => "success",
                    'message' => $message,
                    'transaction_date' => $date,
                ];
                $this->transaction_model->add_transaction($transaction_data);

                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'Fund Transfer Successful';

                echo json_encode($this->response);
                return false;
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }




    // AJAX endpoint to get zipcodes filtered by provider_type = 'company'
    public function get_company_zipcodes()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $search = $this->input->get('search', true);

            $this->db->select('id, zipcode');
            $this->db->from('zipcodes');
            $this->db->where('provider_type', 'company');

            if (!empty($search)) {
                $this->db->like('zipcode', $search);
            }

            $this->db->order_by('zipcode', 'ASC');
            $this->db->limit(50);

            $zipcodes = $this->db->get()->result_array();

            echo json_encode($zipcodes);
        } else {
            echo json_encode([]);
        }
    }
}

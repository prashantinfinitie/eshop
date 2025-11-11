<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Affiliate_users extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model(['Setting_model', 'Home_model', 'affiliate_model']);
        $this->data['firebase_project_id'] = get_settings('firebase_project_id');
        $this->data['service_account_file'] = get_settings('service_account_file');
        if (!has_permissions('read', 'about_us')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        }
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = AFFILIATE . 'manage-users';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Users | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Users | ' . $settings['app_name'];
            $this->data['about_us'] = get_settings('about_us');
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function manage_user()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = AFFILIATE . 'users';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Users | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Users | ' . $settings['app_name'];

            if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {
                $this->data['fetched_data'] = $this->db->select(' u.*,af.* ')
                    ->join('users_groups ug', ' ug.user_id = u.id ')
                    ->join('affiliates af', ' af.user_id = u.id ')
                    ->where(['ug.group_id' => '5', 'ug.user_id' => $_GET['edit_id']])
                    ->get('users u')
                    ->result_array();
            }
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function get_users()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if ($_GET['affiliate_status'] && !empty($_GET['affiliate_status'])) {
                return $this->affiliate_model->get_affiliates_list($_GET['affiliate_status']);
            }
            return $this->affiliate_model->get_affiliates_list();
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function add_user()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (print_msg(!has_permissions('create', 'affiliate_users'), PERMISSION_ERROR_MSG, 'affiliate_users')) {
                return true;
            }
            $user = $this->ion_auth->user()->row();
            $this->form_validation->set_rules('full_name', 'Name', 'trim|required|xss_clean');
            if (empty($_POST['edit_affiliate_user'])) {
                $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|numeric|xss_clean|min_length[5]|max_length[16]|edit_unique[users.mobile.' . $user->id . ']');
                $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
                $this->form_validation->set_rules('confirm_password', 'Confirm password', 'trim|required|matches[password]|xss_clean');
            }
            $this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean');
            $this->form_validation->set_rules(
                'my_website',
                'Website URL',
                'trim|required|xss_clean',
                array('required' => 'Your Website URL is required')
            );
            $this->form_validation->set_rules(
                'my_app',
                'App URL',
                'trim|required|xss_clean',
                array('required' => 'Your App URL is required')
            );
            // $this->form_validation->set_rules('my_website', 'Website URL', 'trim|required|xss_clean');
            // $this->form_validation->set_rules('my_app', 'App URL', 'trim|required|xss_clean');
            $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean');



            if (!$this->form_validation->run()) {

                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
                return false;
            } else {

                if (isset($_POST['edit_affiliate_user']) && !empty($_POST['edit_affiliate_user'])) {

                    $current_status = fetch_details('affiliates', ['user_id' => $this->input->post('edit_affiliate_user')], 'status')[0];

                    if ($current_status['status'] != $this->input->post('status', true)) {
                        $system_settings = get_settings('system_settings', true);
                        if ($this->input->post('status', true) == 0 || $this->input->post('status', true) == '0') {
                            $title = 'Account Deactivation Notice';
                            $fcm_admin_msg = 'We hope this message finds you well. We are writing to inform you about the deactivation of your affiliate account on our platform.';
                            $mail_admin_msg = 'We hope this message finds you well. We are writing to inform you about the deactivation of your affiliate account on our platform.Please be aware that this action is not reversible, and your access to the affiliate dashboard and associated services will be terminated.';
                        }
                        if ($this->input->post('status', true) == 1 || $this->input->post('status', true) == '1') {
                            $title = 'Congratulations! Your affiliate Account Has Been Approved';
                            $fcm_admin_msg = 'We are delighted to inform you that your application to become an approved affiliate on our platform has been successful! Congratulations on this significant milestone.';
                            $mail_admin_msg = 'We are delighted to inform you that your application to become an approved affiliate on our platform has been successful! Congratulations on this significant milestone.With your approval, you gain access to a range of exclusive features and tools that will help you manage your business effectively. Our platform is designed to empower affiliates like you, providing all the necessary resources to enhance your success.';
                        }
                        if ($this->input->post('status', true) == 2 || $this->input->post('status', true) == '2') {
                            $title = 'Update on Your affiliate Account Application';
                            $fcm_admin_msg = 'We hope this message finds you well. We wanted to take a moment to inform you about the status of your recent affiliate account application with ' . $system_settings['app_name'];
                            $mail_admin_msg = 'We hope this message finds you well. We wanted to take a moment to inform you about the status of your recent affiliate account application with ' . $system_settings['app_name'] . 'We appreciate your interest in becoming a affiliate on our platform and thank you for taking the time to submit your application. We understand that starting your journey as a affiliate requires dedication and effort, and we value your commitment to becoming part of our growing community.';
                        }
                        $affiliate_fcm = fetch_details('users', ['id' => $this->input->post('edit_affiliate_user')], 'fcm_id,email,username,platform_type');
                        // Step 1: Group by platform
                        $groupedByPlatform = [];
                        foreach ($affiliate_fcm as $item) {
                            $platform = $item['platform_type'];
                            $groupedByPlatform[$platform][] = $item['fcm_id'];
                        }

                        // Step 2: Chunk each platform group into arrays of 1000
                        $fcm_ids = [];
                        foreach ($groupedByPlatform as $platform => $fcmIds) {
                            $fcm_ids[$platform] = array_chunk($fcmIds, 1000);
                        }

                        $affiliate_fcm_id[0] = $affiliate_fcm[0]['fcm_id'];

                        $registrationIDs_chunks = $fcm_ids;
                        $firebase_project_id = $this->data['firebase_project_id'];
                        $service_account_file = $this->data['service_account_file'];
                        $email_settings = get_settings('email_settings', true);

                        if (!empty($affiliate_fcm_id) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                            $fcmMsg = array(
                                'title' => $title,
                                'body' => $fcm_admin_msg,
                                'type' => "affiliate_account_update",
                            );
                            send_notification($fcmMsg, $registrationIDs_chunks, $fcmMsg);
                        }
                        if (isset($email_settings) && !empty($email_settings)) {
                            $email_message = array(
                                'username' => 'Hello, Dear <b>' . ucfirst($affiliate_fcm[0]['username']) . '</b>, ',
                                'subject' => $title,
                                'email' => $affiliate_fcm[0]['email'],
                                'message' => $mail_admin_msg
                            );
                            send_mail($affiliate_fcm[0]['email'], $title, $this->load->view('admin/pages/view/contact-email-template', $email_message, TRUE));
                        }
                    }

                    $fullname = $this->input->post('full_name', true);

                    $affiliate_data = array(
                        'user_id' => $this->input->post('edit_affiliate_user', true),
                        'edit_affiliate_data_id' => $this->input->post('edit_affiliate_data_id', true),
                        'uuid' => $this->input->post('affiliate_uuid', true),
                        'website_url' => $this->input->post('my_website', true),
                        'mobile_app_url' => $this->input->post('my_app', true),
                        'status' => $this->input->post('status', true),
                        'commission_type' => 'percentage',

                    );
                    $affiliate_profile = array(
                        'username' => $fullname,
                        'email' => $this->input->post('email', true),
                        'mobile' => $this->input->post('mobile', true),
                        'address' => $this->input->post('address', true),
                        'is_affiliate_user' => $this->input->post('is_affiliate_user', true),
                    );

                    if ($this->affiliate_model->add_affiliate($affiliate_data, $affiliate_profile)) {
                        $this->response['error'] = false;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $message = 'Affiliate User Updated Successfully';
                        $this->response['message'] = $message;
                        print_r(json_encode($this->response));
                    } else {
                        $this->response['error'] = true;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['message'] = "Affiliate data was not updated";
                        print_r(json_encode($this->response));
                    }
                } else {

                    $name = $this->input->post('full_name'); // or 'first_name'
                    $identity_column = $this->config->item('identity', 'ion_auth');
                    $email = strtolower($this->input->post('email'));
                    $mobile = $this->input->post('mobile');
                    $identity = ($identity_column == 'mobile') ? $mobile : $email;
                    $password = $this->input->post('password');
                    $address = $this->input->post('address');


                    $additional_data = array(
                        'username' => $name,
                        'email' => $email,
                        'mobile' => $mobile,
                        'password' => $password,
                        'address' => $address,
                        'type' => 'phone',
                        'is_affiliate_user' => 1,
                    );
                    // print_r($additional_data);
                    $this->ion_auth->register($identity, $password, $email, $additional_data, ['5']);

                    if (update_details(['active' => 1], [$identity_column => $identity], 'users')) {
                        $user_id = fetch_details('users', ['mobile' => $mobile], 'id')[0]['id'];

                        $affiliate_id = generate_unique_affiliate_uuid($user_id);

                        $affiliate_data = array(
                            'user_id' => $user_id,
                            'uuid' => $affiliate_id,
                            'website_url' => $this->input->post('my_website', true),
                            'mobile_app_url' => $this->input->post('my_app', true),
                            'status' => $this->input->post('status', true),
                            'commission_type' => 'percentage',
                        );

                        $insert_id = $this->affiliate_model->add_affiliate($affiliate_data);
                        if (!empty($insert_id)) {
                            $this->response['error'] = false;
                            $this->response['csrfName'] = $this->security->get_csrf_token_name();
                            $this->response['csrfHash'] = $this->security->get_csrf_hash();
                            $this->response['message'] = 'Affiliate User Added Successfully';
                            print_r(json_encode($this->response));
                        } else {
                            $this->response['error'] = true;
                            $this->response['csrfName'] = $this->security->get_csrf_token_name();
                            $this->response['csrfHash'] = $this->security->get_csrf_hash();
                            $this->response['message'] = "Affiliate data was not added";
                            print_r(json_encode($this->response));
                        }
                    } else {
                        $this->response['error'] = true;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $message = (isset($_POST['edit_affiliate_user'])) ? 'Affiliate User not Updated' : 'Affiliate User not Added.';
                        $this->response['message'] = $message;
                        print_r(json_encode($this->response));
                    }
                }
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function remove_affiliate()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (print_msg(!has_permissions('delete', 'affiliate_users'), PERMISSION_ERROR_MSG, 'affiliate_users', false)) {
                return true;
            }

            if (!isset($_GET['id']) && empty($_GET['id'])) {
                $this->response['error'] = true;
                $this->response['message'] = 'Affiliate id is required';
                print_r(json_encode($this->response));
                return;
                exit();
            }
            $all_status = [0, 1, 2, 7];
            $status = $this->input->get('status', true);

            $id = $this->input->get('id', true);
            if (!in_array($status, $all_status)) {
                $this->response['error'] = true;
                $this->response['message'] = 'Invalid status';
                print_r(json_encode($this->response));
                return;
                exit();
            }
            if ($status == 2) {
                $this->response['error'] = true;
                $this->response['message'] = 'Please approve affiliate first for delete only affiliate.';
                print_r(json_encode($this->response));
                return;
                exit();
            }

            if ($status == 7) {
                update_details(['status' => $status], ['user_id' => $id], 'affiliates');
                $this->response['error'] = false;
                $this->response['message'] = 'Your account removal request processed at this time.There are pending wallet balances or unsettled orders associated with your account. Please ensure all transactions are completed before proceeding with the account deletion request.';
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


    public function delete_affiliate()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('delete', 'affiliate_users'), PERMISSION_ERROR_MSG, 'affiliate_users', false)) {
                return true;
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }
}

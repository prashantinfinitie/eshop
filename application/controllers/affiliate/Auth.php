<?php
class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language']);
        $this->load->model('affiliate_model');
        $this->lang->load('auth');
    }

    public function index()
    {
        if (!$this->ion_auth->logged_in() && !$this->ion_auth->is_affiliate_user()) {
            $this->data['main_page'] = FORMS . 'login';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Affiliate Login Panel | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Affiliate Login Panel | ' . $settings['app_name'];
            $this->data['logo'] = get_settings('logo');
            $this->data['app_name'] = $settings['app_name'];
            $identity = $this->config->item('identity', 'ion_auth');
            if (empty($identity)) {
                $identity_column = 'text';
            } else {
                $identity_column = $identity;
            }
            $this->data['identity_column'] = $identity_column;
            $this->load->view('affiliate/login', $this->data);
        } else if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {
            redirect('affiliate/home', 'refresh');
        } else if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            redirect('admin/home', 'refresh');
        }
    }

    public function sign_up()
    {
        $this->load->model('category_model');
        $this->data['main_page'] = FORMS . 'affiliate-registration';
        $this->data["categories"] = fetch_details('categories', "status = 1");

        $settings = get_settings('system_settings', true);
        $shipping_method = get_settings('shipping_method', true);
        $this->data['title'] = 'Sign Up Affiliate | ' . $settings['app_name'];
        $this->data['meta_description'] = 'Sign Up Affiliate | ' . $settings['app_name'];
        $this->data['logo'] = get_settings('logo');
        $this->data['shipping_method'] = $shipping_method;

        $this->load->view('affiliate/login', $this->data);
    }

    public function add_user()
    {

        $this->form_validation->set_rules('full_name', 'Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|numeric|xss_clean|min_length[5]|max_length[16]');
        $this->form_validation->set_rules('email', 'email', 'trim|required|valid_email|xss_clean|min_length[5]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
        $this->form_validation->set_rules('confirm_password', 'Confirm password', 'trim|required|matches[password]|xss_clean');

        $this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean');
        $this->form_validation->set_rules('my_website', 'Website', 'trim|required|xss_clean');
        $this->form_validation->set_rules('my_app', 'App', 'trim|required|xss_clean');

        
        if (!$this->form_validation->run()) {

            $this->response['error'] = true;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = validation_errors();
            print_r(json_encode($this->response));
            return false;
        } else {
            if (!$this->form_validation->is_unique($_POST['mobile'], 'users.mobile') || !$this->form_validation->is_unique($_POST['email'], 'users.email')) {
                $response["error"]   = true;
                $response["message"] = "Email or mobile already exists !";
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                $response["data"] = array();
                echo json_encode($response);
                return false;
            }

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
                    $affiliate_user_id = fetch_details('affiliates', ['id' => $insert_id]);
                    $affiliate_id = fetch_details('users', ['id' => $affiliate_user_id[0]['user_id']]);

                    //find admin email 
                    $user_group = fetch_details('users_groups', ['group_id' => 1], '*');
                    $admin_id = fetch_details('users', ['id' => $user_group[0]['user_id']], 'email,username');

                    if (!empty($admin_id[0]['email'])) {
                        $title = "affiliate registered Successfully in your plateform Please check";
                        $mail_admin_msg = 'Congratulations , We hope this message finds you well. We are writing to inform you about the registrer of affiliate account on your platform.Please be aware that this action is not reversible, Please conect with us.';
                        $email_message = array(
                            'username' => 'Hello, Dear <b>' . ucfirst($admin_id[0]['username']) . '</b>, ',
                            'subject' => $title,
                            'email' => $admin_id[0]['email'],
                            'message' => $mail_admin_msg
                        );
                        send_mail($admin_id[0]['email'],  $title, $this->load->view('admin/pages/view/contact-email-template', $email_message, TRUE));
                    }


                    $title = "affiliate registered Successfully. Wait for approval of admin.";
                    $mail_admin_msg = 'Congratulations , We hope this message finds you well. We are writing to inform you about the registrer of your affiliate account on our platform.Please be aware that this action is not reversible, Please conect with us and wait for admin approval for your account.';
                    $email_message = array(
                        'username' => 'Hello, Dear <b>' . ucfirst($affiliate_id[0]['username']) . '</b>, ',
                        'subject' => $title,
                        'email' => $affiliate_id[0]['email'],
                        'message' => $mail_admin_msg
                    );

                    send_mail($affiliate_id[0]['email'],  $title, $this->load->view('admin/pages/view/contact-email-template', $email_message, TRUE));

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

    public function verify_account()
    {
        $identity_column = $this->config->item('identity', 'ion_auth');
        $identity = $this->input->post('identity', true);
        $this->form_validation->set_rules('identity', 'Mobile', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
        if ($this->form_validation->run()) {
            $res = $this->db->select('id,mobile,username')->where($identity_column, $identity)->get('users')->result_array();
            if (!empty($res)) {
                // exiting user  
                if ($this->ion_auth_model->in_group('affiliate', $res[0]['id'])) {
                    // already affiliate
                    $response['error'] = false;
                    $response['csrfName'] = $this->security->get_csrf_token_name();
                    $response['csrfHash'] = $this->security->get_csrf_hash();
                    $response['message'] = "This user is already affiliate user please do login";
                    $response['data'] = array();
                    $response['redirect'] = 1;
                    echo json_encode($response);
                } else {
                    // already user
                    $this->session->set_flashdata('to_be_affiliate_name', $res[0]['username']);
                    $this->session->set_flashdata('to_be_affiliate_mobile', $res[0]['mobile']);
                    $this->session->set_flashdata('to_be_affiliate_id', $res[0]['id']);
                    $response['error'] = false;
                    $response['csrfName'] = $this->security->get_csrf_token_name();
                    $response['csrfHash'] = $this->security->get_csrf_hash();
                    $response['message'] = "Already user";
                    $response['data'] = array();
                    $response['redirect'] = 3;
                    echo json_encode($response);
                }
            } else {
                // no user
                $response['error'] = true;
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                $response['message'] = "redirect to new registration";
                $response['data'] = array();
                $response['redirect'] = 5;
                echo json_encode($response);
            }
        } else {
            $response['error'] = true;
            $response['csrfName'] = $this->security->get_csrf_token_name();
            $response['csrfHash'] = $this->security->get_csrf_hash();
            $response['message'] = validation_errors();
            $response['data'] = array();
            $response['redirect'] = 0;
            echo json_encode($response);
        }
    }
}

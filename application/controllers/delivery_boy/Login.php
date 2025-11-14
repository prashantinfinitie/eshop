<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language']);
        $this->load->model(['Delivery_boy_model', 'Area_model']);

        $this->lang->load('auth');
    }

    public function index()
    {
        if (!$this->ion_auth->logged_in() && !$this->ion_auth->is_delivery_boy()) {
            $this->data['main_page'] = FORMS . 'login';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Delivery Boy Login Panel | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Delivery Boy Login Panel | ' . $settings['app_name'];
            $this->data['app_name'] = $settings['app_name'];
            $this->data['logo'] = get_settings('logo');
            $identity = $this->config->item('identity', 'ion_auth');
            if (empty($identity)) {
                $identity_column = 'text';
            } else {
                $identity_column = $identity;
            }

            $this->data['identity_column'] = $identity_column;
            $this->load->view('delivery_boy/login', $this->data);
        } else if ($this->ion_auth->logged_in() && $this->ion_auth->is_delivery_boy()) {
            redirect('delivery_boy/home', 'refresh');
        } else if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            redirect('admin/home', 'refresh');
        }
    }

    public function sign_up()
    {


        $this->data['main_page'] = FORMS . 'delivery_boy-registration';
        $settings = get_settings('system_settings', true);
        $this->data['title'] = 'Sign Up Delivery | ' . $settings['app_name'];
        $this->data['meta_description'] = 'Sign Up Delivery | ' . $settings['app_name'];
        $this->data['logo'] = get_settings('logo');

        $this->data['fetched_data'] = $this->db->select(' u.* ')
            ->join('users_groups ug', ' ug.user_id = u.id ')
            ->where(['ug.group_id' => '3'])
            ->get('users u')
            ->result_array();

        $this->data['shipping_method'] = get_settings('shipping_method', true);
        $this->data['system_settings'] = get_settings('system_settings', true);
        $this->data['cities'] = fetch_details('cities', "", 'name,id', '5');
        $this->load->view('delivery_boy/login', $this->data);
    }

    public function create_delivery_boy()
    {

        $regex = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";

        $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', 'Mail', 'trim|required|xss_clean');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|xss_clean|min_length[5]|max_length[16]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

        $this->form_validation->set_rules('confirm_password', 'Confirm password', 'trim|required|matches[password]|xss_clean');
        $this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean');


        if (isset($_POST['pincode_wise_deliverability']) && !empty($_POST['pincode_wise_deliverability']) && ($_POST['pincode_wise_deliverability'] == 1)) {
            $this->form_validation->set_rules('serviceable_zipcodes[]', 'Serviceable Zipcodes', 'trim|required|xss_clean');
        }
        if (isset($_POST['city_wise_deliverability']) && !empty($_POST['city_wise_deliverability']) && ($_POST['city_wise_deliverability'] == 1)) {
            $this->form_validation->set_rules('serviceable_cities[]', 'Serviceable Cities', 'trim|required|xss_clean');
        }

        if (!isset($_POST['edit_delivery_boy'])) {
            if (isset($_FILES) && !empty($_FILES) && count($_FILES['driving_license']['name']) < 2) {
                $this->form_validation->set_rules('driving_license', 'driving license', 'trim|required|xss_clean', array('required' => 'Please add front and back image of Driving license'));
            }
            if (isset($_FILES) && !empty($_FILES) && count($_FILES['driving_license']['name']) > 2) {
                $this->form_validation->set_rules('driving_license', 'driving license', 'trim|required|xss_clean', array('required' => 'You can only choose two images'));
            }
        }

        if (isset($_POST['edit_delivery_boy'])) {
            $delivery_boy_data = fetch_details('users', ['id' => $_POST['edit_delivery_boy']], 'driving_license');
            $driving_license = explode(',', $delivery_boy_data[0]['driving_license']);
        }

        if (isset($_POST['edit_delivery_boy'])) {
            if (isset($_FILES) && !empty($_FILES) && !empty($_FILES['driving_license']['name'][0]) && count($_FILES['driving_license']['name']) < 2) {

                $this->form_validation->set_rules('driving_license', 'driving license', 'trim|required|xss_clean', array('required' => 'Please add front and back image of Driving license'));
            } elseif (isset($driving_license) && !empty($driving_license[0]) && $driving_license[0] != 'NULL') {

                if (count($driving_license) < 2) {

                    $this->form_validation->set_rules('driving_license', 'driving license', 'trim|required|xss_clean', array('required' => 'Please add front and back image of Driving license'));
                }
            }
            if (isset($_FILES) && !empty($_FILES) && !empty($_FILES['driving_license']['name'][0]) && count($_FILES['driving_license']['name']) > 2) {
                $this->form_validation->set_rules('driving_license', 'driving license', 'trim|required|xss_clean', array('required' => 'You can only choose two images'));
            } elseif (isset($driving_license) && !empty($driving_license[0]) && count($driving_license) > 2) {
                $this->form_validation->set_rules('driving_license', 'driving license', 'trim|required|xss_clean', array('required' => 'You can only choose two images'));
            }
        }

        if (!preg_match($regex, $_POST['password'])) {
            $this->response['error'] = true;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = 'please enter strong password';
            print_r(json_encode($this->response));
            return;
        }

        if (!$this->form_validation->run()) {

            $this->response['error'] = true;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = validation_errors();
            print_r(json_encode($this->response));
        } else {
            // upload driving license

            if (!file_exists(FCPATH . DELIVERY_BOY_DOCUMENTS_PATH)) {
                mkdir(FCPATH . DELIVERY_BOY_DOCUMENTS_PATH, 0777);
            }

            $temp_array = array();
            $files = $_FILES;
            $images_new_name_arr = array();
            $images_info_error = "";
            $allowed_media_types = implode('|', allowed_media_types());
            $config = [
                'upload_path' =>  FCPATH . DELIVERY_BOY_DOCUMENTS_PATH,
                'allowed_types' => $allowed_media_types,
                'max_size' => 8000,
            ];

            if (isset($files['driving_license']) && !empty($files['driving_license']['name'][0]) && isset($files['driving_license']['name'][0])) {
                $other_image_cnt = count((array)$files['driving_license']['name']);
                $other_img = $this->upload;
                $other_img->initialize($config);


                if (isset($_POST['edit_delivery_boy']) && !empty($_POST['edit_delivery_boy']) && isset($delivery_boy_data[0]['driving_license']) && !empty($delivery_boy_data[0]['driving_license'])) {
                    $old_logo = explode('/', $delivery_boy_data[0]['driving_license']);

                    delete_images(DELIVERY_BOY_DOCUMENTS_PATH, $old_logo[4]);
                }
                for ($i = 0; $i < $other_image_cnt; $i++) {

                    if (!empty($_FILES['driving_license']['name'][$i])) {

                        $_FILES['temp_image']['name'] = $files['driving_license']['name'][$i];
                        $_FILES['temp_image']['type'] = $files['driving_license']['type'][$i];
                        $_FILES['temp_image']['tmp_name'] = $files['driving_license']['tmp_name'][$i];
                        $_FILES['temp_image']['error'] = $files['driving_license']['error'][$i];
                        $_FILES['temp_image']['size'] = $files['driving_license']['size'][$i];
                        if (!$other_img->do_upload('temp_image')) {
                            $images_info_error = 'driving license :' . $images_info_error . ' ' . $other_img->display_errors();
                        } else {
                            $temp_array = $other_img->data();
                            resize_review_images($temp_array, FCPATH . DELIVERY_BOY_DOCUMENTS_PATH);
                            $images_new_name_arr[$i] = DELIVERY_BOY_DOCUMENTS_PATH . $temp_array['file_name'];
                        }
                    } else {
                        $_FILES['temp_image']['name'] = $files['driving_license']['name'][$i];
                        $_FILES['temp_image']['type'] = $files['driving_license']['type'][$i];
                        $_FILES['temp_image']['tmp_name'] = $files['driving_license']['tmp_name'][$i];
                        $_FILES['temp_image']['error'] = $files['driving_license']['error'][$i];
                        $_FILES['temp_image']['size'] = $files['driving_license']['size'][$i];
                        if (!$other_img->do_upload('temp_image')) {
                            $images_info_error = $other_img->display_errors();
                        }
                    }
                }
                //Deleting Uploaded attachments if any overall error occured
                if ($images_info_error != NULL || !$this->form_validation->run()) {
                    if (isset($images_new_name_arr) && !empty($images_new_name_arr || !$this->form_validation->run())) {

                        foreach ($images_new_name_arr as $key => $val) {
                            if (file_exists(FCPATH . DELIVERY_BOY_DOCUMENTS_PATH . $images_new_name_arr[$key])) {
                                unlink(FCPATH . DELIVERY_BOY_DOCUMENTS_PATH . $images_new_name_arr[$key]);
                            }
                        }
                    }
                }
            }


            if ($images_info_error != NULL) {
                $this->response['error'] = true;
                $this->response['message'] =  $images_info_error;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
                return false;
            }
            if (!isset($_POST['edit_delivery_boy'])) {

                if (!$this->form_validation->is_unique($_POST['mobile'], 'users.mobile') || !$this->form_validation->is_unique($_POST['email'], 'users.email')) {
                    $response["error"]   = true;
                    $response["message"] = "Email or mobile already exists !";
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

                if (isset($_POST['serviceable_zipcodes']) && !empty($_POST['serviceable_zipcodes'])) {
                    $serviceable_zipcodes = implode(",", $this->input->post('serviceable_zipcodes', true));
                } else {
                    $serviceable_zipcodes = NULL;
                }

                if (isset($_POST['serviceable_cities']) && !empty($_POST['serviceable_cities'])) {
                    $serviceable_cities = implode(",", $this->input->post('serviceable_cities', true));
                } else {
                    $serviceable_cities = NULL;
                }

                $additional_data = [
                    'username' => $this->input->post('name'),
                    'address' => $this->input->post('address'),
                    'serviceable_zipcodes' => $serviceable_zipcodes,
                    'serviceable_cities' => $serviceable_cities,
                    'type' => 'phone',
                    'driving_license' => implode(',', $images_new_name_arr),
                    'country_code' => isset($_POST['country_code']) ? str_replace('+', '', $this->input->post('country_code')) : 0,

                ];

                $insert_id = $this->ion_auth->register($identity, $password, $email, $additional_data, ['3']);
                if (!empty($insert_id)) {
                    $delivery_boy_id = fetch_details('users', ['id' => $insert_id]);
                    $title = "Delivery Boy registered Successfully. Wait for approval of admin.";
                    $mail_admin_msg = 'Congratulations , We hope this message finds you well. We are writing to inform you about the registrer of your delivery boy account on our platform.Please be aware that this action is not reversible, Please conect with us and wait for admin approval for your account.';
                    $email_message = array(
                        'username' => 'Hello, Dear <b>' . ucfirst($delivery_boy_id[0]['username']) . '</b>, ',
                        'subject' => $title,
                        'email' => $delivery_boy_id[0]['email'],
                        'message' => $mail_admin_msg
                    );
                    send_mail($delivery_boy_id[0]['email'],  $title, $this->load->view('admin/pages/view/contact-email-template', $email_message, TRUE));


                    //send mail to admin
                    $user_group = fetch_details('users_groups', ['group_id' => 1], '*');
                    $admin_id = fetch_details('users', ['id' => $user_group[0]['user_id']], 'email,username');

                    if (!empty($admin_id[0]['email'])) {
                        $title = "Delivery Boy registeredlat Successfully in your plateform Please check";
                        $mail_admin_msg = 'Congratuions , We hope this message finds you well. We are writing to inform you about the registrer of delivery boy account on your platform.Please be aware that this action is not reversible, Please conect with us.';
                        $email_message = array(
                            'username' => 'Hello, Dear <b>' . ucfirst($admin_id[0]['username']) . '</b>, ',
                            'subject' => $title,
                            'email' => $admin_id[0]['email'],
                            'message' => $mail_admin_msg
                        );
                        send_mail($admin_id[0]['email'],  $title, $this->load->view('admin/pages/view/contact-email-template', $email_message, TRUE));
                    }
                }
                update_details(['active' => 1], [$identity_column => $identity], 'users');
            }

            $this->response['error'] = false;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $message = 'Delivery Boy registered Successfully. Wait for approval of admin.';
            $this->response['message'] = $message;
            print_r(json_encode($this->response));
        }
    }

    public function update_user()
    {

        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            $this->response['error'] = true;
            $this->response['message'] = DEMO_VERSION_MSG;
            echo json_encode($this->response);
            return false;
            exit();
        }

        $identity_column = $this->config->item('identity', 'ion_auth');
        $identity = $this->session->userdata('identity');
        $regex_password = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
        $user = $this->ion_auth->user()->row();
        if ($identity_column == 'email') {
            $this->form_validation->set_rules('email', 'Email', 'required|xss_clean|trim|valid_email|edit_unique[users.email.' . $user->id . ']');
        } else {
            $this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean|trim|numeric|edit_unique[users.mobile.' . $user->id . ']');
        }
        $this->form_validation->set_rules('username', 'Username', 'required|xss_clean|trim');

        if (!empty($_POST['old']) || !empty($_POST['new']) || !empty($_POST['new_confirm'])) {
            $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required|xss_clean');
            $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|xss_clean|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required|xss_clean');

            if (!preg_match($regex_password, $_POST['new'])) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'please enter strong password';
                print_r(json_encode($this->response));
                return;
            }
        }

        $tables = $this->config->item('tables', 'ion_auth');
        if (!$this->form_validation->run()) {
            if (validation_errors()) {
                $response['error'] = true;
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                $response['message'] = validation_errors();
                echo json_encode($response);
                return false;
                exit();
            }
            if ($this->session->flashdata('message')) {
                $response['error'] = false;
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                $response['message'] = $this->session->flashdata('message');
                echo json_encode($response);
                return false;
                exit();
            }
        } else {

            if (!empty($_POST['old']) || !empty($_POST['new']) || !empty($_POST['new_confirm'])) {
                if (!$this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'))) {
                    $response['error'] = true;
                    $response['csrfName'] = $this->security->get_csrf_token_name();
                    $response['csrfHash'] = $this->security->get_csrf_hash();
                    $response['message'] = $this->ion_auth->errors();
                    echo json_encode($response);
                    return;
                    exit();
                }
            }


            // process images of profile

            if (!file_exists(FCPATH . USER_IMG_PATH)) {
                mkdir(FCPATH . USER_IMG_PATH, 0777);
            }

            //process Profile Image
            $temp_array_profile = $profile_doc = array();
            $profile_files = $_FILES;
            $profile_error = "";
            $config = [
                'upload_path' => FCPATH . USER_IMG_PATH,
                'allowed_types' => 'jpg|png|jpeg|gif',
                'max_size' => 8000,
            ];
            if (isset($profile_files['image']) && !empty($profile_files['image']['name']) && isset($profile_files['image']['name'])) {
                $other_img = $this->upload;
                $other_img->initialize($config);



                if (!empty($profile_files['image']['name'])) {

                    $_FILES['temp_image']['name'] = $profile_files['image']['name'];
                    $_FILES['temp_image']['type'] = $profile_files['image']['type'];
                    $_FILES['temp_image']['tmp_name'] = $profile_files['image']['tmp_name'];
                    $_FILES['temp_image']['error'] = $profile_files['image']['error'];
                    $_FILES['temp_image']['size'] = $profile_files['image']['size'];
                    if (!$other_img->do_upload('temp_image')) {
                        $profile_error = 'Images :' . $profile_error . ' ' . $other_img->display_errors();
                    } else {
                        $temp_array_profile = $other_img->data();
                        resize_review_images($temp_array_profile, FCPATH . USER_IMG_PATH);
                        $profile_doc = USER_IMG_PATH . $temp_array_profile['file_name'];
                    }
                } else {
                    $_FILES['temp_image']['name'] = $profile_files['image']['name'];
                    $_FILES['temp_image']['type'] = $profile_files['image']['type'];
                    $_FILES['temp_image']['tmp_name'] = $profile_files['image']['tmp_name'];
                    $_FILES['temp_image']['error'] = $profile_files['image']['error'];
                    $_FILES['temp_image']['size'] = $profile_files['image']['size'];
                    if (!$other_img->do_upload('temp_image')) {
                        $profile_error = $other_img->display_errors();
                    }
                }
                //Deleting Uploaded Images if any overall error occured
                if ($profile_error != NULL || !$this->form_validation->run()) {
                    if (isset($profile_doc) && !empty($profile_doc || !$this->form_validation->run())) {
                        foreach ($profile_doc as $key => $val) {
                            unlink(FCPATH . USER_IMG_PATH . $profile_doc[$key]);
                        }
                    }
                }
            }

            if ($profile_error != NULL) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = $profile_error;
                print_r(json_encode($this->response));
                return;
            }


            $set = [
                'username' => $this->input->post('username'),
                'email' => $this->input->post('email'),
                'image' => (!empty($profile_doc)) ? $profile_doc : $this->input->post('old_profile_image', true)
            ];
            $set = escape_array($set);
            $this->db->set($set)->where($identity_column, $identity)->update($tables['login_users']);
            $response['error'] = false;
            $response['csrfName'] = $this->security->get_csrf_token_name();
            $response['csrfHash'] = $this->security->get_csrf_hash();
            $response['message'] = 'Profile Update Succesfully';
            echo json_encode($response);
            return;
        }
    }
    public function auth()
    {
        $identity_column = $this->config->item('identity', 'ion_auth');
        $identity = $this->input->post('identity', true);
        $this->form_validation->set_rules('identity', 'Email', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
        $res = $this->db->select('id, status')->where($identity_column, $identity)->get('users')->result_array();

        if ($this->form_validation->run()) {
            if (!empty($res)) {
                if ($this->ion_auth_model->in_group('delivery_boy', $res[0]['id'])) {
                    $remember = (bool)$this->input->post('remember');
                    if ($this->ion_auth->login($this->input->post('identity', true), $this->input->post('password', true), $remember, 'phone')) {
                        if ($res[0]['status'] == 0) {
                            $response['error'] = false;
                            $response['csrfName'] = $this->security->get_csrf_token_name();
                            $response['csrfHash'] = $this->security->get_csrf_hash();
                            $response['message'] = 'Wait for approval of admin.';
                            echo json_encode($response);
                            $this->ion_auth->logout();
                            redirect('delivery_boy/login', 'refresh');
                        }
                        //if the login is successful
                        $response['error'] = false;
                        $response['csrfName'] = $this->security->get_csrf_token_name();
                        $response['csrfHash'] = $this->security->get_csrf_hash();
                        $response['message'] = $this->ion_auth->messages();
                        echo json_encode($response);
                    } else {
                        // if the login was un-successful
                        $response['error'] = true;
                        $response['csrfName'] = $this->security->get_csrf_token_name();
                        $response['csrfHash'] = $this->security->get_csrf_hash();
                        $response['message'] = $this->ion_auth->errors();
                        echo json_encode($response);
                    }
                } else {
                    $response['error'] = true;
                    $response['csrfName'] = $this->security->get_csrf_token_name();
                    $response['csrfHash'] = $this->security->get_csrf_hash();
                    $response['message'] = ucfirst($identity_column) . ' field is not correct';
                    echo json_encode($response);
                }
            } else {
                $response['error'] = true;
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                $response['message'] = '' . ucfirst($identity_column) . ' field is not correct';
                echo json_encode($response);
            }
        } else {
            $response['error'] = true;
            $response['csrfName'] = $this->security->get_csrf_token_name();
            $response['csrfHash'] = $this->security->get_csrf_hash();
            $response['message'] = validation_errors();
            echo json_encode($response);
        }
    }


    public function forgot_password()
    {
        $this->data['main_page'] = FORMS . 'forgot-password';
        $settings = get_settings('system_settings', true);
        $this->data['title'] = 'Forgot Password | ' . $settings['app_name'];
        $this->data['meta_description'] = 'Forget Password | ' . $settings['app_name'];
        $this->data['logo'] = get_settings('logo');
        $this->load->view('delivery_boy/login', $this->data);
    }

    public function get_zipcodes()
    {
        $limit = (isset($_GET['limit'])) ? $this->input->post('limit', true) : 25;
        $offset = (isset($_GET['offset'])) ? $this->input->post('offset', true) : 0;
        $search =  (isset($_GET['search'])) ? $_GET['search'] : null;
        $zipcodes = $this->Area_model->get_zipcodes($search, $limit, $offset);
        $this->response['data'] = $zipcodes['data'];
        $this->response['csrfName'] = $this->security->get_csrf_token_name();
        $this->response['csrfHash'] = $this->security->get_csrf_hash();
        print_r(json_encode($this->response));
    }

    public function get_cities()
    {
        $search = $this->input->get('search');
        $response = $this->Area_model->get_cities_list($search);
        echo json_encode($response);
    }
}

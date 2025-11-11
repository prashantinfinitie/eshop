<?php defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language']);

        $this->lang->load('auth');
    }
    public function index()
    {

        if (!$this->ion_auth->logged_in() && !$this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'login';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Login Panel | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Login Panel | ' . $settings['app_name'];
            $this->data['logo'] = get_settings('logo');

            $identity = $this->config->item('identity', 'ion_auth');
            if (empty($identity)) {
                $identity_column = 'text';
            } else {
                $identity_column = $identity;
            }
            $this->data['identity_column'] = $identity_column;
            $this->load->view('admin/login', $this->data);
        } else {
            if ($this->session->has_userdata('url')) {
                $url = $this->session->userdata('url');
                $this->session->unset_userdata('url');
                redirect('admin/home', 'refresh');
                // redirect($url, 'refresh');
            } else {
                if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller()) {
                    redirect('seller/home', 'refresh');
                } else if ($this->ion_auth->logged_in() && $this->ion_auth->is_delivery_boy()) {
                    redirect('delivery_boy/home', 'refresh');
                } else {
                    redirect('admin/home', 'refresh');
                }
            }
        }
    }

    public function forgot_password()
    {
        $this->data['main_page'] = FORMS . 'forgot-password';
        $settings = get_settings('system_settings', true);
        $this->data['title'] = 'Forgot Password | ' . $settings['app_name'];
        $this->data['meta_description'] = 'Forget Password | ' . $settings['app_name'];
        $this->data['logo'] = get_settings('logo');
        $this->load->view('admin/login', $this->data);
    }

    public function update_user()
    {
        if (print_msg(!has_permissions('update', 'profile'), PERMISSION_ERROR_MSG, 'profile')) {
            return false;
        }
        if (defined('SEMI_DEMO_MODE') && SEMI_DEMO_MODE == 0) {
            $this->response['error'] = true;
            $this->response['message'] = SEMI_DEMO_MODE_MSG;
            echo json_encode($this->response);
            return false;
            exit();
        }
        $identity_column = $this->config->item('identity', 'ion_auth');
        // $identity = $this->session->userdata('identity');
        $user_id = $_SESSION['user_id'];
        $identity_col = fetch_details('users', ['id' => $user_id], ['mobile', 'email']);

        $identity = $identity_col[0]['mobile'];
        $user = $this->ion_auth->user()->row();
        if ($identity_column == 'email') {
            $this->form_validation->set_rules('email', 'Email', 'required|xss_clean|trim|valid_email|edit_unique[users.email.' . $user->id . ']');
        } else {
            $this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean|trim|numeric|edit_unique[users.mobile.' . $user->id . ']');
        }
        $this->form_validation->set_rules('username', 'Username', 'required|xss_clean|trim');
        $this->form_validation->set_rules('address', 'address', 'required|xss_clean');
        $this->form_validation->set_rules('latitude', 'latitude', 'required|xss_clean');
        $this->form_validation->set_rules('longitude', 'longitude', 'required|xss_clean');
        $old = $this->input->post('old', true);
        $new = $this->input->post('new', true);
        $new_confirm = $this->input->post('new_confirm', true);
        if (!empty($old) || !empty($new) || !empty($new_confirm)) {
            $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required|xss_clean');
            $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|xss_clean|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required|xss_clean');
        }

        $tables = $this->config->item('tables', 'ion_auth');

        $regex_latitude = "/^([+-]?(90(\.0+)?|[1-8]?\d(\.\d+)?))$/";
        $regex_longitude = "/^([+-]?(180(\.0+)?|1[0-7]\d(\.\d+)?|[1-9]?\d(\.\d+)?))$/";

        $latitude = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';

        if (!preg_match($regex_latitude, $latitude)) {
            $this->response['error'] = true;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = 'Please enter a valid latitude.';
            print_r(json_encode($this->response));
            return;
        }

        if (!preg_match($regex_longitude, $longitude)) {
            $this->response['error'] = true;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = 'Please enter a valid longitude.';
            print_r(json_encode($this->response));
            return;
        }


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
            if (!empty($old) || !empty($new) || !empty($new_confirm)) {
                if (!$this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'))) {
                    // if the login was un-successful
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
                'mobile' => $this->input->post('mobile'),
                'address' => $this->input->post('address'),
                'latitude' => $this->input->post('latitude'),
                'longitude' => $this->input->post('longitude'),
                'image' => (!empty($profile_doc)) ? $profile_doc : $this->input->post('old_profile_image', true),
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


    public function reset_password($code = NULL)
    {
        if (!$code) {
            redirect(base_url());
        }
        $this->data['user'] = $this->ion_auth->forgotten_password_check($code);
        if ($this->data['user']) {
            $settings = get_settings('system_settings', true);
            $this->data['main_page'] = FORMS . 'reset_password';
            $this->data['title'] = 'Reset Password |' . $settings['app_name'];
            $this->data['meta_description'] = 'Reset Password |' . $settings['app_name'];
            $this->data['logo'] = get_settings('logo');
            $this->load->view('admin/login', $this->data);
        } else {
            redirect(base_url('admin/login/forgot_password'), 'refresh');
        }
    }
}

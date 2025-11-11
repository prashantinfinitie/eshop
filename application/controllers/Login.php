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
        $this->data['is_logged_in'] = ($this->ion_auth->logged_in()) ? 1 : 0;
        $this->data['user'] = ($this->ion_auth->logged_in()) ? $this->ion_auth->user()->row() : array();
        $this->response['csrfName'] = $this->security->get_csrf_token_name();
        $this->response['csrfHash'] = $this->security->get_csrf_hash();
        $this->data['settings'] = get_settings('system_settings', true);
    }

    public function login_check()
    {
        if (!$this->ion_auth->logged_in()) {
            $this->data['main_page'] = 'home';
            $this->data['title'] = 'Login Panel | ' . $this->data['settings']['app_name'];
            $this->data['meta_description'] = 'Login Panel | ' . $this->data['settings']['app_name'];

            $identity_column = $this->config->item('identity', 'ion_auth');
            if ($identity_column == 'mobile') {
                $this->form_validation->set_rules('mobile', 'Mobile', 'trim|numeric|required|xss_clean');
            } elseif ($identity_column == 'email') {
                $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');
            } else {
                $this->form_validation->set_rules('identity', 'Identity', 'trim|required|xss_clean');
            }
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

            $login = $this->ion_auth->login($this->input->post('mobile'), $this->input->post('password'));
            if ($login) {
                $data = fetch_details('users', ['mobile' => $this->input->post('mobile', true)]);
                $username = $this->session->set_userdata('username', $data[0]['username']);
                $this->response['error'] = false;
                $this->response['message'] = 'Login Succesfully';
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                echo json_encode($this->response);
                return false;
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Mobile Number or Password is wrong.';
                echo json_encode($this->response);
                return false;
            }
        } else {
            $this->response['error'] = true;
            $this->response['message'] = 'You are already logged in.';
            echo json_encode($this->response);
            return false;
        }
    }

    public function logout()
    {
        $this->ion_auth->logout();
        $this->response['error'] = true;
        $this->response['message'] = 'Logout successful.';
        $this->response['csrfName'] = $this->security->get_csrf_token_name();
        $this->response['csrfHash'] = $this->security->get_csrf_hash();
        echo json_encode($this->response);
        return false;
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
        $old = $this->input->post('old', true);
        $new = $this->input->post('new', true);
        $new_confirm = $this->input->post('new_confirm', true);
        if (!empty($old) || !empty($new) || !empty($new_confirm)) {
            $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required|xss_clean');
            $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|xss_clean|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required|xss_clean');
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

            if (!file_exists(FCPATH . USER_IMG_PATH)) {
                mkdir(FCPATH . USER_IMG_PATH, 0777);
            }

            $temp_array = array();
            $files = $_FILES;
            $images_new_name_arr = array();
            $images_info_error = "";
            $allowed_media_types = implode('|', allowed_media_types());
            $config = [
                'upload_path' =>  FCPATH . USER_IMG_PATH,
                'allowed_types' => $allowed_media_types,
                'max_size' => 8000,
            ];

            if (!empty($_FILES['profile_image']['name'][0]) && isset($_FILES['profile_image']['name'])) {
                $other_image_cnt = count($_FILES['profile_image']['name']);
                $other_img = $this->upload;
                $other_img->initialize($config);

                for ($i = 0; $i < $other_image_cnt; $i++) {

                    if (!empty($_FILES['profile_image']['name'][$i])) {

                        $_FILES['temp_image']['name'] = $files['profile_image']['name'][$i];
                        $_FILES['temp_image']['type'] = $files['profile_image']['type'][$i];
                        $_FILES['temp_image']['tmp_name'] = $files['profile_image']['tmp_name'][$i];
                        $_FILES['temp_image']['error'] = $files['profile_image']['error'][$i];
                        $_FILES['temp_image']['size'] = $files['profile_image']['size'][$i];
                        if (!$other_img->do_upload('temp_image')) {
                            $images_info_error = 'profile_image :' . $images_info_error . ' ' . $other_img->display_errors();
                        } else {
                            $temp_array = $other_img->data();
                            resize_review_images($temp_array, FCPATH . USER_IMG_PATH);
                            $images_new_name_arr[$i] = USER_IMG_PATH . $temp_array['file_name'];
                        }
                    } else {
                        $_FILES['temp_image']['name'] = $files['profile_image']['name'][$i];
                        $_FILES['temp_image']['type'] = $files['profile_image']['type'][$i];
                        $_FILES['temp_image']['tmp_name'] = $files['profile_image']['tmp_name'][$i];
                        $_FILES['temp_image']['error'] = $files['profile_image']['error'][$i];
                        $_FILES['temp_image']['size'] = $files['profile_image']['size'][$i];
                        if (!$other_img->do_upload('temp_image')) {
                            $images_info_error = $other_img->display_errors();
                        }
                    }
                }
                //Deleting Uploaded attachments if any overall error occured
                if ($images_info_error != NULL || !$this->form_validation->run()) {
                    if (isset($images_new_name_arr) && !empty($images_new_name_arr || !$this->form_validation->run())) {
                        foreach ($images_new_name_arr as $key => $val) {
                            unlink(FCPATH . USER_IMG_PATH . $images_new_name_arr[$key]);
                        }
                    }
                }
            }
            if ($images_info_error != NULL) {
                $this->response['error'] = true;
                $this->response['message'] =  $images_info_error;
                print_r(json_encode($this->response));
                return false;
            }

            $set = [
                'username' => $this->input->post('username'),
                'email' => $this->input->post('email'),
                'mobile' => $this->input->post('mobile'),
                'image' => (isset($images_new_name_arr[0]) && !empty($images_new_name_arr[0])) ? $images_new_name_arr[0] : $_POST['user_profile_image'],
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
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller{

    public function __construct()
    {
        return parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language']);
        $this->load->model(['Shipping_company_model', 'Area_model']);
    }

    public function index()
    {
        if (!$this->ion_auth->logged_in() && !$this->ion_auth->is_shipping_company()) {
            $this->data['main_page'] = FORMS . 'login';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Shipping Company Login Panel | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Shipping Company Login Panel | ' . $settings['app_name'];
            $this->data['app_name'] = $settings['app_name'];
            $this->data['logo'] = get_settings('logo');
            $identity = $this->config->item('identity', 'ion_auth');
            if (empty($identity)) {
                $identity_column = 'text';
            } else {
                $identity_column = $identity;
            }

            $this->data['identity_column'] = $identity_column;
            $this->load->view('shipping_company/login', $this->data);
        } else if ($this->ion_auth->logged_in() && $this->ion_auth->is_shipping_company()) {
            redirect('shipping_company/home', 'refresh');
        } else if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            redirect('admin/home', 'refresh');
        }
    }


    public function sign_up()
    {


        $this->data['main_page'] = FORMS . 'shipping-company-registration';
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

    // Login
    public function auth()
    {
        // Only allow POST
        if ($this->input->method() !== 'post') {
            show_404();
        }

        $identity_column = $this->config->item('identity', 'ion_auth');

        // -------- Form Validation --------
        $this->form_validation->set_rules('identity', ucfirst($identity_column), 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

        if (!$this->form_validation->run()) {
            return $this->_json_response(true, validation_errors());
        }

        // -------- Fetch User --------
        $identity = $this->input->post('identity', true);

        $user = $this->db->select('id, status')
            ->where($identity_column, $identity)
            ->limit(1)
            ->get('users')
            ->row_array();

        if (empty($user)) {
            return $this->_json_response(true, ucfirst($identity_column) . ' field is not correct');
        }

        // -------- Check Group: shipping_company --------
        if (!$this->ion_auth_model->in_group('shipping_company', $user['id'])) {
            return $this->_json_response(true, 'You are not registered as a shipping company user');
        }

        // -------- Attempt Login --------
        $remember = (bool) $this->input->post('remember');

        if (!$this->ion_auth->login($identity, $this->input->post('password', true), $remember, $identity_column)) {
            // Login failed
            return $this->_json_response(true, $this->ion_auth->errors());
        }

        // -------- Check Approval Status --------
        if ((int) $user['status'] === 0) {
            $this->ion_auth->logout();
            return $this->_json_response(true, 'Wait for admin approval.');
        }

        // -------- Success --------
        return $this->_json_response(false, $this->ion_auth->messages());
    }

    /**
     * Helper to send JSON response with CSRF
     */
    private function _json_response($error, $message)
    {
        $response = [
            'error'    => (bool) $error,
            'message'  => $message,
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }



}

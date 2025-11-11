<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SMS_gateway_settings extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper', 'sms_helper']);
        $this->load->model(['Setting_model', 'notification_model', 'category_model', 'custom_sms_model']);
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (!has_permissions('read', 'sms-gateway-settings')) {
                $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
                redirect('admin/home', 'refresh');
            }
            $this->data['main_page'] = FORMS . 'sms-gateway-settings';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'SMS Gateway Settings | ' . $settings['app_name'];
            $this->data['meta_description'] = ' SMS Gateway Settings  | ' . $settings['app_name'];
            $this->data['sms_gateway_settings'] = get_settings('sms_gateway_settings', true);
            $this->data['send_notification_settings'] = get_settings('send_notification_settings', true);
            $this->data['notification_modules'] = $this->config->item('notification_modules');
            if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {
                $this->data['fetched_data'] = fetch_details('custom_sms', ['id' => $_GET['edit_id']]);
            }
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function add_sms_data()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (!has_permissions('read', 'sms-gateway-settings')) {
                $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
                redirect('admin/home', 'refresh');
            }
            if (defined('SEMI_DEMO_MODE') && SEMI_DEMO_MODE == 0) {
                $this->response['error'] = true;
                $this->response['message'] = SEMI_DEMO_MODE_MSG;
                echo json_encode($this->response);
                return false;
                exit();
            }
            if (print_msg(!has_permissions('update', 'sms-gateway-settings'), PERMISSION_ERROR_MSG, 'sms-gateway-settings')) {
                return false;
            }
            $sms_data['base_url'] = (isset($_POST['base_url']) && !empty(($_POST['base_url']))) ? $this->input->post('base_url', true) : "";
            $sms_data['sms_gateway_method'] = (isset($_POST['sms_gateway_method']) && !empty(($_POST['sms_gateway_method']))) ? $this->input->post('sms_gateway_method', true) : "";
            $sms_data['header_key'] = (isset($_POST['header_key']) && !empty(($_POST['header_key']))) ? $this->input->post('header_key', true) : [];
            $sms_data['header_value'] = (isset($_POST['header_value']) && !empty(($_POST['header_value']))) ? $this->input->post('header_value', true) : [];
            $sms_data['text_format_data'] = (isset($_POST['text_format_data']) && !empty(($_POST['text_format_data']))) ? $this->input->post('text_format_data', true) : "";
            $sms_data['body_key'] = (isset($_POST['body_key']) && !empty(($_POST['body_key']))) ? $this->input->post('body_key', true) : [];
            $sms_data['body_value'] = (isset($_POST['body_value']) && !empty(($_POST['body_value']))) ? $this->input->post('body_value', true) : [];
            $sms_data['params_key'] = (isset($_POST['params_key']) && !empty(($_POST['params_key']))) ? $this->input->post('params_key', true) : [];
            $sms_data['params_value'] = (isset($_POST['params_value']) && !empty(($_POST['params_value']))) ? $this->input->post('params_value', true) : [];

            $this->Setting_model->update_smsgateway($sms_data);
            $this->response['error'] = false;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = 'System Setting Updated Successfully';
            print_r(json_encode($this->response));
        }
    }

    public function update_notification_module()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (!has_permissions('read', 'sms-gateway-settings')) {
                $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
                redirect('admin/home', 'refresh');
            }
            if (defined('SEMI_DEMO_MODE') && SEMI_DEMO_MODE == 0) {
                $this->response['error'] = true;
                $this->response['message'] = SEMI_DEMO_MODE_MSG;
                echo json_encode($this->response);
                return false;
                exit();
            }

            $this->Setting_model->update_notification_setting($_POST);
            $this->response['error'] = false;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = (isset($edit_id)) ? ' Data Updated Successfully' : 'Data Added Successfully';

            print_r(json_encode($this->response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    function test_sms()
    {
        $this->data['main_page'] = 'test';
        return $this->load->view('front-end/' . THEME . '/template');

    }
}

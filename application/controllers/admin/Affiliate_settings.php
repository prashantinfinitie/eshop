<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Affiliate_settings extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model(['Setting_model', 'category_model']);
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = AFFILIATE . 'settings';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Affiliate Settings | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Affiliate Settings | ' . $settings['app_name'];
            $this->data['categories'] = $this->category_model->get_categories();

            $this->data['affiliate_commissions'] = fetch_details('categories', ['affiliate_commission !=' => 0.00], '*');

            $this->data['affiliate_settings'] = get_settings('affiliate_settings', true);
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function update_affiliate_settings()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('update', 'settings'), PERMISSION_ERROR_MSG, 'settings')) {
                return false;
            }
            if (defined('SEMI_DEMO_MODE') && SEMI_DEMO_MODE == 0) {
                $this->response['error'] = true;
                $this->response['message'] = SEMI_DEMO_MODE_MSG;
                echo json_encode($this->response);
                return false;
                exit();
            }
            // $this->form_validation->set_rules('account_maintainance_fees', 'Account Maintenance Fees', 'trim|required|numeric|xss_clean');
            $this->form_validation->set_rules('account_delete_days', 'Account Delete Days', 'trim|required|numeric|xss_clean');
            $this->form_validation->set_rules('max_amount_for_withwrawal_req', 'Max Amount for Withdrawal Request', 'trim|required|numeric|xss_clean');
            $this->form_validation->set_rules('min_amount_for_withwrawal_req', 'Min Amount for Withdrawal Request', 'trim|required|numeric|xss_clean');
            
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                $this->Setting_model->update_affiliate_setting($this->input->post(null, true));
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'Affiliate Setting Updated Successfully';
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function update_commission()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (print_msg(!has_permissions('update', 'settings'), PERMISSION_ERROR_MSG, 'settings')) {
                return false;
            }
            if (defined('SEMI_DEMO_MODE') && SEMI_DEMO_MODE == 0) {
                $this->response['error'] = true;
                $this->response['message'] = SEMI_DEMO_MODE_MSG;
                echo json_encode($this->response);
                return false;
                exit();
            }
            $this->form_validation->set_rules('category_parent[]', 'Category', 'trim|required|xss_clean');
            $this->form_validation->set_rules('commission[]', 'Commission', 'trim|required|numeric|xss_clean|greater_than[0]|less_than[100]');

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                $category_parents = $this->input->post('category_parent[]');
                $commissions = $this->input->post('commission[]');

                $combined_data = [];
                if (is_array($category_parents) && is_array($commissions)) {
                    foreach ($category_parents as $index => $category_id) {
                        $combined_data[] = [
                            'category_id' => $category_id,
                            'commission' => isset($commissions[$index]) ? $commissions[$index] : 0
                        ];
                    }
                }

                // Set commission for submitted categories
                foreach ($combined_data as $item) {
                    $set = ['affiliate_commission' => floatval($item['commission']), 'is_in_affiliate' => 1];
                    $where = ['id' => $item['category_id']];
                    update_details($set, $where, 'categories');
                }

                // Set commission to 0 for all categories not in the submitted list
                if (!empty($category_parents)) {
                    $this->db->where_not_in('id', $category_parents);
                    $this->db->update('categories', ['affiliate_commission' => 0, 'is_in_affiliate' => 0]);
                }

                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'Affiliate Commission Updated Successfully';
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }
}

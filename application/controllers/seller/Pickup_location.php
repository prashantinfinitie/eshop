<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pickup_location extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper', 'file']);
        $this->load->model('Pickup_location_model');
    }

    public function manage_pickup_locations()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller() && ($this->ion_auth->seller_status() == 1 || $this->ion_auth->seller_status() == 0)) {

            $this->data['main_page'] = TABLES . 'manage-pickup_location';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Pickup location Management | ' . $settings['app_name'];
            $this->data['meta_description'] = ' Pickup location Management  | ' . $settings['app_name'];
            if (isset($_GET['edit_id'])) {
                $this->data['fetched_data'] = fetch_details('pickup_locations', ['id' => $_GET['edit_id']]);
            }
            $this->load->view('seller/template', $this->data);
        } else {
            redirect('seller/login', 'refresh');
        }
    }

    public function add_pickup_location()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller() && ($this->ion_auth->seller_status() == 1 || $this->ion_auth->seller_status() == 0)) {



            $this->form_validation->set_rules('pickup_location', ' Pickup Location ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('name', ' Name ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('email', ' Email ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('phone', ' Phone ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('city', ' City ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('state', ' State ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('country', ' Country ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('pincode', ' Pincode ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('address', ' Address ', 'trim|required|xss_clean');
            $this->form_validation->set_rules('address2', ' Address 2 ', 'trim|xss_clean');
            $this->form_validation->set_rules('latitude', ' Latitude ', 'trim|numeric|xss_clean');
            $this->form_validation->set_rules('longitude', ' Longitude ', 'trim|numeric|xss_clean');


            if (!$this->form_validation->run()) {

                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                $fields = [
                    'pickup_location', 'name', 
                    'email', 'phone', 'city', 'state', 'country', 'pincode', 'address', 
                    'address2', 'latitude', 'longitude'
                ];
                
                foreach ($fields as $field) {
                    $pickup_location[$field] = $this->input->post($field, true) ?? "";
                }
                $pickup_location['seller_id'] = $this->session->userdata('user_id');
                $result = $this->Pickup_location_model->add_pickup_location($pickup_location);
                
                $this->response['error'] = (isset($result) && !empty($result) && $result['error'] == '1') ? true : false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $message = (isset($_POST['edit_pickup_location'])) ? 'Update Pickup Location' : 'Add Pickup Location';
                $this->response['message'] = (isset($result['message']) && !empty($result['message'])) ? $result['message'] : $message;
                print_r(json_encode($this->response));
            }
        } else {
            redirect('seller/login', 'refresh');
        }
    }
    public function get_areas()
    {
        if ($this->ion_auth->logged_in()) {
            $this->form_validation->set_rules('city_id', 'City Id', 'trim|required|xss_clean');
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
                return false;
            }

            $city_id = $this->input->post('city_id', true);
            $areas = fetch_details('areas', ['city_id' => $city_id]);
            if (empty($areas)) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = "No Areas found for this City.";
                print_r(json_encode($this->response));
                return false;
            }
            $this->response['error'] = false;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['data'] = $areas;
            print_r(json_encode($this->response));
            return false;
        } else {
            $this->response['error'] = true;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = 'Unauthorized access is not allowed';
            print_r(json_encode($this->response));
            return false;
        }
    }

    public function view_pickup_location()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller() && ($this->ion_auth->seller_status() == 1 || $this->ion_auth->seller_status() == 0)) {
            return $this->Pickup_location_model->get_list($table = 'pickup_locations', NULL, $this->session->userdata('user_id'));
        } else {
            redirect('seller/login', 'refresh');
        }
    }
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language']);
        $this->load->model('Seller_model');
        $this->lang->load('auth');
    }

    public function index()
    {
        if (!$this->ion_auth->logged_in() && !$this->ion_auth->is_seller()) {
            $this->data['main_page'] = FORMS . 'login';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Seller Login Panel | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Seller Login Panel | ' . $settings['app_name'];
            $this->data['logo'] = get_settings('logo');
            $this->data['app_name'] = $settings['app_name'];
            $identity = $this->config->item('identity', 'ion_auth');
            if (empty($identity)) {
                $identity_column = 'text';
            } else {
                $identity_column = $identity;
            }
            $this->data['identity_column'] = $identity_column;
            $this->load->view('seller/login', $this->data);
        } else if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller()) {
            redirect('seller/home', 'refresh');
        } else if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            redirect('admin/home', 'refresh');
        }
    }

    public function sign_up()
    {
        $this->load->model('category_model');
        $this->data['main_page'] = FORMS . 'seller-registration';
        $this->data["categories"] = fetch_details('categories', "status = 1");

        $settings = get_settings('system_settings', true);
        $shipping_method = get_settings('shipping_method', true);
        $this->data['title'] = 'Sign Up Seller | ' . $settings['app_name'];
        $this->data['meta_description'] = 'Sign Up Seller | ' . $settings['app_name'];
        $this->data['logo'] = get_settings('logo');
        $this->data['shipping_method'] = $shipping_method;

        if (isset($_SESSION['to_be_seller_name']) && !empty($_SESSION['to_be_seller_name']) && isset($_SESSION['to_be_seller_mobile']) && !empty($_SESSION['to_be_seller_mobile']) && isset($_SESSION['to_be_seller_id']) && !empty($_SESSION['to_be_seller_id'])) {
            $this->data['title'] = 'Update Seller | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Update Seller | ' . $settings['app_name'];
            $this->data['user_data'] = $_SESSION;
        }
        $this->load->view('seller/login', $this->data);
    }

    public function create_seller()
    {
        if (!isset($_POST['user_id'])) {
            $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|xss_clean|min_length[5]');
            $this->form_validation->set_rules('email', 'Mail', 'trim|required|xss_clean');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('confirm_password', 'Confirm password', 'trim|required|matches[password]|xss_clean');
            $this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean');
        } else {
            $this->form_validation->set_rules('user_name', 'Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('user_mobile', 'Mobile', 'trim|required|xss_clean|min_length[5]');
        }
        $this->form_validation->set_rules('store_name', 'Store Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('tax_name', 'Tax Name', 'trim|xss_clean');
        $this->form_validation->set_rules('tax_number', 'Tax Number', 'trim|xss_clean');
        $this->form_validation->set_rules('store_logo', 'Store Logo', 'trim|xss_clean');
        $this->form_validation->set_rules('national_identity_card', 'National Identity Card', 'trim|xss_clean');
        $this->form_validation->set_rules('authorized_signature', 'Authorized Signature', 'trim|xss_clean');
        $this->form_validation->set_rules('address_proof', 'Address Proof', 'trim|xss_clean');
        $this->form_validation->set_rules('deliverable_zipcode_type', 'Deliverable zipcode type', 'trim|xss_clean');
        $this->form_validation->set_rules('deliverable_city_type', 'Deliverable city type', 'trim|xss_clean');
        $this->form_validation->set_rules('low_stock_limit', 'Low Stock Limit', 'trim|numeric|xss_clean');
        $shipping_method = get_settings('shipping_method', true);
        $regex_password = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";

        if (isset($shipping_method['pincode_wise_deliverability']) && !empty($shipping_method['pincode_wise_deliverability']) && ($shipping_method['pincode_wise_deliverability'] == 1) && isset($shipping_method['local_shipping_method']) &&  $shipping_method['local_shipping_method'] == 1) {
            if (isset($_POST['deliverable_zipcode_type']) && !empty($_POST['deliverable_zipcode_type']) && ($_POST['deliverable_zipcode_type'] == INCLUDED)) {
                $this->form_validation->set_rules('serviceable_zipcodes[]', 'Serviceable Zipcodes', 'trim|required|xss_clean');
            }
        }
        if (isset($shipping_method['city_wise_deliverability']) && !empty($shipping_method['city_wise_deliverability']) && ($shipping_method['city_wise_deliverability'] == 1) && isset($shipping_method['local_shipping_method']) &&  $shipping_method['local_shipping_method'] == 1) {
            if (isset($_POST['deliverable_city_type']) && !empty($_POST['deliverable_city_type']) && ($_POST['deliverable_city_type'] == INCLUDED)) {
                $this->form_validation->set_rules('serviceable_cities[]', 'Serviceable Cities', 'trim|required|xss_clean');
            }
        }

        if (!preg_match($regex_password, $_POST['password'])) {
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

            $selected_categories = ($this->input->post("categories"));

            $categoryArray = explode(',', $selected_categories);
            $result = [];

            foreach ($categoryArray as $index => $catId) {
                $result["category_id[$index]"] = $catId;
                $result["commission[$index]"] = "0"; // Default commission
            }

            $commission_data_1 =  json_encode($result);

            if (!file_exists(FCPATH . SELLER_DOCUMENTS_PATH)) {
                mkdir(FCPATH . SELLER_DOCUMENTS_PATH, 0777);
            }

            //process store logo
            $temp_array_logo = $store_logo_doc = array();
            $logo_files = $_FILES;
            $store_logo_error = "";
            $config = [
                'upload_path' =>  FCPATH . SELLER_DOCUMENTS_PATH,
                'allowed_types' => 'jpg|png|jpeg|gif',
                'max_size' => 8000,
            ];
            if (isset($logo_files['store_logo']) && !empty($logo_files['store_logo']['name']) && isset($logo_files['store_logo']['name'])) {
                $other_img = $this->upload;
                $other_img->initialize($config);

                if (isset($_POST['edit_seller']) && !empty($_POST['edit_seller']) && isset($_POST['old_store_logo']) && !empty($_POST['old_store_logo'])) {
                    $old_logo = explode('/', $this->input->post('old_store_logo', true));
                    delete_images(SELLER_DOCUMENTS_PATH, $old_logo[2]);
                }

                if (!empty($logo_files['store_logo']['name'])) {

                    $_FILES['temp_image']['name'] = $logo_files['store_logo']['name'];
                    $_FILES['temp_image']['type'] = $logo_files['store_logo']['type'];
                    $_FILES['temp_image']['tmp_name'] = $logo_files['store_logo']['tmp_name'];
                    $_FILES['temp_image']['error'] = $logo_files['store_logo']['error'];
                    $_FILES['temp_image']['size'] = $logo_files['store_logo']['size'];
                    if (!$other_img->do_upload('temp_image')) {
                        $store_logo_error = 'Images :' . $store_logo_error . ' ' . $other_img->display_errors();
                    } else {
                        $temp_array_logo = $other_img->data();
                        resize_review_images($temp_array_logo, FCPATH . SELLER_DOCUMENTS_PATH);
                        $store_logo_doc  = SELLER_DOCUMENTS_PATH . $temp_array_logo['file_name'];
                    }
                } else {
                    $_FILES['temp_image']['name'] = $logo_files['store_logo']['name'];
                    $_FILES['temp_image']['type'] = $logo_files['store_logo']['type'];
                    $_FILES['temp_image']['tmp_name'] = $logo_files['store_logo']['tmp_name'];
                    $_FILES['temp_image']['error'] = $logo_files['store_logo']['error'];
                    $_FILES['temp_image']['size'] = $logo_files['store_logo']['size'];
                    if (!$other_img->do_upload('temp_image')) {
                        $store_logo_error = $other_img->display_errors();
                    }
                }
                //Deleting Uploaded Images if any overall error occured
                if ($store_logo_error != NULL || !$this->form_validation->run()) {
                    if (isset($store_logo_doc) && !empty($store_logo_doc || !$this->form_validation->run())) {
                        foreach ($store_logo_doc as $key => $val) {
                            unlink(FCPATH . SELLER_DOCUMENTS_PATH . $store_logo_doc[$key]);
                        }
                    }
                }
            }

            if ($store_logo_error != NULL) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] =  $store_logo_error;
                print_r(json_encode($this->response));
                return;
            }

            //process Authorized Signature
            $temp_array_authorized_signature = $authorized_signature_doc = array();
            $authorized_signature_files = $_FILES;
            $authorized_signature_error = "";
            $config = [
                'upload_path' =>  FCPATH . SELLER_DOCUMENTS_PATH,
                'allowed_types' => 'jpg|png|jpeg|gif',
                'max_size' => 8000,
            ];
            if (isset($authorized_signature_files['authorized_signature']) && !empty($authorized_signature_files['authorized_signature']['name']) && isset($authorized_signature_files['authorized_signature']['name'])) {
                $other_img = $this->upload;
                $other_img->initialize($config);

                if (isset($_POST['edit_seller']) && !empty($_POST['edit_seller']) && isset($_POST['old_authorized_signature']) && !empty($_POST['old_authorized_signature'])) {
                    $old_authorized_signature = explode('/', $this->input->post('old_authorized_signature', true));
                    delete_images(SELLER_DOCUMENTS_PATH, $old_authorized_signature[2]);
                }

                if (!empty($authorized_signature_files['authorized_signature']['name'])) {

                    $_FILES['temp_image']['name'] = $authorized_signature_files['authorized_signature']['name'];
                    $_FILES['temp_image']['type'] = $authorized_signature_files['authorized_signature']['type'];
                    $_FILES['temp_image']['tmp_name'] = $authorized_signature_files['authorized_signature']['tmp_name'];
                    $_FILES['temp_image']['error'] = $authorized_signature_files['authorized_signature']['error'];
                    $_FILES['temp_image']['size'] = $authorized_signature_files['authorized_signature']['size'];
                    if (!$other_img->do_upload('temp_image')) {
                        $authorized_signature_error = 'Images :' . $authorized_signature_error . ' ' . $other_img->display_errors();
                    } else {
                        $temp_array_authorized_signature = $other_img->data();
                        resize_review_images($temp_array_authorized_signature, FCPATH . SELLER_DOCUMENTS_PATH);
                        $authorized_signature_doc  = SELLER_DOCUMENTS_PATH . $temp_array_authorized_signature['file_name'];
                    }
                } else {
                    $_FILES['temp_image']['name'] = $authorized_signature_files['authorized_signature']['name'];
                    $_FILES['temp_image']['type'] = $authorized_signature_files['authorized_signature']['type'];
                    $_FILES['temp_image']['tmp_name'] = $authorized_signature_files['authorized_signature']['tmp_name'];
                    $_FILES['temp_image']['error'] = $authorized_signature_files['authorized_signature']['error'];
                    $_FILES['temp_image']['size'] = $authorized_signature_files['authorized_signature']['size'];
                    if (!$other_img->do_upload('temp_image')) {
                        $authorized_signature_error = $other_img->display_errors();
                    }
                }
                //Deleting Uploaded Images if any overall error occured
                if ($authorized_signature_error != NULL || !$this->form_validation->run()) {
                    if (isset($authorized_signature_doc) && !empty($authorized_signature_doc || !$this->form_validation->run())) {
                        foreach ($authorized_signature_doc as $key => $val) {
                            unlink(FCPATH . SELLER_DOCUMENTS_PATH . $authorized_signature_doc[$key]);
                        }
                    }
                }
            }

            if ($authorized_signature_error != NULL) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] =  $authorized_signature_error;
                print_r(json_encode($this->response));
                return;
            }

            //process national_identity_card
            $temp_array_id_card = $id_card_doc = array();
            $id_card_files = $_FILES;
            $id_card_error = "";
            $config = [
                'upload_path' =>  FCPATH . SELLER_DOCUMENTS_PATH,
                'allowed_types' => 'jpg|png|jpeg|gif',
                'max_size' => 8000,
            ];
            if (isset($id_card_files['national_identity_card']) &&  !empty($id_card_files['national_identity_card']['name']) && isset($id_card_files['national_identity_card']['name'])) {
                $other_img = $this->upload;
                $other_img->initialize($config);

                if (isset($_POST['edit_seller']) && !empty($_POST['edit_seller']) && isset($_POST['old_national_identity_card']) && !empty($_POST['old_national_identity_card'])) {
                    $old_national_identity_card = explode('/', $this->input->post('old_national_identity_card', true));
                    delete_images(SELLER_DOCUMENTS_PATH, $old_national_identity_card[2]);
                }

                if (!empty($id_card_files['national_identity_card']['name'])) {

                    $_FILES['temp_image']['name'] = $id_card_files['national_identity_card']['name'];
                    $_FILES['temp_image']['type'] = $id_card_files['national_identity_card']['type'];
                    $_FILES['temp_image']['tmp_name'] = $id_card_files['national_identity_card']['tmp_name'];
                    $_FILES['temp_image']['error'] = $id_card_files['national_identity_card']['error'];
                    $_FILES['temp_image']['size'] = $id_card_files['national_identity_card']['size'];
                    if (!$other_img->do_upload('temp_image')) {
                        $id_card_error = 'Images :' . $id_card_error . ' ' . $other_img->display_errors();
                    } else {
                        $temp_array_id_card = $other_img->data();
                        resize_review_images($temp_array_id_card, FCPATH . SELLER_DOCUMENTS_PATH);
                        $id_card_doc  = SELLER_DOCUMENTS_PATH . $temp_array_id_card['file_name'];
                    }
                } else {
                    $_FILES['temp_image']['name'] = $id_card_files['national_identity_card']['name'];
                    $_FILES['temp_image']['type'] = $id_card_files['national_identity_card']['type'];
                    $_FILES['temp_image']['tmp_name'] = $id_card_files['national_identity_card']['tmp_name'];
                    $_FILES['temp_image']['error'] = $id_card_files['national_identity_card']['error'];
                    $_FILES['temp_image']['size'] = $id_card_files['national_identity_card']['size'];
                    if (!$other_img->do_upload('temp_image')) {
                        $id_card_error = $other_img->display_errors();
                    }
                }
                //Deleting Uploaded Images if any overall error occured
                if ($id_card_error != NULL || !$this->form_validation->run()) {
                    if (isset($id_card_doc) && !empty($id_card_doc || !$this->form_validation->run())) {
                        foreach ($id_card_doc as $key => $val) {
                            unlink(FCPATH . SELLER_DOCUMENTS_PATH . $id_card_doc[$key]);
                        }
                    }
                }
            }

            if ($id_card_error != NULL) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] =  $id_card_error;
                print_r(json_encode($this->response));
                return;
            }

            //process address_proof
            $temp_array_proof = $proof_doc = array();
            $proof_files = $_FILES;
            $proof_error = "";
            $config = [
                'upload_path' =>  FCPATH . SELLER_DOCUMENTS_PATH,
                'allowed_types' => 'jpg|png|jpeg|gif',
                'max_size' => 8000,
            ];
            if (isset($proof_files['address_proof']) && !empty($proof_files['address_proof']['name']) && isset($proof_files['address_proof']['name'])) {
                $other_img = $this->upload;
                $other_img->initialize($config);

                if (isset($_POST['edit_seller']) && !empty($_POST['edit_seller']) && isset($_POST['old_address_proof']) && !empty($_POST['old_address_proof'])) {
                    $old_address_proof = explode('/', $this->input->post('old_address_proof', true));
                    delete_images(SELLER_DOCUMENTS_PATH, $old_address_proof[2]);
                }

                if (!empty($proof_files['address_proof']['name'])) {

                    $_FILES['temp_image']['name'] = $proof_files['address_proof']['name'];
                    $_FILES['temp_image']['type'] = $proof_files['address_proof']['type'];
                    $_FILES['temp_image']['tmp_name'] = $proof_files['address_proof']['tmp_name'];
                    $_FILES['temp_image']['error'] = $proof_files['address_proof']['error'];
                    $_FILES['temp_image']['size'] = $proof_files['address_proof']['size'];
                    if (!$other_img->do_upload('temp_image')) {
                        $proof_error = 'Images :' . $proof_error . ' ' . $other_img->display_errors();
                    } else {
                        $temp_array_proof = $other_img->data();
                        resize_review_images($temp_array_proof, FCPATH . SELLER_DOCUMENTS_PATH);
                        $proof_doc  = SELLER_DOCUMENTS_PATH . $temp_array_proof['file_name'];
                    }
                } else {
                    $_FILES['temp_image']['name'] = $proof_files['address_proof']['name'];
                    $_FILES['temp_image']['type'] = $proof_files['address_proof']['type'];
                    $_FILES['temp_image']['tmp_name'] = $proof_files['address_proof']['tmp_name'];
                    $_FILES['temp_image']['error'] = $proof_files['address_proof']['error'];
                    $_FILES['temp_image']['size'] = $proof_files['address_proof']['size'];
                    if (!$other_img->do_upload('temp_image')) {
                        $proof_error = $other_img->display_errors();
                    }
                }
                //Deleting Uploaded Images if any overall error occured
                if ($proof_error != NULL || !$this->form_validation->run()) {
                    if (isset($proof_doc) && !empty($proof_doc || !$this->form_validation->run())) {
                        foreach ($proof_doc as $key => $val) {
                            unlink(FCPATH . SELLER_DOCUMENTS_PATH . $proof_doc[$key]);
                        }
                    }
                }
            }

            if ($proof_error != NULL) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] =  $proof_error;
                print_r(json_encode($this->response));
                return;
            }
            if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {

                /* check whether user exist or not */
                $user_id_to_seller = $this->input->post('user_id', true);
                $user = fetch_users($this->input->post('user_id', true));
                if (empty($user)) {
                    $this->response['error'] = true;
                    $this->response['message'] = "User not found!";
                    $response['csrfName'] = $this->security->get_csrf_token_name();
                    $response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['data'] = [];
                    print_r(json_encode($this->response));
                    return false;
                }
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
                $seller_data = array(
                    'user_id' => $this->input->post('user_id', true),
                    'store_logo' => (!empty($store_logo_doc)) ? $store_logo_doc : null,
                    'authorized_signature' => (!empty($authorized_signature_doc)) ? $authorized_signature_doc : $this->input->post('authorized_signature', true),
                    'tax_number' => $this->input->post('tax_number', true),
                    'tax_name' => $this->input->post('tax_name', true),
                    'store_description' => (isset($_POST['store_description']) && !empty($_POST['store_description'])) ? $this->input->post('store_description', true) : "",
                    'store_url' => (isset($_POST['store_url']) && !empty($_POST['store_url'])) ? $this->input->post('store_url', true) : "",
                    'store_name' => (isset($_POST['store_name']) && !empty($_POST['store_name'])) ? $this->input->post('store_name', true) : "",
                    'serviceable_zipcodes' => $serviceable_zipcodes,
                    'serviceable_cities' => $serviceable_cities,
                    'deliverable_zipcode_type' => $this->input->post('deliverable_zipcode_type', true),
                    'deliverable_city_type' => $this->input->post('deliverable_city_type', true),
                    'low_stock_limit' => $this->input->post('low_stock_limit', true) ?? 0,
                    'slug' => create_unique_slug($this->input->post('store_name', true), 'seller_data'),
                    "categories" => $selected_categories
                );

                if ($this->Seller_model->add_seller($seller_data)) {
                    $group_id = $this->ion_auth->get_users_groups($user_id_to_seller)->row()->id;
                    $this->ion_auth->remove_from_group($group_id, $user_id_to_seller);
                    $this->ion_auth->add_to_group('4', $user_id_to_seller);
                    $this->response['error'] = false;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $message = 'Seller Update Successfully';
                    $this->response['message'] = $message;
                    print_r(json_encode($this->response));
                } else {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = "Seller data was not updated";
                    print_r(json_encode($this->response));
                }
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
                $identity_column = $this->config->item('identity', 'ion_auth');
                $email = strtolower($this->input->post('email'));
                $mobile = $this->input->post('mobile');
                $identity = ($identity_column == 'mobile') ? $mobile : $email;
                $password = $this->input->post('password');

                $additional_data = [
                    'username' => $this->input->post('name', true),
                    'address' => $this->input->post('address', true),
                    'latitude' => $this->input->post('latitude', true),
                    'longitude' => $this->input->post('longitude', true),
                    'country_code' => isset($_POST['country_code']) ? str_replace('+', '', $this->input->post('country_code')) : 0,
                    'type' => 'phone',
                ];
                $this->ion_auth->register($identity, $password, $email, $additional_data, ['4']);
                if (update_details(['active' => 1], [$identity_column => $identity], 'users')) {
                    $user_id = fetch_details('users', ['mobile' => $mobile], 'id');

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
                    $data = array(
                        'user_id' => $user_id[0]['id'],
                        'store_logo' => (!empty($store_logo_doc)) ? $store_logo_doc : null,
                        'authorized_signature' => (!empty($authorized_signature_doc)) ? $authorized_signature_doc : null,
                        'tax_number' => $this->input->post('tax_number', true),
                        'tax_name' => $this->input->post('tax_name', true),
                        'store_description' => (isset($_POST['store_description']) && !empty($_POST['store_description'])) ? $this->input->post('store_description', true) : "",
                        'store_url' => (isset($_POST['store_url']) && !empty($_POST['store_url'])) ? $this->input->post('store_url', true) : "",
                        'store_name' => (isset($_POST['store_name']) && !empty($_POST['store_name'])) ? $this->input->post('store_name', true) : "",
                        'deliverable_zipcode_type' => $this->input->post('deliverable_zipcode_type', true),
                        'deliverable_city_type' => $this->input->post('deliverable_city_type', true),
                        'serviceable_zipcodes' => $serviceable_zipcodes,
                        'serviceable_cities' => $serviceable_cities,
                        'low_stock_limit' => $this->input->post('low_stock_limit', true) ?? 0,
                        'slug' => create_unique_slug($this->input->post('store_name', true), 'seller_data'),
                        "categories" => $selected_categories
                    );

                    $com_data = array();
                    $commission_data_1 =  json_encode($result);

                    if (isset($commission_data_1) && !empty($commission_data_1)) {
                        $user_id = fetch_details('users', ['mobile' => $mobile], 'id');

                        // Decode and reformat JSON with indexed keys like "category_id[0]"
                        $commission_data_raw = json_decode($commission_data_1, true);
                        $commission_data = ['category_id' => [], 'commission' => []];

                        foreach ($commission_data_raw as $key => $value) {
                            if (preg_match('/(category_id|commission)\[(\d+)\]/', $key, $matches)) {
                                $field = $matches[1]; // 'category_id' or 'commission'
                                $index = $matches[2]; // numeric index
                                $commission_data[$field][$index] = $value;
                            }
                        }

                        if (is_array($commission_data['category_id'])) {
                            if (count($commission_data['category_id']) >= 2) {
                                $cat_array = array_unique($commission_data['category_id']);
                                foreach ($commission_data['commission'] as $key => $val) {
                                    if (!array_key_exists($key, $cat_array)) {
                                        unset($commission_data['commission'][$key]);
                                    }
                                }
                                $cat_array = array_values($cat_array);
                                $com_array = array_values($commission_data['commission']);

                                for ($i = 0; $i < count($cat_array); $i++) {
                                    $tmp['seller_id'] = $user_id[0]['id'];
                                    $tmp['category_id'] = $cat_array[$i];
                                    $tmp['commission'] = $com_array[$i];
                                    $com_data[] = $tmp;
                                }
                            } else {
                                $com_data[0] = array(
                                    "seller_id" => $user_id[0]['id'],
                                    "category_id" => $commission_data['category_id'][0],
                                    "commission" => $commission_data['commission'][0],
                                );
                            }
                        } else {
                            $com_data[0] = array(
                                "seller_id" => $user_id[0]['id'],
                                "category_id" => $commission_data['category_id'],
                                "commission" => $commission_data['commission'],
                            );
                        }
                    }

                    $insert_id = $this->Seller_model->add_seller($data, com_data: $com_data);
                    if (!empty($insert_id)) {
                        $seller_user_id = fetch_details('seller_data', ['id' => $insert_id]);
                        $seller_id = fetch_details('users', ['id' => $seller_user_id[0]['user_id']]);

                        //find admin email 
                        $user_group = fetch_details('users_groups', ['group_id' => 1], '*');
                        $admin_id = fetch_details('users', ['id' => $user_group[0]['user_id']], 'email,username');

                        if (!empty($admin_id[0]['email'])) {
                            $title = "Seller registered Successfully in your plateform Please check";
                            $mail_admin_msg = 'Congratulations , We hope this message finds you well. We are writing to inform you about the registrer of seller account on your platform.Please be aware that this action is not reversible, Please conect with us.';
                            $email_message = array(
                                'username' => 'Hello, Dear <b>' . ucfirst($admin_id[0]['username']) . '</b>, ',
                                'subject' => $title,
                                'email' => $admin_id[0]['email'],
                                'message' => $mail_admin_msg
                            );
                            send_mail($admin_id[0]['email'],  $title, $this->load->view('admin/pages/view/contact-email-template', $email_message, TRUE));
                        }


                        $title = "Seller registered Successfully. Wait for approval of admin.";
                        $mail_admin_msg = 'Congratulations , We hope this message finds you well. We are writing to inform you about the registrer of your seller account on our platform.Please be aware that this action is not reversible, Please conect with us and wait for admin approval for your account.';
                        $email_message = array(
                            'username' => 'Hello, Dear <b>' . ucfirst($seller_id[0]['username']) . '</b>, ',
                            'subject' => $title,
                            'email' => $seller_id[0]['email'],
                            'message' => $mail_admin_msg
                        );

                        send_mail($seller_id[0]['email'],  $title, $this->load->view('admin/pages/view/contact-email-template', $email_message, TRUE));
                        $this->response['error'] = false;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['message'] = 'Seller registered Successfully. Wait for approval of admin.';
                        print_r(json_encode($this->response));
                    } else {
                        $this->response['error'] = true;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['message'] = "Seller data was not added";
                        print_r(json_encode($this->response));
                    }
                } else {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $message = (isset($_POST['edit_seller'])) ? 'Seller not Updated' : 'Seller not registered.';
                    $this->response['message'] = $message;
                    print_r(json_encode($this->response));
                }
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
                if ($this->ion_auth_model->in_group('seller', $res[0]['id'])) {
                    // already seller
                    $response['error'] = false;
                    $response['csrfName'] = $this->security->get_csrf_token_name();
                    $response['csrfHash'] = $this->security->get_csrf_hash();
                    $response['message'] = "This user is already seller please do login";
                    $response['data'] = array();
                    $response['redirect'] = 1;
                    echo json_encode($response);
                } else {
                    // already user
                    $this->session->set_flashdata('to_be_seller_name', $res[0]['username']);
                    $this->session->set_flashdata('to_be_seller_mobile', $res[0]['mobile']);
                    $this->session->set_flashdata('to_be_seller_id', $res[0]['id']);
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
                $response['redirect'] = 2;
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


    public function update_user()
    {

        $identity_column = $this->config->item('identity', 'ion_auth');
        $identity = $this->session->userdata('identity');
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
            $set = ['username' => $this->input->post('username'), 'email' => $this->input->post('email')];
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
        $res = $this->db->select('id')->where($identity_column, $identity)->get('users')->result_array();
        if ($this->form_validation->run()) {
            if (!empty($res)) {
                if ($this->ion_auth_model->in_group('seller', $res[0]['id'])) {
                    $remember = (bool)$this->input->post('remember');
                    if ($this->ion_auth->login($this->input->post('identity', true), $this->input->post('password', true), $remember, 'phone')) {
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
        $this->data['title'] = 'Forget Password | Seller Panel';
        $this->data['meta_description'] = 'Ekart';
        $this->data['logo'] = get_settings('logo');
        $this->load->view('seller/login', $this->data);
    }
}

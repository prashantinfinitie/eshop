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
        } else if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller() && ($this->ion_auth->seller_status() == 2 || $this->ion_auth->seller_status() == 7)) {
            $this->ion_auth->logout();
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
        } else if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller() && ($this->ion_auth->seller_status() == 1 || $this->ion_auth->seller_status() == 0)) {
            redirect('seller/home', 'refresh');
        } else if ($this->ion_auth->logged_in() && $this->ion_auth->is_delivery_boy()) {
            redirect('delivery_boy/home', 'refresh');
        } else if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            redirect('admin/home', 'refresh');
        }
    }

    public function update_user()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_seller() && ($this->ion_auth->seller_status() == 1 || $this->ion_auth->seller_status() == 0)) {

            if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                $this->response['error'] = true;
                $this->response['message'] = DEMO_VERSION_MSG;
                echo json_encode($this->response);
                return false;
                exit();
            }

            $regex_latitude = "/([+-]?(90(\.0+)?|[1-8]?\d(\.\d+)?))/";
            $regex_longitude = "/([+-]?(180(\.0+)?|1[0-7]\d(\.\d+)?|[1-9]?\d(\.\d+)?))/";
            $regex_password = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
            $identity_column = $this->config->item('identity', 'ion_auth');
            $identity = $this->session->userdata('identity');
            $user = $this->ion_auth->user()->row();
            if ($identity_column == 'email') {
                $this->form_validation->set_rules('email', 'Email', 'required|xss_clean|trim|valid_email');
            } else {
                $this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean|trim|numeric');
            }
            $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('email', 'Mail', 'trim|required|xss_clean');
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|xss_clean|min_length[5]');
            if (!empty($_POST['old']) || !empty($_POST['new']) || !empty($_POST['new_confirm'])) {
                $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required|xss_clean');
                $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|xss_clean|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
                $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required|xss_clean');
            }
            $this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean');
            $this->form_validation->set_rules('store_name', 'Store Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('tax_name', 'Tax Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('tax_number', 'Tax Number', 'trim|required|xss_clean');
            $this->form_validation->set_rules('latitude', 'Latitude', 'trim|required|xss_clean');
            $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required|xss_clean');
            $this->form_validation->set_rules('deliverable_zipcode_type', 'Deliverable zipcode type', 'trim|xss_clean');
            $this->form_validation->set_rules('deliverable_city_type', 'Deliverable city type', 'trim|xss_clean');
            $this->form_validation->set_rules('low_stock_limit', 'Low Stock Limit', 'trim|numeric|xss_clean');

            if (isset($shipping_method['pincode_wise_deliverability']) && !empty($shipping_method['pincode_wise_deliverability']) && ($shipping_method['pincode_wise_deliverability'] == 1) && isset($shipping_method['local_shipping_method']) && $shipping_method['local_shipping_method'] == 1) {
                if (isset($_POST['deliverable_zipcode_type']) && !empty($_POST['deliverable_zipcode_type']) && ($_POST['deliverable_zipcode_type'] == INCLUDED)) {
                    $this->form_validation->set_rules('serviceable_zipcodes[]', 'Serviceable Zipcodes', 'trim|required|xss_clean');
                }
            }
            if (isset($shipping_method['city_wise_deliverability']) && !empty($shipping_method['city_wise_deliverability']) && ($shipping_method['city_wise_deliverability'] == 1) && isset($shipping_method['local_shipping_method']) && $shipping_method['local_shipping_method'] == 1) {
                if (isset($_POST['deliverable_city_type']) && !empty($_POST['deliverable_city_type']) && ($_POST['deliverable_city_type'] == INCLUDED)) {
                    $this->form_validation->set_rules('serviceable_cities[]', 'Serviceable Cities', 'trim|required|xss_clean');
                }
            }

            //seo validation
            $this->form_validation->set_rules('seo_page_title', ' SEO Page Title', 'trim|xss_clean');
            $this->form_validation->set_rules('seo_meta_keywords', 'SEO Meta Keywords', 'trim|xss_clean');
            $this->form_validation->set_rules('seo_meta_description', 'SEO Meta Description', 'trim|xss_clean');
            $this->form_validation->set_rules('seo_og_image', 'SEO Open Graph Image', 'trim|xss_clean');

            if (isset($_POST['new']) && !empty($_POST['new'])) {
                if (!preg_match($regex_password, $_POST['new'])) {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'please enter strong password';
                    print_r(json_encode($this->response));
                    return;
                }
            }

            if (!preg_match($regex_latitude, $_POST['latitude'])) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'please enter valid latitude.';
                print_r(json_encode($this->response));
                return;
            }
            if (!preg_match($regex_longitude, $_POST['longitude'])) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = 'please enter valid longitude.';
                print_r(json_encode($this->response));
                return;
            }

            $this->form_validation->set_rules('status', 'Status', 'trim|xss_clean');

            if (!isset($_POST['edit_seller'])) {
                $this->form_validation->set_rules('store_logo', 'Store Logo', 'trim|xss_clean');
                $this->form_validation->set_rules('authorized_signature', 'Authorized Signature', 'trim|xss_clean');
            }

            if (!$this->form_validation->run()) {

                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {

                // process images of seller

                if (!file_exists(FCPATH . SELLER_DOCUMENTS_PATH)) {
                    mkdir(FCPATH . SELLER_DOCUMENTS_PATH, 0777);
                }

                //process store logo
                $temp_array_logo = $store_logo_doc = array();
                $logo_files = $_FILES;
                $store_logo_error = "";
                $config = [
                    'upload_path' => FCPATH . SELLER_DOCUMENTS_PATH,
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
                            $store_logo_doc = SELLER_DOCUMENTS_PATH . $temp_array_logo['file_name'];
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
                    $this->response['message'] = $store_logo_error;
                    print_r(json_encode($this->response));
                    return;
                }

                //process Authorized Signature

                $temp_array_authorized_signature = $authorized_signature_doc = array();
                $authorized_signature_files = $_FILES;
                $authorized_signature_error = "";
                $config = [
                    'upload_path' => FCPATH . SELLER_DOCUMENTS_PATH,
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
                            $authorized_signature_doc = SELLER_DOCUMENTS_PATH . $temp_array_authorized_signature['file_name'];
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
                    $this->response['message'] = $authorized_signature_error;
                    print_r(json_encode($this->response));
                    return;
                }

                //process national_identity_card
                $temp_array_id_card = $id_card_doc = array();
                $id_card_files = $_FILES;
                $id_card_error = "";
                $config = [
                    'upload_path' => FCPATH . SELLER_DOCUMENTS_PATH,
                    'allowed_types' => 'jpg|png|jpeg|gif',
                    'max_size' => 8000,
                ];
                if (isset($id_card_files['national_identity_card']) && !empty($id_card_files['national_identity_card']['name']) && isset($id_card_files['national_identity_card']['name'])) {
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
                            $id_card_doc = SELLER_DOCUMENTS_PATH . $temp_array_id_card['file_name'];
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
                    $this->response['message'] = $id_card_error;
                    print_r(json_encode($this->response));
                    return;
                }

                //process address_proof
                $temp_array_proof = $proof_doc = array();
                $proof_files = $_FILES;
                $proof_error = "";
                $config = [
                    'upload_path' => FCPATH . SELLER_DOCUMENTS_PATH,
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
                            $proof_doc = SELLER_DOCUMENTS_PATH . $temp_array_proof['file_name'];
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
                    $this->response['message'] = $proof_error;
                    print_r(json_encode($this->response));
                    return;
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
                    'max_size' => 10000,
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

                if (isset($_POST['seo_meta_keywords']) && $_POST['seo_meta_keywords'] != '') {
                    $_POST['seo_meta_keywords'] = json_decode($_POST['seo_meta_keywords'], 1);
                    $seo_meta_keywords = array_column($_POST['seo_meta_keywords'], 'value');
                    $_POST['seo_meta_keywords'] = implode(",", $seo_meta_keywords);
                }

                if (isset($_POST['edit_seller'])) {

                    $seller_data = array(
                        'user_id' => $this->input->post('edit_seller', true),
                        'edit_seller_data_id' => $this->input->post('edit_seller_data_id', true),
                        'store_logo' => (!empty($store_logo_doc)) ? $store_logo_doc : $this->input->post('old_store_logo', true),
                        'authorized_signature' => (!empty($authorized_signature_doc)) ? $authorized_signature_doc : $this->input->post('old_authorized_signature', true),
                        'status' => $this->input->post('status', true),
                        'tax_number' => $this->input->post('tax_number', true),
                        'tax_name' => $this->input->post('tax_name', true),
                        'store_description' => $this->input->post('store_description', true),
                        'store_url' => $this->input->post('store_url', true),
                        'store_name' => $this->input->post('store_name', true),
                        'categories' => 'seller_profile',
                        'deliverable_zipcode_type' => $this->input->post('deliverable_zipcode_type', true),
                        'deliverable_city_type' => $this->input->post('deliverable_city_type', true),
                        'serviceable_zipcodes' => $serviceable_zipcodes,
                        'serviceable_cities' => $serviceable_cities,
                        'low_stock_limit' => $this->input->post('low_stock_limit', true) ?? 0,
                        'seo_page_title' => $this->input->post('seo_page_title', true),
                        'seo_meta_keywords' => $this->input->post('seo_meta_keywords', true),
                        'seo_meta_description' => $this->input->post('seo_meta_description', true),
                        'seo_og_image' => $this->input->post('seo_og_image', true),
                        'slug' => create_unique_slug($this->input->post('store_name', true), 'seller_data')
                    );
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


                    $seller_profile = array(
                        'name' => $this->input->post('name', true),
                        'email' => $this->input->post('email', true),
                        'mobile' => $this->input->post('mobile', true),
                        'address' => $this->input->post('address', true),
                        'latitude' => $this->input->post('latitude', true),
                        'longitude' => $this->input->post('longitude', true),
                        'image' => (!empty($profile_doc)) ? $profile_doc : $this->input->post('old_profile_image', true)
                    );


                    if ($this->Seller_model->add_seller($seller_data, $seller_profile)) {
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
                }
            }
        } else {
            redirect('seller/home', 'refresh');
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
                    $remember = (bool) $this->input->post('remember');
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
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            $this->response['error'] = true;
            $this->response['message'] = DEMO_VERSION_MSG;
            echo json_encode($this->response);
            return false;
            exit();
        }

        $this->data['main_page'] = FORMS . 'forgot-password';
        $settings = get_settings('system_settings', true);
        $this->data['title'] = 'Forgot Password | ' . $settings['app_name'];
        $this->data['meta_description'] = 'Forget Password | ' . $settings['app_name'];
        $this->data['logo'] = get_settings('logo');
        $this->load->view('seller/login', $this->data);
    }
}

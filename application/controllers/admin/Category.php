<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Category extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation', 'upload']);
        $this->load->helper(['url', 'language', 'file']);
        $this->load->model(['category_model']);

        if (!has_permissions('read', 'categories')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        }
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = TABLES . 'manage-category';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Category Management | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Category Management | ' . $settings['app_name'];
            $id = $this->input->get('id', true);
            if (isset($id) && !empty($id)) {
                $this->data['base_category_url'] = base_url() . 'admin/category/category_list?id=' . $id;
            } else {
                $this->data['base_category_url']  = base_url() . 'admin/category/category_list';
            }
            $this->data['category_result'] = $this->category_model->get_categories();
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function get_categories()
    {
        $ignore_status = isset($_GET['ignore_status']) && $_GET['ignore_status'] == 1 ? 1 : '';
        $seller_id = (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) ? $_GET['seller_id'] : 0;
        $response['data'] = $this->data['category_result'] = $this->category_model->get_categories(NULL, '', '', 'row_order', 'ASC', 'true', '', $ignore_status, $seller_id);
        echo json_encode($response);
        return;
    }

    public function get_seller_categories()
    {
        $this->form_validation->set_data($this->input->get());
        $this->form_validation->set_rules('seller_id', 'Seller ID', 'trim|numeric|required|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return;
        } else {
            $seller_id = $this->input->get('seller_id', true);
            $ignore_status = isset($_GET['ignore_status']) && $_GET['ignore_status'] == 1 ? 1 : '';
            $response['data'] = $this->category_model->get_seller_categories($seller_id);
            echo json_encode($response);
            return;
        }
    }


    public function create_category()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'category';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) ? 'Edit Category | ' . $settings['app_name'] : 'Add Category | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Add Category , Create Category | ' . $settings['app_name'];
            if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {
                $this->data['fetched_data'] = fetch_details('categories', ['id' => $_GET['edit_id']]);
            }

            $this->data['categories'] = $this->category_model->get_categories();

            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function category_order()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = TABLES . 'category-order';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Category Order | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Category Order | ' . $settings['app_name'];
            $this->data['categories'] = $this->category_model->get_categories();
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    function delete_category()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (print_msg(!has_permissions('delete', 'categories'), PERMISSION_ERROR_MSG, 'categories')) {
                return false;
            }
            if (defined('SEMI_DEMO_MODE') && SEMI_DEMO_MODE == 0) {
                $this->response['error'] = true;
                $this->response['message'] = SEMI_DEMO_MODE_MSG;
                echo json_encode($this->response);
                return false;
                exit();
            }
            $category_id = $this->input->get('id', true);
            $products = fetch_details('products', ['category_id' => $category_id]);
            $offers = fetch_details('offers', ['type_id' => $category_id, 'type' => 'categories']);
            $sliders = fetch_details('sliders', ['type_id' => $category_id, 'type' => 'categories']);

            //check category assign to product 
            if (isset($products) && !empty($products)) {
                $this->response['error'] = true;
                $this->response['message'] = 'You cannot delete category , please assign another category to product';
                print_r(json_encode($this->response));
                return;
                exit();
            }
            //check category assign to offer 
            if (isset($offers) && !empty($offers)) {
                $this->response['error'] = true;
                $this->response['message'] = 'You cannot delete category , please assign another category n offer';
                print_r(json_encode($this->response));
                return;
                exit();
            }

            //check category assign to slider 
            if (isset($sliders) && !empty($sliders)) {
                $this->response['error'] = true;
                $this->response['message'] = 'You cannot delete category , please assign another category in slider';
                print_r(json_encode($this->response));
                return;
                exit();
            }

            if (delete_details(['id' => $_GET['id']], 'categories') == TRUE) {
                $this->response['error'] = false;
                $this->response['message'] = 'Deleted Succesfully';
                print_r(json_encode($this->response));
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Something Went Wrong';
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function category_list()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            return $this->category_model->get_category_list();
        } else {
            redirect('admin/login', 'refresh');
        }
    }



    public function add_category()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

            if (isset($_POST['edit_category'])) {
                if (print_msg(!has_permissions('update', 'categories'), PERMISSION_ERROR_MSG, 'categories')) {
                    return false;
                }
            } else {
                if (print_msg(!has_permissions('create', 'categories'), PERMISSION_ERROR_MSG, 'categories')) {
                    return false;
                }
            }

            $this->form_validation->set_rules('category_input_name', 'Category Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('banner', 'Banner', 'trim|xss_clean');

            if (isset($_POST['edit_category'])) {

                $this->form_validation->set_rules('category_input_image', 'Image', 'trim|xss_clean');
            } else {
                $this->form_validation->set_rules('category_input_image', 'Image', 'trim|required|xss_clean', array('required' => 'Category image is required'));
            }
            //seo validation
            $this->form_validation->set_rules('seo_page_title', ' SEO Page Title', 'trim|xss_clean');
            $this->form_validation->set_rules('seo_meta_keywords', 'SEO Meta Keywords', 'trim|xss_clean');
            $this->form_validation->set_rules('seo_meta_description', 'SEO Meta Description', 'trim|xss_clean');
            $this->form_validation->set_rules('seo_og_image', 'SEO Open Graph Image', 'trim|xss_clean');

            if (!$this->form_validation->run()) {

                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                $edit_category = $this->input->post('edit_category', true);
                $category_input_name = $this->input->post('category_input_name', true);
                if (isset($edit_category)) {
                    if (is_exist(['name' => $category_input_name], 'categories', $edit_category)) {
                        $response["error"]   = true;
                        $response['csrfName'] = $this->security->get_csrf_token_name();
                        $response['csrfHash'] = $this->security->get_csrf_hash();
                        $response["message"] = "Category Already exist you should use a different name";
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }
                    if (is_exist(['name' => $category_input_name, 'parent_id' => $_POST['category_parent']], 'categories', $edit_category)) {
                        $response["error"]   = true;
                        $response['csrfName'] = $this->security->get_csrf_token_name();
                        $response['csrfHash'] = $this->security->get_csrf_hash();
                        $response["message"] = "This Category Already exist as a child category.";
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }
                }

                if (isset($_POST['seo_meta_keywords']) && $_POST['seo_meta_keywords'] != '') {
                    $_POST['seo_meta_keywords'] = json_decode($_POST['seo_meta_keywords'], 1);
                    $seo_meta_keywords = array_column($_POST['seo_meta_keywords'], 'value');
                    $_POST['seo_meta_keywords'] = implode(",", $seo_meta_keywords);
                }
    
                $fields = [
                    'edit_category',
                    'category_input_name',
                    'category_parent',
                    'category_input_image',
                    'banner',
                    'seo_page_title',
                    'seo_meta_keywords',
                    'seo_meta_description',
                    'seo_og_image'
                ];

                foreach ($fields as $field) {
                    $category[$field] = $this->input->post($field, true) ?? "";
                }
                $this->category_model->add_category($category);
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $message = (isset($edit_category)) ? 'Category Updated Successfully' : 'Category Added Successfully';
                $this->response['message'] = $message;
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function update_category_order()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('update', 'category_order'), PERMISSION_ERROR_MSG, 'category_order', false)) {
                return false;
            }
            $i = 0;
            $temp = array();
            foreach ($_GET['category_id'] as $row) {
                $temp[$row] = $i;
                $data = [
                    'row_order' => $i
                ];
                $data = escape_array($data);
                $this->db->where(['id' => $row])->update('categories', $data);
                $i++;
            }

            $response['error'] = false;
            $response['message'] = 'Category Order Saved !';

            print_r(json_encode($response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function top_category()
    {

        $this->category_model->top_category();
    }

    public function bulk_upload()
    {

        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'category-bulk-upload';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Bulk Upload | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Bulk Upload | ' . $settings['app_name'];

            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function process_bulk_upload()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('create', 'product'), PERMISSION_ERROR_MSG, 'product')) {
                return false;
            }
            $this->form_validation->set_rules('bulk_upload', '', 'xss_clean');
            $this->form_validation->set_rules('type', 'Type', 'trim|required|xss_clean');
            if (empty($_FILES['upload_file']['name'])) {
                $this->form_validation->set_rules('upload_file', 'File', 'trim|required|xss_clean', array('required' => 'Please choose file'));
            }

            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {
                $allowed_mime_type_arr = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv');
                $mime = get_mime_by_extension($_FILES['upload_file']['name']);
                if (!in_array($mime, $allowed_mime_type_arr)) {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'Invalid file format!';
                    print_r(json_encode($this->response));
                    return false;
                }
                $csv = $_FILES['upload_file']['tmp_name'];
                $temp = 0;
                $temp1 = 0;
                $handle = fopen($csv, "r");
                $this->response['message'] = '';
                $type = $this->input->post('type', true);
                if ($type == 'upload') {
                    while (($row = fgetcsv($handle, 10000, ",")) != FALSE) //get row values
                    {
                        if ($temp != 0) {
                            if (empty($row[0])) {
                                $this->response['error'] = true;
                                $this->response['message'] = 'Name is empty at row ' . $temp;
                                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                                print_r(json_encode($this->response));
                                return false;
                            }
                            if (!empty($row[0])) {
                                if (is_exist(['name' => $row[0]], 'categories')) { //check category exist or not
                                    $response["error"]   = true;
                                    $response["message"] = "category Already Exist! Provide another category name at row." . $temp;
                                    $response['csrfName'] = $this->security->get_csrf_token_name();
                                    $response['csrfHash'] = $this->security->get_csrf_hash();
                                    $response["data"] = array();
                                    echo json_encode($response);
                                    return false;
                                }
                            }
                            if (empty($row[1])) {
                                $this->response['error'] = true;
                                $this->response['message'] = 'Image is empty at row ' . $temp;
                                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                                print_r(json_encode($this->response));
                                return false;
                            }
                           
                        }
                        $temp++;
                    }

                    fclose($handle);
                    $handle = fopen($csv, "r");
                    while (($row = fgetcsv($handle, 10000, ",")) != FALSE) //get row vales
                    {
                        if ($temp1 != 0) {
                            $data['name'] = $row[0];
                            $data['slug'] = create_unique_slug($row[0], 'categories');
                            $data['image'] = $row[1];
                            $data['seo_page_title'] = $row[2];
                            $data['seo_meta_keywords'] = $row[3];
                            $data['seo_meta_description'] = $row[4];
                            $data['seo_og_image'] = $row[5];
                            $data['status'] = 1;
                            $this->db->insert('categories', $data);
                        }
                        $temp1++;
                    }
                    fclose($handle);
                    $this->response['error'] = false;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'categories uploaded successfully!';
                    print_r(json_encode($this->response));
                    return false;
                } else { // bulk_update
                    while (($row = fgetcsv($handle, 10000, ",")) != FALSE) //get row vales
                    {
                        if ($temp != 0) {
                            if (empty($row[0])) {
                                $this->response['error'] = true;
                                $this->response['message'] = 'category id is empty at row ' . $temp;
                                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                                print_r(json_encode($this->response));
                                return false;
                            }
                            if (!empty($row[0])) {
                                if (!is_exist(['id' => $row[0]], 'categories')) {
                                    $response["error"]   = true;
                                    $response["message"] = "category is not exist Provide another category id at row." . $temp;
                                    $response['csrfName'] = $this->security->get_csrf_token_name();
                                    $response['csrfHash'] = $this->security->get_csrf_hash();
                                    $response["data"] = array();
                                    echo json_encode($response);
                                    return false;
                                }
                            }
                            if (empty($row[1])) {
                                $this->response['error'] = true;
                                $this->response['message'] = 'Name is empty at row ' . $temp;
                                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                                print_r(json_encode($this->response));
                                return false;
                            }
                            if (empty($row[2])) {
                                $this->response['error'] = true;
                                $this->response['message'] = 'Image is empty at row ' . $temp;
                                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                                print_r(json_encode($this->response));
                                return false;
                            }
                        }
                        $temp++;
                    }
                    fclose($handle);
                    $handle = fopen($csv, "r");
                    while (($row = fgetcsv($handle, 10000, ",")) != FALSE) //get row values
                    {
                        if (
                            $temp1 != 0
                        ) {
                            $category_id = $row[0];
                            $categories = fetch_details('categories', ['id' => $category_id], '*');
                            if (isset($categories[0]) && !empty($categories[0])) {
                                if (!empty($row[1])) {
                                    $data['name'] = $row[1];
                                    $data['slug'] = create_unique_slug($row[1], 'categories');
                                } else {
                                    $data['name'] = $categories[0]['name'];
                                }
                                if (!empty($row[2])) {
                                    $data['image'] = $row[2];
                                } else {
                                    $data['image'] = $categories[0]['image'];
                                }
                                if (!empty($row[3])) {
                                    $data['parent_id'] = $row[3];
                                } else {
                                    $data['parent_id'] = $categories[0]['parent_id'];
                                }
                                if (!empty($row[4])) {
                                    $data['seo_page_title'] = $row[4];
                                } else {
                                    $data['seo_page_title'] = $categories[0]['seo_page_title'];
                                }
                                if (!empty($row[5])) {
                                    $data['seo_meta_keywords'] = $row[5];
                                } else {
                                    $data['seo_meta_keywords'] = $categories[0]['seo_meta_keywords'];
                                }
                                if (!empty($row[6])) {
                                    $data['seo_meta_description'] = $row[6];
                                } else {
                                    $data['seo_meta_description'] = $categories[0]['seo_meta_description'];
                                }
                                if (!empty($row[7])) {
                                    $data['seo_og_image'] = $row[7];
                                } else {
                                    $data['seo_og_image'] = $categories[0]['seo_og_image'];
                                }
                                $this->db->where('id', $row[0])->update('categories', $data);
                            }
                        }
                        $temp1++;
                    }
                    fclose($handle);
                    $this->response['error'] = false;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = 'categories updated successfully!';
                    print_r(json_encode($this->response));
                    return false;
                }
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function category_bulk_dowload()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (!has_permissions('create', 'categories')) {
                print_msg(PERMISSION_ERROR_MSG, 'categories');
                return;
            }

            $filename = 'categories_' . date('Ymd') . '.csv';

            $categories = $this->category_model->get_download_categories();

            $csvHeaders = [
                'category id',
                'category',
                'parent_id',
                'image',
                'seo_page_title',
                'seo_meta_keywords',
                'seo_meta_description',
                'seo_og_image'
            ];

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename);

            $output = fopen('php://output', 'w');
            fputcsv($output, $csvHeaders);

            foreach ($categories as $category) {
                $data = [$category['id'], $category['name'], $category['parent_id'], $category['image'], $category['seo_page_title'], $category['seo_meta_keywords'], $category['seo_meta_description'], $category['seo_og_image']];
                fputcsv($output, $data);
            }

            fclose($output);
        }
    }
}

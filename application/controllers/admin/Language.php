<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Language extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper', 'file']);
        $this->load->model('language_model');

        if (!has_permissions('read', 'settings')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        }
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'languages';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Languages | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Languages  | ' . $settings['app_name'];
            $this->data['settings'] = get_settings('system_settings', true);
            $this->data['languages'] = get_languages();
            $this->data['default_language'] = get_languages('', '', '', '', 1);

            if (isset($_GET['id'])) {
                $this->data['language'] = get_languages($_GET['id']);
            } else {
                $this->data['language'] = get_languages();
            }

            if (empty($this->data['language'])) {
                redirect(base_url('admin/language'), 'refresh');
            }
            $this->data['language'] = $this->data['language'][0];

            $this->data['lang_labels'] = $this->lang->load('web_labels_lang', strtolower($this->data['default_language'][0]['language']), true);
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function create()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('update', 'settings'), PERMISSION_ERROR_MSG, 'settings')) {
                return false;
            }
            if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                $this->response['error'] = true;
                $this->response['message'] = DEMO_VERSION_MSG;
                echo json_encode($this->response);
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                return false;
                exit();
            }
            $this->form_validation->set_rules('language', 'Language', 'trim|required|xss_clean|strtolower|alpha|is_unique[languages.language]|strtolower', array('is_unique' => 'This Language is already exists.'));
            $this->form_validation->set_rules('native_language', 'Native Language', 'trim|required|xss_clean');
            $this->form_validation->set_rules('code', 'Code', 'trim|required|xss_clean|strtolower|is_unique[languages.language]', array('is_unique' => 'This Code is already exists.'));
            $this->form_validation->set_rules('is_rtl', 'RTL', 'trim|xss_clean');
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
                return false;
            }
            $language = $this->input->post('language', true);
            $language = strtolower($language);
            if ($this->language_model->create($this->input->post(null, true))) {
                $langstr_final = "<?php defined('BASEPATH') OR exit('No direct script access allowed');";
                if (!is_dir('./application/language/' . $language . '/')) {
                    mkdir('./application/language/' . $language . '/', 0777, TRUE);
                }
                if (file_exists('./application/language/' . $language . '/web_labels_lang.php')) {
                    delete_files('./application/language/' . $language . '/web_labels_lang.php');
                    write_file('./application/language/' . $language . '/web_labels_lang.php', $langstr_final);
                } else {
                    write_file('./application/language/' . $language . '/web_labels_lang.php', $langstr_final);
                }
                $language_directory = './system/language/' . $language . '/';
                if (!is_dir($language_directory)) {
                    mkdir($language_directory, 0777, true); // Creates the directory recursively
                }

                // Array of files to be created
                $files_to_create = array(
                    'calendar_lang.php',
                    'date_lang.php',
                    'db_lang.php',
                    'email_lang.php',
                    'form_validation_lang.php',
                    'ftp_lang.php',
                    'imglib_lang.php',
                    'migration_lang.php',
                    'number_lang.php',
                    'pagination_lang.php',
                    'profiler_lang.php',
                    'unit_test_lang.php',
                    'upload_lang.php'
                );

                foreach ($files_to_create as $file) {
                    if (!file_exists($language_directory . $file)) {
                        // Create files with empty content
                        write_file($language_directory . $file, "<?php defined('BASEPATH') OR exit('No direct script access allowed');\n\n");
                    }
                }
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = "Language added successfully.";
                print_r(json_encode($this->response));
            } else {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = "Cannot add language.Please try again later.";
                print_r(json_encode($this->response));
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function save()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (print_msg(!has_permissions('update', 'settings'), PERMISSION_ERROR_MSG, 'settings')) {
                return false;
            }
            if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                $this->response['error'] = true;
                $this->response['message'] = DEMO_VERSION_MSG;
                echo json_encode($this->response);
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                return false;
                exit();
            }
            $this->form_validation->set_rules('language_id', 'ID', 'trim|required|xss_clean|numeric');
            $this->form_validation->set_rules('is_rtl', 'RTL', 'trim|xss_clean');
            if (!$this->form_validation->run()) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
                return false;
            }
            $language_id = $this->input->post('language_id', true);
            $language = get_languages($language_id);
            if (empty($language)) {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = "No Language Found.";
                print_r(json_encode($this->response));
                return false;
            }
            $language = $language[0];
            $language_name = strtolower($language['language']);
            $lang = array();
            $langstr = '';
            $data = $this->input->post(null, true);
            foreach ($data as $key => $value) {
                $label_data =  strip_tags($value);
                $label_data = $this->db->escape_str($label_data);
                $label_key = $key;
                $langstr .= "\$lang['" . $label_key . "'] = \"$label_data\";" . "\n";
            }

            $langstr_final = "<?php defined('BASEPATH') OR exit('No direct script access allowed');" . "\n\n\n" . $langstr;
            if (file_exists('./application/language/' . $language_name . '/web_labels_lang.php')) {
                delete_files('./application/language/' . $language_name . '/web_labels_lang.php');
            }

            $data['is_rtl'] = (isset($_POST['is_rtl']) && !empty($_POST['is_rtl'])) ? 1 : 0;

            if (write_file('./application/language/' . $language_name . '/web_labels_lang.php', $langstr_final)) {
                if ($this->language_model->update($data)) {
                    $this->response['error'] = false;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = "Language added successfully.";
                    print_r(json_encode($this->response));
                    return false;
                } else {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] = "This Language file is not writable.";
                    print_r(json_encode($this->response));
                    return false;
                }
            } else {
                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = "This Language file is not writable.";
                echo json_encode($this->response);
                return false;
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function set_default_for_web()
    {

        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                $this->response['error'] = true;
                $this->response['message'] = DEMO_VERSION_MSG;
                echo json_encode($this->response);
                $response['csrfName'] = $this->security->get_csrf_token_name();
                $response['csrfHash'] = $this->security->get_csrf_hash();
                return false;
                exit();
            }
            $data['language_id'] = (isset($_POST['language_id']) && !empty($_POST['language_id'])) ? $this->input->post('language_id', true) : '';
            $data['is_default'] = (isset($_POST['is_default']) && !empty($_POST['is_default'])) ? 1 : 0;

            if ($this->language_model->is_default_for_web($data)) {
                $this->response['error'] = false;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = "Language set successfully.";
                print_r(json_encode($this->response));
                return false;
            }
        }
    }

    public function get_list()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->language_model->get_language_list();
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    // In your LanguageController.php

    public function delete_language()
    {
        // Load necessary model
        $this->load->model('Language_model');

        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            $this->response['error'] = true;
            $this->response['message'] = DEMO_VERSION_MSG;
            echo json_encode($this->response);
            $response['csrfName'] = $this->security->get_csrf_token_name();
            $response['csrfHash'] = $this->security->get_csrf_hash();
            return false;
            exit();
        }
        $this->form_validation->set_rules('id', 'language Id', 'required|trim|xss_clean|numeric');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] = validation_errors();
            print_r(json_encode($this->response));
        } else {

            // Get the language ID from the POST request
            $language_id = $this->input->post('id');
    
            // Call model function to delete the language
            $delete_status = $this->Language_model->delete_language($language_id);
           
            $this->response['error'] = false;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['message'] =  'Language deleted Successfully';
            print_r(json_encode($this->response));
        }
    }
}

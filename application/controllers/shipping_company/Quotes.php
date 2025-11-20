<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Quotes extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'function_helper', 'file']);
        $this->load->model('Shipping_company_quotes_model');

        // require shipping company login
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group('shipping_company')) {
            redirect('shipping_company/auth', 'refresh');
        }
    }

    public function index()
    {
        $company = $this->ion_auth->user()->row();
        $company_id = $company->id;

        $row = $this->db->select('serviceable_zipcodes')->where('id', $company_id)->get('users')->row_array();
        $zipcodes = [];
        if (!empty($row['serviceable_zipcodes'])) {
            $parts = array_filter(array_map('trim', explode(',', $row['serviceable_zipcodes'])));

            // detect if values are numeric ids (IDs => need to fetch the actual zipcode strings)
            $all_numeric = true;
            foreach ($parts as $p) {
                if (!ctype_digit((string)$p)) {
                    $all_numeric = false;
                    break;
                }
            }

            if ($all_numeric) {
                // fetch zipcode strings for given ids
                $this->db->select('zipcode');
                $this->db->where_in('id', $parts);
                $zrows = $this->db->get('zipcodes')->result_array();
                foreach ($zrows as $zr) {
                    $zipcodes[] = $zr['zipcode'];
                }
            } else {
                // already zipcode strings
                $zipcodes = $parts;
            }
        }

        $settings = get_settings('system_settings', true);

        $this->data['main_page'] = TABLES . 'shipping-company-manage-quotes';
        $this->data['title'] = 'Manage Quotes | ' . (isset($settings['app_name']) ? $settings['app_name'] : 'App');
        $this->data['meta_description'] = 'Manage Shipping Quotes';
        $this->data['zipcodes'] = $zipcodes;
        $this->data['currency'] = isset($settings['currency']) ? $settings['currency'] : '';

        $this->load->view('shipping_company/template', $this->data);
    }

    /**
     * bootstrap-table server-side list
     */
    public function list()
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group('shipping_company')) {
            redirect('shipping_company/auth', 'refresh');
        }

        $company = $this->ion_auth->user()->row();
        $company_id = $company->id;

        $offset = intval($this->input->get('offset', true) ?? 0);
        $limit  = intval($this->input->get('limit', true) ?? 10);
        $search = $this->input->get('search', true) ?? '';
        $sort   = $this->input->get('sort', true) ?? 'id';
        $order  = $this->input->get('order', true) ?? 'DESC';

        $result = $this->Shipping_company_quotes_model->list_for_company($company_id, $offset, $limit, $search, $sort, $order);
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }

    /**
     * Get single quote for editing
     */
    public function get($id)
    {
        if (!$this->input->is_ajax_request()) show_404();

        $company = $this->ion_auth->user()->row();
        $company_id = $company->id;

        $quote = $this->Shipping_company_quotes_model->get_quote($id, $company_id);

        if (empty($quote)) {
            $resp = [
                'error' => true,
                'message' => 'Quote not found',
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ];
        } else {
            $resp = [
                'error' => false,
                'data' => $quote,
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ];
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($resp));
    }

    public function create()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $this->form_validation->set_rules('zipcode', 'Zipcode', 'required|trim|xss_clean');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric|trim');
        $this->form_validation->set_rules('eta_text', 'ETA', 'required|trim|xss_clean');

        if (!$this->form_validation->run()) {
            $resp = [
                'error' => true,
                'message' => validation_errors(),
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ];
            print_r(json_encode($resp));
            return;
        }

        $company = $this->ion_auth->user()->row();
        $company_id = $company->id;

        // validate zipcode belongs to company serviceable list (production safety)
        $row = $this->db->select('serviceable_zipcodes')->where('id', $company_id)->get('users')->row_array();
        $allowed = [];
        if (!empty($row['serviceable_zipcodes'])) {
            $parts = array_filter(array_map('trim', explode(',', $row['serviceable_zipcodes'])));

            // detect if values are numeric ids (IDs => need to fetch the actual zipcode strings)
            $all_numeric = true;
            foreach ($parts as $p) {
                if (!ctype_digit((string)$p)) {
                    $all_numeric = false;
                    break;
                }
            }

            if ($all_numeric) {
                // fetch zipcode strings for given ids
                $this->db->select('zipcode');
                $this->db->where_in('id', $parts);
                $zrows = $this->db->get('zipcodes')->result_array();
                foreach ($zrows as $zr) {
                    $allowed[] = $zr['zipcode'];
                }
            } else {
                // already zipcode strings
                $allowed = $parts;
            }
        }

        if (!in_array($this->input->post('zipcode', true), $allowed)) {
            $resp = [
                'error' => true,
                'message' => 'Zipcode not allowed for this company',
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ];
            print_r(json_encode($resp));
            return;
        }

        // Parse additional charges JSON
        $additional_charges = $this->input->post('additional_charges', true);
        $charges_data = null;
        if (!empty($additional_charges)) {
            $decoded = json_decode($additional_charges, true);
            if (json_last_error() === JSON_ERROR_NONE && !empty($decoded)) {
                $charges_data = json_encode($decoded);
            }
        }

        $payload = [
            'shipping_company_id' => $company_id,
            'zipcode' => $this->input->post('zipcode', true),
            'price' => $this->input->post('price', true),
            'eta_text' => $this->input->post('eta_text', true),
            'cod_available' => $this->input->post('cod_available') ? 1 : 0,
            'additional_charges' => $charges_data,
            'is_active' => $this->input->post('is_active', true) ? 1 : 0
        ];

        $id = $this->Shipping_company_quotes_model->create_quote($payload);
        if (!$id) {
            $resp = [
                'error' => true,
                'message' => 'Failed to create quote',
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ];
            print_r(json_encode($resp));
            return;
        }

        $resp = [
            'error' => false,
            'message' => 'Quote created successfully',
            'id' => $id,
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash()
        ];
        print_r(json_encode($resp));
    }

    public function update()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $this->form_validation->set_rules('id', 'Quote ID', 'required|numeric');
        $this->form_validation->set_rules('zipcode', 'Zipcode', 'required|trim|xss_clean');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric|trim');
        $this->form_validation->set_rules('eta_text', 'ETA', 'required|trim|xss_clean');

        if (!$this->form_validation->run()) {
            $resp = [
                'error' => true,
                'message' => validation_errors(),
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ];
            print_r(json_encode($resp));
            return;
        }

        $id = (int)$this->input->post('id', true);
        $company = $this->ion_auth->user()->row();
        $company_id = $company->id;

        // ensure this quote belongs to company
        $existing = $this->Shipping_company_quotes_model->get_quote($id, $company_id);
        if (empty($existing)) {
            $resp = [
                'error' => true,
                'message' => 'Quote not found',
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ];
            print_r(json_encode($resp));
            return;
        }

        // Parse additional charges JSON
        $additional_charges = $this->input->post('additional_charges', true);
        $charges_data = null;
        if (!empty($additional_charges)) {
            $decoded = json_decode($additional_charges, true);
            if (json_last_error() === JSON_ERROR_NONE && !empty($decoded)) {
                $charges_data = json_encode($decoded);
            }
        }

        $payload = [
            'zipcode' => $this->input->post('zipcode', true),
            'price' => $this->input->post('price', true),
            'eta_text' => $this->input->post('eta_text', true),
            'cod_available' => $this->input->post('cod_available') ? 1 : 0,
            'additional_charges' => $charges_data,
            'is_active' => $this->input->post('is_active', true) ? 1 : 0
        ];

        $ok = $this->Shipping_company_quotes_model->update_quote($id, $company_id, $payload);
        $resp = $ok
            ? [
                'error' => false,
                'message' => 'Quote updated successfully',
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
            : [
                'error' => true,
                'message' => 'No changes or update failed',
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ];
        print_r(json_encode($resp));
    }

    public function delete()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $this->form_validation->set_rules('id', 'Quote ID', 'required|numeric');
        if (!$this->form_validation->run()) {
            $resp = [
                'error' => true,
                'message' => validation_errors(),
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ];
            print_r(json_encode($resp));
            return;
        }

        $id = (int)$this->input->post('id', true);
        $company = $this->ion_auth->user()->row();
        $company_id = $company->id;

        $ok = $this->Shipping_company_quotes_model->delete_quote($id, $company_id);
        $resp = $ok
            ? [
                'error' => false,
                'message' => 'Quote deleted successfully',
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
            : [
                'error' => true,
                'message' => 'Delete failed',
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ];
        print_r(json_encode($resp));
    }

    /**
     * Public endpoint for frontend checkout to get quotes by zipcode
     * Returns active quotes for a zipcode (no auth)
     */
    public function by_zipcode()
    {
        $zipcode = $this->input->get('zipcode', true);
        if (empty($zipcode)) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'error' => true,
                'message' => 'zipcode required'
            ]));
            return;
        }

        $quotes = $this->Shipping_company_quotes_model->get_active_quotes_by_zipcode($zipcode);
        $this->output->set_content_type('application/json')->set_output(json_encode([
            'error' => false,
            'quotes' => $quotes
        ]));
    }
}

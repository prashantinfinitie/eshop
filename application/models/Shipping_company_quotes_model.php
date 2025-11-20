<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Shipping_company_quotes_model extends CI_Model
{
    protected $table = 'shipping_company_quotes';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['function_helper']);
    }

    /**
     * Create new quote
     */
    public function create_quote($data)
    {
        $data = escape_array($data);
        $data['created_at'] = date('Y-m-d H:i:s');

        $this->db->trans_start();
        $this->db->insert($this->table, $data);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            log_message('error', 'Create quote failed: ' . print_r($data, true));
            return false;
        }

        return $insert_id ? (int)$insert_id : false;
    }

    /**
     * Update existing quote
     */
    public function update_quote($id, $company_id, $data)
    {
        $data = escape_array($data);
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->trans_start();
        $this->db->where([
            'id' => (int)$id,
            'shipping_company_id' => (int)$company_id
        ]);
        $this->db->update($this->table, $data);
        $affected = $this->db->affected_rows();
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            log_message('error', 'Update quote failed id:' . $id . ' data: ' . print_r($data, true));
            return false;
        }

        return $affected > 0;
    }

    /**
     * Delete quote
     */
    public function delete_quote($id, $company_id)
    {
        $this->db->trans_start();
        $this->db->where([
            'id' => (int)$id,
            'shipping_company_id' => (int)$company_id
        ]);
        $this->db->delete($this->table);
        $affected = $this->db->affected_rows();
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            log_message('error', 'Delete quote failed id:' . $id);
            return false;
        }

        return $affected > 0;
    }

    /**
     * Get single quote by ID
     */
    public function get_quote($id, $company_id = null)
    {
        $this->db->where('id', (int)$id);

        if ($company_id !== null) {
            $this->db->where('shipping_company_id', (int)$company_id);
        }

        $result = $this->db->get($this->table)->row_array();

        return $result ? output_escaping($result) : null;
    }

    /**
     * Server-side list for company panel (bootstrap-table expected format)
     * Returns ['total' => int, 'rows' => array]
     */
    public function list_for_company($company_id, $offset = 0, $limit = 10, $search = '', $sort = 'id', $order = 'DESC')
    {
        // Sanitize inputs
        $company_id = (int)$company_id;
        $offset = (int)$offset;
        $limit = (int)$limit;
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        // Whitelist sortable columns
        $allowed_sort = ['id', 'zipcode', 'price', 'is_active', 'created_at'];
        if (!in_array($sort, $allowed_sort)) {
            $sort = 'id';
        }

        // Build base query for counting
        $this->db->from($this->table);
        $this->db->where('shipping_company_id', $company_id);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('zipcode', $search);
            $this->db->or_like('eta_text', $search);
            $this->db->or_like('price', $search);
            $this->db->group_end();
        }

        // Get total count
        $total = (int)$this->db->count_all_results('', false); // false = don't reset query

        // Add sorting and pagination
        $this->db->order_by($sort, $order);
        $this->db->limit($limit, $offset);

        // Execute query
        $query = $this->db->get();
        $results = $query->result_array();

        // Format rows for bootstrap-table
        $rows = [];
        foreach ($results as $row) {
            $row = output_escaping($row);

            // Build action buttons
            $operate = '';
            $operate .= '<a href="javascript:void(0)" class="edit-quote btn btn-primary btn-xs mr-1 mb-1"
                            title="Edit Quote" data-id="' . $row['id'] . '">
                            <i class="fa fa-pen"></i>
                         </a>';
            $operate .= '<a href="javascript:void(0)" class="delete-quote btn btn-danger btn-xs mr-1 mb-1"
                            title="Delete Quote" data-id="' . $row['id'] . '">
                            <i class="fa fa-trash"></i>
                         </a>';

            $tempRow = [
                'id' => $row['id'],
                'zipcode' => isset($row['zipcode']) ? $row['zipcode'] : '',
                'price' => isset($row['price']) ? number_format((float)$row['price'], 2) : '0.00',
                'eta_text' => isset($row['eta_text']) ? $row['eta_text'] : '',
                'cod_available' => (isset($row['cod_available']) && $row['cod_available'] == 1)
                    ? '<span class="badge badge-success">Yes</span>'
                    : '<span class="badge badge-danger">No</span>',
                'additional_charges' => $this->format_additional_charges($row['additional_charges']),
                'is_active' => (isset($row['is_active']) && $row['is_active'] == 1)
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-warning">Inactive</span>',
                'created_at' => isset($row['created_at']) ? date('d-m-Y H:i', strtotime($row['created_at'])) : '',
                'operate' => $operate
            ];

            $rows[] = $tempRow;
        }

        return [
            'total' => $total,
            'rows' => $rows
        ];
    }

    /**
     * Admin listing (with company name, action buttons)
     */
    public function list_for_admin($offset = 0, $limit = 10, $search = '', $filters = [], $sort = 'id', $order = 'DESC')
    {
        // Sanitize inputs
        $offset = (int)$offset;
        $limit = (int)$limit;
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        // Whitelist sortable columns
        $allowed_sort = ['id', 'zipcode', 'price', 'is_active', 'created_at'];
        if (!in_array($sort, $allowed_sort)) {
            $sort = 'shipping_company_quotes.id';
        } else {
            $sort = 'shipping_company_quotes.' . $sort;
        }

        // Build base query
        $this->db->from($this->table);
        $this->db->join('users u', 'u.id = shipping_company_quotes.shipping_company_id', 'left');

        // Apply filters
        if (!empty($filters['company_id'])) {
            $this->db->where('shipping_company_id', (int)$filters['company_id']);
        }
        if (!empty($filters['zipcode'])) {
            $this->db->where('zipcode', $filters['zipcode']);
        }
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('is_active', (int)$filters['is_active']);
        }

        // Apply search
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('shipping_company_quotes.zipcode', $search);
            $this->db->or_like('shipping_company_quotes.eta_text', $search);
            $this->db->or_like('shipping_company_quotes.price', $search);
            $this->db->or_like('u.username', $search);
            $this->db->group_end();
        }

        // Get total count
        $total = (int)$this->db->count_all_results('', false);

        // Add selection, sorting and pagination
        $this->db->select('shipping_company_quotes.*, u.username as company_name, u.email as company_email');
        $this->db->order_by($sort, $order);
        $this->db->limit($limit, $offset);

        $query = $this->db->get();
        $results = $query->result_array();

        // Format rows for bootstrap-table
        $rows = [];
        foreach ($results as $row) {
            $row = output_escaping($row);

            // Action buttons for admin
            $operate = '';
            $operate .= '<a href="javascript:void(0)" class="edit-quote-admin btn btn-primary btn-xs mr-1 mb-1"
                            title="Edit" data-id="' . $row['id'] . '">
                            <i class="fa fa-pen"></i>
                         </a>';
            $operate .= '<a href="javascript:void(0)" class="delete-quote-admin btn btn-danger btn-xs mr-1 mb-1"
                            title="Delete" data-id="' . $row['id'] . '">
                            <i class="fa fa-trash"></i>
                         </a>';
            $operate .= '<a href="javascript:void(0)" class="toggle-active-quote btn btn-info btn-xs mr-1 mb-1"
                            title="Toggle Status" data-id="' . $row['id'] . '" data-active="' . $row['is_active'] . '">
                            <i class="fa fa-toggle-' . ($row['is_active'] == 1 ? 'on' : 'off') . '"></i>
                         </a>';

            $tempRow = [
                'id' => $row['id'],
                'company_name' => isset($row['company_name']) ? $row['company_name'] : 'N/A',
                'company_email' => isset($row['company_email']) ? $row['company_email'] : '',
                'zipcode' => isset($row['zipcode']) ? $row['zipcode'] : '',
                'price' => isset($row['price']) ? number_format((float)$row['price'], 2) : '0.00',
                'eta_text' => isset($row['eta_text']) ? $row['eta_text'] : '',
                'cod_available' => (isset($row['cod_available']) && $row['cod_available'] == 1)
                    ? '<span class="badge badge-success">Yes</span>'
                    : '<span class="badge badge-danger">No</span>',
                'additional_charges' => $this->format_additional_charges($row['additional_charges']),
                'is_active' => (isset($row['is_active']) && $row['is_active'] == 1)
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-warning">Inactive</span>',
                'created_at' => isset($row['created_at']) ? date('d-m-Y H:i', strtotime($row['created_at'])) : '',
                'operate' => $operate
            ];

            $rows[] = $tempRow;
        }

        return [
            'total' => $total,
            'rows' => $rows
        ];
    }

    /**
     * Format additional charges for display (modern, compact, Bootstrap list-group)
     *
     * @param string|null $charges_json JSON string of charges (assoc array)
     * @param string $currency       Currency symbol or empty string (default: '₹')
     * @return string                HTML (escaped, ready for output)
     */
    // private function format_additional_charges($charges_json, $currency = '₹')
    // {
    //     if (empty($charges_json)) {
    //         return '<span class="text-muted">None</span>';
    //     }

    //     $charges = json_decode($charges_json, true);
    //     if (json_last_error() !== JSON_ERROR_NONE || empty($charges)) {
    //         return '<span class="text-muted">None</span>';
    //     }

    //     // Sort by key for stable ordering (optional)
    //     ksort($charges);

    //     $output  = '<div class="list-group list-group-flush small">';
    //     foreach ($charges as $key => $value) {
    //         // sanitize and format
    //         $label = htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');
    //         $amount = number_format((float)$value, 2);

    //         $output .= '<div class="list-group-item d-flex justify-content-between align-items-center px-2 py-1">';
    //         // left side: badge-like label
    //         $output .= '<div class="d-flex align-items-center">';
    //         $output .= '<span class="badge badge-light border mr-2 text-uppercase small" style="letter-spacing:0.02em;">' . $label . '</span>';
    //         $output .= '</div>';
    //         // right side: amount
    //         $output .= '<div class="font-weight-bold">' . htmlspecialchars($currency, ENT_QUOTES, 'UTF-8') . $amount . '</div>';
    //         $output .= '</div>';
    //     }
    //     $output .= '</div>';

    //     return $output;
    // }



    private function format_additional_charges($charges_json, $currency = '₹')
    {
        if (empty($charges_json)) {
            return '<span class="text-muted">None</span>';
        }

        $charges = json_decode($charges_json, true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($charges)) {
            return '<span class="text-muted">None</span>';
        }

        $items = [];
        foreach ($charges as $key => $value) {
            $k = htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');
            $v = number_format((float)$value, 2);
            $items[] = '<span class="badge badge-pill badge-secondary mr-1 mb-1 small">' . $k . ': ' . htmlspecialchars($currency, ENT_QUOTES, 'UTF-8') . $v . '</span>';
        }

        return '<div class="d-flex flex-wrap">' . implode('', $items) . '</div>';
    }


    /**
     * Get active quotes by zipcode (for frontend checkout)
     * Returns array of quote objects with company info
     */
    public function get_active_quotes_by_zipcode($zipcode)
    {
        $this->db->select('
            shipping_company_quotes.*,
            u.username as company_name,
            u.email as company_email,
            u.mobile  as company_phone
        ');
        $this->db->from($this->table);
        $this->db->join('users u', 'u.id = shipping_company_quotes.shipping_company_id', 'left');
        $this->db->where('shipping_company_quotes.zipcode', $zipcode);
        $this->db->where('shipping_company_quotes.is_active', 1);
        $this->db->where('u.active', 1); // Only active companies
        $this->db->order_by('shipping_company_quotes.price', 'ASC');

        $results = $this->db->get()->result_array();

        return array_map('output_escaping', $results);
    }

    /**
     * Get quotes by company ID
     */
    public function get_quotes_by_company($company_id, $active_only = false)
    {
        $this->db->where('shipping_company_id', (int)$company_id);

        if ($active_only) {
            $this->db->where('is_active', 1);
        }

        $this->db->order_by('zipcode', 'ASC');
        $results = $this->db->get($this->table)->result_array();

        return array_map('output_escaping', $results);
    }

    /**
     * Toggle active status
     */
    public function toggle_active($id, $company_id = null)
    {
        $this->db->where('id', (int)$id);

        if ($company_id !== null) {
            $this->db->where('shipping_company_id', (int)$company_id);
        }

        $current = $this->db->get($this->table)->row_array();

        if (empty($current)) {
            return false;
        }

        $new_status = $current['is_active'] == 1 ? 0 : 1;

        $this->db->where('id', (int)$id);
        if ($company_id !== null) {
            $this->db->where('shipping_company_id', (int)$company_id);
        }

        $this->db->update($this->table, [
            'is_active' => $new_status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Check if quote exists for zipcode and company
     */
    public function quote_exists($zipcode, $company_id, $exclude_id = null)
    {
        $this->db->where([
            'zipcode' => $zipcode,
            'shipping_company_id' => (int)$company_id
        ]);

        if ($exclude_id) {
            $this->db->where('id !=', (int)$exclude_id);
        }

        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Get statistics for company dashboard
     */
    public function get_company_stats($company_id)
    {
        $company_id = (int)$company_id;

        $this->db->where('shipping_company_id', $company_id);
        $total = $this->db->count_all_results($this->table);

        $this->db->where([
            'shipping_company_id' => $company_id,
            'is_active' => 1
        ]);
        $active = $this->db->count_all_results($this->table);

        $this->db->select('COUNT(DISTINCT zipcode) as unique_zipcodes');
        $this->db->where('shipping_company_id', $company_id);
        $zipcodes_result = $this->db->get($this->table)->row_array();
        $unique_zipcodes = $zipcodes_result['unique_zipcodes'] ?? 0;

        return [
            'total_quotes' => $total,
            'active_quotes' => $active,
            'inactive_quotes' => $total - $active,
            'unique_zipcodes' => $unique_zipcodes
        ];
    }
}

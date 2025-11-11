<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Return_reason_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    public function add_return_reason_details($data)
    {

        $data = escape_array($data);

        $return_reasons = [
            'return_reason' => $data['return_reason'],
            'message' => (isset($data['message']) && !empty($data['message'])) ? $data['message'] : '',
            'image' => $data['image'],
        ];
        if (isset($data['edit_return_reason_id']) && !empty($data['edit_return_reason_id'])) {
            $this->db->set($return_reasons)->where('id', $data['edit_return_reason_id'])->update('return_reasons');
        } else {
            $this->db->insert('return_reasons', $return_reasons);
        }
    }

    public function get_return_reason_list($offset = 0, $limit = 10, $sort = 'id', $order = 'ASC')
    {
        $multipleWhere = '';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['rr.`id`' => $search, 'rr.`retun_reason`' => $search, 'rr.`message`' => $search];
        }

        $count_res = $this->db->select(' COUNT(rr.id) as `total` ');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->or_where($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $sc_count = $count_res->get('return_reasons rr')->result_array();

        foreach ($sc_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' rr.`id` as id , rr.`return_reason`, rr.`image` , rr.`message` ');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->or_like($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $sc_search_res = $search_res->order_by($sort, "desc")->limit($limit, $offset)->get('return_reasons rr')->result_array();

        $bulkData = array();
        $bulkData['total'] = count($sc_search_res);
        $rows = array();
        $tempRow = array();

        foreach ($sc_search_res as $row) {
            $row = output_escaping($row);

            $operate = ' <a href="' . base_url('admin/return_reasons/manage_return_reason?edit_id=' . $row['id']) . '" class="btn btn-success edit_return_reason action-btn btn-xs ml-1 mr-1 mb-1"  title="Edit" data-id="' . $row['id'] . '" data-target="#add_return_reason" data-toggle="modal"><i class="fa fa-pen"></i></a>';
            $operate .= '<a class="btn btn-danger action-btn btn-xs ml-1 mr-1 mb-1" href="javascript:void(0)" id="delete-return-reason" title="Delete" data-id="' . $row['id'] . '" ><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row['id'];
            $tempRow['return_reason'] = $row['return_reason'];
            // $tempRow['message'] = $row['message'];
            $row['image'] = (isset($row['image']) && !empty($row['image'])) ? base_url() . $row['image'] :  base_url() . NO_IMAGE;
            $tempRow['image'] = '<div class="image-box-100"><a href=' . $row['image'] . ' data-toggle="lightbox" data-gallery="gallery"><img src=' . $row['image'] . ' class="rounded"></a></div>';
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
}

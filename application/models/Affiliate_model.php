<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Affiliate_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    public function add_affiliate($data = [], $affiliate_profile = [])
    {
        $data = escape_array($data);
        $affiliate_profile = (!empty($affiliate_profile)) ? escape_array($affiliate_profile) : [];


        $affiliate_data = array(
            'user_id' => $data['user_id'],
            'uuid' => $data['uuid'],
            'website_url' => $data['website_url'],
            'mobile_app_url' => $data['mobile_app_url'],
            'status' => $data['status'],
            'commission_type' => 'percentage',
        );

        if (isset($data['edit_affiliate_data_id']) && !empty($data['edit_affiliate_data_id'])) {
            if ($this->db->set($affiliate_profile)->where('id', $data['user_id'])->update('users')) {
                $this->db->set($affiliate_data)->where('id', $data['edit_affiliate_data_id'])->update('affiliates');
                return true;
            } else {
                return false;
            }
        } else {

            $this->db->insert('affiliates', $affiliate_data);
            $insert_id = $this->db->insert_id();
            if (!empty($insert_id)) {
                return  $insert_id;
            } else {
                return false;
            }
        }
    }
    public function add_affiliate_tracking($data = [])
    {
        $data = escape_array($data);

        // print_r($data);
        $affiliate_data = array(
            // 'edit_affiliate_data_id' => isset($data['edit_affiliate_data_id']) && $data['edit_affiliate_data_id'] != "" ? $data['edit_affiliate_data_id'] : NULL,
            'product_id' => $data['product_id'],
            'affiliate_id' => $data['affiliate_id'],
            'token' => $data['token'],
            'category_id' => $data['category_id'],
            'category_commission' => $data['category_commission']
        );
        // print_r($affiliate_data);
        // if (isset($data['edit_affiliate_data_id']) && !empty($data['edit_affiliate_data_id'])) {
        //     $this->db->set($affiliate_data)->where('id', $data['edit_affiliate_data_id'])->update('affiliate_tracking');
        // } else {

        $this->db->insert('affiliate_tracking', $affiliate_data);
        $insert_id = $this->db->insert_id();
        if (!empty($insert_id)) {
            return  $insert_id;
        } else {
            return false;
        }
        // }
    }

    public function get_affiliates_list($get_affiliate_list = "")
    {

        $offset = 0;
        $limit = 10;
        $sort = 'u.id';
        $order = 'DESC';
        $multipleWhere = '';
        $where = ['u.active' => 1];

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "u.id";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) && $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['u.`id`' => $search, 'u.`username`' => $search, 'u.`email`' => $search, 'u.`mobile`' => $search, 'u.`address`' => $search, 'u.`balance`' => $search];
        }

        $count_res = $this->db->select(' COUNT(u.id) as `total` ')->join('users_groups ug', ' ug.user_id = u.id ')->join('affiliates af', ' af.user_id = u.id ');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_start();
            $count_res->or_like($multipleWhere);
            $count_res->group_end();
        }
        if (isset($where) && !empty($where)) {
            $where['ug.group_id'] = '5';
            $count_res->where($where);
        }

        if ($get_affiliate_list == "approved") {
            $count_res->where('af.status', '1');
        }
        if ($get_affiliate_list == "not_approved") {
            $count_res->where('af.status', '2');
        }
        if ($get_affiliate_list == "deactive") {
            $count_res->where('af.status', '0');
        }
        if ($get_affiliate_list == "removed") {
            $count_res->where('af.status', '7');
        }

        $offer_count = $count_res->get('users u')->result_array();
        foreach ($offer_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select('u.id as userID, u.username, u.email, u.mobile, u.is_affiliate_user, u.balance,u.address, af.* ')->join('users_groups ug', ' ug.user_id = u.id ')->join('affiliates af', ' af.user_id = u.id ');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_start();
            $search_res->or_like($multipleWhere);
            $search_res->group_end();
        }
        if (isset($where) && !empty($where)) {
            $where['ug.group_id'] = '5';
            $search_res->where($where);
        }

        if ($get_affiliate_list == "approved") {
            $search_res->where('af.status', '1');
        }
        if ($get_affiliate_list == "not_approved") {
            $search_res->where('af.status', '2');
        }
        if ($get_affiliate_list == "deactive") {
            $search_res->where('af.status', '0');
        }
        if ($get_affiliate_list == "removed") {
            $search_res->where('af.status', '7');
        }

        $offer_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('users u')->result_array();

        // print_r($offer_search_res);
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($offer_search_res as $row) {
            $row = output_escaping($row);
            $operate = " <a href='./affiliate_users/manage_user?edit_id=" . $row['user_id'] . "' data-id=" . $row['user_id'] . " class='btn action-btn btn-success btn-xs mr-2 mb-1' title='Edit' ><i class='fa fa-pen'></i></a>";
            $operate .= '<a  href="javascript:void(0)" class="delete-affiliate btn action-btn btn-danger btn-xs mr-2 mb-1" title="Delete Affiliate with related Affiliate data "   data-id="' . $row['user_id'] . '" ><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row['user_id'];
            $tempRow['name'] = $row['username'];
            if (isset($row['email']) && !empty($row['email']) && $row['email'] != "" && $row['email'] != " ") {
                $tempRow['email'] = (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($row['email']) - 3) . substr($row['email'], -3) : ucfirst($row['email']);
            } else {
                $tempRow['email'] = "";
            }
            if (isset($row['mobile']) && !empty($row['mobile']) && $row['mobile'] != "" && $row['mobile'] != " ") {
                $tempRow['mobile'] =  (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($row['mobile']) - 3) . substr($row['mobile'], -3) : $row['mobile'];
            } else {
                $tempRow['mobile'] = "";
            }
            $tempRow['address'] = $row['address'];
            $tempRow['website_url'] = $row['website_url'];
            $tempRow['mobile_app_url'] = $row['mobile_app_url'];



            // seller status
            if ($row['status'] == 2)
                $tempRow['status'] = "<label class='badge badge-warning'>Not-Approved</label>";
            else if ($row['status'] == 1)
                $tempRow['status'] = "<label class='badge badge-success'>Approved</label>";
            else if ($row['status'] == 0)
                $tempRow['status'] = "<label class='badge badge-danger'>Deactive</label>";
            else if ($row['status'] == 7)
                $tempRow['status'] = "<label class='badge badge-danger'>Removed</label>";



            $tempRow['balance'] =  $row['affiliate_wallet_balance'] == null || $row['affiliate_wallet_balance'] == 0 || empty($row['affiliate_wallet_balance']) ? "0" : number_format($row['affiliate_wallet_balance'], 2);
            $tempRow['date'] = date('d-m-Y', strtotime($row['created_at']));
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    public function get_product_details($flag = NULL, $seller_id = NULL, $p_status = NULL, $is_in_affiliate = 0, $type = '', $affiliate_categories = [])
    {

        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = '';
        $user_id = $this->session->userdata('user_id');

        if (isset($_GET['offset']))

            $offset = $_GET['offset'];

        if (isset($_GET['limit']))

            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "p.id";
            } else {
                $sort = $_GET['sort'];
            }

        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = trim($_GET['search']);
            $multipleWhere = ['p.`id`' => $search, 'p.`name`' => $search, 'c.name' => $search];
        }

        if (isset($_GET['category_id'])) {
            if (isset($_GET['category_id']) and $_GET['category_id'] != '') {
                $category_id = $_GET['category_id'];
            }
        }

        $count_res = $this->db->select(' COUNT( distinct(p.id)) as `total` ')
            ->join('categories c', 'p.category_id=c.id', 'left')
            ->join('affiliate_tracking at', 'at.product_id = p.id', 'left')
            ->join('brands b', 'p.brand=b.id', 'left')
            ->join('seller_data sd', 'p.seller_id=sd.user_id', 'left');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_Start();
            $count_res->or_like($multipleWhere);
            $count_res->group_End();
        }

        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        if (isset($seller_id) && $seller_id != "") {
            $count_res->where("p.seller_id", $seller_id);
        }

        if (isset($p_status) && $p_status != "") {
            $count_res->where("p.status", $p_status);
        }


        if (isset($is_in_affiliate) && $is_in_affiliate != "0") {
            $count_res->where("p.is_in_affiliate", $is_in_affiliate);
        }

        if (isset($type) && $type == "digital_product") {
            $count_res->where("p.type !=", $type);
        }

        if (isset($affiliate_categories) && !empty($affiliate_categories)) {
            $count_res->where_in('c.id', $affiliate_categories);
        }

        if (isset($category_id) && !empty($category_id)) {
            $count_res->group_Start();
            $count_res->or_where('p.category_id', $category_id);
            $count_res->or_where('c.parent_id', $category_id);
            $count_res->group_End();
        }

        $product_count = $count_res->get('products p')->result_array();

        foreach ($product_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(
            'c.name as category_name, c.affiliate_commission,
            b.name as brand_name, sd.store_name, p.id as pid,p.name, 
            p.type, p.image, p.status, p.brand, p.slug, p.is_in_affiliate, p.category_id, 
            at.id, at.affiliate_id, at.product_id, at.token, at.usage_count, at.commission_earned'
        )
            ->join("categories c", "p.category_id=c.id")
            ->join('affiliate_tracking at', 'at.product_id = p.id', 'left')
            ->join("brands b", "p.brand=b.id", 'left')
            ->join("seller_data sd", "sd.user_id=p.seller_id ");

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_Start();
            $search_res->or_like($multipleWhere);
            $search_res->group_End();
        }

        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        if (isset($category_id) && !empty($category_id)) {
            //category select where
            $search_res->group_Start();
            $search_res->or_where('p.category_id', $category_id);
            $search_res->or_where('c.parent_id', $category_id);
            $search_res->group_End();
        }

        if (isset($seller_id) && $seller_id != "") {
            $count_res->where("p.seller_id", $seller_id);
        }

        if (isset($p_status) && $p_status != "") {
            $search_res->where("p.status", $p_status);
        }

        if (isset($is_in_affiliate) && $is_in_affiliate != "0") {
            $search_res->where("p.is_in_affiliate", $is_in_affiliate);
        }

        if (isset($type) && $type == "digital_product") {
            $count_res->where("p.type !=", $type);
        }

        if (isset($affiliate_categories) && !empty($affiliate_categories)) {
            $count_res->where_in('c.id', $affiliate_categories);
        }

        $pro_search_res = $search_res->group_by('pid')->order_by($sort, "DESC")->limit($limit, $offset)->get('products p')->result_array();
        // print_R($pro_search_res);
        // echo $this->db->last_query();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        foreach ($pro_search_res as $row) {

            $row = output_escaping($row);
            // print_R($row);
            $user_mobile = $this->session->userdata('mobile');
            $user_id = $this->session->userdata('user_id');


            if ($this->ion_auth->is_affiliate_user()) {
                // if ($link_generated == 0) {
                //     $operate = "<a href='javascript:void(0)' class='btn action-btn btn-info btn-xs mr-1 mb-1 open-affiliate-link-modal'
                //     data-id='" . $row['pid'] . "'
                //     data-name='" . htmlspecialchars($row['name']) . "'
                //     data-user_mobile='" . $user_mobile . "' 
                //     data-user_id='" . $user_id . "' 
                //     data-affiliate_commission='" . $row['affiliate_commission'] . "' 
                //             data-category_id='" . $row['category_id'] . "' 
                //             data-toggle='modal' data-target='#product-affiliate-link-modal' title='Edit'><i class='fa fa-eye'></i></a> ";
                // }

                // if ($row['token'] != '' && $link_generated == 1) {
                // $operate = "<a href='javascript:void(0)' class='btn btn-info btn-xs copy-affiliate-link-btn mr-1 mb-1 action-btn'
                //             data-id='" . $row['pid'] . "'
                //             data-slug='" . $row['slug'] . "'
                //             data-user_id='" . $user_id . "'
                //             title='Copy Affiliate Link' data-toggle='modal' data-target='#shareAffiliateModal'>
                //             <i class='fa fa-share-alt'></i></a>";
                // }
            } else {
                $operate = "<a href='javascript:void(0)' class='btn action-btn btn-success btn-xs mr-1 mb-1 open-affiliate-modal'
                        data-id='" . $row['pid'] . "'
                        data-name='" . htmlspecialchars($row['name']) . "'
                        data-is_in_affiliate='" . $row['is_in_affiliate'] . "' 
                        data-toggle='modal' data-target='#product-affiliate-modal' title='Edit'><i class='fa fa-pen'></i></a> ";
            }

            if ($row['status'] == '1') {
                $tempRow['status'] = '<a class="badge badge-success text-white" >Active</a>';
            } else if ($row['status'] == '0') {
                $tempRow['status'] = '<a class="badge badge-danger text-white" >Inactive</a>';
            }

            $tempRow['usage_count'] = $row['usage_count'];
            $tempRow['commission_earned'] = $row['commission_earned'];

            $tempRow['id'] = $row['pid'];
            $tempRow['name'] = $row['name'] . '<br><small>' . ucwords(str_replace('_', ' ', $row['type'])) . '</small><br><small> By </small><b>' . $row['store_name'] . '</b>';
            $tempRow['product_name'] = $row['name'];
            $tempRow['type'] = $row['type'];
            $tempRow['brand'] = $row['brand_name'];
            $tempRow['is_in_affiliate'] = $row['is_in_affiliate'];
            if ($row['is_in_affiliate'] == '1') {
                $tempRow['is_in_affiliate_status'] = '<a class="badge badge-success text-white" >Yes</a>';
            } else {
                $tempRow['is_in_affiliate_status'] = '<a class="badge badge-danger text-white" >No</a>';
            }
            $tempRow['category_name'] = $row['category_name'];

            $row['image'] = get_image_url($row['image'], 'thumb', 'sm');
            $tempRow['image'] = '<div class="mx-auto product-image image-box-100"><a href=' . $row['image'] . ' data-toggle="lightbox" data-gallery="gallery">
        <img src=' . $row['image'] . ' class="rounded"></a></div>';
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
    public function get_promoted_product_list($flag = NULL,  $p_status = NULL, $is_in_affiliate = 0, $type = '', $affiliate_categories = [], $affiliate_id = '')
    {

        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = '';
        $user_id = $this->session->userdata('user_id');

        if (isset($_GET['offset']))

            $offset = $_GET['offset'];

        if (isset($_GET['limit']))

            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "p.id";
            } elseif ($_GET['sort'] == 'date') {
                $sort = "at.created_at";
            } else {
                $sort = $_GET['sort'];
            }


        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = trim($_GET['search']);
            $multipleWhere = ['p.`id`' => $search, 'p.`name`' => $search, 'c.name' => $search];
        }

        if (isset($_GET['category_id'])) {
            if (isset($_GET['category_id']) and $_GET['category_id'] != '') {
                $category_id = $_GET['category_id'];
            }
        }

        $count_res = $this->db->select(' COUNT( distinct(p.id)) as `total` ')
            ->join('categories c', 'p.category_id=c.id', 'left')
            ->join('affiliate_tracking at', 'at.product_id = p.id')
            ->join('brands b', 'p.brand=b.id', 'left');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_Start();
            $count_res->or_like($multipleWhere);
            $count_res->group_End();
        }

        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }


        if (isset($p_status) && $p_status != "") {
            $count_res->where("p.status", $p_status);
        }


        if (isset($is_in_affiliate) && $is_in_affiliate != "0") {
            $count_res->where("p.is_in_affiliate", $is_in_affiliate);
        }
        if (isset($affiliate_id) && !empty($affiliate_id)) {
            $count_res->where("at.affiliate_id", $affiliate_id);
        }

        if (isset($type) && $type == "digital_product") {
            $count_res->where("p.type !=", $type);
        }

        if (isset($affiliate_categories) && !empty($affiliate_categories)) {
            $count_res->where_in('c.id', $affiliate_categories);
        }

        if (isset($category_id) && !empty($category_id)) {
            $count_res->group_Start();
            $count_res->or_where('p.category_id', $category_id);
            $count_res->or_where('c.parent_id', $category_id);
            $count_res->group_End();
        }

        $product_count = $count_res->get('products p')->result_array();

        foreach ($product_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(
            'c.name as category_name, c.affiliate_commission,
            b.name as brand_name, p.id as pid,p.name, 
            p.type, p.image, p.status, p.brand, p.slug, p.is_in_affiliate, p.category_id, 
            at.id, at.affiliate_id, at.product_id, at.token, at.usage_count, at.commission_earned, at.created_at'
        )
            ->join("categories c", "p.category_id=c.id")
            ->join('affiliate_tracking at', 'at.product_id = p.id')
            ->join("brands b", "p.brand=b.id", 'left');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_Start();
            $search_res->or_like($multipleWhere);
            $search_res->group_End();
        }

        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        if (isset($category_id) && !empty($category_id)) {
            //category select where
            $search_res->group_Start();
            $search_res->or_where('p.category_id', $category_id);
            $search_res->or_where('c.parent_id', $category_id);
            $search_res->group_End();
        }


        if (isset($p_status) && $p_status != "") {
            $search_res->where("p.status", $p_status);
        }

        if (isset($is_in_affiliate) && $is_in_affiliate != "0") {
            $search_res->where("p.is_in_affiliate", $is_in_affiliate);
        }

        if (isset($affiliate_id) && !empty($affiliate_id)) {
            $count_res->where("at.affiliate_id", $affiliate_id);
        }

        if (isset($type) && $type == "digital_product") {
            $count_res->where("p.type !=", $type);
        }

        if (isset($affiliate_categories) && !empty($affiliate_categories)) {
            $count_res->where_in('c.id', $affiliate_categories);
        }

        $pro_search_res = $search_res->group_by('id')->order_by($sort, "DESC")->limit($limit, $offset)->get('products p')->result_array();
        // print_R($pro_search_res);
        // echo $this->db->last_query();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $currency_symbol = get_settings('currency');

        foreach ($pro_search_res as $row) {

            $row = output_escaping($row);


            if ($row['status'] == '1') {
                $tempRow['status'] = '<a class="badge badge-success text-white" >Active</a>';
            } else if ($row['status'] == '0') {
                $tempRow['status'] = '<a class="badge badge-danger text-white" >Inactive</a>';
            }

            $tempRow['usage_count'] = $row['usage_count'];
            $tempRow['commission_earned'] = $currency_symbol . ' ' . $row['commission_earned'];
            $tempRow['affiliate_commission'] = $row['affiliate_commission'] . '%';

            $tempRow['id'] = $row['id'];
            $tempRow['product_id'] = $row['pid'];
            $tempRow['name'] = ucfirst($row['name']);
            $tempRow['product_name'] = $row['name'];
            $tempRow['type'] = $row['type'];
            $tempRow['brand'] = $row['brand_name'];

            $tempRow['category_name'] = $row['category_name'];
            $tempRow['date'] = date('d-m-Y', strtotime($row['created_at']));

            $row['image'] = get_image_url($row['image'], 'thumb', 'sm');
            $tempRow['image'] = '<div class="mx-auto product-image image-box-100"><a href=' . $row['image'] . ' data-toggle="lightbox" data-gallery="gallery">
        <img src=' . $row['image'] . ' class="rounded"></a></div>';

            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
    public function get_affiliate_products_by_categories($category_ids = [], $limit = 10, $offset = 0, $search = '')
    {

        $this->db->select('p.id AS product_id, p.type, p.is_prices_inclusive_tax, p.tax, p.category_id, p.brand, p.name, p.short_description, p.slug, p.image, p.status, p.is_in_affiliate,
        pv.price, pv.special_price,
        b.name AS brand_name, b.id AS brand_id,
        c.id AS category_main_id, c.name AS category_name, c.affiliate_commission, c.is_in_affiliate AS category_is_in_affiliate,
        (SELECT GROUP_CONCAT(tax.percentage) FROM taxes as tax WHERE FIND_IN_SET(tax.id, p.tax)) as tax_percentage');

        $this->db->from('products p');
        $this->db->join("categories c", "p.category_id=c.id");
        $this->db->join('taxes tax', 'tax.id = p.tax', 'LEFT');
        $this->db->join("product_variants pv", "p.id=pv.product_id");
        // $this->db->join('affiliate_tracking at', 'at.product_id = p.id', 'left');
        $this->db->join("brands b", "p.brand=b.id", 'left');
        // $this->db->join('affiliate_tracking at', 'at.product_id = p.id');
        $this->db->where_in('p.category_id', $category_ids);
        $this->db->where('p.is_in_affiliate', 1);

        $this->db->where('p.type !=', 'digital_product');
        $this->db->where('p.status', 1);


        if (!empty($search)) {
            $this->db->like('p.name', $search);
        }

        $this->db->group_by('p.id'); // to avoid duplicates if multiple affiliate entries exist
        $this->db->limit($limit, $offset);


        $query = $this->db->get();
        $data = $query->result_array();
        // echo $this->db->last_query();
        return $data;
    }

    public function count_affiliate_products_by_categories($category_ids = [], $search = '')
    {
        if (empty($category_ids)) {
            return 0;
        }

        $this->db->select('p.id AS product_id, p.type, p.is_prices_inclusive_tax, p.tax, p.category_id, p.brand, p.name, p.short_description, p.slug, p.image, p.status, p.is_in_affiliate,
        pv.price, pv.special_price,
        b.name AS brand_name, b.id AS brand_id,
        c.id AS category_main_id, c.name AS category_name, c.affiliate_commission, c.is_in_affiliate AS category_is_in_affiliate,
        (SELECT GROUP_CONCAT(tax.percentage) FROM taxes as tax WHERE FIND_IN_SET(tax.id, p.tax)) as tax_percentage');

        $this->db->from('products p');
        $this->db->join("categories c", "p.category_id=c.id");
        $this->db->join("product_variants pv", "p.id=pv.product_id");
        $this->db->join('taxes tax', 'tax.id = p.tax', 'LEFT');

        // $this->db->join('affiliate_tracking at', 'at.product_id = p.id', 'left');
        $this->db->join("brands b", "p.brand=b.id", 'left');
        // $this->db->join('affiliate_tracking at', 'at.product_id = p.id');
        $this->db->where_in('p.category_id', $category_ids);
        $this->db->where('p.is_in_affiliate', 1);
        $this->db->where('p.type !=', 'digital_product');
        $this->db->where('p.status', 1);


        if (!empty($search)) {
            $this->db->like('p.name', $search);
        }

        $this->db->group_by('p.id'); // to avoid duplicates if multiple affiliate entries exist

        return $this->db->count_all_results();
    }

    public function permanent_delete_affiliate_account()
    {
        // Step 1: Fetch deletion threshold (e.g., 30 days)
        $affiliate_settings = get_settings('affiliate_settings', true);

        $days_limit = $affiliate_settings['account_delete_days'];

        if (!$days_limit || !is_numeric($days_limit)) {
            return []; // skip if invalid
        }

        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days_limit} days"));

        // Step 2: Get affiliates with status = 7 and older than cutoff
        $this->db->select('a.user_id');
        $this->db->from('affiliates a');
        $this->db->where('a.status', 7);
        $this->db->where('a.updated_at <=', $cutoff_date);
        $query = $this->db->get();
        $affiliates = $query->result_array();


        $deleted = [];

        foreach ($affiliates as $affiliate) {
            $user_id = $affiliate['user_id'];

            // Check order items: all commissions settled
            $this->db->where('affiliate_id', $user_id);
            $this->db->where('is_affiliate_commission_settled !=', 1);
            if ($this->db->get('order_items')->num_rows() > 0) {
                continue;
            }

            // Check wallet balance and pending amount = 0
            $this->db->select('affiliate_wallet_balance');
            $this->db->where('user_id', $user_id);
            $wallet = $this->db->get('affiliates')->row_array();


            if (!$wallet || $wallet['wallet_balance'] != 0 || $wallet['pending_commission'] != 0) {
                continue;
            }

            // Proceed to delete
            $this->db->trans_start();

            $this->db->where('user_id', $user_id)->delete('affiliates');
            $this->db->where('affiliate_id', $user_id)->delete('affiliate_tracking');
            $this->db->where('user_id', $user_id)->delete('affiliate_wallet_transactions');
            $this->db->where('user_id', $user_id)->delete('users_groups');
            $this->db->where('id', $user_id)->delete('users');

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                $deleted[] = $user_id;
                $deleted = true;
            }
        }

        $response_data = [
            'error' => $deleted ? false : true,
            'message' => $deleted ? 'Affiliate Deleted Successfully' : 'No affiliate Delete',
        ];

        print_r(json_encode($response_data));
    }

    public function count_affiliate_users()
    {
        $res = $this->db->select('count(u.id) as counter')->join('users_groups ug', ' ug.`user_id` = u.`id` ')
            ->where('ug.group_id=5')
            ->get('`users u`')->result_array();
        return $res[0]['counter'];
    }

    public function total_admin_earnings_via_affiliate($type = "admin")
    {
        $select = "";
        if ($type == "admin") {
            $select = "SUM(admin_commission_amount) as total ";
        }

        $count_res = $this->db->select($select);
        $where = "is_credited=1";
        $where = "affiliate_token!=''";
        $count_res->where($where);

        $product_count = $count_res->get('order_items')->result_array();

        return $product_count[0]['total'];
    }

    public function count_orders_by_affiliate()
    {
        $res = $this->db->select('count(id) as counter');
        $this->db->where('affiliate_token !=', '');
        $res = $this->db->get('order_items oi')->result_array();
        return $res[0]['counter'];
    }
}

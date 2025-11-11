<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sales_inventory_model extends CI_Model
{
    public function get_sales_inventory_list(
        $offset = 0,
        $limit = 10,
        $sort = "qty",
        $order = 'DESC'
    ) {
        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }
        if (isset($_GET['sort']) && !empty($_GET['sort'])) {
            $sort = $_GET['sort'];
        }
        if (isset($_GET['order']) && !empty($_GET['order'])) {
            $order = strtoupper($_GET['order']);
        }

        // Map table fields to database columns
        $sort_map = [
            'id' => 'p.id',
            'name' => 'p.name',
            'stock' => 'CASE WHEN (p.stock OR pv.stock) <= 0 THEN p.stock ELSE pv.stock END',
            'qty' => 'SUM(oi.quantity)'
        ];
        $sort_field = isset($sort_map[$sort]) ? $sort_map[$sort] : 'SUM(oi.quantity)';

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $filters = [
                'p.id' => $search,
                'p.name' => $search,
            ];
        }

        $count_res = $this->db->select('COUNT(DISTINCT oi.product_variant_id) as total')
            ->join('product_variants pv', 'pv.id=oi.product_variant_id')
            ->join('products p', 'p.id=pv.product_id');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $count_res->where(" DATE(oi.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $count_res->where(" DATE(oi.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $this->db->group_start();
            $count_res->or_like($filters);
            $this->db->group_end();
        }

        if (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) {
            $count_res->where("oi.seller_id", $_GET['seller_id']);
        }

        $sales_count = $count_res->get('order_items oi')->result_array();


        $total = $sales_count[0]['total'] ?? 0;

        $search_res = $this->db->select('p.id, oi.product_variant_id, p.name, SUM(oi.quantity) AS qty, (p.availability OR pv.availability) AS availability, (CASE WHEN (p.stock OR pv.stock) <= 0 THEN p.stock ELSE pv.stock END) AS stock')
            ->join('product_variants pv', 'pv.id=oi.product_variant_id')
            ->join('products p', 'p.id=pv.product_id');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $search_res->where(" DATE(oi.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $search_res->where(" DATE(oi.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $this->db->group_start();
            $search_res->or_like($filters);
            $this->db->group_end();
        }

        if (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) {
            $search_res->where("oi.seller_id", $_GET['seller_id']);
        }

        $user_details = $search_res->group_by('oi.product_variant_id')->order_by($sort_field, $order)->limit($limit, $offset)->get('order_items oi')->result_array();


        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        foreach ($user_details as $row) {
            if (isset($row['id']) && $row['id'] != '') {

                if ((isset($row['stock']) && $row['stock'] != '') || (($row['availability'] <= 0) && $row['stock'] <= 0)) {
                    $stock = "<span class='badge badge-warning'>available</span>";
                } else {
                    $stock = "<span class='badge badge-danger'>N/A</span>";
                }
                $tempRow['id'] = (isset($row['id']) && $row['id'] != '') ? $row['id'] : "-";
                $tempRow['name'] = (isset($row['name']) && $row['name'] != '') ? $row['name'] : "-";
                $tempRow['stock'] = $stock;
                $tempRow['qty'] = (isset($row['qty']) && $row['qty'] != '') ? $row['qty'] : "-";
                $rows[] = $tempRow;
            }
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    public function get_seller_sales_inventory_list(
        $offset = 0,
        $limit = 10,
        $sort = "qty",
        $order = 'DESC'
    ) {
        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }
        if (isset($_GET['sort']) && !empty($_GET['sort'])) {
            $sort = $_GET['sort'];
        }
        if (isset($_GET['order']) && !empty($_GET['order'])) {
            $order = strtoupper($_GET['order']);
        }

        // Map table fields to database columns
        $sort_map = [
            'id' => 'p.id',
            'name' => 'p.name',
            'stock' => 'CASE WHEN (p.stock OR pv.stock) <= 0 THEN p.stock ELSE pv.stock END',
            'qty' => 'SUM(oi.quantity)'
        ];
        $sort_field = isset($sort_map[$sort]) ? $sort_map[$sort] : 'SUM(oi.quantity)';

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $filters = [
                'p.id' => $search,
                'p.name' => $search,
            ];
        }

        $count_res = $this->db->select('COUNT(DISTINCT oi.product_variant_id) as total')
            ->join('product_variants pv', 'pv.id=oi.product_variant_id', 'left')
            ->join('products p', 'p.id=pv.product_id', 'left')
            ->where("oi.seller_id=" . $_SESSION['user_id']);

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $count_res->where(" DATE(oi.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $count_res->where(" DATE(oi.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $this->db->group_start();
            $count_res->or_like($filters);
            $this->db->group_end();
        }

        $sales_count = $count_res->get('order_items oi')->result_array();
        $total = $sales_count[0]['total'] ?? 0;

        $search_res = $this->db->select('p.id, oi.product_variant_id, p.name, SUM(oi.quantity) AS qty, (p.availability OR pv.availability) AS availability, (CASE WHEN (p.stock OR pv.stock) <= 0 THEN p.stock ELSE pv.stock END) AS stock')
            ->join('product_variants pv', 'pv.id=oi.product_variant_id', 'left')
            ->join('products p', 'p.id=pv.product_id', 'left')
            ->where("oi.seller_id=" . $_SESSION['user_id']);

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $search_res->where(" DATE(oi.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $search_res->where(" DATE(oi.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $this->db->group_start();
            $search_res->or_like($filters);
            $this->db->group_end();
        }

        $user_details = $search_res->group_by('oi.product_variant_id')->order_by($sort_field, $order)->limit($limit, $offset)->get('order_items oi')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        foreach ($user_details as $row) {
            if (isset($row['stock']) && $row['stock'] != '') {
                $stock = "<span class='badge badge-success'>" . $row['stock'] . "</span>";
            } else if (($row['availability'] <= 0) && $row['stock'] <= 0) {
                $stock = "<span class='badge badge-warning'>available</span>";
            } else {
                $stock = "<span class='badge badge-danger'>N/A</span>";
            }
            $tempRow['id'] = (isset($row['id']) && $row['id'] != '') ? $row['id'] : "-";
            $tempRow['name'] = (isset($row['name']) && $row['name'] != '') ? $row['name'] : "-";
            $tempRow['stock'] = $stock;
            $tempRow['qty'] = (isset($row['qty']) && $row['qty'] != '') ? $row['qty'] : "-";
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
}

<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Point_of_sale_model extends CI_Model
{
    function get_users($search_term = "")
    {
        // Fetch users
        $this->db->select('users.*,ug.group_id')->join('users_groups ug', 'ug.user_id=users.id', 'left');
        $this->db->where("ug.group_id", 2); // Assuming group_id 2 is for customers
        $this->db->group_Start();
        $this->db->where("users.username like '%" . $search_term . "%'");
        $this->db->or_where("users.id like '%" . $search_term . "%'");
        $this->db->or_where("users.mobile like '%" . $search_term . "%'");
        $this->db->or_where("users.email like '%" . $search_term . "%'");
        $this->db->group_End();
        $fetched_records = $this->db->get('users');
        $users = $fetched_records->result_array();

        // Initialize Array with fetched data
        $data = array();
        foreach ($users as $user) {
            $data[] = array("id" => $user['id'], "text" => $user['username'] . " | " . $user['mobile'] . " | " . $user['email'], "number" => $user['mobile'], "email" => $user['email'], "name" => $user['username']);
        }
        return $data;
    }
    
    public function get_pos_orders($delivery_boy_id = NULL, $offset = 0, $limit = 10, $sort = " oi.id ", $order = 'ASC', $seller_id = NULL, $is_pos_order = 1)
    {
        $customer_privacy = false;
        if (isset($seller_id) && $seller_id != "") {
            $customer_privacy = get_seller_permission($seller_id, 'customer_privacy');
        }

        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];

            $filters = [
                'un.username' => $search,
                'u.username' => $search,
                'us.username' => $search,
                'un.email' => $search,
                'oi.id' => $search,
                'o.mobile' => $search,
                'o.address' => $search,
                'o.payment_method' => $search,
                'oi.sub_total' => $search,
                'o.delivery_time' => $search,
                'oi.active_status' => $search,
                'oi.date_added' => $search
            ];
        }

        $count_res = $this->db->select(' COUNT(o.id) as `total` ')
            ->join(' `users` u', 'u.id= oi.delivery_boy_id', 'left')
            ->join('users us ', ' us.id = oi.seller_id', 'left')
            ->join(' `orders` o', 'o.id= oi.order_id')
            ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
            ->join('products p ', ' p.id = v.product_id ', 'left')
            ->join('users un ', ' un.id = o.user_id', 'left');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {

            $count_res->where(" DATE(oi.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $count_res->where(" DATE(oi.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $this->db->group_Start();
            $count_res->or_like($filters);
            $this->db->group_End();
        }

        if (isset($delivery_boy_id)) {
            $count_res->where("oi.delivery_boy_id", $delivery_boy_id);
        }

        if (isset($seller_id) && $seller_id != "") {
            $count_res->where("oi.seller_id", $seller_id);
            $count_res->where("oi.active_status != 'awaiting'");
        }

        if (isset($_GET['user_id']) && $_GET['user_id'] != null) {
            $count_res->where("o.user_id", $_GET['user_id']);
        }

        if (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) {
            $count_res->where("oi.seller_id", $_GET['seller_id']);
        }

        if (isset($_GET['order_status']) && !empty($_GET['order_status'])) {
            $count_res->where('oi.active_status', $_GET['order_status']);
        }
        if (isset($is_pos_order) && !empty($is_pos_order)) {
            $count_res->where('o.is_pos_order', 1);
        }
        // Filter By payment
        if (isset($_GET['payment_method']) && !empty($_GET['payment_method'])) {
            $count_res->where('payment_method', $_GET['payment_method']);
        }
        // Filter By order type
        if (isset($_GET['order_type']) && !empty($_GET['order_type']) && $_GET['order_type'] == 'physical_order') {
            $count_res->where('p.type!=', 'digital_product');
        }
        if (isset($_GET['order_type']) && !empty($_GET['order_type']) && $_GET['order_type'] == 'digital_order') {
            $count_res->where('p.type', 'digital_product');
        }

        $product_count = $count_res->get('order_items oi')->result_array();

        foreach ($product_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(
            ' o.id as order_id,oi.id as order_item_id,
            oi.seller_id,oi.quantity, oi.is_credited, oi.variant_name, oi.product_name,oi.sub_total, oi.updated_by,
             oi.active_status, o.payment_method, o.notes, o.mobile, o.delivery_date, o.delivery_time, o.user_id,o.date_added,
            ot.courier_agency,ot.tracking_id,ot.url,
            t.status as transaction_status, u.username as delivery_boy, un.username as username,us.username as seller_name,p.type,p.download_allowed'
        )
            ->join('users u', 'u.id= oi.delivery_boy_id', 'left')
            ->join('users us ', ' us.id = oi.seller_id', 'left')
            ->join('order_tracking ot ', ' ot.order_item_id = oi.id', 'left')
            ->join('orders o', 'o.id= oi.order_id', 'left')
            ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
            ->join('products p ', ' p.id = v.product_id ', 'left')
            ->join('transactions t ', ' t.order_item_id = oi.id ', 'left')
            ->join('users un ', ' un.id = o.user_id', 'left')->group_by('oi.id');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $search_res->where(" DATE(oi.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $search_res->where(" DATE(oi.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $search_res->group_Start();
            $search_res->or_like($filters);
            $search_res->group_End();
        }

        if (isset($delivery_boy_id)) {
            $search_res->where("oi.delivery_boy_id", $delivery_boy_id);
        }

        if (isset($seller_id) && $seller_id != "") {
            $search_res->where("oi.seller_id", $seller_id);
            $search_res->where("oi.active_status != 'awaiting'");
        }

        if (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) {
            $count_res->where("oi.seller_id", $_GET['seller_id']);
        }

        if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $search_res->where("o.user_id", $_GET['user_id']);
        }

        if (isset($_GET['order_status']) && !empty($_GET['order_status'])) {
            $search_res->where('oi.active_status', $_GET['order_status']);
        }
        if (isset($is_pos_order) && !empty($is_pos_order)) {
            $count_res->where('o.is_pos_order', 1);
        }
        // Filter By payment
        if (isset($_GET['payment_method']) && !empty($_GET['payment_method'])) {
            $count_res->where('payment_method', $_GET['payment_method']);
        }

        // Filter By order type
        if (isset($_GET['order_type']) && !empty($_GET['order_type']) && $_GET['order_type'] == 'physical_order') {
            $search_res->where('p.type!=', 'digital_product');
        }
        if (isset($_GET['order_type']) && !empty($_GET['order_type']) && $_GET['order_type'] == 'digital_order') {
            $search_res->where('p.type', 'digital_product');
        }


        $user_details = $search_res->order_by($sort, "DESC")->limit($limit, $offset)->get('order_items oi')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $tota_amount = 0;
        $final_total_amount = 0;
        $currency_symbol = get_settings('currency');
        $count = 1;
        foreach ($user_details as $row) {

            $temp = '';
            if (!empty($row['items'][0]['order_status'])) {
                $status = json_decode($row['items'][0]['order_status'], 1);
                foreach ($status as $st) {
                    $temp .= @$st[0] . " : " . @$st[1] . "<br>------<br>";
                }
            }

            if (trim($row['active_status']) == 'awaiting') {
                $active_status = '<label class="badge badge-secondary">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'received') {
                $active_status = '<label class="badge badge-primary">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'processed') {
                $active_status = '<label class="badge badge-info">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'shipped') {
                $active_status = '<label class="badge badge-warning">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'delivered') {
                $active_status = '<label class="badge badge-success">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'returned' || $row['active_status'] == 'cancelled') {
                $active_status = '<label class="badge badge-danger">' . $row['active_status'] . '</label>';
            }
            if ($row['active_status'] == 'return_request_decline') {
                $active_status = '<label class="badge badge-danger">' . str_replace('_', ' ', $row['active_status']) . '</label>';
            }
            if ($row['active_status'] == 'return_request_approved') {
                $active_status = '<label class="badge badge-success">' . str_replace('_', ' ', $row['active_status']) . '</label>';
            }
            if ($row['active_status'] == 'return_request_pending') {
                $active_status = '<label class="badge badge-secondary">' . str_replace('_', ' ', $row['active_status']) . '</label>';
            }

            if ($row['is_shiprocket_order'] == 1) {
                $active_status = '<label class="badge badge-secondary">' . str_replace('_', ' ', $row['active_status']) . '</label>';
            }

            if ($row['type'] == 'digital_product' && $row['download_allowed'] == 0) {
                if ($row['is_sent'] == 1) {
                    $mail_status = '<label class="badge badge-success">SENT </label>';
                } else if ($row['is_sent'] == 0) {
                    $mail_status = '<label class="badge badge-danger">NOT SENT</label>';
                } else {
                    $mail_status = '';
                }
            } else {
                $mail_status = '';
            }

            if ($row['transaction_status'] == 0 || $row['transaction_status'] == 'awaiting') {

                $transaction_status = '<label class="badge badge-primary">Awaiting</label>';
            }
            if ($row['transaction_status'] == 1 || $row['transaction_status'] == 'success') {
                $transaction_status = '<label class="badge badge-success">Success</label>';
            } else {
                $transaction_status = '<label class="badge badge-warning">' . $row['transaction_status'] . '</label>';
            }

            $status = $temp;
            $tempRow['id'] = $count;
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['order_item_id'] = $row['order_item_id'];
            $tempRow['user_id'] = $row['user_id'];
            $tempRow['seller_id'] = $row['seller_id'];
            $tempRow['notes'] = (isset($row['notes']) && !empty($row['notes'])) ? $row['notes'] : "";
            $tempRow['username'] = $row['username'];
            $tempRow['seller_name'] = $row['seller_name'];
            $tempRow['is_credited'] = ($row['is_credited']) ? '<label class="badge badge-success">Credited</label>' : '<label class="badge badge-danger">Not Credited</label>';
            $tempRow['product_name'] = $row['product_name'];
            $tempRow['product_name'] .= (!empty($row['variant_name'])) ? '(' . $row['variant_name'] . ')' : "";
            if (isset($row['mobile']) && !empty($row['mobile']) && $row['mobile'] != "" && $row['mobile'] != " ") {
                $tempRow['mobile'] =  (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($row['mobile']) - 3) . substr($row['mobile'], -3) : $row['mobile'];
            } else {
                $tempRow['mobile'] = "";
            }
            $tempRow['sub_total'] = $currency_symbol . ' ' . $row['sub_total'];
            $tempRow['quantity'] = $row['quantity'];
            $final_total_amount += $row['sub_total'];
            $tempRow['payment_method'] = ($row['payment_method'] == "COD") ? 'cash Payment' : str_replace('_', ' ', $row['payment_method']);
            $tempRow['product_variant_id'] = $row['product_variant_id'];
            $tempRow['active_status'] = $active_status;
            $tempRow['date_added'] = date('d-m-Y', strtotime($row['date_added']));
            $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['order_id'] . '" class="btn action-btn btn-primary btn-xs mr-1 ml-1 mb-1" title="View" ><i class="fa fa-eye"></i></a>';

            if ($this->ion_auth->is_delivery_boy()) {
                $operate = '<a href=' . base_url('delivery_boy/orders/edit_orders') . '?edit_id=' . $row['order_id'] . ' class="btn action-btn btn-primary btn-xs mr-1 mb-1 ml-1" title="View"><i class="fa fa-eye"></i></a>';
            } else if ($this->ion_auth->is_seller()) {
                $operate = '<a href=' . base_url('seller/orders/edit_orders') . '?edit_id=' . $row['order_id'] . ' class="btn action-btn btn-primary btn-xs mr-1 ml-1 mb-1" title="View"><i class="fa fa-eye"></i></a>';
                $operate .= '<a href="' . base_url() . 'seller/invoice?edit_id=' . $row['order_id'] . '" class="btn btn-info action-btn btn-xs ml-1 mb-1" title="Invoice" ><i class="fa fa-file"></i></a>';
                
                if ($row['download_allowed'] == 0 && $row['type'] == 'digital_product') {
                    $operate .= '<a href="javascript:void(0)" class="sendMailBtn btn action-btn btn-primary btn-xs mr-1 mb-1 ml-1" data-target="#ManageOrderSendMailModal" data-toggle="modal" title="Edit" data-email="' . $row['email']  . '" data-id="' . $row['order_item_id']  . '" data-url="seller/orders/"><i class="fas fa-paper-plane"></i></a>';
                    $operate .= '<a href="https://mail.google.com/mail/?view=cm&fs=1&tf=1&to=' . $row['email'] . '" class="btn action-btn btn-danger btn-xs ml-1 mr-1 mb-1" target="_blank"><i class="fab fa-google"></i></a>';
                    $operate .= ' <a href="javascript:void(0)" class="edit_digital_order_mails action-btn btn btn-warning btn-xs ml-1 mb-1" title="Digital Order Mails" data-order_item_id="' . $row['order_item_id'] . '"  data-target="#digital-order-mails" data-toggle="modal"><i class="far fa-envelope-open"></i></a>';
                }
            } else if ($this->ion_auth->is_admin()) {
                $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['order_id'] . ' class="btn action-btn btn-primary btn-xs mr-1 mb-1" title="View" ><i class="fa fa-eye"></i></a>';
                $operate .= '<a href="javascript:void(0)" class="delete-order-items btn action-btn btn-danger btn-xs mr-1 mb-1" data-id=' . $row['order_item_id'] . ' title="Delete" ><i class="fa fa-trash"></i></a>';
                $operate .= '<a href="' . base_url() . 'admin/invoice?edit_id=' . $row['order_id'] . '" class="btn action-btn btn-info btn-xs mr-1 mb-1" title="Invoice" ><i class="fa fa-file"></i></a>';
                if ($row['type'] != 'digital_product') {
                    $operate .= ' <a href="javascript:void(0)" class="edit_order_tracking btn btn-success action-btn btn-xs mr-1 mb-1" title="Order Tracking" data-order_id="' . $row['order_id'] . '" data-order_item_id="' . $row['order_item_id'] . '" data-seller_id="' . $row['seller_id'] . '" data-courier_agency="' . $row['courier_agency'] . '"  data-tracking_id="' . $row['tracking_id'] . '" data-url="' . $row['url'] . '" data-target="#transaction_modal" data-toggle="modal"><i class="fa fa-map-marker-alt"></i></a>';
                }
                if ($row['download_allowed'] == 0 && $row['type'] == 'digital_product') {
                    $operate .= '<a href="javascript:void(0)" class="btn sendMailBtn action-btn btn-primary btn-xs mr-1 mb-1" data-target="#ManageOrderSendMailModal" data-toggle="modal" title="Edit" data-email="' . $row['email']  . '" data-id="' . $row['order_item_id']  . '" data-url="admin/orders/"><i class="fas fa-paper-plane"></i></a>';
                    $operate .= '<a href="https://mail.google.com/mail/?view=cm&fs=1&tf=1&to=' . $row['email'] . '" class="btn action-btn btn-danger btn-xs mb-1" target="_blank"><i class="fab fa-google"></i></a>';
                    $operate .= ' <a href="javascript:void(0)" class="edit_digital_order_mails btn btn-warning action-btn btn-xs mr-1 mb-1" title="Digital Order Mails" data-order_item_id="' . $row['order_item_id'] . '"  data-target="#digital-order-mails" data-toggle="modal"><i class="far fa-envelope-open"></i></a>';
                }
            } else {
                $operate = "";
            }
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $count++;
        }
        if (!empty($user_details)) {
            $tempRow['id'] = '-';
            $tempRow['order_id'] = '-';
            $tempRow['order_item_id'] = '-';
            $tempRow['user_id'] = '-';
            $tempRow['seller_id'] = '-';
            $tempRow['username'] = '-';
            $tempRow['seller_name'] = '-';
            $tempRow['is_credited'] = '-';
            $tempRow['mobile'] = '-';
            $tempRow['delivery_charge'] = '-';
            $tempRow['product_name'] = '-';
            $tempRow['sub_total'] = '<span class="badge badge-danger">' . $currency_symbol . ' ' . $final_total_amount . '</span>';
            $tempRow['discount'] = '-';
            $tempRow['quantity'] = '-';
            $tempRow['delivery_boy'] = '-';
            $tempRow['delivery_time'] = '-';
            $tempRow['status'] = '-';
            $tempRow['active_status'] = '-';
            $tempRow['transaction_status'] = '-';
            $tempRow['date_added'] = '-';
            $tempRow['operate'] = '-';
            $tempRow['mail_status'] = '-';
            array_push($rows, $tempRow);
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
}

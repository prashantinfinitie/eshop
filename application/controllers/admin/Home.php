<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'function_helper', 'bootstrap_table_helper', 'file']);
        $this->load->model(['Home_model', 'Order_model', 'Cart_model']);
    }

    public function index()
    {

        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = FORMS . 'home';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Admin Panel | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Admin Panel | ' . $settings['app_name'];
            $this->data['curreny'] = get_settings('currency');
            $this->data['order_counter'] = $this->Home_model->count_dashboard_orders();
            $this->data['user_counter'] = $this->Home_model->count_new_users();
            $this->data['delivery_boy_counter'] = $this->Home_model->count_delivery_boys();
            $this->data['product_counter'] = $this->Home_model->count_products();
            $this->data['count_products_low_status'] = $this->Home_model->count_products_stock_low_status();
            $this->data['count_products_availability_status'] = $this->Home_model->count_products_availability_status();
            $this->data['total_earnings'] = $this->Home_model->total_earnings($type = 'overall');
            $this->data['admin_earnings'] = $this->Home_model->total_earnings($type = 'admin');
            $this->data['seller_earnings'] = $this->Home_model->total_earnings($type = 'seller');
            $orders_count['awaiting'] = orders_count("awaiting");
            $orders_count['received'] = orders_count("received");
            $orders_count['processed'] = orders_count("processed");
            $orders_count['shipped'] = orders_count("shipped");
            $orders_count['delivered'] = orders_count("delivered");
            $orders_count['cancelled'] = orders_count("cancelled");
            $orders_count['returned'] = orders_count("returned");
            $orders_count['draft'] = orders_count("draft");
            $orders_count['return_request_approved'] = orders_count("return_request_approved");
            $orders_count['return_request_pending'] = orders_count("return_request_pending");
            $this->data['status_counts'] = $orders_count;
            $this->data['approved_sellers'] = $this->Home_model->approved_seller();
            $this->data['count_approved_sellers'] = $this->Home_model->count_approved_seller();
            $this->data['not_approved_sellers'] = $this->Home_model->not_approved_seller();
            $this->data['count_not_approved_sellers'] = $this->Home_model->count_not_approved_seller();
            $this->data['deactive_sellers'] = $this->Home_model->deactive_seller();
            $this->data['count_deactive_sellers'] = $this->Home_model->count_deactive_seller();

            $this->load->view('admin/template', $this->data);
        } elseif (isset($_SESSION) && isset($_SESSION["user_id"])) {
            $user_id = $_SESSION["user_id"];
            $user_group = fetch_details('users_groups', ['user_id' => $user_id], 'group_id');
            $group_id = $user_group[0]['group_id'];
            if ($group_id == 2) {
                redirect('home', 'refresh');
            } else {
                redirect('admin/login', 'refresh');
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }
    public function reset_password()
    {
        /* Parameters to be passed
            mobile_no:7894561235            
            new: pass@123
        */
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            $this->response['error'] = true;
            $this->response['message'] = DEMO_VERSION_MSG;
            echo json_encode($this->response);
            return false;
        }
        
        $this->form_validation->set_rules('mobile', 'Mobile No', 'trim|numeric|required|xss_clean|max_length[16]');
        $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean');

        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            print_r(json_encode($this->response));
            return false;
        }
        $mobile = $this->input->post('mobile', true);
        $new_password = $this->input->post('new_password', true);
        $identity_column = $this->config->item('identity', 'ion_auth');
        $res = fetch_details('users', ['mobile' => $mobile]);
        if (!empty($res)) {
            $identity = ($identity_column  == 'email') ? $res[0]['email'] : $res[0]['mobile'];
            if (!$this->ion_auth->reset_password($identity, $new_password)) {
                $this->response['error'] = true;
                $this->response['message'] = $this->ion_auth->messages();
                $this->response['data'] = array();
                echo json_encode($this->response);
                return false;
            } else {
                $this->response['error'] = false;
                $this->response['message'] = 'Reset Password Successfully';
                $this->response['data'] = array();
                echo json_encode($this->response);
                return false;
            }
        } else {
            $this->response['error'] = true;
            $this->response['message'] = 'User does not exists !';
            $this->response['data'] = array();
            echo json_encode($this->response);
            return false;
        }
    }

    public function category_wise_product_sales()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $res = $this->db->select('c.name as category,count(oi.product_variant_id) as sales')
                ->join(' `product_variants` `pv` ', 'oi.`product_variant_id`=pv.`id`')
                ->join(' `products` p  ', ' pv.`product_id`=p.`id` ')
                ->join(' categories c ', ' p.category_id=c.id ')
                ->group_by('p.category_id')->get('`order_items` oi')->result_array();
            $response['category'] = array_column($res, 'category');
            $response['sales'] = array_column($res, 'sales');
            echo json_encode($response);
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function fetch_sales()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $sales = [];

            $current_year = date('Y');
            $current_month = date('m');

            // --- Month-wise sales for current year ---
            $all_months = [
                'Jan' => 0,
                'Feb' => 0,
                'Mar' => 0,
                'Apr' => 0,
                'May' => 0,
                'Jun' => 0,
                'Jul' => 0,
                'Aug' => 0,
                'Sep' => 0,
                'Oct' => 0,
                'Nov' => 0,
                'Dec' => 0
            ];

            $month_res = $this->db->select('SUM(final_total) AS total_sale, DATE_FORMAT(date_added,"%b") AS month_name')
                ->where('YEAR(date_added)', $current_year)
                ->group_by('MONTH(date_added)')
                ->order_by('MONTH(date_added)')
                ->get('orders')->result_array();

            // Update the all_months array with sales data
            foreach ($month_res as $sale) {
                if (isset($all_months[$sale['month_name']])) {
                    $all_months[$sale['month_name']] = (float)$sale['total_sale'];
                }
            }

            // Format the data for the final response
            $month_wise_sales = [
                'total_sale' => array_values($all_months),
                'month_name' => array_keys($all_months)
            ];
            $sales[0] = $month_wise_sales;

            // --- Week-wise sales for current year (current week only) ---
            $all_days = [
                'Sunday' => 0,
                'Monday' => 0,
                'Tuesday' => 0,
                'Wednesday' => 0,
                'Thursday' => 0,
                'Friday' => 0,
                'Saturday' => 0
            ];

            $d = strtotime("today");
            $start_week = strtotime("last sunday midnight", $d);
            $end_week = strtotime("next saturday", $d);
            $start = date("Y-m-d", $start_week);
            $end = date("Y-m-d", $end_week);

            $week_res = $this->db->select("DATE_FORMAT(date_added, '%Y-%m-%d') as date, SUM(final_total) as total_sale")
                ->where("date(date_added) >= '$start' and date(date_added) <= '$end'")
                ->where('YEAR(date_added)', $current_year)
                ->group_by('DAY(date_added)')
                ->get('orders')->result_array();



            foreach ($week_res as $sale) {
                // Convert the 'date' field to a timestamp to get the day of the week
                $day_name = date('l', strtotime($sale['date'])); // 'l' gives the full day name (Monday, Tuesday, etc.)

                // Add the sales total to the correct day
                if (isset($all_days[$day_name])) {
                    $all_days[$day_name] = (float)$sale['total_sale'];
                }
            }

            // Format the data for the final response
            $week_wise_sales = [
                'total_sale' => array_values($all_days),  // Get just the sales figures
                'week' => array_keys($all_days)       // Get just the day names
            ];
            $sales[1] = $week_wise_sales;

            // --- Day-wise sales for current year (last 30 days of current year) ---
            $day_res = $this->db->select("DAY(date_added) as date, SUM(final_total) as total_sale")
                ->where('date_added >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)')
                ->where('YEAR(date_added)', $current_year)
                ->where('MONTH(date_added)', $current_month)
                ->group_by('DAY(date_added)')
                ->get('orders')->result_array();

            $all_days = array_fill(0, 30, 0);

            foreach ($day_res as $sale) {
                $day_of_month = (int)$sale['date'];
                if ($day_of_month > 0 && $day_of_month <= 30) {
                    $all_days[$day_of_month - 1] = (float)$sale['total_sale'];
                }
            }

            $day_wise_sales = [
                'total_sale' => $all_days,
                'day' => range(1, 30)
            ];
            $sales[2] = $day_wise_sales;

            print_r(json_encode($sales));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function category_wise_product_count()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $res = $this->db->select('c.name as name,count(c.id) as counter')->where(['p.status' => '1', 'c.status' => '1'])->join('products p', 'p.category_id=c.id')->group_by('c.id')->get('categories c')->result_array();
            $result = array();
            $result[0][] = 'Task';
            $result[0][] = 'Hours per Day';
            array_walk($res, function ($v, $k) use (&$result) {
                $result[$k + 1][] = $v['name'];
                $result[$k + 1][] = intval($v['counter']);
            });
            echo json_encode(array_values($result));
        } else {
            redirect('admin/login', 'refresh');
        }
    }


    public function delete_image()
    {
        $id = $this->input->post('id', true);
        $path = $this->input->post('path', true);
        $field = $this->input->post('field', true);
        $img_name = $this->input->post('img_name', true);
        $table_name = $this->input->post('table_name', true);
        $isjson = $this->input->post('isjson', true);

        $this->response['is_deleted'] = delete_image($id, $path, $field, $img_name, $table_name, $isjson);
        $this->response['csrfName'] = $this->security->get_csrf_token_name();
        $this->response['csrfHash'] = $this->security->get_csrf_hash();
        print_r(json_encode($this->response));
    }
    public function logout()
    {
        $this->ion_auth->logout();
        redirect('admin/login', 'refresh');
    }

    public function profile()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $identity_column = $this->config->item('identity', 'ion_auth');
            $this->data['users'] = $this->ion_auth->user()->row();
            $settings = get_settings('system_settings', true);
            $this->data['identity_column'] = $identity_column;
            $this->data['main_page'] = FORMS . 'profile';
            $this->data['title'] = 'Profile | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Profile | ' . $settings['app_name'];
            $this->load->view('admin/template', $this->data);
        } else {
            redirect('admin/home', 'refresh');
        }
    }

    public function update_status()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                $this->response['error'] = true;
                $this->response['message'] = DEMO_VERSION_MSG;
                echo json_encode($this->response);
                return false;
                exit();
            }

            // Toggle status based on the input
            if ($_GET['status'] == '1') {
                $_GET['status'] = 0;
            } else if ($_GET['status'] == '2') {
                $_GET['status'] = 1;
            } else {
                $_GET['status'] = 1;
            }

            $this->db->trans_start();

            if ($_GET['table'] == 'users') {
                // Update the 'active' field for users table
                $this->db->set('active', $this->db->escape($_GET['status']));
            } else if ($_GET['table'] == 'attribute_values') {
                $attribute_id = $_GET['id']; // Assuming 'attribute_id' is passed via GET
                $attribute_status = $_GET['status']; // Assuming 'attribute_id' is passed via GET

                $this->db->select('*');
                $this->db->from('product_attributes');
                $this->db->where('find_in_set("' . $attribute_id . '", attribute_value_ids) > 0');
                $query = $this->db->get()->result_array();

                if (count($query) > 0 && $attribute_status == 0) {
                    $response['error'] = true;
                    $response['csrfName'] = $this->security->get_csrf_token_name();
                    $response['csrfHash'] = $this->security->get_csrf_hash();
                    $response['message'] = 'This attribute is in use . you cannot deactivate it anymore';
                    print_r(json_encode($response));
                    exit();
                } else {
                    // Update based on FIND_IN_SET
                    $this->db->set('status', $this->db->escape($_GET['status']));
                }
            } else {
                // Update the status for other tables
                $this->db->set('status', $this->db->escape($_GET['status']));
            }

            // Update the specified table
            $this->db->where('id', $_GET['id'])->update($_GET['table']);
            $this->db->trans_complete();

            $error = false;
            $message = str_replace('_', ' ', $_GET['table']);
            if ($this->db->trans_status() === true) {
                $error = true;
            }

            // Prepare the response
            $response['error'] = $error;
            $response['csrfName'] = $this->security->get_csrf_token_name();
            $response['csrfHash'] = $this->security->get_csrf_hash();
            $response['message'] = $message;
            print_r(json_encode($response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    // send admin notification
    public function get_notification()
    {
        $count_noti = fetch_details('system_notification',  ["read_by" => 0],  'count(id) as total');

        $response['error'] = false;
        $response['count_notifications'] = $count_noti[0]['total'];

        print_r(json_encode($response));
    }

    public function new_notification_list()
    {

        $notifications = fetch_details('system_notification', ["read_by" => 0],  '*',  '3', '0',  'id', 'DESC',  '',  '');

        $response['error'] = false;
        $response['notifications'] = $notifications;

        print_r(json_encode($response));
    }
}

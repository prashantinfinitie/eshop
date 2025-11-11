<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

    public function __construct()
    {
        // print_R("here3");
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language']);
        $this->load->model(['Home_model', 'Order_model', 'affiliate_model','affiliate_transaction_model']);
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {

            $user_id = $this->session->userdata('user_id');
            $user_res = $this->db->select('balance,username')->where('id', $user_id)->get('users')->result_array();
            $this->data['main_page'] = VIEW . 'home';
            $settings = get_settings('system_settings', true);
            $this->data['currency'] = get_settings('currency');
            $this->data['title'] = 'Affiliate Panel | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Affiliate Panel | ' . $settings['app_name'];

            $res = $this->db->select('p.name as product_name,p.image as product_image, SUM(usage_count) as sales')
                ->join(' products p ', ' at.product_id=p.id ')
                ->group_by('at.product_id')->order_by('usage_count', 'DESC')->get('affiliate_tracking at')->result_array();

            $earning_data = $this->affiliate_transaction_model->get_affiliate_commission_summary($user_id);

            $this->data['earning_data'] = $earning_data;

            $affiliate_categories = fetch_details('categories', ['is_in_affiliate' => 1], '*', 9);

            $this->data['affiliate_categories'] = $affiliate_categories;

            $this->data['top_products'] = $res;


            $this->load->view('affiliate/template', $this->data);
            // } elseif (isset($_SESSION) && isset($_SESSION["user_id"])) {
            //     $user_id = $_SESSION["user_id"];
            //     $user_group = fetch_details('users_groups', ['user_id' => $user_id], 'group_id');
            //     $group_id = $user_group[0]['group_id'];
            //     if ($group_id == 2) {
            //         redirect('home', 'refresh');
            //     } else {
            //         redirect('affiliate/login', 'refresh');
            //     }
        } else {
            redirect('affiliate/login', 'refresh');
        }
    }

    public function profile()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {
            $identity_column = $this->config->item('identity', 'ion_auth');
            $settings = get_settings('system_settings', true);
            $user_id = $this->session->userdata('user_id');
            $this->data['identity_column'] = $identity_column;
            $this->data['main_page'] = FORMS . 'profile';
            $this->data['title'] = 'Affiliate Profile | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Affiliate Profile | ' . $settings['app_name'];
            $shipping_method = get_settings('shipping_method', true);
            $this->data['shipping_method'] = $shipping_method;


            $this->data['fetched_data'] = $this->db->select(' u.*,af.*, af.status as affiliate_user_status')
                ->join('users_groups ug', ' ug.user_id = u.id ')
                ->join('affiliates af', ' af.user_id = u.id ')
                ->where(['ug.group_id' => '5', 'ug.user_id' => $user_id])
                ->get('users u')
                ->result_array();
            $this->load->view('affiliate/template', $this->data);
        } else {
            redirect('affiliate/home', 'refresh');
        }
    }

    public function logout()
    {
        $this->ion_auth->logout();
        redirect('affiliate/login', 'refresh');
    }

    public function fetch_sales()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {
            $sales = [];

            $user_id = $_SESSION['user_id'];

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

            $month_res = $this->db->select('SUM(amount) AS total_sale, DATE_FORMAT(created_at,"%b") AS month_name')
                ->where('YEAR(created_at)', $current_year)
                ->where('reference_type', 'order')
                ->where('user_id', $user_id)
                ->group_by('MONTH(created_at)')
                ->order_by('MONTH(created_at)')
                ->get('affiliate_wallet_transactions')->result_array();

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

            $week_res = $this->db->select("DATE_FORMAT(created_at, '%Y-%m-%d') as date, SUM(amount) as total_sale")
                ->where('reference_type', 'order')
                ->where('user_id', $user_id)
                ->where("date(created_at) >= '$start' and date(created_at) <= '$end'")
                ->where('YEAR(created_at)', $current_year)
                ->group_by('DAY(created_at)')
                ->get('affiliate_wallet_transactions')->result_array();



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
            $day_res = $this->db->select("DAY(created_at) as date, SUM(amount) as total_sale")
                ->where('reference_type', 'order')
                ->where('user_id', $user_id)
                ->where('created_at >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)')
                ->where('YEAR(created_at)', $current_year)
                ->where('MONTH(created_at)', $current_month)
                ->group_by('DAY(created_at)')
                ->get('affiliate_wallet_transactions')->result_array();

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
            redirect('affiliate/login', 'refresh');
        }
    }

    public function most_selling_affiliate_categories()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {

            $user_id = $_SESSION['user_id'];
            $res = $this->db->select('c.name as category,count(at.category_id) as sales')
                ->where('usage_count !=', 0)
                ->where('affiliate_id', $user_id)
                ->join(' categories c ', ' at.category_id=c.id ')
                ->group_by('at.category_id')->get('affiliate_tracking at')->result_array();
            $response['category'] = array_column($res, 'category');
            $response['sales'] = array_column($res, 'sales');
            print_R(json_encode($response));
        } else {
            redirect('affiliate/login', 'refresh');
        }
    }
    // public function most_selling_affiliate_products()
    // {
    //     if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {
    //         $res = $this->db->select('p.name as product_name,SUM(usage_count) as sales')
    //             ->join(' products p ', ' at.product_id=p.id ')
    //             ->group_by('at.product_id')->get('affiliate_tracking at')->result_array();
    //         // $response['products'] = array_column($res, 'products');
    //         // $response['sales'] = array_column($res, 'sales');
    //         print_R(json_encode($response));
    //     } else {
    //         redirect('affiliate/login', 'refresh');
    //     }
    // }


    public function remove_affiliate()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_affiliate_user()) {

            if (!isset($_GET['id']) && empty($_GET['id'])) {
                $this->response['error'] = true;
                $this->response['message'] = 'Affiliate id is required';
                print_r(json_encode($this->response));
                return;
                exit();
            }
            $all_status = [0, 1, 2, 7];
            $status = $this->input->get('status', true);

            $id = $this->input->get('id', true);
            if (!in_array($status, $all_status)) {
                $this->response['error'] = true;
                $this->response['message'] = 'Invalid status';
                print_r(json_encode($this->response));
                return;
                exit();
            }
            if ($status == 2) {
                $this->response['error'] = true;
                $this->response['message'] = 'Please approve affiliate first for delete only affiliate.';
                print_r(json_encode($this->response));
                return;
                exit();
            }

            if ($status == 7) {
                update_details(['status' => $status], ['user_id' => $id], 'affiliates');
                $this->response['error'] = false;
                $this->response['message'] = 'Your account removal request processed at this time.There are pending wallet balances or unsettled orders associated with your account. Please ensure all transactions are completed before proceeding with the account deletion request.';
                print_r(json_encode($this->response));
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Something Went Wrong';
                print_r(json_encode($this->response));
            }
        } else {
            redirect('affiliate/login', 'refresh');
        }
    }
}

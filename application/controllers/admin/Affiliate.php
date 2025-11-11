<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Affiliate extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model(['Setting_model', 'Home_model', 'affiliate_model']);
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = AFFILIATE . 'dashboard';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Dashboard | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Dashboard | ' . $settings['app_name'];
            $this->data['currency'] = get_settings('currency');
            $this->data['order_counter'] = $this->affiliate_model->count_orders_by_affiliate();
            $this->data['user_counter'] = $this->affiliate_model->count_affiliate_users();
            $this->data['admin_earnings_via_affiliate'] = $this->affiliate_model->total_admin_earnings_via_affiliate($type = 'admin');
            $res = $this->db->select('p.name as product_name,p.image as product_image, SUM(usage_count) as sales')
                ->join(' products p ', ' at.product_id=p.id ')
                ->group_by('at.product_id')->order_by('usage_count', 'DESC')->get('affiliate_tracking at')->result_array();

            $this->data['top_products'] = $res;

            

            $this->load->view('admin/template', $this->data);
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

            $month_res = $this->db->select('SUM(amount) AS total_sale, DATE_FORMAT(created_at,"%b") AS month_name')
                ->where('YEAR(created_at)', $current_year)
                ->where('reference_type', 'order')
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
            redirect('admin/login', 'refresh');
        }
    }

    public function most_selling_affiliate_categories()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $res = $this->db->select('c.name as category,count(at.category_id) as sales')
                ->where('usage_count !=', 0)
                ->join(' categories c ', ' at.category_id=c.id ')
                ->group_by('at.category_id')->get('affiliate_tracking at')->result_array();
            $response['category'] = array_column($res, 'category');
            $response['sales'] = array_column($res, 'sales');
            print_R(json_encode($response));
        } else {
            redirect('admin/login', 'refresh');
        }
    }
}

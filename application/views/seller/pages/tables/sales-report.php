<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>View Sale Reports</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('seller/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Sales Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 main-content">
                    <div class="card content-area p-4">
                        <div class="card-innr">
                            <div class="gaps-1-5x">
                                <div class="row d-flex align-items-end flex-wrap">
                                    <!-- Date Filter -->
                                    <div class="form-group col-12 col-md-3 mb-3">
                                        <label>From & To Date</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-clock"></i></span>
                                            </div>
                                            <input type="text" class="form-control" id="datepicker">
                                            <input type="hidden" id="start_date" class="form-control">
                                            <input type="hidden" id="end_date" class="form-control">
                                        </div>
                                    </div>
                                    <!-- Payment Method Filter -->
                                    <div class="form-group col-12 col-md-3 mb-3">
                                        <label>Payment Method</label>
                                        <select id="payment_method_filter" class="form-control">
                                            <option value="">All</option>
                                            <?php
                                            $this->db->select('payment_method, COUNT(*) as usage_count')
                                                ->from('orders')
                                                ->where('payment_method IS NOT NULL')
                                                ->where('payment_method !=', '')
                                                ->group_by('payment_method')
                                                ->order_by('usage_count', 'DESC');
                                            $payment_methods = $this->db->get()->result_array();
                                            foreach ($payment_methods as $method) {
                                                $value = $method['payment_method'];
                                                $label = ucfirst(str_replace('_', ' ', $value));
                                                echo '<option value="' . htmlspecialchars($value) . '">' . htmlspecialchars($label) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <!-- Order Status Filter -->
                                    <div class="form-group col-12 col-md-3 mb-3">
                                        <label>Order Status</label>
                                        <select id="order_status_filter" class="form-control">
                                            <option value="">All</option>
                                            <?php
                                            $preferred_order = [
                                                'awaiting' => 'Awaiting',
                                                'received' => 'Received',
                                                'processed' => 'Processed',
                                                'shipped' => 'Shipped',
                                                'delivered' => 'Delivered',
                                                'cancelled' => 'Cancelled',
                                                'returned' => 'Returned',
                                                'return_request_pending' => 'Return Request Pending',
                                                'return_request_approved' => 'Return Request Approved',
                                                'return_request_decline' => 'Return Request Declined'
                                            ];
                                            $this->db->select('active_status')
                                                ->from('order_items')
                                                ->distinct();
                                            $db_statuses = $this->db->get()->result_array();
                                            $db_status_values = array_column($db_statuses, 'active_status');
                                            foreach ($preferred_order as $value => $label) {
                                                if (in_array($value, $db_status_values)) {
                                                    echo '<option value="' . htmlspecialchars($value) . '">' . htmlspecialchars($label) . '</option>';
                                                }
                                            }
                                            $other_statuses = array_diff($db_status_values, array_keys($preferred_order));
                                            sort($other_statuses);
                                            foreach ($other_statuses as $status_value) {
                                                $status_label = ucfirst(str_replace('_', ' ', $status_value));
                                                echo '<option value="' . htmlspecialchars($status_value) . '">' . htmlspecialchars($status_label) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <!-- Filter and Clear Button -->
                                    <div class="form-group col-3 d-flex justify-content-end gap-2 mb-3">
                                        <button type="button" class="btn btn-outline-danger btn-sm mr-2" onclick="resetfilters()" aria-label="Clear Filters">Clear</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm mr-2" onclick="status_date_wise_search()" aria-label="Apply Filters">Filter</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card shadow-sm p-4 mb-4 bg-light border-left-primary">
                                <h6 class="font-weight-bold text-primary">
                                    <i class="fas fa-coins mr-2"></i> Total Order Value:
                                    <span id="total-order-sum" class="text-dark"><?= $curreny ?> 0.00</span>
                                </h6>
                            </div>

                            <table id="sales-report-table" class="table table-striped"
                                data-detail-view="true"
                                data-detail-formatter="salesReport"
                                data-auto-refresh="true"
                                data-toggle="table"
                                data-url="<?= base_url('seller/Sales_report/get_seller_sales_report_list') ?>"
                                data-side-pagination="server"
                                data-pagination="true"
                                data-page-list="[5, 10, 25, 50, 100, 200, All]"
                                data-search="true"
                                data-trim-on-search="false"
                                data-show-columns="true"
                                data-show-columns-search="true"
                                data-show-refresh="true"
                                data-mobile-responsive="true"
                                data-sort-name="id"
                                data-sort-order="DESC"
                                data-toolbar=""
                                data-show-export="true"
                                data-maintain-selected="true"
                                data-query-params="sales_report_query_params"
                                data-export-types='["txt","excel"]'>
                                <thead>
                                    <tr>
                                        <th data-field="id" data-sortable="true"><?= labels('id', 'Item ID') ?></th>
                                        <th data-field="product_name" data-sortable="true"><?= labels('product_name', 'Product name') ?></th>
                                        <th data-field="final_total" data-sortable="true"><?= labels('final_total', 'Final Total') ?></th>
                                        <th data-field="payment_method" data-sortable="true"><?= labels('payment_method', 'Payment Method') ?></th>
                                        <th data-field="date_added" data-sortable="true"><?= labels('date_added', 'Order Date') ?></th>
                                        <th data-field="active_status" data-sortable="true"><?= labels('active_status', 'Order Status') ?></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- JavaScript -->
    <script>
        $(document).ready(function() {
            $('#sales-report-table').on('load-success.bs.table', function(e, data) {
                if (data && data.total_order_sum) {
                    $('#total-order-sum').text('<?= $curreny ?> ' + data.total_order_sum);
                } else {
                    $('#total-order-sum').text('<?= $curreny ?> 0.00');
                }
            });
        });
    </script>
</div>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Manage Orders</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('shipping_company/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Orders</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 main-content">
                    <div class="card content-area p-4">
                        <div class="card-innr">
                            <div class="gaps-1-5x row d-flex adjust-items-center">
                                <div class="form-group col-md-4">
                                    <label>Date and time range:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                                        </div>
                                        <input type="text" class="form-control float-right" id="datepicker">
                                        <input type="hidden" id="start_date" class="form-control float-right">
                                        <input type="hidden" id="end_date" class="form-control float-right">
                                    </div>
                                </div>

                                <div class="form-group col-md-8">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Filter By Status</label>
                                            <select id="order_status" name="order_status" class="form-control">
                                                <option value="">All Orders</option>
                                                <option value="received">Received</option>
                                                <option value="processed">Processed</option>
                                                <option value="shipped">Shipped</option>
                                                <option value="delivered">Delivered</option>
                                                <option value="cancelled">Cancelled</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-5">
                                            <label>Filter By Payment Method</label>
                                            <select id="payment_method" name="payment_method" class="form-control">
                                                <option value="">All Payment Methods</option>
                                                <option value="COD">Cash On Delivery</option>
                                                <option value="online-payment">Online Payment</option>
                                            </select>
                                        </div>

                                        <div class="col-md-2 mt-4">
                                            <button type="button" class="btn btn-default mt-2" onclick="status_date_wise_search()">Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 main-content">
                                <div class="card content-area p-4">
                                    <div class="card-innr" id="orders_table">
                                        <table class='table-striped' data-toggle="table"
                                            data-url="<?= base_url('shipping_company/orders/view_orders') ?>"
                                            data-click-to-select="true"
                                            data-side-pagination="server"
                                            data-pagination="true"
                                            data-page-list="[5, 10, 20, 50, 100, 200]"
                                            data-search="true"
                                            data-show-columns="true"
                                            data-show-refresh="true"
                                            data-trim-on-search="false"
                                            data-sort-name="oi.id"
                                            data-sort-order="desc"
                                            data-mobile-responsive="true"
                                            data-show-export="true"
                                            data-maintain-selected="true"
                                            data-export-types='["txt","excel","csv"]'
                                            data-export-options='{"fileName": "shipping-orders-list","ignoreColumn": ["state"] }'
                                            data-query-params="orders_query_params">
                                            <thead>
                                                <tr>
                                                    <th data-field="id" data-sortable='true' data-footer-formatter="totalFormatter">ID</th>
                                                    <th data-field="order_item_id" data-sortable='true'>Order Item ID</th>
                                                    <th data-field="order_id" data-sortable='true'>Order ID</th>
                                                    <th data-field="user_id" data-sortable='true' data-visible="false">User ID</th>
                                                    <th data-field="seller_id" data-sortable='true' data-visible="false">Seller ID</th>
                                                    <th data-field="quantity" data-sortable='true' data-visible="false">Quantity</th>
                                                    <th data-field="username" data-sortable='true'>Customer Name</th>
                                                    <th data-field="seller_name" data-sortable='true'>Seller Name</th>
                                                    <th data-field="product_name" data-sortable='true'>Product Name</th>
                                                    <th data-field="mobile" data-sortable='true' data-visible='false'>Mobile</th>
                                                    <th data-field="sub_total" data-sortable='true' data-visible="true">Total(<?= $curreny ?>)</th>
                                                    <th data-field="product_variant_id" data-sortable='true' data-visible='false'>Product Variant Id</th>
                                                    <th data-field="delivery_date" data-sortable='true' data-visible='false'>Delivery Date</th>
                                                    <th data-field="delivery_time" data-sortable='true' data-visible='false'>Delivery Time</th>
                                                    <th data-field="updated_by" data-sortable='true' data-visible="false">Updated by</th>
                                                    <th data-field="active_status" data-sortable='true' data-visible='true'>Status</th>
                                                    <th data-field="date_added" data-sortable='true'>Order Date</th>
                                                    <th data-field="operate" data-sortable="false">Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>



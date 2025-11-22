<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row pt-4">
                <div class="col-xl col-lg col-md col-12">
                    <div class="card pull-up">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="align-self-center text-warning">
                                        <i class="ion-ios-cart-outline display-4"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h5 class="text-muted text-bold-500">Orders</h5>
                                        <h3 class="text-bold-600"><?= ($order_counter ?? 0) ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (isset($bonus) && $bonus > 0) { ?>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                        <div class="card pull-up">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center text-primary">
                                            <i class="fas fa-wallet fa-3x"></i>
                                        </div>
                                        <div class="media-body text-right">
                                            <h5 class="text-muted text-bold-500">Bonus</h5>
                                            <h3 class="text-bold-600"><?= ($bonus) ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="col-xl col-lg col-md col-12">
                    <div class="card pull-up">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="align-self-center text-success">
                                        <i class="ion-cash display-4"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h5 class="text-muted text-bold-500">Balance</h5>
                                        <h3 class="text-bold-600"><?= ($curreny . ' ' . number_format($balance ?? 0, 2)) ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 main-content">
                    <div class="card content-area p-4">
                        <div class="card-innr">
                            <div class="gaps-1-5x row d-flex adjust-items-center">
                                <div class="row col-md-12">
                                    <div class="form-group col-md-4">
                                        <label>Date and time range:</label>
                                        <div class="input-group col-md-12">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-clock"></i></span>
                                            </div>
                                            <input type="text" class="form-control float-right" id="datepicker">
                                            <input type="hidden" id="start_date" class="form-control float-right">
                                            <input type="hidden" id="end_date" class="form-control float-right">
                                        </div>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Filter By status</label>
                                        <select id="order_status" name="order_status" class="form-control">
                                            <option value="">All Orders</option>
                                            <option value="processed">Processed</option>
                                            <option value="shipped">Shipped</option>
                                            <option value="delivered">Delivered</option>
                                            <option value="returned">Returned</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>Filter By Payment Method</label>
                                        <select id="payment_method" name="payment_method" class="form-control">
                                            <option value="">All Payment Methods</option>
                                            <option value="COD">Cash On Delivery</option>
                                            <option value="online-payment">Online Payment</option>
                                        </select>
                                    </div>

                                    <div class="form-group d-flex align-items-center pt-4">
                                        <button type="button" class="btn btn-default mt-2" onclick="status_date_wise_search()">Filter</button>
                                    </div>
                                </div>
                            </div>

                            <div class="card content-area p-4">
                                <div class="card-innr">
                                    <h4">Orders</h3>
                                        <div class="tab-content">
                                            <div id="orders_table" class="tab-pane active"><br>
                                                <table class='table-striped' data-toggle="table" data-url="<?= base_url('shipping_company/orders/view_orders') ?>"
                                                    data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                                    data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="id"
                                                    data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true"
                                                    data-export-types='["txt","excel","csv"]' data-export-options='{
                                                    "fileName": "shipping-orders-list",
                                                    "ignoreColumn": ["state"]
                                                   }' data-query-params="home_query_params">
                                                    <thead>
                                                        <tr>
                                                            <th data-field="id" data-sortable='true' data-footer-formatter="totalFormatter">ID</th>
                                                            <th data-field="order_id" data-sortable='true'>Order ID</th>
                                                            <th data-field="username" data-sortable='true'>Buyer Name</th>
                                                            <th data-field="mobile" data-sortable='false'>Buyer Mobile</th>
                                                            <th data-field="product_name" data-sortable='true'>Product Name</th>
                                                            <th data-field="quantity" data-sortable='false'>Quantity</th>
                                                            <th data-field="active_status" data-sortable='true'>Status</th>
                                                            <th data-field="payment_method" data-sortable='true'>Payment Method</th>
                                                            <th data-field="date_added" data-sortable='true'>Order Date</th>
                                                            <th data-field="operate" data-sortable="false">Action</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div><!-- .card-innr -->
                    </div><!-- .card -->
                </div>
            </div>
        </div>
    </section>
</div>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>View Sale Inventory Reports</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Sales Inventory Reports</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Top Section: Pie Chart and Filters -->
            <div class="row mt-3">
                <!-- Pie Chart (Left) -->
                <div class="col-md-6 col-12 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Top Selling Products</h3>
                        </div>
                        <div class="card-body">
                            <div id="sales_piechart_3d" class="piechat_height"></div>
                        </div>
                    </div>
                </div>
                <!-- Filters (Right) -->
                <div class="col-md-6 col-12 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Filters</h3>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="row">
                                <div class="form-group col-md-12 col-12">
                                    <label>From & To Date</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                                        </div>
                                        <input type="text" class="form-control float-right" id="datepicker">
                                        <input type="hidden" id="start_date" class="form-control float-right">
                                        <input type="hidden" id="end_date" class="form-control float-right">
                                    </div>
                                </div>
                            </div>

                            <!-- Button aligned bottom-left -->
                            <div class="d-flex justify-content-start mt-auto pt-2">
                                <button type="button" class="btn btn-outline-danger btn-sm mr-2" onclick="resetfilters()" aria-label="Clear Filters">Clear</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="status_date_wise_search()">Filter</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <!-- Bottom Section: Table -->
            <div class="row">
                <div class="col-md-12 main-content">
                    <div class="card content-area p-4">
                        <div class="card-innr">
                            <table class="table-striped"
                                data-toggle="table"
                                data-url="<?= base_url('seller/Sales_inventory/get_seller_sales_inventory_list') ?>"
                                data-click-to-select="true"
                                data-side-pagination="server"
                                data-pagination="true"
                                data-page-list="[5, 10, 20, 50, 100, 200]"
                                data-search="true"
                                data-show-columns="true"
                                data-show-refresh="true"
                                data-trim-on-search="false"
                                data-sort-name="id"
                                data-sort-order="desc"
                                data-mobile-responsive="true"
                                data-toolbar=""
                                data-show-export="true"
                                data-maintain-selected="true"
                                data-export-types='["txt","excel"]'
                                data-query-params="sales_inventory_report_query_params">
                                <thead>
                                    <tr>
                                        <th data-field="id" data-sortable="true">Item ID</th>
                                        <th data-field="name" data-sortable="true">Product name</th>
                                        <th data-field="stock" data-sortable="true">Stock</th>
                                        <th data-field="qty" data-sortable="true">Orders Placed</th>
                                    </tr>
                                </thead>
                            </table>
                        </div><!-- .card-innr -->
                    </div><!-- .card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
</div>
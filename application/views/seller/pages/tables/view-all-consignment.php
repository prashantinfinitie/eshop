<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Consignments</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('seller/home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('seller/orders') ?>">Orders</a></li>
                        <li class="breadcrumb-item active">Consignments</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <table class='table-striped' data-toggle="table" data-url="<?= base_url('seller/orders/consignment_view') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="o.id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-export-types='["txt","excel","csv"]' data-export-options='{"fileName": "orders-list","ignoreColumn": ["state"] }' data-query-params="consignment_query_params" id="consignment_table">
                <thead>
                    <tr>
                        <th data-field="id" data-sortable='true' data-footer-formatter="totalFormatter">ID</th>
                        <th data-field="order_id" data-sortable='true'>Order ID</th>
                        <th data-field="name" data-sortable='true'>Name</th>
                        <th data-field="status" data-sortable='true'>Status</th>
                        <th data-field="otp" data-sortable='true'>OTP</th>
                        <th data-field="created_date" data-sortable='true'>Created Date</th>
                        <th data-field="operate" data-sortable="false">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
</div>
<div class="modal fade" id="view_consignment_items_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header mb-1">
                <h5 class="modal-title" id="myModalLabel">Consignment Items</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Consignment Id</th>
                            <th scope="col">Order Iitem Id</th>
                            <th scope="col">Product Variant Id</th>
                            <th scope="col">Unit Price</th>
                            <th scope="col">Quantity</th>
                        </tr>
                    </thead>
                    <tbody id="consignment_details">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="consignment_status_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header mb-1">
                <h5 class="modal-title" id="myModalLabel">Consignment Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-group flex-nowrap">
                    <span class="input-group-text bg-gradient-light">Consignment Id</span>
                    <input type="text" class="form-control" aria-label="Username" aria-describedby="addon-wrapping" id="consignment_id" disabled>
                </div>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text bg-gradient-light">Consignment Name</span>
                    <input type="text" class="form-control" aria-label="Username" aria-describedby="addon-wrapping" id="consignment_name" disabled>
                </div>
                <hr/>
                <div class="input-group flex-nowrap my-2">
                    <span class="input-group-text bg-gradient-light">Consignment Status</span>
                    <select name="status" class="form-control consignment_status">
                        <option value="received">received</option>
                        <option value="processed">processed</option>
                        <option value="shipped">shipped</option>
                        <option value="delivered">delivered</option>
                    </select>
                </div>
                <div class="d-flex justify-content-end px-2">
                    <button type="button" class="btn btn-primary" id="update_status">Update Status</button>
                </div>
            </div>
        </div>
    </div>
</div>
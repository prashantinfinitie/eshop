<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Manage Seller</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Seller</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 main-content">
                    <div class="card content-area p-4">

                        <div class="card-innr">
                            <div class="align-items-center d-flex justify-content-between mb-2">
                                <div class="align-items-center d-flex">
                                    <div class="">
                                        <div class="">
                                            <a href="#" class="btn btn-success update-seller-commission" title="If you found seller commission not crediting using cron job you can update seller commission from here!">Update Seller Commission</a>
                                        </div>
                                    </div>
                                    <div class="mx-4 w-75">
                                        <label for="seller_status_filter" class="col-form-label p-0">Filter By Seller Status</label>
                                        <select id="seller_status_filter" name="seller_status_filter" placeholder="Select Status" required="" class="form-control">
                                            <option value="">All</option>
                                            <option value="approved">Approved</option>
                                            <option value="not_approved">Not Approved</option>
                                            <option value="deactive">Deactive</option>
                                            <option value="removed">Removed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <a href="<?= base_url() . 'admin/sellers/manage-seller' ?>" class="btn btn-block  btn-outline-primary btn-sm">Add Seller </a>
                                </div>
                            </div>
                            <p>
                                <lable class="badge badge-info mt-4 text-sm">Note : If you found seller commission not crediting using cron job you can update seller commission manually from here! ( click on update seller commission button )</lable>
                            </p>
                            <div class="gaps-1-5x"></div>
                            <table class='table-striped' id='seller_table' data-toggle="table" data-url="<?= base_url('admin/sellers/view_sellers') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="sd.id" data-sort-order="DESC" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-export-types='["txt","excel"]' data-query-params="seller_status_params">
                                <thead>
                                    <tr>
                                        <th data-field="id" data-sortable="true">ID</th>
                                        <th data-field="name" data-sortable="false">Name</th>
                                        <th data-field="email" data-sortable="false">Email</th>
                                        <th data-field="mobile" data-sortable="true">Mobile No</th>
                                        <th data-field="address" data-sortable="true" data-visible="false">Address</th>
                                        <th data-field="balance" data-sortable="true">Balance</th>
                                        <th data-field="rating" data-sortable="true">Rating</th>
                                        <th data-field="store_name" data-sortable="true">Store Name</th>
                                        <th data-field="store_url" data-sortable="true" data-visible="false">Store URL</th>
                                        <th data-field="store_description" data-sortable="true" data-visible="false">Store Description</th>
                                        <th data-field="latitude" data-sortable="true" data-visible="false">Latitude</th>
                                        <th data-field="longitude" data-sortable="true" data-visible="false">Longitude</th>
                                        <th data-field="status" data-sortable="false">Status</th>
                                        <th data-field="category_ids" data-sortable="true" data-visible="false">Categories</th>
                                        <th data-field="logo" data-sortable="false">Logo</th>
                                        <th data-field="address_proof" data-sortable="true" data-visible="false">Address Proof</th>
                                        <th data-field="permissions" data-sortable="true" data-visible="false">Permissions</th>
                                        <th data-field="date" data-sortable="true" data-visible="false">Date</th>
                                        <th data-field="operate" data-sortable="false">Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div><!-- .card-innr -->
                    </div><!-- .card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
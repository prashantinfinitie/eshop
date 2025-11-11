<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>View Affiliate Users</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
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
                                <div class="align-items-center col-md-10 d-flex">
                                    <div class="">
                                        <a href="#" class="btn btn-success update-affiliate-commission" title="If you found affiliate commission not crediting using cron job you can update Affiliate commission from here!">Update Affiliate Commission</a>
                                    </div>
                                    <div class="mx-4 w-25">
                                        <label for="affiliate_status_filter" class="col-form-label p-0">Filter By Affiliate Status</label>
                                        <select id="affiliate_status_filter" name="affiliate_status_filter" placeholder="Select Status" required="" class="form-control">
                                            <option value="">All</option>
                                            <option value="approved">Approved</option>
                                            <option value="not_approved">Not Approved</option>
                                            <option value="deactive">Deactive</option>
                                            <option value="removed">Removed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <a href="<?= base_url() . 'admin/affiliate_users/manage_user' ?>" class="btn btn-block btn-outline-primary btn-sm add-user-tab">Add User</a>

                                </div>
                            </div>

                            <div class="gaps-1-5x"></div>
                            <table class='table-striped' id="affiliate-users-table" data-toggle="table" data-url="<?= base_url('admin/affiliate_users/get_users') ?>"
                                data-side-pagination="server" data-click-to-select="true" data-pagination="true" data-id-field="id" data-page-list="[5, 10, 20, 50, 100, 200]"
                                data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc"
                                data-mobile-responsive="true" data-toolbar="#toolbar" data-show-export="true" data-maintain-selected="true" data-export-types='["txt","excel"]'
                                data-query-params="affiliate_status_params">
                                <thead>
                                    <tr>
                                        <th data-field="id" data-sortable="true">ID</th>
                                        <th data-field="name" data-sortable="false">Name</th>
                                        <th data-field="email" data-sortable="true">Email</th>
                                        <th data-field="mobile" data-sortable="true">Mobile No</th>
                                        <th data-field="balance" data-sortable="true">Balance</th>
                                        <th data-field="date" data-sortable="false">Date</th>
                                        <th data-field="status" data-sortable="true">Status</th>
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
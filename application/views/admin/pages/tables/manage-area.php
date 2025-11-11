<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-md-6">
                    <h4>Manage Area</h4>
                </div>
                <div class="col-md-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Area</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="modal fade edit-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Edit Area</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 main-content">
                    <div class="card content-area p-4">
                        <div class="card-head">
                            <h4 class="card-title">Area Details</h4>
                        </div>
                        <div class="card-innr">
                            <div class="gaps-1-5x"></div>
                            <table class='table-striped' data-toggle="table" data-url="<?= base_url('admin/area/view_area') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-export-types='["txt","excel"]' data-query-params="queryParams">
                                <thead>
                                    <tr>
                                        <th data-field="id" data-sortable="true">ID</th>
                                        <th data-field="name" data-sortable="false">Name</th>
                                        <th data-field="city_name" data-sortable="false">City Name</th>
                                        <th data-field="zipcode" data-sortable="false">Zipcode</th>
                                        <th data-field="minimum_free_delivery_order_amount" data-sortable="false">Minimum Free Delivery Order Amount</th>
                                        <th data-field="delivery_charges" data-sortable="false">Delivery Charges</th>
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
<div class="modal fade" id="bulk_update">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Bulk Update</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <form method="post" action='<?= base_url('admin/area/bulk_update') ?>' id="bulk_area_update_form">
                    <div class="form-group">
                        <input type="hidden" name=" <?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                    </div>
                    <div class="col-md-12">
                        <select class="form-control mb-2" name="city">
                            <option value=" ">-------------------Select City------------------</option>
                            <?php foreach ($city as $row) { ?>
                                <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group row">
                        <label for="bulk_update_minimum_free_delivery_order_amount" class="control-label col-md-12">Minimum Free Delivery Order Amount <span class='text-danger text-xs'>*</span></label>
                        <div class="col-md-12">
                            <input type="number" class="form-control" name="bulk_update_minimum_free_delivery_order_amount" id="bulk_update_minimum_free_delivery_order_amount" min="0">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="bulk_update_delivery_charges" class="control-label col-md-12">Delivery Charges <span class='text-danger text-xs'>*</span></label>
                        <div class="col-md-12">
                            <input type="number" class="form-control" name="bulk_update_delivery_charges" id="bulk_update_delivery_charges" min="0">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="save-areas-result-btn" name="bulk_update" value="Save">Update</button>
                    <div class="mt-3">
                        <div id="save-areas-result"></div>
                    </div>
                </form>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

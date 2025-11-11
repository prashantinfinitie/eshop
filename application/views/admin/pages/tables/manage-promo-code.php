<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4> Manage Promo Code</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Manage Promo Code</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="modal fade edit-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Manage Promo Code</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 main-content">
                    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id='add_promocode'>
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Promo Code</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body p-0">
                                    <form class="form-horizontal form-submit-event add_promocode_form" id="add_promocode_form" method="POST">


                                        <div class="card-body">

                                            <input type="hidden" name="edit_promo_code" id="edit_promo_code" value="">

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="">Promo Code <span class='text-danger text-sm'>*</span></label>
                                                    <input type="text" class="form-control" name="promo_code" id="promo_code" value="">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="">Message <span class='text-danger text-sm'>*</span></label>
                                                    <input type="text" class="form-control" name="message" id="message" value="">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="">Start Date <span class='text-danger text-sm'>*</span></label>
                                                    <input type="date" class="form-control" name="start_date" id="start_date" min="<?= date('Y-m-d') ?>" value="">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="">End Date <span class='text-danger text-sm'>*</span></label>
                                                    <input type="date" class="form-control" name="end_date" id="end_date" min="<?= date('Y-m-d') ?>" value="">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="">No. Of Users <span class='text-danger text-sm'>*</span></label>
                                                    <input type="number" min="0" class="form-control" name="no_of_users" id="no_of_users" value="">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="">Minimum Order Amount <span class='text-danger text-sm'>*</span></label>
                                                    <input type="number" min="1" class="form-control" name="minimum_order_amount" id="minimum_order_amount" value="">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="">Discount <span class='text-danger text-sm'>*</span></label>
                                                    <input type="number" min="1" class="form-control discount" name="discount" id="discount" value="">
                                                    <div class="error"></div>
                                                </div>


                                                <div class="form-group col-md-6">
                                                    <label for="">Discount Type <span class='text-danger text-sm'>*</span></label>
                                                    <select name="discount_type" id="discount_type_select" class="form-control discount_type">
                                                        <option value="">Select</option>
                                                        <option value="percentage">Percentage</option>
                                                        <option value="amount">Amount</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6 <?= (isset($fetched_details[0]['discount_type']) && $fetched_details[0]['discount_type'] == 'percentage') ? '' : 'd-none' ?>" id="max_discount_amount_html">
                                                    <label for="">Max Discount Amount <span class='text-danger text-sm'>*</span></label>
                                                    <input type="number" min="1" class="form-control" name="max_discount_amount" id="max_discount_amount" value="">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="">Repeat Usage <span class='text-danger text-sm'>*</span></label>
                                                    <select name="repeat_usage" id="repeat_usage" class="form-control">
                                                        <option value="">Select</option>
                                                        <option value="1">Allowed</option>
                                                        <option value="0">Not Allowed</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="image">Main Image <span class='text-danger text-sm'>*</span><small>(Recommended Size : 80 x 80 pixels)</small></label>
                                                    <div class="col-sm-10">
                                                        <div class='col-md-5'><a class="uploadFile img btn btn-primary text-white btn-sm" data-input='image' data-isremovable='0' data-is-multiple-uploads-allowed='0' data-toggle="modal" data-target="#media-upload-modal" value="Upload Photo"><i class='fa fa-upload'></i> Upload</a></div>


                                                        <label class="text-danger mt-3 edit_promo_upload_image_note">*Only Choose When Update is necessary</label>

                                                        <div class="container-fluid row image-upload-section">
                                                            <div class="col-sm-6 shadow bg-white rounded text-center grow image">
                                                                <div class=''>
                                                                    <img class="img-fluid mb-2" id="slider_uploaded_image"
                                                                        src="<?= base_url() . NO_IMAGE ?>" alt="Image Not Found">
                                                                    <input type="hidden" name="image"
                                                                        id="uploaded_slider_uploaded_image"
                                                                        class="uploaded_image_here form-control form-input">
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="">Status <span class='text-danger text-sm'>*</span></label>
                                                    <select name="status" id="status" class="form-control">
                                                        <option value="">Select</option>
                                                        <option value="1">Active</option>
                                                        <option value="0">Deactive</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6 d-none" id="repeat_usage_html">
                                                    <label for=""> No of repeat usage <span class='text-danger text-sm'>*</span> </label>
                                                    <input type="number" class="form-control" min='0' name="no_of_repeat_usage" id="no_of_repeat_usage" value="">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="is_cashback"> Is Cashback? </label>
                                                    <div class="card-body p-0">
                                                        <input type="checkbox" name="is_cashback" id="is_cashback" data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="is_cashback"> List Promocode? </label>
                                                    <div class="card-body">
                                                        <input type="checkbox" name="list_promocode" id="list_promocode" data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button type="reset" class="btn btn-warning reset_promo_code">Reset</button>
                                                <button type="submit" class="btn btn-success save_promocode" id="submit_btn"><?= (isset($fetched_details[0]['id'])) ? 'Update Promo Code' : 'Add Promo Code' ?></button>
                                            </div>
                                        </div>

                                        <!-- /.card-footer -->
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card content-area p-4">
                        <div class="card-header border-0">
                            <div class="card-tools">
                                <button type="button" class="btn btn-block  btn-outline-primary btn-sm add_promocode_btn" data-toggle="modal" data-target="#add_promocode">
                                    Add Promo Code
                                </button>
                            </div>
                        </div>
                        <div class="card-innr">
                            <div class="gaps-1-5x"></div>
                            <table class='table-striped' data-toggle="table" data-url="<?= base_url('admin/promo_code/view_promo_code') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-export-types='["txt","excel"]' data-export-options='{
                            "fileName": "promocode-list",
                            "ignoreColumn": ["state"]
                            }' data-query-params="queryParams">
                                <thead>
                                    <tr>
                                        <th data-field="id" data-sortable="true" data-align='center'>ID</th>
                                        <th data-field="promo_code" data-sortable="false" data-align='center'>Promo Code</th>
                                        <th data-field="image" data-sortable="false" data-align='center'>Image</th>
                                        <th data-field="message" data-sortable="true" data-align='center'>Message</th>
                                        <th data-field="start_date" data-sortable="true" data-align='center'>Start Date</th>
                                        <th data-field="end_date" data-sortable="true" data-align='center'>End Date</th>
                                        <th data-field="no_of_users" data-sortable="true" data-visible='false' data-align='center'>No .of users</th>
                                        <th data-field="min_order_amt" data-sortable="true" data-visible='false' data-align='center'>Minimum order amount</th>
                                        <th data-field="discount" data-sortable="true" data-align='center'>Discount</th>
                                        <th data-field="discount_type" data-sortable="true" data-align='center'>Discount type</th>
                                        <th data-field="max_discount_amt" data-sortable="true" data-visible='false' data-align='center'>Max discount amount</th>
                                        <th data-field="repeat_usage" data-sortable="true" data-visible='false' data-align='center'>Repeat usage</th>
                                        <th data-field="no_of_repeat_usage" data-sortable="true" data-visible='false' data-align='center'>No of repeat usage</th>
                                        <th data-field="status" data-sortable="true" data-align='center'>Status</th>
                                        <th data-field="is_cashback" data-sortable="true" data-align='center'>Is Cashback</th>
                                        <th data-field="list_promocode" data-sortable="true" data-align='center'>View Promocode</th>
                                        <th data-field="operate" data-sortable="false" data-align='center'>Actions</th>
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
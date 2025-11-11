<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4> Manage Return Reasons</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Manage Return Reasons</li>
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
                                <h5 class="modal-title">Manage Return Reasons</h5>
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
                    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id='add_return_reason'>
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Return Reasons</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body p-0">
                                    <form class="form-horizontal form-submit-event add_return_reason_form" id="add_return_reason_form" method="POST">


                                        <div class="card-body">

                                            <input type="hidden" name="edit_return_reason_id" id="edit_return_reason_id" value="">

                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="">Title <span class='text-danger text-sm'>*</span></label>
                                                    <input type="text" class="form-control" name="return_reason" id="return_reason" value="">
                                                </div>

                                                <div class="form-group col-md-12 d-none">
                                                    <label for="">Message <span class='text-danger text-sm'>*</span></label>
                                                    <input type="text" class="form-control" name="message" id="message" value="">
                                                </div>

                                                <div class="form-group col-md-12">
                                                    <label for="image">Main Image <span class='text-danger text-sm'>*</span><small>(Recommended Size : 80 x 80 pixels)</small></label>
                                                    <div class="col-sm-10">
                                                        <div class='col-md-5'><a class="uploadFile img btn btn-primary text-white btn-sm" data-input='image' data-isremovable='0' data-is-multiple-uploads-allowed='0' data-toggle="modal" data-target="#media-upload-modal" value="Upload Photo"><i class='fa fa-upload'></i> Upload</a></div>


                                                        <label class="text-danger mt-3 edit_promo_upload_image_note">*Only Choose When Update is necessary</label>
                                                        <div class="container-fluid image-upload-section">
                                                            <div class="col-md-12 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
                                                                <div class='image-upload-div'>
                                                                    <img id="uploaded_image_here" src="" alt="Uploaded Image" class="uploaded_image_here">
                                                                    <input type="hidden" name="image" id="uploaded_image_here_val" class="uploaded_image_here">
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                            <div class="form-group">
                                                <button type="reset" class="btn btn-warning reset_return_reason">Reset</button>
                                                <button type="submit" class="btn btn-success save_return_reason" id="submit_btn"><?= (isset($fetched_details[0]['id'])) ? 'Update Return Reason' : 'Add Return Reason' ?></button>
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
                                <button type="button" class="btn btn-block  btn-outline-primary btn-sm add_return_reason_btn" data-toggle="modal" data-target="#add_return_reason">
                                    Add Retrun Reasons
                                </button>
                            </div>
                        </div>
                        <div class="card-innr">
                            <div class="gaps-1-5x"></div>
                            <table class='table-striped' data-toggle="table" data-url="<?= base_url('admin/return_reasons/view_return_reason') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-export-types='["txt","excel"]' data-export-options='{
                            "fileName": "promocode-list",
                            "ignoreColumn": ["state"]
                            }' data-query-params="queryParams">
                                <thead>
                                    <tr>
                                        <th data-field="id" data-sortable="true" data-align='center'>ID</th>
                                        <th data-field="return_reason" data-sortable="false" data-align='center'>Return Reason</th>
                                        <th data-field="image" data-sortable="false" data-align='center'>Image</th>
                                        <!-- <th data-field="message" data-sortable="true" data-align='center'>Message</th> -->
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
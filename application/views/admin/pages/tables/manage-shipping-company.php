<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Manage Shipping Companies</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Shipping Companies</li>
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
                                <h5 class="modal-title" id="exampleModalLongTitle">Edit Shipping Company</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id='fund_transfer_shipping_company'>
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Fund Transfer - Shipping Company</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-0">
                                <form class="form-horizontal form-submit-event" action="<?= base_url('admin/fund_transfer/add-fund-transfer'); ?>" method="POST" enctype="multipart/form-data">
                                    <div class="card-body row">
                                        <input type="hidden" name='shipping_company_id' id="shipping_company_id">
                                        <input type="hidden" name='user_type' value="shipping_company">
                                        <div class="form-group col-md-6">
                                            <label for="name" class="col-sm-2 col-form-label">Company Name</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="name" name="name" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="mobile" class="col-sm-2 col-form-label">Mobile</label>
                                            <div class="col-sm-10">
                                                <input type="number" class="form-control" id="mobile" name="mobile" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="balance" class="col-sm-2 col-form-label">Balance</label>
                                            <div class="col-sm-10">
                                                <input type="number" class="form-control" id="balance" name="balance" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="transfer_amt" class="col-sm-6 col-form-label">Transfer Amount</label>
                                            <div class="col-sm-10">
                                                <input type="number" class="form-control" id="transfer_amt" name="transfer_amt">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="message" class="col-sm-2 col-form-label">Message</label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control" id="message" name="message">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <button type="reset" class="btn btn-warning">Reset</button>
                                            <button type="submit" class="btn btn-success" id="submit_btn">Transfer Fund</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 main-content">
                    <div class="card content-area p-4">
                        <div class="align-items-center d-flex justify-content-between">
                            <div class="col-md-3">
                                <label for="shipping_company_status_filter" class="col-form-label">Filter By Status</label>
                                <select id="shipping_company_status_filter" name="shipping_company_status_filter" placeholder="Select Status" required="" class="form-control">
                                    <option value="">All</option>
                                    <option value="approved">Approved</option>
                                    <option value="not_approved">Not Approved</option>
                                </select>
                            </div>
                            <div class="card-tools">
                                <button type="button" class="btn btn-block btn-outline-primary btn-sm" data-toggle="modal" data-target="#add_shipping_company">
                                    Add Shipping Company
                                </button>
                            </div>
                        </div>
                        <div class="card-innr">
                            <div class="row col-md-6">
                            </div>
                            <div class="gaps-1-5x"></div>
                            <table class='table-striped' id='shipping_company_data' data-toggle="table"
                                data-url="<?= base_url('admin/shipping_companies/view_shipping_companies') ?>" data-click-to-select="true"
                                data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false"
                                data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar=""
                                data-show-export="true" data-maintain-selected="true" data-export-types='["txt","excel"]'
                                data-query-params="shipping_company_status_params">
                                <thead>
                                    <tr>
                                        <th data-field="id" data-sortable="true">ID</th>
                                        <th data-field="name" data-sortable="false">Company Name</th>
                                        <th data-field="email" data-sortable="false">Email</th>
                                        <th data-field="mobile" data-sortable="true">Mobile No</th>
                                        <th data-field="address" data-sortable="false">Address</th>
                                        <!-- <th data-field="balance" data-sortable="true">Balance</th> -->
                                        <th data-field="status" data-sortable="true">Status</th>
                                        <th data-field="date" data-sortable="true">Date</th>
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

    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id='add_shipping_company'>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Shipping Company</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <form class="form-horizontal form-submit-event add_shipping_company" method="POST" id="add_shipping_company_form" enctype="multipart/form-data">
                        <?php if (isset($fetched_data[0]['id'])) { ?>
                            <input type="hidden" name="edit_shipping_company" class="edit_shipping_company" value="<?= $fetched_data[0]['id'] ?>">
                        <?php } ?>
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="company_name" class="col-sm-3 col-form-label">Company Name <span class='text-danger text-sm'>*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="company_name" placeholder="Company Name" name="company_name" value="<?= @$fetched_data[0]['username'] ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="mobile" class="col-sm-3 col-form-label">Mobile <span class='text-danger text-sm'>*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" maxlength="16" oninput="validateNumberInput(this)" id="mobile" placeholder="Enter Mobile" name="mobile" value="<?= @$fetched_data[0]['mobile'] ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-sm-3 col-form-label">Email <span class='text-danger text-sm'>*</span></label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="email" placeholder="Enter Email" name="email" value="<?= @$fetched_data[0]['email'] ?>">
                                </div>
                            </div>
                            <?php if (!isset($fetched_data[0]['id'])) { ?>
                                <div class="form-group row ">
                                    <label for="password" class="col-sm-3 col-form-label">Password <span class='text-danger text-sm'>*</span></label>
                                    <div class="input-group col-sm-9">
                                        <input type="password" class="form-control form-input passwordToggle" name="password" id="password" placeholder="Type Password here" value="" required>
                                        <span class="input-group-text togglePassword" style="cursor: pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group row ">
                                    <label for="confirm_password" class="col-sm-3 col-form-label">Confirm Password <span class='text-danger text-sm'>*</span></label>
                                    <div class="input-group col-sm-9">
                                        <input type="password" class="form-control form-input passwordToggle" name="confirm_password" id="confirm_password" placeholder="Type Confirm Password here" value="" required>
                                        <span class="input-group-text togglePassword" style="cursor: pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="form-group row">
                                <label for="address" class="col-sm-3 col-form-label">Address <span class='text-danger text-sm'>*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="address" placeholder="Enter Address" name="address" value="<?= @$fetched_data[0]['address'] ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="assign_zipcode" class="col-form-label col-sm-3">Assign Zipcodes <span class='text-danger text-sm'>*</span></label>
                                <div class="col-sm-9">
                                    <?php
                                    $assigned_zipcodes = (isset($fetched_data[0]['assign_zipcode']) && $fetched_data[0]['assign_zipcode'] != NULL) ? explode(",", $fetched_data[0]['assign_zipcode']) : [];

                                    $zipcodes_data = [];
                                    if (!empty($assigned_zipcodes)) {
                                        $zipcodes_data = fetch_details('zipcodes', ['provider_type' => 'company'], 'zipcode,id', "", "", "", "", "id", $assigned_zipcodes);
                                    }
                                    ?>
                                    <select name="assign_zipcode[]" class="assign_zipcode form-control w-100" multiple onload="multiselect()" id="">
                                        <?php if (!empty($zipcodes_data)) {
                                            foreach ($zipcodes_data as $row) {
                                        ?>
                                                <option value="<?= $row['id'] ?>" <?= (in_array($row['id'], $assigned_zipcodes)) ? 'selected' : ''; ?>><?= $row['zipcode'] ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                    <small class="form-text text-muted">Only zipcodes for shipping companies</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="kyc_documents" class="col-sm-3 col-form-label">KYC Documents <span class='text-danger text-sm'>*</span></label>
                                <div class="col-sm-9">
                                    <?php if (isset($fetched_data[0]['kyc_documents']) && !empty($fetched_data[0]['kyc_documents'])) { ?>
                                        <span class="text-danger">*Leave blank if there is no change</span>
                                    <?php } else { ?>
                                        <span class="text-danger">*Upload KYC documents (Registration certificate, Tax ID, etc.)</span>
                                    <?php } ?>
                                    <input type="file" class="form-control file_upload_height" name="kyc_documents[]" id="kyc_documents" accept="image/*,application/pdf" multiple />
                                </div>
                            </div>

                            <div class="form-group row">
                                <?php
                                if (isset($fetched_data[0]['kyc_documents']) && !empty($fetched_data[0]['kyc_documents'])) {
                                    $documents = explode(",", $fetched_data[0]['kyc_documents']);
                                    foreach ($documents as $doc) {
                                        $extension = pathinfo($doc, PATHINFO_EXTENSION);
                                ?>
                                        <label class="col-sm-3 col-form-label"></label>
                                        <div class="mx-auto col-sm-9 kyc-document">
                                            <?php if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) { ?>
                                                <a href="<?= base_url($doc); ?>" data-toggle="lightbox" data-gallery="gallery_kyc">
                                                    <img src="<?= base_url($doc); ?>" class="img-fluid rounded" style="max-height: 150px;">
                                                </a>
                                            <?php } else { ?>
                                                <a href="<?= base_url($doc); ?>" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fa fa-file"></i> View Document
                                                </a>
                                            <?php } ?>
                                        </div>
                                <?php
                                    }
                                } ?>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Status <span class='text-danger text-sm'>*</span></label>
                                <div id="status" class="btn-group">
                                    <label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                        <input type="radio" name="status" value="1" <?= (isset($fetched_data[0]['status']) && $fetched_data[0]['status'] == '1') ? 'Checked' : '' ?>> Approved
                                    </label>
                                    <label class="btn btn-danger" data-toggle-class="btn-danger" data-toggle-passive-class="btn-default">
                                        <input type="radio" name="status" value="0" <?= (isset($fetched_data[0]['status']) && $fetched_data[0]['status'] == '0') ? 'Checked' : '' ?>> Not-Approved
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="reset" class="btn btn-warning">Reset</button>
                                <button type="submit" class="btn btn-success" id="add_shiping_comapny_submit_btn"><?= (isset($fetched_data[0]['id'])) ? 'Update Shipping Company' : 'Add Shipping Company' ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->
</div>

<!-- <script>
    $(document).ready(function() {
        // Initialize Select2 for modal zipcode selection
        $('#assign_zipcode').select2({
            placeholder: "Select Zipcodes",
            allowClear: true,
            dropdownParent: $('#add_shipping_company'),
            ajax: {
                url: '<?= base_url("admin/shipping_companies/get_company_zipcodes") ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.id,
                                text: item.zipcode
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 0
        });
    });
</script> -->

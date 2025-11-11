<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Manage Delivery Boy</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Delivery Boy</li>
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
                                <h5 class="modal-title" id="exampleModalLongTitle">Edit Delivery boy</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id='fund_transfer_delivery_boy'>
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Fund Transfer Delivery boy</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-0">
                                <form class="form-horizontal form-submit-event" action="<?= base_url('admin/fund_transfer/add-fund-transfer'); ?>" method="POST" enctype="multipart/form-data">
                                    <div class="card-body row">
                                        <input type="hidden" name='delivery_boy_id' id="delivery_boy_id">
                                        <div class="form-group col-md-6">
                                            <label for="name" class="col-sm-2 col-form-label">Name</label>
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

                                    <!-- /.card-body -->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 main-content">
                    <div class="card content-area p-4">
                        <div class="align-items-center d-flex justify-content-between">
                            <div class="col-md-3">
                                <label for="delivery_boy_status_filter" class="col-form-label">Filter By Delivery Boy Status</label>
                                <select id="delivery_boy_status_filter" name="delivery_boy_status_filter" placeholder="Select Status" required="" class="form-control">
                                    <option value="">All</option>
                                    <option value="approved">Approved</option>
                                    <option value="not_approved">Not Approved</option>
                                </select>
                            </div>
                            <div class="card-tools">
                                <button type="button" class="btn btn-block  btn-outline-primary btn-sm" data-toggle="modal" data-target="#add_delivery_boy">
                                    Add Delivery Boy
                                </button>
                            </div>
                        </div>
                        <div class="card-innr">
                            <div class="row col-md-6">
                            </div>
                            <div class="gaps-1-5x"></div>
                            <table class='table-striped' id='delivery_boy_data' data-toggle="table"
                                data-url="<?= base_url('admin/delivery_boys/view_delivery_boys') ?>" data-click-to-select="true"
                                data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false"
                                data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar=""
                                data-show-export="true" data-maintain-selected="true" data-export-types='["txt","excel"]'
                                data-query-params="delivery_boy_status_params">
                                <thead>
                                    <tr>
                                        <th data-field="id" data-sortable="true">ID</th>
                                        <th data-field="name" data-sortable="false">Name</th>
                                        <th data-field="email" data-sortable="false">Email</th>
                                        <th data-field="mobile" data-sortable="true">Mobile No</th>
                                        <th data-field="address" data-sortable="false">Address</th>
                                        <th data-field="bonus_type" data-sortable="false">Bonus Type</th>
                                        <th data-field="bonus" data-sortable="false">Bonus</th>
                                        <th data-field="balance" data-sortable="true">Balance</th>
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

    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id='add_delivery_boy'>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Delivery boy</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <form class="form-horizontal form-submit-event add_delivery_boy" method="POST" id="add_product_form">
                        <?php if (isset($fetched_data[0]['id'])) { ?>
                            <input type="hidden" name="edit_delivery_boy" class="edit_delivery_boy" value="<?= $fetched_data[0]['id'] ?>">
                        <?php
                        } ?>
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="name" class="col-sm-3 col-form-label">Name <span class='text-danger text-sm'>*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name" placeholder="Delivery Boy Name" name="name" value="<?= @$fetched_data[0]['username'] ?>">
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
                            <?php
                            if (!isset($fetched_data[0]['id'])) {
                            ?>
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
                            <?php
                            }
                            ?>
                            <div class="form-group row">
                                <label for="address" class="col-sm-3 col-form-label">Address <span class='text-danger text-sm'>*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="address" placeholder="Enter Address" name="address" value="<?= @$fetched_data[0]['address'] ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <?php
                                $bonus_type = ['fixed_amount_per_order_item', 'percentage_per_order_item'];
                                ?>
                                <label for="bonus_type" class="col-sm-3 control-label">Bonus Types <span class='text-danger text-sm'> * </span></label>
                                <div class="col-sm-9">
                                    <select name="bonus_type" class="form-control bonus_type">
                                        <option value=" ">Select Types</option>
                                        <option value="<?= 'fixed_amount_per_order_item' ?>" <?= (isset($fetched_data[0]['id']) &&  $fetched_data[0]['bonus_type'] == 'fixed_amount_per_order_item') ? "Selected" : "" ?>>Fixed amount per Parcel</option>
                                        <option value="<?= 'percentage_per_order_item' ?>" <?= (isset($fetched_data[0]['id']) &&  $fetched_data[0]['bonus_type'] == 'percentage_per_order_item') ? "Selected" : "" ?>>Percentage per Parcel</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row fixed_amount_per_order <?= (isset($fetched_data[0]['id'])  && $fetched_data[0]['bonus_type'] == 'fixed_amount_per_order_item') ? '' : 'd-none' ?>">
                                <label for="bonus" class="col-sm-3 col-form-label">Amount <span class='text-danger text-sm'>*</span></label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="bonus_amount" placeholder="Enter amount to be given to the delivery boy on successful parcel delivery" name="bonus_amount" value="<?= @$fetched_data[0]['bonus'] ?>">
                                </div>
                            </div>
                            <div class="form-group row percentage_per_order <?= (isset($fetched_data[0]['id'])  && $fetched_data[0]['bonus_type'] == 'percentage_per_order_item') ? '' : 'd-none' ?>">
                                <label for="bonus" class="col-sm-3 col-form-label">Bonus(%) <span class='text-danger text-sm'>*</span></label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="bonus_percentage" placeholder="Enter Bonus(%) to be given to the delivery boy on successful parcel delivery" name="bonus_percentage" value="<?= @$fetched_data[0]['bonus'] ?>">
                                </div>
                            </div>
                            <?php
                            $pincode_wise_deliverability = (isset($shipping_method['pincode_wise_deliverability']) && $shipping_method['pincode_wise_deliverability'] == 1) ? $shipping_method['pincode_wise_deliverability'] : '0';
                            $city_wise_deliverability = (isset($shipping_method['city_wise_deliverability']) && $shipping_method['city_wise_deliverability'] == 1) ? $shipping_method['city_wise_deliverability'] : '0';
                            ?>

                            <input type="hidden" name="city_wise_deliverability" value="<?= $city_wise_deliverability ?>">
                            <input type="hidden" name="pincode_wise_deliverability" value="<?= $pincode_wise_deliverability ?>">
                            <div class="form-group row">
                                <?php if ((isset($shipping_method['pincode_wise_deliverability']) && $shipping_method['pincode_wise_deliverability'] == 1) || (isset($shipping_method['local_shipping_method']) && isset($shipping_method['shiprocket_shipping_method']) && $shipping_method['local_shipping_method'] == 1 && $shipping_method['shiprocket_shipping_method'] == 1)) { ?>
                                    <label for="serviceable_zipcodes" class="col-form-label col-sm-3">Serviceable Zipcodes <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-9">
                                        <?php
                                        $zipcodes = (isset($fetched_data[0]['serviceable_zipcodes']) &&  $fetched_data[0]['serviceable_zipcodes'] != NULL) ? explode(",", $fetched_data[0]['serviceable_zipcodes']) : [];
                                        $zipcodes_name = fetch_details('zipcodes', "", 'zipcode,id', "", "", "", "", "id", $zipcodes);

                                        ?>
                                        <select name="serviceable_zipcodes[]" class="search_zipcode form-control w-100" multiple onload="multiselect()" id="deliverable_zipcodes">
                                            <?php if (isset($zipcodes) && !empty($zipcodes)) {
                                                foreach ($zipcodes_name as $row) {
                                            ?>
                                                    <option value="<?= $row['id'] ?>" <?= (!empty($zipcodes) && in_array($row['id'], $zipcodes)) ? 'selected' : ''; ?>><?= $row['zipcode'] ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>

                                <?php  }
                                if (isset($shipping_method['city_wise_deliverability']) && $shipping_method['city_wise_deliverability'] == 1 && $shipping_method['shiprocket_shipping_method'] != 1) { ?>
                                    <label for="cities" class="col-form-label col-sm-3">Serviceable Cities <span class='text-danger text-sm'>*</span></label>
                                    <?php
                                    $selected_city_ids = (isset($fetched_data[0]['serviceable_cities']) &&  $fetched_data[0]['serviceable_cities'] != NULL) ? explode(",", $fetched_data[0]['serviceable_cities']) : [];
                                    ?>
                                    <div class="col-sm-9">

                                        <select class="form-control city_list " name="serviceable_cities[]" id="deliverable_cities" multiple>
                                            <?php foreach ($cities as $row) { ?>
                                                <option value="<?= $row['id'] ?>" <?= (in_array($row['id'], $selected_city_ids)) ? 'selected' : ''; ?>><?= $row['name'] ?></option>
                                            <?php }; ?>
                                        </select>
                                    </div>
                                <?php } ?>

                            </div>
                            <div class="form-group row">
                                <label for="driving_license" class="col-sm-3 col-form-label">Driving License <span class='text-danger text-sm'>*</span></label>
                                <div class="col-sm-9">
                                    <?php if (isset($fetched_data[0]['driving_license']) && !empty($fetched_data[0]['driving_license'])) { ?>
                                        <span class="text-danger">*Leave blank if there is no change</span>
                                    <?php } else { ?>
                                        <span class="text-danger">*Add Driving License's front and back image(select multiple)</span>
                                    <?php } ?>
                                    <input type="file" class="form-control file_upload_height" name="driving_license[]" id="driving_license" accept="image/*" multiple />
                                </div>
                            </div>
                            <div class="form-group row">
                                <?php
                                if (isset($fetched_data[0]['driving_license']) && !empty($fetched_data[0]['driving_license'])) {
                                    $images = explode(",", $fetched_data[0]['driving_license']);

                                    foreach ($images as $row) { ?>
                                        <label class="col-sm-3 col-form-label"></label>
                                        <div class="mx-auto col-sm-9 driving-license-image">
                                            <a href="<?= base_url($row); ?>" data-toggle="lightbox" data-gallery="gallery_seller">
                                                <img src="<?= base_url($row); ?>" class="img-fluid rounded">
                                            </a>
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
                                <button type="submit" class="btn btn-success" id="submit_btn"><?= (isset($fetched_data[0]['id'])) ? 'Update Delivery Boy' : 'Add Delivery Boy' ?></button>
                            </div>
                        </div>

                        <!-- /.card-footer -->
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->
</div>
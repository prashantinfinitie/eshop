<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4><?= isset($fetched_data[0]['id']) ? 'Edit Shipping Company' : 'Add Shipping Company' ?></h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Shipping Company</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <!-- form start -->
                        <form class="form-horizontal form-submit-event add_shipping_company" action="<?= base_url('admin/shipping_companies/add_shipping_company'); ?>" method="POST" id="add_shipping_company_form" enctype="multipart/form-data">
                            <?php if (isset($fetched_data[0]['id'])) { ?>
                                <input type="hidden" name="edit_shipping_company" class="edit_shipping_company" value="<?= $fetched_data[0]['id'] ?>">
                            <?php } ?>

                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="company_name" class="col-sm-2 col-form-label">Company Name <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="company_name" placeholder="Company Name" name="company_name" value="<?= @$fetched_data[0]['username'] ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="mobile" class="col-sm-2 col-form-label">Mobile <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" maxlength="16" oninput="validateNumberInput(this)" id="mobile" placeholder="Enter Mobile" name="mobile" value="<?= @$fetched_data[0]['mobile'] ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="email" class="col-sm-2 col-form-label">Email <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" id="email" placeholder="Enter Email" name="email" value="<?= @$fetched_data[0]['email'] ?>">
                                    </div>
                                </div>

                                <?php if (!isset($fetched_data[0]['id'])) { ?>
                                    <div class="form-group row">
                                        <label for="password" class="col-sm-2 col-form-label">Password <span class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="password" placeholder="Enter Password" name="password">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="confirm_password" class="col-sm-2 col-form-label">Confirm Password <span class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="confirm_password" placeholder="Enter Confirm Password" name="confirm_password">
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="form-group row">
                                    <label for="address" class="col-sm-2 col-form-label">Address <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="address" placeholder="Enter Address" name="address" value="<?= @$fetched_data[0]['address'] ?>">
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
                                        <label for="serviceable_zipcodes" class="col-form-label col-sm-2">Serviceable Zipcodes <span class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <?php
                                            $zipcodes = (isset($fetched_data[0]['serviceable_zipcodes']) &&  $fetched_data[0]['serviceable_zipcodes'] != NULL) ? explode(",", $fetched_data[0]['serviceable_zipcodes']) : [];
                                            $zipcodes_name = fetch_details('zipcodes', "", 'zipcode,id', "", "", "", "", "id", $zipcodes);
                                            ?>
                                            <select name="serviceable_zipcodes[]" class="search_zipcode form-control w-100" multiple onload="multiselect()" id="serviceable_zipcodes">
                                                <?php if (isset($zipcodes) && !empty($zipcodes)) {
                                                    foreach ($zipcodes_name as $row) {
                                                ?>
                                                        <option value="<?= $row['id'] ?>" <?= (!empty($zipcodes) && in_array($row['id'], $zipcodes)) ? 'selected' : ''; ?>><?= $row['zipcode'] ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </div>
                                    <?php } ?>

                                    <?php if (isset($shipping_method['city_wise_deliverability']) && $shipping_method['city_wise_deliverability'] == 1 && $shipping_method['shiprocket_shipping_method'] != 1) { ?>
                                        <label for="cities" class="col-form-label col-sm-2">Serviceable Cities <span class='text-danger text-sm'>*</span></label>
                                        <?php
                                        $selected_city_ids = (isset($fetched_data[0]['serviceable_cities']) &&  $fetched_data[0]['serviceable_cities'] != NULL) ? explode(",", $fetched_data[0]['serviceable_cities']) : [];
                                        ?>
                                        <div class="col-sm-10">
                                            <select class="form-control city_list" name="serviceable_cities[]" id="serviceable_cities" multiple>
                                                <?php foreach ($cities as $row) { ?>
                                                    <option value="<?= $row['id'] ?>" <?= (in_array($row['id'], $selected_city_ids)) ? 'selected' : ''; ?>><?= $row['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="form-group row">
                                    <label for="kyc_documents" class="col-sm-2 col-form-label">KYC Documents <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
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
                                            <label class="col-sm-2 col-form-label"></label>
                                            <div class="mx-auto col-sm-10 kyc-document">
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
                                    <label class="col-sm-2 col-form-label">Status <span class='text-danger text-sm'>*</span></label>
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
    </section>
</div>

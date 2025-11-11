<div class="">
    <!-- Content Header (Page header) -->
    <!-- Main content -->

    <section class="content form-box">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card_seller card-info m-0 form-card">
                        <form class="form-horizontal" method="POST" id="add_seller_form">
                            <?php if (isset($user_data) && !empty($user_data)) { ?>
                                <input type="hidden" name="user_id" value="<?= $user_data['to_be_seller_id'] ?>">
                                <input type='hidden' name='user_name' value='<?= $user_data['to_be_seller_name'] ?>'>
                                <input type='hidden' name='user_mobile' value='<?= $user_data['to_be_seller_mobile'] ?>'>
                                <?php
                            } ?>
                            <div class="card-body">
                                <div class="login-logo">
                                    <a href="<?= base_url() . 'seller/login' ?>"><img
                                            src="<?= base_url() . $logo ?>"></a>
                                </div>
                                <h4 class="mb-4">Seller Registration</h4>
                                <h5>Personal Details</h5>
                                <hr>
                                <div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label">Name <span
                                            class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="name" placeholder="Seller Name"
                                            name="name" <?= (isset($user_data) && !empty($user_data) && !empty($user_data['to_be_seller_id'])) ? 'disabled' : ''; ?>
                                            value="<?= @$user_data['to_be_seller_name'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mobile" class="col-sm-2 col-form-label">Mobile <span
                                            class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="seller_mobile"
                                            placeholder="Enter Mobile" name="mobile" <?= (isset($user_data) && !empty($user_data) && !empty($user_data['to_be_seller_id'])) ? 'disabled' : ''; ?> value="<?= @$user_data['to_be_seller_mobile'] ?>">
                                    </div>
                                </div>

                                <?php
                                if (!isset($user_data) && empty($user_data)) {
                                    ?>
                                    <div class="form-group row">
                                        <label for="email" class="col-sm-2 col-form-label">Email <span
                                                class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <input type="email" class="form-control" id="email" placeholder="Enter Email"
                                                name="email">
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label for="password" class="col-sm-2 col-form-label">Password <span
                                                class='text-danger text-sm'>*</span></label>
                                        <div class="input-group col-sm-10">
                                            <input type="password" class="form-control form-input passwordToggle"
                                                name="password" id="password" placeholder="Type Password here" value=""
                                                required>
                                            <span class="input-group-text togglePassword" style="cursor: pointer;">
                                                <i class="fa fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label for="confirm_password" class="col-sm-2 col-form-label">Confirm Password <span
                                                class='text-danger text-sm'>*</span></label>
                                        <div class="input-group col-sm-10">
                                            <input type="password" class="form-control form-input passwordToggle"
                                                name="confirm_password" id="confirm_password"
                                                placeholder="Type Confirm Password here" value="" required>
                                            <span class="input-group-text togglePassword" style="cursor: pointer;">
                                                <i class="fa fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="address" class="col-sm-2 col-form-label">Address <span
                                                class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <textarea type="text" class="form-control" id="address"
                                                placeholder="Enter Address" name="address"></textarea>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="form-group row">
                                    <label for="authorized_signature" class="col-sm-2 col-form-label">Authorized
                                        Signature <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="file" class="form-control" name="authorized_signature"
                                            id="authorized_signature" accept="image/*" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="categories" class="col-sm-2 col-form-label">Categories</label>
                                    <div class="col-sm-10">
                                        <select multiple="multiple" name="categories"
                                            id="seller-register-category-field">
                                            <?php
                                            foreach ($categories as $category) { ?>
                                                <option value="<?= $category["id"] ?>">
                                                    <?= ($category["parent_id"] == "0") ? $category["name"] : "-- " . $category["name"] ?>
                                                </option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row deliverable_type">
                                    <?php if ((isset($shipping_method['pincode_wise_deliverability']) && $shipping_method['pincode_wise_deliverability'] == 1) || (isset($shipping_method['local_shipping_method']) && isset($shipping_method['shiprocket_shipping_method']) && $shipping_method['local_shipping_method'] == 1 && $shipping_method['shiprocket_shipping_method'] == 1)) { ?>

                                        <label for="deliverable_type" class="col-form-label col-md-2">Deliverable Zipcode
                                            Type</label>
                                        <div class="form-group col-sm-9">
                                            <select class="form-control" name="deliverable_zipcode_type"
                                                id="deliverable_zipcode_type">
                                                <option value="<?= ALL ?>" selected>All</option>
                                                <option value="<?= EXCLUDED ?>">Excluded</option>
                                            </select>
                                        </div>

                                    <?php }
                                    if (isset($shipping_method['city_wise_deliverability']) && $shipping_method['city_wise_deliverability'] == 1 && $shipping_method['shiprocket_shipping_method'] != 1) { ?>
                                        <label for="" class="col-form-label col-md-2">Deliverable City Type</label>
                                        <div class="form-group col-md-9">
                                            <select class="form-control" name="deliverable_city_type"
                                                id="deliverable_city_type">
                                                <option value="<?= ALL ?>" selected>All</option>
                                                <option value="<?= EXCLUDED ?>">Excluded</option>
                                            </select>
                                        </div>
                                    <?php } ?>
                                </div>
                                <?php
                                $pincode_wise_deliverability = (isset($shipping_method['pincode_wise_deliverability']) && $shipping_method['pincode_wise_deliverability'] == 1) ? $shipping_method['pincode_wise_deliverability'] : '0';
                                $city_wise_deliverability = (isset($shipping_method['city_wise_deliverability']) && $shipping_method['city_wise_deliverability'] == 1) ? $shipping_method['city_wise_deliverability'] : '0';
                                ?>

                                <input type="hidden" name="city_wise_deliverability"
                                    value="<?= $city_wise_deliverability ?>">
                                <input type="hidden" name="pincode_wise_deliverability"
                                    value="<?= $pincode_wise_deliverability ?>">
                                <div class="form-group row">
                                    <?php if ((isset($shipping_method['pincode_wise_deliverability']) && $shipping_method['pincode_wise_deliverability'] == 1) || (isset($shipping_method['local_shipping_method']) && isset($shipping_method['shiprocket_shipping_method']) && $shipping_method['local_shipping_method'] == 1 && $shipping_method['shiprocket_shipping_method'] == 1)) { ?>
                                        <label for="serviceable_zipcodes" class="col-form-label col-md-2">Serviceable
                                            Zipcodes <span class='text-danger text-sm'>*</span></label>
                                        <div class="col-md-10">
                                            <?php
                                            $zipcodes = (isset($fetched_data[0]['serviceable_zipcodes']) && $fetched_data[0]['serviceable_zipcodes'] != NULL) ? explode(",", $fetched_data[0]['serviceable_zipcodes']) : [];
                                            $zipcodes_name = fetch_details('zipcodes', "", 'zipcode,id', "", "", "", "", "id", $zipcodes);

                                            ?>
                                            <select name="serviceable_zipcodes[]" class="search_zipcode form-control w-100"
                                                multiple onload="multiselect()" id="deliverable_zipcodes">
                                                <?php if (isset($zipcodes) && !empty($zipcodes)) {
                                                    foreach ($zipcodes_name as $row) {
                                                        ?>
                                                        <option value="<?= $row['id'] ?>"><?= $row['zipcode'] ?></option>
                                                    <?php }
                                                } ?>
                                            </select>
                                        </div>

                                    <?php }
                                    if (isset($shipping_method['city_wise_deliverability']) && $shipping_method['city_wise_deliverability'] == 1 && $shipping_method['shiprocket_shipping_method'] != 1) { ?>
                                        <label for="cities" class="col-form-label col-md-2">Serviceable Cities <span
                                                class='text-danger text-sm'>*</span></label>
                                        <?php
                                        $selected_city_ids = (isset($fetched_data[0]['serviceable_cities']) && $fetched_data[0]['serviceable_cities'] != NULL) ? explode(",", $fetched_data[0]['serviceable_cities']) : [];

                                        ?>
                                        <div class="col-md-10">

                                            <select class="form-control city_list w-100" name="serviceable_cities[]"
                                                id="deliverable_cities" multiple>
                                                <?php foreach ($cities as $row) { ?>
                                                    <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                                <?php }
                                                ; ?>
                                            </select>
                                        </div>
                                    <?php } ?>

                                </div>

                                <h5>Store Details</h5>
                                <hr>
                                <div class="form-group row">
                                    <label for="store_name" class="col-sm-2 col-form-label">Name <span
                                            class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="store_name" placeholder="Store Name"
                                            name="store_name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="logo" class="col-sm-2 col-form-label">Logo <span
                                            class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="file" class="form-control file_upload_height" name="store_logo" id="store_logo"
                                            accept="image/*" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="store_url" class="col-sm-2 col-form-label">URL </label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="store_url" placeholder="Store URL"
                                            name="store_url">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="store_description" class="col-sm-3 col-form-label">Description </label>
                                    <div class="col-sm-12">
                                        <textarea type="text" class="form-control" id="store_description"
                                            placeholder="Store Description" name="store_description"></textarea>
                                    </div>
                                </div>

                                <h5>Store Tax Details</h5>
                                <hr>
                                <div class="form-group row">
                                    <label for="tax_name" class="col-sm-2 col-form-label">Tax Name <span
                                            class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="tax_name" placeholder="GST"
                                            name="tax_name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="tax_number" class="col-sm-2 col-form-label">Tax Number <span
                                            class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="tax_number" placeholder="GSTIN1234"
                                            name="tax_number">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="tax_number" class="col-sm-2 col-form-label">Low Stock Limit <small>(Default limit if product-wise
                                            stock limit is not set. )</small></label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="low_stock_limit"
                                            placeholder="Product low stock limit" name="low_stock_limit">
                                    </div>
                                </div>



                                <div class="form-group">
                                    <button type="reset" class="btn btn-warning">Reset</button>
                                    <button type="submit" class="btn btn-success" id="submit_btn">Submit</button>
                                </div>
                            </div>

                            <!-- /.card-footer -->
                        </form>
                    </div>
                    <!--/.card-->
                </div>
                <!--/.col-md-12-->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Seller Profile</h4>
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

                <div class="col-md-12">
                    <div class="card card-info">
                        <!-- form start -->

                        <div class="d-flex form-group justify-content-center profile_image mt-3">
                            <?php if (!empty($fetched_data[0]['image'])) { ?>
                                <img class="avatar" src="<?= base_url(rawurlencode($fetched_data[0]['image'])) ?>" alt="<?= !empty($this->lang->line('profile_image')) ? str_replace('\\', '', $this->lang->line('profile_image')) : 'Profile Image' ?>">
                            <?php } else { ?>
                                <img class="avatar" src="<?= base_url() . NO_USER_IMAGE ?>" alt="<?= !empty($this->lang->line('profile_image')) ? str_replace('\\', '', $this->lang->line('profile_image')) : 'Profile Image' ?>">
                            <?php } ?>
                        </div>

                        <form class="form-horizontal form-submit-event" action="<?= base_url('seller/login/update_user'); ?>" method="POST" id="add_product_form">
                            <?php if (isset($fetched_data[0]['id'])) { ?>
                                <input type="hidden" name="edit_seller" value="<?= $fetched_data[0]['user_id'] ?>">
                                <input type="hidden" name="status" value="1">
                                <input type="hidden" name="edit_seller_data_id" value="<?= $fetched_data[0]['id'] ?>">
                                <input type="hidden" name="old_address_proof" value="<?= $fetched_data[0]['address_proof'] ?>">
                                <input type="hidden" name="old_store_logo" value="<?= $fetched_data[0]['logo'] ?>">
                                <input type="hidden" name="old_authorized_signature" value="<?= $fetched_data[0]['authorized_signature'] ?>">
                                <input type="hidden" name="old_national_identity_card" value="<?= $fetched_data[0]['national_identity_card'] ?>">
                                <input type="hidden" name="old_profile_image" value="<?= $fetched_data[0]['image'] ?>">
                            <?php
                            } ?>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="image" class="col-sm-2 col-form-label">Profile Image <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="file" class="form-control file_upload_height" name="image" id="image" accept="image/*" />
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label">Name <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="name" placeholder="Seller Name" name="name" value="<?= @$fetched_data[0]['username'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mobile" class="col-sm-2 col-form-label">Mobile <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="mobile" placeholder="Enter Mobile" name="mobile" value="<?= @$fetched_data[0]['mobile'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email" class="col-sm-2 col-form-label">Email <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" id="email" placeholder="Enter Email" name="email" value="<?= @$fetched_data[0]['email'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="old" class="col-sm-2 col-form-label">Old Password</label>

                                    <div class="input-group col-sm-10">
                                        <input type="password" class="form-control form-input passwordToggle" name="old" id="old" placeholder="Type Password here" value="">
                                        <span class="input-group-text togglePassword" style="cursor: pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="new" class="col-sm-2 col-form-label">New Password</label>

                                    <div class="input-group col-sm-10">
                                        <input type="password" class="form-control form-input passwordToggle" name="new" id="new" placeholder="New Password" value="">
                                        <span class="input-group-text togglePassword" style="cursor: pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="new_confirm" class="col-sm-2 col-form-label">Confirm New Password</label>

                                    <div class="input-group col-sm-10">
                                        <input type="password" class="form-control form-input passwordToggle" name="new_confirm" id="new_confirm" placeholder="Type Confirm Password here" value="">
                                        <span class="input-group-text togglePassword" style="cursor: pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address" class="col-sm-2 col-form-label">Address <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <textarea type="text" class="form-control" id="address" placeholder="Enter Address" name="address"><?= isset($fetched_data[0]['address']) ? @$fetched_data[0]['address'] : ""; ?></textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="authorized_signature" class="col-sm-2 col-form-label">Authorized Signature <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <?php if (isset($fetched_data[0]['authorized_signature']) && !empty($fetched_data[0]['authorized_signature'])) { ?>
                                            <span class="text-danger">*Leave blank if there is no change</span>
                                        <?php } ?>
                                        <input type="file" class="form-control file_upload_height" name="authorized_signature" id="authorized_signature" accept="image/*" />
                                    </div>
                                </div>
                                <?php if (isset($fetched_data[0]['authorized_signature']) && !empty($fetched_data[0]['authorized_signature'])) { ?>
                                    <div class="form-group row">
                                        <div class="mx-auto product-image"><a href="<?= base_url($fetched_data[0]['authorized_signature']); ?>" data-toggle="lightbox" data-gallery="gallery_seller"><img src="<?= base_url($fetched_data[0]['authorized_signature']); ?>" class="img-fluid rounded"></a></div>
                                    </div>
                                <?php } ?>

                                <?php
                                $pincode_wise_deliverability = (isset($shipping_method['pincode_wise_deliverability']) && $shipping_method['pincode_wise_deliverability'] == 1) ? $shipping_method['pincode_wise_deliverability'] : '0';
                                $city_wise_deliverability = (isset($shipping_method['city_wise_deliverability']) && $shipping_method['city_wise_deliverability'] == 1) ? $shipping_method['city_wise_deliverability'] : '0';
                                ?>

                                <input type="hidden" name="city_wise_deliverability" value="<?= $city_wise_deliverability ?>">
                                <input type="hidden" name="pincode_wise_deliverability" value="<?= $pincode_wise_deliverability ?>">
                                <div class="form-group row deliverable_type">
                                    <?php if ((isset($shipping_method['pincode_wise_deliverability']) && $shipping_method['pincode_wise_deliverability'] == 1) || (isset($shipping_method['local_shipping_method']) && isset($shipping_method['shiprocket_shipping_method']) && $shipping_method['local_shipping_method'] == 1 && $shipping_method['shiprocket_shipping_method'] == 1)) { ?>

                                        <label for="deliverable_type" class="col-form-label col-sm-2">Deliverable Zipcode Type</label>
                                        <div class="form-group col-sm-10">
                                            <select class="form-control" name="deliverable_zipcode_type" id="deliverable_zipcode_type">
                                                <option value="<?= ALL ?>" <?= (isset($fetched_data[0]['deliverable_zipcode_type']) && $fetched_data[0]['deliverable_zipcode_type'] == ALL) ? 'selected' : ''; ?>>All</option>
                                                <option value="<?= INCLUDED ?>" <?= (isset($fetched_data[0]['deliverable_zipcode_type']) && $fetched_data[0]['deliverable_zipcode_type'] == INCLUDED) ? 'selected' : ''; ?>>Included</option>
                                            </select>
                                        </div>

                                    <?php  }
                                    if (isset($shipping_method['city_wise_deliverability']) && $shipping_method['city_wise_deliverability'] == 1 && $shipping_method['shiprocket_shipping_method'] != 1) { ?>

                                        <label for="" class="col-form-label col-sm-2">Deliverable City Type</label>
                                        <div class="form-group col-md-10">
                                            <select class="form-control" name="deliverable_city_type" id="deliverable_city_type">
                                                <option value="<?= ALL ?>" <?= (isset($fetched_data[0]['deliverable_city_type']) && $fetched_data[0]['deliverable_city_type'] == ALL) ? 'selected' : ''; ?>>All</option>
                                                <option value="<?= INCLUDED ?>" <?= (isset($fetched_data[0]['deliverable_city_type']) && $fetched_data[0]['deliverable_city_type'] == INCLUDED) ? 'selected' : ''; ?>>Included</option>
                                            </select>
                                        </div>

                                    <?php } ?>
                                </div>
                                <div class="form-group row">
                                    <?php if ((isset($shipping_method['pincode_wise_deliverability']) && $shipping_method['pincode_wise_deliverability'] == 1) || (isset($shipping_method['local_shipping_method']) && isset($shipping_method['shiprocket_shipping_method']) && $shipping_method['local_shipping_method'] == 1 && $shipping_method['shiprocket_shipping_method'] == 1)) { ?>
                                        <label for="serviceable_zipcodes" class="col-form-label col-sm-2">Serviceable Zipcodes <small>(required for included)</small><span class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <?php
                                            $zipcodes = (isset($fetched_data[0]['serviceable_zipcodes']) &&  $fetched_data[0]['serviceable_zipcodes'] != NULL) ? explode(",", $fetched_data[0]['serviceable_zipcodes']) : [];
                                            $zipcodes_name = fetch_details('zipcodes', "", 'zipcode,id', "", "", "", "", "id", $zipcodes);

                                            ?>
                                            <select name="serviceable_zipcodes[]" class="search_zipcode form-control w-100" multiple onload="multiselect()" id="deliverable_zipcodes" <?= (isset($fetched_data[0]['deliverable_zipcode_type']) && ($fetched_data[0]['deliverable_zipcode_type'] == INCLUDED)) ? "" : "disabled" ?>>
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
                                        <label for="cities" class="col-form-label col-sm-2">Serviceable Cities <small>(required for included)</small><span class='text-danger text-sm'>*</span></label>
                                        <?php
                                        $selected_city_ids = (isset($fetched_data[0]['serviceable_cities']) &&  $fetched_data[0]['serviceable_cities'] != NULL) ? explode(",", $fetched_data[0]['serviceable_cities']) : [];
                                        ?>
                                        <div class="col-sm-10">

                                            <select class="form-control city_list" name="serviceable_cities[]" id="deliverable_cities" multiple <?= (isset($fetched_data[0]['deliverable_city_type']) && ($fetched_data[0]['deliverable_city_type'] == INCLUDED)) ? "" : "disabled" ?>>
                                                <?php foreach ($cities as $row) { ?>
                                                    <option value="<?= $row['id'] ?>" <?= (in_array($row['id'], $selected_city_ids)) ? 'selected' : ''; ?>><?= $row['name'] ?></option>
                                                <?php }; ?>
                                            </select>
                                        </div>
                                    <?php } ?>

                                </div>

                                <h4>Store Details</h4>
                                <hr>
                                <div class="form-group row">
                                    <label for="store_name" class="col-sm-2 col-form-label">Name <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="store_name" placeholder="Store Name" name="store_name" value="<?= @$fetched_data[0]['store_name'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="store_url" class="col-sm-2 col-form-label">URL</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="store_url" placeholder="Store URL" name="store_url" value="<?= @$fetched_data[0]['store_url'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="store_description" class="col-sm-2 col-form-label">Description <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <textarea type="text" class="form-control" id="store_description" placeholder="Store Description" name="store_description"><?= isset($fetched_data[0]['store_description']) ? @$fetched_data[0]['store_description'] : ""; ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="logo" class="col-sm-2 col-form-label">Logo <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <?php if (isset($fetched_data[0]['logo']) && !empty($fetched_data[0]['logo'])) { ?>
                                            <span class="text-danger">*Leave blank if there is no change</span>
                                        <?php } ?>
                                        <input type="file" class="form-control file_upload_height" name="store_logo" id="store_logo" accept="image/*" />
                                    </div>
                                </div>
                                <?php if (isset($fetched_data[0]['logo']) && !empty($fetched_data[0]['logo'])) { ?>
                                    <div class="form-group row">
                                        <div class="mx-auto product-image"><a href="<?= base_url($fetched_data[0]['logo']); ?>" data-toggle="lightbox" data-gallery="gallery_seller"><img src="<?= base_url($fetched_data[0]['logo']); ?>" class="img-fluid rounded"></a></div>
                                    </div>
                                <?php } ?>

                                <h4>Other Details</h4>
                                <hr>
                                <div class="form-group row">
                                    <label for="latitude" class="col-sm-2 col-form-label">Latitude <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="latitude" placeholder="Latitude" name="latitude" value="<?= @$fetched_data[0]['latitude'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="longitude" class="col-sm-2 col-form-label">Longitude <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="longitude" placeholder="Longitude" name="longitude" value="<?= @$fetched_data[0]['longitude'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="tax_name" class="col-sm-2 col-form-label">Tax Name <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="tax_name" placeholder="Tax Name" name="tax_name" value="<?= @$fetched_data[0]['tax_name'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="tax_number" class="col-sm-2 col-form-label">Tax Number <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="tax_number" placeholder="Tax Number" name="tax_number" value="<?= @$fetched_data[0]['tax_number'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="tax_number" class="col-sm-2 col-form-label">Low Stock Limit<small>(Default limit if product-wise stock limit is not set. )</small></label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="low_stock_limit" placeholder="Product low stock limit" name="low_stock_limit" value="<?= @$fetched_data[0]['low_stock_limit'] ?>">
                                    </div>
                                </div>

                                <hr class="mt-4">
                                <h4 class="bg-light m-0 px-2 py-3">SEO Configuration</h4>

                                <div class="d-flex bg-light">
                                    <div class="form-group col-sm-6">
                                        <label for="seo_page_title" class="form-label form-label-sm d-flex">
                                            SEO Page Title
                                        </label>
                                        <input type="text" class="form-control" id="seo_page_title"
                                            placeholder="SEO Page Title" name="seo_page_title"
                                            value="<?= isset($fetched_data[0]['seo_page_title']) ? output_escaping($fetched_data[0]['seo_page_title']) : "" ?>">
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label for="seo_meta_keywords" class="form-label form-label-sm d-flex">
                                            SEO Meta Keywords
                                        </label>
                                        <input class='tags bg-white' id='seo_meta_keywords' placeholder="SEO Meta Keywords" name="seo_meta_keywords" value="<?= isset($fetched_data[0]['seo_meta_keywords']) ? output_escaping($fetched_data[0]['seo_meta_keywords']) : "" ?>" />
                                    </div>
                                </div>
                                <div class="d-flex bg-light">

                                    <div class="form-group col-sm-6">
                                        <label for="seo_meta_description" class="form-label form-label-sm d-flex">
                                            SEO Meta Description
                                        </label>
                                        <textarea class="form-control" id="seo_meta_description"
                                            placeholder="SEO Meta Keywords" name="seo_meta_description"><?= isset($fetched_data[0]['seo_meta_description']) ? output_escaping($fetched_data[0]['seo_meta_description']) : "" ?></textarea>
                                    </div>

                                    <div class="col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <label for="image">SEO Open Graph Image <small>(Recommended Size : 131 x 131 pixels)</small></label>
                                            <div class="col-sm-10">
                                                <div class='col-md-12'>
                                                    <a class="uploadFile img btn btn-primary text-white btn-sm" data-input='seo_og_image' data-isremovable='1'
                                                        data-is-multiple-uploads-allowed='0' data-toggle="modal" data-target="#media-upload-modal" value="Upload"><i class='fa fa-upload'></i> Upload</a>
                                                </div>
                                                <?php
                                                if (!empty(@$fetched_data[0]['seo_og_image'])) {
                                                ?>
                                                    <label class="text-danger mt-3">*Only Choose When Update is
                                                        necessary</label>
                                                    <div class="container-fluid row image-upload-section w-25">
                                                        <div class="col-md-12 col-sm-12 shadow p-3 bg-white rounded text-center grow image">
                                                            <div class='image-upload-div'><img class="img-fluid mb-2"
                                                                    src="<?= base_url() . str_replace('//', '/', $fetched_data[0]['seo_og_image']) ?>"
                                                                    alt="Image Not Found"></div>
                                                            <input type="hidden" name="seo_og_image" value='<?= $fetched_data[0]['seo_og_image'] ?>'>
                                                        </div>
                                                    </div>
                                                <?php
                                                } else { ?>
                                                    <div class="container-fluid row image-upload-section">
                                                        <div
                                                            class="col-md-12 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image d-none">
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group my-3">
                                    <button type="reset" class="btn btn-warning">Reset</button>
                                    <button type="submit" class="btn btn-success" id="submit_btn">Update Profile</button>
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
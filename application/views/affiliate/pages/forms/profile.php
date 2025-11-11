<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <!-- form start -->
                        <div class="d-flex form-group justify-content-center profile_image mt-3">
                            <?php if (!empty($fetched_data[0]['image'])) { ?>
                                <img class="avatar" src="<?= base_url($fetched_data[0]['image']) ?>" alt="<?= !empty($this->lang->line('profile_image')) ? str_replace('\\', '', $this->lang->line('profile_image')) : 'Profile Image' ?>">
                            <?php } else { ?>
                                <img class="avatar" src="<?= base_url() . NO_USER_IMAGE ?>" alt="<?= !empty($this->lang->line('profile_image')) ? str_replace('\\', '', $this->lang->line('profile_image')) : 'Profile Image' ?>">
                            <?php } ?>
                        </div>
                        
                        <form class="form-submit-event" action="<?= base_url('affiliate/login/update_user') ?>" method="POST">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="image" class="col-sm-2 col-form-label">Profile Image <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="file" class="form-control file_upload_height" name="image" id="image" accept="image/*"  />
                                    </div>
                                </div>

                                <input type="hidden" name="old_profile_image" value="<?= $fetched_data[0]['image'] ?>">
                                <input type="hidden" name="edit_affiliate_user" id="edit_affiliate_user" value="<?= isset($fetched_data) ? $fetched_data[0]['user_id'] : ''; ?>">
                                <input type="hidden" name="edit_affiliate_data_id" id="edit_affiliate_data_id" value="<?= isset($fetched_data) ? $fetched_data[0]['id'] : ''; ?>">
                                <input type="hidden" name="affiliate_uuid" id="affiliate_uuid" value="<?= isset($fetched_data) ? $fetched_data[0]['uuid'] : ''; ?>">
                                <input type="hidden" name="is_affiliate_user" id="is_affiliate_user" value="<?= isset($fetched_data) ? $fetched_data[0]['is_affiliate_user'] : ''; ?>">
                                <input type="hidden" name="status" id="status" value="<?= isset($fetched_data) ? $fetched_data[0]['affiliate_user_status'] : ''; ?>">

                                <div class="form-group row">
                                    <label for="full_name" class="col-sm-2 col-form-label">Username <span class='text-danger text-xs'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="full_name" placeholder="Type Username here" name="full_name" value="<?= isset($fetched_data) ? $fetched_data[0]['username'] : ''; ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mobile" class="col-sm-2 col-form-label">Mobile <span class='text-danger text-xs'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" maxlength="16" oninput="validateNumberInput(this)" id="mobile" placeholder="Type Mobile Number here" name="mobile" value="<?= isset($fetched_data) ? $fetched_data[0]['mobile'] : ''; ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email" class="col-sm-2 col-form-label">Email Address<span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" id="email" placeholder="Enter Email" name="email" value="<?= isset($fetched_data) ? $fetched_data[0]['email'] : ''; ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address" class="col-sm-2 col-form-label">Address<span class='text-danger text-xs'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="address" placeholder="Add your address here" name="address" value="<?= isset($fetched_data) ? $fetched_data[0]['address'] : ''; ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="my_website" class="col-sm-2 col-form-label">Enter Your Website <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="url" class="form-control" id="my_website" placeholder="https://www.example.com/myblog" name="my_website" value="<?= isset($fetched_data) ? $fetched_data[0]['website_url'] : ''; ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="my_app" class="col-sm-2 col-form-label">Enter Your Mobile APP <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="url" class="form-control" id="my_app" placeholder="https://xxxx/dp/xxxx" name="my_app" value="<?= isset($fetched_data) ? $fetched_data[0]['mobile_app_url'] : ''; ?>">
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
                                <div class="form-group">
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
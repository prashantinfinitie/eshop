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
                            <?php if (!empty($users->image)) { ?>
                                <img class="avatar" src="<?= base_url(rawurlencode($users->image)) ?>" alt="<?= !empty($this->lang->line('profile_image')) ? str_replace('\\', '', $this->lang->line('profile_image')) : 'Profile Image' ?>">
                            <?php } else { ?>
                                <img class="avatar" src="<?= base_url() . NO_USER_IMAGE ?>" alt="<?= !empty($this->lang->line('profile_image')) ? str_replace('\\', '', $this->lang->line('profile_image')) : 'Profile Image' ?>">
                            <?php } ?>
                        </div>
                        <form class="form-submit-event" action="<?= base_url('admin/login/update_user') ?>" method="POST">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="image" class="col-sm-2 col-form-label">Profile Image <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="file" class="form-control file_upload_height" name="image"
                                            id="image" accept="image/*" />
                                    </div>
                                </div>
                               
                                <input type="hidden" name="old_profile_image" value="<?= $users->image ?>">
                                <div class="form-group row">
                                    <label for="username" class="col-sm-2 col-form-label">Username <span class='text-danger text-xs'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="username" placeholder="Type Username here" name="username" value="<?= $users->username ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <?php if ($identity_column == 'email') { ?>
                                        <label for="email" class="col-sm-2 col-form-label">Email <span class='text-danger text-xs'>*</span></label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="email" placeholder="Type Email ID here" name="email" value="<?= $users->email ?>">
                                        </div>
                                    <?php } else { ?>
                                        <label for="mobile" class="col-sm-2 col-form-label">Mobile <span class='text-danger text-xs'>*</span></label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" maxlength="16" oninput="validateNumberInput(this)" id="mobile" placeholder="Type Mobile Number here" name="mobile" value="<?= $users->mobile ?>">
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="form-group row">
                                    <label for="address" class="col-sm-2 col-form-label">Address<span class='text-danger text-xs'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="address" placeholder="Add your address here" name="address" value="<?= $users->address ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="latitude" class="col-sm-2 col-form-label">Latitude <span class='text-danger text-xs'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="latitude" placeholder="Add your latitude here" name="latitude" value="<?= $users->latitude ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="longitude" class="col-sm-2 col-form-label">Longitude<span class='text-danger text-xs'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="longitude" placeholder="Add your longitude here" name="longitude" value="<?= $users->longitude ?>">
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
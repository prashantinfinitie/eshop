<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Add User</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
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


                        <!-- <h2 class="text-center">Creating Your Associates Account</h2> -->
                        <div class="mx-5 my-3 stepper-container">
                            <div class="step-wrapper">
                                <div class="step" id="step1" onclick="goToStep(1)">
                                    <div class="circle">✓</div>
                                    <div class="label">Account Information</div>
                                </div>
                                <div class="bar"></div>
                            </div>
                            <div class="step-wrapper">
                                <div class="step" id="step2" onclick="goToStep(2)">
                                    <div class="circle">✓</div>
                                    <div class="label">Website and Mobile App List</div>
                                </div>
                                <div class="bar"></div>
                            </div>
                            <div class="step-wrapper">
                                <div class="step" id="step4" onclick="goToStep(3)">
                                    <div class="circle"></div>
                                    <div class="label">Start Using Associates Central</div>
                                </div>
                            </div>
                        </div>

                        <!-- <form id="amazonForm"> -->
                        <form class="form-horizontal form-submit-event add_affiliate_user_form" action="<?= base_url('admin/affiliate_users/add_user'); ?>" method="POST" id="add_affiliate_user_form" novalidate>
                            <div class="card-body">
                                <div class="step-page" id="page1">
                                    <h2>Account Information</h2>
                                    <p class="mb-4 text-gray">Please provide your basic account information.</p>

                                    <input type="hidden" name="edit_affiliate_user" id="edit_affiliate_user" value="<?= isset($fetched_data) ? $fetched_data[0]['user_id'] : ''; ?>">
                                    <input type="hidden" name="edit_affiliate_data_id" id="edit_affiliate_data_id" value="<?= isset($fetched_data) ? $fetched_data[0]['id'] : ''; ?>">
                                    <input type="hidden" name="affiliate_uuid" id="affiliate_uuid" value="<?= isset($fetched_data) ? $fetched_data[0]['uuid'] : ''; ?>">

                                    <input type="hidden" name="is_affiliate_user" id="is_affiliate_user" value="1">
                                    <div class="form-group row">
                                        <label for="full_name" class="col-sm-2 col-form-label">Full Name <span class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="full_name" placeholder="Enter Full Name" name="full_name" value="<?= isset($fetched_data) ? $fetched_data[0]['username'] : ''; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="email" class="col-sm-2 col-form-label">Email Address<span class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <input type="email" class="form-control" id="email" placeholder="Enter Email" name="email" value="<?= isset($fetched_data) ? $fetched_data[0]['email'] : ''; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="mobile" class="col-sm-2 col-form-label">Mobile <span class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <input type="text" maxlength="16" oninput="validateNumberInput(this)" class="form-control" id="mobile" placeholder="Enter Mobile" name="mobile" value="<?= isset($fetched_data) ? $fetched_data[0]['mobile'] : ''; ?>">
                                        </div>
                                    </div>
                                    <?php
                                    if (!isset($fetched_data[0]['id'])) {
                                    ?>
                                        <div class="form-group row ">
                                            <label for="password" class="col-sm-2 col-form-label">Password <span class='text-danger text-sm'>*</span></label>
                                            <div class="col-sm-10 d-flex form-group">
                                                <input type="password" class="form-control passwordToggle" id="password" placeholder="Enter Passsword" name="password" value="<?= isset($fetched_data) ? $fetched_data[0]['password'] : ''; ?>">
                                                <span class="input-group-text togglePassword" style="cursor: pointer;">
                                                    <i class="fa fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="form-group row ">
                                            <label for="confirm_password" class="col-sm-2 col-form-label">Confirm Password <span class='text-danger text-sm'>*</span></label>
                                            <div class="col-sm-10 d-flex form-group">
                                                <input type="password" class="form-control passwordToggle" id="confirm_password" placeholder="Enter Confirm Password" name="confirm_password" value="<?= isset($fetched_data) ? $fetched_data[0]['password'] : ''; ?>">
                                                <span class="input-group-text togglePassword" style="cursor: pointer;">
                                                    <i class="fa fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="form-group row">
                                        <label for="address" class="col-sm-2 col-form-label">Address <span
                                                class='text-danger text-sm'>*</span></label>
                                        <div class="col-sm-10">
                                            <textarea type="text" class="form-control" id="address" placeholder="Enter Address" name="address"><?= isset($fetched_data) ? $fetched_data[0]['address'] : ''; ?></textarea>
                                        </div>
                                    </div>

                                    <button class="btn btn-primary mt-3" type="button" onclick="nextStep(2)">Next</button>
                                </div>

                                <div class="step-page d-none" id="page2">
                                    <h4 class="mb-4">Your Websites and Mobile Apps</h4>
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
                                    <button class="btn btn-secondary" type="button" onclick="prevStep(1)">Previous</button>
                                    <button class="btn btn-primary" type="button" onclick="nextStep(3)">Next</button>
                                </div>

                                <div class="step-page d-none" id="page3">
                                    <h4>Start Using Associates Central</h4>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Status <span
                                                class='text-danger text-sm'>*</span></label>
                                        <div id="status" class="btn-group col-sm-4">
                                            <label class="btn btn-default" data-toggle-class="btn-default"
                                                data-toggle-passive-class="btn-default">
                                                <input type="radio" name="status" value="0"
                                                    <?= (isset($fetched_data[0]['status']) && $fetched_data[0]['status'] == '0') ? 'Checked' : '' ?>> Deactive
                                            </label>
                                            <label class="btn btn-primary" data-toggle-class="btn-primary"
                                                data-toggle-passive-class="btn-default">
                                                <input type="radio" name="status" value="1"
                                                    <?= (isset($fetched_data[0]['status']) && $fetched_data[0]['status'] == '1') ? 'Checked' : '' ?>> Approved
                                            </label>
                                            <label class="btn btn-danger" data-toggle-class="btn-danger"
                                                data-toggle-passive-class="btn-default">
                                                <input type="radio" name="status" value="2"
                                                    <?= (isset($fetched_data[0]['status']) && $fetched_data[0]['status'] == '2') ? 'Checked' : '' ?>> Not-Approved
                                            </label>
                                        </div>
                                    </div>
                                    <button class="btn btn-secondary" type="button" onclick="prevStep(2)">Previous</button>
                                    <button class="btn btn-success" id="submit_btn" type="submit">Finish</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
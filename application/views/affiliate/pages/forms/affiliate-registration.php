<div class="">
    <!-- Content Header (Page header) -->
    <!-- Main content -->

    <section class="content form-box">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card_seller card-info p-3 form-card">
                        <div class="login-logo">
                            <a href="<?= base_url() . 'affiliate/login' ?>"><img
                                    src="<?= base_url() . $logo ?>"></a>
                        </div>
                        <h2 class="text-center">Creating Your Associates Account</h2>

                        <form class="form-horizontal form-submit-event" action="<?= base_url('affiliate/auth/add_user'); ?>" method="POST" id="add_affiliate_user_form">
                            <div class="card-body px-2">
                                <!-- <div class="step-page" id="page1"> -->
                                <!-- <h2>Account Information</h2>
                                    <p class="mb-4 text-gray">Please provide your basic account information.</p> -->

                                <input type="hidden" name="edit_affiliate_user" id="edit_affiliate_user" value="<?= isset($fetched_data) ? $fetched_data[0]['user_id'] : ''; ?>">
                                <input type="hidden" name="edit_affiliate_data_id" id="edit_affiliate_data_id" value="<?= isset($fetched_data) ? $fetched_data[0]['id'] : ''; ?>">
                                <input type="hidden" name="affiliate_uuid" id="affiliate_uuid" value="<?= isset($fetched_data) ? $fetched_data[0]['uuid'] : ''; ?>">

                                <input type="hidden" name="is_affiliate_user" id="is_affiliate_user" value="1">
                                <div class="d-flex form-group justify-content-between">
                                    <label for="full_name" class="col-form-label">Full Name <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="full_name" placeholder="Enter Full Name" name="full_name" value="<?= isset($fetched_data) ? $fetched_data[0]['username'] : ''; ?>">
                                    </div>
                                </div>

                                <div class="d-flex form-group justify-content-between">
                                    <label for="email" class="col-form-label">Email Address<span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-8">
                                        <input type="email" class="form-control" id="email" placeholder="Enter Email" name="email" value="<?= isset($fetched_data) ? $fetched_data[0]['email'] : ''; ?>">
                                    </div>
                                </div>

                                <div class="d-flex form-group justify-content-between">
                                    <label for="mobile" class="col-form-label">Mobile <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" maxlength="16" oninput="validateNumberInput(this)" class="form-control" id="mobile" placeholder="Enter Mobile" name="mobile" value="<?= isset($fetched_data) ? $fetched_data[0]['mobile'] : ''; ?>">
                                    </div>
                                </div>

                                <div class="d-flex form-group justify-content-between ">
                                    <label for="password" class="col-form-label">Password <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-8 d-flex form-group">
                                        <input type="password" class="form-control passwordToggle" id="password" placeholder="Enter Passsword" name="password">
                                        <span class="input-group-text togglePassword" style="cursor: pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex form-group justify-content-between ">
                                    <label for="confirm_password" class="col-form-label">Confirm Password <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-8 d-flex form-group">
                                        <input type="password" class="form-control passwordToggle" id="confirm_password" placeholder="Enter Confirm Password" name="confirm_password">
                                        <span class="input-group-text togglePassword" style="cursor: pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex form-group justify-content-between">
                                    <label for="address" class="col-form-label">Address <span
                                            class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-8">
                                        <textarea type="text" class="form-control" id="address" placeholder="Enter Address" name="address"><?= isset($fetched_data) ? $fetched_data[0]['address'] : ''; ?></textarea>
                                    </div>
                                </div>

                                <div class="d-flex form-group justify-content-between">
                                    <label for="my_website" class="col-form-label">Your Website <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-8">
                                        <input type="url" class="form-control" id="my_website" placeholder="https://www.example.com/myblog" name="my_website" value="<?= isset($fetched_data) ? $fetched_data[0]['website_url'] : ''; ?>">
                                    </div>
                                </div>
                                <div class="d-flex form-group justify-content-between">
                                    <label for="my_app" class="col-form-label">Your Mobile APP <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-8">
                                        <input type="url" class="form-control" id="my_app" placeholder="https://xxxx/dp/xxxx" name="my_app" value="<?= isset($fetched_data) ? $fetched_data[0]['mobile_app_url'] : ''; ?>">
                                    </div>
                                </div>
                                <button class="btn btn-success" type="submit">Submit</button>
                                <a href="<?= base_url('affiliate/login') ?>" class="btn btn-warning">Back to Login</a>


                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<style>
    .step.active {
        font-weight: bold;
        color: green;
    }

    .step.completed::after {
        content: '\2713';
        color: white;
        background: green;
        border-radius: 50%;
        padding: 2px 5px;
        margin-left: 8px;
    }

    .step {
        margin-right: 20px;
    }
</style>
<script>

</script>
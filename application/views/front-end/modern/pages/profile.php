<!-- breadcrumb -->
<div class="content-wrapper deeplink_wrapper">
    <section class="wrapper bg-soft-grape">
        <div class="container py-3 py-md-5">
            <nav class="d-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('my-account/profile') ?>" class="text-decoration-none"><?= !empty($this->lang->line('dashboard')) ? str_replace('\\', '', $this->lang->line('dashboard')) : 'Dashboard' ?></a></li>
                    <?php if (isset($right_breadcrumb) && !empty($right_breadcrumb)) {
                        foreach ($right_breadcrumb as $row) {
                    ?>
                            <li class="breadcrumb-item"><?= $row ?></li>
                    <?php }
                    } ?>
                    <li class="breadcrumb-item active text-muted" aria-current="page"><?= !empty($this->lang->line('my_account')) ? str_replace('\\', '', $this->lang->line('my_account')) : 'My Account' ?></li>
                </ol>
            </nav>
            <!-- /nav -->
        </div>
        <!-- /.container -->
    </section>
</div>
<!-- end breadcrumb -->

<section class="my-account-section">
    <div class="container mb-15">
        <div class="my-8">
            <?php $this->load->view('front-end/' . THEME . '/pages/dashboard') ?>
        </div>

        <div>
            <div class="card-header bg-white">
                <h1 class="h4"><?= !empty($this->lang->line('profile')) ? str_replace('\\', '', $this->lang->line('profile')) : 'PROFILE' ?></h1>
            </div>
            <hr class="mt-5 mb-5">
            <div class="card p-7">

                <form class="form-submit-event" method="POST" action="<?= base_url('login/update_user') ?>">
                    <div class="d-flex form-group justify-content-center profile_image">
                        <?php if (!empty($users->image)) { ?>
                            <img class="avatar" src="<?= base_url(rawurlencode($users->image)) ?>" alt="<?= !empty($this->lang->line('profile_image')) ? str_replace('\\', '', $this->lang->line('profile_image')) : 'Profile Image' ?>">
                        <?php } else { ?>
                            <img class="avatar" src="<?= base_url() . NO_USER_IMAGE ?>" alt="<?= !empty($this->lang->line('profile_image')) ? str_replace('\\', '', $this->lang->line('profile_image')) : 'Profile Image' ?>">
                        <?php } ?>
                    </div>
                    <div class="col-md-6 form-group px-0">
                        <label for="profile_image" class="col-form-label p-0"><?= !empty($this->lang->line('profile_image')) ? str_replace('\\', '', $this->lang->line('profile_image')) : 'Profile Image' ?></label>
                        <input type="file" class="form-control" name="profile_image[]" id="profile_image" />
                        <input type="hidden" name="user_profile_image" value="<?= $users->image ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="username" class="col-sm-12 col-form-label p-0"><?= !empty($this->lang->line('username')) ? str_replace('\\', '', $this->lang->line('username')) : 'Username' ?>*</label>
                            <input type="text" class="form-control" id="username" placeholder="<?= !empty($this->lang->line('username')) ? str_replace('\\', '', $this->lang->line('username')) : 'Username' ?>" name="username" value="<?= $users->username ?>">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="mobile" class="col-sm-12 col-form-label p-0"><?= !empty($this->lang->line('mobile')) ? str_replace('\\', '', $this->lang->line('mobile')) : 'Mobile' ?>*</label>
                            <div>
                                <input type="phone" class="form-control" id="mobile" placeholder="<?= !empty($this->lang->line('enter_mobile_number')) ? str_replace('\\', '', $this->lang->line('enter_mobile_number')) : 'Enter Mobile Number' ?>" name="mobile" value="<?= $users->mobile ?>" <?= isset($users->type) && ($users->type == 'phone' || $users->type == '') && ($system_settings['login_with_email'] == 0 || $system_settings['login_with_email'] == '0') ? 'readonly' : '' ?>>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="email" class="col-sm-12 col-form-label p-0"><?= !empty($this->lang->line('email')) ? str_replace('\\', '', $this->lang->line('email')) : 'Email' ?>*</label>
                            <input type="text" class="form-control" id="profile_email" placeholder="<?= !empty($this->lang->line('email')) ? str_replace('\\', '', $this->lang->line('email')) : 'Email' ?>" name="email" value="<?= $users->email ?>" <?= (isset($users->type) && !empty($users->type) && ($users->type == 'google' || ($users->type == 'facebook') && $users->type != '' && !empty($users->email))) || ($system_settings['login_with_email'] == 1 || $system_settings['login_with_email'] == '1') ? 'readonly' : '' ?>>
                        </div>
                    </div>

                    <div class="form-group <?= isset($users->type) && !empty($users->type) && $users->type != 'phone' ? 'd-none' : '' ?>">
                        <label for="old" class="col-sm-12 col-form-label p-0"><?= !empty($this->lang->line('old_password')) ? str_replace('\\', '', $this->lang->line('old_password')) : 'Old Password' ?></label>
                        <input type="password" class="form-control" id="old" placeholder="<?= !empty($this->lang->line('old_password')) ? str_replace('\\', '', $this->lang->line('old_password')) : 'Old Password' ?>" name="old">
                    </div>
                    <div class="form-row <?= isset($users->type) && !empty($users->type) && $users->type != 'phone' ? 'd-none' : '' ?>">
                        <div class="form-group col-md-6">
                            <label for="new" class="col-sm-12 col-form-label p-0"><?= !empty($this->lang->line('new_password')) ? str_replace('\\', '', $this->lang->line('new_password')) : 'New Password' ?></label>
                            <input type="password" class="form-control" id="new" placeholder="<?= !empty($this->lang->line('new_password')) ? str_replace('\\', '', $this->lang->line('new_password')) : 'New Password' ?>" name="new">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="new_confirm" class="col-sm-12 col-form-label p-0"><?= !empty($this->lang->line('confirm_new_password')) ? str_replace('\\', '', $this->lang->line('confirm_new_password')) : 'Confirm New Password' ?></label>
                            <input type="password" class="form-control" id="new_confirm" placeholder="<?= !empty($this->lang->line('confirm_new_password')) ? str_replace('\\', '', $this->lang->line('confirm_new_password')) : 'Confirm New Password' ?>" name="new_confirm">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm btn-5 submit_btn"><?= !empty($this->lang->line('save')) ? str_replace('\\', '', $this->lang->line('save')) : 'Save' ?></button>
                    <div class="d-flex justify-content-center">
                        <div class="form-group" id="error_box">
                        </div>
                    </div>

                </form>

                <!--end profile -->
                <!--end col-->
            </div>
            <!--end row-->
        </div>
        <!--end container-->
        <!-- </div> -->
    </div>
</section>
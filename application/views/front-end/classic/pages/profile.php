<!-- breadcrumb -->
<section class="breadcrumb-title-bar colored-breadcrumb deeplink_wrapper">
    <div class="main-content responsive-breadcrumb">
        <h2><?= !empty($this->lang->line('my_account')) ? str_replace('\\', '', $this->lang->line('my_account')) : 'My Account' ?></h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('my-account') ?>"><?= !empty($this->lang->line('dashboard')) ? str_replace('\\', '', $this->lang->line('dashboard')) : 'Dashboard' ?></a></li>
                <li class="breadcrumb-item"><?= !empty($this->lang->line('my_account')) ? str_replace('\\', '', $this->lang->line('my_account')) : 'My Account' ?></li>
            </ol>
        </nav>
    </div>

</section>
<!-- end breadcrumb -->
<section class="my-account-section">
    <div class="main-content">
        <div class="col-md-12 mt-5 mb-3">
            <div class="user-detail align-items-center">
                <div class="ml-3">
                    <h6 class="text-muted mb-0"><?= !empty($this->lang->line('hello')) ? str_replace('\\', '', $this->lang->line('hello')) : 'Hello' ?></h6>
                    <h5 class="mb-0"><?= $user->username ?></h5>
                </div>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-md-4">
                <?php $this->load->view('front-end/' . THEME . '/pages/my-account-sidebar') ?>
            </div>
            <div class="col-md-8 col-12">
                <form class="form-submit-event" method="POST" action="<?= base_url('login/update_user') ?>">
                    <div class="d-flex form-group justify-content-center profile_image">
                        <?php if (!empty($users->image)) { ?>
                            <img class="avatar" src="<?= base_url(rawurlencode($users->image)) ?>" alt="<?= !empty($this->lang->line('profile_image')) ? str_replace('\\', '', $this->lang->line('profile_image')) : 'Profile Image' ?>">
                        <?php } else { ?>
                            <img class="avatar" src="<?= base_url() . NO_USER_IMAGE ?>" alt="<?= !empty($this->lang->line('profile_image')) ? str_replace('\\', '', $this->lang->line('profile_image')) : 'Profile Image' ?>">
                        <?php } ?>
                    </div>
                    <div class="col-md-6 form-group px-0">
                        <label for="profile_image" class="col-form-label"><?= !empty($this->lang->line('profile_image')) ? str_replace('\\', '', $this->lang->line('profile_image')) : 'Profile Image' ?></label>
                        <input type="file" class="form-control" name="profile_image[]" id="profile_image" />
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="username" class="col-sm-12 col-form-label"><?= !empty($this->lang->line('username')) ? str_replace('\\', '', $this->lang->line('username')) : 'Username' ?>*</label>
                            <input type="text" class="form-control" id="username" placeholder="<?= !empty($this->lang->line('username')) ? str_replace('\\', '', $this->lang->line('username')) : 'Username' ?>" name="username" value="<?= $users->username ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="mobile" class="col-sm-12 col-form-label"><?= !empty($this->lang->line('mobile')) ? str_replace('\\', '', $this->lang->line('mobile')) : 'Mobile' ?>*</label>
                            <div>
                                <input type="phone" class="form-control" id="mobile" placeholder="<?= !empty($this->lang->line('enter_mobile_number')) ? str_replace('\\', '', $this->lang->line('enter_mobile_number')) : 'Enter Mobile Number' ?>" name="mobile" value="<?= $users->mobile ?>" <?= isset($users->type) && ($users->type == 'phone' || $users->type == '') && ($system_settings['login_with_email'] == 0 || $system_settings['login_with_email'] == '0') ? 'readonly' : '' ?>>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="email" class="col-sm-12 col-form-label"><?= !empty($this->lang->line('email')) ? str_replace('\\', '', $this->lang->line('email')) : 'Email' ?>*</label>
                            <input type="text" class="form-control" id="email" placeholder="<?= !empty($this->lang->line('email')) ? str_replace('\\', '', $this->lang->line('email')) : 'Email' ?>" name="email" value="<?= $users->email ?>" <?= (isset($users->type) && !empty($users->type) && ($users->type == 'google' || ($users->type == 'facebook') && $users->type != '' && !empty($users->email))) || ($system_settings['login_with_email'] == 1 || $system_settings['login_with_email'] == '1') ? 'readonly' : '' ?>>
                        </div>
                    </div>

                    <div class="form-group <?= isset($users->type) && !empty($users->type) && $users->type != 'phone' ? 'd-none' : '' ?>">
                        <label for="old" class="col-sm-12 col-form-label"><small><?= !empty($this->lang->line('old_password')) ? str_replace('\\', '', $this->lang->line('old_password')) : 'Old Password' ?></small></label>
                        <input type="password" class="form-control" id="old" placeholder="<?= !empty($this->lang->line('old_password')) ? str_replace('\\', '', $this->lang->line('old_password')) : 'Old Password' ?>" name="old">
                    </div>
                    <div class="form-row <?= isset($users->type) && !empty($users->type) && $users->type != 'phone' ? 'd-none' : '' ?>">
                        <div class="form-group col-md-6">
                            <label for="new" class="col-sm-12 col-form-label"><?= !empty($this->lang->line('new_password')) ? str_replace('\\', '', $this->lang->line('new_password')) : 'New Password' ?></label>
                            <input type="password" class="form-control" id="new" placeholder="<?= !empty($this->lang->line('new_password')) ? str_replace('\\', '', $this->lang->line('new_password')) : 'New Password' ?>" name="new">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="new_confirm" class="col-sm-12 col-form-label"><?= !empty($this->lang->line('confirm_new_password')) ? str_replace('\\', '', $this->lang->line('confirm_new_password')) : 'Confirm New Password' ?></label>
                            <input type="password" class="form-control" id="new_confirm" placeholder="T<?= !empty($this->lang->line('confirm_new_password')) ? str_replace('\\', '', $this->lang->line('confirm_new_password')) : 'Confirm New Password' ?>" name="new_confirm">
                        </div>
                    </div>
                    <button type="submit" class="button button-success btn-5 submit_btn"><?= !empty($this->lang->line('save')) ? str_replace('\\', '', $this->lang->line('save')) : 'Save' ?></button>
                    <div class="d-flex justify-content-center mt-3">
                        <div class="form-group" id="error_box">
                        </div>
                    </div>
                </form>
                <!--end profile -->
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
    <!--end container-->
</section>
<?php
$footer_logo = get_settings('web_footer_logo');
?>
<!-- footer starts -->

<footer class="text-inverse mt-10">
    <section class="angled bg-navy pt-1 upper-end wrapper">
        <div class="container pb-4 pt-4">
            <div class="row gy-6 gy-lg-0">
                <div class="col-md-4 col-lg-3">
                    <div class="widget">
                        <div class="footer-logo-footer">
                            <?php if (ALLOW_MODIFICATION == 0) { ?>
                                <img src="<?= base_url("assets/front_end/modern/img/logo/orange.png") ?>" class="brand-logo-link logo-img" alt="site-logo image">
                            <?php } else { ?>

                                <a href="<?= base_url() ?>"><img src="<?= base_url(isset($footer_logo) ? $footer_logo : $web_logo) ?>" alt="footer-logo"></a>
                            <?php } ?>
                        </div>
                        <?php if (isset($web_settings['address']) && !empty($web_settings['address'])) { ?>
                            <div class="pe-xl-15 pe-xxl-17">
                                <div class="single-cta">
                                    <div class="cta-text">
                                        <p><?= output_escaping(str_replace('\r\n', '</br>', $web_settings['address'])) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php
                        if (isset($web_settings['copyright_details']) && !empty($web_settings['copyright_details'])) {
                        ?>
                            <p> <?= (isset($web_settings['copyright_details']) && !empty($web_settings['copyright_details'])) ? output_escaping(str_replace('\r\n', '&#13;&#10;', $web_settings['copyright_details'])) : " " ?> </p>
                        <?php } else { ?>
                            <p>Copyright &copy; <?= date('Y') ?> - <?= date('Y') + 1 ?>, All Right Reserved <a target="_blank" href="https://www.wrteam.in/">WRTeam</a></p>
                        <?php } ?>
                        <nav class="nav social social-white">
                            <?php if (isset($web_settings['twitter_link']) && !empty($web_settings['twitter_link'])) { ?>
                                <a href="<?= $web_settings['twitter_link'] ?>" target="_blank" aria-label="twitter-link" class="text-decoration-none"><i class="uil uil-twitter"></i></a>
                            <?php } ?>
                            <?php if (isset($web_settings['facebook_link']) &&  !empty($web_settings['facebook_link'])) { ?>
                                <a href="<?= $web_settings['facebook_link'] ?>" target="_blank" aria-label="facebook link" class="text-decoration-none"><i class="uil uil-facebook-f"></i></a>
                            <?php } ?>
                            <?php if (isset($web_settings['instagram_link']) &&  !empty($web_settings['instagram_link'])) { ?>
                                <a href="<?= $web_settings['instagram_link'] ?>" target="_blank" aria-label="instragram link" class="text-decoration-none"><i class="uil uil-instagram"></i></a>
                            <?php } ?>
                            <?php if (isset($web_settings['youtube_link']) &&  !empty($web_settings['youtube_link'])) { ?>
                                <a href="<?= $web_settings['youtube_link'] ?>" target="_blank" aria-label="youtube-link" class="text-decoration-none"><i class="uil uil-youtube"></i></a>
                            <?php } ?>
                        </nav>
                        <!-- /.social -->
                    </div>
                    <!-- /.widget -->
                </div>
                <!-- /column -->
                <div class="col-md-12 col-lg-3">
                    <div class="widget">
                        <?php if (isset($web_settings['support_number']) && !empty($web_settings['support_number'])) { ?>
                            <a href="tel:<?= $web_settings['support_number'] ?>">
                                <div class="single-cta">
                                    <div class="cta-text">
                                        <h4 class="widget-title text-white"><?= !empty($this->lang->line('call_us')) ? str_replace('\\', '', $this->lang->line('call_us')) : 'Call us' ?></h4>
                                        <p><?= $web_settings['support_number'] ?></p>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                        <?php if (isset($web_settings['support_email']) && !empty($web_settings['support_email'])) { ?>
                            <a href="mailto:<?= $web_settings['support_email'] ?>" class="text-decoration-none">
                                <div class="single-cta">
                                    <div class="cta-text">
                                        <h4 class="widget-title text-white"><?= !empty($this->lang->line('mail_us')) ? str_replace('\\', '', $this->lang->line('mail_us')) : 'Mail us' ?></h4>
                                        <p><?= $web_settings['support_email'] ?></p>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
                <!-- /column -->
                <div class="col-md-4 col-lg-3">
                    <div class="widget">
                        <h4 class="widget-title text-white mb-3"><?= !empty($this->lang->line('useful_links')) ? str_replace('\\', '', $this->lang->line('useful_links')) : 'Useful Links' ?></h4>
                        <ul class="list-unstyled  mb-0">
                            <li><a href="<?= base_url('seller/auth/sign_up') ?>" class="text-decoration-none hover"><?= !empty($this->lang->line('become_a_seller')) ? str_replace('\\', '', $this->lang->line('become_a_seller')) : 'Become a Seller' ?></a></li>
                            <li><a href="<?= base_url('affiliate/auth/sign_up') ?>" class="text-decoration-none hover"><?= !empty($this->lang->line('become_a_affiliate_user')) ? str_replace('\\', '', $this->lang->line('become_a_affiliate_user')) : 'Become a Affiliate User' ?></a></li>
                            <li><a href="<?= base_url('home/return-policy') ?>" class="text-decoration-none hover"><?= !empty($this->lang->line('return_policy')) ? str_replace('\\', '', $this->lang->line('return_policy')) : 'Return Policy' ?></a></li>
                            <li><a href="<?= base_url('home/shipping-policy') ?>" class="text-decoration-none hover"><?= !empty($this->lang->line('shipping_policy')) ? str_replace('\\', '', $this->lang->line('shipping_policy')) : 'Shipping Policy' ?></a></li>
                            <li><a href="<?= base_url('products') ?>" class="text-decoration-none hover"><?= !empty($this->lang->line('products')) ? str_replace('\\', '', $this->lang->line('products')) : 'Products' ?></a></li>
                            <li><a href="<?= base_url('home/terms-and-conditions') ?>" class="text-decoration-none hover"><?= !empty($this->lang->line('terms_and_condition')) ? str_replace('\\', '', $this->lang->line('terms_and_condition')) : 'Terms & Conditions' ?></a></li>
                            <li><a href="<?= base_url('home/privacy-policy') ?>" class="text-decoration-none hover"><?= !empty($this->lang->line('privacy_policy')) ? str_replace('\\', '', $this->lang->line('privacy_policy')) : 'Privacy Policy' ?></a></li>
                            <li><a href="<?= base_url('home/about-us') ?>" class="text-decoration-none hover"><?= !empty($this->lang->line('about_us')) ? str_replace('\\', '', $this->lang->line('about_us')) : 'About Us' ?></a></li>
                            <li><a href="<?= base_url('home/system-contact-us') ?>" class="text-decoration-none hover"><?= !empty($this->lang->line('contact_us')) ? str_replace('\\', '', $this->lang->line('contact_us')) : 'Contact Us' ?></a></li>
                        </ul>
                    </div>
                    <!-- /.widget -->
                </div>
                <!-- /column -->
                <div class="col-md-4 col-lg-3">
                    <div class="widget">
                        <!-- <div class="footer-widget"> -->
                        <div class="footer-widget-heading">
                            <h4 class="widget-title text-white mb-3"><?= !empty($this->lang->line('about_us')) ? str_replace('\\', '', $this->lang->line('about_us')) : 'About Us' ?></h4>
                        </div>
                        <div class="footer-text">
                            <?php if (isset($web_settings['app_short_description'])) { ?>
                                <p><?= output_escaping(str_replace('\r\n', '</br>', $web_settings['app_short_description'])) ?></p>

                            <?php } ?>
                        </div>
                        <!-- </div> -->
                    </div>
                    <!-- /.widget -->
                </div>
                <!-- /column -->
            </div>
            <!--/.row -->
        </div>
        <!-- /.container -->
    </section>
</footer>

<!-- footer ends -->
<?php if (ALLOW_MODIFICATION == 0) { ?>

    <!-- color switcher -->
    <div id="colors-switcher">
        <div>
            <h6><?= !empty($this->lang->line('pick_your_theme')) ? str_replace('\\', '', $this->lang->line('pick_your_theme')) : 'Pick Your Theme' ?></h6>
            <ul class="px-2 text-center">
                <li class="list-item-inline mb-3">
                    <a class="text-decoration-none text-dark" href="<?= base_url("themes/switch/modern") ?>">
                        <p class="m-0"><?= !empty($this->lang->line('modern_theme')) ? str_replace('\\', '', $this->lang->line('modern_theme')) : 'Modern Theme' ?></p>
                        <img class="lazy w-75" src="<?= base_url('media/image?path=assets/front_end/modern/preview-image/modern.png&width=120&quality=80') ?>" data-src="<?= base_url('media/image?path=assets/front_end/modern/preview-image/modern.png&width=120&quality=80') ?>" alt="Modern image" />


                    </a>
                </li>
                <li class="list-item-inline mb-3">
                    <a class="text-decoration-none text-dark" href="<?= base_url("themes/switch/classic") ?>">
                        <p class="m-0"><?= !empty($this->lang->line('classic_theme')) ? str_replace('\\', '', $this->lang->line('classic_theme')) : 'Classic Theme' ?></p>
                        <img class="lazy w-75" src="<?= base_url('media/image?path=assets/front_end/classic/preview-image/classic.png&width=120&quality=80') ?>" data-src="<?= base_url('media/image?path=assets/front_end/classic/preview-image/classic.jpg&width=120&quality=80') ?>" alt="Modern image" />

                    </a>
                </li>
            </ul>
        </div>

        <div>
            <h6><?= !empty($this->lang->line('pick_your_favorite_color')) ? str_replace('\\', '', $this->lang->line('pick_your_favorite_color')) : 'Pick Your Favorite Color' ?></h6>
            <ul class="color-style text-center mb-2">
                <li class="list-item-inline">
                    <a href="#" class="color-switcher orange" aria-label="orange-logo" data-url="<?= base_url("/assets/front_end/modern/css/colors/orange.css") ?>" data-image="<?= base_url("assets/front_end/modern/img/logo/orange.png") ?>"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="color-switcher blue" aria-label="blue-logo" data-url="<?= base_url("/assets/front_end/modern/css/colors/blue.css") ?>" data-image="<?= base_url("assets/front_end/modern/img/logo/dark-blue.png") ?>"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="color-switcher aqua" aria-label="aqua-logo" data-url="<?= base_url("/assets/front_end/modern/css/colors/aqua.css") ?>" data-image="<?= base_url("assets/front_end/modern/img/logo/aqua.png") ?>"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="color-switcher fuchsia" aria-label="fuchsia-logo" data-url="<?= base_url("/assets/front_end/modern/css/colors/fuchsia.css") ?>" data-image="<?= base_url("assets/front_end/modern/img/logo/fuchsia.png") ?>"></a>
                </li>

                <li class="list-item-inline">
                    <a href="#" class="color-switcher grape" aria-label="grape-logo" data-url="<?= base_url("/assets/front_end/modern/css/colors/grape.css") ?>" data-image="<?= base_url("assets/front_end/modern/img/logo/grape.png") ?>"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="color-switcher green" aria-label="green-logo" data-url="<?= base_url("/assets/front_end/modern/css/colors/green.css") ?>" data-image="<?= base_url("assets/front_end/modern/img/logo/green.png") ?>"></a>
                </li>

                <li class="list-item-inline">
                    <a href="#" class="color-switcher leaf" aria-label="leaf-logo" data-url="<?= base_url("/assets/front_end/modern/css/colors/leaf.css") ?>" data-image="<?= base_url("assets/front_end/modern/img/logo/leaf.png") ?>"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="color-switcher navy" aria-label="navy-logo" data-url="<?= base_url("/assets/front_end/modern/css/colors/navy.css") ?>" data-image="<?= base_url("assets/front_end/modern/img/logo/navy.png") ?>"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="color-switcher pink" aria-label="pink-logo" data-url="<?= base_url("/assets/front_end/modern/css/colors/pink.css") ?>" data-image="<?= base_url("assets/front_end/modern/img/logo/pink.png") ?>"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="color-switcher purple" aria-label="purple-logo" data-url="<?= base_url("/assets/front_end/modern/css/colors/purple.css") ?>" data-image="<?= base_url("assets/front_end/modern/img/logo/purple.png") ?>"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="color-switcher red" aria-label="red-logo" data-url="<?= base_url("/assets/front_end/modern/css/colors/red.css") ?>" data-image="<?= base_url("assets/front_end/modern/img/logo/red.png") ?>"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="color-switcher sky" aria-label="sky-logo" data-url="<?= base_url("/assets/front_end/modern/css/colors/sky.css") ?>" data-image="<?= base_url("assets/front_end/modern/img/logo/sky.png") ?>"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="color-switcher violet" aria-label="violet-logo" data-url="<?= base_url("/assets/front_end/modern/css/colors/violet.css") ?>" data-image="<?= base_url("assets/front_end/modern/img/logo/violet.png") ?>"></a>
                </li>

            </ul>
            <div class="color-bottom">
                <a href="#" aria-label="color-switcher" class="settings bg-white d-block"><i class="fa fa-cog fa-lg fa-spin setting-icon"></i></a>
            </div>
        </div>
    </div> <!-- end color switcher -->
<?php } ?>


<div class="modal fade" id="modal-signin" tabindex="-1" aria-hidden="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center">
            <div class="modal-body">
                <section id="login_div">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h2 class="mb-3 text-start"><?= !empty($this->lang->line('welcome_back')) ? str_replace('\\', '', $this->lang->line('welcome_back')) : 'Welcome Back' ?></h2>
                    <p class="lead mb-6 text-start"><?= !empty($this->lang->line('fill_your_mobile_and_password_to_sign_in')) ? str_replace('\\', '', $this->lang->line('fill_your_mobile_and_password_to_sign_in')) : 'Fill your mobile and password to sign in.' ?></p>
                    <form action="<?= base_url('home/login') ?>" class='form-submit-event' id="login_form" method="post">
                        <input type="hidden" class="form-control" name="type" value="phone">
                        <div class="form-floating mb-4">
                            <input type="tel" class="form-control" name="identity" placeholder="<?= !empty($this->lang->line('enter_mobile_number')) ? str_replace('\\', '', $this->lang->line('enter_mobile_number')) : 'Enter Mobile Number' ?>" id="loginEmail" value="<?= (ALLOW_MODIFICATION == 0) ? '1212121212' : '' ?>" pattern="^\d*$" inputmode="numeric">
                            <label for="loginEmail"><?= !empty($this->lang->line('enter_mobile_number')) ? str_replace('\\', '', $this->lang->line('enter_mobile_number')) : 'Enter Mobile Number' ?></label>
                        </div>
                        <div class="form-floating password-field mb-4">
                            <input type="password" class="form-control" name="password" placeholder="Password" id="loginPassword" value="<?= (ALLOW_MODIFICATION == 0) ? '12345678' : '' ?>">
                            <span class="password-toggle"><i class="uil uil-eye"></i></span>
                            <label for="loginPassword"><?= !empty($this->lang->line('password')) ? str_replace('\\', '', $this->lang->line('password')) : 'Password' ?></label>
                        </div>
                        <footer>
                            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-soft-dark btn-sm rounded-pill"><?= !empty($this->lang->line('cancel')) ? str_replace('\\', '', $this->lang->line('cancel')) : 'Cancel' ?></button>
                            <button type="submit" class="submit_btn btn btn-primary btn-sm rounded-pill"><?= !empty($this->lang->line('login')) ? str_replace('\\', '', $this->lang->line('login')) : 'Login' ?></button>
                        </footer>
                        <br>

                        <p class="mb-1">
                            <a href="<?= base_url() ?>" id="forgot_password_link" class="text-decoration-none text-blue fs-15 hover"><?= !empty($this->lang->line('forgot_password')) ? str_replace('\\', '', $this->lang->line('forgot_password')) : 'Forgot Password' ?> ?</a>
                        </p>
                        <p class="mb-0"><?= !empty($this->lang->line('dont_have_an_account')) ? str_replace('\\', '', $this->lang->line('dont_have_an_account')) : 'Don\'t have an account?' ?><a class="text-decoration-none text-blue fs-15 hover" href="#" data-bs-target="#modal-signup" data-bs-toggle="modal" data-bs-dismiss="modal" class="hover"><?= !empty($this->lang->line('Sign_up_here')) ? str_replace('\\', '', $this->lang->line('Sign_up_here')) : ' Sign up Here' ?></a></p>

                        <?php if ((!empty($settings['google_login']) && $settings['google_login'] == 1) || (!empty($settings['facebook_login']) && $settings['facebook_login'] == 1)) { ?>
                            <div class="divider-icon my-4">or</div>
                            <div class="row">
                                <div class="social-login col-md-12 text-center mt-3">
                                    <?php if (!empty($settings['google_login']) && ($settings['google_login'] == 1 || $settings['google_login'] == '1')) { ?>
                                        <a href="#" id="googleLogin" class="btn btn-circle btn-sm btn-google btn-red">
                                            <i class="uil uil-google"></i></a>
                                    <?php } ?>

                                </div>
                            </div>
                        <?php } ?>

                        <div class="d-flex justify-content-center">
                            <div class="form-group" id="error_box"></div>
                        </div>
                    </form>
                </section>
                <!-- login section complete -->


                <section class="hide pt-0" id="forgot_password_div">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center h5"><?= !empty($this->lang->line('forgot_password')) ? str_replace('\\', '', $this->lang->line('forgot_password')) : 'Forgot Password' ?></div>
                    <hr class="my-4">
                    <form id="send_forgot_password_otp_form" method="POST" action="#">
                        <div class="input-group mb-5">
                            <input type="hidden" name="forget_password_val" value="1" id="forget_password_val">
                            <input type="text" class="form-control" name="mobile_number" id="forgot_password_number" placeholder="<?= !empty($this->lang->line('mobile_number')) ? str_replace('\\', '', $this->lang->line('mobile_number')) : 'Mobile Number' ?>" value="">
                        </div>
                        <?php if ($auth_settings['authentication_method'] == 'firebase') { ?>
                            <div class="col-12 d-flex justify-content-center pb-4 mt-3">
                                <div id="recaptcha-container-2"></div>
                            </div>
                        <?php } ?>
                        <footer>
                            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-soft-dark btn-sm rounded-pill"><?= !empty($this->lang->line('cancel')) ? str_replace('\\', '', $this->lang->line('cancel')) : 'Cancel' ?></button>
                            <button type="submit" id="forgot_password_send_otp_btn" class="submit_btn btn btn-primary btn-sm rounded-pill forgot-send-otp-btn"><?= !empty($this->lang->line('send_otp')) ? str_replace('\\', '', $this->lang->line('send_otp')) : 'Send OTP' ?></button>
                        </footer>
                        <br>
                        <div class="d-flex justify-content-center">
                            <div class="form-group" id="forgot_pass_error_box"></div>
                        </div>
                    </form>
                    <form id="verify_forgot_password_otp_form" class="d-none" method="post" action="#">
                        <div class="input-group mb-3">
                            <input type="text" id="forgot_password_otp" class="form-control" name="otp" placeholder="<?= !empty($this->lang->line('otp')) ? str_replace('\\', '', $this->lang->line('otp')) : 'OTP' ?>" value="" autocomplete="off" required>
                        </div>


                        <div class="input-group mb-3 password-field">
                            <input type="password" class="form-control" name="new_password" id="passwordInput" placeholder="<?= !empty($this->lang->line('new_password')) ? str_replace('\\', '', $this->lang->line('new_password')) : 'New Password' ?>" value="" required>
                            <span class="password-toggle togglePassword"><i class="uil uil-eye"></i></span>
                        </div>
                        <footer>
                            <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal" aria-label="Close"><?= !empty($this->lang->line('cancel')) ? str_replace('\\', '', $this->lang->line('cancel')) : 'Cancel' ?></button>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill submit_btn" id="reset_password_submit_btn"><?= !empty($this->lang->line('submit')) ? str_replace('\\', '', $this->lang->line('submit')) : 'Submit' ?></button>
                        </footer>
                        <br>
                        <div class="d-flex justify-content-center">
                            <div class="form-group" id="set_password_error_box"></div>
                        </div>
                    </form>
                </section>
            </div>
            <!--/.modal-content -->
        </div>
        <!--/.modal-body -->
    </div>
    <!--/.modal-dialog -->
</div>
<!--/.modal -->


<div class="modal fade" id="modal-signup" tabindex="-1" aria-hidden="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h2 class="mb-3 text-start"><?= !empty($this->lang->line('Sign_up_here')) ? str_replace('\\', '', $this->lang->line('Sign_up_here')) : 'Sign up Here' ?></h2>
                <p class="lead mb-6 text-start"><?= !empty($this->lang->line('registration_takes_less_than_a_minute')) ? str_replace('\\', '', $this->lang->line('registration_takes_less_than_a_minute')) : 'Registration takes less than a minute.' ?></p>
                <section id="register_div">
                    <form id='send-otp-form' class='send-otp-form' action='#'>
                        <div class="row sign-up-verify-number">
                            <div class="col-12 d-flex justify-content-center pb-4">
                                <input type="text" class='form-input form-control' placeholder="<?= !empty($this->lang->line('enter_mobile_number')) ? str_replace('\\', '', $this->lang->line('enter_mobile_number')) : 'Enter Mobile Number' ?>" id="phone-number" required>
                            </div>
                            <div class="col-12 d-flex justify-content-center pb-4">
                                <div id="error-msg" class="hide text-danger"><?= !empty($this->lang->line('enter_valid_number')) ? str_replace('\\', '', $this->lang->line('enter_valid_number')) : 'Enter a valid number' ?></div>
                            </div>
                            <?php if ($auth_settings['authentication_method'] == 'firebase') { ?>
                                <div class="col-12 d-flex justify-content-center">
                                    <div id="recaptcha-container"></div>
                                </div>
                            <?php } ?>
                            <div class="col-12 d-flex justify-content-center pb-4">
                                <div id='is-user-exist-error' class='text-center p-3 text-danger'></div>
                            </div>

                        </div>
                        <footer>
                            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-soft-dark btn-sm rounded-pill"><?= !empty($this->lang->line('cancel')) ? str_replace('\\', '', $this->lang->line('cancel')) : 'Cancel' ?></button>
                            <button id='send-otp-button' class="btn btn-primary btn-sm rounded-pill"><?= !empty($this->lang->line('send_otp')) ? str_replace('\\', '', $this->lang->line('send_otp')) : 'Send OTP' ?></button>
                        </footer>
                        <p class="mb-0 mt-6"><?= !empty($this->lang->line('already_have_an_account')) ? str_replace('\\', '', $this->lang->line('already_have_an_account')) : 'Already have an account?' ?>
                            <a class="text-decoration-none text-blue fs-15 hover" href="#" data-bs-target="#modal-signin" data-bs-toggle="modal" data-bs-dismiss="modal" class="hover"><?= !empty($this->lang->line('sign_in')) ? str_replace('\\', '', $this->lang->line('sign_in')) : ' Sign In' ?></a>
                        </p>

                        <?php if ((!empty($settings['google_login']) && $settings['google_login'] == 1) || (!empty($settings['facebook_login']) && $settings['facebook_login'] == 1)) { ?>
                            <br>
                            <div class="divider-icon mt-0 mb-3">or</div>
                            <div class="row">
                                <div class="social-login col-md-12 text-center mt-3">
                                    <?php if (!empty($settings['google_login']) && ($settings['google_login'] == 1 || $settings['google_login'] == '1')) { ?>
                                        <a href="#" id="googleLogin" class="btn btn-circle btn-sm btn-google btn-red">
                                            <i class="uil uil-google"></i></a>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </form>
                    <form id='verify-otp-form' class='verify-otp-form d-none' action='<?= base_url('auth/register-user') ?>' method="POST">
                        <div class="row sign-up-verify-number">
                            <div class="col-12 d-flex justify-content-center pb-4">
                                <input type="hidden" class='form-input form-control' id="type" name="type" value="phone" autocomplete="off">
                            </div>
                            <div class="col-12 d-flex justify-content-center pb-4 form-floating">
                                <input type="text" class='form-input form-control' placeholder="<?= !empty($this->lang->line('otp')) ? str_replace('\\', '', $this->lang->line('otp')) : 'OTP' ?>" id="otp" name="otp" autocomplete="off">
                                <label for="otp"><?= !empty($this->lang->line('otp')) ? str_replace('\\', '', $this->lang->line('otp')) : 'OTP' ?></label>
                            </div>
                            <div class="col-12 d-flex justify-content-center pb-4 form-floating">
                                <input type="text" class='form-input form-control' placeholder="<?= !empty($this->lang->line('username')) ? str_replace('\\', '', $this->lang->line('username')) : 'Username' ?>" id="name" name="name">
                                <label for="name"><?= !empty($this->lang->line('username')) ? str_replace('\\', '', $this->lang->line('username')) : 'Username' ?></label>
                            </div>
                            <div class="col-12 d-flex justify-content-center pb-4 form-floating">
                                <input type="email" class='form-input form-control' placeholder="<?= !empty($this->lang->line('email')) ? str_replace('\\', '', $this->lang->line('email')) : 'Email' ?>" id="email" name="email">
                                <label for="email"><?= !empty($this->lang->line('email')) ? str_replace('\\', '', $this->lang->line('email')) : 'Email' ?></label>
                            </div>
                            <div class="col-12 d-flex justify-content-center pb-4 form-floating password-field">
                                <input type="password" class='form-input form-control' placeholder="<?= !empty($this->lang->line('password')) ? str_replace('\\', '', $this->lang->line('password')) : 'Password' ?>" id="password" name="password">
                                <span class="password-toggle d-flex"><i class="uil uil-eye mb-4 mr-2"></i></span>
                                <label for="password"><?= !empty($this->lang->line('password')) ? str_replace('\\', '', $this->lang->line('password')) : 'Password' ?></label>
                            </div>
                            <?php
                            if (!empty($settings['is_refer_earn_on']) && ($settings['is_refer_earn_on'] == 1 || $settings['is_refer_earn_on'] == '1')) {
                                $referal_code = substr(str_shuffle(str_repeat("AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz1234567890", 8)), 0, 8); ?>
                                <input type="hidden" class='form-input' name="referral_code" value=<?= $referal_code; ?>>
                            <?php } ?>
                            <div class="col-12 d-flex justify-content-center pb-4 form-floating ">
                                <input type="text" class='form-input form-control' placeholder="Friends code" id="friends_code" name="friends_code">
                                <label for="friends_code"><?= !empty($this->lang->line('friends_code')) ? str_replace('\\', '', $this->lang->line('friends_code')) : 'Friends code' ?></label>
                            </div>
                            <div class="col-12 d-flex justify-content-center pb-4">
                                <div id='registration-error' class='text-center p-3 text-danger'></div>
                            </div>
                        </div>
                        <footer>
                            <button data-bs-dismiss="modal" aria-label="Close" class="btn btn-soft-dark btn-sm rounded-pill"><?= !empty($this->lang->line('cancel')) ? str_replace('\\', '', $this->lang->line('cancel')) : 'Cancel' ?></button>
                            <button type="submit" id='register_submit_btn' class="btn btn-primary btn-sm rounded-pill"><?= !empty($this->lang->line('submit')) ? str_replace('\\', '', $this->lang->line('submit')) : 'Submit' ?></button>
                        </footer>
                    </form>
                    <form id='sign-up-form' class='sign-up-form collapse' action='#'>
                        <input type="text" placeholder="<?= !empty($this->lang->line('username')) ? str_replace('\\', '', $this->lang->line('username')) : 'Username' ?>" name='username' class='form-input form-control' required>
                        <input type="text" placeholder="email" name='<?= !empty($this->lang->line('email')) ? str_replace('\\', '', $this->lang->line('email')) : 'Email' ?>' class='form-input form-control' required>
                        <input type="password" placeholder="<?= !empty($this->lang->line('password')) ? str_replace('\\', '', $this->lang->line('password')) : 'Password' ?>" name='password' class='form-input form-control' required>
                        <div id='sign-up-error' class='text-center p-3'></div>
                        <footer>
                            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-soft-dark btn-sm rounded-pill"><?= !empty($this->lang->line('cancel')) ? str_replace('\\', '', $this->lang->line('cancel')) : 'Cancel' ?></button>
                            <button type='submit' class="btn btn-primary btn-sm rounded-pill"><?= !empty($this->lang->line('register')) ? str_replace('\\', '', $this->lang->line('register')) : 'Register' ?></button>
                        </footer>
                    </form>
                </section>
            </div>
            <!--/.modal-content -->
        </div>
        <!--/.modal-body -->
    </div>
    <!--/.modal-dialog -->
</div>
<!--/.modal -->

<!-- quick view -->
<div id="quick-view" data-iziModal-group="grupo3" class='product-page-content' style="display: none;">
    <button data-izimodal-close="" class="icon-close btn btn-circle bg-soft-primary m-3">
        <i class="fa fa-close fs-18 text-dark"></i>
    </button>
    <div class="row p-4">

        <!-- /.swiper-container -->
        <div class="col-12 col-sm-6 product-preview-image-section-md swiper-thumbs-container">
            <div class="swiper-container gallery-top overflow-hidden">
                <div class="swiper-wrapper-main swiper-wrapper"></div>
            </div>
            <div class="swiper-container gallery-thumbs overflow-hidden mt-10">
                <div class="swiper-wrapper-thumbs swiper-wrapper"></div>
            </div>
        </div>
        <!-- Mobile Product Image Slider -->
        <div class="col-12 col-sm-6 product-preview-image-section-sm">
            <div class="swiper-container mobile-image-swiper">
                <div class="mobile-swiper swiper-wrapper-mobile swiper-wrapper"></div>
            </div>
        </div>

        <div class="col-12 col-sm-6 product-page-details">
            <h3 class="my-3 product-title" id="modal-product-title"></h3>
            <div id="modal-product-sellers"></div>
            <div id="modal-product-statistics"></div>
            <div id="modal-product-brand" class="d-flex gap-1"></div>
            <p id="modal-product-short-description"></p>
            <p id="modal-product-total-stock"></p>
            <hr class="mb-2 mt-2">

            <input type="text" id="modal-product-rating" class="d-none" data-size="xs" value="0" data-show-clear="false" data-show-caption="false" readonly>
            (<span class="rating-status" id="modal-product-no-of-ratings">1203</span> <?= !empty($this->lang->line('reviews')) ? str_replace('\\', '', $this->lang->line('reviews')) : 'reviews' ?> )
            <!-- </div> -->
            <p class="mb-0 price">
                <span id="modal-product-price"></span>
                <sup>
                    <span class="striped-price text-danger" id="modal-product-special-price-div">
                        <s id="modal-product-special-price"></s>
                    </span>
                </sup>
            </p>
            <div id="modal-product-variant-attributes" class="overflow-auto"></div>
            <div id="modal-product-stock"></div>
            <div id="modal-product-variants-div"></div>
            <div class="num-block skin-2 py-2 pt-4 pb-4 mt-2">
                <div class="num-in form-control d-flex align-items-center">
                    <span class="minus dis"></span>
                    <input type="text" class="in-num" pattern="^\d+$" id="modal-product-quantity">
                    <span class="plus"></span>
                </div>
            </div>
            <div class="d-flex mb-3 mt-2 text-center text-md-left gap-2 overflow-auto">

                <button class="m-0 mt-1 btn btn-xs btn-yellow rounded-pill w-100" id="modal-add-to-cart-button">&nbsp;<i class="uil uil-shopping-bag fs-16"></i> <?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add To Cart' ?></button>

                <button class="m-0 buy_now mt-1 btn btn-xs btn-danger rounded-pill w-100 <?= ($this->ion_auth->logged_in()) ? '' : 'disabled' ?>" id="modal-buy-now-button"> &nbsp;<i class="uil uil-bolt fs-16"></i> <?= !empty($this->lang->line('buy_now')) ? str_replace('\\', '', $this->lang->line('buy_now')) : 'Buy Now' ?></button>

                <button type="button" name="compare" class="btn btn-xs btn-outline-blue rounded-pill h-9 m-0 mt-1 compare" id="compare"><i class="uil uil-exchange-alt fs-20"></i></button>

                <button class="btn btn-xs btn-outline-red rounded-pill h-9 m-0 add-fav mt-1" id="add_to_favorite_btn"><i class="fa fa-heart fs-20"></i></button>

            </div>

            <div class="mt-2">
                <span>
                    <div id="modal-product-tags"></div>
                </span>
            </div>
        </div>
    </div>
</div>

<?php if (isset($settings['whatsapp_number']) && !empty($settings['whatsapp_number'])) { ?>
    <div class="whatsapp-icon">
        <a href="https://api.whatsapp.com/send?phone=<?= $settings['whatsapp_number'] ?>&text&type=phone_number&app_absent=0" target="_blank" class="btn"><img src="<?= base_url('assets/logo/whatsapp_icon.png') ?>" alt="whatsapp"></a>
    </div>
<?php } ?>

<?php if (ALLOW_MODIFICATION == 0) { ?>
    <div class="buy-now-btn">
        <a href="https://codecanyon.net/item/eshop-web-multi-vendor-ecommerce-marketplace-cms/34380052" target="_blank" class="btn btn-danger btn-sm rounded-pill"> <i class="fa fa-shopping-cart"></i>&nbsp; <?= !empty($this->lang->line('buy_now')) ? str_replace('\\', '', $this->lang->line('buy_now')) : 'Buy Now' ?></a>
    </div>
<?php } ?>

<div class="fixed-icon">
    <?php if ($this->ion_auth->logged_in()) {
        $currentURL = current_url();
        if (strpos($currentURL, 'my-account/chat') === false) { ?>
            <div id="chat-button"><i class="uil uil-comments"></i></div>
            <!-- Floating chat iframe -->
            <iframe src="<?= base_url('my-account/floating_chat_modern') ?>" class="chat-iframe" id="chat-iframe"></iframe>
    <?php }
    } ?>
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>
</div>
<!-- end -->
<!-- main content ends -->
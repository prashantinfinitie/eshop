<?php $web_settings = get_settings('web_settings', true);
$system_settings = get_settings('system_settings', true); ?>
<!-- footer starts -->
<div class="footer-section-footer" id="dark-footer">
    <div class="main-content footer-con">
        <div class="footer-cta pt-5 pb-5">
            <div class="row">
                <?php if (isset($web_settings['address']) && !empty($web_settings['address'])) { ?>
                    <div class="col-xl-4 col-md-4 mb-30">
                        <div class="single-cta">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="cta-text">
                                <h3><?= !empty($this->lang->line('find_us')) ? str_replace('\\', '', $this->lang->line('find_us')) : 'Find us' ?></h3>
                                <p><?= output_escaping(str_replace('\r\n', '</br>', $web_settings['address'])) ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if (isset($web_settings['support_number']) && !empty($web_settings['support_number'])) { ?>
                    <div class="col-xl-4 col-md-4 mb-30">
                        <a href="tel:<?= $web_settings['support_number'] ?>">
                            <div class="single-cta">
                                <i class="fas fa-phone"></i>
                                <div class="cta-text">
                                    <h3><?= !empty($this->lang->line('call_us')) ? str_replace('\\', '', $this->lang->line('call_us')) : 'Call us' ?></h3>
                                    <p><?= $web_settings['support_number'] ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>

                <?php if (isset($web_settings['support_email']) && !empty($web_settings['support_email'])) { ?>
                    <div class="col-xl-4 col-md-4 mb-30">
                        <a href="mailto:<?= $web_settings['support_email'] ?>">
                            <div class="single-cta">
                                <i class="far fa-envelope-open"></i>
                                <div class="cta-text">
                                    <h3><?= !empty($this->lang->line('mail_us')) ? str_replace('\\', '', $this->lang->line('mail_us')) : 'Mail us' ?></h3>
                                    <p><?= $web_settings['support_email'] ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="footer-content-footer pt-5 pb-5">
            <div class="row">
                <div class="col-lg-3 mb-50">
                    <div class="footer-widget">
                        <div class="footer-logo-footer">
                            <?php
                            $logo = get_settings('web_logo');
                            $footer_logo = get_settings('web_footer_logo');  ?>
                            <a href="<?= base_url() ?>"><img src="<?= base_url(isset($footer_logo) ? $footer_logo : $logo) ?>" alt="footer-logo"></a>
                        </div>
                        <div class="footer-social-icon">
                            <h4><?= !empty($this->lang->line('follow_us')) ? str_replace('\\', '', $this->lang->line('follow_us')) : 'Follow us' ?></h4>
                            <?php if (isset($web_settings['facebook_link']) &&  !empty($web_settings['facebook_link'])) { ?>
                                <a href="<?= $web_settings['facebook_link'] ?>" target="_blank"><i class="fab fa-facebook-f rounded-icon clr-facebbok"></i></a>
                            <?php } ?>
                            <?php if (isset($web_settings['twitter_link']) && !empty($web_settings['twitter_link'])) { ?>
                                <a href="<?= $web_settings['twitter_link'] ?>" target="_blank"><i class="fab fa-twitter rounded-icon clr-twitter"></i></a>
                            <?php } ?>
                            <?php if (isset($web_settings['instagram_link']) &&  !empty($web_settings['instagram_link'])) { ?>
                                <a href="<?= $web_settings['instagram_link'] ?>" target="_blank"><i class="fab fa-instagram rounded-icon clr-insta"></i></a>
                            <?php } ?>
                            <?php if (isset($web_settings['youtube_link']) &&  !empty($web_settings['youtube_link'])) { ?>
                                <a href="<?= $web_settings['youtube_link'] ?>" target="_blank"><i class="fab fa-youtube rounded-icon clr-youtube"></i></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-6 mb-50">
                    <div class="footer-widget">
                        <div class="footer-widget-heading">
                            <h4><?= !empty($this->lang->line('about_us')) ? str_replace('\\', '', $this->lang->line('about_us')) : 'About Us' ?></h4>
                        </div>
                        <div class="footer-text">
                            <?php if (isset($web_settings['app_short_description'])) { ?>
                                <p><?= $web_settings['app_short_description'] ?></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-30">
                    <div class="footer-widget">
                        <div class="footer-widget-heading">
                            <h4><?= !empty($this->lang->line('useful_links')) ? str_replace('\\', '', $this->lang->line('useful_links')) : 'Useful Links' ?></h4>
                        </div>
                        <ul>
                            <li><a href="<?= base_url('seller/auth/sign_up') ?>"><?= !empty($this->lang->line('become_a_seller')) ? str_replace('\\', '', $this->lang->line('become_a_seller')) : 'Become a Seller' ?></a></li>
                            <li><a href="<?= base_url('affiliate/auth/sign_up') ?>"><?= !empty($this->lang->line('become_a_affiliate_user')) ? str_replace('\\', '', $this->lang->line('become_a_affiliate_user')) : 'Become a Affiliate User' ?></a></li>
                            <li><a href="<?= base_url('products') ?>"><?= !empty($this->lang->line('products')) ? str_replace('\\', '', $this->lang->line('products')) : 'Products' ?></a></li>
                            <li><a href="<?= base_url('home/categories') ?>"><?= !empty($this->lang->line('category')) ? str_replace('\\', '', $this->lang->line('category')) : 'Shop by Categories' ?></a></li>
                            <li><a href="<?= base_url('my-account') ?>"><?= !empty($this->lang->line('my_account')) ? str_replace('\\', '', $this->lang->line('my_account')) : 'My Account' ?></a></li>
                            <li><a href="<?= base_url('my-account/orders') ?>"><?= !empty($this->lang->line('my_orders')) ? str_replace('\\', '', $this->lang->line('my_orders')) : 'My Orders' ?></a></li>
                            <li><a href="<?= base_url('my-account/favorites') ?>"><?= !empty($this->lang->line('favorite')) ? str_replace('\\', '', $this->lang->line('favorite')) : 'Favorite' ?></a></li>
                            <li><a href="<?= base_url('home/terms-and-conditions') ?>"><?= !empty($this->lang->line('terms_and_condition')) ? str_replace('\\', '', $this->lang->line('terms_and_condition')) : 'Terms & Conditions' ?></a></li>
                            <li><a href="<?= base_url('home/privacy-policy') ?>"><?= !empty($this->lang->line('privacy_policy')) ? str_replace('\\', '', $this->lang->line('privacy_policy')) : 'Privacy Policy' ?></a></li>
                            <li><a href="<?= base_url('home/shipping-policy') ?>"><?= !empty($this->lang->line('shipping_policy')) ? str_replace('\\', '', $this->lang->line('shipping_policy')) : 'Shipping Policy' ?></a></li>
                            <li><a href="<?= base_url('home/return-policy') ?>"><?= !empty($this->lang->line('return_policy')) ? str_replace('\\', '', $this->lang->line('return_policy')) : 'Return Policy' ?></a></li>
                            <li><a href="<?= base_url('home/about-us') ?>"><?= !empty($this->lang->line('about_us')) ? str_replace('\\', '', $this->lang->line('about_us')) : 'About Us' ?></a></li>
                            <li><a href="<?= base_url('home/system-contact-us') ?>"><?= !empty($this->lang->line('contact_us')) ? str_replace('\\', '', $this->lang->line('contact_us')) : 'Contact Us' ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright-area">
        <div class="main-content footer-con">
            <div class="row">
                <div class="col-xl-6 col-lg-6 text-center text-lg-left">
                    <div class="copyright-text">
                        <?php $company_name = get_settings('web_settings', true);
                        if (isset($company_name['copyright_details']) && !empty($company_name['copyright_details'])) {
                        ?>
                            <p> <?= (isset($company_name['copyright_details']) && !empty($company_name['copyright_details'])) ? output_escaping(str_replace('\r\n', '&#13;&#10;', $company_name['copyright_details'])) : " " ?> </p>
                        <?php } else { ?>
                            <p>Copyright &copy; <?= date('Y') ?> - <?= date('Y') + 1 ?>, All Right Reserved <a target="_blank" href="https://www.wrteam.in/">WRTeam</a></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
                        <img src="<?= base_url("/assets/front_end/modern/preview-image/modern.png") ?>" alt="Modern image" class="w-75">

                    </a>
                </li>
                <li class="list-item-inline mb-3">
                    <a class="text-decoration-none text-dark" href="<?= base_url("themes/switch/classic") ?>">
                        <p class="m-0"><?= !empty($this->lang->line('classic_theme')) ? str_replace('\\', '', $this->lang->line('classic_theme')) : 'Classic Theme' ?></p>
                        <img src="<?= base_url("/assets/front_end/classic/preview-image/classic.jpg") ?>" alt="classic image" class="w-75">
                    </a>
                </li>
            </ul>
        </div>
        <div>
            <h6><?= !empty($this->lang->line('pick_your_favorite_color')) ? str_replace('\\', '', $this->lang->line('pick_your_favorite_color')) : 'Pick Your Favorite Color' ?></h6>
            <ul class="color-style text-center mb-2">
                <li class="list-item-inline">
                    <a href="#" class="peach"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="default"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="yellow"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="green"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="purple"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="red"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="dark-blue"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="orange"></a>
                </li>
                <li class="list-item-inline">
                    <a href="#" class="cyan-dark"></a>
                </li>
            </ul>
        </div>
        <div class="color-bottom">
            <a href="#" class="settings bg-white d-block"><i class="fa fa-cog fa-lg fa-spin setting-icon"></i></a>
        </div>
    </div> <!-- end color switcher -->
<?php } ?>

<div id="modal-custom" class="auth-modal" data-iziModal-group="group1"  style="display: none;">
    <button data-iziModal-close class="icon-close">x</button>
    <header>
        <a href="" id="login"><?= !empty($this->lang->line('login')) ? str_replace('\\', '', $this->lang->line('login')) : 'Login' ?></a>
        <a href="" id="register" class="active"><?= !empty($this->lang->line('register')) ? str_replace('\\', '', $this->lang->line('register')) : 'Register' ?></a>
    </header>
    <section class="hide" id="login_div">
        <form action="<?= base_url('home/login') ?>" class='form-submit-event' id="login_form" method="post">
            <input type="hidden" class="form-control" name="type" value="phone">
            <div class="input-group">
                <input type="text" class="form-control" name="identity" placeholder="<?= !empty($this->lang->line('enter_mobile_number')) ? str_replace('\\', '', $this->lang->line('enter_mobile_number')) : 'Enter Mobile Number' ?>" value="<?= (ALLOW_MODIFICATION == 0) ? '1212121212' : '' ?>" required>
            </div>
            <div class="input-group">
                <input type="password" id="loginPassword" class="form-control" name="password" placeholder="Password" value="<?= (ALLOW_MODIFICATION == 0) ? '12345678' : '' ?>" required>
                <span class="password-toggle"><i id="passwordVisible" class="far fa-eye"></i></span>
            </div>


            <div class="mb-4 text-right">
                <a href="<?= base_url() ?>" id="forgot_password_link"><?= !empty($this->lang->line('forgot_password')) ? str_replace('\\', '', $this->lang->line('forgot_password')) : 'Forgot Password' ?> ?</a>
            </div>
            <footer>
                <button type="button" data-iziModal-close><?= !empty($this->lang->line('cancel')) ? str_replace('\\', '', $this->lang->line('cancel')) : 'Cancel' ?></button>
                <button type="submit" class="submit_btn  btn btn-primary btn-block"><?= !empty($this->lang->line('login')) ? str_replace('\\', '', $this->lang->line('login')) : 'Login' ?></button>
            </footer>
            <br>
            <?php if ((!empty($system_settings['google_login']) && $system_settings['google_login'] == 1) || (!empty($system_settings['facebook_login']) && $system_settings['facebook_login'] == 1)) { ?>
                <div class="separator">OR</div>
                <div class="row">
                    <div class="social-login col-md-12 text-center mt-3">
                        <?php if (!empty($system_settings['google_login']) && ($system_settings['google_login'] == 1 || $system_settings['google_login'] == '1')) { ?>
                            <a href="#" id="googleLogin" class="btn google-btn">
                                <img src="<?= base_url('assets/front_end/classic/images/google_logo.png') ?>" class="mr-2 social_logo">Google</a>
                        <?php } ?>
                        <?php if (!empty($system_settings['facebook_login']) && ($system_settings['facebook_login'] == 1 || $system_settings['facebook_login'] == '1')) { ?>
                            <a href="#" id="facebookLogin" class="btn facebook_btn ml-2">
                                <img src="<?= base_url('assets/front_end/classic/images/facebook_logo.png') ?>" class="mr-2 social_logo">
                                Facebook</a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            <div class="d-flex justify-content-center mt-2">
                <div class="form-group" id="error_box"></div>
            </div>
        </form>
    </section>
    <section id="register_div">
        <form id='send-otp-form' class='send-otp-form' action='#'>
            <div class="row sign-up-verify-number">
                <div class="col-12 d-flex justify-content-center pb-4">
                    <input type="text" maxlength="16" class='form-input' placeholder="Enter Mobile Number" id="phone-number" required>
                </div>
                <div class="col-12 d-flex justify-content-center pb-4">
                    <div id="error-msg" class="hide text-danger"><?= !empty($this->lang->line('enter_valid_number')) ? str_replace('\\', '', $this->lang->line('enter_valid_number')) : 'Enter a valid number' ?></div>
                </div>
                <div class="col-12 d-flex justify-content-center pb-4">
                    <div id="recaptcha-container"></div>
                </div>
                <div class="col-12 d-flex justify-content-center pb-4">
                    <div id='is-user-exist-error' class='text-center p-3 text-danger'></div>
                </div>
            </div>
            <footer>
                <button type="button" data-iziModal-close><?= !empty($this->lang->line('cancel')) ? str_replace('\\', '', $this->lang->line('cancel')) : 'Cancel' ?></button>
                <button id='send-otp-button'><?= !empty($this->lang->line('send_otp')) ? str_replace('\\', '', $this->lang->line('send_otp')) : 'Send OTP' ?></button>
            </footer>
            <?php if ((!empty($system_settings['google_login']) && $system_settings['google_login'] == 1) || (!empty($system_settings['facebook_login']) && $system_settings['facebook_login'] == 1)) { ?>
                <br>
                <div class="separator">OR</div>
                <div class="row">
                    <div class="social-login col-md-12 text-center mt-3">
                        <?php if (!empty($system_settings['google_login']) && ($system_settings['google_login'] == 1 || $system_settings['google_login'] == '1')) { ?>
                            <a href="#" id="googleLogin" class="btn google-btn">
                                <img src="<?= base_url('assets/front_end/classic/images/google_logo.png') ?>" class="mr-2 social_logo">Google</a>
                        <?php  } ?>
                        <?php if (!empty($system_settings['facebook_login']) && ($system_settings['facebook_login'] == 1 || $system_settings['facebook_login'] == '1')) { ?>
                            <a href="#" id="facebookLogin" class="btn facebook_btn ml-2">
                                <img src="<?= base_url('assets/front_end/classic/images/facebook_logo.png') ?>" class="mr-2 social_logo">
                                Facebook</a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </form>
        <form id='verify-otp-form' class='verify-otp-form d-none' action='<?= base_url('auth/register-user') ?>' method="POST">
            <div class="row sign-up-verify-number">
                <div class="col-12 d-flex justify-content-center pb-4">
                    <input type="hidden" class='form-input' id="type" name="type" value="phone" autocomplete="off">
                </div>
                <div class="col-12 d-flex justify-content-center pb-4">
                    <input type="text" class='form-input' placeholder="<?= !empty($this->lang->line('otp')) ? str_replace('\\', '', $this->lang->line('otp')) : 'OTP' ?>" id="otp" name="otp" autocomplete="off">
                </div>
                <div class="col-12 d-flex justify-content-center pb-4">
                    <input type="text" class='form-input' placeholder="<?= !empty($this->lang->line('username')) ? str_replace('\\', '', $this->lang->line('username')) : 'Username' ?>" id="name" name="name">
                </div>
                <div class="col-12 d-flex justify-content-center pb-4">
                    <input type="email" class='form-input' placeholder="<?= !empty($this->lang->line('email')) ? str_replace('\\', '', $this->lang->line('email')) : 'Email' ?>" id="email" name="email">
                </div>
                <div class="col-12 d-flex justify-content-center pb-4">
                    <input type="password" class='form-input' placeholder="<?= !empty($this->lang->line('password')) ? str_replace('\\', '', $this->lang->line('password')) : 'Password' ?>" id="password" name="password">
                    <span class="register-password-toggle"><i id="registerPasswordVisible" class="far fa-eye"></i></span>
                </div>
                <?php $referal_code = substr(str_shuffle(str_repeat("AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz1234567890", 8)), 0, 8);
                ?> <input type="hidden" class='form-input' name="referral_code" value=<?= $referal_code; ?>>
                <div class="col-12 d-flex justify-content-center pb-4">
                    <input type="text" class='form-input' placeholder="<?= !empty($this->lang->line('friends_code')) ? str_replace('\\', '', $this->lang->line('friends_code')) : 'Friends code' ?>" id="friends_code" name="friends_code">
                </div>
                <div class="col-12 d-flex justify-content-center pb-4">
                    <div id='registration-error' class='text-center p-3 text-danger'></div>
                </div>
            </div>
            <footer>
                <button data-iziModal-close><?= !empty($this->lang->line('cancel')) ? str_replace('\\', '', $this->lang->line('cancel')) : 'Cancel' ?></button>
                <button type="submit" id='register_submit_btn'><?= !empty($this->lang->line('submit')) ? str_replace('\\', '', $this->lang->line('submit')) : 'Submit' ?></button>
            </footer>
        </form>
        <form id='sign-up-form' class='sign-up-form collapse' action='#'>
            <input type="text" placeholder="<?= !empty($this->lang->line('username')) ? str_replace('\\', '', $this->lang->line('username')) : 'Username' ?>" name='username' class='form-input' required>
            <input type="text" placeholder="<?= !empty($this->lang->line('email')) ? str_replace('\\', '', $this->lang->line('email')) : 'Email' ?>" name='email' class='form-input' required>
            <input type="password" placeholder="<?= !empty($this->lang->line('password')) ? str_replace('\\', '', $this->lang->line('password')) : 'Password' ?>" name='password' class='form-input' required>
            <div id='sign-up-error' class='text-center p-3'></div>
            <footer>
                <button type="button" data-iziModal-close><?= !empty($this->lang->line('cancel')) ? str_replace('\\', '', $this->lang->line('cancel')) : 'Cancel' ?></button>
                <button type='submit'><?= !empty($this->lang->line('register')) ? str_replace('\\', '', $this->lang->line('register')) : 'Register' ?></button>
            </footer>
        </form>
    </section>
    <section class="hide pt-0" id="forgot_password_div">
        <div class="text-center h5"><?= !empty($this->lang->line('forgot_password')) ? str_replace('\\', '', $this->lang->line('forgot_password')) : 'Forgot Password' ?></div>
        <hr class="mt-0">
        <form id="send_forgot_password_otp_form" method="POST" action="#">
            <input type="hidden" name="forget_password_val" value="1" id="forget_password_val">
            <div class="input-group">
                <input type="text" class="form-control" name="mobile_number" id="forgot_password_number" placeholder="<?= !empty($this->lang->line('enter_mobile_number')) ? str_replace('\\', '', $this->lang->line('enter_mobile_number')) : 'Enter Mobile Number' ?>" value="">
            </div>
            <div class="col-12 d-flex justify-content-center pb-4 mt-3">
                <div id="recaptcha-container-2"></div>
            </div>
            <footer>
                <button type="button" data-iziModal-close><?= !empty($this->lang->line('cancel')) ? str_replace('\\', '', $this->lang->line('cancel')) : 'Cancel' ?></button>
                <button type="submit" id="forgot_password_send_otp_btn" class="submit_btn  btn btn-primary btn-block forgot-send-otp-btn"><?= !empty($this->lang->line('send_otp')) ? str_replace('\\', '', $this->lang->line('send_otp')) : 'Send OTP' ?></button>
            </footer>
            <br>
            <div class="d-flex justify-content-center mt-2">
                <div class="form-group" id="forgot_pass_error_box"></div>
            </div>
        </form>
        <form id="verify_forgot_password_otp_form" class="d-none" method="post" action="#">
            <div class="input-group">
                <input type="text" id="forgot_password_otp" class="form-control" name="otp" placeholder="<?= !empty($this->lang->line('otp')) ? str_replace('\\', '', $this->lang->line('otp')) : 'OTP' ?>" value="" autocomplete="off" required>
            </div>
            <div class="input-group">
                <input type="password" class="form-control" name="new_password" placeholder="<?= !empty($this->lang->line('new_password')) ? str_replace('\\', '', $this->lang->line('new_password')) : 'New Password' ?>" value="" required>
            </div>
            <footer>
                <button type="button" data-iziModal-close><?= !empty($this->lang->line('cancel')) ? str_replace('\\', '', $this->lang->line('cancel')) : 'Cancel' ?></button>
                <button type="submit" class="submit_btn  btn btn-primary btn-block" id="reset_password_submit_btn"><?= !empty($this->lang->line('submit')) ? str_replace('\\', '', $this->lang->line('submit')) : 'Submit' ?></button>
            </footer>
            <br>
            <div class="d-flex justify-content-center mt-2">
                <div class="form-group" id="set_password_error_box"></div>
            </div>
        </form>
    </section>
</div>
<!-- quick view -->
<div id="quick-view" data-iziModal-group="grupo3" class='product-page-content' style="display: none;">
    <button data-iziModal-close class="icon-close">x</button>
    <div class="row p-4">
        <div class="col-12 col-sm-6 product-preview-image-section-md">
            <div class="swiper-container gallery-top">
                <div class="swiper-wrapper"></div>
                <!-- Add Arrows -->
                <div class="swiper-button-next swiper-button-black"></div>
                <div class="swiper-button-prev swiper-button-black"></div>
            </div>
            <div class="swiper-container gallery-thumbs">
                <div class="swiper-wrapper" id="gal1"></div>
            </div>
        </div>
        <!-- Mobile Product Image Slider -->
        <div class="col-12 col-sm-6 product-preview-image-section-sm">
            <div class=" swiper-container mobile-image-swiper">
                <div class="swiper-wrapper"></div>
                <div class="swiper-pagination mobile-image-swiper-pagination text-center"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 product-page-details">
            <h3 class="my-3 product-title" id="modal-product-title"></h3>
            <div id="modal-product-sellers">
            </div>
            <hr>
            <p id="modal-product-short-description"></p>
            <p id="modal-product-total-stock"></p>
            <hr class="mb-0">
            <div class="mb-3 product-rating-small">
                <input type="text" class="kv-fa rating rating-loading" id="modal-product-rating" value="0" data-show-caption="false" data-size="sm" data-show-clear="false" title="" readonly>
                (<span class="rating-status" id="modal-product-no-of-ratings">1203</span> <?= !empty($this->lang->line('reviews')) ? str_replace('\\', '', $this->lang->line('reviews')) : 'reviews' ?> )
            </div>
            <p class="mb-0 price">
                <span id="modal-product-price"></span>
                <sup>
                    <span class="striped-price text-danger" id="modal-product-special-price-div">
                        <s id="modal-product-special-price"></s>
                    </span>
                </sup>
            </p>
            <div id="modal-product-variant-attributes"></div>
            <div id="modal-product-variants-div"></div>
            <div class="num-block skin-2 py-2 pt-4 pb-4">
                <div class="num-in">
                    <span class="minus dis"></span>
                    <input type="text" class="in-num" id="modal-product-quantity">
                    <span class="plus"></span>
                </div>
            </div>
            <div class="pb-2 text-center text-md-left">
                <div class="button button-sm m-0 p-0">
                    <button class="buttons btn-6-3 extra-small m-0 mt-1" id="modal-add-to-cart-button"><i class="fas fa-cart-plus"></i> <?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add To Cart' ?></button>
                </div>
                <div class="button button-sm m-0 p-0">
                    <button class="buy_now buttons btn-6-2 extra-small m-0 mt-1 <?= ($this->ion_auth->logged_in()) ? '' : 'disabled' ?>" id="modal-buy-now-button"> &nbsp;<i class="fas fa-bolt"></i> <?= !empty($this->lang->line('buy_now')) ? str_replace('\\', '', $this->lang->line('buy_now')) : 'Buy Now' ?></button>
                </div>

                <div class="button button-sm m-0 p-0">
                    <button type="button" name="compare" class="buttons btn-6-6 extra-small m-0 mt-1 compare" id="compare"><i class="fa fa-random"></i> <?= !empty($this->lang->line('compare')) ? str_replace('\\', '', $this->lang->line('compare')) : 'Compare' ?></button>
                </div>
                <div class="button button-sm m-0 p-0">
                    <button class="buttons btn-6-1 extra-small m-0 add-fav mt-1" id="add_to_favorite_btn"><i class="far fa-heart"></i> <span><?= !empty($this->lang->line('add_to_favorite')) ? str_replace('\\', '', $this->lang->line('add_to_favorite')) : 'Add to Favorite' ?></span></button>
                </div>
            </div>

            <div class="mt-2">
                <span>
                    <div id="modal-product-tags"></div>
                </span>
            </div>
        </div>
    </div>
</div>

<?php if (isset($system_settings['whatsapp_number']) && !empty($system_settings['whatsapp_number'])) { ?>
    <div class="whatsapp-icon">
        <a href="https://api.whatsapp.com/send?phone=<?= $settings['whatsapp_number'] ?>&text&type=phone_number&app_absent=0" target="_blank" class="btn"><img src="<?= base_url('media/image?path=assets/logo/whatsapp_icon.png&width=40&quality=80') ?>" alt="whatsapp"></a>
    </div>
<?php } ?>

<?php if (ALLOW_MODIFICATION == 0) { ?>
    <div class="buy-now-btn">
        <a href="https://codecanyon.net/item/eshop-web-multi-vendor-ecommerce-marketplace-cms/34380052" target="_blank" class="btn btn-danger"> <i class="fa fa-shopping-cart"></i> <?= !empty($this->lang->line('buy_now')) ? str_replace('\\', '', $this->lang->line('buy_now')) : 'Buy Now' ?></a>
    </div>
    <?php }

if ($this->ion_auth->logged_in()) {
    $currentURL = current_url();
    if (strpos($currentURL, 'my-account/chat') === false) { ?>
        <div id="chat-button"><i class="far fa-comments"></i></div>
        <!-- Floating chat iframe -->
        <iframe src="<?= base_url('my-account/floating_chat_classic') ?>" id="chat-iframe" class="chat_icon_style"></iframe>
<?php }
} ?>

<!-- end -->
<!-- main content ends -->
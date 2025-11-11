<!-- breadcrumb -->
<section class="breadcrumb-title-bar colored-breadcrumb deeplink_wrapper">
    <div class="main-content responsive-breadcrumb">
        <h2><?= !empty($this->lang->line('refer_and_earn')) ? str_replace('\\', '', $this->lang->line('refer_and_earn')) : 'Refer and Earn' ?></h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('my-account') ?>"><?= !empty($this->lang->line('dashboard')) ? str_replace('\\', '', $this->lang->line('dashboard')) : 'Dashboard' ?></a></li>
                <li class="breadcrumb-item"><?= !empty($this->lang->line('refer_and_earn')) ? str_replace('\\', '', $this->lang->line('refer_and_earn')) : 'Refer and Earn' ?></li>
            </ol>
        </nav>
    </div>

</section>
<!-- end breadcrumb -->

<!-- <main class="deeplink_wrapper"> -->
    <section class="my-account-section py-5">
        <div class="row main-content">
            <div class="col-md-4 myaccount-navigation py-3">
                <?php $this->load->view('front-end/' . THEME . '/pages/my-account-sidebar') ?>
            </div>
            <div class="col-md-8 padding-16-30">
                <h3><?= label('refer_and_earn', 'Refer and Earn') ?></h3>
                <div class="text-center mt-4">
                    <div class="refer-img-box">
                        <img src="<?= base_url('assets/front_end/classic/images/referral.png') ?>" alt="">
                    </div>
                    <h4 class="fw-semibold">Your Referral Code</h4>
                    <div class=" row col-12 d-flex justify-content-center">
                        <div class="col-md-4 border refer_and_earn_border" id="text-to-copy">
                            <?php
                            if ((!empty($settings['is_refer_earn_on']) && ($settings['is_refer_earn_on'] == 1 || $settings['is_refer_earn_on'] == '1'))) {

                                $referral_code = fetch_details('users', ['id' => $_SESSION['user_id']], 'referral_code');
                                if (empty($referral_code[0]['referral_code']) && $referral_code[0]['referral_code'] == '') {
                                    $referral_generate_code = substr(str_shuffle(str_repeat("AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz1234567890", 8)), 0, 8);
                                    update_details(['referral_code' => $referral_generate_code], ['id' => $_SESSION['user_id']], 'users');
                                }
                            }
                            ?>
                            <h2 class="mt-2"><?= $referral_code[0]['referral_code']; ?></h2>
                        </div>
                    </div>
                    <button class="my-2 btn btn-primary btn-sm copy-button" onclick="copyText()"><?= !empty($this->lang->line('tap_to_copy')) ? str_replace('\\', '', $this->lang->line('tap_to_copy')) : 'Tap to copy' ?></button>
                    <h6 class="text-body-secondary mt-2"><?= !empty($this->lang->line('invite_your_friends_to_join_and_get_the_reward_as_soon')) ? str_replace('\\', '', $this->lang->line('invite_your_friends_to_join_and_get_the_reward_as_soon')) : 'Invite your friends to join and get the reward as soon as your friend places his first order' ?></h6>
                </div>
            </div>
        </div>
    </section>
<!-- </main> -->
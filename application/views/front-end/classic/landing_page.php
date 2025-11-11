<?php
$settings = get_settings('system_settings', true);

?>

<section class='error_404'>
    <div class="deeplink_wrapper">

        <div class="align-items-center d-flex flex-column" style="margin-top: 25%">
            <h1 data-shadow='oops!'>OPEN IN APP</h1>
            <img src="<?= base_url('assets/open_in_app_2.jpg') ?>" alt="open_in_app">
        </div>
        <input type="hidden" name="android_app_store_link" id="android_app_store_link" value="<?= (isset($settings['android_app_store_link']) && !empty($settings['android_app_store_link'])) ? $settings['android_app_store_link'] : '' ?>">
        <input type="hidden" name="ios_app_store_link" id="ios_app_store_link" value="<?= (isset($settings['ios_app_store_link']) && !empty($settings['ios_app_store_link'])) ? $settings['ios_app_store_link'] : '' ?>">
        <input type="hidden" name="scheme" id="scheme" value="<?= (isset($settings['scheme']) && !empty($settings['scheme'])) ? $settings['scheme'] : '' ?>">
        <input type="hidden" name="host" id="host" value="<?= (isset($settings['host']) && !empty($settings['host'])) ? $settings['host'] : '' ?>">
        <input type="hidden" name="share_slug" id="share_slug" value="true">
        <link rel="stylesheet" href="<?= THEME_ASSETS_URL . 'css/bootstrap.min.css' ?>">

        <script src="<?= THEME_ASSETS_URL . 'js/jquery.min.js' ?>"></script>
        <script src="<?= THEME_ASSETS_URL . 'js/deeplink.js' ?>"></script>
    </div>
</section>
<style>
    .bottom-sheet {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: #fff;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        transform: translateY(100%);
        transition: transform 0.3s ease-out;
        z-index: 1050;
    }

    .bottom-sheet.show {
        transform: translateY(0);
    }
</style>
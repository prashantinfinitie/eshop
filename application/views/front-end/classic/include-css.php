<!-- Izimodal -->
<link rel="stylesheet" href="<?= THEME_ASSETS_URL . 'css/iziModal.min.css' ?>" />
<!-- Favicon -->
<?php $favicon = get_settings('web_favicon');

$path = ($is_rtl == 1) ? 'rtl/' : "";
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= THEME_ASSETS_URL . 'css/eshop-bundle.css' ?>" />
<link rel="stylesheet" href="<?= THEME_ASSETS_URL . 'css/' . $path . 'eshop-bundle-main.css' ?>">
<link rel="icon" href="<?= base_url($favicon) ?>" type="image/gif" sizes="16x16">

<!-- Color CSS -->
<link rel="stylesheet" href="<?= THEME_ASSETS_URL . 'css/colors/peach.css' ?>" id="color-switcher">
<!-- jssocials -->
<link rel="stylesheet" href="<?= THEME_ASSETS_URL . 'css/jquery.jssocials-theme-flat.css' ?>">
<link rel="stylesheet" href="<?= THEME_ASSETS_URL . 'css/jquery.jssocials.css' ?>">

<!-- Jquery -->
<script src="<?= THEME_ASSETS_URL . 'js/jquery.min.js' ?>"></script>
<script src="<?= THEME_ASSETS_URL . 'js/eshop-bundle-top-js.js' ?>"></script>
<script type="text/javascript">
    base_url = "<?= base_url() ?>";
    currency = "<?= $settings['currency'] ?>";
    csrfName = "<?= $this->security->get_csrf_token_name() ?>";
    csrfHash = "<?= $this->security->get_csrf_hash() ?>";
</script>


<!-- <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet"> -->

<!-- Favicon -->
<?php $favicon = get_settings('web_favicon');

$path = ($is_rtl == 1) ? 'rtl/' : "";
?>
<link rel="icon" href="<?= base_url($favicon) ?>" type="image/gif" sizes="16x16">
<link rel="stylesheet" href="<?= THEME_ASSETS_URL . 'css/eshop-bundle.css' ?>" />
<!-- Bootstrap -->
<link rel="stylesheet" href="<?= THEME_ASSETS_URL . 'css/' . $path . 'bootstrap.min.css' ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- Star rating CSS -->
<link rel="stylesheet" href="<?= THEME_ASSETS_URL . 'css/style.css' ?>">
<link rel="stylesheet" href="<?= THEME_ASSETS_URL . 'css/'. $path .'eshop-bundle-main.css' ?>">

<!-- chat -->
<link rel="stylesheet" href="<?= THEME_ASSETS_URL . 'css/components.css' ?>">
<?php if (ALLOW_MODIFICATION == 0) { ?>
    <link rel="stylesheet" href="<?= THEME_ASSETS_URL . 'css/colors/orange.css' ?>" id="color-switcher">
<?php } else { ?>
    <?php
    $settings = get_settings('web_settings', true);
    $modern_theme_color = (isset($settings['modern_theme_color']) && !empty($settings['modern_theme_color'])) ? $settings['modern_theme_color'] : 'orange'; ?>
    <link rel="stylesheet" href="<?= THEME_ASSETS_URL . 'css/colors/' . $modern_theme_color . '.css' ?>">

<?php } ?>


<!-- Jquery -->
<script src="<?= THEME_ASSETS_URL . 'js/jquery.min.js' ?>"></script>
<!-- Date Range Picker -->
<script src="<?= THEME_ASSETS_URL . 'js/moment.min.js' ?>"></script>
<script src="<?= THEME_ASSETS_URL . 'js/daterangepicker.js' ?>"></script>
<script type="text/javascript">
    base_url = "<?= base_url() ?>";
    currency = "<?= isset($settings['currency'])? $settings['currency'] : '$' ?>";
    csrfName = "<?= $this->security->get_csrf_token_name() ?>";
    csrfHash = "<?= $this->security->get_csrf_hash() ?>";
</script>
<style>
    @import url("https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700");
</style>


<!DOCTYPE html>
<html>
<?php $this->load->view('admin/include-head.php'); ?>
<?php 
$auth_settings = get_settings('authentication_settings', true);
?>
<body class="hold-transition login-page bg-admin">
    <img src="<?= base_url('assets/admin/images/eshop_img.jpg') ?>" class="h-100 w-100">
    <div class="overlay"></div>
    <input type="hidden" id="auth_settings" name="auth_settings" value='<?= isset($auth_settings['authentication_method']) ? $auth_settings['authentication_method'] : ''; ?>'>

    <?php $this->load->view('admin/pages/' . $main_page); ?>
    <!-- Footer -->
    <?php $this->load->view('admin/include-script.php'); ?>
</body>

</html>
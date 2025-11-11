<!DOCTYPE html>
<html>
<?php $this->load->view('affiliate/include-head.php'); ?>

<body class="hold-transition login-page  bg-admin">
    <img src="<?= base_url('assets/admin/images/eshop_img.jpg') ?>" class="h-100 w-100">
    <div class="overlay"></div>
	<?php $this->load->view('affiliate/pages/' . $main_page); ?>
	<!-- Footer -->
	<?php $this->load->view('affiliate/include-script.php'); ?>
</body>

</html>
<!DOCTYPE html>
<html>
<?php $this->load->view('shipping_company/include-head.php'); ?>
<div id="loading">
    <div class="lds-ring">
        <div></div>
    </div>
</div>

<body class="hold-transition sidebar-mini layout-fixed ">
    <div class=" wrapper ">
        <?php $this->load->view('shipping_company/include-navbar.php') ?>
        <?php $this->load->view('shipping_company/include-sidebar.php'); ?>
        <?php $this->load->view('shipping_company/pages/' . $main_page); ?>
        <?php $this->load->view('shipping_company/include-footer.php'); ?>
    </div>
    <?php $this->load->view('shipping_company/include-script.php'); ?>
</body>

</html>

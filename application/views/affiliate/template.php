<!DOCTYPE html>
<html>
<?php $this->load->view('affiliate/include-head.php'); ?>
<div id="loading">
    <div class="lds-ring">
        <div></div>
    </div>
</div>
<body class="hold-transition sidebar-mini layout-fixed ">
    <div class=" wrapper ">
        <?php $this->load->view('affiliate/include-navbar.php') ?>
        <?php $this->load->view('affiliate/include-sidebar.php'); ?>
        <?php $this->load->view('affiliate/pages/' . $main_page); ?>
        <?php $this->load->view('affiliate/include-footer.php'); ?>
    </div>
    <?php $this->load->view('affiliate/include-script.php'); ?>
</body>

</html>
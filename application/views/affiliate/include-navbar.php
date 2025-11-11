<?php $current_version = get_current_version();
?>
<nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item my-auto">
            <span class="badge badge-success">v <?= (isset($current_version) && !empty($current_version)) ? $current_version : '1.0' ?></span>
        </li>
        <?php
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
        ?>
            <li class="nav-item my-auto ml-2">
                <span class="badge badge-danger">Demo mode</span>
            </li>
        <?php } ?>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <!-- google translate  -->
        <div id="google_translate_element"></div>

        <?php if (ALLOW_MODIFICATION == 0) { ?>
            <li class="nav-item">
                <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                    <i class="fas fa-th-large"></i>
                </a>
            </li>
        <?php } ?>

        
        <div id="list" class="dropdown-menu dropdown-menu-lg dropdown-menu-right"></div>
        <!-- end send admin notification  -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fa fa-2x fa-user"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <?php if ($this->ion_auth->is_affiliate_user()) { ?>
                    <a href="#" class="dropdown-item">Welcome <b><?= ucfirst($this->ion_auth->user()->row()->username) ?> </b> ! </a>
                    <a href="<?= base_url('affiliate/home/profile') ?>" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> Profile
                    </a>
                    <a href="#" class="dropdown-item delete-affiliate" data-id=<?=$_SESSION['user_id'] ?>>
                        <i class="fa-trash-alt fas mr-2"></i> Delete my account
                    </a>
                    <a href="<?= base_url('affiliate/home/logout') ?>" class="dropdown-item">
                        <i class="fa fa-sign-out-alt mr-2"></i> Log Out
                    </a>
                <?php } else { ?>
                    <a href="#" class="dropdown-item">Welcome <b><?= ucfirst($this->ion_auth->user()->row()->username) ?> </b>! </a>
                    <a href="<?= base_url('affiliate/home/profile') ?>" class="dropdown-item"><i class="fas fa-user mr-2"></i> Profile </a>
                    <a href="<?= base_url('affiliate/home/logout') ?>" class="dropdown-item "><i class="fa fa-sign-out-alt mr-2"></i> Log Out </a>
                <?php } ?>
            </div>
        </li>
    </ul>
</nav>
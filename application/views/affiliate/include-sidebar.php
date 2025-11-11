<?php $settings = get_settings('system_settings', true);
$login_user =  fetch_details('affiliates', ['user_id' =>$_SESSION['user_id']], 'id,user_id,status');
// echo "<pre>";
// print_r($login_user);
?>

<aside class="main-sidebar elevation-2 sidebar-dark-indigo">
    <!-- Brand Logo -->
    <a href="<?= base_url('affiliate/home') ?>" class="brand-link">
        <img src="<?= base_url()  . get_settings('favicon') ?>" alt="<?= $settings['app_name']; ?>" title="<?= $settings['app_name']; ?>" class="brand-image">
        <span class="brand-text font-weight-light small"><?= $settings['app_name']; ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent nav-flat mb-5" data-widget="treeview" role="menu" data-accordion="false">
          
                <li class="nav-item has-treeview">
                    <a href="<?= base_url('affiliate/home') ?>" class="nav-link">
                        <i class="nav-icon fas fa-th-large text-primary"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <!-- <li class="nav-item has-treeview">
                    <a href="<?//= base_url('affiliate/product') ?>" class="nav-link">
                        <i class="fa-boxes fas nav-icon text-danger"></i>
                        <p>Manage Products</p>
                    </a>
                </li> -->

                <li class="nav-item has-treeview <?= (isset($login_user[0]['status']) && $login_user[0]['status'] == 7) ? 'd-none' : '' ?>">
                    <a href="<?= base_url('affiliate/category') ?>" class="nav-link">
                        <i class="fa-boxes fas nav-icon text-danger"></i>
                        <p>Categories</p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="<?= base_url('affiliate/product/manage_promoted_products') ?>" class="nav-link">
                        <i class="fa fa-chart-line nav-icon text-success"></i>
                        <p>Promoted Products</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-money-bill-wave nav-icon text-primary"></i>
                        <p>Money
                            <i class="right fas fa-angle-left "></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('affiliate/transaction') ?>" class="nav-link">
                                <i class="fa fa-coins nav-icon"></i>
                                <p>My Earnings</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('affiliate/transaction/payment_request') ?>" class="nav-link">
                                <i class="fa fa-wallet nav-icon"></i>
                                <p>Request Payment</p>
                            </a>
                        </li>
                        
                    </ul>
                </li>

                <li class="nav-item has-treeview">
                    <a href="<?= base_url('affiliate/policy') ?>" class="nav-link">
                        <i class="fa fa-lock nav-icon text-success"></i>
                        <p>Privacy Policy</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="<?= base_url('affiliate/policy/terms_conditions') ?>" class="nav-link">
                        <i class="fa fa-exclamation-triangle nav-icon text-warning"></i>
                        <p>Terms & Conditions</p>
                    </a>
                </li>


            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
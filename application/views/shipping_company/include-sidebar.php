<?php $settings = get_settings('system_settings', true);
$auth_settings = get_settings('authentication_settings', true);
?>
<aside class="main-sidebar elevation-2 sidebar-dark-indigo">
    <!-- Brand Logo -->
    <a href="<?= base_url('admin/home') ?>" class="brand-link">
        <img src="<?= base_url() . get_settings('favicon') ?>" alt="<?= $settings['app_name']; ?>" title="<?= $settings['app_name']; ?>" class="brand-image">
        <span class="brand-text font-weight-light small"><?= $settings['app_name']; ?></span>
    </a>
    <input type="hidden" id="auth_settings" name="auth_settings" value='<?= isset($auth_settings['authentication_method']) ? $auth_settings['authentication_method'] : ''; ?>'>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item has-treeview">
                    <a href="<?= base_url('shipping_company/home') ?>" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                            Home
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('shipping_company/orders/') ?>" class="nav-link">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Orders
                        </p>
                    </a>
                </li>

                <!-- Quotes menu item added here -->
                <li class="nav-item">
                    <a href="<?= base_url('shipping_company/quotes') ?>" class="nav-link">
                        <i class="nav-icon fas fa-shipping-fast"></i>
                        <p>Quotes</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('shipping_company/fund-transfer/') ?>" class="nav-link">
                        <i class="fa fa-rupee-sign nav-icon"></i>
                        <p>
                            Fund Transfers
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('shipping_company/fund-transfer/manage-cash') ?>" class="nav-link">
                        <i class="fas fa-money-bill-alt nav-icon "></i>
                        <p> Cash Collection </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('shipping_company/fund-transfer/manage-transactions') ?>" class="nav-link">
                        <i class="fas fa-money-check nav-icon "></i>
                        <p> Transactions </p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="<?= base_url('shipping_company/policy') ?>" class="nav-link">
                        <i class="fa fa-lock nav-icon"></i>
                        <p>Privacy Policy</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="<?= base_url('shipping_company/policy/terms_conditions') ?>" class="nav-link">
                        <i class="fa fa-exclamation-triangle nav-icon"></i>
                        <p>Terms & Conditions</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<?php $current_url = current_url(); ?>
<ul class="nav nav-pills nav-justified flex-column bg-white rounded shadow-lg p-3 mb-0 my-account-tab" id="pills-tab" role="tablist">
    <li class="nav-item p-1">
        <a class="rounded text-decoration-none d-flex gap-1 align-items-center <?= ($current_url == base_url('my-account/profile')) ? 'active h6 text-primary' : '' ?>" id="dashboard" href="<?= base_url('my-account/profile') ?>">
            <div>
                <i class="uil uil-user-circle fs-22"></i>
            </div>
            <p class="mb-0">
                <?= !empty($this->lang->line('profile')) ? str_replace('\\', '', $this->lang->line('profile')) : 'PROFILE' ?>
            </p>
        </a>
    </li>
    <li class="nav-item p-1">
        <a class="rounded text-decoration-none d-flex gap-1 align-items-center <?= ($current_url == base_url('my-account/orders')) ? 'active h6 text-primary' : '' ?>" id="order-history" href="<?= base_url('my-account/orders') ?>">
            <div>
                <i class="uil uil-history fs-22"></i>
            </div>
            <p class="mb-0">
                <?= !empty($this->lang->line('orders')) ? str_replace('\\', '', $this->lang->line('orders')) : 'ORDERS' ?>
            </p>
        </a>
    </li>
    <!-- <li class="nav-item p-1">
        <a class="rounded text-decoration-none d-flex gap-1 align-items-center <?//= ($current_url == base_url('my-account/notifications')) ? 'active h6 text-primary' : '' ?>" id="notification" href="<?= base_url('my-account/notifications') ?>">
            <div>
                <i class="uil uil-bell fs-22"></i>
            </div>
            <p class="mb-0">
                <?//= !empty($this->lang->line('notification')) ? str_replace('\\', '', $this->lang->line('notification')) : 'NOTIFICATION' ?>
            </p>
        </a>
    </li> -->
    <li class="nav-item p-1">
        <a class="rounded text-decoration-none d-flex gap-1 align-items-center <?= ($current_url == base_url('my-account/favorites')) ? 'active h6 text-primary' : '' ?>" id="wishlist" href="<?= base_url('my-account/favorites') ?>">
            <div>
                <i class="uil uil-heart-alt fs-22"></i>
            </div>
            <p class="mb-0">
                <?= !empty($this->lang->line('favorite')) ? str_replace('\\', '', $this->lang->line('favorite')) : 'Favorite' ?>
            </p>
        </a>
    </li>
    <li class="nav-item p-1">
        <a class="rounded text-decoration-none d-flex gap-1 align-items-center <?= ($current_url == base_url('my-account/manage-address')) ? 'active h6 text-primary' : '' ?>" id="v-pills-settings-tab" href="<?= base_url('my-account/manage-address') ?>" id="addresses" href="<?= base_url('my-account/manage-address') ?>">
            <div>
                <i class="uil uil-map fs-22"></i>
            </div>
            <p class="mb-0">
                <?= !empty($this->lang->line('address')) ? str_replace('\\', '', $this->lang->line('address')) : 'ADDRESS' ?>
            </p>
        </a>
    </li>
    <li class="nav-item p-1">
        <a class="rounded text-decoration-none d-flex gap-1 align-items-center <?= ($current_url == base_url('my-account/wallet')) ? 'active h6 text-primary' : '' ?>" id="wallet-details" href="<?= base_url('my-account/wallet') ?>">
            <div>
                <i class="uil uil-wallet fs-22"></i>
            </div>
            <p class="mb-0">
                <?= !empty($this->lang->line('wallet')) ? str_replace('\\', '', $this->lang->line('wallet')) : 'WALLET' ?>
            </p>
        </a>
    </li>
    <li class="nav-item p-1">
        <a class="rounded text-decoration-none d-flex gap-1 align-items-center <?= ($current_url == base_url('my-account/transactions')) ? 'active h6 text-primary' : '' ?>" id="transaction-details" href="<?= base_url('my-account/transactions') ?>">
            <div>
                <i class="uil uil-money-bill fs-22"></i>
            </div>
            <p class="mb-0">
                <?= !empty($this->lang->line('transaction')) ? str_replace('\\', '', $this->lang->line('transaction')) : 'TRANSACTION' ?>
            </p>
        </a>
    </li>
    <li class="nav-item p-1">
        <a class="rounded text-decoration-none d-flex gap-1 align-items-center <?= ($current_url == base_url('my-account/chat')) ? 'active h6 text-primary' : '' ?>" id="user-chat" href="<?= base_url('my-account/chat') ?>">
            <div>
                    <i class="uil uil-comments-alt fs-22"></i> 
            </div>
            <p class="mb-0">
                    <?= !empty($this->lang->line('chat')) ? str_replace('\\', '', $this->lang->line('chat')) : 'Chat' ?>
            </p>
        </a>
    </li>
    <li class="nav-item p-1">
        <a class="rounded text-decoration-none d-flex gap-1 align-items-center <?= ($current_url == base_url('my-account/tickets')) ? 'active h6 text-primary' : '' ?>" id="user-chat" href="<?= base_url('my-account/tickets') ?>">
            <div>
                <i class="uil uil-ticket fs-22"></i>
            </div>
            <p class="mb-0">
                <?= !empty($this->lang->line('support_tickets')) ? str_replace('\\', '', $this->lang->line('support_tickets')) : 'Support Tickets' ?>
            </p>
        </a>
    </li>
    <li class="nav-item p-1">
        <a class="rounded text-decoration-none d-flex gap-1 align-items-center <?= ($current_url == base_url('login/logout')) ? 'active h6 text-primary' : '' ?>" id="user_logout" href="<?= base_url('login/logout') ?>">
            <div>
                <i class="uil uil-signout fs-22"></i>
            </div>
            <p class="mb-0">
                <?= !empty($this->lang->line('logout')) ? str_replace('\\', '', $this->lang->line('logout')) : 'LOGOUT' ?>
            </p>
        </a>
    </li>
</ul>
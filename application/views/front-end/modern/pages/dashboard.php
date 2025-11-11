<?php $current_url = current_url(); ?>

        <div class="overflow-auto">
            <div class="px-1 py-2">
                <div class="d-flex gap-2 justify-content-between">
                    <div class='card col-6 col-md-3 h-15 text-center w-15 p-0 <?= ($current_url == base_url('my-account/profile')) ? 'bg-soft-primary' : '' ?>'>
                        <a href='<?= base_url('my-account/profile') ?>' class="link-color text-decoration-none">
                            <div class='bg-transparent card-header fs-12 p-2'>
                                <?= !empty($this->lang->line('profile')) ? str_replace('\\', '', $this->lang->line('profile')) : 'PROFILE' ?>
                            </div>
                            <div class='card-body p-2'>
                                <i class="uil uil-user-circle fs-22 dashboard-icon link-color"></i>
                            </div>
                        </a>
                    </div>
                    <div class='card col-6 col-md-3 h-15 text-center w-15 p-0 <?= ($current_url == base_url('my-account/orders')) ? 'bg-soft-primary' : '' ?>'>
                        <a href='<?= base_url('my-account/orders') ?>' class="link-color text-decoration-none">
                            <div class='bg-transparent card-header fs-12 p-2'>
                                <?= !empty($this->lang->line('orders')) ? str_replace('\\', '', $this->lang->line('orders')) : 'ORDERS' ?>
                            </div>
                            <div class='card-body p-2'>
                                <i class="uil uil-history fs-22 dashboard-icon link-color"></i>
                            </div>
                        </a>
                    </div>
                    
                    <div class='card col-6 col-md-3 h-15 text-center w-15 p-0 <?= ($current_url == base_url('my-account/favorites')) ? 'bg-soft-primary' : '' ?>'>
                        <a href='<?= base_url('my-account/favorites') ?>' class="link-color text-decoration-none">
                            <div class='bg-transparent card-header fs-12 p-2'>
                                <?= !empty($this->lang->line('favorite')) ? str_replace('\\', '', $this->lang->line('favorite')) : 'Favorite' ?>
                            </div>
                            <div class='card-body p-2'>
                                <i class="uil uil-heart-alt fs-22 dashboard-icon link-color"></i>
                            </div>
                        </a>
                    </div>
                    <div class='card col-6 col-md-3 h-15 text-center w-15 p-0 <?= ($current_url == base_url('my-account/manage-address')) ? 'bg-soft-primary' : '' ?>'>
                        <a href='<?= base_url('my-account/manage-address') ?>' class="link-color text-decoration-none">
                            <div class='bg-transparent card-header fs-12 p-2'>
                                <?= !empty($this->lang->line('address')) ? str_replace('\\', '', $this->lang->line('address')) : 'ADDRESS' ?>
                            </div>
                            <div class='card-body p-2'>
                                <i class="uil uil-map fs-22 dashboard-icon link-color"></i>
                            </div>
                        </a>
                    </div>
                    <div class='card col-6 col-md-3 h-15 text-center w-15 p-0 <?= ($current_url == base_url('my-account/wallet')) ? 'bg-soft-primary' : '' ?>'>
                        <a href='<?= base_url('my-account/wallet') ?>' class="link-color text-decoration-none">
                            <div class='bg-transparent card-header fs-12 p-2'>
                                <?= !empty($this->lang->line('wallet')) ? str_replace('\\', '', $this->lang->line('wallet')) : 'WALLET' ?>
                            </div>
                            <div class='card-body p-2'>
                                <i class="uil uil-wallet fs-22 dashboard-icon link-color"></i>
                            </div>
                        </a>
                    </div>
                    <div class='card col-6 col-md-3 h-15 text-center w-15 p-0 <?= ($current_url == base_url('my-account/transactions')) ? 'bg-soft-primary' : '' ?>'>
                        <a href='<?= base_url('my-account/transactions') ?>' class="link-color text-decoration-none">
                            <div class='bg-transparent card-header fs-12 p-2'>
                                <?= !empty($this->lang->line('transaction')) ? str_replace('\\', '', $this->lang->line('transaction')) : 'TRANSACTION' ?>
                            </div>
                            <div class='card-body p-2'>
                                <i class="uil uil-money-bill fs-22 dashboard-icon link-color"></i>
                            </div>
                        </a>
                    </div>
                    <div class='card col-6 col-md-3 h-15 text-center w-15 p-0 <?= ($current_url == base_url('my-account/chat')) ? 'bg-soft-primary' : '' ?>'>
                        <a href='<?= base_url('my-account/chat') ?>' class="link-color text-decoration-none">
                            <div class='bg-transparent card-header fs-12 p-2'>
                                <?= !empty($this->lang->line('chat')) ? str_replace('\\', '', $this->lang->line('chat')) : 'Chat' ?>
                            </div>
                            <div class='card-body p-2'>
                                <i class="uil uil-comments-alt fs-22 dashboard-icon link-color"></i>
                            </div>
                        </a>
                    </div>
                    <div class='card col-6 col-md-3 h-15 text-center w-15 p-0 <?= ($current_url == base_url('my-account/tickets')) ? 'bg-soft-primary' : '' ?>'>
                        <a href='<?= base_url('my-account/tickets') ?>' class="link-color text-decoration-none">
                            <div class='bg-transparent card-header fs-12 p-1'>
                                <?= !empty($this->lang->line('support_tickets')) ? str_replace('\\', '', $this->lang->line('support_tickets')) : 'Support Tickets' ?>
                            </div>
                            <div class='card-body p-2'>
                                <i class="uil uil-ticket fs-22 dashboard-icon link-color"></i>
                            </div>
                        </a>
                    </div>
                    <?php 
                    if((!empty($settings['is_refer_earn_on']) && ($settings['is_refer_earn_on'] == 1 || $settings['is_refer_earn_on'] == '1'))){
                    ?>
                    <div class='card col-6 col-md-3 h-15 text-center w-15 p-0 <?= ($current_url == base_url('my-account/refer_and_earn')) ? 'bg-soft-primary' : '' ?>'>
                        <a href='<?= base_url('my-account/refer_and_earn') ?>' class="link-color text-decoration-none">
                            <div class='bg-transparent card-header fs-12 px-1 py-2'>
                                <?= !empty($this->lang->line('refer_and_earn')) ? str_replace('\\', '', $this->lang->line('refer_and_earn')) : 'Refer and Earn' ?>
                            </div>
                            <div class='card-body p-2'>
                                <i class="uil uil-coins fs-22 dashboard-icon link-color"></i>
                            </div>
                        </a>
                    </div>
                    <?php } ?>
                    <div class='card col-6 col-md-3 h-15 text-center w-15 p-0 <?= ($current_url == base_url('my-account/logout')) ? 'bg-soft-primary' : '' ?>'>
                        <a href='' class="link-color text-decoration-none" id="user_logout">
                            <div class='bg-transparent card-header fs-12 p-2'>
                                <?= !empty($this->lang->line('logout')) ? str_replace('\\', '', $this->lang->line('logout')) : 'LOGOUT' ?>
                            </div>
                            <div class='card-body p-2'>
                                <i class="uil uil-signout fs-22 dashboard-icon link-color"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
<!-- breadcrumb -->
<section class="breadcrumb-title-bar colored-breadcrumb deeplink_wrapper">
    <div class="main-content responsive-breadcrumb">
        <h2><?= !empty($this->lang->line('transactions')) ? str_replace('\\', '', $this->lang->line('transactions')) : 'Transactions' ?></h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('my-account') ?>"><?= !empty($this->lang->line('dashboard')) ? str_replace('\\', '', $this->lang->line('dashboard')) : 'Dashboard' ?></a></li>
                <li class="breadcrumb-item"><?= !empty($this->lang->line('transactions')) ? str_replace('\\', '', $this->lang->line('transactions')) : 'Transactions' ?></li>
            </ol>
        </nav>
    </div>

</section>
<!-- end breadcrumb -->

<section class="my-account-section">
    <div class="main-content">
        <div class="col-md-12 mt-5 mb-3">
            <div class="user-detail align-items-center">
                <div class="ml-3">
                    <h6 class="text-muted mb-0"><?= !empty($this->lang->line('hello')) ? str_replace('\\', '', $this->lang->line('hello')) : 'Hello' ?></h6>
                    <h5 class="mb-0"><?= $user->username ?></h5>
                </div>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-md-4">
                <?php $this->load->view('front-end/' . THEME . '/pages/my-account-sidebar') ?>
            </div>
            <div class="col-md-8 col-12">
                <div class=' border-0'>
                    <div class="card-header bg-white">
                        <h1 class="h4"><?= !empty($this->lang->line('transactions')) ? str_replace('\\', '', $this->lang->line('transactions')) : 'Transactions' ?></h1>
                    </div>
                    <div class="card-body">
                        <table class='table-striped' data-toggle="table" data-url="<?= base_url('my-account/get-transactions') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-query-params="transaction_query_params">
                            <thead>
                                <tr>
                                    <th data-field="id" data-sortable="true"><?= !empty($this->lang->line('id')) ? str_replace('\\', '', $this->lang->line('id')) : 'ID' ?></th>
                                    <th data-field="name" data-sortable="false"><?= !empty($this->lang->line('username')) ? str_replace('\\', '', $this->lang->line('username')) : 'Username' ?></th>
                                    <th data-field="order_id" data-sortable="false"><?= !empty($this->lang->line('order_id')) ? str_replace('\\', '', $this->lang->line('order_id')) : 'Order ID' ?></th>
                                    <th data-field="txn_id" data-sortable="false"><?= !empty($this->lang->line('transaction_id')) ? str_replace('\\', '', $this->lang->line('transaction_id')) : 'Transaction ID' ?></th>
                                    <th data-field="payu_txn_id" data-sortable="false" data-visible="false"><?= !empty($this->lang->line('pay_transaction_id')) ? str_replace('\\', '', $this->lang->line('pay_transaction_id')) : 'Payment Transaction ID' ?></th>
                                    <th data-field="amount" data-sortable="false"><?= !empty($this->lang->line('amount')) ? str_replace('\\', '', $this->lang->line('amount')) : 'Amount' ?></th>
                                    <th data-field="status" data-sortable="false"><?= !empty($this->lang->line('status')) ? str_replace('\\', '', $this->lang->line('status')) : 'Status' ?></th>
                                    <th data-field="message" data-sortable="false" data-visible="false"><?= !empty($this->lang->line('message')) ? str_replace('\\', '', $this->lang->line('message')) : 'Message' ?></th>
                                    <th data-field="txn_date" data-sortable="false"><?= !empty($this->lang->line('date')) ? str_replace('\\', '', $this->lang->line('date')) : 'Date' ?></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
    <!--end container-->
</section>
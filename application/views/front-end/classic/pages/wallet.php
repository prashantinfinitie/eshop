<!-- breadcrumb -->
<section class="breadcrumb-title-bar colored-breadcrumb deeplink_wrapper">
    <div class="main-content responsive-breadcrumb">
        <h2><?= !empty($this->lang->line('wallet')) ? str_replace('\\', '', $this->lang->line('wallet')) : 'Wallet' ?></h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('my-account') ?>"><?= !empty($this->lang->line('dashboard')) ? str_replace('\\', '', $this->lang->line('dashboard')) : 'Dashboard' ?></a></li>
                <li class="breadcrumb-item"><?= !empty($this->lang->line('wallet')) ? str_replace('\\', '', $this->lang->line('wallet')) : 'Wallet' ?></li>
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
                <div class='card border-0'>
                    <div class="card-header bg-white">
                        <div class="row">
                            <h1 class="h2 col-6"><?= !empty($this->lang->line('wallet')) ? str_replace('\\', '', $this->lang->line('wallet')) : 'Wallet' ?></h1>

                        </div>
                    </div>
                    <?php $payment_methods = get_settings('payment_method', true); ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 row mr-1">
                                <div class='card col-md-12 wallet-card'>
                                    <div class="row d-flex justify-content-center">
                                        <div class="h4 d-flex  mt-3  ">
                                            <img src="<?= THEME_ASSETS_URL . 'images/wallet.png' ?>" class="payment-gateway-images" alt="wallet" title="Customer wallet balance">
                                        </div>
                                        <div class="h4 d-flex  mt-3  justify-content-center">
                                        <?= !empty($this->lang->line('balance')) ? str_replace('\\', '', $this->lang->line('balance')) : 'Balance' ?>
                                        </div>
                                        <div class="h4 d-flex justify-content-center  price">
                                            <p class="h4"> <?= $settings['currency'] . ' ' . number_format($user->balance, 2)  ?> </p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-4 row">
                                <div class='card col-md-12 wallet-card'>
                                    <div class='card-body bg-transparent'>

                                        <div class="h4 d-flex justify-content-center mt-2">
                                            <button type="button" class="button button-rounded button-primary" data-toggle="modal" data-target="#myModal"><?= !empty($this->lang->line('add_money')) ? str_replace('\\', '', $this->lang->line('add_money')) : 'Add Money' ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 row">
                                <div class='card col-md-12 ml-3 wallet-card'>
                                    <div class='card-body bg-transparent'>
                                        <div class="h4 d-flex justify-content-center mt-2">
                                            <button type="button" class="button button-rounded button-danger" data-toggle="modal" data-target="#withdraw"><?= !empty($this->lang->line('withdraw_money')) ? str_replace('\\', '', $this->lang->line('withdraw_money')) : 'Withdraw Money' ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal" id="myModal">
                                <div class="modal-dialog">
                                    <div class="modal-content card">

                                        <div class="modal-header">
                                            <h4 class="modal-title price"><?= !empty($this->lang->line('add_money_to_wallet')) ? str_replace('\\', '', $this->lang->line('add_money_to_wallet')) : 'Add money to wallet' ?></h4>
                                            </div>
                                        <!-- Modal body -->
                                        <div class="ship-details-wrapper mt-3 payment-methods">
                                            <form class="form-horizontal form-submit-event" method="POST" id="wallet_form" enctype="multipart/form-data">
                                                <?php
                                                $CUR_USERID = $_SESSION['user_id'];
                                                $order_id = 'wallet-refill-user' . "-" . $CUR_USERID . "-" . time() . "-" . rand("900", "999");


                                                $payment_methods = get_settings('payment_method', true);
                                                $name = fetch_details('users', ['id' => $_SESSION['user_id']]);


                                                ?>

                                                <input type="hidden" name="app_name" id="app_name" value="<?= $settings['app_name'] ?>" />
                                                <input type="hidden" id="flutterwave_currency" value="<?= $payment_methods['flutterwave_currency_code'] ?>" />
                                                <input type="hidden" id="user_email" value="<?= $_SESSION['email']  ?>" />

                                                <input type="hidden" id="username" value="<?= $username['username'] ?>" />
                                                <input type="hidden" id="user_contact" value="<?= $username['mobile'] ?>" />
                                                <input type="hidden" name="logo" id="logo" value="<?= base_url(get_settings('web_logo')) ?>" />


                                                <input type="hidden" name="order_id" id="order_id" value="<?= $order_id ?>">
                                                <input type="number" name="amount" id="amount" min="1" required class="col-md-11 ml-4 form-control" placeholder="<?= !empty($this->lang->line('enter_amount')) ? str_replace('\\', '', $this->lang->line('enter_amount')) : 'Enter Amount' ?>">


                                                <input type="text" name="message" class="col-md-11 ml-4 mt-3 ticket_msg form-control " id="message_input" placeholder="<?= !empty($this->lang->line('type_message')) ? str_replace('\\', '', $this->lang->line('type_message')) : 'Type Message' ?>">
                                                <br>

                                                <div class="align-item-center ship-title-details justify-content-between d-flex ml-3">
                                                    <h5><?= !empty($this->lang->line('payment_method')) ? str_replace('\\', '', $this->lang->line('payment_method')) : 'Payment Method' ?></h5>
                                                </div>
                                                <div class="shipped-details mt-3 col-md-6">
                                                    <table class="table table-step-shipping">
                                                        <tbody>

                                                            <?php if (isset($payment_methods['paypal_payment_method']) && $payment_methods['paypal_payment_method'] == 1) { ?>
                                                                <tr>
                                                                    <td class="tab_border">
                                                                        <label for="paypal">
                                                                            <input id="paypal" name="payment_method" type="radio" value="Paypal" required>
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="paypal">
                                                                            <img src="<?= THEME_ASSETS_URL . 'images/paypal.png' ?>" class="payment-gateway-images" alt="Paypal">
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="paypal">
                                                                            Paypal
                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                            <?php if (isset($payment_methods['razorpay_payment_method']) && $payment_methods['razorpay_payment_method'] == 1) { ?>
                                                                <tr>
                                                                    <td class="tab_border">
                                                                        <label for="razorpay">
                                                                            <input id="razorpay" name="payment_method" type="radio" value="Razorpay" required>
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="razorpay">
                                                                            <img src="<?= THEME_ASSETS_URL . 'images/razorpay.png' ?>" class="payment-gateway-images" alt="Razorpay">
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="razorpay">
                                                                            RazorPay
                                                                        </label>
                                                                        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="" />
                                                                        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id" value="" />
                                                                        <input type="hidden" name="razorpay_signature" id="razorpay_signature" value="" />
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                            <?php if (isset($payment_methods['paystack_payment_method']) && $payment_methods['paystack_payment_method'] == 1) { ?>
                                                                <tr>
                                                                    <td class="tab_border">
                                                                        <label for="paystack">
                                                                            <input id="paystack" name="payment_method" type="radio" value="Paystack" required>
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="paystack">
                                                                            <img src="<?= THEME_ASSETS_URL . 'images/paystack.png' ?>" class="payment-gateway-images" alt="Paystack">
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="paystack">
                                                                            Paystack
                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                            <?php if (isset($payment_methods['payumoney_payment_method']) && $payment_methods['payumoney_payment_method'] == 1) { ?>
                                                                <tr>
                                                                    <td class="tab_border">
                                                                        <label for="payumoney">
                                                                            <input id="payumoney" name="payment_method" type="radio" value="Payumoney" required>
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="payumoney">
                                                                            <img src="<?= THEME_ASSETS_URL . 'images/payumoney.png' ?>" class="payment-gateway-images" alt="Payumoney">
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="payumoney">
                                                                            Payumoney
                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                            <?php if (isset($payment_methods['flutterwave_payment_method']) && $payment_methods['flutterwave_payment_method'] == 1) { ?>
                                                                <tr>
                                                                    <td class="tab_border">
                                                                        <label for="flutterwave">
                                                                            <input id="flutterwave" name="payment_method" type="radio" value="Flutterwave" required>
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="flutterwave">
                                                                            <img src="<?= THEME_ASSETS_URL . 'images/flutterwave.png' ?>" class="payment-gateway-images" alt="Flutterwave">
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="flutterwave">
                                                                            Flutterwave
                                                                        </label>
                                                                        <?php foreach ($name as $username) { ?>
                                                                            <input type="hidden" name="flutterwave_public_key" id="flutterwave_public_key" value="<?= $payment_methods['flutterwave_public_key'] ?>" />
                                                                            <input type="hidden" name="flutterwave_transaction_id" id="flutterwave_transaction_id" value="" />
                                                                            <input type="hidden" name="flutterwave_transaction_ref" id="flutterwave_transaction_ref" value="" />
                                                                            <input type="hidden" name="promo_set" id="promo_set" value="" />
                                                                        <?php  } ?>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                            <?php if (isset($payment_methods['paytm_payment_method']) && $payment_methods['paytm_payment_method'] == 1) { ?>
                                                                <tr>
                                                                    <td class="tab_border">
                                                                        <label for="paytm">
                                                                            <input id="paytm" name="payment_method" type="radio" value="Paytm" required>
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="paytm">
                                                                            <img src="<?= THEME_ASSETS_URL . 'images/paytm.png' ?>" class="payment-gateway-images" alt="Paytm">
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="paytm">
                                                                            Paytm
                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                            <?php if (isset($payment_methods['stripe_payment_method']) && $payment_methods['stripe_payment_method'] == 1) { ?>
                                                                <tr>
                                                                    <td class="tab_border">
                                                                        <label for="stripe">
                                                                            <input id="stripe" name="payment_method" type="radio" value="Stripe" required>
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="stripe">
                                                                            <img src="<?= THEME_ASSETS_URL . 'images/stripe.png' ?>" class="payment-gateway-images" alt="Stripe">
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="stripe">
                                                                            Stripe
                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if (isset($payment_methods['midtrans_payment_method']) && $payment_methods['midtrans_payment_method'] == 1) { ?>
                                                                <tr>
                                                                    <td class="tab_border">
                                                                        <label for="midtrans">
                                                                            <input id="midtrans" name="payment_method" type="radio" value="Midtrans" required>
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="midtrans">
                                                                            <img src="<?= THEME_ASSETS_URL . 'images/midtrans.jpg' ?>" class="payment-gateway-images" alt="Midtrans">
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="midtrans">
                                                                            Midtrans
                                                                        </label>

                                                                    </td>
                                                                </tr>
                                                            <?php } ?>


                                                            <?php if (isset($payment_methods['myfaoorah_payment_method']) && $payment_methods['myfaoorah_payment_method'] == 1) { ?>
                                                                <tr>
                                                                    <td class="tab_border">
                                                                        <label for="my_fatoorah">
                                                                            <input id="my_fatoorah" name="payment_method" type="radio" value="my_fatoorah" required>
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="my_fatoorah">
                                                                            <img src="<?= THEME_ASSETS_URL . 'images/myfatoorah.jpg' ?>" class="payment-gateway-images" alt="myfatoorah">
                                                                        </label>
                                                                    </td>

                                                                    <td class="tab_border">
                                                                        <label for="my_fatoorah">
                                                                            My Fatoorah
                                                                        </label>
                                                                    </td>
                                                                </tr>

                                                            <?php } ?>

                                                            <?php if (isset($payment_methods['instamojo_payment_method']) && $payment_methods['instamojo_payment_method'] == 1) { ?>
                                                                <tr>
                                                                    <td class="tab_border">
                                                                        <label for="instamojo">
                                                                            <input id="instamojo" name="payment_method" type="radio" value="instamojo" required>
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="instamojo">
                                                                            <img src="<?= THEME_ASSETS_URL . 'images/instamojo.png' ?>" class="payment-gateway-images" alt="instamojo">
                                                                        </label>
                                                                    </td>

                                                                    <td class="tab_border">
                                                                        <label for="instamojo">
                                                                            Instamojo
                                                                        </label>
                                                                     </td>
                                                                </tr>

                                                            <?php } ?>

                                                            <?php if (isset($payment_methods['phonepe_payment_method']) && $payment_methods['phonepe_payment_method'] == 1) { ?>
                                                                <tr>
                                                                    <td class="tab_border">
                                                                        <label for="phonepe">
                                                                            <input id="phonepe" class="form-check-input m-0" name="payment_method" type="radio" value="phonepe" required>
                                                                        </label>
                                                                    </td>
                                                                    <td class="tab_border">
                                                                        <label for="phonepe">
                                                                            <img src="<?= THEME_ASSETS_URL . 'images/phonepay-logo.png' ?>" class="payment-gateway-images" alt="phonepe">
                                                                        </label>
                                                                    </td>

                                                                    <td class="tab_border">
                                                                        <label for="phonepe">
                                                                            PhonePe
                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>

                                                        </tbody>
                                                    </table>
                                                    <div id="stripe_div">
                                                        <div id="stripe-card-element">
                                                            <!--Stripe.js injects the Card Element-->
                                                        </div>
                                                        <p id="card-error" role="alert"></p>
                                                        <p class="result-message hidden"></p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success" id="wallet_refill" value="Save"><?= !empty($this->lang->line('refill')) ? str_replace('\\', '', $this->lang->line('refill')) : 'Refill' ?></button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?= !empty($this->lang->line('close')) ? str_replace('\\', '', $this->lang->line('close')) : 'Close' ?></button>
                                                </div>
                                            </form>
                                            <!-- </form> -->
                                        </div>

                                        <!-- Modal footer -->


                                    </div>
                                </div>
                            </div>

                            <div class="modal" id="withdraw">
                                <div class="modal-dialog">
                                    <div class="modal-content card">

                                        <!-- Modal Header -->
                                        <div class="modal-header">
                                            <h4 class="modal-title price"><?= !empty($this->lang->line('withdraw_money')) ? str_replace('\\', '', $this->lang->line('withdraw_money')) : 'Withdraw Money' ?></h4>


                                        </div>
                                        <!-- Modal body -->
                                        <div class="ship-details-wrapper mt-3 payment-methods">
                                            <!-- <form action="<?= base_url('my_account/withdraw_money') ?>" class='form-submit-event' method="post"> -->

                                            <input type="number" name="amount" id="withdrawal_amount" min="1" required class="col-md-11 ml-4 form-control" placeholder="<?= !empty($this->lang->line('withdraw_money')) ? str_replace('\\', '', $this->lang->line('withdraw_money')) : 'Withdrawal Money' ?>">

                                            <input type="hidden" id="user_id" name='user_id' value="<?= $_SESSION['user_id']  ?>" />


                                            <input type="text" name="payment_address" id="payment_address" class="col-md-11 ml-4 mt-3 ticket_msg form-control " id="message_input" placeholder="<?= !empty($this->lang->line('enter_bank_details')) ? str_replace('\\', '', $this->lang->line('enter_bank_details')) : 'Enter bank details' ?>">
                                            <br>



                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success" id="withdraw_amount" value="Save"><?= labels('Withdraw', 'Withdraw') ?></button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><?= !empty($this->lang->line('close')) ? str_replace('\\', '', $this->lang->line('close')) : 'Close' ?>+</button>
                                            </div>
                                            <!-- </form> -->
                                        </div>

                                        <!-- Modal footer -->


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#wallet_transaction"><?= !empty($this->lang->line('wallet_transaction')) ? str_replace('\\', '', $this->lang->line('wallet_transaction')) : 'Wallet transaction' ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#withdrawal_requests"><?= !empty($this->lang->line('withdrawal_request')) ? str_replace('\\', '', $this->lang->line('withdrawal_request')) : 'Withdrawal requests' ?></a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div id="wallet_transaction" class="tab-pane active"><br>
                            <table class='table-striped price' data-toggle="table" data-url="<?= base_url('my-account/get-wallet-transactions') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-query-params="customer_wallet_query_params">
                                <thead>
                                <tr>
                                    <th data-field="id" data-sortable="true"><?= !empty($this->lang->line('id')) ? str_replace('\\', '', $this->lang->line('id')) : 'ID' ?></th>
                                    <th data-field="name" data-sortable="false"><?= !empty($this->lang->line('username')) ? str_replace('\\', '', $this->lang->line('username')) : 'Username' ?></th>
                                    <th data-field="type" data-sortable="false"><?= !empty($this->lang->line('type')) ? str_replace('\\', '', $this->lang->line('type')) : 'Type' ?></th>
                                    <th data-field="amount" data-sortable="false"><?= !empty($this->lang->line('amount')) ? str_replace('\\', '', $this->lang->line('amount')) : 'Amount' ?></th>
                                    <th data-field="status" data-sortable="false"><?= !empty($this->lang->line('status')) ? str_replace('\\', '', $this->lang->line('status')) : 'Status' ?></th>
                                    <th data-field="message" data-sortable="false"><?= !empty($this->lang->line('message')) ? str_replace('\\', '', $this->lang->line('message')) : 'Message' ?></th>
                                    <th data-field="date" data-sortable="false"><?= !empty($this->lang->line('date')) ? str_replace('\\', '', $this->lang->line('date')) : 'Date' ?></th>
                                </tr>
                                </thead>
                            </table>
                        </div>

                        <div id="withdrawal_requests" class="tab-pane fade"><br>
                            <table class='table-striped' data-toggle="table" data-url="<?= base_url('my-account/get_withdrawal_request') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="oi.id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-export-types='["txt","excel","csv"]' data-export-options='{"fileName": "order-item-list","ignoreColumn": ["state"] }' data-query-params="customer_withdrawal_query_params">
                                <thead>
                                    <tr>
                                        <th data-field="id" data-sortable="true">ID</th>
                                        <th data-field="name" data-sortable="false">User Name</th>
                                        <th data-field="payment_address" data-sortable="false">payment_address</th>
                                        <th data-field="amount_requested" data-sortable="false">Amount</th>
                                        <th data-field="status" data-sortable="false">Status</th>
                                        <th data-field="date_created" data-sortable="false">Date</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
    </div>
    <!--end container-->
</section>
<form action="<?= base_url('payment/paypal_wallet') ?>" id="paypal_form" method="POST">
    <input type="hidden" id="csrf_token" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="order_id" id="paypal_order_id" value="" />
    <input type="hidden" name="amount" id="paypal_amount" value="" />
</form>



<input type="hidden" name="razorpay_key_id" id="razorpay_key_id" value="<?= $payment_methods['razorpay_key_id'] ?>" />

<input type="hidden" name="paystack_reference" id="paystack_reference" value="" />
<input type="hidden" name="paystack_key_id" id="paystack_key_id" value="<?= $payment_methods['paystack_key_id'] ?>" />

<input type="hidden" id="paytm_transaction_token" name="paytm_transaction_token" value="" />
<input type="hidden" id="paytm_order_id" name="paytm_order_id" value="" />

<input type="hidden" name="my_fatoorah_order_id" id="my_fatoorah_order_id" value="" />
<input type="hidden" name="my_fatoorah_session_id" id="my_fatoorah_session_id" value="" />
<input type="hidden" name="my_fatoorah_payment_id" id="my_fatoorah_payment_id" value="" />

<input type="hidden" name="stripe_client_secret" id="stripe_client_secret" value="" />
<input type="hidden" name="stripe_payment_id" id="stripe_payment_id" value="" />
<input type="hidden" name="stripe_key_id" id="stripe_key_id" value="<?= $payment_methods['stripe_publishable_key'] ?>" />

<?php if (isset($payment_methods['paytm_payment_method']) && $payment_methods['paytm_payment_method'] == 1) {
    $url = ($payment_methods['paytm_payment_mode'] == "production") ? "https://securegw.paytm.in/" : "https://securegw-stage.paytm.in/";
?>
    <script type="application/javascript" src="<?= $url ?>merchantpgpui/checkoutjs/merchants/<?= $payment_methods['paytm_merchant_id'] ?>.js"></script>
<?php } ?>
<script src="https://checkout.flutterwave.com/v3.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://js.paystack.co/v1/inline.js"></script>
<script src="https://js.stripe.com/v3/"></script>
<script src="https://js.instamojo.com/v1/checkout.js"></script>

<?php
$midtrans_url = $midtrans_client_key = "";
if (isset($payment_methods['midtrans_payment_mode'])) {
    $midtrans_url = (isset($payment_methods['midtrans_payment_mode']) && $payment_methods['midtrans_payment_mode'] == "sandbox") ? "https://app.sandbox.midtrans.com/snap/snap.js" : "https://app.midtrans.com/snap/snap.js";
    $midtrans_client_key = $payment_methods['midtrans_client_key'];
}
?>
<script type="text/javascript" src="<?= $midtrans_url ?>" data-client-key="<?= $midtrans_client_key ?>"></script>

<script src="<?= THEME_ASSETS_URL . '/js/wallet.js' ?>"></script>
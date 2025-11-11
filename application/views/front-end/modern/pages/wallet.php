<!-- breadcrumb -->
<div class="content-wrapper deeplink_wrapper">
    <section class="wrapper bg-soft-grape">
        <div class="container py-3 py-md-5">
            <nav class="d-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('my-account/profile') ?>" class="text-decoration-none"><?= !empty($this->lang->line('dashboard')) ? str_replace('\\', '', $this->lang->line('dashboard')) : 'Dashboard' ?></a></li>
                    <?php if (isset($right_breadcrumb) && !empty($right_breadcrumb)) {
                        foreach ($right_breadcrumb as $row) {
                    ?>
                            <li class="breadcrumb-item"><?= $row ?></li>
                    <?php }
                    } ?>
                    <li class="breadcrumb-item active text-muted" aria-current="page"><?= !empty($this->lang->line('wallet')) ? str_replace('\\', '', $this->lang->line('wallet')) : 'Wallet' ?></li>
                </ol>
            </nav>
            <!-- /nav -->
        </div>
        <!-- /.container -->
    </section>
</div>
<!-- end breadcrumb -->

<section class="my-account-section">
    <div class="container mb-15">
        <div class="my-8">
            <?php $this->load->view('front-end/' . THEME . '/pages/dashboard') ?>
        </div>
        <div class="col-12">
            <div class=' border-0'>
                <div class="card-header bg-white">
                    <div class="row">
                        <h1 class="col-6 h4"><?= !empty($this->lang->line('wallet')) ? str_replace('\\', '', $this->lang->line('wallet')) : 'Wallet' ?></h1>
                    </div>
                </div>
                <hr class="mt-0 mb-5">
                <div class="align-items-center d-flex justify-content-center">
                    <div class="col-md-4 col-6">
                        <div class="card col-md-12">
                            <div class="d-flex flex-column justify-content-center">
                                <div class="d-flex justify-content-center align-items-center">
                                    <img src="<?= base_url("assets/front_end/modern/img/new/wallet.png") ?>" alt="wallet-image" height="60px" width="80px" />
                                </div>
                                <div class="d-flex fs-16 fw-semibold justify-content-center align-items-center">
                                    <?= !empty($this->lang->line('balance')) ? str_replace('\\', '', $this->lang->line('balance')) : 'Balance' ?>
                                </div>
                                <div class="d-flex justify-content-center align-items-center">
                                    <p class="fs-14 fw-semibold"><?= $settings['currency'] . ' ' . $user->balance ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-6">
                        <div class="card">
                            <div class="bg-transparent card-body px-0 py-5">
                                <div class="d-flex justify-content-center flex-column gap-2">
                                    <a href="#" class="btn btn-primary btn-xs rounded-pill mx-1 mb-2 mb-md-0 mx-xl-15" data-bs-toggle="modal" data-bs-target="#myModal"><?= !empty($this->lang->line('add_money')) ? str_replace('\\', '', $this->lang->line('add_money')) : 'Add Money' ?></a>
                                    <a href="#" class="btn btn-danger btn-xs rounded-pill mx-1 mb-2 mb-md-0 mx-xl-15" data-bs-toggle="modal" data-bs-target="#withdraw"><?= !empty($this->lang->line('withdraw_money')) ? str_replace('\\', '', $this->lang->line('withdraw_money')) : 'Withdraw money' ?></a>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="modal" id="myModal">
                        <div class="modal-dialog">
                            <div class="modal-content card">

                                <!-- Modal Header -->
                                <div class="modal-header">
                                    <h4 class="modal-title price"><?= !empty($this->lang->line('add_money_to_wallet')) ? str_replace('\\', '', $this->lang->line('add_money_to_wallet')) : 'Add money to wallet' ?></h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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


                                        <div class="align-items-start d-flex flex-column pl-4">
                                            <div class="ship-title-detail">
                                                <h5><?= !empty($this->lang->line('select_payment_method')) ? str_replace('\\', '', $this->lang->line('select_payment_method')) : 'Payment Method' ?></h5>
                                            </div>
                                            <div class="shipped-details">
                                                <table class="table table-step-shipping">
                                                    <tbody>

                                                        <?php if (isset($payment_methods['paypal_payment_method']) && $payment_methods['paypal_payment_method'] == 1) { ?>
                                                            <tr>
                                                                <td class="tab_border">
                                                                    <label for="paypal">
                                                                        <input id="paypal" class="form-check-input m-0" name="payment_method" type="radio" value="Paypal" required>
                                                                    </label>
                                                                </td>
                                                                <td class="tab_border">
                                                                    <label for="paypal">
                                                                        <img src="<?= THEME_ASSETS_URL . 'img/payments/paypal.png' ?>" class="payment-gateway-images" alt="Paypal">
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
                                                                        <input id="razorpay" class="form-check-input m-0" name="payment_method" type="radio" value="Razorpay" required>
                                                                    </label>
                                                                </td>
                                                                <td class="tab_border">
                                                                    <label for="razorpay">
                                                                        <img src="<?= THEME_ASSETS_URL . 'img/payments/razorpay.png' ?>" class="payment-gateway-images" alt="Razorpay">
                                                                    </label>
                                                                </td>
                                                                <td class="tab_border">
                                                                    <label for="razorpay">
                                                                        RazorPay
                                                                    </label>
                                                                    <input type="hidden" class="form-check-input m-0" name="razorpay_order_id" id="razorpay_order_id" value="" />
                                                                    <input type="hidden" class="form-check-input m-0" name="razorpay_payment_id" id="razorpay_payment_id" value="" />
                                                                    <input type="hidden" class="form-check-input m-0" name="razorpay_signature" id="razorpay_signature" value="" />
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                        <?php if (isset($payment_methods['paystack_payment_method']) && $payment_methods['paystack_payment_method'] == 1) { ?>
                                                            <tr>
                                                                <td class="tab_border">
                                                                    <label for="paystack">
                                                                        <input id="paystack" class="form-check-input m-0" name="payment_method" type="radio" value="Paystack" required>
                                                                    </label>
                                                                </td>
                                                                <td class="tab_border">
                                                                    <label for="paystack">
                                                                        <img src="<?= THEME_ASSETS_URL . 'img/payments/paystack.png' ?>" class="payment-gateway-images" alt="Paystack">
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
                                                                        <input id="payumoney" class="form-check-input m-0" name="payment_method" type="radio" value="Payumoney" required>
                                                                    </label>
                                                                </td>
                                                                <td class="tab_border">
                                                                    <label for="payumoney">
                                                                        <img src="<?= THEME_ASSETS_URL . 'img/payments/payumoney.png' ?>" class="payment-gateway-images" alt="Payumoney">
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
                                                                        <input id="flutterwave" class="form-check-input m-0" name="payment_method" type="radio" value="Flutterwave" required>
                                                                    </label>
                                                                </td>
                                                                <td class="tab_border">
                                                                    <label for="flutterwave">
                                                                        <img src="<?= THEME_ASSETS_URL . 'img/payments/flutterwave.png' ?>" class="payment-gateway-images" alt="Flutterwave">
                                                                    </label>
                                                                </td>
                                                                <td class="tab_border">
                                                                    <label for="flutterwave">
                                                                        Flutterwave
                                                                    </label>
                                                                    <?php foreach ($name as $username) { ?>
                                                                        <input type="hidden" class="form-check-input m-0" name="flutterwave_public_key" id="flutterwave_public_key" value="<?= $payment_methods['flutterwave_public_key'] ?>" />
                                                                        <input type="hidden" class="form-check-input m-0" name="flutterwave_transaction_id" id="flutterwave_transaction_id" value="" />
                                                                        <input type="hidden" class="form-check-input m-0" name="flutterwave_transaction_ref" id="flutterwave_transaction_ref" value="" />
                                                                        <input type="hidden" class="form-check-input m-0" name="promo_set" id="promo_set" value="" />
                                                                    <?php  } ?>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                        <?php if (isset($payment_methods['paytm_payment_method']) && $payment_methods['paytm_payment_method'] == 1) { ?>
                                                            <tr>
                                                                <td class="tab_border">
                                                                    <label for="paytm">
                                                                        <input id="paytm" class="form-check-input m-0" name="payment_method" type="radio" value="Paytm" required>
                                                                    </label>
                                                                </td>
                                                                <td class="tab_border">
                                                                    <label for="paytm">
                                                                        <img src="<?= THEME_ASSETS_URL . 'img/payments/paytm.png' ?>" class="payment-gateway-images" alt="Paytm">
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
                                                                        <input id="stripe" class="form-check-input m-0" name="payment_method" type="radio" value="Stripe" required>
                                                                    </label>
                                                                </td>
                                                                <td class="tab_border">
                                                                    <label for="stripe">
                                                                        <img src="<?= THEME_ASSETS_URL . 'img/payments/stripe.png' ?>" class="payment-gateway-images" alt="Stripe">
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
                                                                        <input id="midtrans" class="form-check-input m-0" name="payment_method" type="radio" value="Midtrans" required>
                                                                    </label>
                                                                </td>
                                                                <td class="tab_border">
                                                                    <label for="midtrans">
                                                                        <img src="<?= THEME_ASSETS_URL . 'img/payments/midtrans.jpg' ?>" class="payment-gateway-images" alt="Midtrans">
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
                                                                        <input id="my_fatoorah" class="form-check-input m-0" name="payment_method" type="radio" value="my_fatoorah" required>
                                                                    </label>
                                                                </td>
                                                                <td class="tab_border">
                                                                    <label for="my_fatoorah">
                                                                        <img src="<?= THEME_ASSETS_URL . 'img/payments/myfatoorah.jpg' ?>" class="payment-gateway-images" alt="myfatoorah">
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
                                                                        <input id="instamojo" class="form-check-input m-0" name="payment_method" type="radio" value="instamojo" required>
                                                                    </label>
                                                                </td>
                                                                <td class="tab_border">
                                                                    <label for="instamojo">
                                                                        <img src="<?= THEME_ASSETS_URL . 'img/payments/instamojo.png' ?>" class="payment-gateway-images" alt="instamojo">
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
                                                                        <img src="<?= THEME_ASSETS_URL . 'img/payments/phonepay-logo.png' ?>" class="payment-gateway-images" alt="phonepe">
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
                                        </div>
                                        <div class="modal-footer p-0 p-5">
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill" id="wallet_refill" value="Save"><?= !empty($this->lang->line('refill')) ? str_replace('\\', '', $this->lang->line('refill')) : 'Refill' ?></button>
                                            <button type="button" class="btn btn-danger btn-sm rounded-pill" data-bs-dismiss="modal"><?= !empty($this->lang->line('close')) ? str_replace('\\', '', $this->lang->line('close')) : 'Close' ?></button>
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
                                <div class="modal-headerp-0 p-6">
                                    <h4 class="modal-title price"><?= !empty($this->lang->line('withdraw_money')) ? str_replace('\\', '', $this->lang->line('withdraw_money')) : 'Withdraw Money' ?></h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>


                                </div>
                                <!-- Modal body -->
                                <div class="ship-details-wrapper mt-3 payment-methods">
                                    <input type="number" name="amount" id="withdrawal_amount" min="1" required class="col-md-11 ml-4 form-control" placeholder="<?= !empty($this->lang->line('withdraw_money')) ? str_replace('\\', '', $this->lang->line('withdraw_money')) : 'Withdrawal Money' ?>">

                                    <input type="hidden" id="user_id" name='user_id' value="<?= $_SESSION['user_id']  ?>" />


                                    <input type="text" name="payment_address" id="payment_address" class="col-md-11 ml-4 mt-3 ticket_msg form-control " id="message_input" placeholder="<?= !empty($this->lang->line('enter_bank_details')) ? str_replace('\\', '', $this->lang->line('enter_bank_details')) : 'Enter bank details' ?>">
                                    <br>



                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill" id="withdraw_amount" value="Save"><?= !empty($this->lang->line('withdraw')) ? str_replace('\\', '', $this->lang->line('withdraw')) : 'Withdraw' ?></button>
                                        <button type="button" class="btn btn-danger btn-sm rounded-pill" data-bs-dismiss="modal"><?= !empty($this->lang->line('close')) ? str_replace('\\', '', $this->lang->line('close')) : 'Close' ?></button>
                                    </div>
                                    <!-- </form> -->
                                </div>

                                <!-- Modal footer -->


                            </div>
                        </div>
                    </div>


                </div>
                <hr class="mt-5 mb-5">
                <div class="card-body">
                    <table class='' data-toggle="table" data-url="<?= base_url('my-account/get-wallet-transactions') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-query-params="customer_wallet_query_paramss" id="customer_wallet_query_paramss">
                        <thead class="thead-light">
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
            </div>
        </div>

    </div>
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
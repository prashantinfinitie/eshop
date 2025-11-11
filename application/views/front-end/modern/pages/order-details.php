<!-- breadcrumb -->
<div class="content-wrapper deeplink_wrapper">
    <section class="wrapper bg-soft-grape">
        <div class="container py-3 py-md-5">
            <nav class="d-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('my-account') ?>" class="text-decoration-none"><?= !empty($this->lang->line('dashboard')) ? str_replace('\\', '', $this->lang->line('dashboard')) : 'Dashboard' ?></a></li>
                    <li class="breadcrumb-item"><?= !empty($this->lang->line('orders')) ? str_replace('\\', '', $this->lang->line('orders')) : 'Orders' ?></li>
                    <?php if (isset($right_breadcrumb) && !empty($right_breadcrumb)) {
                        foreach ($right_breadcrumb as $row) {
                    ?>
                            <li class="breadcrumb-item"><?= $row ?></li>
                    <?php }
                    } ?>
                    <li class="breadcrumb-item active text-muted" aria-current="page"><?= !empty($this->lang->line('view_details')) ? str_replace('\\', '', $this->lang->line('view_details')) : 'View Details' ?></li>
                </ol>
            </nav>
            <!-- /nav -->
        </div>
        <!-- /.container -->
    </section>
</div>
<!-- end breadcrumb -->

<!-- Demo header-->
<section class="header settings-tab">
    <div class="container pb-15">
        <div class="row">
            <div class="col-md-12 orders-section settings-tab-content">
                <div class="mb-4 border-0 shadow-xl p-10">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between">
                            <div class="col">
                                <p class="text-muted"> <?= !empty($this->lang->line('order_id')) ? str_replace('\\', '', $this->lang->line('order_id')) : 'Order ID' ?><span class="font-weight-bold text-dark"> : <?= $order['id'] ?></span></p>
                                <p class="text-muted"> <?= !empty($this->lang->line('place_on')) ? str_replace('\\', '', $this->lang->line('place_on')) : 'Place On' ?><span class="font-weight-bold text-dark"> : <?= $order['date_added'] ?></span> </p>
                            </div>

                            <div class="flex-col my-auto">
                                <h6 class="ml-auto mr-3">
                                    <?php
                                    if ($order['order_items'][0]['is_already_cancelled'] != 1) { ?>

                                        <a target="_blank" href="<?= base_url('my-account/order-invoice/' . $order['id']) ?>" class='btn btn-xs btn-outline-primary'><?= !empty($this->lang->line('invoice')) ? str_replace('\\', '', $this->lang->line('invoice')) : 'Invoice' ?></a>
                                    <?php }
                                    // }
                                    ?>
                                    <a href="<?= base_url('my-account/orders/') ?>" class='btn btn-xs btn-outline-danger'><?= !empty($this->lang->line('back_to_list')) ? str_replace('\\', '', $this->lang->line('back_to_list')) : 'Back to List' ?></a>
                                </h6>
                            </div>
                        </div>

                        <?php if ($order['payment_method'] == "Bank Transfer") { ?>
                            <div class="row">
                                <form class="form-horizontal " id="send_bank_receipt_form" action="<?= base_url('cart/send-bank-receipt'); ?>" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <div class="form-group ">
                                        <label for="receipt"> <strong> <?= !empty($this->lang->line('bank_payment_receipt')) ? str_replace('\\', '', $this->lang->line('bank_payment_receipt')) : 'Bank Payment Receipt' ?></strong> </label>
                                        <input type="file" class="form-control" name="attachments[]" id="receipt" multiple />
                                    </div>
                                    <div class="form-group">
                                        <button type="reset" class="btn btn-outline-warning btn-xs"><?= !empty($this->lang->line('reset')) ? str_replace('\\', '', $this->lang->line('reset')) : 'Reset' ?></button>
                                        <button type="submit" class="btn btn-outline-blue btn-xs" id="submit_btn"><?= !empty($this->lang->line('send')) ? str_replace('\\', '', $this->lang->line('send')) : 'Send' ?></button>
                                    </div>
                                </form>

                            </div>
                        <?php } ?>
                        <div class="row">
                            <?php if (!empty($bank_transfer)) { ?>
                                <div class="col-md-6">
                                    <?php $i = 1;
                                    foreach ($bank_transfer as $row1) { ?>
                                        <small>[<a href="<?= base_url() . $row1['attachments'] ?>" target="_blank"><?= !empty($this->lang->line('attachment')) ? str_replace('\\', '', $this->lang->line('attachment')) : 'Attachment' ?> <?= $i ?> </a>]</small>
                                    <?php $i++;
                                    }
                                    if ($bank_transfer[0]['status'] == 0) { ?>
                                        <label class="badge badge-warning"><?= !empty($this->lang->line('pending')) ? str_replace('\\', '', $this->lang->line('pending')) : 'Pending' ?></label>
                                    <?php } else if ($bank_transfer[0]['status'] == 1) { ?>
                                        <label class="badge badge-danger"><?= !empty($this->lang->line('rejected')) ? str_replace('\\', '', $this->lang->line('rejected')) : 'Rejected' ?></label>
                                    <?php } else if ($bank_transfer[0]['status'] == 2) { ?>
                                        <label class="badge badge-primary"><?= !empty($this->lang->line('accepted')) ? str_replace('\\', '', $this->lang->line('accepted')) : 'Accepted' ?></label>
                                    <?php } else { ?>
                                        <label class="badge badge-danger"><?= !empty($this->lang->line('invalid_value')) ? str_replace('\\', '', $this->lang->line('invalid_value')) : 'Invalid Value' ?></label>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php foreach ($order['order_items'] as $key => $item) {
                        ?>

                            <div class="media flex-column flex-sm-row">
                                <div class="media-body ">

                                    <a href="<?= base_url('products/details/' . $item['slug']) ?>" class="text-decoration-none">
                                        <h5 class="bold"><?= ($key + 1) . '. ' . $item['name'] ?></h5>
                                    </a>
                                    <?php
                                    if (!empty($item['variant_values'])) {
                                        $values = explode(', ', $item['variant_values']);
                                        $attributes = explode(', ', $item['attr_name']);
                                        // Initialize an empty string to store the final output
                                        $output = '';
                                        // Iterate through both arrays simultaneously
                                        foreach ($attributes as $key => $attribute) {
                                            // Append the attribute name and corresponding value to the output string
                                            $output .= '<p class="mb-0 text-dark">' . $attribute . ': ' . $values[$key] . '</p>';
                                            // Add line break if it's not the last attribute
                                            if ($key < count($attributes) - 1) {
                                                $output .= ",";
                                            }
                                        }

                                    ?>
                                        <div class="d-flex gap-2 mb-0 text-dark"><?= $output ?></div>
                                    <?php } ?>
                                    <p class="text-muted mb-0"> <?= !empty($this->lang->line('quantity')) ? str_replace('\\', '', $this->lang->line('quantity')) : 'Quantity' ?> : <?= $item['quantity'] ?></p>
                                    <?php if ($item['otp'] != 0) { ?>
                                        <p class="text-muted"> <?= !empty($this->lang->line('otp')) ? str_replace('\\', '', $this->lang->line('otp')) : 'OTP' ?> <span class="font-weight-bold text-dark"> : <?= $item['otp'] ?></span> </p>
                                    <?php } ?>
                                    <?php
                                    if (!empty($item['url']) || !empty($item['shiprocket_order_tracking_url'])) { ?>
                                        <h6><?= !empty($this->lang->line('order_tracking_details')) ? str_replace('\\', '', $this->lang->line('order_tracking_details')) : 'Order tracking details' ?> : </h6>


                                        <?php if (isset($item['courier_agency']) && !empty($item['courier_agency'])) { ?>
                                            <p class="text-muted mb-0"><?= !empty($this->lang->line('courier_agency')) ? str_replace('\\', '', $this->lang->line('courier_agency')) : 'Courier Agency' ?> :<a href="<?= $item['url'] ?>" title="<?= !empty($this->lang->line('click_here_to_trace_the_order')) ? str_replace('\\', '', $this->lang->line('click_here_to_trace_the_order')) : 'click here to trace the order' ?>"><?= $item['courier_agency'] ?></a> </p>
                                            <p class="text-muted mb-0" data-toggle="tooltip" data-placement="top" title="<?= !empty($this->lang->line('copy_this_tracking_id_and_trace_your_order_with_courier_agency')) ? str_replace('\\', '', $this->lang->line('copy_this_tracking_id_and_trace_your_order_with_courier_agency')) : 'Copy this Tracking ID and trace your order with Courier Agency' ?>"> <?= !empty($this->lang->line('tracking_id')) ? str_replace('\\', '', $this->lang->line('tracking_id')) : 'Tracking ID' ?> <span class="font-weight-bold text-dark"> : <?= $item['tracking_id'] ?></span> </p>
                                        <?php } ?>
                                        <?php if (isset($item['url']) && !empty($item['url'])) { ?>
                                            <p class="text-muted mb-0">
                                                <?= !empty($this->lang->line('order_tracking_url')) ? str_replace('\\', '', $this->lang->line('order_tracking_url')) : 'Order Tracking Url' ?> :
                                                <a href="<?= $item['url'] ?>" title="<?= !empty($this->lang->line('click_here_to_trace_the_order')) ? str_replace('\\', '', $this->lang->line('click_here_to_trace_the_order')) : 'click here to trace the order' ?>">
                                                    <?= $item['url'] ?></a>
                                            </p>
                                        <?php } ?>
                                        <?php if (isset($item['shiprocket_order_tracking_url']) && !empty($item['shiprocket_order_tracking_url'])) { ?>
                                            <p class="text-muted mb-0">
                                                <?= !empty($this->lang->line('shiprocket_order_tracking_url')) ? str_replace('\\', '', $this->lang->line('shiprocket_order_tracking_url')) : 'Shiprocket Order Tracking Url' ?> :
                                                <a href="<?= $item['shiprocket_order_tracking_url'] ?>" title="<?= !empty($this->lang->line('click_here_to_trace_the_order')) ? str_replace('\\', '', $this->lang->line('click_here_to_trace_the_order')) : 'click here to trace the order' ?>">
                                                    <?= $item['shiprocket_order_tracking_url'] ?></a>
                                            </p>
                                    <?php }
                                    } ?>
                                    <h4 class="mt-3 mb-2 bold"> <span class="mt-5"><i><?= $settings['currency'] ?></i></span> <?= number_format(($item['price'] * $item['quantity']), 2) ?> <span class="small text-muted"></span></h4>
                                    <?php
                                    $status = ["awaiting", "received", "processed", "shipped", "delivered", "cancelled", "return_request_pending", "return_request_approved", "returned"];
                                    $cancelable_till = $item['cancelable_till'];
                                    $active_status = $item['active_status'];
                                    $cancellable_index = array_search($cancelable_till, $status);
                                    $active_index = array_search($active_status, $status);
                                    if (!$item['is_already_cancelled'] && $item['product_is_cancelable'] && $active_index <= $cancellable_index && $item['type'] != 'digital_product') { ?>
                                        <button class="btn btn-danger btn-xs update-order-item-cancel" data-status="cancelled" data-item-id="<?= $item['id'] ?>"><?= !empty($this->lang->line('cancel')) ? str_replace('\\', '', $this->lang->line('cancel')) : 'Cancel' ?></button>
                                    <?php } ?>
                                    <?php
                                    $order_date = $order['order_items'][0]['status'][3][1];
                                    if ($item['product_is_returnable'] && !$item['is_already_returned'] && isset($order_date) && !empty($order_date)) { ?>
                                        <?php
                                        $settings = get_settings('system_settings', true);
                                        $timestemp = strtotime($order_date);
                                        $date = date('Y-m-d', $timestemp);
                                        $today = date('Y-m-d');
                                        $return_till = date('Y-m-d', strtotime($order_date . ' + ' . $settings['max_product_return_days'] . ' days'));

                                        if ($today < $return_till && $item['type'] != 'digital_product') { ?>
                                            <div class="col my-auto p-0">
                                                <a class="update-order-item block btn btn-xs btn-danger text-white mt-3 m-0"
                                                    data-status="returned"
                                                    data-item-id="<?= $item['id'] ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#returnModal">
                                                    <?= !empty($this->lang->line('return')) ? str_replace('\\', '', $this->lang->line('return')) : 'Return' ?>
                                                </a>

                                            </div>
                                        <?php } ?>
                                    <?php } ?>

                                    <?php if ($item['type'] == 'digital_product' &&  $item['download_allowed'] == 1 && ($item['active_status'] == 'received' || $item['active_status'] == 'delivered')) {
                                        $download_link = $item['hash_link'];
                                    ?>
                                        <div class="media-body mt-3">
                                            <a href="<?= base_url('products/download_link_hash/' . $item['id']) ?>" title="<?= !empty($this->lang->line('download')) ? str_replace('\\', '', $this->lang->line('download')) : 'Download' ?>" class="btn btn-outline-info btn-xs gap-1"><i class="uil uil-download-alt"></i> <?= !empty($this->lang->line('download')) ? str_replace('\\', '', $this->lang->line('download')) : 'Download' ?></a>
                                        </div>
                                    <?php }
                                    if ($item['type'] == 'digital_product' &&  $item['download_allowed'] == 0) { ?>
                                        <div class="media-body mt-3">
                                            <span class="text-danger"><?= !empty($this->lang->line('you_will_receive_this_item_from_seller_via_email')) ? str_replace('\\', '', $this->lang->line('you_will_receive_this_item_from_seller_via_email')) : 'You will receive this item from seller via email' ?></span>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="align-self-center img-fluid">
                                    <a href="<?= base_url('products/details/' . $item['slug']) ?>"><img src="<?= $item['image_sm'] ?>" width="180 " height="180" class="logo-fit"></a>
                                </div>
                            </div>

                            <?php if ($item['type'] != 'digital_product') { ?>
                                <section class="wrapper bg-light">
                                    <div class="container py-8">
                                        <div class="d-flex gx-lg-8 gx-xl-12 gy-6 process-wrapper line overflow-auto" id="progressbar">
                                            <?php
                                            $status = array('received', 'processed', 'shipped', 'delivered');
                                            $i = 1;
                                            foreach ($item['status'] as $value) { ?>
                                                <?php
                                                $class = '';
                                                if ($value[0] == "cancelled" || $value[0] == "returned" || $value[0] == "return_request_pending") {
                                                    $class = 'cancel';
                                                    $status = array();
                                                } elseif (($ar_key = array_search($value[0], $status)) !== false) {
                                                    unset($status[$ar_key]);
                                                }
                                                ?>
                                                <div class="col-md-3 col-lg-2 active <?= $class ?>">
                                                    <span class="icon btn btn-circle btn-primary pe-none mb-2">
                                                        <span class="number" id="step<?= $i ?>"></span>
                                                    </span>
                                                    <h5 class="mb-0"><?= str_replace('_', ' ', strtoupper($value[0]))  ?></h5>
                                                    <p class="mb-0"><?= $value[1] ?></p>
                                                </div>
                                            <?php
                                                $i++;
                                            } ?>

                                            <?php

                                            foreach ($status as $value) { ?>
                                                <div class="col-md-6 col-lg-2">
                                                    <span class="icon btn btn-circle btn-soft-primary pe-none mb-2">
                                                        <span class="number" id="step<?= $i ?>"></span>
                                                    </span>
                                                    <p class="mb-0"><?= str_replace('_', ' ', strtoupper($value)) ?></p>
                                                </div>
                                            <?php $i++;
                                            } ?>

                                        </div>
                                        <!--/.row -->
                                    </div>
                                    <!-- /.container -->
                                </section>
                                <!-- /section -->
                            <?php } ?>
                            <hr class="mt-5 mb-5">
                        <?php  } ?>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="h5"><?= !empty($this->lang->line('shipping_details')) ? str_replace('\\', '', $this->lang->line('shipping_details')) : 'Shipping Details' ?></h6>
                                <hr class="mt-5 mb-5">
                                <span><?= $order['username'] ?></span> <br>
                                <span><?= $order['address'] ?></span> <br>
                                <span><?= $order['mobile'] ?></span> <br>
                                <span><?= $order['delivery_time'] ?></span> <br>
                                <span><?= $order['delivery_date'] ?></span> <br>
                            </div>
                            <div class="col-md-6">
                                <h6 class="h5"><?= !empty($this->lang->line('price_details')) ? str_replace('\\', '', $this->lang->line('price_details')) : 'Price Details' ?></h6>
                                <hr class="mt-5 mb-5">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th><?= !empty($this->lang->line('total_order_price')) ? str_replace('\\', '', $this->lang->line('total_order_price')) : 'Total Order Price' ?></th>
                                                <td>+ <?= $settings['currency'] . ' ' . number_format($order['total'], 2) ?></td>
                                            </tr>
                                            <?php if ($item['type'] != 'digital_product') { ?>
                                                <tr>
                                                    <th><?= !empty($this->lang->line('delivery_charge')) ? str_replace('\\', '', $this->lang->line('delivery_charge')) : 'Delivery Charge' ?></th>
                                                    <td>+ <?= $settings['currency'] . ' ' . number_format($order['delivery_charge'], 2) ?></td>
                                                </tr>
                                            <?php } ?>
                                            <tr class="d-none">
                                                <th><?= !empty($this->lang->line('tax')) ? str_replace('\\', '', $this->lang->line('tax')) : 'Tax' ?> - (<?= $order['total_tax_percent'] ?>%)</th>
                                                <td>+ <?= $settings['currency'] . ' ' . number_format($order['total_tax_amount'], 2) ?></td>
                                            </tr>
                                            <?php if (!empty($order['promo_code']) && !empty($order['promo_discount'])) { ?>
                                                <tr>
                                                    <th><?= !empty($this->lang->line('promocode_discount')) ? str_replace('\\', '', $this->lang->line('promocode_discount')) : 'Promocode Discount' ?> - (<?= $order['promo_code'] ?>)
                                                    </th>
                                                    <td>- <?= $settings['currency'] . ' ' . number_format($order['promo_discount'], 2) ?></td>
                                                </tr>
                                            <?php } ?>
                                            <tr>
                                                <th><?= !empty($this->lang->line('wallet_used')) ? str_replace('\\', '', $this->lang->line('wallet_used')) : 'Wallet Used' ?></th>
                                                <td>- <?= $settings['currency'] . ' ' . number_format($order['wallet_balance'], 2) ?></td>
                                            </tr>
                                            <tr class="h6">
                                                <th><?= !empty($this->lang->line('final_total')) ? str_replace('\\', '', $this->lang->line('final_total')) : 'Final Total' ?></th>
                                                <td><?= $settings['currency'] . ' ' . number_format($order['final_total'], 2) ?><span class="small text-muted"> <?= !empty($this->lang->line('via')) ? str_replace('\\', '', $this->lang->line('via')) : 'via' ?> (<?= $order['payment_method'] ?>) </span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- /.col -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white px-sm-3 pt-sm-4 px-0">
                        <div class="row text-center ">
                            <?php
                            $status = ["awaiting", "received", "processed", "shipped", "delivered", "cancelled",  "return_request_pending", "return_request_approved", "returned"];
                            $cancelable_till = $item['cancelable_till'];
                            $active_status = $item['active_status'];
                            $cancellable_index = array_search($cancelable_till, $status);
                            $active_index = array_search($active_status, $status);
                            if (!$item['is_already_cancelled'] && $item['product_is_cancelable'] && $active_index <= $cancellable_index) { ?>
                                <div class="col my-auto">
                                    <a class="update-order block btn-xs btn btn-danger text-white mt-3 m-0" data-status="cancelled" data-order-id="<?= $order['id'] ?>"><?= !empty($this->lang->line('cancel')) ? str_replace('\\', '', $this->lang->line('cancel')) : 'Cancel' ?></a>
                                </div>
                            <?php } ?>
                            <?php
                            $order_date = $order['order_items'][0]['status'][3][1];
                            if ($order['product_is_returnable'] && !$order['is_already_returned'] && isset($order_date) && !empty($order_date)) { ?>
                                <?php
                                $settings = get_settings('system_settings', true);

                                $timestemp = strtotime($order_date);
                                $date = date('Y-m-d', $timestemp);
                                $today = date('Y-m-d');
                                $return_till = date('Y-m-d', strtotime($order_date . ' + ' . $settings['max_product_return_days'] . ' days'));

                                if ($today < $return_till) { ?>
                                    <div class="col my-auto ">
                                        <a class="update-order block buttons button-sm btn-6-3 mt-3 m-0" data-status="returned" data-order-id="<?= $order['id'] ?>"><?= !empty($this->lang->line('return')) ? str_replace('\\', '', $this->lang->line('return')) : 'Return' ?></a>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>


                    <!-- Return Item Modal -->
                    <div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header py-7">
                                    <h3 class="modal-title" id="returnModalLabel">Return Item</h3>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body py-0">
                                    <div class="d-flex flex-column flex-wrap gap-2 mb-3">
                                        <input type="hidden" id="returnItemId" value="<?= $item['id'] ?>">
                                        <input type="hidden" id="status" value="returned">

                                        <!-- Predefined reason option -->
                                        <?php foreach ($return_reasons as $return_reason) { ?>
                                            <label class="return-reason-card py-1 d-flex align-items-center border rounded cursor-pointer">
                                                <input type="radio" name="return_reason" value="<?= $return_reason['return_reason'] ?>" class="reason-radio me-2">
                                                <img src="<?= base_url() . rawurlencode($return_reason['image']) ?>" alt="Reason Icon" class="me-2">
                                                <p class="fs-14 mb-0"><?= $return_reason['return_reason'] ?></p>
                                            </label>
                                        <?php } ?>

                                        <!-- "Other" option -->
                                        <label class="return-reason-card py-1 d-flex align-items-center border rounded cursor-pointer">
                                            <img src="<?= base_url() . NO_IMAGE ?>" alt="Reason Icon" class="me-2">
                                            <input type="radio" name="return_reason" value="other" class="me-2 reason-radio" id="otherReasonRadio">
                                            <p class="fs-14 mb-0"><?= !empty($this->lang->line('other')) ? str_replace('\\', '', $this->lang->line('other')) : 'Other' ?></p>
                                        </label>

                                        <!-- Text field for "Other" reason (hidden by default) -->
                                        <input type="text" id="otherReasonField" class="form-control mt-2" placeholder="Enter your reason" style="display: none;">
                                        <!-- Image Upload Section -->
                                        <div class="mb-3">
                                            <label for="returnImage" class="form-label">Upload Image of Item</label>
                                            <input type="file" class="form-control" id="return_item_image" name="return_item_images[]" accept="image/*" multiple>
                                        </div>
                                    </div>

                                </div>

                                <div class="modal-footer py-3">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-danger btn-sm confirmReturn" id="confirmReturn">Confirm Return</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</section>
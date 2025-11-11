<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>View Order</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('delivery_boy/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Orders</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info overflow-auto">
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <input type="hidden" name="hidden" id="order_id"
                                        value="<?php echo $order_detls['order_id']; ?>">
                                    <th class="w-10px">Order ID</th>
                                    <td><?php echo $order_detls['order_id']; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Buyer Name</th>
                                    <td><?php echo $order_detls[0]['username']; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Email</th>
                                    <td><?= (ALLOW_MODIFICATION == 0 && !defined(ALLOW_MODIFICATION)) ? str_repeat("X", strlen($order_detls[0]['email']) - 3) . substr($order_detls[0]['email'], -3) : $order_detls[0]['email'] ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Contact</th>

                                    <td><?= (!defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($order_detls[0]['mobile']) - 3) . substr($order_detls[0]['mobile'], -3) : $order_detls[0]['mobile']; ?>
                                    </td>
                                </tr>
                                <?php if (!empty($order_detls['notes'])) { ?>
                                    <tr>
                                        <th class="w-10px">Order note</th>
                                        <td><?php echo $order_detls['notes']; ?></td>
                                    </tr>
                                <?php } ?>
                                <?php
                                $sellers = array_values(array_unique(array_column($order_detls, "seller_id")));
                                ?>
                                <tr>
                                    <th class="w-10px">Items</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <?php
                                        for ($i = 0; $i < count($sellers); $i++) {
                                            $seller_data = fetch_details('users', ['id' => $sellers[$i]], 'username');
                                            $seller_otp_data = fetch_details('order_items', ['order_id' => $order_detls['order_id'], 'seller_id' => $sellers[$i]], ['otp', 'deliveryboy_otp_setting_on']);
                                            $seller_otp = $seller_otp_data[0]['otp'];
                                            $otp_system = $seller_otp_data[0]['deliveryboy_otp_setting_on'];

                                            $total = 0;
                                            $tax_amount = 0;
                                            ?>
                                            <div class="card card-info mb-3 mt-2 ">
                                                <div class="card-body">
                                                    <div class="col-md-6 m-2 text-left">
                                                        <strong>
                                                            <p class="mb-0">Seller :
                                                        </strong>
                                                        <?= ucfirst($seller_data[0]['username']) ?></p>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">#</th>
                                                                    <th scope="col">Name</th>
                                                                    <th scope="col">Image</th>
                                                                    <th scope="col">Quantity</th>
                                                                    <th scope="col">Product Type</th>
                                                                    <th scope="col">Variant ID</th>
                                                                    <th scope="col">Discounted Price</th>
                                                                    <th scope="col">Subtotal</th>
                                                                    <th scope="col">Active Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $badges = ["draft" => "secondary", "awaiting" => "secondary", "received" => "primary", "processed" => "info", "shipped" => "warning", "delivered" => "success", "returned" => "danger", "cancelled" => "danger", "return_request_approved" => "danger", "return_request_decline" => "danger", "return_request_pending" => "danger", "return_pickedup" => "secondary"];
                                                                echo '<div class="container-fluid row">';
                                                                foreach ($items as $item) {
                                                                    $item['discounted_price'] = ($item['discounted_price'] == '') ? 0 : $item['discounted_price'];
                                                                    $total += $subtotal = ($item['quantity'] != 0 && ($item['discounted_price'] != '' && $item['discounted_price'] > 0) && $item['price'] > $item['discounted_price']) ? ($item['price'] - $item['discounted_price']) : ($item['price'] * $item['quantity']);
                                                                    $tax_amount += $item['tax_amount'];
                                                                    if ($sellers[$i] == $item['seller_id']) {
                                                                        ?>
                                                                        <tr>
                                                                            <th scope="row"><?= $index + 1 ?></th>
                                                                            <td><?= $item['pname'] ?></td>
                                                                            <td><a href='<?= $item['product_image'] ?>'
                                                                                    class="image-box-100" data-toggle='lightbox'
                                                                                    data-gallery='order-images'> <img
                                                                                        src='<?= $item['product_image'] ?>'
                                                                                        alt="<?= $item['pname'] ?>"></a></td>
                                                                            <td><?= $item['quantity'] ?></td>
                                                                            <td><?= str_replace('_', ' ', $item['product_type']) ?>
                                                                            </td>
                                                                            <td><?= $item['product_variant_id'] ?></td>
                                                                            <td><?= ($item['discounted_price'] == null) ? "0" : round($item['discounted_price'], 2) ?>
                                                                            </td>
                                                                            <td><?= round($item['price'], 2) ?></td>
                                                                            <td><span
                                                                                    class="text-uppercase p-1 status-<?= $item['id'] ?> badge badge-<?= $badges[$item['active_status']] ?>"><?= str_replace('_', ' ', ($item['active_status'] == 'draft' ? "awaiting" : $item['active_status'])) ?></span>
                                                                            </td>
                                                                        </tr>
                                                                        <input type="hidden" class="product_variant_id"
                                                                            name="product_variant_id"
                                                                            value="<?= $item['product_variant_id'] ?>">
                                                                        <input type="hidden" class="product_name"
                                                                            name="product_name" value="<?= $item['pname'] ?>">
                                                                        <input type="hidden" class="order_item_id"
                                                                            name="order_item_id" value="<?= $item['id'] ?>">
                                                                    <?php }
                                                                } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <h5 class="text-middle-line" type="button"><span>Update
                                                                Status</span></h5>
                                                    </div>
                                                    <select name="status" class="form-control consignment_status mb-3">
                                                        <option value=''>Select Status</option>
                                                        <option value="received" <?= $item['active_status'] == 'received' ? "selected" : "" ?>>Received</option>
                                                        <option value="processed" <?= $item['active_status'] == 'processed' ? "selected" : "" ?>>Processed</option>
                                                        <option value="shipped" <?= $item['active_status'] == 'shipped' ? "selected" : "" ?>>Shipped</option>
                                                        <option value="delivered" <?= $item['active_status'] == 'delivered' ? "selected" : "" ?>>Delivered</option>
                                                    </select>

                                                    <?php if ($otp_system == 1 || $otp_system == '1') { ?>
                                                        <input type="number" name="otp" id="otp"
                                                            class="form-control my-2 d-none otp-field"
                                                            placeholder="Enter Otp Here">
                                                    <?php } ?>
                                                    <div class="d-flex justify-content-end align-items-center">
                                                        <button type="button"
                                                            class="btn btn-primary update_status_delivery_boy"
                                                            data-id='<?= $order_detls['consignment_id'] ?>'
                                                            data-otp-system='<?= $otp_system != 0 ? '1' : '0' ?>'>Submit</button>
                                                    </div>
                                                    <?php
                                                    if (isset($order_detls['discount']) && $order_detls['discount'] > 0) {
                                                        $discount = $order_detls['total_payable'] * ($order_detls['discount'] / 100);
                                                        $total = round($order_detls['total_payable'] - $discount, 2);
                                                    }
                                                    if ($order_detls['payment_method'] == "COD" && $order_detls['is_cod_collected'] == 1) { ?>
                                                        <p class="m-0 mt-2 font-weight-bold h5 text-success">Cash Collected</p>
                                                    <?php } elseif ($order_detls['payment_method'] != "COD") { ?>
                                                        <p class="m-0 mt-2 font-weight-bold h5 text-success">Payment Online Done
                                                        </p>
                                                    <?php } elseif ($order_detls['payment_method'] == "COD" && $order_detls['is_cod_collected'] == 0) { ?>
                                                        <p class="m-0 mt-2 font-weight-bold h5">Cash On Delivery. Collect <span
                                                                class="text-middle-line"><?= $settings['currency'] . intval($total + $order_detls['delivery_charge'] - $order_detls['wallet_balance'] - $order_detls['promo_discount']) ?></span>
                                                        </p>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Total(<?= $settings['currency'] ?>)</th>
                                    <td id='amount'><?php echo round($total, 2); ?></td>
                                </tr>

                                <tr>
                                    <th class="w-10px">Tax(<?= $settings['currency'] ?>)</th>
                                    <td id='amount'><?php echo round($tax_amount, 2); ?> <small>(All Tax Included In
                                            Total)</small></td>
                                </tr>

                                <tr>
                                    <th class="w-10px">Delivery Charge(<?= $settings['currency'] ?>)</th>
                                    <td id='delivery_charge'><?php echo $order_detls['delivery_charge'];
                                    $total = $total + $order_detls['delivery_charge']; ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="w-10px">Wallet Balance(<?= $settings['currency'] ?>)</th>
                                    <td><?php echo number_format($order_detls['wallet_balance'], 2);
                                    $total = $total - $order_detls['wallet_balance']; ?></td>
                                </tr>

                                <input type="hidden" name="total_amount" id="total_amount"
                                    value="<?php echo $order_detls['order_total'] + $order_detls['delivery_charge'] ?>">
                                <input type="hidden" name="final_amount" id="final_amount"
                                    value="<?php echo $order_detls['final_total']; ?>">
                                <input type="hidden" name="delivery_boy_otp_system" id="delivery_boy_otp_system"
                                    value="<?= $otp_system ?>">
                                <tr>
                                    <th class="w-10px">Discount %</th>
                                    <td>
                                        <?=
                                            $order_detls[0]['discount']; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Promo Code Discount (<?= $settings['currency'] ?>)</th>
                                    <td><?php if ($order_detls['total_promo_discount'] != $order_detls['promo_discount']) {
                                        echo "Total Promo Code Discount Is <b>" . $order_detls['total_promo_discount'] . "</b> and <b>" . number_format($order_detls['promo_discount'], 2) . "</b> Used For This Items";
                                    } else {
                                        echo $order_detls['promo_discount'];
                                    }
                                    $total = floatval($total - $order_detls['promo_discount']); ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Payable Total(<?= $settings['currency'] ?>)</th>
                                    <td><?= ($order_detls['payment_method'] == "COD" && $order_detls['is_cod_collected'] == 0) ? (number_format(intval($total), 2)) : "0" ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Deliver By</th>
                                    <td>
                                        <?php
                                        $delivery_res = fetch_details('users', ['id' => $order_detls['delivery_boy_id']], 'username');
                                        echo $delivery_res[0]['username'];
                                        ?>

                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="w-10px">Payment Method</th>
                                    <td><?php echo $order_detls['payment_method']; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Address</th>
                                    <td><?php echo $order_detls['address']; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Delivery Date & Time</th>
                                    <td><?php echo (!empty($order_detls[0]['delivery_date']) && $order_detls[0]['delivery_date'] != NUll) ? date('d-M-Y', strtotime($order_detls[0]['delivery_date'])) . " - " . $order_detls[0]['delivery_time'] : "Anytime"; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Order Date</th>
                                    <td><?php echo date('d-M-Y', strtotime($order_detls[0]['created_date'])); ?></td>
                                </tr>

                            </table>
                        </div>

                    </div>
                    <!--/.card-->
                </div>
                <!--/.col-md-12-->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
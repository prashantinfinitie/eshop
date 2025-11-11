<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Invoice</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('seller/home'); ?>">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Invoice</li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info " id="section-to-print">
                        <div class="row m-3">
                            <div class="col-md-12 d-flex justify-content-between">
                                <h2 class="text-left">
                                    <img src="<?= base_url()  . get_settings('logo') ?>" class="d-block invoice_logo">
                                </h2>
                                <h4 class="text-right">
                                    Mo. <?= (isset($s_user_data[0]['country_code']) && !empty($s_user_data[0]['country_code']))  ? "+" . $s_user_data[0]['country_code'] . " " . $s_user_data[0]['mobile'] : "+91 " . $s_user_data[0]['mobile'] ?>
                                </h4>
                            </div>
                            <!-- /.col -->
                        </div>
                        <?php

                        $order_caharges_data = fetch_details('order_charges', ['order_id' => $order_detls[0]['order_id'], 'seller_id' => $order_detls[0]['seller_id']]);
                        ?>
                        <!-- info row -->
                        <div class="row m-3 mt-3">
                            <div class="col-md-3">
                                <strong>
                                    <p>Sold By</p>
                                </strong>
                                <address>
                                    <?= $settings['app_name'] ?><br>
                                    <b>Email</b> : <?= $s_user_data[0]['email'] ?><br>
                                    <b>Customer Care</b> : <?= (isset($s_user_data[0]['country_code']) && !empty($s_user_data[0]['country_code'])) ? "+" . $s_user_data[0]['country_code'] . " " . $s_user_data[0]['mobile'] : "+91 " . $s_user_data[0]['mobile'] ?><br>
                                    <?php if (isset($seller_data[0]['store_name']) && !empty($seller_data[0]['store_name'])) { ?>
                                        <b>Store Name</b> : <?= $seller_data[0]['store_name'] ?><br>
                                    <?php } ?>
                                    <?php if (isset($seller_data[0]['tax_name']) && !empty($seller_data[0]['tax_name'])) { ?>
                                        <b>Tax Name</b> : <?= $seller_data[0]['tax_name'] ?><br>
                                    <?php } ?>
                                    <?php if (isset($seller_data[0]['tax_number']) && !empty($seller_data[0]['tax_number'])) { ?>
                                        <b>Tax Number</b> : <?= $seller_data[0]['tax_number'] ?><br>
                                    <?php } ?>
                                    <b> Address  </b> : <?=  str_replace('\\', '', $s_user_data[0]['address']); ?>
                                </address>
                                <?php if (isset($seller_data[0]['pan_number']) && !empty($seller_data[0]['pan_number'])) { ?>
                                    <p><b>PAN NO.</b> : <?= $seller_data[0]['pan_number'] ?></p>
                                <?php } ?>
                                <?php if (!empty($items[0]['delivery_boy'])) { ?><strong>Delivery By: </strong><?= $items[0]['delivery_boy'] ?><?php } ?><br>
                            </div>
                            <div class="col-md-6"></div>
                            <!-- /.col -->
                            <div class="col-md-3">
                                <strong>
                                    <p>Shipping Address</p>
                                </strong>

                                <address>
                                    <?= ($order_detls[0]['user_name'] != "") ? $order_detls[0]['user_name'] : $order_detls[0]['uname'] ?>
                                    <?= $order_detls[0]['address'] ?><br>
                                    <?= ((!defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) || ($this->ion_auth->is_seller() && get_seller_permission($order_detls[0]['seller_id'], 'customer_privacy') == false)) ? (!empty($order_detls[0]['mobile']) ? (str_repeat("X", strlen($order_detls[0]['mobile']) - 3) . substr($order_detls[0]['mobile'], -3)) : $order_detls[0]['mobile_number']) : $order_detls[0]['mobile']; ?><br>
                                    <?= ((!defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) || ($this->ion_auth->is_seller() && get_seller_permission($order_detls[0]['seller_id'], 'customer_privacy') == false)) ? str_repeat("X", strlen($order_detls[0]['email']) - 3) . substr($order_detls[0]['email'], -3) : $order_detls[0]['email']; ?><br>
                                </address>
                                <p><strong>Order No : </strong>#<?= $order_detls[0]['id'] ?><br>
                                    <strong>Order Date : </strong><?= $order_detls[0]['date_added'] ?>
                                </p>

                            </div>

                        </div>

                        <!-- Table row -->
                        <div class="row m-4">
                            <p>Product Details:</p>
                        </div>
                        <div class="row m-4">
                            <div class="col-md-12 table-responsive">
                                <table class="table borderless text-center text-sm">
                                    <thead class="">
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Product Code</th>
                                            <th>Name</th>
                                            <th>variants</th>
                                            <th>HSN Code</th>
                                            <th>Price</th>
                                            <th>Tax (%)</th>
                                            <th>Tax Amount (<?= $settings['currency'] ?>)</th>
                                            <th>Qty</th>
                                            <th>SubTotal (<?= $settings['currency'] ?>)</th>
                                            <th class="d-none">Order Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1;
                                        $total = $quantity = $total_tax = $total_discount = $cal_final_total = 0;
                                        foreach ($items as $row) {

                                            $total_tax = 0;
                                            $product_id = $row['product_id'];  // ensure $row['product_id'] contains the correct product ID

                                            $order_tax_ids = (isset($row['tax_ids']) && !empty($row['tax_ids'])) ? explode(',', $row['tax_ids']) : array();
                                            
                                            $taxes = [];
                                            foreach ($order_tax_ids as $tax_id) {
                                                $tax = getTtaxById($tax_id);
                                                if ($tax) {
                                                    $taxes[] = $tax;
                                                }
                                            }

                                            $total += floatval($row['price'] + $tax_amount) * floatval($row['quantity']);

                                            $product_variants = get_variants_values_by_id($row['product_variant_id']);
                                            $product_variants = isset($product_variants[0]['variant_values']) && !empty($product_variants[0]['variant_values']) ? str_replace(',', ' | ', $product_variants[0]['variant_values']) : '-';
                                            $tax_amount  = $row['price'] - ($row['price'] * (100 / (100 + floatval($row['tax_percent']))));
                                            $hsn_code = ($row['hsn_code']) ? $row['hsn_code'] : '-';

                                            $quantity += floatval($row['quantity']);

                                            $price_without_tax = $row['price'] - $tax_amount;
                                            $sub_total = floatval($row['price']) * $row['quantity'];
                                            $final_sub_total += $sub_total;
                                        ?>
                                            <tr>
                                                <td><?= $j ?><br></td>
                                                <td><?= $row['product_variant_id'] ?><br></td>
                                                <td class="w-25"><?= $row['pname'] ?><br></td>
                                                <td class="w-25"><?= $product_variants ?><br></td>
                                                <td><?= $hsn_code ?><br></td>
                                                <td><?= $settings['currency'] . ' ' . number_format($price_without_tax, 2) ?><br></td>
                                                <td><?php foreach ($taxes as $tax) { ?>
                                                        <div class="d-flex"><span><?= $tax['title'] ?></span>
                                                            <span>-</span>
                                                            <span><?= $tax['percentage'] . '%' ?> </span>
                                                        </div>
                                                    <?php } ?>
                                                </td>

                                                <td><?php foreach ($taxes as $tax) { ?>
                                                        <div class="d-flex"><span><?= $tax['title'] ?></span>
                                                            <span>-</span>
                                                            <?php $total_tax += ($price_without_tax * $tax['percentage']) / 100 ?>
                                                            <span><?= number_format(($price_without_tax * $tax['percentage']) / 100, 2)  ?> </span>
                                                        </div>
                                                    <?php }
                                                    ?>
                                                    <div class="d-flex">
                                                        <span><b><?= 'Total - ' . number_format($total_tax, 2)   ?></b></span>
                                                    </div>
                                                </td>
                                                <td><?= $row['quantity'] ?><br></td>
                                                <td><?= $settings['currency'] . ' ' . number_format($sub_total, 2); ?><br></td>
                                                <td class="d-none"><?= $row['active_status'] ?><br></td>
                                            </tr>
                                        <?php $i++;
                                            $cal_final_total += ($sub_total);
                                        }

                                        ?>
                                    </tbody>
                                    <tbody>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th>Total</th>
                                            <th class="d-none">
                                                <?= $total_tax ?>
                                            </th>
                                            <th>
                                                <?= $quantity ?>
                                                <br>
                                            </th>
                                            <th>
                                                <?= $settings['currency'] . ' ' . number_format($cal_final_total, 2);  ?>
                                                <br>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                        </div>

                        <!-- /.row -->
                        <div class="row m-3 text-right">
                            <div class="col-md-6 text-left">
                                <b>Payment Method : </b> <?= $order_detls[0]['payment_method'] ?>
                            </div>

                            <!-- accepted payments column -->
                            <div class="col-md-6 text-right">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th>Total Order Price (
                                                    <?= $settings['currency'] ?>)</th>
                                                <td>+
                                                    <?= number_format($cal_final_total, 2) ?>
                                                </td>
                                            </tr>
                                            <?php if ($order_detls[0]['type'] != 'digital_product') { ?>
                                                <tr>
                                                    <th>Delivery Charge (
                                                        <?= $settings['currency'] ?>)</th>
                                                    <td>+
                                                        <?php $cal_final_total += $order_caharges_data[0]['delivery_charge'];
                                                        echo number_format($order_caharges_data[0]['delivery_charge'], 2); ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if (isset($order_detls[0]['wallet_balance']) && !empty($order_detls[0]['wallet_balance'])) { ?>
                                                <tr>
                                                    <th>Wallet Balance (
                                                        <?= $settings['currency'] ?>)</th>
                                                    <td>-
                                                        <?php $cal_final_total -= $order_detls[0]['wallet_balance'];
                                                        echo number_format($order_detls[0]['wallet_balance'], 2); ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>

                                            <tr class="d-none">
                                                <th>Tax
                                                    <?= $settings['currency'] ?></th>
                                                <td>+
                                                    <?php echo $total_tax; ?>
                                                </td>
                                            </tr>

                                            <?php
                                            if (isset($promo_code[0]['promo_code'])) { ?>
                                                <tr>
                                                    <th>Promo (
                                                        <?= $promo_code[0]['promo_code'] ?>) Discount (
                                                        <?= floatval($promo_code[0]['discount']); ?>
                                                        <?= ($promo_code[0]['discount_type'] == 'percentage') ? '%' : ' '; ?> )
                                                    </th>
                                                    <td>-
                                                        <?php
                                                        echo $order_caharges_data[0]['promo_discount'];
                                                        $cal_final_total = $cal_final_total - $order_caharges_data[0]['promo_discount'];
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php
                                            if (isset($order_detls[0]['discount']) && $order_detls[0]['discount'] > 0 && $order_detls[0]['discount'] != NULL) { ?>
                                                <tr>
                                                    <th>Special Discount
                                                        <?= $settings['currency'] ?>(<?= $order_detls[0]['discount'] ?> %)</th>
                                                    <td>-
                                                        <?php echo $special_discount = round($cal_final_total * $order_detls[0]['discount'] / 100, 2);
                                                        $cal_final_total = floatval($cal_final_total - $special_discount);
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                            <tr class="d-none">
                                                <th>Total Payable (
                                                    <?= $settings['currency'] ?>)</th>
                                                <td>
                                                    <?= $settings['currency'] . '  ' . number_format($cal_final_total, 2) ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Final Total (
                                                    <?= $settings['currency'] ?>)</th>
                                                <td>

                                                    <?= $settings['currency'] . '  ' . number_format($cal_final_total, 2); ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                        <?php if (isset($seller_data[0]['authorized_signature']) && !empty($seller_data[0]['authorized_signature'])) { ?>
                            <div class="row m-3">
                                <div class="col-md-6"></div>
                                <div class="col-md-6 text-right">
                                    <p><strong>For <?= ucfirst($seller_data[0]['store_name']); ?> :</strong></p>
                                    <div class="h-25"><img src='<?= base_url($seller_data[0]['authorized_signature']) ?>' id="<?= $sellers[$i] ?>" class="float-right product-image"></div><br><br>
                                    <p><strong>Authorized Signature</strong></p>
                                </div>
                            </div>
                        <?php } ?>
                        <!-- this row will not appear when printing -->
                        <div class="row m-3" id="section-not-to-print">
                            <div class="col-xs-12">
                                <button type='button' value='Print this page' onclick='{window.print()};' class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                            </div>
                        </div>
                    </div>
                    <!--/.card-->
                </div>
                <!--/.col-md-12-->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
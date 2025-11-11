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
                                    <input type="hidden" name="hidden" id="order_id" value="<?php echo $order_detls['order_id']; ?>">
                                    <th class="w-10px">Order ID</th>
                                    <td><?php echo $order_detls['order_id']; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Buyer Name</th>
                                    <td><?php echo $order_detls['uname']; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Email</th>
                                    <td><?= (ALLOW_MODIFICATION == 0 && !defined(ALLOW_MODIFICATION)) ? str_repeat("X", strlen($order_detls['email']) - 3) . substr($order_detls['email'], -3) : $order_detls['email'] ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Contact</th>

                                    <td><?= (!defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0)  ? str_repeat("X", strlen($order_detls['mobile_number']) - 3) . substr($order_detls['mobile_number'], -3) : $order_detls['mobile_number']; ?></td>
                                </tr>
                                <?php if (!empty($order_detls['notes'])) { ?>
                                    <tr>
                                        <th class="w-10px">Order note</th>
                                        <td><?php echo  $order_detls['notes']; ?></td>
                                    </tr>
                                <?php } ?>
                                <?php 
                                $sellers = $order_detls['seller_id']; 
                                
                                
                                ?>
                                <tr>
                                    <th class="w-10px">Items</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <?php
                                            $seller_data = fetch_details('users', ['id' => $sellers], 'username');
                                            $seller_otp = fetch_details('order_items', ['order_id' => $order_detls['order_id'], 'seller_id' => $sellers], 'otp')[0]['otp'];
                                            $system_settings = get_settings('system_settings', true);
                                            
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
                                                                $badges = ["draft" => "secondary", "awaiting" => "secondary", "received" => "primary", "processed" => "info", "shipped" => "warning", "delivered" => "success", "returned" => "danger", "cancelled" => "danger", "return_request_approved" => "danger", "return_request_decline" => "danger", "return_pickedup" => "secondary", "return_request_pending" => "danger"];
                                                                echo '<div class="container-fluid row">';
                                                                foreach ($items as $item) {
                                                                    $item['discounted_price'] = ($item['discounted_price'] == '') ? 0 : $item['discounted_price'];
                                                                    $total += $subtotal = ($item['quantity'] != 0 && ($item['discounted_price'] != '' && $item['discounted_price'] > 0) && $item['price'] > $item['discounted_price']) ? ($item['price'] - $item['discounted_price']) : ($item['price'] * $item['quantity']);
                                                                    $tax_amount += $item['tax_amount'];
                                                                ?>
                                                                        <tr>
                                                                            <th scope="row"><?= $index + 1 ?></th>
                                                                            <td><?= $item['pname'] ?></td>
                                                                            <td><a href='<?= $item['product_image'] ?>' class="image-box-100" data-toggle='lightbox' data-gallery='order-images'> <img src='<?= $item['product_image'] ?>' alt="<?= $item['pname'] ?>"></a></td>
                                                                            <td><?= $item['quantity'] ?></td>
                                                                            <td><?= str_replace('_', ' ', $item['product_type']) ?></td>
                                                                            <td><?= $item['product_variant_id'] ?></td>
                                                                            <td><?= ($item['discounted_price'] == null) ? "0" : round($item['discounted_price'], 2) ?></td>
                                                                            <td><?= round($total, 2) ?></td>
                                                                            <td><span class="text-uppercase p-1 status-<?= $item['id'] ?> badge badge-<?= $badges[$item['active_status']] ?>"><?= str_replace('_', ' ', ($item['active_status'] == 'draft' ? "awaiting" : $item['active_status'])) ?></span></td>
                                                                        </tr>
                                                                        <input type="hidden" class="product_variant_id" name="product_variant_id" value="<?= $item['product_variant_id'] ?>">
                                                                        <input type="hidden" class="product_name" name="product_name" value="<?= $item['pname'] ?>">
                                                                        <input type="hidden" class="order_item_id" name="order_item_id" value="<?= $item['id'] ?>">
                                                                <?php
                                                                } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <h5 class="text-middle-line" type="button"><span>Update Status</span></h5>
                                                    </div>
                                                    <select name="status" class="form-control order_item_status mb-3">
                                                        <option value=''>Select Status</option>
                                                        <option value="return_pickedup" <?= $item['active_status'] == 'return_pickedup' ? "selected" : "" ?>>Return pickedup</option>
                                                    </select>
                                                    
                                                    <div class="d-flex justify-content-end align-items-center">
                                                        <button type="button" class="btn btn-primary update_return_status_delivery_boy" data-id='<?= $order_detls['order_item_id'] ?>'>Submit</button>
                                                    </div>
                                                    <?php
                                                    if (isset($order_detls['discount']) && $order_detls['discount'] > 0) {
                                                        $discount = $order_detls['total_payable']  *  ($order_detls['discount'] / 100);
                                                        $total = round($order_detls['total_payable'] - $discount, 2);
                                                    } ?>
                                                </div>
                                            </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Total(<?= $settings['currency'] ?>)</th>
                                    <td id='amount'><?php echo round($total, 2); ?></td>
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
                                    <td><?php echo (!empty($order_detls['delivery_date']) && $order_detls['delivery_date'] != NUll) ? date('d-M-Y', strtotime($order_detls['delivery_date'])) . " - " . $order_detls['delivery_time'] : "Anytime"; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Order Date</th>
                                    <td><?php echo date('d-M-Y', strtotime($order_detls['date_added'])); ?></td>
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
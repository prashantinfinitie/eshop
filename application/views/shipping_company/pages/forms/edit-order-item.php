<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>View Order</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('shipping_company/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Orders</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info overflow-auto">
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <input type="hidden" name="order_id" id="order_id" value="<?php echo $order_detls['id']; ?>">
                                    <th class="w-10px">Order ID</th>
                                    <td><?php echo $order_detls['id']; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Buyer Name</th>
                                    <td><?php echo $order_detls['uname'] ?? 'N/A'; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Email</th>
                                    <td><?= (ALLOW_MODIFICATION == 0 && !defined(ALLOW_MODIFICATION)) ? str_repeat("X", strlen($order_detls['email']) - 3) . substr($order_detls['email'], -3) : $order_detls['email'] ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Contact</th>
                                    <td><?= (!defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($order_detls['mobile']) - 3) . substr($order_detls['mobile'], -3) : $order_detls['mobile']; ?></td>
                                </tr>

                                <?php if (!empty($order_detls['notes'])) { ?>
                                    <tr>
                                        <th class="w-10px">Order Note</th>
                                        <td><?php echo $order_detls['notes']; ?></td>
                                    </tr>
                                <?php } ?>

                                <tr>
                                    <th class="w-10px">Items</th>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td colspan="2">
                                        <div class="card card-info mb-3 mt-2">
                                            <div class="card-body">
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
                                                                <th scope="col">Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $badges = [
                                                                "draft" => "secondary",
                                                                "awaiting" => "secondary",
                                                                "received" => "primary",
                                                                "processed" => "info",
                                                                "shipped" => "warning",
                                                                "delivered" => "success",
                                                                "returned" => "danger",
                                                                "cancelled" => "danger"
                                                            ];

                                                            $total = 0;
                                                            foreach ($items as $index => $item) {
                                                                $item['discounted_price'] = ($item['discounted_price'] == '') ? 0 : $item['discounted_price'];
                                                                $subtotal = ($item['quantity'] != 0 && ($item['discounted_price'] != '' && $item['discounted_price'] > 0) && $item['price'] > $item['discounted_price']) ? ($item['price'] - $item['discounted_price']) : ($item['price'] * $item['quantity']);
                                                                $total += $subtotal;
                                                            ?>
                                                                <tr>
                                                                    <th scope="row"><?= $index + 1 ?></th>
                                                                    <td><?= $item['pname'] ?></td>
                                                                    <td>
                                                                        <a href='<?= $item['product_image'] ?>' class="image-box-100" data-toggle='lightbox' data-gallery='order-images'>
                                                                            <img src='<?= $item['product_image'] ?>' alt="<?= $item['pname'] ?>">
                                                                        </a>
                                                                    </td>
                                                                    <td><?= $item['quantity'] ?></td>
                                                                    <td><?= str_replace('_', ' ', $item['product_type']) ?></td>
                                                                    <td><?= $item['product_variant_id'] ?></td>
                                                                    <td><?= ($item['discounted_price'] == null) ? "0" : round($item['discounted_price'], 2) ?></td>
                                                                    <td><?= round($subtotal, 2) ?></td>
                                                                    <td>
                                                                        <span class="text-uppercase p-1 status-<?= $item['id'] ?> badge badge-<?= $badges[$item['active_status']] ?>">
                                                                            <?= str_replace('_', ' ', ($item['active_status'] == 'draft' ? "awaiting" : $item['active_status'])) ?>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                <input type="hidden" class="order_item_id" name="order_item_id" value="<?= $item['id'] ?>">
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="d-flex justify-content-center align-items-center mt-4">
                                                    <h5 class="text-middle-line" type="button"><span>Update Status</span></h5>
                                                </div>

                                                <select name="status" class="form-control order_item_status mb-3">
                                                    <option value=''>Select Status</option>
                                                    <option value="received">Received</option>
                                                    <option value="processed">Processed</option>
                                                    <option value="shipped">Shipped</option>
                                                    <option value="delivered">Delivered</option>
                                                </select>

                                                <div class="d-flex justify-content-end align-items-center">
                                                    <button type="button" class="btn btn-primary update_shipping_status">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="w-10px">Total(<?= $settings['currency'] ?>)</th>
                                    <td id='amount'><?php echo round($total, 2); ?></td>
                                </tr>

                                <tr>
                                    <th class="w-10px">Delivery Charge(<?= $settings['currency'] ?>)</th>
                                    <td><?php echo round($order_detls['delivery_charge'], 2); ?></td>
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
                                    <td><?php echo (!empty($order_detls['delivery_date']) && $order_detls['delivery_date'] != NULL) ? date('d-M-Y', strtotime($order_detls['delivery_date'])) . " - " . $order_detls['delivery_time'] : "Anytime"; ?></td>
                                </tr>

                                <tr>
                                    <th class="w-10px">Order Date</th>
                                    <td><?php echo date('d-M-Y', strtotime($order_detls['date_added'])); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        // Update shipping status
        $(document).on('click', '.update_shipping_status', function(e) {
            e.preventDefault();

            var order_item_id = $('.order_item_id').val();
            var status = $('.order_item_status').val();
            var order_id = $('#order_id').val();

            if (status == '' || status == null) {
                Swal.fire({
                    title: 'Error',
                    text: 'Please select status',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to update this status!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                console.log(result);
                if (result.value == true) {
                    $.ajax({
                        url: '<?= base_url('shipping_company/orders/update_order_status') ?>',
                        type: 'GET',
                        data: {
                            id: order_item_id,
                            status: status
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);
                            if (response.error == false) {
                                Swal.fire({
                                    title: 'Success',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                title: 'Error',
                                text: 'Something went wrong!',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });
    });
</script>

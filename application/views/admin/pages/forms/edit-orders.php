<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>View Order</h4>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Orders</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <!-- modal for send digital product -->
                <div id="sendMailModal" class="modal fade editSendMail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Manage Digital Product</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body ">
                                <form class="form-horizontal form-submit-event" action="<?= base_url('admin/orders/send_digital_product'); ?>" method="POST" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <input type="hidden" name="order_id" value="<?= $order_detls[0]['order_id'] ?>">
                                        <input type="hidden" name="order_item_id" value="<?= $this->input->get('edit_id') ?>">
                                        <input type="hidden" name="username" value="<?= $order_detls[0]['uname']  ?>">
                                        <div class="row form-group">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="product_name">Customer Email-ID </label>
                                                    <input type="text" class="form-control" id="email" name="email" value="<?= $order_detls[0]['user_email'] ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="product_name">Subject </label>
                                                    <input type="text" class="form-control" id="subject" placeholder="Enter Subject for email" name="subject" value="">
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="product_name">Message</label>
                                                    <textarea class="textarea" id="mail_msg" placeholder="Message for Email" name="message"></textarea>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2" id="digital_media_container">
                                                <label for="image" class="ml-2">File <span class='text-danger text-sm'>*</span></label>
                                                <div class='col-md-6'><a class="uploadFile img btn btn-primary text-white btn-sm" data-input='pro_input_file' data-isremovable='1' data-media_type='archive,document' data-is-multiple-uploads-allowed='0' data-toggle="modal" data-target="#media-upload-modal" value="Upload Photo"><i class='fa fa-upload'></i> Upload</a></div>
                                                <div class="container-fluid row image-upload-section">
                                                    <div class="col-md-6 col-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image d-none">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-success mt-3" id="submit_btn" value="Save"><?= labels('send_mail', 'Send Mail') ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- modal for order tracking -->
                <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="transaction_modal" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="user_name">Order Tracking</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card-info">
                                            <form class="form-horizontal " id="order_tracking_form" action="<?= base_url('admin/orders/update-order-tracking/'); ?>" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="order_id" id="order_id" value="<?= $_GET["edit_id"] ?>">
                                                <input type="hidden" name="order_item_id" id="order_item_id">
                                                <input type="hidden" name="seller_id" id="seller_id" value="">
                                                <input type="hidden" name="consignment_id" id="consignment_id">
                                                <div class="card-body pad">
                                                    <div class="form-group ">
                                                        <label for="courier_agency">Courier Agency</label>
                                                        <input type="text" class="form-control" name="courier_agency" id="courier_agency" placeholder="Courier Agency" />
                                                    </div>
                                                    <div class="form-group ">
                                                        <label for="tracking_id">Tracking Id</label>
                                                        <input type="text" class="form-control" name="tracking_id" id="tracking_id" placeholder="Tracking Id" />
                                                    </div>
                                                    <div class="form-group ">
                                                        <label for="url">URL</label>
                                                        <input type="text" class="form-control" name="url" id="url" placeholder="URL" />
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="reset" class="btn btn-warning">Reset</button>
                                                        <button type="submit" class="btn btn-success" id="submit_btn">Save</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <!--/.card-->
                                    </div>
                                    <!--/.col-md-12-->
                                </div>
                                <!-- /.row -->

                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- modal for create shiprocket order -->
                <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="order_parcel_modal" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Create Shipprocket Order Parcel</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-info">
                                            <!-- form start -->
                                            <form class="form-horizontal " id="shiprocket_order_parcel_form" action="" method="POST">

                                                <?php
                                                $total_items = count($items);
                                                ?>
                                                <div class="card-body pad">
                                                    <div class="form-group">
                                                        <input type="hidden" name=" <?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                                                        <input type="hidden" id="order_id" name="order_id" value="<?php print_r($order_detls[0]['id']); ?>" />
                                                        <input type="hidden" name="user_id" id="user_id" value="<?php echo $order_detls[0]['user_id']; ?>" />
                                                        <input type="hidden" name="total_order_items" id="total_order_items" value="<?php echo $total_items; ?>" />
                                                        <input type="hidden" name="shiprocket_seller_id" value="" />
                                                        <input type="hidden" name="fromadmin" value="1" id="fromadmin" />
                                                        <textarea id="order_items" name="order_items[]" hidden><?= json_encode($items, JSON_FORCE_OBJECT); ?></textarea>
                                                    </div>
                                                    <div class="mt-1 p-2 bg-danger text-white rounded">
                                                        <p><b>Note:</b> Make your pickup location associated with the order is verified from <a href="https://app.shiprocket.in/company-pickup-location?redirect_url=" target="_blank" class="text-decoration-none text-white"> Shiprocket Dashboard </a> and then in <a href="<?php base_url('admin/Pickup_location/manage-pickup-locations'); ?>" target="_blank" class="text-decoration-none text-white"> admin panel </a>. If it is not verified you will not be able to generate AWB later on.</p>
                                                    </div>
                                                    <div class="form-group row mt-4">
                                                        <div class="col-4">
                                                            <label for="txn_amount">Pickup location</label>
                                                        </div>
                                                        <div class="col-8">
                                                            <input type="text" class="form-control" name="pickup_location" id="pickup_location" placeholder="Pickup Location" value="" readonly />
                                                        </div>
                                                    </div>
                                                    <div class="form-group row mt-3">
                                                        <div class="col-md-6">
                                                            <label for="txn_amount">Total Weight of Box</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row mt-4">
                                                        <div class="col-3">
                                                            <label for="parcel_weight" class="control-label col-md-12">Weight <small>(kg)</small> <span class='text-danger text-xs'>*</span></label>
                                                            <input type="number" class="form-control" name="parcel_weight" placeholder="Parcel Weight" id="parcel_weight" value="" step=".01">
                                                        </div>
                                                        <div class="col-3">
                                                            <label for="parcel_height" class="control-label col-md-12">Height <small>(cms)</small> <span class='text-danger text-xs'>*</span></label>
                                                            <input type="number" class="form-control" name="parcel_height" placeholder="Parcel Height" id="parcel_height" value="" min="1">
                                                        </div>
                                                        <div class="col-3">
                                                            <label for="parcel_breadth" class="control-label col-md-12">Breadth <small>(cms)</small> <span class='text-danger text-xs'>*</span></label>
                                                            <input type="number" class="form-control" name="parcel_breadth" placeholder="Parcel Breadth" id="parcel_breadth" value="" min="1">
                                                        </div>
                                                        <div class="col-3">
                                                            <label for="parcel_length" class="control-label col-md-12">Length <small>(cms)</small> <span class='text-danger text-xs'>*</span></label>
                                                            <input type="number" class="form-control" name="parcel_length" placeholder="Parcel Length" id="parcel_length" value="" min="1">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-success create_shiprocket_parcel">Create Order</button>
                                                </div>

                                            </form>
                                        </div>
                                        <!--/.card-->
                                    </div>
                                    <!--/.col-md-12-->
                                </div>
                                <!-- /.row -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- model for update bank transfer recipt  -->
                <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="payment_transaction_modal" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="user_name"></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-info">
                                            <form class="form-horizontal " id="edit_transaction_form" action="<?= base_url('admin/orders/edit_transactions/'); ?>" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="id" id="id">

                                                <div class="card-body pad">
                                                    <div class="form-group ">
                                                        <label for="transaction"> Update Transaction </label>
                                                        <select class="form-control" name="status" id="t_status">
                                                            <option value="awaiting"> Awaiting </option>
                                                            <option value="Success"> Success </option>
                                                            <option value="Failed"> Failed </option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group ">
                                                        <label for="txn_id">Txn_id</label>
                                                        <input type="text" class="form-control" name="txn_id" id="txn_id" placeholder="txn_id" />
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="message">Message</label>
                                                        <input type="text" class="form-control" name="message" id="message" placeholder="Message" />
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="reset" class="btn btn-warning">Reset</button>
                                                        <button type="submit" class="btn btn-success" id="submit_btn">Update Transaction</button>
                                                    </div>
                                                </div>

                                                <!-- /.card-body -->
                                            </form>
                                        </div>
                                        <!--/.card-->
                                    </div>
                                    <!--/.col-md-12-->
                                </div>
                                <!-- /.row -->

                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card card-info overflow-auto">
                        <div class="card-body">
                            <table class="table">
                                <?php
                                $mobile_data = fetch_details('addresses', ['id' => $order_detls[0]['address_id']], 'mobile');
                                ?>
                                <tr>
                                    <input type="hidden" name="hidden" id="order_id" value="<?php echo $order_detls[0]['id']; ?>">
                                    <th class="w-10px">ID</th>
                                    <td><?php echo $order_detls[0]['id']; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Name</th>
                                    <td><?php echo "Account Holder Person : " . $order_detls[0]['uname'] . " | Order Recipient Person :  " . $order_detls[0]['user_name']; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Email</th>
                                    <td><?= (!defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($order_detls[0]['email']) - 3) . substr($order_detls[0]['email'], -3) : $order_detls[0]['email']; ?></td>
                                </tr>
                                <?php if ($order_detls[0]['mobile'] != '' && isset($order_detls[0]['mobile'])) {
                                ?>
                                    <tr>
                                        <th class="w-10px">Contact</th>
                                        <td><?= (!defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0)  ? str_repeat("X", strlen($order_detls[0]['mobile']) - 3) . substr($order_detls[0]['mobile'], -3) : $order_detls[0]['mobile']; ?>
                                        </td>
                                    </tr>

                                <?php  } else {
                                ?>
                                    <tr>
                                        <th class="w-10px">Contact</th>
                                        <td><?= (!defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0)  ? str_repeat("X", strlen($mobile_data[0]['mobile']) - 3) . substr($mobile_data[0]['mobile'], -3) : $mobile_data[0]['mobile']; ?>
                                        </td>
                                    </tr>
                                <?php
                                } ?>
                                <?php if (!empty($order_detls[0]['notes'])) { ?>
                                    <tr>
                                        <th class="w-10px">Order note</th>
                                        <td><?php echo  $order_detls[0]['notes']; ?></td>
                                    </tr>
                                <?php } ?>

                                <?php $sellers = array_values(array_unique(array_column($order_detls, "order_seller_id")));
                                $hide = true;
                                if ($hide = false) {
                                ?>
                                    <tr>
                                        <td colspan="2">

                                            <?php if (isset($items[0]['product_type']) && $items[0]['product_type'] == 'digital_product') { ?>
                                                <p>
                                                    <lable class="badge badge-success text-sm">Select status and radio button of seller which you want to update</lable>
                                                </p>
                                                <div class="row">
                                                    <div class="col-md-4 ">
                                                        <select name="status" class="form-control status">
                                                            <option value=''>Select Status</option>
                                                            <option value="delivered">Delivered</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <a href="javascript:void(0);" title="Bulk Update" class="btn btn-primary col-sm-12 col-md-12 update_status_admin_bulk mr-1">
                                                            Update
                                                        </a>
                                                    </div>
                                                </div>
                                                <p>
                                                    <lable class="badge badge-warning mt-4 text-sm">Note : Select square box of item only when you want to update it as cancelled or returned.</lable>
                                                </p>

                                            <?php } else {

                                            ?>

                                                <p>
                                                    <lable class="badge badge-success text-sm">Select status, delivery boy and radio button of seller which you want to update</lable>
                                                </p>

                                                <div class="d-flex delivery_boy ">
                                                    <div class="col-md-3">
                                                        <select name="status" class="form-control status" <?php echo $items[0]['active_status'] == "awaiting" ? 'disabled' : '' ?>>
                                                            <option value=''>Select Status</option>
                                                            <option value="processed">Processed</option>
                                                            <option value="shipped">Shipped</option>
                                                            <option value="delivered">Delivered</option>
                                                            <option value="cancelled">Cancel</option>
                                                            <option value="returned">Returned</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <a href="javascript:void(0)" class="edit_order_tracking btn btn-success btn-xl " title="Order Tracking" data-order_id=' <?= $order_detls[0]['id']; ?>' data-target="#transaction_modal" data-toggle="modal"><i class="fa fa-map-marker-alt"></i></a>
                                                        <a href="javascript:void(0);" title="Bulk Update" data-id=' <?= $sellers[$i] ?> ' class="btn btn-primary ml-3 update_status_admin_bulk">
                                                            Update
                                                        </a>
                                                        <?php if ($shipping_method['shiprocket_shipping_method'] == 1) { ?>
                                                            <button type="button" disabled class="btn btn-primary ml-3 col-md-6 create_shiprocket_order float-right" data-target="#order_parcel_modal" data-toggle="modal"> Create Shiprocket Order</button>
                                                        <?php } ?>

                                                    </div>
                                                </div>
                                                <p>
                                                    <lable class="badge badge-warning mt-4 text-sm">Note : Select square box of item only when you want to update it as cancelled or returned.</lable>
                                                </p>
                                                <?php if ($shipping_method['shiprocket_shipping_method'] == 1) { ?>
                                                    <p>
                                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#ShiprocketOrderFlow">How to manage shiprocket order </button>
                                                    </p>
                                                <?php } ?>

                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="2">
                                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                            <li class="nav-item mr-2" role="presentation">
                                                <button class="nav-link active btn btn-default " id="order-items-tab" data-toggle="pill" data-target="#order-items" type="button" role="tab" aria-controls="order-items" aria-selected="true">Order Items</button>
                                            </li>

                                            <li class="nav-item mr-2" role="presentation">
                                                <button class="nav-link btn btn-default " id="pills-shipments-tab" data-toggle="pill" data-target="#pills-shipments" type="button" role="tab" aria-controls="pills-shipments" aria-selected="false">Return Requests</button>
                                            </li>

                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link btn btn-default " id="pills-parcel-tab" data-toggle="pill" data-target="#pills-parcels" type="button" role="tab" aria-controls="pills-parcels" aria-selected="false">Parcels</button>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="tab-content" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="order-items" role="tabpanel" aria-labelledby="order-items-tab">
                                                <?php
                                                $badges = ["draft" => "secondary", "awaiting" => "secondary", "received" => "primary", "processed" => "info", "shipped" => "warning", "delivered" => "success", "returned" => "danger", "cancelled" => "danger", "return_request_approved" => "danger", "return_request_decline" => "danger", "return_request_pending" => "danger"];
                                                for ($i = 0; $i < count($sellers); $i++) {
                                                    $seller_data = fetch_details('users', ['id' => $sellers[$i]], 'username,fcm_id,email,mobile');
                                                    $seller_otp = fetch_details('order_items', ['order_id' => $order_detls[0]['order_id'], 'seller_id' => $sellers[$i]], 'otp')[0]['otp'];
                                                    $order_caharges_data = fetch_details('order_charges', ['order_id' => $order_detls[0]['order_id'], 'seller_id' => $sellers[$i]]);
                                                    $this->load->model('Order_model');
                                                    $seller_order = $this->Order_model->get_order_details(['o.id' => $order_detls[0]['order_id'], 'oi.seller_id' => $sellers[$i]]);
                                                    $pickup_location = array_values(array_unique(array_column($seller_order, "pickup_location")));
                                                ?>
                                                    <h5>Seller Name:- <b class="text-capitalize"><?= $seller_data[0]['username'] ?></b></h5>
                                                    <div class="border rounded p-2">

                                                        <div class="table-responsive custom-table-responsive">
                                                            <table class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col">#</th>
                                                                        <th scope="col">Name</th>
                                                                        <th scope="col">Image</th>
                                                                        <th scope="col">Quantity</th>
                                                                        <th scope="col">Product Type</th>
                                                                        <th scope="col">Attachment</th>
                                                                        <th scope="col">Variant ID</th>
                                                                        <th scope="col">Discounted Price</th>
                                                                        <th scope="col">Subtotal</th>
                                                                        <th scope="col">Active Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    foreach ($items as $index => $item) {

                                                                        if ($sellers[$i] == $item['seller_id']) {
                                                                            $count = 0;
                                                                            $is_allow_to_ship_order = true;
                                                                            if ($item['active_status'] == 'draft' || $item['active_status'] == 'awaiting') {
                                                                                $is_allow_to_ship_order = false;
                                                                            }
                                                                            $selected = "";
                                                                            $item['discounted_price'] = ($item['discounted_price'] == '') ? 0 : $item['discounted_price'];
                                                                            $total += $subtotal = ($item['quantity'] != 0 && ($item['discounted_price'] != '' && $item['discounted_price'] > 0) && $item['price'] > $item['discounted_price']) ? ($item['price'] - $item['discounted_price']) : ($item['price'] * $item['quantity']);
                                                                            $tax_amount += $item['tax_amount'];
                                                                            $attachments = json_decode($item['attachment'], true);

                                                                            if (isset($item['attachment']) && !empty($item['attachment'])) {

                                                                                $order_attachments = explode(',', $item['attachment']);

                                                                                $order_attachment = '';


                                                                                // Output <img> tags
                                                                                foreach ($order_attachments as $url) {
                                                                                    $order_attachment .= '<img src="' . base_url($url) . '" alt="Return Image" class="img-responsive" style="width: 50px; height: 50px; margin-right: 5px;" />';
                                                                                }
                                                                            } else {
                                                                                $order_attachment = '<img src="' . base_url() . NO_IMAGE . '" alt="Return Image" class="img-responsive" style="width: 50px; height: 50px; margin-right: 5px;" />';
                                                                            }
                                                                    ?>
                                                                            <tr>
                                                                                <th scope="row"><?= $count + 1;
                                                                                                $count += $count + 1;
                                                                                                ?></th>
                                                                                <td><a href=" <?= base_url('seller/product/view-product?edit_id=' . $item['product_id'] . '') ?>" title="Click To View Product" target="_blank"><?= $item['pname'] ?></a></td>
                                                                                <td><a href='<?= base_url() . $item['product_image'] ?>' class="image-box-100" data-toggle='lightbox' data-gallery='order-images'> <img src='<?= base_url() . $item['product_image'] ?>' alt="<?= $item['pname'] ?>"></a></td>
                                                                                <td><?= $item['quantity'] ?></td>
                                                                                <td><?= str_replace('_', ' ', $item['product_type']) ?></td>

                                                                                <td>
                                                                                    <?= $order_attachment ?>
                                                                                </td>


                                                                                <td><?= $item['product_variant_id'] ?></td>
                                                                                <td><?= ($item['discounted_price'] == null) ? "0" : round($item['discounted_price'], 2) ?></td>
                                                                                <td><?= round($subtotal, 2) ?></td>
                                                                                <td><span class="text-uppercase p-1 status-<?= $item['id'] ?> badge badge-<?= $badges[$item['active_status']] ?>"><?= str_replace('_', ' ', ($item['active_status'] == 'draft' ? "awaiting" : $item['active_status'])) ?></span></td>
                                                                            </tr>
                                                                            <span class="d-none" id="product_variant_id_<?= $item["product_variant_id"] ?>">
                                                                                <?= json_encode([
                                                                                    "id" => $item["id"],
                                                                                    "unit_price" => $item["price"],
                                                                                    "quantity" => $item['quantity'],
                                                                                    "delivered_quantity" => $item['delivered_quantity'],
                                                                                ]) ?>
                                                                            </span>
                                                                            <input type="hidden" class="product_variant_id" name="product_variant_id" value="<?= $item['product_variant_id'] ?>">
                                                                            <input type="hidden" class="product_name" name="product_name" value="<?= $item['pname'] ?>">
                                                                            <input type="hidden" class="order_item_id" name="order_item_id" value="<?= $item['id'] ?>">
                                                                    <?php }
                                                                    } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <input type="hidden" id="order_id" value="<?= $_GET["edit_id"] ?>">
                                            <div class="tab-pane fade" id="pills-shipments" role="tabpanel" aria-labelledby="pills-shipments-tab">
                                                <table class='table-striped' id='return_request_table' data-toggle="table" data-url="<?= base_url('admin/return_request/view_return_request_list?order_id=' . $order_detls[0]['id']) ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-query-params="queryParams">
                                                    <thead>
                                                        <tr>
                                                            <th data-field="id" data-sortable="true">ID</th>
                                                            <th data-field="order_id" data-sortable="true">Order ID</th>
                                                            <th data-field="order_item_id" data-sortable="true">Order Item ID</th>
                                                            <th data-field="user_name" data-sortable="false">Username</th>
                                                            <th data-field="product_name" data-sortable="false">Product Name</th>
                                                            <th data-field="price" data-sortable="false">Price</th>
                                                            <th data-field="discounted_price" data-sortable="false" data-visible="false">Discounted Price</th>
                                                            <th data-field="quantity" data-sortable="false">Quantity</th>
                                                            <th data-field="sub_total" data-sortable="false">Sub Total</th>
                                                            <th data-field="status" data-sortable="false">Status</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            <?php if ($item['product_type'] != "digital_product") { ?>
                                                <div class="tab-pane fade" id="pills-parcels" role="tabpanel" aria-labelledby="pills-parcel-tab">
                                                    <!--                                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#create_consignment_modal" onclick="consignmentModal()">Create A Parcel</button>-->
                                                    <table class='table-striped' data-toggle="table" data-url="<?= base_url('admin/orders/consignment_view') ?>" data-click-to-select="true" data-side-pagination="server"
                                                        data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true"
                                                        data-trim-on-search="false" data-sort-name="o.id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" data-show-export="true"
                                                        data-maintain-selected="true" data-export-types='["txt","excel","csv"]' data-export-options='{"fileName": "orders-list","ignoreColumn": ["state"] }'
                                                        data-query-params="consignment_query_params" id="consignment_table">
                                                        <thead>
                                                            <tr>
                                                                <th data-field="id" data-sortable='true' data-footer-formatter="totalFormatter">ID</th>
                                                                <th data-field="order_id" data-sortable='true'>Order ID</th>
                                                                <th data-field="name" data-sortable='true'>Name</th>
                                                                <th data-field="status" data-sortable='true'>Status</th>
                                                                <th data-field="created_date" data-sortable='true'>Created Date</th>
                                                                <th data-field="operate" data-sortable="false">Action</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Total(<?= $settings['currency'] ?>)</th>
                                    <td id='amount'>
                                        <?php
                                        echo $order_detls[0]['order_total'];
                                        $total = $order_detls[0]['order_total'];
                                        ?>
                                    </td>
                                </tr>

                                <tr class="d-none">
                                    <th class="w-10px">Tax(<?= $settings['currency'] ?>)</th>
                                    <td id='amount'><?php echo $tax_amount;
                                                    ?></td>
                                </tr>
                                <?php if (isset($items[0]['product_type']) && $items[0]['product_type'] != 'digital_product') { ?>
                                    <tr>
                                        <th class="w-10px">Delivery Charge(<?= $settings['currency'] ?>)</th>
                                        <td id='delivery_charge'>
                                            <?php echo $order_detls[0]['delivery_charge'];
                                            $total = $total + $order_detls[0]['delivery_charge']; ?>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <tr>
                                    <th class="w-10px">Wallet Balance(<?= $settings['currency'] ?>)</th>
                                    <td><?php echo $order_detls[0]['wallet_balance'];
                                        $total = $total - $order_detls[0]['wallet_balance']; ?></td>
                                </tr>

                                <input type="hidden" name="total_amount" id="total_amount" value="<?php echo $order_detls[0]['order_total'] + $order_detls[0]['delivery_charge'] ?>">
                                <input type="hidden" name="final_amount" id="final_amount" value="<?php echo $order_detls[0]['final_total']; ?>">

                                <tr>
                                    <th class="w-10px">Promo Code Discount (<?= $settings['currency'] ?>)</th>
                                    <td><?php echo $order_detls[0]['promo_discount'];
                                        $total = floatval($total -
                                            $order_detls[0]['promo_discount']); ?></td>
                                </tr>

                                <?php if (isset($order_detls[0]['is_pos_order']) && $order_detls[0]['is_pos_order'] == 1) { ?>
                                    <tr>
                                        <th class="w-10px">Discount</th>
                                        <td><?php echo $order_detls[0]['discount']; ?></td>
                                    </tr>
                                <?php } ?>

                                <?php if (isset($order_detls[0]['is_pos_order']) && $order_detls[0]['is_pos_order'] == 1) {
                                    $total = round($order_detls[0]['total_payable'], 2);
                                } else {
                                    if (isset($order_detls[0]['discount']) && $order_detls[0]['discount'] > 0) {
                                        $discount = $order_detls[0]['total_payable'] * ($order_detls[0]['discount'] / 100);
                                        $total = round($order_detls[0]['total_payable'] - $discount, 2);
                                    }
                                } ?>
                               
                                <tr>
                                    <th class="w-10px">Payable Total(<?= $settings['currency'] ?>)</th>
                                    <td><input type="text" class="form-control" id="final_total" name="final_total" value="<?= $total; ?>" disabled></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Payment Method</th>
                                    <td>
                                        <?php
                                        if ($order_detls[0]['is_pos_order'] == 1 && $order_detls[0]['payment_method'] == 'COD') {
                                            echo "cash payment";
                                        } else {
                                            echo str_replace('_', ' ', $order_detls[0]['payment_method']);
                                        }
                                        ?>
                                        <?php if (isset($transaction_search_res)) { ?>
                                            <a href="javascript:void(0)" class="edit_transaction action-btn btn btn-success btn-xs mr-1 mb-1" title="Update bank transfer recipt status " data-id="<?= $order_detls[0]['id'] ?>" data-txn_id="<?= $transaction_search_res[0]['txn_id'] ?>" data-status="<?= $transaction_search_res[0]['status'] ?>" data-message="<?= $transaction_search_res[0]['message'] ?>" data-target="#payment_transaction_modal" data-toggle="modal"><i class="fa fa-pen"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                                if (!empty($bank_transfer)) {
                                    $disabled = (isset($order_detls[0]['active_status']) && !empty($order_detls[0]['active_status'] && $order_detls[0]['active_status'] == 'delivered')) ? 'isDisabled' : '';


                                ?>
                                    <tr>
                                        <th class="w-10px">Bank Transfers</th>
                                        <td>
                                            <div class="col-md-6">
                                                <?php $status = ["history", "ban", "check"]; ?>
                                                <a class="btn btn-primary btn-xs mr-1 mb-1 " title="Current Status" href="javascript:void(0)" data-id="<?= $order_detls[0]['id']; ?>"><i class="fa fa-<?= $status[$bank_transfer[0]['status']] ?>"></i></a>
                                                <?php $i = 1;
                                                foreach ($bank_transfer as $row1) { ?>
                                                    <small>[<a href="<?= base_url() . $row1['attachments'] ?>" target="_blank">Attachment <?= $i ?> </a>] </small>
                                                    <?php if ($row1['status'] == 0) { ?>
                                                        <label class="badge badge-warning"><?= !empty($this->lang->line('pending')) ? $this->lang->line('pending') : 'Pending' ?></label>
                                                    <?php } else if ($row1['status'] == 1) { ?>
                                                        <label class="badge badge-danger"><?= !empty($this->lang->line('rejected')) ? $this->lang->line('rejected') : 'Rejected' ?></label>
                                                    <?php } else if ($row1['status'] == 2) { ?>
                                                        <label class="badge badge-primary"><?= !empty($this->lang->line('accepted')) ? $this->lang->line('accepted') : 'Accepted' ?></label>
                                                    <?php } else { ?>
                                                        <label class="badge badge-danger"><?= !empty($this->lang->line('invalid_value')) ? $this->lang->line('invalid_value') : 'Invalid Value' ?></label>
                                                    <?php } ?>
                                                    <a class="delete-receipt btn btn-danger btn-xs mr-1 mb-1 <?= $disabled ?>" title="Delete" href="javascript:void(0)" data-id="<?= $row1['id']; ?>"><i class="fa fa-trash"></i></a>
                                                <?php $i++;
                                                } ?>
                                                <select name="update_receipt_status" id="update_receipt_status" class="form-control status" data-id="<?= $order_detls[0]['id']; ?>" data-user_id="<?= $order_detls[0]['user_id']; ?>">
                                                    <option value=''>Select Status</option>
                                                    <option value="1" <?= (isset($bank_transfer[0]['status']) && $bank_transfer[0]['status'] == 1) ? "selected" : ""; ?>>Rejected</option>
                                                    <option value="2" <?= (isset($bank_transfer[0]['status']) && $bank_transfer[0]['status'] == 2) ? "selected" : ""; ?>>Accepted</option>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if (isset($items[0]['product_type']) && $items[0]['product_type'] != 'digital_product') {
                                    $address_number = fetch_details('addresses', 'id =' . $order_detls[0]['address_id'], 'mobile');
                                ?>
                                    <tr>
                                        <th class="w-10px">Address</th>
                                        <td><?php echo $order_detls[0]['address'] .  ' ,Mobile- ' . $address_number[0]['mobile']; ?></td>
                                    </tr>
                                    <tr>
                                        <th class="w-10px">Delivery Date & Time</th>
                                        <td><?php echo (!empty($order_detls[0]['delivery_date']) && $order_detls[0]['delivery_date'] != NUll) ? date('d-M-Y', strtotime($order_detls[0]['delivery_date'])) . " - " . $order_detls[0]['delivery_time'] : "Anytime"; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <th class="w-10px">Order Date</th>
                                    <td><?php echo date('d-M-Y', strtotime($order_detls[0]['date_added'])); ?></td>
                                </tr>
                            </table>



                            <a href="https://api.whatsapp.com/send?phone=<?= ($order_detls[0]['country_code']) ?><?= ($order_detls[0]['mobile'] != '' && isset($order_detls[0]['mobile'])) ?
                                                                                                                        $order_detls[0]['mobile'] : ((!defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0)  ? str_repeat("X", strlen($mobile_data[0]['mobile']) - 3) . substr($mobile_data[0]['mobile'], -3) : $mobile_data[0]['mobile'])   ?>&amp;text=Hello <?= $order_detls[0]['uname'] ?>, Your order with ID : <?= $order_detls[0]['order_id'] ?> and is <?= $order_detls[0]['oi_active_status'] ?>. Please take a note of it. If you have further queries feel free to contact us. Thank you." target="_blank" title="Send Whatsapp Notification For Order" class="btn btn-success"><i class="fa fa-whatsapp"></i> Send Whatsapp Notification</a>

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
<div class="modal fade" id="view_consignment_items_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header mb-1">
                <h5 class="modal-title" id="myModalLabel">Parcel Items</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Image</th>
                            <th scope="col">Quantity</th>
                        </tr>
                    </thead>
                    <tbody id="consignment_details">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="consignment_status_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header mb-1">
                <h5 class="modal-title" id="myModalLabel">Update Parcel Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="consignment_id" id="consignment_id">
                <input type="hidden" name="delivery_boy_otp_system" id="delivery_boy_otp_system"
                    value="<?= $order_detls[0]['deliveryboy_otp_setting_on'] ?>">
                <?php if (isset($items[0]['product_type']) && $items[0]['product_type'] != 'digital_product') { ?>
                    <div class="col-md-12 mb-2">
                        <lable class="badge badge-success">Select status <?= get_seller_permission($seller_id, 'assign_delivery_boy') ? 'and delivery boy' : '' ?> which you want to update</lable>
                    </div>
                    <div id="consignment-items-container"></div>
                <?php } ?>
                <ul class="nav nav-pills mb-3 d-block" id="pills-tab" role="tablist">
                    <?php if ($order_detls[0]['is_shiprocket_order'] == 0) { ?>
                        <div class="d-flex justify-content-center align-items-center">
                            <h5 class="text-middle-line" type="button"><span>Local Shipping</span></h5>
                        </div>
                    <?php } else { ?>
                        <div class="d-flex justify-content-center align-items-center">
                            <h5 class="text-middle-line" type="button"><span>Standard Shipping (Shiprocket)</span></h5>
                        </div>
                        <div>
                            <div>
                                <button class="btn my-2 btn-default" type="button" data-toggle="collapse" data-target="#collapseTracking" aria-expanded="false" aria-controls="collapseTracking">
                                    Cancelled Shiprocket Order Details
                                </button>
                                <div class="collapse" id="collapseTracking">
                                    <div class="card card-body">
                                        <div id="tracking_box_old"></div>
                                    </div>
                                </div>
                            </div>
                            <div id="tracking_box"></div>
                        </div>
                        <div class="py-2 manage_shiprocket_box d-none">
                            <p class="m-0">If the Order Status Does Not Change Automatically, Please Click the Refresh Button.</p>
                            <button class="btn btn-outline-danger cancel_shiprocket_order">Cancle Shiprocket Order</button>
                            <button class="btn btn-success refresh_shiprocket_status">Refresh</button>
                        </div>
                        <?php

                        $pickup_location = array_values(array_unique(array_column($seller_order, "pickup_location")));

                        for ($j = 0; $j < count($pickup_location); $j++) {
                            $ids = "";
                            foreach ($seller_order as $row) {

                                if ($row['pickup_location'] == $pickup_location[$j]) {
                                    $ids .= $row['order_item_id'] . ',';
                                }
                            }
                            $order_item_ids = explode(',', trim($ids, ','));
                            $order_tracking_data = get_shipment_id($order_item_ids[0], $order_detls[0]['order_id']);
                            $shiprocket_order = get_shiprocket_order($order_tracking_data[0]['shiprocket_order_id']);

                            foreach ($order_item_ids as $id) {
                                $active_status = fetch_details('order_items', ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], 'active_status')[0]['active_status'];

                                if ($shiprocket_order['data']['status'] == 'PICKUP SCHEDULED' &&  $active_status != 'shipped') {
                                    $this->Order_model->update_order(['active_status' => 'shipped'], ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], false, 'order_items');
                                    $this->Order_model->update_order(['status' => 'shipped'], ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], true, 'order_items');
                                    $type = ['type' => "customer_order_shipped"];
                                    $order_status = 'shipped';
                                }
                                if ($shiprocket_order['data']['status'] == 'CANCELED' &&  $active_status != 'cancelled') {
                                    $this->Order_model->update_order(['active_status' => 'cancelled'], ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], false, 'order_items');
                                    $this->Order_model->update_order(['status' => 'cancelled'], ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], true, 'order_items');
                                    $type = ['type' => "customer_order_cancelled"];
                                    $order_status = 'cancelled';
                                }
                                if (strtolower($shiprocket_order['data']['status']) == 'delivered' &&  $active_status != 'delivered') {
                                    $this->Order_model->update_order(['active_status' => 'delivered'], ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], false, 'order_items');
                                    $this->Order_model->update_order(['status' => 'delivered'], ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], true, 'order_items');
                                    $type = ['type' => "customer_order_delivered"];
                                    $order_status = 'delivered';
                                }
                                if ($shiprocket_order['data']['status'] == 'READY TO SHIP' &&  $active_status != 'processed') {
                                    $this->Order_model->update_order(['active_status' => 'processed'], ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], false, 'order_items');
                                    $this->Order_model->update_order(['status' => 'processed'], ['id' => $id, 'seller_id' => $this->session->userdata('user_id')], true, 'order_items');
                                    $type = ['type' => "customer_order_processed"];
                                    $order_status = 'processed';
                                }
                            }
                        ?>
                            <?php if (isset($pickup_location[$j]) && !empty($pickup_location[$j]) && $pickup_location[$j] != 'NULL') {
                            ?>

                                <div class="row m-2 ml-6 shiprocket_field_box d-none" id="<?= $order_tracking_data[0]['shipment_id'] . '_shipment_id' ?>">

                                    <div class="col-md-5">
                                        <?php


                                        if (isset($order_tracking_data[0])) { ?>
                                            <?php if (isset($order_tracking_data[0]['shipment_id']) && (empty($order_tracking_data[0]['awb_code']) || $order_tracking_data[0]['awb_code'] == 'NULL') && $shiprocket_order['data']['status'] != 'CANCELED') { ?>
                                                <a href="" title="Generate AWB" class="btn btn-primary btn-xs mr-1 generate_awb" data-fromseller="1" id=<?php print_r($order_tracking_data[0]['shipment_id']); ?>>AWB</a>
                                            <?php } else { ?>
                                                <?php if (empty($order_tracking_data[0]['pickup_scheduled_date']) && ($shiprocket_order['data']['status_code'] != 4 || $shiprocket_order['data']['status'] != 'PICKUP SCHEDULED') && $shiprocket_order['data']['status'] != 'CANCELED' && $shiprocket_order['data']['status'] != 'CANCELLATION REQUESTED') { ?>
                                                    <a href="" title="Send Pickup Request" class="btn btn-primary btn-xs mr-1 send_pickup_request" data-fromseller="1" name=<?php print_r($order_tracking_data[0]['shipment_id']); ?>><i class="fas fa-shipping-fast "></i></a>
                                                <?php }
                                                if (isset($order_tracking_data[0]['is_canceled']) && $order_tracking_data[0]['is_canceled'] == 0) { ?>
                                                    <a href="" title="Cancel Order" class="btn btn-primary btn-xs mr-1 cancel_shiprocket_order" data-fromseller="1" name=<?php print_r($order_tracking_data[0]['shiprocket_order_id']); ?>><i class="fas fa-redo-alt"></i></a>
                                                <?php } ?>

                                                <?php if (isset($order_tracking_data[0]['label_url']) && !empty($order_tracking_data[0]['label_url'])) { ?>
                                                    <a href="<?php print_r($order_tracking_data[0]['label_url']); ?>" title="Download Label" data-fromseller="1" class="btn btn-primary btn-xs mr-1 download_label"><i class="fas fa-download"></i> Label</a>
                                                <?php } else { ?>
                                                    <a href="" title="Generate Label" class="btn btn-primary btn-xs mr-1 generate_label" data-fromseller="1" name=<?php print_r($order_tracking_data[0]['shipment_id']); ?>><i class="fas fa-tags"></i></a>
                                                <?php } ?>

                                                <?php if (isset($order_tracking_data[0]['invoice_url']) && !empty($order_tracking_data[0]['invoice_url'])) { ?>
                                                    <a href="<?php print_r($order_tracking_data[0]['invoice_url']); ?>" data-fromseller="1" title="Download Invoice" class="btn btn-primary  btn-xs mr-1 download_invoice"><i class="fas fa-download"></i> Invoice</a>
                                                <?php } else { ?>
                                                    <a href="" title="Generate Invoice" class="btn btn-primary btn-xs mr-1 generate_invoice" data-fromseller="1" name=<?php print_r($order_tracking_data[0]['shiprocket_order_id']); ?>><i class="far fa-money-bill-alt"></i></a>
                                                <?php }
                                                if (isset($order_tracking_data[0]['awb_code']) && !empty($order_tracking_data[0]['awb_code'])) { ?>
                                                    <a href="https://shiprocket.co/tracking/<?php echo $order_tracking_data[0]['awb_code']; ?>" target=" _blank" title="Track Order" class="btn btn-primary action-btn btn-xs mr-1 track_order" name=<?php print_r($order_tracking_data[0]['shiprocket_order_id']); ?>><i class="fas fa-map-marker-alt"></i></a>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </ul>
                <?php
                if ($order_detls[0]['is_shiprocket_order'] == 0) { ?>
                    <select name="status" class="form-control consignment_status  mb-3">
                        <option value=''>Select Status</option>
                        <option value="received">Received</option>
                        <option value="processed">Processed</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                    </select>


                <?php } ?>
                <div class="tab-content" id="pills-tabContent">
                    <?php

                    if ($order_detls[0]['is_shiprocket_order'] == 0) {
                    ?>
                        <div class="tab-pane fade show active" id="pills-local" role="tabpanel" aria-labelledby="pills-local-tab">

                            <select id='deliver_by' name='deliver_by' class='form-control mb-2'>
                                <option value=''>Select Delivery Boy</option>
                                <?php

                                foreach ($delivery_res as $row) {
                                ?>
                                    <option value="<?= $row['id'] ?>" <?= ($order_detls[0]['delivery_boy_id'] == $row['id']) ? 'selected' : '' ?>><?= $row['username'] ?></option>
                                <?php  } ?>
                            </select>
                        </div>
                        <div class=" mb-3 form-group otp-field">
                            <label>Enter user OTP</label>
                            <input name="parcel-otp" id="parcel-otp" minlength="6" maxlength="6" class="form-control ">
                        </div>
                    <?php } else { ?>
                        <div class="tab-pane fade show active" id="pills-standard" role="tabpanel" aria-labelledby="pills-standard-tab">
                            <div class="card card-info shiprocket_order_box">
                                <!-- form start -->
                                <form class="form-horizontal" id="shiprocket_order_parcel_form" action="" method="POST">
                                    <?php
                                    $total_items = count($items);
                                    ?>
                                    <div class="card-body pad">
                                        <div class="form-group">
                                            <input type="hidden" name=" <?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                                            <input type="hidden" id="order_id" name="order_id" value="<?php print_r($order_detls[0]['id']); ?>" />
                                            <input type="hidden" name="user_id" id="user_id" value="<?php echo $order_detls[0]['user_id']; ?>" />
                                            <input type="hidden" name="total_order_items" id="total_order_items" value="<?php echo $total_items; ?>" />
                                            <input type="hidden" name="shiprocket_seller_id" value="<?= $seller_id ?>" />
                                            <input type="hidden" name="fromseller" value="1" id="fromseller" />
                                            <textarea id="order_items" name="order_items[]" hidden><?= json_encode($items, JSON_FORCE_OBJECT); ?></textarea>
                                            <input type="hidden" name="order_tracking[]" id="order_tracking" value='<?= json_encode($order_tracking); ?>' />
                                            <input type="hidden" name="consignment_data[]" id="consignment_data" />
                                        </div>
                                        <div class="mt-1 p-2 bg-danger text-white rounded">
                                            <p><b>Note:</b> Make your pickup location associated with the order is verified from <a href="https://app.shiprocket.in/company-pickup-location?redirect_url=" target="_blank" class="text-decoration-none text-white"> Shiprocket Dashboard </a> and then in <a href="<?php base_url('admin/Pickup_location/manage-pickup-locations'); ?>" target="_blank" class="text-decoration-none text-white"> admin panel </a>. If it is not verified you will not be able to generate AWB later on.</p>
                                        </div>
                                        <div class="form-group row mt-4">
                                            <div class="col-4">
                                                <label for="txn_amount">Pickup location</label>
                                            </div>
                                            <div class="col-8">
                                                <input type="text" class="form-control" name="pickup_location" id="pickup_location" placeholder="Pickup Location" value="<?= $order_detls[0]['pickup_location'] ?>" readonly />
                                            </div>
                                        </div>

                                        <div class="form-group row mt-4">
                                            <div class="col-3">
                                                <label for="parcel_weight" class="control-label col-md-12">Weight <small>(kg)</small> <span class='text-danger text-xs'>*</span></label>
                                                <input type="number" class="form-control" name="parcel_weight" placeholder="Parcel Weight" id="parcel_weight" value="" step=".01">
                                            </div>
                                            <div class="col-3">
                                                <label for="parcel_height" class="control-label col-md-12">Height <small>(cms)</small> <span class='text-danger text-xs'>*</span></label>
                                                <input type="number" class="form-control" name="parcel_height" placeholder="Parcel Height" id="parcel_height" value="" min="1">
                                            </div>
                                            <div class="col-3">
                                                <label for="parcel_breadth" class="control-label col-md-12">Breadth <small>(cms)</small> <span class='text-danger text-xs'>*</span></label>
                                                <input type="number" class="form-control" name="parcel_breadth" placeholder="Parcel Breadth" id="parcel_breadth" value="" min="1">
                                            </div>
                                            <div class="col-3">
                                                <label for="parcel_length" class="control-label col-md-12">Length <small>(cms)</small> <span class='text-danger text-xs'>*</span></label>
                                                <input type="number" class="form-control" name="parcel_length" placeholder="Parcel Length" id="parcel_length" value="" min="1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success create_shiprocket_parcel">Create Order</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <?php if ($order_detls[0]['is_shiprocket_order'] == 0) { ?>
                    <div class="d-flex justify-content-end p-2">
                        <a href="javascript:void(0);" title="Bulk Update" data-seller_id="" class="btn btn-primary ml-1 consignment_order_status_update">
                            Update
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="refund_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="user_name">Payment Refund</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-info">
                            <!-- form start -->
                            <form class="form-horizontal " id="refund_form" action="<?= base_url('admin/orders/refund_payment'); ?>" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <input type="hidden" name=" <?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                                    <input type="hidden" name="item_id" id="item_id">
                                </div>
                                <div class="card-body pad">
                                    <div class="form-group ">
                                        <label for="transaction_id">Transaction Id</label>
                                        <input type="text" class="form-control" name="transaction_id" id="transaction_id" placeholder="Transaction Id" disabled />
                                    </div>
                                    <div class="form-group ">
                                        <label for="txn_amount">Amount</label>
                                        <input type="text" class="form-control" name="txn_amount" id="txn_amount" placeholder="Amount" disabled />
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-secondary" id="submit_btn">Refund</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!--/.card-->
                    </div>
                    <!--/.col-md-12-->
                </div>
                <!-- /.row -->

            </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="ShiprocketOrderFlow" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">How to manage shiprocket order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body ">
                <h6><b>Steps:</b></h6>
                <ol>
                    <li> Select Pickup Location for which you want to create parcel and click on <b>Create Shiprocket Order</b> button.</li>
                    <img src="<?= BASE_URL("assets/admin/images/create_order.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                    <li> After create order generate AWB code(its unique number use for identify order) like this.</li>
                    <img src="<?= BASE_URL("assets/admin/images/generate_awb.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                    <li> After generate AWB Send pickup request for scheduled you shipping.</li>
                    <img src="<?= BASE_URL("assets/admin/images/send_pickup_request.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                    <li> Generate and download Label.</li>
                    <img src="<?= BASE_URL("assets/admin/images/generate_label.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                    <img src="<?= BASE_URL("assets/admin/images/download_label.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                    <li> Generate and download Invoice.</li>
                    <img src="<?= BASE_URL("assets/admin/images/generate_invoice.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                    <img src="<?= BASE_URL("assets/admin/images/download_invoice.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                    <li> Cancel shiprocket order.</li>
                    <img src="<?= BASE_URL("assets/admin/images/cancel_order.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                    <li> shiprocket order traking.</li>
                    <img src="<?= BASE_URL("assets/admin/images/order_tracking.png") ?>" class="img-fluid" alt="Responsive image"><br><br>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="create_consignment_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Create a Parcel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="empty_box_body"></div>
            <div class="modal-body" id="modal-body">
                <div class="input-group flex-nowrap">
                    <span class="input-group-text bg-gradient-light">Parcel Title</span>
                    <input type="text" class="form-control" placeholder="Parcel Title" aria-label="Username" aria-describedby="addon-wrapping" id="consignment_title" required>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Product Varient ID</th>
                            <th scope="col">Order Quantity</th>
                            <th scope="col">Unit Price</th>
                            <th scope="col">Select Items</th>
                        </tr>
                    </thead>
                    <tbody id="product_details">
                    </tbody>
                </table>
                <div class="d-flex justify-content-end px-2">
                    <button type="button" class="btn btn-primary" id="ship_parcel_btn">Ship</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="request_rating_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Return Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body container-fluid">
                <form class="form-horizontal form-submit-event" action="<?= base_url('admin/return_request/update-return-request'); ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="return_request_id" id="return_request_id">
                    <input type="hidden" name="user_id" id="user_id">
                    <input type="hidden" name="order_item_id" id="order_item_id">
                    <input type="hidden" name="seller_id" id="seller_id">
                    <input type="hidden" name="delivery_by" id="delivery_by">

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Status <span class='text-danger text-sm'>*</span></label>
                        <div class="col-md-7 col-sm-6 col-xs-12">
                            <div id="status" class="btn-group">
                                <label class="btn btn-warning" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                    <input type="radio" name="status" value="0" class='pending'> Pending
                                </label>
                                <label class="btn btn-danger" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                    <input type="radio" name="status" value="2" class='rejected' id="rejected"> Rejected
                                </label>
                                <label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                    <input type="radio" name="status" value="1" class='approved' id="approved"> Approved
                                </label>
                                <label class="btn btn-secondary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                    <input type="radio" name="status" value="8" class='return_pickedup' id="return_pickedup"> Return Pickedup
                                </label>
                                <label class="btn btn-success" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                    <input type="radio" name="status" value="3" class='returned' id="returned"> Returned
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 d-none" id="return_request_delivery_by">

                        <select id='deliver_by_order' name='deliver_by' class='form-control'>
                            <option value=''>Select Delivery Boy</option>
                            <?php
                            foreach ($delivery_res as $row) {
                            ?>
                                <option value="<?= $row['user_id']  ?>"><?= $row['username']  ?></option>
                            <?php  }
                            ?>
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label class="" for="">Remark</label>
                        <textarea id="update_remarks" name="update_remarks" class="form-control col-12 "></textarea>
                    </div>
                    <input type="hidden" id="id" name="id">
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <button type="reset" class="btn btn-warning">Reset</button>
                        <button type="submit" class="btn btn-success" id="submit_btn">Update</button>
                    </div>

            </div>
            </form>
        </div>
    </div>
</div>
</div>
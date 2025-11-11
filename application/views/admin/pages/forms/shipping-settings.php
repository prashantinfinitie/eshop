<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Shipping Methods Settings</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Shipping Methods Settings</li>
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
                    <div class="card card-info">
                        <form class="form-horizontal form-submit-event" action="<?= base_url('admin/Shipping_settings/update_shipping_settings'); ?>" method="POST" id="payment_setting_form">
                            <?php $shipping_settings = get_settings('shipping_method', true); ?>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="local_shipping_method">Enable Local Shipping <small> ( Use Local Delivery Boy For Shipping) </small>
                                        </label>
                                        <div class="card-body">
                                            <input type="checkbox" <?= (@$settings['local_shipping_method']) == '1' ? 'Checked' : '' ?> data-bootstrap-switch data-off-color="danger" data-on-color="success" name="local_shipping_method">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="default_delivery_charge" class="d-flex">Default Delivery Charge <small> ( Use only for Local Shipping) </small>
                                            <a class="btn btn-primary btn-xs mx-2 py-1 text-white" data-toggle="modal" data-target="#DefaultDeliveryModal" title="How it works">How Default delivery charge work?</a>
                                        </label>

                                        <div>
                                            <input type="text" class="form-control" name="default_delivery_charge" id="" value="<?= (isset($shipping_settings['default_delivery_charge']) && !empty($shipping_settings['default_delivery_charge'])) ? $shipping_settings['default_delivery_charge'] : '' ?>" />
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="shiprocket_shipping_method">Standard delivery method (Shiprocket) <small>( Enable/Disable ) <a href="https://app.shiprocket.in/api-user" target="_blank"> Click here </a> </small>to get credentials. <small> <a href="https://www.shiprocket.in/" target="_blank">What is shiprocket? </a></small>
                                        </label>
                                        <br>
                                        <div class="card-body">
                                            <input type="checkbox" <?= (@$settings['shiprocket_shipping_method']) == '1' ? 'Checked' : '' ?> data-bootstrap-switch data-off-color="danger" data-on-color="success" name="shiprocket_shipping_method">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-5">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="email" id="email" value="<?= (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen(@$settings['email']) - 3) . substr(@$settings['email'], -3) : @$settings['email'] ?>" placeholder="Shiprocket acount email" />
                                    </div>
                                    <div class="form-group col-5">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" name="password" id="" value="<?= @$settings['password'] ?>" placeholder="Shiprocket acount Password" />
                                    </div>
                                    <div class="form-group col-5">
                                        <label for="webhook_url">Shiprocket Webhoook Url</label>
                                        <input type="text" class="form-control" name="webhook_url" id="" value="<?= base_url('admin/webhook/spr_webhook'); ?>" disabled />
                                    </div>
                                    <div class="form-group col-5">
                                        <label for="webhook_token">Shiprocket webhook token</label>
                                        <input type="text" class="form-control" name="webhook_token" id="" value="<?= (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen(@$settings['webhook_token']) - 3) . substr(@$settings['webhook_token'], -3) : @$settings['webhook_token'] ?>" />
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="form-group col-md-12">
                                        <span class="text-danger"><b>Note:</b> You can give free delivery charge only when <b>Standard delivery method </b> is enable.</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-5">
                                        <label for="standard_shipping_free_delivery">Enable Free Delivery Charge </label>
                                        <div class="card-body">
                                            <input type="checkbox" <?= (@$settings['standard_shipping_free_delivery']) == '1' ? 'Checked' : '' ?> data-bootstrap-switch data-off-color="danger" data-on-color="success" name="standard_shipping_free_delivery">
                                        </div>
                                    </div>
                                    <div class="form-group col-5">
                                        <label for="minimum_free_delivery_order_amount">Minimum free delivery order amount </label>
                                        <div>
                                            <input type="text" class="form-control" name="minimum_free_delivery_order_amount" id="" value="<?= @$settings['minimum_free_delivery_order_amount'] ?>" />
                                        </div>
                                    </div>
                                </div>

                                <?php
                                if (isset($shipping_settings) && !empty($shipping_settings)) { ?>


                                    <div class="row">
                                        <div class="col-md-12 form-group">
                                            <span class="d-flex align-items-center ">
                                                <h4 class="m-2">Product Deliverability</h4>
                                            </span>
                                            <hr>
                                            <div class="d-flex">
                                                <div class="form-group d-flex">
                                                    <label class="mb-2" for="deliverability">Select Deliverability Method :</label>
                                                </div>
                                                <div class="form-group d-flex">
                                                    <div class="mx-5 d-flex">
                                                        <!-- Pincode Wise Deliverability Radio Button -->
                                                        <label for="pincode_wise_deliverability" class="form-check-label">
                                                            Pincode Wise Deliverability
                                                        </label>
                                                        <input type="radio" class="form-check-input" id="pincode_wise_deliverability" name="deliverability_method" value="pincode"
                                                            <?= (isset($shipping_settings['pincode_wise_deliverability']) && $shipping_settings['pincode_wise_deliverability'] == true) ? 'checked' : '' ?> />
                                                    </div>
                                                    <div class="mx-5 d-flex">
                                                        <!-- City Wise Deliverability Radio Button -->
                                                        <label for="city_wise_deliverability" class="form-check-label">
                                                            City Wise Deliverability
                                                            <?php if ($shipping_settings['shiprocket_shipping_method'] == 1) { ?>
                                                                <small class="text-muted">(Disabled because standard shipping is on from shipping method)</small>
                                                            <?php } ?>
                                                        </label>
                                                        <input type="radio" class="form-check-input" id="city_wise_deliverability" name="deliverability_method" value="city"
                                                            <?= (isset($shipping_settings['city_wise_deliverability']) && $shipping_settings['city_wise_deliverability'] == true) ? 'checked' : '' ?>
                                                            <?= ($shipping_settings['shiprocket_shipping_method'] == 1) ? 'disabled' : '' ?> />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }
                                ?>
                                <div class="row">
                                    <div class="form-group col-5"></div>
                                </div>
                                <div class="row">

                                    <div class="form-group">
                                        <button type="reset" class="btn btn-warning">Reset</button>
                                        <button type="submit" class="btn btn-success" id="submit_btn">Update Shipping Settings</button>
                                    </div>
                                </div>

                        </form>
                    </div>
                    <div class="modal fade" id="DefaultDeliveryModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">How Default Delivery charges work?</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body ">
                                    <div class="d-flex flex-column">
                                        <p>This is For if seller is not from users area.</p>
                                        <p>This is only apply when get delivery boy based on seller button is on from admin panel -> store settings .</p>
                                        <p>We have two seller products in user cart . say seller1 and seller2.</p>
                                        <p>User's selected zipcode is 123456. seller1's serviceable zipcode is 123456,456789. seller2's serviceable zipcode is 654987.</p>
                                        <p>so user get delivery charge of seller1 is from zipcode based and seller2's delivery charge from here (default delivery charge) </p>
                                        <p>reason : user's pincode is in seller's serviceable zipcodes and not in seller2's serviceable zipode</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
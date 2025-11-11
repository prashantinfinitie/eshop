<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>System Settings</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Store Settings</li>
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
                    <div class="card card-info">
                        <form class="form-horizontal form-submit-event"
                            action="<?= base_url('admin/setting/update_system_settings') ?>" method="POST"
                            id="system_setting_form" enctype="multipart/form-data">
                            <input type="hidden" id="system_configurations" name="system_configurations" required=""
                                value="1" aria-required="true">
                            <input type="hidden" id="system_timezone_gmt" name="system_timezone_gmt"
                                value="<?= (isset($settings['system_timezone_gmt']) && !empty($settings['system_timezone_gmt'])) ? $settings['system_timezone_gmt'] : '+05:30'; ?>"
                                aria-required="true">
                            <input type="hidden" id="system_configurations_id" name="system_configurations_id"
                                value="13" aria-required="true">

                            <div class="card-body">
                                <!-- Basic Information Section -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Basic Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="app_name">App Name <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <input type="text" class="form-control" name="app_name"
                                                    value="<?= (isset($settings['app_name'])) ? $settings['app_name'] : '' ?>"
                                                    placeholder="Name of the App - used in whole system" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="support_number">Support Number <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <input type="text" maxlength="16" oninput="validateNumberInput(this)"
                                                    class="form-control" name="support_number"
                                                    value="<?= (isset($settings['support_number'])) ? $settings['support_number'] : '' ?>"
                                                    placeholder="Customer support mobile number" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="support_email">Support Email <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <input type="text" class="form-control" name="support_email"
                                                    value="<?= (isset($settings['support_email'])) ? $settings['support_email'] : '' ?>"
                                                    placeholder="Customer support email" />
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label for="address">Copyright Details <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <textarea name="copyright_details" id="copyright_details"
                                                    class="form-control" cols="30"
                                                    rows="3"><?= (isset($settings['copyright_details'])) ? output_escaping($settings['copyright_details']) : '' ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Branding Section -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Branding</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="logo">Logo <span
                                                            class='text-danger text-xs'>*</span><small>(Recommended
                                                            Size: 120x120 to 150x150 pixels)</small></label>
                                                    <div class="col-sm-10">
                                                        <div class='col-md-3'><a
                                                                class="uploadFile img btn btn-primary text-white btn-sm"
                                                                data-input='logo' data-isremovable='0'
                                                                data-is-multiple-uploads-allowed='0' data-toggle="modal"
                                                                data-target="#media-upload-modal"
                                                                value="Upload Photo"><i class='fa fa-upload'></i>
                                                                Upload</a></div>
                                                        <?php
                                                        if (!empty($logo)) {
                                                            ?>
                                                            <label class="text-danger mt-3">*Only Choose When Update is
                                                                necessary</label>
                                                            <div class="container-fluid row image-upload-section">
                                                                <div
                                                                    class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
                                                                    <div class=''>
                                                                        <div class='upload-media-div'><img
                                                                                class="img-fluid mb-2"
                                                                                src="<?= BASE_URL() . $logo ?>"
                                                                                alt="Image Not Found"></div>
                                                                        <input type="hidden" name="logo" id='logo'
                                                                            value='<?= $logo ?>'>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        } else { ?>
                                                            <div class="container-fluid row image-upload-section">
                                                                <div
                                                                    class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image d-none">
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="favicon">Favicon <span
                                                            class='text-danger text-xs'>*</span></label>
                                                    <div class="col-sm-10">
                                                        <div class='col-md-3'><a
                                                                class="uploadFile img btn btn-primary text-white btn-sm"
                                                                data-input='favicon' data-isremovable='0'
                                                                data-is-multiple-uploads-allowed='0' data-toggle="modal"
                                                                data-target="#media-upload-modal"
                                                                value="Upload Photo"><i class='fa fa-upload'></i>
                                                                Upload</a></div>
                                                        <?php
                                                        if (!empty($favicon)) {
                                                            ?>
                                                            <label class="text-danger mt-3">*Only Choose When Update is
                                                                necessary</label>
                                                            <div class="container-fluid row image-upload-section">
                                                                <div
                                                                    class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
                                                                    <img class="img-fluid mb-2"
                                                                        src="<?= BASE_URL() . $favicon ?>"
                                                                        alt="Image Not Found">
                                                                    <input type="hidden" name="favicon" id='favicon'
                                                                        value='<?= $favicon ?>'>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        } else { ?>
                                                            <div class="container-fluid row image-upload-section">
                                                                <div
                                                                    class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded text-center grow image d-none">
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- App Settings Section -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">App Settings</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="current_version">Current Version Of Android APP <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <input type="text" class="form-control" name="current_version"
                                                    value="<?= (isset($settings['current_version'])) ? $settings['current_version'] : '' ?>"
                                                    placeholder='Current Version For Android APP' />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="current_version">Current Version Of IOS APP <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <input type="text" class="form-control" name="current_version_ios"
                                                    value="<?= (isset($settings['current_version_ios'])) ? $settings['current_version_ios'] : '' ?>"
                                                    placeholder='Current Version For IOS APP' />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="is_version_system_on">Version System Status</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="is_version_system_on" name="is_version_system_on"
                                                        <?= (isset($settings['is_version_system_on']) && $settings['is_version_system_on'] == '1') ? 'checked' : '' ?>>
                                                    <label class="custom-control-label"
                                                        for="is_version_system_on">Enable Version System</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delivery Settings Section -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Delivery Settings</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="area_wise_delivery_charge">Zipcode/City Wise Delivery
                                                    Charge</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="area_wise_delivery_charge" name="area_wise_delivery_charge"
                                                        <?= (isset($settings['area_wise_delivery_charge']) && $settings['area_wise_delivery_charge'] == '1') ? 'checked' : '' ?>>
                                                    <label class="custom-control-label"
                                                        for="area_wise_delivery_charge">Enable Zipcode/City wise
                                                        Delivery Charges</label>
                                                </div>
                                            </div>
                                            <div
                                                class="form-group col-md-6 delivery_charge <?= (isset($settings['area_wise_delivery_charge']) && $settings['area_wise_delivery_charge'] == '1') ? 'd-none' : '' ?>">
                                                <label for="delivery_charge">Delivery Charge Amount (<?= $currency ?>)
                                                    <span class='text-danger text-xs'>*</span></label>
                                                <input type="number" class="form-control" name="delivery_charge"
                                                    value="<?= (isset($settings['delivery_charge'])) ? $settings['delivery_charge'] : '' ?>"
                                                    placeholder='Delivery Charge on Shopping' min='0' />
                                            </div>
                                            <div
                                                class="form-group col-md-6 min_amount <?= (isset($settings['area_wise_delivery_charge']) && $settings['area_wise_delivery_charge'] == '1') ? 'd-none' : '' ?>">
                                                <label for="min_amount">Minimum Amount for Free Delivery
                                                    (<?= $currency ?>) <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <input type="number" class="form-control" name="min_amount"
                                                    value="<?= (isset($settings['min_amount'])) ? $settings['min_amount'] : '' ?>"
                                                    placeholder='Minimum Order Amount for Free Delivery' min='0' />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- System Settings Section -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">System Settings</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="system_timezone">System Timezone <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <select id="system_timezone" name="system_timezone" required
                                                    class="form-control select2">
                                                    <option value="">--Select Timezone--</option>
                                                    <?php foreach ($timezone as $t): ?>
                                                        <option value="<?= $t["zone"] ?>"
                                                            data-gmt="<?= $t['diff_from_GMT']; ?>"
                                                            <?= (isset($settings['system_timezone']) && $settings['system_timezone'] == $t["zone"]) ? 'selected' : ''; ?>>
                                                            <?= $t['zone'] . ' - ' . $t['diff_from_GMT'] . ' - ' . $t['time']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="minimum_cart_amt">Minimum Cart Amount (<?= $currency ?>)
                                                    <span class='text-danger text-xs'>*</span></label>
                                                <input type="number" class="form-control" name="minimum_cart_amt"
                                                    value="<?= (isset($settings['minimum_cart_amt'])) ? $settings['minimum_cart_amt'] : '' ?>"
                                                    placeholder='Minimum Cart Amount' min='0' />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="max_items_cart">Maximum Items Allowed In Cart <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <input type="number" class="form-control" name="max_items_cart"
                                                    value="<?= (isset($settings['max_items_cart'])) ? $settings['max_items_cart'] : '' ?>"
                                                    placeholder='Maximum Items Allowed In Cart' min='1' />
                                            </div>
                                            <!-- <div class="form-group col-md-6">
                                                <label for="low_stock_limit">Low Stock Limit <small>(Product will be
                                                        considered as low stock)</small></label>
                                                <input type="number" class="form-control" name="low_stock_limit"
                                                    value="<?= (isset($settings['low_stock_limit'])) ? $settings['low_stock_limit'] : '5' ?>"
                                                    placeholder='Product low stock limit' min='1' />
                                            </div> -->
                                            <div class="form-group col-md-6">
                                                <label for="max_product_return_days">Max days to return item</label>
                                                <input type="number" class="form-control" name="max_product_return_days"
                                                    value="<?= (isset($settings['max_product_return_days'])) ? $settings['max_product_return_days'] : '' ?>"
                                                    placeholder='Max days to return item' min="0" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="delivery_boy_bonus_percentage">Delivery Boy Bonus
                                                    (%)</label>
                                                <input type="number" class="form-control"
                                                    name="delivery_boy_bonus_percentage"
                                                    value="<?= (isset($settings['delivery_boy_bonus_percentage'])) ? $settings['delivery_boy_bonus_percentage'] : '' ?>"
                                                    placeholder='Delivery Boy Bonus' min="0" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delivery Boy Settings Section -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Delivery Boy Settings</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="is_delivery_boy_otp_setting_on">Order Delivery OTP
                                                    System</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="is_delivery_boy_otp_setting_on"
                                                        name="is_delivery_boy_otp_setting_on"
                                                        <?= (isset($settings['is_delivery_boy_otp_setting_on']) && $settings['is_delivery_boy_otp_setting_on'] == '1') ? 'checked' : '' ?>>
                                                    <label class="custom-control-label"
                                                        for="is_delivery_boy_otp_setting_on">Enable OTP System</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- App & System Settings Section -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">App & System Settings</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label for="cart_btn_on_list">Cart Button on Products List</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="cart_btn_on_list" name="cart_btn_on_list"
                                                        <?= (isset($settings['cart_btn_on_list']) && $settings['cart_btn_on_list'] == '1') ? 'checked' : '' ?>>
                                                    <label class="custom-control-label" for="cart_btn_on_list">Enable
                                                        Cart Button</label>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="expand_product_images">Expand Product Images</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="expand_product_images" name="expand_product_images"
                                                        <?= (isset($settings['expand_product_images']) && $settings['expand_product_images'] == '1') ? 'checked' : '' ?>>
                                                    <label class="custom-control-label"
                                                        for="expand_product_images">Enable Image Expansion</label>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="tax_name">Tax Name</label>
                                                <input type="text" class="form-control" name="tax_name"
                                                    value="<?= (isset($settings['tax_name'])) ? $settings['tax_name'] : '' ?>"
                                                    placeholder='Example: GST Number / VAT / TIN Number' />
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="tax_number">Tax Number</label>
                                                <input type="text" class="form-control" name="tax_number"
                                                    value="<?= (isset($settings['tax_number'])) ? $settings['tax_number'] : '' ?>"
                                                    placeholder='Example: GSTIN240000120' />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Social Login</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="google_login" name="google_login"
                                                                <?= (isset($settings['google_login']) && $settings['google_login'] == '1') ? 'checked' : '' ?>>
                                                            <label class="custom-control-label"
                                                                for="google_login">Google Login</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="apple_login" name="apple_login"
                                                                <?= (isset($settings['apple_login']) && $settings['apple_login'] == '1') ? 'checked' : '' ?>>
                                                            <label class="custom-control-label" for="apple_login">Apple
                                                                Login</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>WhatsApp Settings</label>
                                                <div class="custom-control custom-switch mb-2">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="whatsapp_status" name="whatsapp_status"
                                                        <?= (isset($settings['whatsapp_status']) && $settings['whatsapp_status'] == true) ? 'checked' : '' ?>>
                                                    <label class="custom-control-label" for="whatsapp_status">Enable
                                                        WhatsApp</label>
                                                </div>

                                                <div id="whatsapp_number_div"
                                                    class="<?= (isset($settings['whatsapp_status']) && $settings['whatsapp_status'] == 1) ? '' : 'd-none' ?>">
                                                    <input type="number" class="form-control" name="whatsapp_number"
                                                        id="whatapp_number_input" placeholder="WhatsApp Number"
                                                        value="<?= isset($settings['whatsapp_number']) ? output_escaping(str_replace('\r\n', '&#13;&#10;', $settings['whatsapp_number'])) : ""; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Native Link Settings Section -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Native Link Settings For APP</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="android_app_store_link">Android Play Store Link <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <input type="text" class="form-control" id="android_app_store_link"
                                                    name="android_app_store_link"
                                                    value="<?= (isset($settings['android_app_store_link'])) ? output_escaping($settings['android_app_store_link']) : '' ?>"
                                                    placeholder="Android App Store Link" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="ios_app_store_link">iOS App Store Link <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <input type="text" class="form-control" id="ios_app_store_link"
                                                    name="ios_app_store_link"
                                                    value="<?= (isset($settings['ios_app_store_link'])) ? output_escaping($settings['ios_app_store_link']) : '' ?>"
                                                    placeholder="iOS App Store Link" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="scheme">Scheme For APP <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <input type="text" class="form-control" id="scheme" name="scheme"
                                                    value="<?= (isset($settings['scheme'])) ? output_escaping($settings['scheme']) : '' ?>"
                                                    placeholder="Scheme For APP" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="host">Domain name For APP <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <input type="text" class="form-control" id="host" name="host"
                                                    value="<?= (isset($settings['host'])) ? output_escaping($settings['host']) : '' ?>"
                                                    placeholder="Host For APP" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="app_store_id">App Store Id <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <input type="text" class="form-control" id="app_store_id"
                                                    name="app_store_id"
                                                    value="<?= (isset($settings['app_store_id'])) ? output_escaping($settings['app_store_id']) : '' ?>"
                                                    placeholder="App Store Id" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="default_country_code">Default Country Code <span
                                                        class='text-danger text-xs'>*</span><small>
                                                        (iso2 - ex. : IN)</small></label>
                                                <input type="text" class="form-control" id="default_country_code"
                                                    name="default_country_code"
                                                    value="<?= (isset($settings['default_country_code'])) ? output_escaping($settings['default_country_code']) : '' ?>"
                                                    placeholder="Default Country Code (e.g., IN)" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Refer & Earn Settings Section -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h3 class="card-title">Refer & Earn Settings</h3>
                                        <a class="btn btn-primary btn-sm text-white" data-toggle="modal"
                                            data-target="#ReferAndEarnModal" title="How it works">How Refer and Earn
                                            works?</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="is_refer_earn_on">Refer & Earn Status</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="is_refer_earn_on" name="is_refer_earn_on"
                                                        <?= (isset($settings['is_refer_earn_on']) && $settings['is_refer_earn_on'] == '1') ? 'checked' : '' ?>>
                                                    <label class="custom-control-label" for="is_refer_earn_on">Enable
                                                        Refer & Earn</label>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="min_refer_earn_order_amount">Minimum Order Amount
                                                    (<?= $currency ?>)</label>
                                                <input type="text" name="min_refer_earn_order_amount"
                                                    class="form-control"
                                                    value="<?= (isset($settings['min_refer_earn_order_amount']) && $settings['min_refer_earn_order_amount'] != '') ? $settings['min_refer_earn_order_amount'] : '' ?>"
                                                    placeholder="Amount of order eligible for bonus" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="refer_earn_bonus_times">Number of times Code can be
                                                    redeemed</label>
                                                <input type="text" class="form-control" name="refer_earn_bonus_times"
                                                    value="<?= (isset($settings['refer_earn_bonus_times'])) ? $settings['refer_earn_bonus_times'] : '' ?>"
                                                    placeholder='No of times customer will get bonus' />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="refer_earn_method_for_user">Refer & Earn Method For
                                                    User</label>
                                                <select name="refer_earn_method_for_user"
                                                    id="refer_earn_method_for_user" class="form-control">
                                                    <option value="">Select</option>
                                                    <option value="percentage"
                                                        <?= (isset($settings['refer_earn_method_for_user']) && $settings['refer_earn_method_for_user'] == "percentage") ? "selected" : "" ?>>Percentage</option>
                                                    <option value="amount"
                                                        <?= (isset($settings['refer_earn_method_for_user']) && $settings['refer_earn_method_for_user'] == "amount") ? "selected" : "" ?>>Amount</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="refer_earn_bonus_for_user">Refer & Earn Bonus For User
                                                    (<?= $currency ?> OR %)</label>
                                                <input type="text" class="form-control" name="refer_earn_bonus_for_user"
                                                    value="<?= (isset($settings['refer_earn_bonus_for_user'])) ? $settings['refer_earn_bonus_for_user'] : '' ?>"
                                                    placeholder='In amount or percentages For User' />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="max_refer_earn_amount_for_user">Maximum Refer & Earn Amount
                                                    For User (<?= $currency ?>)</label>
                                                <input type="text" class="form-control"
                                                    name="max_refer_earn_amount_for_user"
                                                    value="<?= (isset($settings['max_refer_earn_amount_for_user'])) ? $settings['max_refer_earn_amount_for_user'] : '' ?>"
                                                    placeholder='Maximum Refer & Earn Bonus Amount For User' />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="refer_earn_method_for_referal">Refer & Earn Method For
                                                    Referral</label>
                                                <input type="text" class="form-control"
                                                    name="refer_earn_method_for_referal" value="amount"
                                                    placeholder='Amount' disabled />
                                                <input type="hidden" class="form-control"
                                                    name="refer_earn_method_for_referal" value="amount"
                                                    placeholder='Amount' />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="refer_earn_bonus_for_referal">Refer & Earn Bonus For
                                                    Referral (<?= $currency ?>)</label>
                                                <input type="text" class="form-control"
                                                    name="refer_earn_bonus_for_referal"
                                                    value="<?= (isset($settings['refer_earn_bonus_for_referal'])) ? $settings['refer_earn_bonus_for_referal'] : '' ?>"
                                                    placeholder='In amount or percentages For Referral' />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Welcome Wallet Balance Section -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Welcome Wallet Balance</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="welcome_wallet_balance_on">Wallet Balance Status</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="welcome_wallet_balance_on" name="welcome_wallet_balance_on"
                                                        <?= (isset($settings['welcome_wallet_balance_on']) && $settings['welcome_wallet_balance_on'] == '1') ? 'checked' : '' ?>>
                                                    <label class="custom-control-label"
                                                        for="welcome_wallet_balance_on">Enable Welcome Wallet</label>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="wallet_balance_amount">Wallet Balance Amount
                                                    (<?= $currency ?>)</label>
                                                <input type="text" name="wallet_balance_amount" class="form-control"
                                                    value="<?= (isset($settings['wallet_balance_amount']) && $settings['wallet_balance_amount'] != '') ? $settings['wallet_balance_amount'] : '' ?>"
                                                    placeholder="Amount of Welcome Wallet Balance" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Country Currency Section -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Country Currency</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="supported_locals">Country Currency Code</label>
                                                <select name="supported_locals" class="form-control">
                                                    <?php
                                                    $CI = &get_instance();
                                                    $CI->config->load('eshop');
                                                    $supported_methods = $CI->config->item('supported_locales_list');
                                                    foreach ($supported_methods as $key => $value) {
                                                        $text = "$key - $value "; ?>
                                                        <option value="<?= $key ?>" <?= (isset($settings['supported_locals']) && !empty($settings['supported_locals']) && $key == $settings['supported_locals']) ? "selected" : "" ?>>
                                                            <?= $key . ' - ' . $value ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="currency">Store Currency (Symbol or Code) <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <input type="text" class="form-control" name="currency"
                                                    value="<?= (isset($settings['currency'])) ? $settings['currency'] : '' ?>"
                                                    placeholder="Either Symbol or Code - For Example $ or USD" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="decimal_point">Decimal Point</label>
                                                <select name="decimal_point" class="form-control">
                                                    <?php
                                                    $CI = &get_instance();
                                                    $CI->config->load('eshop');
                                                    $decimal_points = $CI->config->item('decimal_point');
                                                    foreach ($decimal_points as $key => $value) {
                                                        $text = "$key - $value "; ?>
                                                        <option value="<?= $key ?>" <?= (isset($settings['decimal_point']) && !empty($settings['decimal_point']) && $key == $settings['decimal_point']) ? "selected" : "" ?>>
                                                            <?= $value ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Settings Section -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Order Settings</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="is_single_seller_order">Single Seller Order System</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="is_single_seller_order" name="is_single_seller_order"
                                                        <?= (isset($settings['is_single_seller_order']) && $settings['is_single_seller_order'] == '1') ? 'checked' : '' ?>>
                                                    <label class="custom-control-label"
                                                        for="is_single_seller_order">Enable Single Seller Order</label>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="update_seller_flow">Show Delivery boy based on seller's
                                                    zipcode/city</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="update_seller_flow" name="update_seller_flow"
                                                        <?= (isset($settings['update_seller_flow']) && $settings['update_seller_flow'] == '1') ? 'checked' : '' ?>>
                                                    <label class="custom-control-label" for="update_seller_flow">Enable
                                                        Location-based Delivery</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cron Job URLs Section -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Cron Job URLs</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-md-8">
                                                <label for="seller_commission_url">Seller Commission URL <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                        value="<?= base_url('admin/cron-job/settle-seller-commission') ?>"
                                                        disabled />
                                                    <div class="input-group-append">
                                                        <a class="btn btn-primary text-white" data-toggle="modal"
                                                            data-target="#howItWorksModal" title="How it works">How it
                                                            works?</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-8">
                                                <label for="cashback_url">Cashback Discount URL <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                        value="<?= base_url('admin/cron_job/settle_cashback_discount') ?>"
                                                        disabled />
                                                    <div class="input-group-append">
                                                        <a class="btn btn-primary text-white" data-toggle="modal"
                                                            data-target="#howItWorksModal1" title="How it works">How it
                                                            works?</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-8">
                                                <label for="cart_url">Remaining Cart Items URL <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <input type="text" class="form-control"
                                                    value="<?= base_url('admin/cron_job/remaining_cart') ?>" disabled />
                                            </div>
                                            <div class="form-group col-md-8">
                                                <label for="draft_order_url">Delete Draft Orders URL <span
                                                        class='text-danger text-xs'>*</span></label>
                                                <input type="text" class="form-control"
                                                    value="<?= base_url('admin/cron_job/draft_order_settel') ?>"
                                                    disabled />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Maintenance Mode Section -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Maintenance Mode</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="is_customer_app_under_maintenance">Customer App</label>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="is_customer_app_under_maintenance"
                                                            name="is_customer_app_under_maintenance"
                                                            <?= (isset($settings['is_customer_app_under_maintenance']) && $settings['is_customer_app_under_maintenance'] == '1') ? 'checked' : '' ?>>
                                                        <label class="custom-control-label"
                                                            for="is_customer_app_under_maintenance">Enable Maintenance
                                                            Mode</label>
                                                    </div>
                                                    <textarea class="form-control mt-2" id="message_for_customer_app" rows="5"
                                                        name="message_for_customer_app"
                                                        placeholder="Message for Customer App"><?= isset($settings['message_for_customer_app']) ? output_escaping(str_replace('\r\n', '&#13;&#10;', $settings['message_for_customer_app'])) : ""; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="is_seller_app_under_maintenance">Seller App</label>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="is_seller_app_under_maintenance"
                                                            name="is_seller_app_under_maintenance"
                                                            <?= (isset($settings['is_seller_app_under_maintenance']) && $settings['is_seller_app_under_maintenance'] == '1') ? 'checked' : '' ?>>
                                                        <label class="custom-control-label"
                                                            for="is_seller_app_under_maintenance">Enable Maintenance
                                                            Mode</label>
                                                    </div>
                                                    <textarea class="form-control mt-2" id="message_for_seller_app" rows="5"
                                                        name="message_for_seller_app"
                                                        placeholder="Message for Seller App"><?= isset($settings['message_for_seller_app']) ? output_escaping(str_replace('\r\n', '&#13;&#10;', $settings['message_for_seller_app'])) : ""; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="is_delivery_boy_app_under_maintenance">Delivery Boy
                                                        App</label>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="is_delivery_boy_app_under_maintenance"
                                                            name="is_delivery_boy_app_under_maintenance"
                                                            <?= (isset($settings['is_delivery_boy_app_under_maintenance']) && $settings['is_delivery_boy_app_under_maintenance'] == '1') ? 'checked' : '' ?>>
                                                        <label class="custom-control-label"
                                                            for="is_delivery_boy_app_under_maintenance">Enable
                                                            Maintenance Mode</label>
                                                    </div>
                                                    <textarea class="form-control mt-2" rows="5"
                                                        id="message_for_delivery_boy_app"
                                                        name="message_for_delivery_boy_app"
                                                        placeholder="Message for Delivery Boy App"><?= isset($settings['message_for_delivery_boy_app']) ? output_escaping(str_replace('\r\n', '&#13;&#10;', $settings['message_for_delivery_boy_app'])) : ""; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="is_web_under_maintenance">Web Maintenance Mode</label>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="is_web_under_maintenance"
                                                            name="is_web_under_maintenance"
                                                            <?= (isset($settings['is_web_under_maintenance']) && $settings['is_web_under_maintenance'] == '1') ? 'checked' : '' ?>>
                                                        <label class="custom-control-label"
                                                            for="is_web_under_maintenance">Enable Maintenance
                                                            Mode</label>
                                                    </div>
                                                    <textarea class="form-control mt-2" id="message_for_web" rows="5"
                                                        name="message_for_web"
                                                        placeholder="Message for Web"><?= isset($settings['message_for_web']) ? output_escaping(str_replace('\r\n', '&#13;&#10;', $settings['message_for_web'])) : ""; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="d-flex">
                                    <button type="reset" class="btn btn-warning mr-3">Reset</button>
                                    <button type="submit" class="btn btn-success float-right" id="submit_btn">Update
                                        Settings</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="howItWorksModal1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">How Promo Code Discount will get credited?</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body ">
                        <ol>
                            <li>Cron job must be set on your server for Promo Code Discount to be work.</li>

                            <li> Cron job will run every mid night at 12:00 AM. </li>

                            <li> Formula for Add Promo Code Discount is <b>Sub total (Excluding delivery charge) - promo
                                    code discount percentage / Amount</b> </li>

                            <li> For example sub total is 1300 and promo code discount is 100 then 1300 - 100 = 1200 so
                                100 will get credited into Users's wallet </li>

                            <li> If Order status is delivered And Return Policy is expired then only users will get
                                Promo Code Discount. </li>

                            <li> Ex - 1. Order placed on 10-Sep-22 and return policy days are set to 1 so 10-Sep + 1
                                days = 11-Sep Promo code discount will get credited on 11-Sep-22 at 12:00 AM (Mid night)
                            </li>

                            <li> If Promo Code Discount doesn't works make sure cron job is set properly and it is
                                working. If you don't know how to set cron job for once in a day please take help of
                                server support or do search for it. </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="howItWorksModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">How seller commission will get credited?</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body ">
                        <ol>
                            <li>
                                Cron job must be set (For once in a day) on your server for seller commission to be
                                work.
                            </li>
                            <li>
                                Cron job will run every mid night at 12:00 AM.
                            </li>
                            <li>
                                Formula for seller commision is <b>Sub total (Excluding delivery charge) / 100 * seller
                                    commission percentage</b>
                            </li>
                            <li>
                                For example sub total is 1378 and seller commission is 20% then 1378 / 100 X 20 = 275.6
                                so 1378 - 275.6 = 1102.4 will get credited into seller's wallet
                            </li>
                            <li>
                                If Order item's status is delivered then only seller will get commisison.
                            </li>
                            <li>
                                Ex - 1. Order placed on 11-Aug-21 and product return days are set to 0 so 11-Aug + 0
                                days = 11-Aug seller commission will get credited on 12-Aug-21 at 12:00 AM (Mid night)
                            </li>
                            <li>
                                Ex - 2. Order placed on 11-Aug-21 and product return days are set to 7 so 11-Aug + 7
                                days = 18-Aug seller commission will get credited on 19-Aug-21 at 12:00 AM (Mid night)
                            </li>
                            <li>
                                If seller commission doesn't works make sure cron job is set properly and it is working.
                                If you don't know how to set cron job for once in a day please take help of server
                                support or do search for it.
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ReferAndEarnModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">How Refer and Earn work For referal and users?</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body ">
                        <h6 class="text-bold">Field Details : </h6>
                        <ol>
                            <li>
                                <div class="d-flex flex-column">
                                    <p class="mb-0">Referal Code On / Off:</p>
                                    <p>This is For if you want to on refer and earn functionality in your system.</p>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex flex-column">
                                    <p class="mb-0">Minimum Refer & Earn Order Amount :</p>
                                    <p class="mb-0"><span class="text-bold"> Description :</span> This is the minimum
                                        order amount required for a referral to be considered valid for earning rewards.
                                    </p>
                                    <p><span class="text-bold">Example : </span> if this amount is set to $500, a
                                        referred user must place an order of at least $500 for the referrer to earn a
                                        bonus.</p>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex flex-column">
                                    <p class="mb-0"> Number of times Code can be redeemed:</p>
                                    <p class="mb-0"><span class="text-bold"> Description :</span> This specifies how
                                        many times a referral code can be used by different users.</p>
                                    <p><span class="text-bold">Example :</span> if the limit is set to 5, the referral
                                        code can only be redeemed five times across different users.</p>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex flex-column">
                                    <p class="mb-0">Refer & Earn Method For User :</p>
                                    <p class="mb-0"><span class="text-bold"> Description:</span> This indicates how the
                                        user (the one who use the referral code) earns their reward when they makes a
                                        firat order. It could be in the form of a percentage of the order amount or fix
                                        amount.<br>
                                    <p><span class="text-bold"> Example:</span> If the method is set as "Fixed Amount,"
                                        the user might earn $10 for each successful referral.</p>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex flex-column">
                                    <p class="mb-0"> Refer & Earn Bonus For User :</p>
                                    <p class="mb-0"><span class="text-bold"> Description:</span> This is the actual
                                        bonus or reward amount the user(the one who use the referral code) earns per
                                        successful referral. The bonus could be a fixed amount, a percentage of the
                                        first order.</p>
                                    <p><span class="text-bold"> Example:</span> If the bonus is set to $10, the referrer
                                        earns $10 for user(the one who use the referral code) first order.</p>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex flex-column">
                                    <p class="mb-0"> Maximum Refer & Earn Amount For User :</p>
                                    <p class="mb-0"><span class="text-bold"> Description:</span> This is the maximum
                                        total amount a user can earn through the referral program. Once this limit is
                                        reached, the user can no longer earn rewards from further referrals.</p>
                                    <p><span class="text-bold"> Example:</span> If the maximum amount is set to $100,
                                        the user can earn up to $100 cashback, after which no more rewards will be
                                        given.</p>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex flex-column">
                                    <p class="mb-0"> Refer & Earn Method For Referral :</p>
                                    <p class="mb-0"><span class="text-bold"> Description:</span> This specifies how the
                                        referred person (the one who share the referral code) receives their reward.
                                        Like the referrer, the referral can also receive a reward in cashback.</p>
                                    <p><span class="text-bold"> Example:</span> The method could be a "Fixed Amount"
                                        giving the referred user 100$ off their first purchase.</p>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex flex-column">
                                    <P class="mb-0">Refer & Earn Bonus For Referral :</P>
                                    <P class="mb-0"><span class="text-bold"> Description: </span> This is the bonus or
                                        reward that the referred person receives when they use the referral code and
                                        complete a qualifying action, such as making a purchase.</P>
                                    <P class="mb-0"><span class="text-bold">Example:</span> If the bonus is $50 off
                                        their first purchase of user(the one who use the referral code), the referal(the
                                        one who share the referral code) receives a $50 for user order.</P>
                                </div>
                            </li>

                        </ol>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
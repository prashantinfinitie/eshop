<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Affiliate Settings</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Affiliate Settings</li>
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

                        <div class="card-body">
                            <form class="form-horizontal form-submit-event" action="<?= base_url('admin/affiliate_settings/update_affiliate_settings') ?>" method="POST"
                                id="system_setting_form" enctype="multipart/form-data">
                                <!-- Basic Information Section -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Basic Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="">
                                            <!-- <div class="form-group col-md-6">

                                                <label for="account_maintainance_fees">
                                                    Account Maintenance Fees <span class='text-danger text-xs'>*</span>
                                                    <i class="fas fa-info-circle text-info" data-toggle="popover" data-placement="right" data-content="Enter monthly fees charged to maintain the account."></i>
                                                </label>
                                                <input type="text" class="form-control" name="account_maintainance_fees"
                                                    value="<?//= (isset($affiliate_settings['account_maintainance_fees'])) ? $affiliate_settings['account_maintainance_fees'] : '' ?>"
                                                    placeholder="Account Maintainance Fees" />
                                            </div> -->
                                            <div class="form-group col-md-6">

                                                <label for="account_delete_days">
                                                    Permanent Account Delete Days <span class='text-danger text-xs'>*</span>
                                                    <i class="fas fa-info-circle text-info" data-toggle="popover" data-placement="right" data-content="After days account will permanent delete."></i>
                                                </label>
                                                <input type="text" class="form-control" name="account_delete_days"
                                                    value="<?= (isset($affiliate_settings['account_delete_days'])) ? $affiliate_settings['account_delete_days'] : '' ?>"
                                                    placeholder="Account Delete Days" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="max_amount_for_withwrawal_req">
                                                    Max Amount for Withdrawal Request <span class='text-danger text-xs'>*</span>
                                                    <i class="fa fa-info-circle text-info" data-toggle="popover" data-placement="right" data-content="Maximum limit a user can request to withdraw at a time."></i>
                                                </label>
                                                <input type="text" maxlength="16"
                                                    class="form-control" name="max_amount_for_withwrawal_req"
                                                    value="<?= (isset($affiliate_settings['max_amount_for_withwrawal_req'])) ? $affiliate_settings['max_amount_for_withwrawal_req'] : '' ?>"
                                                    placeholder="Max Amount for Withdrawal Request" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="min_amount_for_withwrawal_req">
                                                    Min Balance for Withdrawal Request <span class='text-danger text-xs'>*</span>
                                                    <i class="fa fa-info-circle text-info" data-toggle="popover" data-placement="right" data-content="Minimum balance required to place a withdrawal request."></i>
                                                </label>
                                                <input type="text" maxlength="16"
                                                    class="form-control" name="min_amount_for_withwrawal_req"
                                                    value="<?= (isset($affiliate_settings['min_amount_for_withwrawal_req'])) ? $affiliate_settings['min_amount_for_withwrawal_req'] : '' ?>"
                                                    placeholder="Min Amount for Withdrawal Request" />
                                            </div>

                                        </div>
                                        <!-- Form Actions -->
                                        <div class="d-flex mx-2">
                                            <button type="reset" class="btn btn-warning mr-3">Reset</button>
                                            <button type="submit" class="btn btn-success float-right" id="submit_btn">Update
                                                Settings</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <form action="<?= base_url('admin/affiliate_settings/update_commission') ?>" method="post" id="affiliate_commission_form" class="form-horizontal form-submit-event" enctype="multipart/form-data">
                                <!-- Basic Information Section -->
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Affiliate Commission <small>(%)</small></h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <div id="repeater">
                                                <?php
                                                // Build list of used category IDs (having commission > 0)
                                                $used_category_ids = [];
                                                foreach ($affiliate_commissions as $ac) {
                                                    if (floatval($ac['affiliate_commission']) > 0) {
                                                        $used_category_ids[] = $ac['id'];
                                                    }
                                                }

                                                if (!empty($affiliate_commissions)) {
                                                    foreach ($affiliate_commissions as $commission_data) {
                                                        if (floatval($commission_data['affiliate_commission']) > 0) {
                                                ?>
                                                            <div class="repeater-item col-md-8">
                                                                <div class="d-flex mb-3">
                                                                    <select name="category_parent[]" class="category_parent form-control mx-3 w-100" >
                                                                        <option value="">Select Category</option>
                                                                        <?= get_categories_option_html($categories, [$commission_data['id']], $used_category_ids); ?>
                                                                    </select>
                                                                    <input type="text" class="form-control mx-3" name="commission[]" placeholder="Commission"
                                                                        value="<?= htmlspecialchars($commission_data['affiliate_commission']) ?>" >
                                                                    <a type="button" class="remove-btn"><i class="fa-2x fa-times-circle fas text-danger"></i></a>
                                                                </div>
                                                            </div>
                                                    <?php
                                                        }
                                                    }
                                                } else {
                                                    ?>
                                                    <div class="repeater-item col-md-8">
                                                        <div class="d-flex mb-3">
                                                            <select name="category_parent[]" class="category_parent form-control mx-3 w-100" >
                                                                <option value="">Select Category</option>
                                                                <?= get_categories_option_html($categories, [], $used_category_ids); ?>
                                                            </select>
                                                            <input type="text" class="form-control mx-3" name="commission[]" placeholder="Commission" >
                                                            <a type="button" class="remove-btn"><i class="fa-2x fa-times-circle fas text-danger"></i></a>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>


                                            <button type="button" id="add-more" class="btn btn-primary mx-2">Add More</button>

                                        </div>
                                        <!-- Form Actions -->
                                        <div class="d-flex mx-2">
                                            <button type="reset" class="btn btn-warning mr-3">Reset</button>
                                            <button type="submit" class="btn btn-success float-right">Update commission</button>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>

<script>
    const categoriesData = <?= json_encode($categories) ?>;
</script>

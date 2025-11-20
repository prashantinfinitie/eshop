<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Manage Quotes</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('shipping_company/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Quotes</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <button class="btn btn-sm btn-primary" id="addQuoteBtn">
                                <i class="fa fa-plus"></i> Add Quote
                            </button>
                        </div>
                    </div>

                    <table id="quotesTable" class="table table-striped"
                        data-toggle="table"
                        data-url="<?= base_url('shipping_company/quotes/list') ?>"
                        data-click-to-select="true"
                        data-side-pagination="server"
                        data-pagination="true"
                        data-page-list="[5, 10, 20, 50, 100, 200]"
                        data-search="true"
                        data-show-columns="true"
                        data-show-refresh="true"
                        data-trim-on-search="false"
                        data-sort-name="id"
                        data-sort-order="desc"
                        data-mobile-responsive="true"
                        data-toolbar=""
                        data-show-export="true"
                        data-maintain-selected="true"
                        data-export-types='["txt","excel"]'
                        data-query-params="shipping_company_status_params">
                        <thead>
                            <tr>
                                <th data-field="id" data-sortable="true">ID</th>
                                <th data-field="zipcode" data-sortable="true">Zipcode</th>
                                <th data-field="price" data-sortable="true">Price (<?= isset($currency) ? $currency : '' ?>)</th>
                                <th data-field="eta_text">ETA</th>
                                <th data-field="cod_available">COD</th>
                                <th data-field="additional_charges">Add. Charges</th>
                                <th data-field="is_active" data-sortable="true">Status</th>
                                <th data-field="operate" data-sortable="false">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="quoteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="quoteForm" class="modal-content">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title text-dark" id="quoteModalTitle">Add Quote</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body p-4">

                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                    value="<?= $this->security->get_csrf_hash(); ?>" />

                <input type="hidden" name="id" id="quote_id" value="">

                <!-- Zipcode -->
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-dark">Zipcode <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <select name="zipcode" id="zipcode" class="form-control" required>
                            <option value="">Select Zipcode</option>
                            <?php if (!empty($zipcodes)) {
                                foreach ($zipcodes as $z) { ?>
                                    <option value="<?= html_escape($z) ?>"><?= html_escape($z) ?></option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                </div>

                <!-- Price -->
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-dark">Base Price (<?= $currency ?>) <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <input type="number" step="0.01" min="0" name="price" id="price" class="form-control"
                            placeholder="Enter base delivery price" required>
                    </div>
                </div>

                <!-- ETA -->
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-dark">ETA Text <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" maxlength="50" name="eta_text" id="eta_text" class="form-control"
                            placeholder="e.g. 2–3 days" required>
                    </div>
                </div>

                <!-- COD Available -->
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-dark">COD Available</label>
                    <div class="col-sm-9">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="cod_yes" name="cod_available" value="1" class="custom-control-input">
                            <label class="custom-control-label" for="cod_yes">Yes</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="cod_no" name="cod_available" value="0" class="custom-control-input">
                            <label class="custom-control-label" for="cod_no">No</label>
                        </div>
                    </div>
                </div>

                <!-- Additional charges -->
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-dark">Additional Charges</label>
                    <div class="col-sm-9">
                        <div id="additionalChargesContainer">
                            <!-- Dynamic charge rows will be added here -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="addChargeBtn">
                            <i class="fa fa-plus"></i> Add Charge
                        </button>
                        <input type="hidden" name="additional_charges" id="additional_charges_json">
                    </div>
                </div>

                <!-- Status -->
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-dark">Status</label>
                    <div class="col-sm-9">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="status_active" name="is_active" value="1" class="custom-control-input">
                            <label class="custom-control-label" for="status_active">Active</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="status_inactive" name="is_active" value="0" class="custom-control-input">
                            <label class="custom-control-label" for="status_inactive">Inactive</label>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="submit" id="saveQuoteBtn" class="btn btn-primary">
                    <i class="fa fa-save"></i> Save Quote
                </button>
            </div>
        </form>
    </div>
</div>


<script src="/assets/shipping_company/js/quotes.js"></script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Manage Products</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="modal fade" id="product-affiliate-modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form id="affiliateForm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Affiliate Settings</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <input type="hidden" name="product_id" id="modal_product_id">

                                <div class="form-group">
                                    <label for="modal_product_name">Product Name</label>
                                    <input type="text" class="form-control" id="modal_product_name" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="modal_is_in_affiliate">Is in Affiliate</label>
                                    <select class="form-control" name="is_in_affiliate" id="modal_is_in_affiliate">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>

                                <!-- Add other fields as needed -->

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary affiliateFormSave">Save</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bulk Affiliate Modal -->
            <div class="modal fade" id="bulkAffiliateModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <form id="bulkAffiliateForm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Bulk Update Affiliate Status</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <label for="bulk_affiliate_status">Is In Affiliate:</label>
                                <select name="is_in_affiliate" id="bulk_affiliate_status" class="form-control" required>
                                    <option value="">Select Status</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <div class="col-md-12 main-content">
                <div class="card content-area p-4">

                    <div class="card-innr">
                        <div id="mediaToolbar">
                            <!-- <button id="media_remove" class="btn btn-outline-danger"> Bulk Update </button> -->
                            <button type="button" class="btn btn-danger mb-3" id="openBulkModal">Bulk Update</button>

                        </div>
                        <div class="gaps-1-5x"></div>
                        <table class='table-striped' id='products_affiliate_table' data-toggle="table" data-url="<?= base_url('admin/product/get_affiliate_product_data_list') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-export-types='["txt","excel","csv"]' data-export-options='{
                            "fileName": "products-list",
                            "ignoreColumn": ["state"]
                            }' data-query-params="product_query_params">
                            <thead>
                                <tr>
                                    <th data-field="state" data-checkbox="true"></th>

                                    <th data-field="id" data-sortable="true" data-visible='false' data-align='center'>ID</th>
                                    <th data-field="image" data-sortable="true" data-align='center'>Image</th>
                                    <th data-field="name" data-sortable="false" data-align='center'>Name</th>
                                    <th data-field="brand" data-sortable="false" data-align='center' data-visible="false">Brand</th>
                                    <th data-field="is_in_affiliate_status" data-sortable="false" data-align='center'>Is In Affiliate</th>
                                    <th data-field="category_name" data-sortable="false" data-align='center'>Category Name</th>
                                    <th data-field="operate" data-sortable="false" data-align='center'>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div><!-- .card-innr -->
                </div><!-- .card -->
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4> Offers Management </h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Offers</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 main-content">
                    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id='add_offer'>
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Offer Details</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body p-0">
                                    <form class="form-horizontal form-submit-event add_offer_form" method="POST" id="add_offer_form" enctype="multipart/form-data">
                                        <div class="card-body">

                                            <div class="form-group">
                                                <input type="hidden" name="edit_offer" id="edit_offer" value="">
                                                <label for="offer_type">Type <span class='text-danger text-sm'>*</span> </label>
                                                <select name="offer_type" id="offer_type" class="form-control type_event_trigger" required="">
                                                    <option value="">Select Type</option>
                                                    <option value="default">Default</option>
                                                    <option value="categories">Category</option>
                                                    <option value="products">Product</option>
                                                    <option value="offer_url">Offer URL</option>
                                                </select>
                                            </div>
                                            <div id="type_add_html">
                                                <div class="form-group offer-categories d-none ">

                                                    <label for="category_id"> Categories <span class='text-danger text-sm'>*</span></label>
                                                    <select id="category_parent" name="category_id">
                                                        <option value=""><?= (isset($categories) && empty($categories)) ? 'No Categories Exist' : 'Select Categories' ?>
                                                        </option>
                                                        <?php
                                                        echo get_categories_option_html($categories);
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group offer-url d-none ">

                                                    <label for="offer_url"> Link <span class='text-danger text-sm'>*</span></label>
                                                    <input type="text" class="form-control" placeholder="https://example.com" name="link" id="offer_url_val" value="">
                                                </div>
                                                <div class="form-group row offer-products d-none">
                                                    <label for="product_id" class="control-label">Products <span class='text-danger text-sm'>*</span></label>
                                                    <div class="col-md-12">
                                                        <select name="product_id" id="product_offer_id" class="search_admin_faq_product w-100" data-placeholder=" Type to search and select products" onload="multiselect()">
                                                            <?php
                                                            $product_details = fetch_details('products', '', 'id,name');
                                                            if (!empty($product_details)) {
                                                                foreach ($product_details as $value) {
                                                            ?>
                                                                    <option value="<?= $value['id'] ?>"> <?= $value['name'] ?></option>
                                                            <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="form-group">
                                                <div><label for="image" class="form-label form-label-sm">Offer Image <span class='text-danger text-sm'>*</span><small>(Recommended Size : 1648 x 610 pixels)</small></label></div>
                                                <div class="col-sm-12">
                                                    <div class='col-md-6'><a class="uploadFile img btn btn-primary text-white btn-sm" data-input='image' data-isremovable='0' data-is-multiple-uploads-allowed='0' data-toggle="modal" data-target="#media-upload-modal" value="Upload Photo"><i class='fa fa-upload'></i> Upload</a></div>
                                                    <label class="text-danger mt-3 edit_offer_upload_image_note">*Only Choose When Update is necessary</label>
                                                    <div class="container-fluid row image-upload-section">
                                                        <div class="col-md-6 col-sm-12 shadow p-3 bg-white rounded text-center grow image">
                                                            <div class='image-upload-div'>
                                                                <img class="img-fluid mb-2" id="offer_uploaded_image" src="<?= base_url() . NO_IMAGE ?>" alt="Image Not Found">
                                                                <input type="hidden" name="image" id="uploaded_offer_uploaded_image" class="uploaded_image_here form-control form-input">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <button type="reset" class="btn btn-warning">Reset</button>
                                                <button type="submit" class="btn btn-success save_offer" id="submit_btn"><?= (isset($fetched_data[0]['id'])) ? 'Update Offer' : 'Add Offer' ?></button>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card content-area p-4">
                        <div class="card-header border-0">
                            <div class="card-tools">
                                <button type="button" class="btn btn-block  btn-outline-primary btn-sm add_offer_btn" data-toggle="modal" data-target="#add_offer">
                                    Add Offer
                                </button>
                            </div>
                        </div>
                        <div class="card-innr">
                            <div class="gaps-1-5x"></div>
                            <table class='table-striped' data-toggle="table" data-url="<?= base_url('admin/offer/view_offers') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-query-params="queryParams">
                                <thead>
                                    <tr>
                                        <th data-field="id" data-sortable="true" data-align='center'>ID</th>
                                        <th data-field="type" data-sortable="false" data-align='center'>Type</th>
                                        <th data-field="type_id" data-sortable="true" data-align='center' data-visible='false'>Type id</th>
                                        <th data-field="name" data-sortable="false" data-align='center'>Name</th>
                                        <th data-field="image" data-sortable="false" class="col-md-6" data-align='center'>Image</th>
                                        <th data-field="link" data-sortable="false" data-align='center'>Link</th>
                                        <th data-field="date_added" data-sortable="false" data-align='center'>Created at</th>
                                        <th data-field="operate" data-sortable="false" data-align='center'>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div><!-- .card-innr -->
                    </div><!-- .card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
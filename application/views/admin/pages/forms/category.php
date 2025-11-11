<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Add Category</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active"><a href="<?= base_url('admin/category') ?>">Category</a></li>
                        <li class="breadcrumb-item active">Add Category</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <!-- form start -->
                        <form class="form-horizontal form-submit-event" action="<?= base_url('admin/category/add_category'); ?>" method="POST" id="add_product_form" enctype="multipart/form-data">
                            <?php if (isset($fetched_data[0]['id'])) { ?>
                                <input type="hidden" name="edit_category" value="<?= @$fetched_data[0]['id'] ?>">
                            <?php } ?>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="category_input_name" class="col-sm-2 col-form-label">Name <span class='text-danger text-sm'>*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="category_input_name" placeholder="Category Name" name="category_input_name" value="<?= isset($fetched_data[0]['name']) ? output_escaping($fetched_data[0]['name']) : "" ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="category_parent" class="col-sm-2 col-form-label">Select Parent</label>
                                    <div class="col-sm-10">
                                        <select id="category_parent" name="category_parent">
                                            <option value=""><?= (isset($categories) && empty($categories)) ? 'No Categories Exist' : 'Select Parent' ?>
                                            </option>
                                            <?php
                                            $selected_val = (isset($fetched_data[0]['id']) &&  !empty($fetched_data[0]['id'])) ? $fetched_data[0]['parent_id'] : '';
                                            $selected_vals = explode(',', $selected_val);
                                            echo get_categories_option_html($categories, $selected_vals);

                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="image">Main Image <span class='text-danger text-sm'>*</span><small>(Recommended Size : 131 x 131 pixels)</small></label>
                                    <div class="col-sm-10">
                                        <div class='col-md-3'><a class="uploadFile img btn btn-primary text-white btn-sm" data-input='category_input_image' data-isremovable='0' data-is-multiple-uploads-allowed='0' data-toggle="modal" data-target="#media-upload-modal" value="Upload Photo"><i class='fa fa-upload'></i> Upload</a></div>
                                        <?php
                                        if (file_exists(FCPATH . @$fetched_data[0]['image']) && !empty(@$fetched_data[0]['image'])) {
                                        ?>
                                            <label class="text-danger mt-3">*Only Choose When Update is necessary</label>
                                            <div class="container-fluid row image-upload-section">
                                                <div class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
                                                    <div class='image-upload-div'><img class="img-fluid mb-2" src="<?= BASE_URL() . $fetched_data[0]['image'] ?>" alt="Image Not Found"></div>
                                                    <input type="hidden" name="category_input_image" value='<?= $fetched_data[0]['image'] ?>'>
                                                </div>
                                            </div>
                                        <?php
                                        } else { ?>
                                            <div class="container-fluid row image-upload-section">
                                                <div class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image d-none"></div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <hr class="mt-4">
                                <h4 class="bg-light m-0 px-2 py-3">SEO Configuration</h4>

                                <div class="d-flex bg-light">
                                    <div class="form-group col-sm-6">
                                        <label for="seo_page_title" class="form-label form-label-sm d-flex">
                                            SEO Page Title
                                        </label>
                                        <input type="text" class="form-control" id="seo_page_title"
                                            placeholder="SEO Page Title" name="seo_page_title"
                                            value="<?= isset($fetched_data[0]['seo_page_title']) ? output_escaping($fetched_data[0]['seo_page_title']) : "" ?>">
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label for="seo_meta_keywords" class="form-label form-label-sm d-flex">
                                            SEO Meta Keywords
                                        </label>
                                        <input name='seo_meta_keywords' class='tags bg-white' id='seo_meta_keywords' placeholder="SEO Meta Keywords" value="<?= isset($fetched_data[0]['seo_meta_keywords']) ? output_escaping($fetched_data[0]['seo_meta_keywords']) : "" ?>" />
                                    </div>
                                </div>
                                <div class="d-flex bg-light">

                                    <div class="form-group col-sm-6">
                                        <label for="seo_meta_description" class="form-label form-label-sm d-flex">
                                            SEO Meta Description
                                        </label>
                                        <textarea class="form-control" id="seo_meta_description"
                                            placeholder="SEO Meta Keywords" name="seo_meta_description"><?= isset($fetched_data[0]['seo_meta_description']) ? output_escaping($fetched_data[0]['seo_meta_description']) : "" ?></textarea>
                                    </div>

                                    <div class="col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <label for="image">SEO Open Graph Image <small>(Recommended Size : 131 x 131 pixels)</small></label>
                                            <div class="col-sm-10">
                                                <div class='col-md-12'>
                                                    <a class="uploadFile img btn btn-primary text-white btn-sm" data-input='seo_og_image' data-isremovable='1'
                                                        data-is-multiple-uploads-allowed='0' data-toggle="modal" data-target="#media-upload-modal" value="Upload"><i class='fa fa-upload'></i> Upload</a>
                                                </div>
                                                <?php
                                                if (!empty(@$fetched_data[0]['seo_og_image'])) {
                                                ?>
                                                    <label class="text-danger mt-3">*Only Choose When Update is
                                                        necessary</label>
                                                    <div class="container-fluid row image-upload-section">
                                                        <div class="col-md-12 col-sm-12 shadow p-3 bg-white rounded text-center grow image">
                                                            <div class='image-upload-div'><img class="img-fluid mb-2"
                                                                    src="<?= base_url() . str_replace('//', '/', $fetched_data[0]['seo_og_image']) ?>"
                                                                    alt="Image Not Found"></div>
                                                            <input type="hidden" name="seo_og_image" value='<?= $fetched_data[0]['seo_og_image'] ?>'>
                                                        </div>
                                                    </div>
                                                <?php
                                                } else { ?>
                                                    <div class="container-fluid row image-upload-section">
                                                        <div
                                                            class="col-md-12 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image d-none">
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group my-3">
                                    <button type="reset" class="btn btn-warning ">Reset</button>
                                    <button type="submit" class="btn btn-success" id="submit_btn"><?= (isset($fetched_data[0]['id'])) ? 'Update Category' : 'Add Category' ?></button>
                                </div>
                            </div>

                    </div>
                    <!-- /.card-footer -->
                    </form>
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
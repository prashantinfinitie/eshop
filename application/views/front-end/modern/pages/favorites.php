<!-- breadcrumb -->
<div class="content-wrapper deeplink_wrapper">
    <section class="wrapper bg-soft-grape">
        <div class="container py-3 py-md-5">
            <nav class="d-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('my-account/profile') ?>" class="text-decoration-none"><?= !empty($this->lang->line('dashboard')) ? str_replace('\\', '', $this->lang->line('dashboard')) : 'Dashboard' ?></a></li>
                    <?php if (isset($right_breadcrumb) && !empty($right_breadcrumb)) {
                        foreach ($right_breadcrumb as $row) {
                    ?>
                            <li class="breadcrumb-item"><?= $row ?></li>
                    <?php }
                    } ?>
                    <li class="breadcrumb-item active text-muted" aria-current="page"><?= !empty($this->lang->line('favorite')) ? str_replace('\\', '', $this->lang->line('favorite')) : 'favorites' ?></li>
                </ol>
            </nav>
            <!-- /nav -->
        </div>
        <!-- /.container -->
    </section>
</div>
<!-- end breadcrumb -->

<section class="my-account-section">
    <div class="container mb-15">
        <div class="my-8">
            <?php $this->load->view('front-end/' . THEME . '/pages/dashboard') ?>
        </div>
        <div class="col-12 fav-row">
            <div class='border-0 row'>
                <div class="bg-white">
                    <h1 class="h4"><?= !empty($this->lang->line('favorite')) ? str_replace('\\', '', $this->lang->line('favorite')) : 'favorites' ?></h1>
                </div>
                <hr class="my-5">
                <?php
                if (isset($products) && !empty($products)) {
                    foreach ($products as $row) {
                        if ($row['type'] == 'simple_product') {
                            $product_stock = $row['stock'];
                        }else{
                            $product_stock = $row['total_stock'];
                        }
                ?>
                        <div class="my-3 px-0" title="<?= $row['name']; ?>">
                            <div class="d-flex">
                                <div class="col-md-4 px-0">
                                    <div class="">
                                        <div class="product-image item">
                                            <figure class="rounded">
                                                <a href="<?= base_url('products/details/' . $row['slug']) ?>">
                                                    <img class="pic-1 lazy fig_image w-100 image-fit" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('media/image?path=' . rawurlencode($row['relative_path']) . '&width=640&quality=80') ?>">
                                                </a>
                                                <div class="desktop_quick_view">
                                                    <a class="item-like text-decoration-none add-to-fav-btn  <?= ($row['is_favorite'] == 1) ? 'fa fa-heart text-danger' : 'fa fa-heart-o' ?> " href="#" data-bs-toggle="white-tooltip" title="<?= ($row['is_favorite'] == 1) ? 'Remove from wishlist' : 'Add to wishlist' ?>" data-product-id="<?= $row['id'] ?>">
                                                        <i class=""></i>
                                                    </a>

                                                    <a href="#" class="quick-view-btn item-view text-decoration-none" data-bs-toggle="white-tooltip" title="<?= !empty($this->lang->line('quick_view')) ? str_replace('\\', '', $this->lang->line('quick_view')) : 'Quick View' ?>" data-tip="<?= !empty($this->lang->line('quick_view')) ? str_replace('\\', '', $this->lang->line('quick_view')) : 'Quick View' ?>" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $row['variants'][0]['id'] ?>" data-izimodal-open="#quick-view">
                                                        <i class="uil uil-eye"></i>
                                                    </a>
                                                    <?php
                                                    if (count($row['variants']) <= 1) {
                                                        $variant_id = $row['variants'][0]['id'];
                                                        $modal = "";
                                                    } else {
                                                        $variant_id = "";
                                                        $modal = "#quick-view";
                                                    }
                                                    ?>

                                                    <?php
                                                    if (count($row['variants']) <= 1) {
                                                        $variant_id = $row['variants'][0]['id'];
                                                    } else {
                                                        $variant_id = "";
                                                    }
                                                    ?>
                                                    <a href="#" class="compare item-compare text-decoration-none" data-bs-toggle="white-tooltip" title="<?= !empty($this->lang->line('compare')) ? str_replace('\\', '', $this->lang->line('compare')) : 'Compare' ?>" data-tip="<?= !empty($this->lang->line('compare')) ? str_replace('\\', '', $this->lang->line('compare')) : 'Compare' ?>" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $variant_id ?>">
                                                        <i class="uil uil-exchange-alt"></i>
                                                    </a>
                                                </div>

                                                <?php if (isset($row['min_max_price']['special_price']) && $row['min_max_price']['special_price'] != '' && $row['min_max_price']['special_price'] != 0 && $row['min_max_price']['special_price'] < $row['min_max_price']['min_price']) {
                                                ?>
                                                    <span class="avatar sale_tag bg-pink d-flex position-absolute text-uppercase text-white">
                                                        <span class=""><?= !empty($this->lang->line('sale')) ? str_replace('\\', '', $this->lang->line('sale')) : 'Sale'
                                                                        ?></span>
                                                    </span>
                                                <?php } ?>

                                            </figure>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="product-content product_listing_list">
                                        <h4 class="list-product-title title title_wrap" title="<?= $row['name']; ?>">
                                            <a class="text-decoration-none text-dark" href="<?= base_url('products/details/' . $row['slug']) ?>">
                                                <?= str_replace('\r\n', '&#13;&#10;', strip_tags($row['name'])) ?>
                                            </a>
                                        </h4>
                                        <div class="col-md-12 mb-3 product-rating-small ps-0 " dir="ltr">
                                            <input id="input" name="rating" class="rating rating-loading d-none" data-size="xs" value="<?= $row['rating'] ?>" data-show-clear="false" data-show-caption="false" readonly>
                                        </div>

                                        <div class="mt-n2">
                                            <p class="text-muted list-product-desc title_wrap"><?= str_replace('\r\n', '&#13;&#10;', strip_tags($row['short_description'])) ?></p>
                                        </div>
                                        <?php
                                        if (($row['variants'][0]['special_price'] < $row['variants'][0]['price']) && ($row['variants'][0]['special_price'] != 0)) { ?>
                                            <p class="price text-dark">
                                                <span id="price">
                                                    <?php echo $settings['currency'] ?>
                                                    <?php
                                                    $price = $row['variants'][0]['special_price'];
                                                    echo format_price($price);
                                                    ?>
                                                </span>
                                                <sup>
                                                    <span class="special-price striped-price text-danger" id="product-striped-price-div">
                                                        <s id="striped-price">
                                                            <?php echo $settings['currency'] ?>
                                                            <?php $price = $row['variants'][0]['price'];
                                                            echo format_price($price);
                                                            ?>
                                                        </s>
                                                    </span>
                                                </sup>
                                            </p>
                                        <?php } else { ?>
                                            <p class="price text-dark">
                                                <span id="price">
                                                    <?php echo $settings['currency'] ?>
                                                    <?php
                                                    $price = $row['variants'][0]['price'];
                                                    echo format_price($price);
                                                    ?>
                                                </span>
                                            </p>
                                        <?php } ?>

                                        <div class="button button-sm m-0 p-0">
                                            <?php $variant_price = ($row['variants'][0]['special_price'] > 0 && $row['variants'][0]['special_price'] != '') ? $row['variants'][0]['special_price'] : $row['variants'][0]['price'];
                                            $data_min = (isset($row['minimum_order_quantity']) && !empty($row['minimum_order_quantity'])) ? $row['minimum_order_quantity'] : 1;
                                            $data_step = (isset($row['minimum_order_quantity']) && !empty($row['quantity_step_size'])) ? $row['quantity_step_size'] : 1;
                                            $data_max = (isset($row['total_allowed_quantity']) && !empty($row['total_allowed_quantity'])) ? $row['total_allowed_quantity'] : 0;
                                            ?>
                                            <a href="#" class="add_to_cart  btn btn-xs btn-outline-primary rounded-pill" data-product-id="<?= $row['id'] ?>" 
                                            data-product-variant-id="<?= $variant_id ?>" 
                                            data-product-stock="<?= $product_stock ?>"
                                            data-product-title="<?= $row['name'] ?>" data-product-slug="<?= $row['slug'] ?>" data-product-image="<?= $row['image']; ?>" 
                                            data-product-price="<?= $variant_price; ?>" data-min="<?= $data_min; ?>" data-step="<?= $data_step; ?>" 
                                            data-product-description="<?= short_description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', strip_tags($row['short_description'])))); ?>" 
                                            data-izimodal-open="<?= $modal ?>">
                                                <i class="uil uil-shopping-bag"></i>&nbsp;<?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add To Cart' ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                } else { ?>
                    <div class="col-12 m-5">
                        <div class="text-center">
                            <h1 class="h2"><?= !empty($this->lang->line('no_favorite_products_found')) ? str_replace('\\', '', $this->lang->line('no_favorite_products_found')) : 'No Favorite Products Found' ?>.</h1>
                            <a href="<?= base_url('products') ?>" class="button button-rounded button-warning"><?= !empty($this->lang->line('go_to_shop')) ? str_replace('\\', '', $this->lang->line('go_to_shop')) : 'Go to Shop' ?></a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

    </div>
</section>
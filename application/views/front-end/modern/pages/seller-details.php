<!-- breadcrumb -->
<div class="content-wrapper deeplink_wrapper">
    <section class="wrapper bg-soft-grape">
        <div class="container py-3 py-md-5">
            <nav class="d-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                    <?php if (isset($right_breadcrumb) && !empty($right_breadcrumb)) {
                        foreach ($right_breadcrumb as $row) {
                    ?>
                            <li class="breadcrumb-item"><?= $row ?></li>
                    <?php }
                    } ?>
                    <li class="breadcrumb-item active" aria-current="page"><a href="<?= base_url('sellers') ?>"><?= !empty($this->lang->line('seller')) ? str_replace('\\', '', $this->lang->line('seller')) : 'Seller' ?></a></li>
                    <?php
                    if (isset($sellers) && !empty($sellers)) { ?>
                        <li class="breadcrumb-item active text-muted" aria-current="page"><?= $sellers[0]['store_name'] ?></li>
                    <?php } ?>
                </ol>
            </nav>
            <!-- /nav -->
        </div>
        <!-- /.container -->
    </section>
</div>
<!-- end breadcrumb -->


<section class="container listing-page mb-15">
    <div class="product-listing card-solid py-4">
        <div class="mx-0">
            <div class="pt-3 pb-3">

                <?php foreach ($seller_details as $row) { ?>
                    <div class="card p-4">
                        <div class="d-flex gap-1 justify-content-between">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="d-flex">
                                    <i class="fs-60 text-blue uil uil-store"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h1 class="h1"><?= $row['username'] ?></h1>
                                    <ul class="d-flex gap-4 pl-0 flex-wrap">
                                        <li class="d-inline-block">
                                            <i class="text-warning uil uil-star"></i>
                                            <?= $sellers[0]['rating'] ?> <?= !empty($this->lang->line('ratings')) ? str_replace('\\', '', $this->lang->line('ratings')) : 'Ratings' ?>
                                        </li>
                                        <li class="d-inline-block">
                                            <i class="text-success uil uil-check-circle"></i>
                                            <?= $total_orders ?> <?= !empty($this->lang->line('orders')) ? str_replace('\\', '', $this->lang->line('orders')) : 'Orders' ?>
                                        </li>
                                        <li class="d-inline-block">
                                            <i class="text-sky uil uil-cube"></i>
                                            <?= $seller_products_count ?> <?= !empty($this->lang->line('products')) ? str_replace('\\', '', $this->lang->line('products')) : 'Products' ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="">
                                <?php foreach ($sellers as $seller) { ?>
                                    <div class="d-flex justify-content-center seller-profile-card">
                                        <a href="">
                                            <img class="pic-1 lazy" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url(rawurlencode($seller['logo'])) ?>">
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if (isset($sellers[0]['store_description']) && !empty($sellers[0]['store_description'])) { ?>
                    <div class="card p-5 mt-5">
                        <h3>About Seller</h3>
                        <p><?= $sellers[0]['store_description'] ?></p>
                    </div>
                <?php } ?>


                <div class="card mt-5 p-5">

                    <h2 class="display-6 mb-1"><?= !empty($this->lang->line('products')) ? str_replace('\\', '', $this->lang->line('products')) : 'Products' ?></h2>

                    <hr class="my-5">

                    <div class="grid grid-view shop">
                        <div class="row">
                            <?php foreach ($seller_products as $seller_product) {
                                
                                if ($seller_product['type'] == 'simple_product') {
                                    $product_stock = $seller_product['stock'];
                                }else{
                                    $product_stock = $seller_product['total_stock'];
                                }
                            ?>
                                <div class="project item col-6 col-xl-3 default-style my-2" title="<?= $seller_product['name']; ?>">
                                    <figure class="rounded">
                                        <a href="<?= base_url('products/details/' . $seller_product['slug']) ?>">
                                            <img class="pic-1 lazy fig_image image-fit" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('media/image?path=' . rawurlencode($seller_product['relative_path']) . '&width=640&quality=80') ?>">
                                        </a>
                                        <a class="item-like text-decoration-none add-to-fav-btn 
                                                        <?= ($seller_product['is_favorite'] == 1) ? 'fa fa-heart text-danger' : 'fa fa-heart-o' ?>  
                                                        " href="#" data-bs-toggle="white-tooltip" title="<?= !empty($this->lang->line('add_to_wishlist')) ? str_replace('\\', '', $this->lang->line('add_to_wishlist')) : 'Add To wishlist' ?>" data-product-id="<?= $seller_product['id'] ?>">
                                            <i class=""></i>
                                        </a>

                                        <a href="#" class="quick-view-btn item-view text-decoration-none" data-bs-toggle="white-tooltip" title="<?= !empty($this->lang->line('quick_view')) ? str_replace('\\', '', $this->lang->line('quick_view')) : 'Quick View' ?>" data-tip="<?= !empty($this->lang->line('quick_view')) ? str_replace('\\', '', $this->lang->line('quick_view')) : 'Quick View' ?>" data-product-id="<?= $seller_product['id'] ?>" data-product-variant-id="<?= $seller_product['variants'][0]['id'] ?>" data-izimodal-open="#quick-view" data-statistics='<?= $product_statistics ?>'>
                                            <i class="uil uil-eye"></i>
                                        </a>
                                        <?php
                                        if (count($seller_product['variants']) <= 1) {
                                            $variant_id = $seller_product['variants'][0]['id'];
                                            $modal = "";
                                        } else {
                                            $variant_id = "";
                                            $modal = "#quick-view";
                                        }
                                        ?>

                                        <?php
                                        if (count($seller_product['variants']) <= 1) {
                                            $variant_id = $seller_product['variants'][0]['id'];
                                        } else {
                                            $variant_id = "";
                                        }
                                        ?>
                                        <a href="#" class="compare item-compare text-decoration-none" data-bs-toggle="white-tooltip" title="<?= !empty($this->lang->line('compare')) ? str_replace('\\', '', $this->lang->line('compare')) : 'Compare' ?>" data-tip="<?= !empty($this->lang->line('compare')) ? str_replace('\\', '', $this->lang->line('compare')) : 'Compare' ?>" data-product-id="<?= $seller_product['id'] ?>" data-product-variant-id="<?= $variant_id ?>">
                                            <i class="uil uil-exchange-alt"></i>
                                        </a>

                                        <?php if (isset($seller_product['min_max_price']['special_price']) && $seller_product['min_max_price']['special_price'] != '' && $seller_product['min_max_price']['special_price'] != 0 && $seller_product['min_max_price']['special_price'] < $seller_product['min_max_price']['min_price']) { ?>
                                            <span class="avatar bg-pink d-flex position-absolute text-uppercase text-white sale_tag">
                                                <span class=""><?= !empty($this->lang->line('sale')) ? str_replace('\\', '', $this->lang->line('sale')) : 'Sale' ?></span>
                                            </span>
                                        <?php } ?>

                                        <?php
                                        $variant_price = ($seller_product['variants'][0]['special_price'] > 0 && $seller_product['variants'][0]['special_price'] != '') ? $seller_product['variants'][0]['special_price'] : $seller_product['variants'][0]['price'];
                                        
                                        $data_min = (isset($seller_product['minimum_order_quantity']) && !empty($seller_product['minimum_order_quantity'])) ? $seller_product['minimum_order_quantity'] : 1;
                                        $data_step = (isset($seller_product['minimum_order_quantity']) && !empty($seller_product['quantity_step_size'])) ? $seller_product['quantity_step_size'] : 1;
                                        $data_max = (isset($seller_product['total_allowed_quantity']) && !empty($seller_product['total_allowed_quantity'])) ? $seller_product['total_allowed_quantity'] : 0;
                                        ?>
                                        <a href="#" class="add_to_cart item-cart text-decoration-none" data-product-id="<?= $seller_product['id'] ?>"
                                        data-product-variant-id="<?= $variant_id ?>" data-product-stock="<?= $product_stock ?>" data-product-title="<?= $seller_product['name'] ?>" data-product-image="<?= $seller_product['image']; ?>" data-product-price="<?= $variant_price; ?>" data-min="<?= $data_min; ?>" data-step="<?= $data_step; ?>" data-product-description="<?= short_description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', strip_tags($seller_product['short_description'])))); ?>" data-izimodal-open="<?= $modal ?>">
                                            <i class="uil uil-shopping-cart-alt"></i>&nbsp;<?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add To Cart' ?>
                                        </a>

                                    </figure>
                                    <div class="my-2 post-header">
                                        <div class="align-items-center justify-content-between">
                                            <input id="input" name="rating" class="rating rating-loading d-none" data-size="xs" value="<?= $seller_product['rating'] ?>" data-show-clear="false" data-show-caption="false" readonly>
                                        </div>
                                        <h4 class="post-title title_wrap" title="<?= $seller_product['name']; ?>">
                                            <a href="<?= base_url('products/details/' . $seller_product['slug']) ?>" class="link-dark text-decoration-none"><?= str_replace('\r\n', '&#13;&#10;', strip_tags($seller_product['name'])) ?></a>
                                        </h4>
                                        <?php if (($seller_product['variants'][0]['special_price'] < $seller_product['variants'][0]['price']) && ($seller_product['variants'][0]['special_price'] != 0)) { ?>
                                            <p class="price text-muted">
                                                <span id="price">
                                                    <?php echo $settings['currency'] ?>
                                                    <?php
                                                    $price = $seller_product['variants'][0]['special_price'];
                                                    echo format_price($price);
                                                    ?>
                                                </span>
                                                <sup>
                                                    <span class="special-price striped-price text-danger" id="product-striped-price-div">
                                                        <s id="striped-price">
                                                            <?php echo $settings['currency'] ?>
                                                            <?php $price = $seller_product['variants'][0]['price'];
                                                            echo format_price($price);
                                                            ?>
                                                        </s>
                                                    </span>
                                                </sup>
                                            </p>
                                        <?php } else { ?>
                                            <p class="price text-muted">
                                                <span id="price">
                                                    <?php echo $settings['currency'] ?>
                                                    <?php
                                                    $price = $seller_product['variants'][0]['price'];
                                                    echo format_price($price);
                                                    ?>
                                                </span>
                                            </p>
                                        <?php } ?>
                                    </div>
                                    <!-- /.post-header -->
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <nav class="text-center page-link" aria-label="pagination">
                        <?= (isset($links)) ? $links : '' ?>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
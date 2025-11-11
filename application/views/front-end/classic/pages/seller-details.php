<!-- breadcrumb -->
<section class="breadcrumb-title-bar colored-breadcrumb deeplink_wrapper">
    <div class="main-content responsive-breadcrumb">
        <h2><?= isset($page_main_bread_crumb) ? $page_main_bread_crumb : 'Products' ?><?= (isset($seller) && !empty($seller[0]['store_name'])) ? " By " . $seller[0]['store_name'] : '' ?></h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                <?php if (isset($right_breadcrumb) && !empty($right_breadcrumb)) {
                    foreach ($right_breadcrumb as $row) {
                ?>
                        <li class="breadcrumb-item"><?= $row ?></li>
                <?php }
                } ?>
                <li class="breadcrumb-item active" aria-current="page"><?= !empty($this->lang->line('seller')) ? str_replace('\\', '', $this->lang->line('seller')) : 'Seller' ?></li>
                <?php
                if (isset($sellers) && !empty($sellers)) { ?>
                    <li class="breadcrumb-item active text-muted" aria-current="page"><?= $sellers[0]['store_name'] ?></li>
                <?php } ?>
            </ol>
        </nav>
    </div>
</section>
<!-- breadcrumb -->


<section class="listing-page content main-content">
    <div class="product-listing card-solid py-4">
        <div class="mx-0">
            <div class="pt-3 pb-3">
                <?php foreach ($seller_details as $row) { ?>
                    <div class="seller_card p-4">
                        <div class="d-flex gap-1 justify-content-between">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="d-flex">
                                    <i class="fa fa-2x fa-store text-primary"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h1 class=""><?= $row['username'] ?></h1>
                                    <ul class="d-flex gap-4 pl-0 flex-wrap">
                                        <li class="d-inline-block">
                                            <i class="fa fa-star text-warning"></i>
                                            <?= $sellers[0]['rating'] ?> <?= !empty($this->lang->line('ratings')) ? str_replace('\\', '', $this->lang->line('ratings')) : 'Ratings' ?>
                                        </li>
                                        <li class="d-inline-block">
                                            <i class="fa fa-check-circle text-success"></i>
                                            <?= $total_orders ?> <?= !empty($this->lang->line('orders')) ? str_replace('\\', '', $this->lang->line('orders')) : 'Orders' ?>
                                        </li>
                                        <li class="d-inline-block">
                                            <i class="fa fa-cubes text-info"></i>
                                            <?= $seller_products_count ?> <?= !empty($this->lang->line('products')) ? str_replace('\\', '', $this->lang->line('products')) : 'Products' ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <?php foreach ($sellers as $seller) { ?>
                                    <div class="d-flex justify-content-center seller-profile-card">
                                        <div class="d-flex">
                                            <a href="">
                                                <img class="pic-1 lazy" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url(rawurlencode($seller['logo'])) ?>">
                                            </a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="seller_card p-5 mt-5">
                    <h3>About Seller</h3>
                    <p><?= $sellers[0]['store_description'] ?></p>
                </div>


                <div class="seller_card mt-5 p-5">

                    <h2 class="display-6 mb-1"><?= !empty($this->lang->line('products')) ? str_replace('\\', '', $this->lang->line('products')) : 'Products' ?></h2>

                    <hr class="my-5">

                    <div class="row">
                        <?php foreach ($seller_products as $row) {
                            if ($row['type'] == 'simple_product') {
                                $product_stock = $row['stock'];
                            }else{
                                $product_stock = $row['total_stock'];
                            }
                        ?>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="product-grid">
                                    <aside class="add-favorite">
                                        <button type="button" class="btn far fa-heart add-to-fav-btn <?= ($row['is_favorite'] == 1) ? 'fa text-danger' : '' ?>" data-product-id="<?= $row['id'] ?>"></button>
                                    </aside>
                                    <div class="product-image">
                                        <div class="product-image-container">
                                            <a href="<?= base_url('products/details/' . $row['slug']) ?>">
                                                <img class="pic-1 lazy" data-src="<?= base_url('media/image?path='.rawurlencode($row['relative_path']).'&width=320&quality=80') ?>">
                                            </a>
                                        </div>
                                        <ul class="social">
                                            <?php
                                            if (count($row['variants']) <= 1) {
                                                $variant_id = $row['variants'][0]['id'];
                                                $modal = "";
                                            } else {
                                                $variant_id = "";
                                                $modal = "#quick-view";
                                            }
                                            ?>
                                            <?php $variant_price = ($row['variants'][0]['special_price'] > 0 && $row['variants'][0]['special_price'] != '') ? $row['variants'][0]['special_price'] : $row['variants'][0]['price'];
                                            $data_min = (isset($row['minimum_order_quantity']) && !empty($row['minimum_order_quantity'])) ? $row['minimum_order_quantity'] : 1;
                                            $data_step = (isset($row['minimum_order_quantity']) && !empty($row['quantity_step_size'])) ? $row['quantity_step_size'] : 1;
                                            $data_max = (isset($row['total_allowed_quantity']) && !empty($row['total_allowed_quantity'])) ? $row['total_allowed_quantity'] : 0;
                                            ?>
                                            <li><a href="" class="quick-view-btn" data-tip="<?= !empty($this->lang->line('quick_view')) ? str_replace('\\', '', $this->lang->line('quick_view')) : 'Quick View' ?>" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $row['variants'][0]['id'] ?>" data-izimodal-open="#quick-view"><i class="fa fa-search"></i></a></li>
                                            <li>
                                                <?php if ($row['variants'][0]['cart_count'] != 0) { ?>
                                                    <a href="<?= base_url('cart') ?>" data-tip="<?= !empty($this->lang->line('go_to_cart')) ? str_replace('\\', '', $this->lang->line('go_to_cart')) : 'Go To Cart' ?>">
                                                        <i class='fa fa-arrow-right'></i>
                                                    </a>
                                                <?php } else { ?>
                                                    <a href="" data-tip="<?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add To Cart' ?>" class="add_to_cart" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $variant_id ?>" data-product-stock= "<?= $product_stock ?>" data-product-title="<?= $row['name'] ?>" data-product-image="<?= $row['image'] ?>" data-product-price="<?= $variant_price; ?>" data-min="<?= $data_min; ?>" data-step="<?= $data_step; ?>" data-product-description="<?= short_description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', strip_tags($row['short_description'])))); ?>" data-izimodal-open="<?= $modal ?>">
                                                        <i class="fa fa-shopping-cart"></i>
                                                    </a>
                                                <?php } ?>
                                            </li>
                                            <li>
                                                <?php $variant_id = (count($row['variants']) <= 1) ? $row['variants'][0]['id'] : ""; ?>

                                                <a href="#" class="compare" data-tip="<?= !empty($this->lang->line('compare')) ? str_replace('\\', '', $this->lang->line('compare')) : 'Compare' ?>" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $variant_id ?>">
                                                    <i class="fa fa-random"></i>
                                                </a>
                                            </li>
                                        </ul>
                                        <?php if (isset($row['min_max_price']['special_price']) && $row['min_max_price']['special_price'] != '' && $row['min_max_price']['special_price'] != 0 && $row['min_max_price']['special_price'] < $row['min_max_price']['min_price']) { ?>
                                            <span class="product-new-label"><?= !empty($this->lang->line('sale')) ? str_replace('\\', '', $this->lang->line('sale')) : 'Sale' ?></span>
                                            <span class="product-discount-label"><?= $row['min_max_price']['discount_in_percentage'] ?>%</span>
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-12 mb-3 product-rating-small" dir="ltr">
                                        <input type="text" class="kv-fa rating-loading" value="<?= $row['rating'] ?>" data-size="sm" title="" readonly>
                                    </div>
                                    <div class="product-content">
                                        <h2 class="title title_wrap"><a href="<?= base_url('products/details/' . $row['slug']) ?>"><?= str_replace('\r\n', '&#13;&#10;', strip_tags($row['name'])) ?></a></h2>
                                        <div class="">
                                            <?php if (($row['variants'][0]['special_price'] < $row['variants'][0]['price']) && ($row['variants'][0]['special_price'] != 0)) { ?>
                                                <p class="mb-2 mt-2">
                                                    <span id="price" style='font-size: 20px;'>
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
                                                <p class="mb-2 mt-2">
                                                    <span id="price" style='font-size: 20px;'>
                                                        <?php echo $settings['currency'] ?>
                                                        <?php
                                                        $price = $row['variants'][0]['price'];
                                                        echo format_price($price);
                                                        ?>
                                                    </span>
                                                </p>
                                            <?php } ?>
                                        </div>
                                        <?php $variant_price = ($row['variants'][0]['special_price'] > 0 && $row['variants'][0]['special_price'] != '') ? $row['variants'][0]['special_price'] : $row['variants'][0]['price'];
                                        $data_min = (isset($row['minimum_order_quantity']) && !empty($row['minimum_order_quantity'])) ? $row['minimum_order_quantity'] : 1;
                                        $data_step = (isset($row['minimum_order_quantity']) && !empty($row['quantity_step_size'])) ? $row['quantity_step_size'] : 1;
                                        $data_max = (isset($row['total_allowed_quantity']) && !empty($row['total_allowed_quantity'])) ? $row['total_allowed_quantity'] : 0;
                                        ?>
                                        <?php if ($row['variants'][0]['cart_count'] != 0) { ?>
                                            <a class="add-to-cart" href="<?= base_url('cart') ?>"><i class='fas fa-arrow-right'></i> <?= !empty($this->lang->line('go_to_cart')) ? str_replace('\\', '', $this->lang->line('go_to_cart')) : 'Go To Cart' ?></a>
                                        <?php } else { ?>
                                            <a class="add-to-cart add_to_cart" href="" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $variant_id ?>" data-product-stock= "<?= $product_stock ?>" data-product-title="<?= $row['name'] ?>" data-product-image="<?= $row['image'] ?>" data-product-price="<?= $variant_price; ?>" data-min="<?= $data_min; ?>" data-step="<?= $data_step; ?>" data-product-description="<?= short_description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', strip_tags($row['short_description'])))); ?>" data-izimodal-open="<?= $modal ?>">+ <?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add To Cart' ?></a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <nav class="text-center page-link" >
                        <?= (isset($links)) ? $links : '' ?>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
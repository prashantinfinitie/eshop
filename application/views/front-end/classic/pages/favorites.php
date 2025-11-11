<!-- breadcrumb -->
<section class="breadcrumb-title-bar colored-breadcrumb deeplink_wrapper">
    <div class="main-content responsive-breadcrumb">
        <h2><?= !empty($this->lang->line('favorite')) ? str_replace('\\', '', $this->lang->line('favorite')) : 'Favorites' ?></h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?>
                <li class="breadcrumb-item"><?= !empty($this->lang->line('favorite')) ? str_replace('\\', '', $this->lang->line('favorite')) : 'favorites' ?></li>
            </ol>
        </nav>
    </div>

</section>
<!-- end breadcrumb -->
<section class="my-account-section">
    <div class="main-content">
        <div class="col-md-12 mt-5 mb-3">
            <div class="user-detail align-items-center">
                <div class="ml-3">
                    <h6 class="text-muted mb-0"><?= !empty($this->lang->line('hello')) ? str_replace('\\', '', $this->lang->line('hello')) : 'Hello' ?></h6>
                    <h5 class="mb-0"><?= $user->username ?></h5>
                </div>
            </div>
        </div>
        <div class="row m5">
            <div class="col-md-4">
                <?php $this->load->view('front-end/' . THEME . '/pages/my-account-sidebar') ?>
            </div>

            <div class="col-md-8 col-12">
                <div class=' border-0'>
                    <div class="card-header bg-white">
                        <h1 class="h4"><?= !empty($this->lang->line('favorite')) ? str_replace('\\', '', $this->lang->line('favorite')) : 'favorites' ?></h1>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            if (isset($products) && !empty($products)) {
                                foreach ($products as $row) {
                                    if ($row['type'] == 'simple_product') {
                                        $product_stock = $row['stock'];
                                    }else{
                                        $product_stock = $row['total_stock'];
                                    }
                            ?>
                                    <div class="col-lg-4 col-sm-6 mt-5">
                                        <div class="product-grid">
                                            <aside class="add-favorite">
                                                <button type="button" class="btn fa-heart add-to-fav-btn fa text-danger" data-product-id="<?= $row['id'] ?>"></button>
                                            </aside>
                                            <div class="product-image">
                                                <div class="product-image-container">
                                                    <a href="#">
                                                        <img class="pic-1" src="<?= base_url('media/image?path=' . rawurlencode($row['relative_path']) . '&width=300&quality=80') ?>">
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
                                                    <li><a href="" class="quick-view-btn" data-tip="<?= !empty($this->lang->line('quick_view')) ? str_replace('\\', '', $this->lang->line('quick_view')) : 'Quick View' ?>" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $row['variants'][0]['id'] ?>" data-izimodal-open="#quick-view"><i class="fa fa-search"></i></a></li>
                                                    <li><a href="" data-tip="<?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add To Cart' ?>" class="add_to_cart" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $variant_id ?>" data-product-stock= "<?= $product_stock ?>" data-izimodal-open="<?= $modal ?>"><i class="fa fa-shopping-cart"></i></a></li>
                                                    <li>
                                                        <?php $variant_id = (count((array)$product_row['variants']) <= 1) ? $product_row['variants'][0]['id'] : ""; ?>
                                                        <a href="#" class="compare" data-tip="<?= !empty($this->lang->line('compare')) ? str_replace('\\', '', $this->lang->line('compare')) : 'Compare' ?>" data-product-id="<?= $product_row['id'] ?>" data-product-variant-id="<?= $variant_id ?>">
                                                            <i class="fa fa-random"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="rating">
                                                <input type="text" class="kv-fa rating-loading" value="<?= $row['rating'] ?>" data-size="sm" title="" readonly>
                                            </div>
                                            <div class="product-content">
                                                <h3 class="title title_wrap"><a href="<?= base_url('products/details/' . $row['slug']) ?>"><?= str_replace('\r\n', '&#13;&#10;', strip_tags($row['name'])) ?></a></h3>
                                                
                                                    <?php if ($row['type'] == "simple_product") { ?>
                                                        <p class="mb-0 mt-2 price" id="price">
                                                            <?php
                                                            $price = $row['variants'][0]['price'];
                                                            echo format_price($price);
                                                            ?>
                                                            <sup>
                                                                <span class="special-price striped-price">
                                                                    <s>
                                                                        <?= !empty($row['min_max_price']['special_price']) && $row['min_max_price']['special_price'] != NULL  ?   
                                                                        $settings['currency'] . '</i>' . format_price($row['min_max_price']['min_price']) : '' ?>
                                                                        </s>
                                                                    </span>
                                                                </sup>
                                                        </p>
                                                        
                                                    <?php } else { ?>
                                                        <?php if (($row['variants'][0]['special_price'] < $row['variants'][0]['price']) && ($row['variants'][0]['special_price'] != 0)) { ?>
                                                            <p class="mb-0 mt-2">
                                                                <span id="" style='font-size: 20px;'>
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
                                                            <p class="mb-0 mt-2 price">
                                                                <span id="price" style='font-size: 20px;'>
                                                                    <?php echo $settings['currency'] ?>
                                                                    <?php
                                                                    $price = $row['variants'][0]['price'];
                                                                    echo format_price($price);
                                                                    ?>
                                                                </span>
                                                            </p>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <a class="add-to-cart add_to_cart" href="" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $variant_id ?>" data-product-stock= "<?= $product_stock ?>" data-izimodal-open="<?= $modal ?>">+ <?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add To Cart' ?></a>
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
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
    <!--end container-->
</section>
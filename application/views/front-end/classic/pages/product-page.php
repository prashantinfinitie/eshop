<?php $total_images = 0; ?>
<!-- breadcrumb -->
<section class="breadcrumb-title-bar colored-breadcrumb deeplink_wrapper">
    <div class="main-content responsive-breadcrumb">
        <h2><?= $product['product'][0]['name'] ?></h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('products') ?>"><?= !empty($this->lang->line('product')) ? str_replace('\\', '', $this->lang->line('product')) : 'Products' ?></a></li>
                <?php
                $cat_names = array();
                $cat_slugs = array();
                $new_array = array();
                $result = check_for_parent_id($product['product'][0]['category_id']);
                array_push($cat_names, $result[0]['name']);
                array_push($cat_slugs, $result[0]['slug']);
                while (!empty($result[0]['parent_id'])) {
                    $result = check_for_parent_id($result[0]['parent_id']);
                    array_push($cat_names, $result[0]['name']);
                    array_push($cat_slugs, $result[0]['slug']);
                }
                $cat_names = array_reverse($cat_names, true);
                $cat_slugs = array_reverse($cat_slugs, true);

                foreach ($cat_names as $key => $name) {
                    $new_array[] = array(
                        'name' => $name,
                        'slug' => $cat_slugs[$key]
                    );
                }

                foreach ($new_array as $row) {
                ?>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="<?= base_url('products/category/' . $row['slug']) ?>">
                            <?= strip_tags(output_escaping(str_replace('\r\n', '&#13;&#10;', $row['name']))) ?>
                        </a>

                    </li>
                <?php } ?>
            </ol>
        </nav>
    </div>

</section>

<!-- end breadcrumb -->
<?php $seller_slug = fetch_details("seller_data", ['user_id' => $product['product'][0]['seller_id']], "slug"); ?>
<section class="content main-content product-page-content my-2 py-3">
    <div class="card card-solid">
        <div class="row mx-0">
            <div class="col-12 col-md-6 product-preview-image-section-md">
                <div class="swiper-container product-gallery-top gallery-top-1">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class='product-view-grid'>
                                <div class='product-view-image'>
                                    <div class='product-view-image-container'>
                                        <img src="<?= $product['product'][0]['image'] ?>" id="img_01" data-zoom-image="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        $variant_relative_path = array_column($product['product'][0]['variants'], 'variant_relative_path');

                        if (!empty($variant_relative_path)) {
                            foreach ($variant_relative_path as $variant_images) {
                                if (!empty($variant_images)) {
                                    foreach ($variant_images as $image) {
                        ?>
                                        <div class="swiper-slide">
                                            <div class='product-view-grid'>
                                                <div class='product-view-image'>
                                                    <div class='product-view-image-container'>
                                                        <img src="<?= base_url('media/image?path=' . rawurlencode($image) . '&width=75&quality=80') ?>" data-zoom-image="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                        <?php }
                                }
                            }
                        } ?>
                        <?php
                        if (!empty($product['product'][0]['other_images_relative_path']) && isset($product['product'][0]['other_images_relative_path'])) {
                            foreach ($product['product'][0]['other_images_relative_path'] as $other_image) {
                                $total_images++;
                        ?>
                                <div class="swiper-slide">
                                    <div class='product-view-grid'>
                                        <div class='product-view-image'>
                                            <div class='product-view-image-container'>
                                                <img src="<?= base_url('media/image?path=' . rawurlencode($other_image) . '&width=620&quality=80') ?>" id="img_01" data-zoom-image="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php }
                        } ?>
                        <?php
                        if (isset($product['product'][0]['video_type']) && !empty($product['product'][0]['video_type'])) {
                            $total_images++;
                        ?>
                            <div class="swiper-slide">
                                <div class='product-view-grid'>
                                    <div class='product-view-image'>
                                        <div class='product-view-image-container'>
                                            <?php if ($product['product'][0]['video_type'] == 'self_hosted') { ?>
                                                <video controls width="320" height="240" src="<?= $product['product'][0]['video'] ?>">
                                                    <?= !empty($this->lang->line('no_video_tag_support')) ? str_replace('\\', '', $this->lang->line('no_video_tag_support')) : 'Your browser does not support the video tag.' ?>
                                                </video>
                                            <?php } else if ($product['product'][0]['video_type'] == 'youtube' || $product['product'][0]['video_type'] == 'vimeo') {
                                                if ($product['product'][0]['video_type'] == 'vimeo') {
                                                    $url =  explode("/", $product['product'][0]['video']);
                                                    $id = end($url);
                                                    $url = 'https://player.vimeo.com/video/' . $id;
                                                } else if ($product['product'][0]['video_type'] == 'youtube') {
                                                    if (strpos($product['product'][0]['video'], 'watch?v=') !== false) {
                                                        $url = str_replace("watch?v=", "embed/", $product['product'][0]['video']);
                                                    } else if (strpos($product['product'][0]['video'], "youtu.be/") !== false) {
                                                        $url = explode("/", $product['product'][0]['video']);
                                                        $url = "https://www.youtube.com/embed/" . end($url);
                                                    } else if (strpos($product['product'][0]['video'], "shorts/") !== false) {
                                                        $url = str_replace("shorts/", "embed/", $product['product'][0]['video']);
                                                    } else {
                                                        $url = $product['product'][0]['video'];
                                                    }
                                                } else {
                                                    $url = $product['product'][0]['video'];
                                                } ?>
                                                <iframe width="560" height="315" src="<?= $url ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <!-- Add Arrows -->
                    <div class="swiper-button-next swiper-button-black"></div>
                    <div class="swiper-button-prev swiper-button-black"></div>
                </div>
                <div class="swiper-container product-gallery-thumbs gallery-thumbs-1">
                    <div class="swiper-wrapper" id="gal1">
                        <div class="swiper-slide ml-0">
                            <div class='product-view-grid'>
                                <div class='product-view-image'>
                                    <div class='product-view-image-container'>
                                        <img src="<?= base_url('media/image?path=' . rawurlencode($product['product'][0]['relative_path']) . '&width=175&quality=80') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php

                        if (!empty($variant_relative_path)) {
                            foreach ($variant_relative_path as $variant_images) {
                                if (!empty($variant_images)) {
                                    foreach ($variant_images as $image) {
                        ?>
                                        <div class="swiper-slide">
                                            <div class='product-view-grid'>
                                                <div class='product-view-image'>
                                                    <div class='product-view-image-container'>
                                                        <img src="<?= base_url('media/image?path=' . rawurlencode($image) . '&width=175&quality=80') ?>" data-zoom-image="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                        <?php }
                                }
                            }
                        } ?>
                        <?php
                        if (!empty($product['product'][0]['other_images_relative_path']) && isset($product['product'][0]['other_images_relative_path'])) {
                            foreach ($product['product'][0]['other_images_relative_path'] as $other_image) { ?>
                                <div class="swiper-slide ml-0">
                                    <div class='product-view-grid'>
                                        <div class='product-view-image'>
                                            <div class='product-view-image-container'>
                                                <img src="<?= base_url('media/image?path=' . rawurlencode($other_image) . '&width=172&quality=80') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php }
                        } ?>
                        <?php
                        if (isset($product['product'][0]['video_type']) && !empty($product['product'][0]['video_type'])) {
                            $total_images++;
                        ?>
                            <div class="swiper-slide">
                                <div class='product-view-grid'>
                                    <div class='product-view-image'>
                                        <div class='product-view-image-container'>
                                            <img src="<?= base_url('assets/admin/images/video-file.png') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Mobile Product Image Slider -->
            <div class="col-12 col-md-6 product-preview-image-section-sm">
                <div class=" swiper-container preview-image-swiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide text-center"><img src="<?= base_url('media/image?path=' . rawurlencode($product['product'][0]['relative_path']) . '&width=300&quality=80') ?>"></div>
                        <?php
                        if (!empty($product['product'][0]['other_images_relative_path']) && isset($product['product'][0]['other_images_relative_path'])) {
                            foreach ($product['product'][0]['other_images_relative_path'] as $other_image) { ?>
                                <div class="swiper-slide text-center">
                                    <img src="<?= base_url('media/image?path=' . rawurlencode($other_image) . '&width=300&quality=80') ?>">
                                </div>
                        <?php }
                        } ?>
                        <?php if (!empty($variant_relative_path)) {
                            foreach ($variant_relative_path as $variant_images) {
                                if (!empty($variant_images)) {
                                    foreach ($variant_images as $image) {
                        ?>
                                        <div class="swiper-slide text-center">
                                            <img src="<?= base_url('media/image?path=' . rawurlencode($image) . '&width=300&quality=80') ?>" data-zoom-image="">
                                        </div>

                        <?php }
                                }
                            }
                        } ?>
                        <?php
                        if (isset($product['product'][0]['video_type']) && !empty($product['product'][0]['video_type'])) {
                            $total_images++;
                        ?>
                            <div class="swiper-slide">
                                <div class='product-view-grid'>
                                    <div class='product-view-image'>
                                        <div class='product-view-image-container'>
                                            <?php if ($product['product'][0]['video_type'] == 'self_hosted') { ?>
                                                <video controls width="320" height="240" src="<?= $product['product'][0]['video'] ?>">
                                                    <?= !empty($this->lang->line('no_video_tag_support')) ? str_replace('\\', '', $this->lang->line('no_video_tag_support')) : 'Your browser does not support the video tag.' ?>
                                                </video>
                                            <?php } else if ($product['product'][0]['video_type'] == 'youtube' || $product['product'][0]['video_type'] == 'vimeo') {
                                                if ($product['product'][0]['video_type'] == 'vimeo') {
                                                    $url =  explode("/", $product['product'][0]['video']);
                                                    $id = end($url);
                                                    $url = 'https://player.vimeo.com/video/' . $id;
                                                } else if ($product['product'][0]['video_type'] == 'youtube') {
                                                    if (strpos($product['product'][0]['video'], 'watch?v=') !== false) {
                                                        $url = str_replace("watch?v=", "embed/", $product['product'][0]['video']);
                                                    } else if (strpos($product['product'][0]['video'], "youtu.be/") !== false) {
                                                        $url = explode("/", $product['product'][0]['video']);
                                                        $url = "https://www.youtube.com/embed/" . end($url);
                                                    } else if (strpos($product['product'][0]['video'], "shorts/") !== false) {
                                                        $url = str_replace("shorts/", "embed/", $product['product'][0]['video']);
                                                    } else {
                                                        $url = $product['product'][0]['video'];
                                                    }
                                                } else {
                                                    $url = $product['product'][0]['video'];
                                                } ?>
                                                <iframe width="560" height="315" src="<?= $url ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="swiper-pagination preview-image-swiper-pagination text-center"></div>
                </div>
            </div>
            <div class="col-12 col-md-6 product-page-details">
                <h2 class="my-3 product-title">
                    <?= ucfirst($product['product'][0]['name']) ?>
                </h2>
                <p><?= output_escaping(str_replace('\r\n', '&#13;&#10;', $product['product'][0]['short_description'])) ?></p>
                <div class="slider text-primary">
                    <?php if ($statistics['total_ordered'] > 0) { ?>
                        <span>
                            🛒<?php print_r($statistics['total_ordered']) ?> <?= !empty($this->lang->line('item(s)_sold_in_last_30_days')) ? str_replace('\\', '', $this->lang->line('item(s)_sold_in_last_30_days')) : 'item(s) sold in last 30 days' ?>
                        </span>
                    <?php } ?>
                    <?php if ($statistics['total_in_cart'] > 0) { ?>
                        <span>
                            🚀<?php print_r($statistics['total_in_cart']) ?> <?= !empty($this->lang->line('people_have_added_this_to_cart')) ? str_replace('\\', '', $this->lang->line('people_have_added_this_to_cart')) : 'people have added this to cart' ?>
                        </span>
                    <?php } ?>
                    <?php if ($statistics['total_favorites'] > 0) { ?>
                        <span>
                            ❤️<?php print_r($statistics['total_favorites']) ?> <?= !empty($this->lang->line('people_have_added_to_wishlist')) ? str_replace('\\', '', $this->lang->line('people_have_added_to_wishlist')) : 'people have added to wishlist' ?>
                        </span>
                    <?php } ?>

                </div>
                <?php
                $indicator = (isset($product['product'][0]['indicator']) && !empty($product['product'][0]['indicator']) ? $product['product'][0]['indicator'] : '');
                if ($indicator == '1') { ?>
                    <span class="badge badge-success"><?= !empty($this->lang->line('veg')) ? str_replace('\\', '', $this->lang->line('veg')) : 'Veg' ?></span>
                <?php } elseif ($indicator == '2') { ?>
                    <span class="badge badge-danger"><?= !empty($this->lang->line('non_veg')) ? str_replace('\\', '', $this->lang->line('non_veg')) : 'Non Veg' ?></span>
                <?php } ?>

                <hr>
                <div class="col-md-12 mb-3 product-rating-small  pl-0">
                    <input type="text" class="kv-fa rating-loading" value="<?= $product['product'][0]['rating'] ?>" data-size="sm" title="" readonly>
                    <span class="my-auto ml-0"> ( <?= ($product['product'][0]['no_of_ratings'] > 0) ? $product['product'][0]['no_of_ratings'] : "No" ?> <?= !empty($this->lang->line('reviews')) ? str_replace('\\', '', $this->lang->line('reviews')) : 'reviews' ?> ) </span>
                </div>
                <?php if ($product['product'][0]['type'] == "simple_product") { ?>
                    <p class="mb-0 mt-2 price" id="price">
                        <?php echo $settings['currency'] ?>
                        <?php
                        $price = $product['product'][0]['variants'][0]['special_price'];
                        echo format_price($price);
                        ?>
                        <sup>
                            <span class="special-price striped-price">
                                <s>
                                    <?= ($product['product'][0]['variants'][0]['special_price'] < $product['product'][0]['variants'][0]['price']) && ($product['product'][0]['variants'][0]['special_price'] != 0)  ?   $settings['currency'] . '</i>' . format_price($product['product'][0]['variants'][0]['price']) : '' ?>
                                </s>
                            </span>
                        </sup>
                    </p>
                    <p class="mb-0 mt-2 price d-none" id="price">
                        <?php
                        $price = $product['product'][0]['variants'][0]['price'];
                        echo format_price($price);
                        ?>
                    </p>
                <?php } else { ?>
                    <?php if (($product['product'][0]['variants'][0]['special_price'] < $product['product'][0]['variants'][0]['price']) && ($product['product'][0]['variants'][0]['special_price'] != 0)) { ?>
                        <p class="mb-0 mt-2 price">
                            <span id="price" style='font-size: 20px;'>
                                <?php echo $settings['currency'] ?>
                                <?php
                                $price = $product['product'][0]['variants'][0]['special_price'];
                                echo format_price($price);
                                ?>
                            </span>
                            <sup>
                                <span class="special-price striped-price text-danger" id="product-striped-price-div">
                                    <s id="striped-price">
                                        <?php echo $settings['currency'] ?>
                                        <?php $price = $product['product'][0]['variants'][0]['special_price'];
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
                                $price = $product['product'][0]['variants'][0]['price'];
                                echo format_price($price);
                                ?>
                            </span>
                        </p>
                    <?php } ?>
                <?php }
                $color_code = $style = "";
                $product['product'][0]['variant_attributes'] = array_values($product['product'][0]['variant_attributes']);
                $attribute_order = explode(', ', (string)$product['product'][0]['attribute_order']);

                if (isset($product['product'][0]['variant_attributes']) && !empty($product['product'][0]['variant_attributes'])) { ?>
                    <?php
                    if (!empty($attribute_order[0])) {
                        for ($j = 0; $j < count($attribute_order); $j++) {
                            foreach ($product['product'][0]['variant_attributes'] as $attribute) {
                                if ($attribute_order[$j] == $attribute['attr_name']) {
                                    $attribute_values = explode(',', $attribute['values']);
                                    $attribute_ids = explode(',', $attribute['ids']);
                                    $swatche_types = explode(',', $attribute['swatche_type']);
                                    $swatche_values = explode(',', $attribute['swatche_value']);
                                    for ($i = 0; $i < count($swatche_types); $i++) {
                                        if (!empty($swatche_types[$i]) && $swatche_values[$i] != "") {
                                            $style = '<style> .product-page-details .btn-group>.active { background-color: #ffffff; color: #000000; border: 1px solid black;}</style>';
                                        } else if ($swatche_types[$i] == 0 && $swatche_values[$i] == null) {
                                            $style1 = '<style> .product-page-details .btn-group>.active { background-color: var(--primary-color);color: white!important;}</style>';
                                        }
                                    } ?>
                                    <h4 class="mt-2"><?= $attribute['attr_name'] ?></h4>
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons" id="<?= $attribute['attr_name'] ?>">
                                        <?php foreach ($attribute_values as $key => $row) {
                                            if ($swatche_types[$key] == "1") {
                                                echo '<style> .product-page-details .btn-group>.active { border: 1px solid black;}</style>';
                                                $color_code = "style='background-color:" . $swatche_values[$key] . ";'";  ?>
                                                <label class="btn text-center fullCircle " <?= $color_code ?>>
                                                    <input type="radio" name="<?= $attribute['attr_name'] ?>" value="<?= $attribute_ids[$key] ?>" autocomplete="off" class="attributes">
                                                </label>
                                            <?php } else if ($swatche_types[$key] == "2") { ?>
                                                <?= $style ?>
                                                <label class="btn text-center ">
                                                    <img class="swatche-image" src="<?= $swatche_values[$key] ?>">
                                                    <input type="radio" name="<?= $attribute['attr_name'] ?>" value="<?= $attribute_ids[$key] ?>" autocomplete="off" class="attributes">
                                                    <br>
                                                </label>
                                            <?php } else { ?>
                                                <?= '<style> .product-page-details .btn-group>.active { background-color: var(--primary-color);color: white!important;}</style>'; ?>
                                                <label class="btn btn-default text-center">
                                                    <?= $row ?>
                                                    <input type="radio" name="<?= $attribute['attr_name'] ?>" value="<?= $attribute_ids[$key] ?>" autocomplete="off" class="attributes">
                                                    <br>
                                                </label>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                            <?php
                                }
                            }
                        }
                    } else {
                        foreach ($product['product'][0]['variant_attributes'] as $attribute) {

                            $attribute_ids = explode(',', $attribute['ids']);
                            $attribute_values = explode(',', $attribute['values']);
                            $swatche_types = explode(',', $attribute['swatche_type']);
                            $swatche_values = explode(',', $attribute['swatche_value']);
                            $attribute_names = explode(',', $attribute['attr_name']);

                            for ($i = 0; $i < count($swatche_types); $i++) {
                                if (!empty($swatche_types[$i]) && $swatche_values[$i] != "") {
                                    $style = '<style> .product-page-details .btn-group>.active { background-color: #ffffff; color: #000000; border: 1px solid black;}</style>';
                                } else if ($swatche_types[$i] == 0 && $swatche_values[$i] == null) {
                                    $style1 = '<style> .product-page-details .btn-group>.active { background-color: var(--primary-color);color: white!important;}</style>';
                                }
                            }  ?>
                            <h6 class="m-0 mt-2"><?= $attribute['attr_name'] ?></h6>
                            <?php
                            if (!empty($attribute['ids']) || $attribute['ids'] != null || $attribute['ids'] != "") {
                            ?>
                                <div class="btn-group btn-group-toggle gap-1 d-flex flex-wrap" data-toggle="buttons" id="<?= $attribute['attr_name'] ?>">
                                    <?php
                                    foreach ($attribute_values as $key => $row) {
                                        if ($swatche_types[$key] == "1") {
                                            echo '<style> .product-page-details .btn-group>.active { border: 1px solid black;}</style>';
                                            $color_code = "style='background-color:" . $swatche_values[$key] . ";'";  ?>
                                            <label class="btn text-center fullCircle rounded-circle p-3" <?= $color_code ?>>
                                                <input type="radio" name="<?= $attribute['attr_name'] ?>" value="<?= $attribute_ids[$key] ?>" autocomplete="off" class="attributes filter-input">
                                            </label>

                                        <?php } else if ($swatche_types[$key] == "2") { ?>
                                            <?= $style ?>
                                            <label class="btn text-center ">
                                                <img class="swatche-image lazy category-image-container" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= $swatche_values[$key] ?>">
                                                <input type="radio" name="<?= $attribute['attr_name'] ?>" value="<?= $attribute_ids[$key] ?>" autocomplete="off" class="attributes">
                                                <br>
                                            </label>

                                        <?php } else { ?>
                                            <?= '<style> .product-page-details .btn-group>.active { background-color: var(--primary-color);color: white!important;}</style>'; ?>

                                            <label class="btn btn-aqua btn-default btn-xs mb-0 rounded-2 text-center w-11">
                                                <?= $row ?>
                                                <input type="radio" name="<?= $attribute['attr_name'] ?>" value="<?= $attribute_ids[$key] ?>" autocomplete="off" class="attributes">
                                                <br>
                                            </label>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                        <?php
                            }
                        }
                    }
                }
                if (!empty($product['product'][0]['variants']) && isset($product['product'][0]['variants'])) {
                    $total_images = 1;
                    foreach ($product['product'][0]['variants'] as $variant) {
                        ?>
                        <input type="hidden" class="variants" name="variants_ids" data-image-index="<?= $total_images ?>" data-name="" value="<?= $variant['variant_ids'] ?>" data-id="<?= $variant['id'] ?>" data-price="<?= $variant['price'] ?>" data-special_price="<?= $variant['special_price'] ?>" data-stock="<?= $variant['stock'] ?>" />

                <?php
                        $total_images += count($variant['images']);
                    }
                }
                ?>

                <?php if ($product['product'][0]['type'] != 'digital_product') { ?>
                    <?php
                    $shiprocket_settings = get_settings('shipping_method', true);
                    if ((isset($shiprocket_settings['pincode_wise_deliverability']) && $shiprocket_settings['pincode_wise_deliverability'] == 1) || (isset($shiprocket_settings['local_shipping_method']) && isset($shiprocket_settings['shiprocket_shipping_method']) && $shiprocket_settings['local_shipping_method'] == 1 && $shiprocket_settings['shiprocket_shipping_method'] == 1)) {
                    ?>
                        <form class="mt-2" id="validate-zipcode-form" method="POST">
                            <div class="form-row">
                                <div class=" col-md-6">
                                    <input type="hidden" name="product_id" value="<?= $product['product'][0]['id'] ?>">
                                    <input type="text" class="form-control" id="zipcode" placeholder="Zipcode" name="zipcode" autocomplete="off" required value="<?= $product['product'][0]['zipcode']; ?>">
                                </div>
                                <button type="submit" class="button button-primary-outline" id="validate_zipcode" <?= ($this->ion_auth->logged_in()) ? '' : 'disabled' ?>><?= !empty($this->lang->line('check_availability')) ? str_replace('\\', '', $this->lang->line('check_availability')) : 'Check Availability' ?></button>
                            </div>
                            <div class="mt-2" id="error_box">
                                <?php if (!empty($product['product'][0]['zipcode'])) { ?>
                                    <b class="text-<?= ($product['product'][0]['is_deliverable']) ? "success" : "danger" ?>"><?= !empty($this->lang->line('product_is')) ? str_replace('\\', '', $this->lang->line('product_is')) : 'Product is' ?> <?= ($product['product'][0]['is_deliverable']) ? "" : "not" ?> <?= !empty($this->lang->line('delivarable_on')) ? str_replace('\\', '', $this->lang->line('delivarable_on')) : 'Delivarable on' ?> &quot; <?= $product['product'][0]['zipcode']; ?> &quot; </b>
                                <?php } ?>
                            </div>
                        </form>
                    <?php }
                    if (isset($shiprocket_settings['city_wise_deliverability']) && $shiprocket_settings['city_wise_deliverability'] == 1 && $shiprocket_settings['shiprocket_shipping_method'] != 1) { ?>
                        <form class="mt-2" id="validate-city-form" method="POST">
                            <div class="form-row">
                                <div class=" col-md-6">
                                    <input type="hidden" name="product_id" value="<?= $product['product'][0]['id'] ?>">
                                    <input type="text" class="form-control rounded" id="zipcode" placeholder="City" name="city" autocomplete="off" required value="<?= $product['product'][0]['zipcode']; ?>">
                                </div>
                                <button type="submit" class="button button-primary-outline" id="validate_city" <?= ($this->ion_auth->logged_in()) ? '' : 'disabled' ?>><?= !empty($this->lang->line('check_availability')) ? str_replace('\\', '', $this->lang->line('check_availability')) : 'Check Availability' ?></button>
                            </div>
                            <div class="mt-2" id="error_box">
                                <?php if (!empty($product['product'][0]['zipcode'])) { ?>
                                    <b class="text-<?= ($product['product'][0]['is_deliverable']) ? "success" : "danger" ?>"><?= !empty($this->lang->line('product_is')) ? str_replace('\\', '', $this->lang->line('product_is')) : 'Product is' ?> <?= ($product['product'][0]['is_deliverable']) ? "" : "not" ?> <?= !empty($this->lang->line('delivarable_on')) ? str_replace('\\', '', $this->lang->line('delivarable_on')) : 'Delivarable on' ?> &quot; <?= $product['product'][0]['zipcode']; ?> &quot; </b>
                                <?php } ?>
                            </div>
                        </form>
                    <?php } ?>
                <?php } ?>
                <!--end profile -->
                <div class="num-block skin-2 py-4 mt-2">
                    <div class="num-in">
                        <span class="minus dis" data-min="<?= (isset($product['product'][0]['minimum_order_quantity']) && !empty($product['product'][0]['minimum_order_quantity'])) ? $product['product'][0]['minimum_order_quantity'] : 1 ?>" data-step="<?= (isset($product['product'][0]['minimum_order_quantity']) && !empty($product['product'][0]['quantity_step_size'])) ? $product['product'][0]['quantity_step_size'] : 1 ?>"></span>
                        <input type="text" name="qty" class="in-num" value="<?= (isset($product['product'][0]['minimum_order_quantity']) && !empty($product['product'][0]['minimum_order_quantity'])) ? $product['product'][0]['minimum_order_quantity'] : 1 ?>" data-step="<?= (isset($product['product'][0]['minimum_order_quantity']) && !empty($product['product'][0]['quantity_step_size'])) ? $product['product'][0]['quantity_step_size'] : 1 ?>" data-min="<?= (isset($product['product'][0]['minimum_order_quantity']) && !empty($product['product'][0]['minimum_order_quantity'])) ? $product['product'][0]['minimum_order_quantity'] : 1 ?>" data-max="<?= (isset($product['product'][0]['total_allowed_quantity']) && !empty($product['product'][0]['total_allowed_quantity'])) ? $product['product'][0]['total_allowed_quantity'] : '' ?>">
                        <span class="plus" data-max="<?= (isset($product['product'][0]['total_allowed_quantity']) && !empty($product['product'][0]['total_allowed_quantity'])) ? $product['product'][0]['total_allowed_quantity'] : '' ?> " data-step="<?= (isset($product['product'][0]['minimum_order_quantity']) && !empty($product['product'][0]['quantity_step_size'])) ? $product['product'][0]['quantity_step_size'] : 1 ?>"></span>
                    </div>
                </div>
                <div class="bg-gray mt-2 mb-2">
                    <?php ($product['product'][0]['tax_percentage'] != 0) ? "Tax" . $product['product'][0]['tax_percentage'] : '' ?>
                </div>
                <input type="hidden" class="variants_data" id="variants_data" data-name="<?= $product['product'][0]['name'] ?>" data-image="<?= $product['product'][0]['image'] ?>" data-id="<?= $variant['id'] ?>" data-price="<?= $variant['price'] ?>" data-special_price="<?= $variant['special_price'] ?>">
                <div class="" id="result"></div>
                <div class="d-flex gap-2 overflow-auto">
                    <?php
                    if (count($product['product'][0]['variants']) <= 1) {
                        $variant_id = $product['product'][0]['variants'][0]['id'];
                    } else {
                        $variant_id = "";
                    }
                    if ($product['product'][0]['type'] == 'simple_product') {
                        $product_stock = $product['product'][0]['stock'];
                    } else {
                        $product_stock = $product['product'][0]['total_stock'];
                    }
                    ?>
                    <?php if ($product['product'][0]['variants'][0]['cart_count'] != 0) { ?>
                        <a class="buttons btn-6-6 extra-small m-0" href="<?= base_url('cart') ?>"><i class='fas fa-arrow-right'></i> <?= !empty($this->lang->line('go_to_cart')) ? str_replace('\\', '', $this->lang->line('go_to_cart')) : 'Go To Cart' ?></a>
                    <?php } else { ?>
                        <button type="button" name="add_cart" class="buttons btn-6-3 extra-small m-0 add_to_cart mt-1" id="add_cart" data-product-id="<?= $product['product'][0]['id'] ?>" data-product-stock="<?= $product_stock ?>" data-product-title="<?= $product['product'][0]['name'] ?>" data-product-image="<?= $product['product'][0]['image'] ?>" data-product-price="<?= ($variant['special_price'] > 0 && $variant['special_price'] != '0' && $variant['special_price'] != '') ? $variant['special_price'] : $variant['price']; ?>" data-product-description="<?= short_description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', strip_tags($product['product'][0]['short_description'])))); ?>" data-step="<?= (isset($product['product'][0]['minimum_order_quantity']) && !empty($product['product'][0]['quantity_step_size'])) ? $product['product'][0]['quantity_step_size'] : 1 ?>" data-min="<?= (isset($product['product'][0]['minimum_order_quantity']) && !empty($product['product'][0]['minimum_order_quantity'])) ? $product['product'][0]['minimum_order_quantity'] : 1 ?>" data-max="<?= (isset($product['product'][0]['total_allowed_quantity']) && !empty($product['product'][0]['total_allowed_quantity'])) ? $product['product'][0]['total_allowed_quantity'] : '' ?>" data-product-variant-id="<?= $variant_id ?>">
                            <i class="fas fa-cart-plus"></i> <?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add to Cart' ?>
                        </button>
                    <?php } ?>
                    <button type="button" name="buy_now" class="buttons btn-6-5 extra-small m-0 buy_now mt-1" id="buy_now" data-product-id="<?= $product['product'][0]['id'] ?>" data-product-title="<?= $product['product'][0]['name'] ?>" data-product-image="<?= $product['product'][0]['image'] ?>" data-product-price="<?= ($variant['special_price'] > 0 && $variant['special_price'] != '0' && $variant['special_price'] != '') ? $variant['special_price'] : $variant['price']; ?>" data-product-description="<?= short_description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', strip_tags($product['product'][0]['short_description'])))); ?>" data-step="<?= (isset($product['product'][0]['minimum_order_quantity']) && !empty($product['product'][0]['quantity_step_size'])) ? $product['product'][0]['quantity_step_size'] : 1 ?>" data-min="<?= (isset($product['product'][0]['minimum_order_quantity']) && !empty($product['product'][0]['minimum_order_quantity'])) ? $product['product'][0]['minimum_order_quantity'] : 1 ?>" data-max="<?= (isset($product['product'][0]['total_allowed_quantity']) && !empty($product['product'][0]['total_allowed_quantity'])) ? $product['product'][0]['total_allowed_quantity'] : '' ?>" data-product-variant-id="<?= $variant_id ?>">
                        <i class="fas fa-bolt"></i> <?= !empty($this->lang->line('buy_now')) ? str_replace('\\', '', $this->lang->line('buy_now')) : 'Buy Now' ?>
                    </button>
                    <button type="button" name="compare" class="buttons btn-6-6 extra-small m-0 mt-1 compare" id="compare" data-product-id="<?= $product['product'][0]['id'] ?>" data-product-variant-id="<?= $variant_id ?>">
                        <i class="fa fa-random"></i> <?= !empty($this->lang->line('compare')) ? str_replace('\\', '', $this->lang->line('compare')) : 'Compare' ?>
                    </button>
                    <?php if ($product['product'][0]['is_favorite'] == 0) { ?>
                        <button class="buttons btn-6-1 extra-small m-0 add-fav mt-1" id="add_to_favorite_btn" data-product-id="<?= $product['product'][0]['id'] ?>">
                            <i class="fas fa-heart mr-2"></i>
                            <span><?= !empty($this->lang->line('add_to_favorite')) ? str_replace('\\', '', $this->lang->line('add_to_favorite')) : 'Favorite' ?></span>
                        </button>
                    <?php } else { ?>
                        <button class="buttons btn-6-1 extra-small m-0 mt-1 remove-fav" id="add_to_favorite_btn" data-product-id="<?= $product['product'][0]['id'] ?>">
                            <i class="fas fa-heart mr-2"></i>
                            <span><?= !empty($this->lang->line('remove_from_favorite')) ? str_replace('\\', '', $this->lang->line('remove_from_favorite')) : 'Favorite' ?></span>
                        </button>
                    <?php } ?>
                    <?php
                    if (isset($settings['whatsapp_status']) && $settings['whatsapp_status'] == 1 && isset($settings['whatsapp_number'])) { ?>
                        <a href="https://api.whatsapp.com/send?phone= <?= ($settings['whatsapp_number'] != '' && isset($settings['whatsapp_number'])) ? $settings['whatsapp_number'] : ((!defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0)  ? str_repeat("X", strlen($settings['whatsapp_number']) - 3) . substr($settings['whatsapp_number'], -3) : $settings['whatsapp_number'])   ?> &amp;text=Hello, I Seen This <?= $product['product'][0]['name'] ?> In Your Website And I Want to Buy This <?= base_url('products/details/' . $product['product'][0]['slug']) ?>" target="_blank" title="Order From Whatsapp" class="buttons btn-6-4 extra-small m-0 mt-1">
                            <i class="fab fa-whatsapp"></i>
                            <span><?= !empty($this->lang->line('order_from_whatsapp')) ? str_replace('\\', '', $this->lang->line('order_from_whatsapp')) : 'Order From Whatsapp' ?></span>
                        </a>
                    <?php } ?>
                </div>
                <div class="mt-2"><?= !empty($this->lang->line('seller')) ? str_replace('\\', '', $this->lang->line('seller')) : 'Seller' ?>
                    <?php if (isset($product['product'][0]['seller_name']) && !empty($product['product'][0]['seller_name'])) { ?>
                        <a target="_BLANK" href="<?= base_url('products?seller=' . $seller_slug[0]['slug']) ?>"><?= $product['product'][0]['seller_name'] ?></a>
                        </span>
                    <?php } ?>
                </div>

                <?php if ($this->ion_auth->logged_in()) { ?>
                    <div class="mt-2"><?= !empty($this->lang->line('seller')) ? str_replace('\\', '', $this->lang->line('chat_with')) : 'Chat With' ?>
                        <?php if (isset($product['product'][0]['seller_name']) && !empty($product['product'][0]['seller_name'])) { ?>
                            <a id="chat-with-button" class="text-success" data-id="<?= $product['product'][0]['seller_id'] ?>" href="#">
                                <i class="fa fa-comments"></i>
                                <?= $product['product'][0]['seller_name'] ?></a>
                            </span>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if (isset($product['product'][0]['tags']) && !empty($product['product'][0]['tags'])) { ?>
                    <div class="mt-2"><?= !empty($this->lang->line('tags')) ? str_replace('\\', '', $this->lang->line('tags')) : 'Tags' ?>
                        <?php foreach ($product['product'][0]['tags'] as $tag) { ?>
                            <a href="<?= base_url('products/tags/' . $tag) ?>"><span class="badge badge-secondary p-1"><?= $tag ?></span></a>
                        <?php } ?>
                        </span>
                    <?php } ?>
                    <div class="mt-3 row product-permission-feature no-gutters">
                        <?php if (isset($product['product'][0]['cod_allowed']) && !empty($product['product'][0]['cod_allowed']) && $product['product'][0]['cod_allowed'] == 1 && $payment_methods['cod_method'] == 1) {  ?>
                            <div class="col-2">
                                <div class="product-permission">
                                    <img src="<?= base_url('assets/front_end/classic/images/cod_logo.png') ?>">
                                </div>
                                <div class="product-permission-text">
                                    <?= !empty($this->lang->line('cod')) ? str_replace('\\', '', $this->lang->line('cod')) : 'COD' ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-2">
                            <?php if (isset($product['product'][0]['is_cancelable']) && !empty($product['product'][0]['is_cancelable']) && $product['product'][0]['is_cancelable'] == 1) {  ?>
                                <div class="product-permission" class="ml-4">
                                    <img src="<?= base_url('assets/front_end/classic/images/cancelable.png') ?>">
                                </div>
                                <div class="product-permission-text">
                                    <?= !empty($this->lang->line('cancelable_till')) ? str_replace('\\', '', $this->lang->line('cancelable_till')) : 'Cancelable till' ?><?= ' ' . $product['product'][0]['cancelable_till'] ?>
                                </div>
                            <?php } else { ?>
                                <div class="product-permission" class="ml-4">
                                    <img src="<?= base_url('assets/front_end/classic/images/notcancelable.png') ?>">
                                </div>
                                <div class="product-permission-text">
                                    <?= !empty($this->lang->line('no_cancellation')) ? str_replace('\\', '', $this->lang->line('no_cancellation')) : 'No Cancellation' ?>
                                </div>
                            <?php  } ?>
                        </div>
                        <div class="col-2">
                            <?php if (isset($product['product'][0]['is_returnable']) && !empty($product['product'][0]['is_returnable']) && $product['product'][0]['is_returnable'] == 1) {  ?>
                                <div class="product-permission" class="ml-4">
                                    <img src="<?= base_url('assets/front_end/classic/images/returnable.png') ?>">
                                </div>
                                <div class="product-permission-text">
                                    <?= $settings['max_product_return_days'] ?> <?= !empty($this->lang->line('days_returnable')) ? str_replace('\\', '', $this->lang->line('days_returnable')) : 'Days Returnable' ?>
                                </div>
                            <?php } else { ?>
                                <div class="product-permission" class="ml-4">
                                    <img src="<?= base_url('assets/front_end/classic/images/notreturnable.png') ?>">
                                </div>
                                <div class="product-permission-text">
                                    <?= !empty($this->lang->line('no_returnable')) ? str_replace('\\', '', $this->lang->line('no_returnable')) : 'No Returnable' ?>
                                </div>
                            <?php  } ?>
                        </div>
                        <?php if (isset($product['product'][0]['guarantee_period']) && !empty($product['product'][0]['guarantee_period'])) {  ?>
                            <div class="col-2">
                                <div class="product-permission" class="ml-4">
                                    <img src="<?= base_url('assets/front_end/classic/images/guarantee.png') ?>">
                                </div>
                                <div class="product-permission-text">
                                    <?= $product['product'][0]['guarantee_period'] ?> <?= !empty($this->lang->line('guarantee')) ? str_replace('\\', '', $this->lang->line('guarantee')) : 'Guarantee' ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if (isset($product['product'][0]['warranty_period']) && !empty($product['product'][0]['warranty_period'])) {  ?>
                            <div class="col-2">
                                <div class="product-permission" class="ml-4">
                                    <img src="<?= base_url('assets/front_end/classic/images/warranty.png') ?>">
                                </div>
                                <div class="product-permission-text">
                                    <?= $product['product'][0]['warranty_period'] ?> <?= !empty($this->lang->line('warranty')) ? str_replace('\\', '', $this->lang->line('warranty')) : 'Warranty' ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    
                    <?php if (isset($product['product'][0]['cod_allowed']) && !empty($product['product'][0]['cod_allowed']) && $product['product'][0]['cod_allowed'] == 1 && $payment_methods['cod_method'] == 1) { ?>
                        <span class="fs-14 fw-bolder text-info">Cash on Delivery is eligible for orders above <?= $settings['currency'] . $payment_methods['min_cod_amount'] ?> and below <?= $settings['currency'] . $payment_methods['max_cod_amount'] ?></span>
                    <?php } ?>

                    <div class="mt-3">
                        <span class="product-details-title"><?= !empty($this->lang->line('product_details')) ? str_replace('\\', '', $this->lang->line('product_details')) : 'Product Details : ' ?> </span>
                        <hr>
                        <table class="product-detail-tab">
                            <?php if (isset($product['product'][0]['attributes']) && !empty($product['product'][0]['attributes']) && $product['product'][0]['attributes'] != []) { ?>
                                <?php foreach ($product['product'][0]['attributes'] as $attrbute) { ?>
                                    <tr>
                                        <td class="col-sm-2"><?= ucfirst($attrbute['name']) ?> :</td>
                                        <td><?= $attrbute['value'] ?></td>
                                    </tr>
                                <?php }
                            }
                            if (isset($product['product'][0]['made_in']) && !empty($product['product'][0]['made_in']) && $product['product'][0]['made_in'] != '') { ?>
                                <tr>
                                    <td class="col-sm-2"><?= !empty($this->lang->line('made_in')) ? str_replace('\\', '', $this->lang->line('made_in')) : 'Made In : ' ?></td>
                                    <td><?= ucfirst($product['product'][0]['made_in']) ?></td>
                                </tr>
                                <?php }
                            if (isset($product['product'][0]['brand']) && !empty($product['product'][0]['brand']) && $product['product'][0]['brand'] != '') {

                                $brand_img = fetch_details('brands', ['name' => $product['product'][0]['brand']]);
                                if (isset($brand_img) && !empty($brand_img)) {
                                ?>
                                    <tr>
                                        <td class="col-sm-2"><?= !empty($this->lang->line('brand')) ? str_replace('\\', '', $this->lang->line('brand')) : 'Brand : ' ?> </td>
                                        <td>
                                            <a href="<?= base_url('products?brand=' . html_escape($brand_img[0]['slug'])) ?>" class="text-decoration-none">
                                                <img src="<?= base_url(rawurlencode($brand_img[0]['image'])) ?>" class="h-6">
                                                <?= ucfirst($product['product'][0]['brand']) ?>
                                            </a>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>
                        </table>
                    </div>
                    <div class="mt-3" id="share"></div>
                    </div>
            </div>
            <div class="col-12 row mt-4">
                <div class="modal fade" id="add-faqs-form">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h5 class="modal-title"><?= !empty($this->lang->line('add_faq')) ? str_replace('\\', '', $this->lang->line('add_faq')) : 'Add FAQ' ?></h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">x
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="post" action='<?= base_url('products/add_faqs') ?>' id="add-faqs">
                                    <div class="form-group">

                                        <input type="hidden" name=" <?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                                        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'];  ?>">
                                        <input type="hidden" name="seller_id" value="<?= $product['product'][0]['seller_id'];  ?>">
                                        <input type="hidden" name="product_id" value="<?= $product['product'][0]['id']  ?>">
                                        <input type="text" class="form-control" id="question" placeholder="<?= !empty($this->lang->line('enter_your_question_here')) ? str_replace('\\', '', $this->lang->line('enter_your_question_here')) : 'Enter Your Question Here' ?>" name="question">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm" id="add-faqs" name="add-faqs" value="Save"><?= !empty($this->lang->line('add_faq')) ? str_replace('\\', '', $this->lang->line('add_faq')) : 'Add FAQ' ?></button>
                                    <div class="mt-3">
                                        <div id="add_faqs_result"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <nav class="w-100">
                    <div class="nav nav-tabs" id="product-tab" role="tablist">
                        <?php
                        if (isset($product['product'][0]['description']) && !empty($product['product'][0]['description'])) { ?>
                            <a class="nav-item nav-link product-nav-tab active" id="product-desc-tab" data-toggle="tab" href="#product-desc" role="tab" aria-controls="product-desc" aria-selected="true"><?= !empty($this->lang->line('description')) ? str_replace('\\', '', $this->lang->line('description')) : 'Description' ?></a>
                        <?php } ?>
                        <a class="nav-item nav-link product-nav-tab " id="product-review-tab" data-toggle="tab" href="#product-review" role="tab" aria-controls="product-review" aria-selected="false"><?= !empty($this->lang->line('reviews')) ? str_replace('\\', '', $this->lang->line('reviews')) : 'Reviews' ?></a>
                        <a class="nav-item nav-link product-nav-tab " id="product-seller-tab" data-toggle="tab" href="#product-seller" role="tab" aria-controls="product-seller" aria-selected="false"><?= !empty($this->lang->line('sold_by')) ? str_replace('\\', '', $this->lang->line('sold_by')) : 'Sold by' ?></a>
                        <a class="nav-item nav-link product-nav-tab" id="product-faq-tab" data-toggle="tab" href="#product-faq" role="tab" aria-controls="product-faq" aria-selected="true"><?= !empty($this->lang->line('faq')) ? str_replace('\\', '', $this->lang->line('faq')) : 'FAQ' ?></a>
                    </div>
                </nav>
                <div class="tab-content p-3 w-100" id="nav-tabContent">
                    <div class="tab-pane fade active show" id="product-desc" role="tabpanel" aria-labelledby="product-desc-tab">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12 description">

                                    <?= (isset($product['product'][0]['description']) && !empty($product['product'][0]['description'])) ? $product['product'][0]['description'] : ""  ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- product faq tab -->
                    <div class="tab-pane " id="product-faq" role="tabpanel" aria-labelledby="product-faq-tab">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <?php if ($this->ion_auth->logged_in()) { ?>
                                        <div class="add-faqs-form float-right">
                                            <button class="btn btn-primary btn-xs mt-2" type="submit" data-toggle="modal" data-target="#add-faqs-form"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-12">
                                    <div class="accordion mt-3" id="accordionExample">

                                        <?php if ((!isset($faq['data']) && empty($faq['data'])) || $faq['data'] == []) { ?>
                                            <div class="col-md-12 d-flex justify-content-center h5"><?= !empty($this->lang->line('no_faqs_found')) ? str_replace('\\', '', $this->lang->line('no_faqs_found')) : 'No FAQs Found' ?></div>
                                        <?php } else { ?>
                                            <?php foreach ($faq['data'] as $row) {
                                            ?>
                                                <?php if (isset($row['answer']) && !empty($row['answer']) && ($row['answer'] != '')) {
                                                ?>
                                                    <div class="card">
                                                        <div class="card-header product-faqs-card" id="<?= "h-" . $row['id'] ?>">
                                                            <h2 class="clearfix mb-0">
                                                                <a class="home_faq_btn btn btn-link collapsed" data-toggle="collapse" data-target="#<?= "c-" . $row['id'] ?>" aria-expanded="true" aria-controls="collapseone">
                                                                    <?= html_escape($row['question']) ?>
                                                                    <i class="fa fa-angle-down rotate"></i></a>
                                                            </h2>
                                                        </div>
                                                        <?php $product_data = fetch_details('users', ['id' => $row['answered_by']], 'username'); ?>
                                                        <div id="<?= "c-" . $row['id'] ?>" class="collapse" aria-labelledby="<?= "h-" . $row['id'] ?>" data-parent="#accordionExample">
                                                            <div class="card-body"><?= html_escape($row['answer']) ?></div>
                                                            <div class="card-body"><?= !empty($this->lang->line('answer_by')) ? str_replace('\\', '', $this->lang->line('answer_by')) : 'Answer by' ?> : <?= isset($product_data[0]['username']) && !empty($product_data[0]['username']) ? html_escape($product_data[0]['username']) : "" ?></div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                        <?php }
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="product-review" role="tabpanel" aria-labelledby="product-review-tab">
                        <?php
                        if (!empty($review_images['total_images'])) {
                            if ($review_images['total_images'] > 0) { ?>
                                <h3 class="review-title"> <?= !empty($this->lang->line('user_review_images')) ? str_replace('\\', '', $this->lang->line('user_review_images')) : 'User Review Images' ?> (<span><?= $review_images['total_images'] ?></span>)</h3>
                            <?php
                            }
                        }

                        if (isset($review_images['product_rating']) && !empty($review_images['product_rating'])) {

                            ?>
                            <div class="row reviews">
                                <?php
                                $count = 0;
                                $total_images = $review_images['total_images'];
                                for ($i = 0; $i < count($review_images['product_rating']); $i++) {
                                    if (!empty($review_images['product_rating'][$i]['images'])) {
                                        for ($j = 0; $j < count($review_images['product_rating'][$i]['images']); $j++) {
                                            if ($count <= 8) {

                                                if ($count == 8 && !empty($review_images['product_rating'][$i]['images'][$j])) {

                                ?>
                                                    <div class="col-sm-1">
                                                        <div class="review-box">

                                                            <a href="<?= $review_images['product_rating'][$i]['images'][$j];  ?>">
                                                                <p class="limit_position"><?= "+" . ((int)$total_images - 8) ?></p>
                                                                <img id="review-image-title" src="<?= $review_images['product_rating'][$i]['images'][$j];  ?>" alt="Review Image" data-reached-end="false" data-review-limit="1" data-review-offset="0" data-product-id="<?= $review_images['product_rating'][$i]['product_id'] ?>" data-review-title="User Review Images(<span><?= $review_images['total_images'] ?></span>)" data-izimodal-open="#user-review-images" class="overlay">
                                                            </a>
                                                        </div>
                                                    </div>
                                                <?php } else if (!empty($review_images['product_rating'][$i]['images'][$j])) {
                                                ?>

                                                    <div class="col-sm-1">
                                                        <div class="review-box">
                                                            <a href="<?= $review_images['product_rating'][$i]['images'][$j];  ?>" data-lightbox="users-review-images" data-title="<?= "<button class='label btn-success'>" . $review_images['product_rating'][$i]['rating'] . " <i class='fa fa-star'></i></button></br>" . $review_images['product_rating'][$i]['user_name'] . "<br>" . $review_images['product_rating'][$i]['comment'] ?> ">
                                                                <img src="<?= $review_images['product_rating'][$i]['images'][$j];  ?>" alt="Review Images">
                                                            </a>
                                                        </div>
                                                    </div>
                                <?php }
                                                $count += 1;
                                            }
                                        }
                                    }
                                }
                                ?>
                            </div>
                        <?php } ?>
                        <div class="row mt-2">
                            <div class="col-xl-7">
                                <h3 class="review-title"> <span id="no_ratings"><?= $product['product'][0]['no_of_ratings'] ?></span> <?= !empty($this->lang->line('reviews_for_this_product')) ? str_replace('\\', '', $this->lang->line('reviews_for_this_product')) : 'Reviews For this Product' ?></h3>
                                <ol class="review-list" id="review-list">
                                    <?php if (isset($my_rating) && !empty($my_rating)) {
                                        foreach ($my_rating['product_rating'] as $row) { ?>
                                            <li class="review-container">
                                                <div class="review-image">
                                                    <img src="<?= THEME_ASSETS_URL . 'images/user.png' ?>" alt="" width="65" height="65">
                                                </div>
                                                <div class="review-comment">
                                                    <div class="rating-list">
                                                        <div class="product-rating">
                                                            <input type="text" class="kv-fa rating-loading" value="<?= $row['rating'] ?>" data-size="xs" title="" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="review-info">
                                                        <h4 class="reviewer-name"><?= $row['user_name'] ?></h4>
                                                        <span class="review-date text-muted"><?= $row['data_added'] ?></span>
                                                    </div>
                                                    <div class="review-text">
                                                        <p class="text-muted"><?= $row['comment'] ?></p>
                                                        <a id="delete_rating" href="<?= base_url('products/delete-rating') ?>" class="text-danger" data-rating-id="<?= $row['id'] ?>">Delete</a>
                                                    </div>
                                                    <div class="row reviews">
                                                        <?php foreach ($row['images'] as $image) { ?>
                                                            <div class="col-sm-2">
                                                                <div class="review-box">

                                                                    <a href="<?= $image;  ?>" data-lightbox="review-images">
                                                                        <img src="<?= $image; ?>" alt="Review Image">
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </li>
                                    <?php }
                                    } ?>
                                    <?php if (isset($product_ratings) && !empty($product_ratings)) {
                                        $user_id = (isset($user->id)) ? $user->id : '';
                                        foreach ($product_ratings['product_rating'] as $row) {
                                            if ($row['user_id'] != $user_id) { ?>
                                                <li class="review-container">
                                                    <div class="review-image">
                                                        <img src="<?= THEME_ASSETS_URL . 'images/user.png' ?>" alt="" width="65" height="65">
                                                    </div>
                                                    <div class="review-comment">
                                                        <div class="rating-list">
                                                            <div class="product-rating">
                                                                <input type="text" class="kv-fa rating-loading" value="<?= $row['rating'] ?>" data-size="xs" title="" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="review-info">
                                                            <h4 class="reviewer-name"><?= $row['user_name'] ?></h4>
                                                            <span class="review-date text-muted"><?= $row['data_added'] ?></span>
                                                        </div>
                                                        <div class="review-text">
                                                            <p class="text-muted"><?= $row['comment'] ?></p>
                                                        </div>
                                                        <div class="row reviews">
                                                            <?php foreach ($row['images'] as $image) { ?>
                                                                <div class="col-md-2">
                                                                    <div class="review-box">

                                                                        <a href="<?= $image; ?>" data-lightbox="review-images">
                                                                            <img src="<?= $image; ?>" alt="Review Image">
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </li>
                                    <?php }
                                        }
                                    } ?>
                                </ol>
                            </div>
                            <!-- Edit product review -->

                            <?php if ($product['product'][0]['is_purchased'] == 1 && !empty($my_rating)) {
                                $form_link = (!empty($my_rating)) ? base_url('products/save-rating') : base_url('products/save-rating');  ?>
                                <div class="col-xl-5 " id="rating-box">
                                    <div class="add-review">
                                        <h3 class="review-title"><?= !empty($this->lang->line('edit_your_review')) ? str_replace('\\', '', $this->lang->line('edit_your_review')) : 'Edit Your Review' ?></h3>
                                        <form action="<?= $form_link ?>" id="product-rating-form" method="POST">
                                            <?php if (!empty($my_rating)) { ?>
                                                <input type="hidden" name="rating_id" value="<?= $my_rating['product_rating'][0]['id'] ?>">
                                            <?php } ?>
                                            <input type="hidden" name="product_id" value="<?= $product['product'][0]['id'] ?>">
                                            <div class="rating-form">
                                                <label for="rating"><?= !empty($this->lang->line('your_rating')) ? str_replace('\\', '', $this->lang->line('your_rating')) : 'Your rating' ?></label>
                                                <input type="text" class="kv-fa rating-loading" data-step="1" name="rating" value="<?= isset($my_rating['product_rating'][0]['rating']) ? $my_rating['product_rating'][0]['rating'] : '0' ?>" data-size="sm" title="">
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleFormControlTextarea1"><?= !empty($this->lang->line('your_review')) ? str_replace('\\', '', $this->lang->line('your_review')) : 'Your Review' ?></label>
                                                <textarea class="form-control" name="comment" rows="3"><?= isset($my_rating['product_rating'][0]['comment']) ? $my_rating['product_rating'][0]['comment'] : '' ?></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleFormControlTextarea1"><?= !empty($this->lang->line('images')) ? str_replace('\\', '', $this->lang->line('images')) : 'Images' ?></label>
                                                <input type="file" name="images[]" accept="image/x-png,image/gif,image/jpeg" multiple />
                                            </div>
                                            <button class="buttons extra-small primary-button text-center m-0" id="rating-submit-btn">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($product['product'][0]['is_purchased'] == 1) {
                                $form_link = (!empty($my_rating)) ? base_url('products/edit-rating') : base_url('products/save-rating');
                            ?>
                                <div class="col-xl-5 <?= (!empty($my_rating)) ? 'd-none' : '' ?>" id="rating-box">
                                    <div class="add-review">
                                        <h3 class="review-title"><?= !empty($this->lang->line('add_your_review')) ? str_replace('\\', '', $this->lang->line('add_your_review')) : 'Add Your Review' ?></h3>
                                        <form action="<?= $form_link ?>" id="product-rating-form" method="POST">
                                            <?php if (!empty($my_rating)) { ?>
                                                <input type="hidden" name="rating_id" value="<?= $my_rating['product_rating'][0]['id'] ?>">
                                            <?php } ?>
                                            <input type="hidden" name="product_id" value="<?= $product['product'][0]['id'] ?>">
                                            <div class="rating-form">
                                                <label for="rating"><?= !empty($this->lang->line('your_rating')) ? str_replace('\\', '', $this->lang->line('your_rating')) : 'Your rating' ?></label>
                                                <input type="text" class="kv-fa rating-loading" data-step="1" name="rating" value="<?= isset($my_rating['product_rating'][0]['rating']) ? $my_rating['product_rating'][0]['rating'] : '0' ?>" data-size="sm" title="">
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleFormControlTextarea1"><?= !empty($this->lang->line('your_review')) ? str_replace('\\', '', $this->lang->line('your_review')) : 'Your Review' ?></label>
                                                <textarea class="form-control" name="comment" rows="3"><?= isset($my_rating['product_rating'][0]['comment']) ? $my_rating['product_rating'][0]['comment'] : '' ?></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleFormControlTextarea1"><?= !empty($this->lang->line('images')) ? str_replace('\\', '', $this->lang->line('images')) : 'Images' ?></label>
                                                <input type="file" name="images[]" accept="image/x-png,image/gif,image/jpeg" multiple />
                                            </div>
                                            <button class="buttons extra-small primary-button text-center m-0" id="rating-submit-btn"><?= !empty($this->lang->line('submit')) ? str_replace('\\', '', $this->lang->line('submit')) : 'Submit' ?></button>
                                        </form>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if (isset($product_ratings) && !empty($product_ratings) && count($product_ratings['product_rating']) > 7) { ?>
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <button class="buttons btn-6-6" id="load-user-ratings" data-product="<?= $product['product'][0]['id'] ?>" data-limit="<?= $user_rating_limit ?>" data-offset="<?= $user_rating_offset ?>">Load more</button>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="tab-pane fade show" id="product-seller" role="tabpanel" aria-labelledby="product-seller-tab">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-2 seller-image-container">
                                    <img src="<?= $product['product'][0]['seller_profile'] ?>" class="pic-1">
                                </div>
                                <div class="col-sm-6">
                                    <div class="card-body-right seller_tab">
                                        <h4 class="mb-0"><?= $product['product'][0]['store_name'] . "  " ?><span class="badge badge-success "><?= number_format($product['product'][0]['seller_rating'], 1) . " " ?><i class="fa fa-star"></i></span> </h4>
                                        <span class="text-muted d-block mb-2"><?= $product['product'][0]['seller_name'] ?></span>
                                        <p><?= $product['product'][0]['store_description'] ?></p>
                                        <a target="_BLANK" href="<?= base_url('products?seller=' . $seller_slug[0]['slug']) ?>" class="button button-primary-outline">Products</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.card -->
    <div class="card card-solid mt-4">
        <h3 class="h3"><?= !empty($this->lang->line('related_products')) ? str_replace('\\', '', $this->lang->line('related_products')) : 'Related Products' ?> </h3>
        <!-- Default Style Design-->

        <div class="col-12 product-style-default pb-4 mt-2 mb-2">
            <div class="swiper-container product-image-swiper">
                <div <?= ($is_rtl == true) ? "dir='rtl'" : ""; ?> class="swiper-wrapper">
                    <?php foreach ($related_products['product'] as $row) {
                        if ($row['type'] == 'simple_product') {
                            $product_stock = $row['stock'];
                        } else {
                            $product_stock = $row['total_stock'];
                        }
                    ?>
                        <div class="swiper-slide">
                            <div class="product-grid">
                                <aside class="add-fav">
                                    <button type="button" class="btn far fa-heart add-to-fav-btn <?= ($row['is_favorite'] == 1) ? 'fa text-danger' : '' ?>" data-product-id="<?= $row['id'] ?>"></button>
                                </aside>
                                <div class="product-image">
                                    <div class="product-image-container">
                                        <a href="<?= base_url('products/details/' . $row['slug']) ?>">
                                            <img class="pic-1" src="<?= base_url('media/image?path=' . rawurlencode($row['relative_path']) . '&width=225&quality=80') ?>">
                                        </a>
                                    </div>
                                    <ul class="social">
                                        <li>
                                            <a href="#" class="quick-view-btn" data-tip="<?= !empty($this->lang->line('quick_view')) ? str_replace('\\', '', $this->lang->line('quick_view')) : 'Quick View' ?>" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $row['variants'][0]['id'] ?>" data-izimodal-open="#quick-view">
                                                <i class="fa fa-search"></i>
                                            </a>
                                        </li>
                                        <li>
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
                                            <a href="" data-tip="<?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add To Cart' ?>" class="add_to_cart" data-product-id="<?= $row['id'] ?>" data-product-stock="<?= $product_stock ?>" data-product-variant-id="<?= $variant_id ?>" data-product-title="<?= $row['name'] ?>" data-product-image="<?= $row['image'] ?>" data-product-price="<?= $variant_price; ?>" data-min="<?= $data_min; ?>" data-step="<?= $data_step; ?>" data-product-description="<?= short_description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', strip_tags($row['short_description'])))); ?>" data-izimodal-open="<?= $modal ?>">
                                                <i class="fa fa-shopping-cart"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <?php $variant_id = (count($row['variants']) <= 1) ? $row['variants'][0]['id'] : ""; ?>

                                            <a href="#" class="compare" data-tip="<?= !empty($this->lang->line('compare')) ? str_replace('\\', '', $this->lang->line('compare')) : 'Compare' ?>" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $variant_id ?>">
                                                <i class="fa fa-random"></i>
                                            </a>
                                        </li>
                                    </ul>
                                    <?php if (isset($row['min_max_price']['special_price']) && $row['min_max_price']['special_price'] != '' && $row['min_max_price']['special_price'] != 0 && $row['min_max_price']['special_price'] < $row['min_max_price']['min_price']) { ?>
                                        <span class="product-new-label"><?= !empty($this->lang->line('sale')) ? str_replace('\\', '', $this->lang->line('sale')) : 'Sale' ?> <?= $row['min_max_price']['discount_in_percentage'] ?>%</span>

                                    <?php } ?>
                                </div>
                                <div class="product-content">
                                    <h3 class="title title_wrap" title="<?= $row['name'] ?>">
                                        <a href="<?= base_url('products/details/' . $row['slug']) ?>"><?= str_replace('\r\n', '&#13;&#10;', strip_tags($row['name'])) ?></a>
                                    </h3>
                                    <?php if (($row['variants'][0]['special_price'] < $row['variants'][0]['price']) && ($row['variants'][0]['special_price'] != 0)) { ?>
                                        <p class="mb-0 mt-2 ">
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
                                        <p class="mb-0 mt-2 ">
                                            <span id="price" style='font-size: 20px;'>
                                                <?php echo $settings['currency'] ?>
                                                <?php
                                                $price = $row['variants'][0]['price'];
                                                echo format_price($price);
                                                ?>
                                            </span>
                                        </p>
                                    <?php } ?>
                                    <?php $variant_price = ($row['variants'][0]['special_price'] > 0 && $row['variants'][0]['special_price'] != '') ? $row['variants'][0]['special_price'] : $row['variants'][0]['price'];
                                    $data_min = (isset($row['minimum_order_quantity']) && !empty($row['minimum_order_quantity'])) ? $row['minimum_order_quantity'] : 1;
                                    $data_step = (isset($row['minimum_order_quantity']) && !empty($row['quantity_step_size'])) ? $row['quantity_step_size'] : 1;
                                    $data_max = (isset($row['total_allowed_quantity']) && !empty($row['total_allowed_quantity'])) ? $row['total_allowed_quantity'] : 0;
                                    ?>

                                    <a href="" class="add-to-cart add_to_cart" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $variant_id ?>" data-product-title="<?= $row['name'] ?>" data-product-image="<?= $row['image'] ?>" data-product-price="<?= $variant_price; ?>" data-min="<?= $data_min; ?>" data-step="<?= $data_step; ?>" data-product-description="<?= short_description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', strip_tags($row['short_description'])))); ?>" data-izimodal-open="<?= $modal ?>"><i class="fas fa-cart-plus"></i> <?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add To Cart' ?></a>

                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
            <div class="swiper-button-next product-image-swiper-next"></div>
            <div class="swiper-button-prev product-image-swiper-prev"></div>
        </div>
    </div>
</section>
<div id="user-review-images" class='product-page-content'>
    <div class="container" id="review-image-div">
        <?php
        if (isset($review_images['product_rating']) && !empty($review_images['product_rating'])) { ?>
            <div class="row reviews" id="user_image_data">

            </div>
            <div id="load_more_div">
            </div>
        <?php } ?>
    </div>
</div>
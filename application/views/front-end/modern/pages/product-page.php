<?php $total_images = 0; ?>
<!-- breadcrumb -->
<div class="content-wrapper deeplink_wrapper">
    <section class="wrapper bg-soft-grape">
        <div class="container py-3 py-md-5">
            <nav class="d-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent">
                    <li class="breadcrumb-item"><a href="<?= base_url('products') ?>" class="text-decoration-none"><?= !empty($this->lang->line('product')) ? str_replace('\\', '', $this->lang->line('product')) : 'Products' ?></a></li>
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
            <!-- /nav -->
        </div>
        <!-- /.container -->
    </section>
</div>
<!-- end breadcrumb -->

<?php $seller_slug = fetch_details("seller_data", ['user_id' => $product['product'][0]['seller_id']]);

?>
<section class="wrapper bg-light">
    <div class="container main-content my-10">

        <div class="row">
            <div class="col-md-7">

                <div class="swiper-container swiper-thumbs-container" data-margin="10" data-dots="false" data-nav="true" data-thumbs="true">
                    <div class="d-flex product-page-preview-image-section-md">
                        <div class="col-md-2 overflow-auto product-thumb-img overflow-height">
                            <div class="swiper swiper-thumbs swiper-vertical gallery-thumbs-1">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide swiper-img mb-1 product_main_image">
                                        <img src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('media/image?path=' . rawurlencode($product['product'][0]['relative_path']) . '&width=75&quality=80') ?>" class="rounded p-1 lazy">
                                    </div>
                                    <?php
                                    $variant_relative_path = array_column($product['product'][0]['variants'], 'variant_relative_path');
                                    if (!empty($variant_relative_path)) {
                                        foreach ($variant_relative_path as $variant_images) {
                                            if (!empty($variant_images)) {
                                                foreach ($variant_images as $image) { ?>
                                                    <div class="swiper-slide  swiper-img mb-1 product_main_image">
                                                        <img src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('media/image?path=' . rawurlencode($image) . '&width=75&quality=80') ?>" class="rounded p-1 lazy" alt="">
                                                    </div>
                                    <?php }
                                            }
                                        }
                                    } ?>
                                    <?php
                                    if (!empty($product['product'][0]['other_images_relative_path']) && isset($product['product'][0]['other_images_relative_path'])) {
                                        foreach ($product['product'][0]['other_images_relative_path'] as $other_image) {
                                    ?>
                                            <div class="swiper-slide  swiper-img mb-1 product_main_image">
                                                <img src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('media/image?path=' . rawurlencode($other_image) . '&width=75&quality=80') ?>" class="rounded p-1 lazy" alt="">
                                            </div>
                                    <?php }
                                    } ?>
                                    <?php
                                    if (isset($product['product'][0]['video_type']) && !empty($product['product'][0]['video_type'])) {
                                        $total_images++;
                                    ?>
                                        <div class="swiper-slide  swiper-img mb-1 product_main_image">
                                            <img src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('assets/admin/images/video-file.png') ?>" class="rounded p-1 lazy" alt="">
                                        </div>
                                    <?php } ?>

                                </div>
                                <!--/.swiper-wrapper -->
                                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                            </div>
                        </div>

                        <div class="col-md-10">
                            <div class="swiper  gallery-top-1">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <figure class="rounded">
                                            <img src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('media/image?path=' . rawurlencode($product['product'][0]['relative_path']) . '&width=900&quality=80') ?>" alt="" class="lazy product_other_images">
                                            <a class="item-link text-decoration-none" href="<?= base_url('media/image?path=' . $product['product'][0]['relative_path'] . '&width=900&quality=80') ?>" data-glightbox="" data-gallery="product-group"><i class="uil uil-focus-add"></i></a>
                                        </figure>
                                    </div>
                                    <?php
                                    $variant_relative_path = array_column($product['product'][0]['variants'], 'variant_relative_path');

                                    if (!empty($variant_relative_path)) {
                                        foreach ($variant_relative_path as $variant_images) {
                                            if (!empty($variant_images)) {
                                                foreach ($variant_images as $image) {
                                    ?>
                                                    <div class="swiper-slide 12345">
                                                        <figure class="rounded">
                                                            <img src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('media/image?path=' . rawurlencode($image) . '&width=900&quality=80') ?>" class="lazy product_other_images" alt="">
                                                            <a class="item-link text-decoration-none" href="<?= base_url('media/image?path=' . $image . '&width=900&quality=80') ?>" data-glightbox="" data-gallery="product-group"><i class="uil uil-focus-add"></i></a>
                                                        </figure>
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
                                                <figure class="rounded">
                                                    <img src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('media/image?path=' . rawurlencode($other_image) . '&width=900&quality=80') ?>" class="lazy product_other_images" alt="" id="img_01">
                                                    <a class="item-link text-decoration-none" href="<?= base_url('media/image?path=' . $other_image . '&width=900&quality=80') ?>" data-glightbox="" data-gallery="product-group"><i class="uil uil-focus-add"></i></a>
                                                </figure>
                                            </div>
                                    <?php }
                                    } ?>
                                    <?php
                                    if (isset($product['product'][0]['video_type']) && !empty($product['product'][0]['video_type'])) {
                                        $total_images++;
                                    ?>
                                        <div class="swiper-slide">
                                            <figure class="rounded">
                                                <?php if ($product['product'][0]['video_type'] == 'self_hosted') { ?>
                                                    <video controls width="320" height="240" src="<?= $product['product'][0]['video'] ?>" controlsList="nodownload" disablePictureInPicture>
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
                                            </figure>
                                        </div>
                                    <?php } ?>
                                    <!--/.swiper-slide -->

                                </div>
                                <!--/.swiper-wrapper -->
                                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Mobile Product Image Slider -->
                <div class="product-preview-image-section-sm overflow-auto">
                    <div class="swiper swiper-container preview-image-swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide text-center"><img class="lazy" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('media/image?path=' . rawurlencode($product['product'][0]['relative_path']) . '&width=800&quality=80') ?>"></div>
                            <?php
                            if (!empty($product['product'][0]['other_images_relative_path']) && isset($product['product'][0]['other_images_relative_path'])) {
                                foreach ($product['product'][0]['other_images_relative_path'] as $other_image) {
                            ?>
                                    <div class="swiper-slide text-center">
                                        <img class="lazy" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('media/image?path=' . rawurlencode($other_image) . '&width=800&quality=80') ?>">
                                    </div>
                            <?php }
                            } ?>
                            <?php
                            $variant_relative_path = array_column($product['product'][0]['variants'], 'variant_relative_path');

                            if (!empty($variant_relative_path)) {
                                foreach ($variant_relative_path as $variant_images) {
                                    if (!empty($variant_images)) {
                                        foreach ($variant_images as $image) {
                            ?>
                                            <div class="swiper-slide text-center">
                                                <img class="lazy" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('media/image?path=' . rawurlencode($image) . '&width=800&quality=80') ?>" data-zoom-image="">
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
            </div>

            <div class="col-md-5">
                <div class="post-header mb-5">
                    <h3 class="post-title"><a href="" class="link-dark text-decoration-none"><?= ucfirst($product['product'][0]['name']) ?></a></h3>
                    <p class="mb-1"><?= output_escaping(str_replace('\r\n', '&#13;&#10;', $product['product'][0]['short_description'])) ?></p>
                    <?php
                    if (isset($statistics) && !empty($statistics) && $statistics['total_ordered'] > 0 && $statistics['total_in_cart'] > 0 && $statistics['total_favorites'] > 0) { ?>
                        <p class="my-1">
                            <span class="rotator-zoom text-blue">
                                <?php if ($statistics['total_ordered'] > 0) { ?>
                                    🛒<?php print_r($statistics['total_ordered']) ?> <?= !empty($this->lang->line('item(s)_sold_in_last_30_days')) ? str_replace('\\', '', $this->lang->line('item(s))_sold_in_last_30_days')) : 'item(s) sold in last 30 days' ?>
                                    <?php } ?>,
                                    <?php if ($statistics['total_in_cart'] > 0) { ?>
                                        🚀<?php print_r($statistics['total_in_cart']) ?> <?= !empty($this->lang->line('people_have_added_this_to_cart')) ? str_replace('\\', '', $this->lang->line('people_have_added_this_to_cart')) : 'people have added this to cart' ?>
                                    <?php } ?> ,
                                    <?php if ($statistics['total_favorites'] > 0) { ?>
                                        ❤️<?php print_r($statistics['total_favorites']) ?> <?= !empty($this->lang->line('people_have_added_to_wishlist')) ? str_replace('\\', '', $this->lang->line('people_have_added_to_wishlist')) : 'people have added to wishlist' ?>
                                    <?php } ?>
                            </span>
                        </p>
                    <?php } ?>
                    <hr class="my-3">

                    <?php
                    $indicator = (isset($product['product'][0]['indicator']) && !empty($product['product'][0]['indicator']) ? $product['product'][0]['indicator'] : '');
                    if ($indicator == '1') { ?>
                        <span class="badge badge-success"><?= !empty($this->lang->line('veg')) ? str_replace('\\', '', $this->lang->line('veg')) : 'Veg' ?></span>
                    <?php } elseif ($indicator == '2') { ?>
                        <span class="badge badge-danger"><?= !empty($this->lang->line('non_veg')) ? str_replace('\\', '', $this->lang->line('non_veg')) : 'Non Veg' ?></span>
                    <?php } ?>

                    <?php

                    if (($product['product'][0]['variants'][0]['special_price'] < $product['product'][0]['variants'][0]['price']) && ($product['product'][0]['variants'][0]['special_price'] != 0)) { ?>
                        <p class="my-1 price text-muted">
                            <span id="price" style='font-size: 18px;'>
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
                                        <?php $price = $product['product'][0]['variants'][0]['price'];
                                        echo format_price($price);
                                        ?>
                                    </s>
                                </span>
                            </sup>
                        </p>
                    <?php } else { ?>
                        <p class="mb-0 mt-2 price text-muted">
                            <span id="price" style='font-size: 18px;'>
                                <?php echo $settings['currency'] ?>
                                <?php
                                $price = $product['product'][0]['variants'][0]['price'];
                                echo format_price($price);
                                ?>
                            </span>
                        </p>
                    <?php } ?>

                    <div class="d-flex gap-1 pl-0 product-rating-small" dir="ltr">
                        <input id="input" name="rating" class="rating rating-loading d-none mt-n5" data-size="xs" value="<?= $product['product'][0]['rating'] ?>" data-show-clear="false" data-show-caption="false" readonly>
                        <span class="my-auto ml-0 text-muted"> ( <?= ($product['product'][0]['no_of_ratings'] > 0 ? $product['product'][0]['no_of_ratings'] : "No") ?> <?= !empty($this->lang->line('reviews')) ? str_replace('\\', '', $this->lang->line('reviews')) : 'reviews' ?> ) </span>
                    </div>

                    <?php
                    $attribute_order = explode(', ', $product['product'][0]['attribute_order']);
                    $color_code = $style = "";
                    $product['product'][0]['variant_attributes'] = isset($product['product'][0]['variant_attributes']) ? array_values($product['product'][0]['variant_attributes']) : '';

                    if (isset($product['product'][0]['variant_attributes']) && !empty($product['product'][0]['variant_attributes'])) { ?>
                        <?php
                        if (!empty($attribute_order[0])) {
                            for ($j = 0; $j < count($attribute_order); $j++) {
                                foreach ($product['product'][0]['variant_attributes'] as $attribute) {

                                    if ($attribute_order[$j] == $attribute['attr_name']) {
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
                                        <div class="btn-group btn-group-toggle gap-1 d-flex flex-wrap" data-toggle="buttons" id="<?= $attribute['attr_name'] ?>">
                                            <?php
                                            if (!empty($attribute_values[0])) {
                                                foreach ($attribute_values as $key => $row) {
                                                    if ($swatche_types[$key] == "1") {
                                                        echo '<style> .product-page-details .btn-group>.active { border: 1px solid black;}</style>';
                                                        $color_code = "style='background-color:" . $swatche_values[$key] . ";'";  ?>
                                                        <ul class="p-0 mb-0">
                                                            <li class="list-unstyled">
                                                                <label class="btn text-center fullCircle rounded-circle p-3" <?= $color_code ?>>
                                                                    <input type="radio" name="<?= $attribute['attr_name'] ?>" value="<?= $attribute_ids[$key] ?>" autocomplete="off" class="attributes filter-input">
                                                                </label>
                                                            </li>

                                                        </ul>
                                                    <?php } else if ($swatche_types[$key] == "2") { ?>
                                                        <?= $style ?>
                                                        <ul class="p-0 mb-0">
                                                            <li class="list-unstyled">
                                                                <label class="btn text-center ">
                                                                    <img class="swatche-image lazy category-image-container" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= $swatche_values[$key] ?>">
                                                                    <input type="radio" name="<?= $attribute['attr_name'] ?>" value="<?= $attribute_ids[$key] ?>" autocomplete="off" class="attributes">
                                                                    <br>
                                                                </label>
                                                            </li>
                                                        </ul>

                                                    <?php } else { ?>
                                                        <?= '<style> .product-page-details .btn-group>.active { background-color: var(--primary-color);color: white!important;}</style>'; ?>
                                                        <ul class="p-0 mb-0">
                                                            <li class="list-unstyled">
                                                                <label class="btn btn-aqua btn-default btn-xs mb-0 rounded-2 text-center w-11">
                                                                    <?= $row ?>
                                                                    <input type="radio" name="<?= $attribute['attr_name'] ?>" value="<?= $attribute_ids[$key] ?>" autocomplete="off" class="attributes">
                                                                    <br>
                                                                </label>
                                                            </li>
                                                        </ul>
                                                    <?php } ?>
                                            <?php }
                                            } ?>
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
                                <div class="btn-group btn-group-toggle gap-1 d-flex flex-wrap" data-toggle="buttons" id="<?= $attribute['attr_name'] ?>">
                                    <?php
                                    if (!empty($attribute_values[0])) {
                                        foreach ($attribute_values as $key => $row) {
                                            if ($swatche_types[$key] == "1") {
                                                echo '<style> .product-page-details .btn-group>.active { border: 1px solid black;}</style>';
                                                $color_code = "style='background-color:" . $swatche_values[$key] . ";'";  ?>
                                                <ul class="p-0 mb-0">
                                                    <li class="list-unstyled">
                                                        <label class="btn text-center fullCircle rounded-circle p-3" <?= $color_code ?>>
                                                            <input type="radio" name="<?= $attribute['attr_name'] ?>" value="<?= $attribute_ids[$key] ?>" autocomplete="off" class="attributes filter-input">
                                                        </label>
                                                    </li>

                                                </ul>
                                            <?php } else if ($swatche_types[$key] == "2") { ?>
                                                <?= $style ?>
                                                <ul class="p-0 mb-0">
                                                    <li class="list-unstyled">
                                                        <label class="btn text-center ">
                                                            <img class="swatche-image lazy category-image-container" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= $swatche_values[$key] ?>">
                                                            <input type="radio" name="<?= $attribute['attr_name'] ?>" value="<?= $attribute_ids[$key] ?>" autocomplete="off" class="attributes">
                                                            <br>
                                                        </label>
                                                    </li>
                                                </ul>

                                            <?php } else { ?>
                                                <?= '<style> .product-page-details .btn-group>.active { background-color: var(--primary-color);color: white!important;}</style>'; ?>
                                                <ul class="p-0 mb-0">
                                                    <li class="list-unstyled">
                                                        <label class="btn btn-aqua btn-default btn-xs mb-0 rounded-2 text-center w-11">
                                                            <?= $row ?>
                                                            <input type="radio" name="<?= $attribute['attr_name'] ?>" value="<?= $attribute_ids[$key] ?>" autocomplete="off" class="attributes">
                                                            <br>
                                                        </label>
                                                    </li>
                                                </ul>
                                            <?php } ?>
                                    <?php }
                                    } ?>
                                </div>
                            <?php
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

                    <div class="num-block skin-2 py-4 mt-2">
                        <div class="num-in form-control d-flex align-items-center">
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
                    <?php
                    if ($product['product'][0]['type'] != 'digital_product') { ?>
                        <?php
                        $shiprocket_settings = get_settings('shipping_method', true);
                        if ((isset($shiprocket_settings['pincode_wise_deliverability']) && $shiprocket_settings['pincode_wise_deliverability'] == 1) || (isset($shiprocket_settings['local_shipping_method']) && isset($shiprocket_settings['shiprocket_shipping_method']) && $shiprocket_settings['local_shipping_method'] == 1 && $shiprocket_settings['shiprocket_shipping_method'] == 1)) {
                        ?>
                            <form class="mt-2" id="validate-zipcode-form" method="POST">
                                <div class="input-group">
                                    <input type="hidden" name="product_id" value="<?= $product['product'][0]['id'] ?>">
                                    <input type="text" class="form-control" id="zipcode" placeholder="Zipcode" name="zipcode" autocomplete="off" required value="<?= $product['product'][0]['zipcode']; ?>">
                                    <button type="submit" class="btn btn-primary btn-sm ml-0" id="validate_zipcode" <?= ($this->ion_auth->logged_in()) ? '' : 'disabled' ?>><?= !empty($this->lang->line('check_availability')) ? str_replace('\\', '', $this->lang->line('check_availability')) : 'Check Availability' ?></button>
                                </div>
                                <div class="mt-2" id="error_box">
                                    <?php if (!empty($product['product'][0]['zipcode'])) { ?>
                                        <p class="m-0 text-<?= ($product['product'][0]['is_deliverable']) ? "success" : "danger" ?>"><?= !empty($this->lang->line('product_is')) ? str_replace('\\', '', $this->lang->line('product_is')) : 'Product is' ?> <?= ($product['product'][0]['is_deliverable']) ? "" : "not" ?> <?= !empty($this->lang->line('delivarable_on')) ? str_replace('\\', '', $this->lang->line('delivarable_on')) : 'Delivarable on' ?> &quot; <?= $product['product'][0]['zipcode']; ?> &quot; </p>
                                    <?php } ?>
                                </div>
                            </form>
                        <?php }
                        if (isset($shiprocket_settings['city_wise_deliverability']) && $shiprocket_settings['city_wise_deliverability'] == 1 && $shiprocket_settings['shiprocket_shipping_method'] != 1) { ?>
                            <form class="mt-2" id="validate-city-form" method="POST">
                                <div class="input-group">
                                    <input type="hidden" name="product_id" value="<?= $product['product'][0]['id'] ?>">
                                    <input type="text" class="form-control rounded" id="zipcode" placeholder="City" name="city" autocomplete="off" required value="<?= $product['product'][0]['zipcode']; ?>">
                                    <button type="submit" class="btn btn-primary btn-sm ml-0" id="validate_city" <?= ($this->ion_auth->logged_in()) ? '' : 'disabled' ?>><?= !empty($this->lang->line('check_availability')) ? str_replace('\\', '', $this->lang->line('check_availability')) : 'Check Availability' ?></button>
                                </div>
                                <div class="mt-2" id="error_box">
                                    <?php if (!empty($product['product'][0]['zipcode'])) { ?>
                                        <p class="m-0 text-<?= ($product['product'][0]['is_deliverable']) ? "success" : "danger" ?>"><?= !empty($this->lang->line('product_is')) ? str_replace('\\', '', $this->lang->line('product_is')) : 'Product is' ?> <?= ($product['product'][0]['is_deliverable']) ? "" : "not" ?> <?= !empty($this->lang->line('delivarable_on')) ? str_replace('\\', '', $this->lang->line('delivarable_on')) : 'Delivarable on' ?> &quot; <?= $product['product'][0]['zipcode']; ?> &quot; </p>
                                    <?php } ?>
                                </div>
                            </form>
                        <?php } ?>
                    <?php } ?>

                    <div class="col-lg-10 d-flex flex-row pt-2 gap-1 product-page-div p-0">
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
                            <a class="btn btn-yellow btn-icon btn-xs btn-icon-start rounded-pill" href="<?= base_url('cart') ?>"><i class='uil uil-arrow-right'></i> <?= !empty($this->lang->line('go_to_cart')) ? str_replace('\\', '', $this->lang->line('go_to_cart')) : 'Go To Cart' ?></a>
                        <?php } else { ?>
                            <button type="button" name="add_cart" class="add_to_cart btn btn-xs btn-yellow rounded-pill " id="add_cart"
                                title="<?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add to Cart' ?>" data-product-id="<?= $product['product'][0]['id'] ?>"
                                data-product-title="<?= $product['product'][0]['name'] ?>" data-product-slug="<?= $product['product'][0]['slug'] ?>"
                                data-product-reference_id="<?= (isset($reference_id) && !empty($reference_id)) ? $reference_id : '' ?>"
                                data-product-image="<?= $product['product'][0]['image'] ?>" data-product-price="<?= ($variant['special_price'] > 0 && $variant['special_price'] != '0' && $variant['special_price'] != '') ? $variant['special_price'] : $variant['price']; ?>"
                                data-product-description="<?= short_description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', strip_tags($product['product'][0]['short_description'])))); ?>"
                                data-step="<?= (isset($product['product'][0]['minimum_order_quantity']) && !empty($product['product'][0]['quantity_step_size'])) ? $product['product'][0]['quantity_step_size'] : 1 ?>"
                                data-min="<?= (isset($product['product'][0]['minimum_order_quantity']) && !empty($product['product'][0]['minimum_order_quantity'])) ? $product['product'][0]['minimum_order_quantity'] : 1 ?>"
                                data-max="<?= (isset($product['product'][0]['total_allowed_quantity']) && !empty($product['product'][0]['total_allowed_quantity'])) ? $product['product'][0]['total_allowed_quantity'] : '' ?>"
                                data-product-variant-id="<?= $variant_id ?>" data-product-stock="<?= $product_stock ?>">
                                <i class="uil uil-shopping-bag mr-2"></i> <?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add to Cart' ?>
                            </button>
                        <?php } ?>
                        <button type="button" name="buy_now" class="buy_now btn btn-xs btn-danger rounded-pill" id="buy_now"
                            title="<?= !empty($this->lang->line('buy_now')) ? str_replace('\\', '', $this->lang->line('buy_now')) : 'Buy Now' ?>" data-product-id="<?= $product['product'][0]['id'] ?>"
                            data-product-title="<?= $product['product'][0]['name'] ?>" data-product-slug="<?= $product['product'][0]['slug'] ?>"
                            data-product-reference_id="<?= (isset($reference_id) && !empty($reference_id)) ? $reference_id : '' ?>"
                            data-product-image="<?= $product['product'][0]['image'] ?>" data-product-price="<?= ($variant['special_price'] > 0 && $variant['special_price'] != '0' && $variant['special_price'] != '') ? $variant['special_price'] : $variant['price']; ?>"
                            data-product-description="<?= short_description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', strip_tags($product['product'][0]['short_description'])))); ?>"
                            data-step="<?= (isset($product['product'][0]['minimum_order_quantity']) && !empty($product['product'][0]['quantity_step_size'])) ? $product['product'][0]['quantity_step_size'] : 1 ?>"
                            data-min="<?= (isset($product['product'][0]['minimum_order_quantity']) && !empty($product['product'][0]['minimum_order_quantity'])) ? $product['product'][0]['minimum_order_quantity'] : 1 ?>"
                            data-max="<?= (isset($product['product'][0]['total_allowed_quantity']) && !empty($product['product'][0]['total_allowed_quantity'])) ? $product['product'][0]['total_allowed_quantity'] : 1 ?>"
                            data-product-variant-id="<?= $variant_id ?>"
                            <?= ($this->ion_auth->logged_in()) ? '' : 'disabled' ?>>
                            <i class="mr-2 uil uil-bolt-alt"></i><?= !empty($this->lang->line('buy_now')) ? str_replace('\\', '', $this->lang->line('buy_now')) : 'Buy Now' ?>
                        </button>
                        <button type="button" name="compare" class="btn btn-outline-blue btn-icon btn-sm rounded-pill w-15 p-0 compare" id="compare" title="<?= !empty($this->lang->line('compare')) ? str_replace('\\', '', $this->lang->line('compare')) : 'Compare' ?>" data-product-id="<?= $product['product'][0]['id'] ?>" data-product-variant-id="<?= $variant_id ?>">
                            <i class="uil uil-exchange-alt fs-20"></i>
                        </button>

                        <button class="item-like text-decoration-none btn-outline-red btn-sm rounded-pill w-15 p-0 fs-19 fav_button_dif add-to-fav-btn <?= ($product['product'][0]['is_favorite'] == 1) ? 'fa fa-heart text-danger' : 'fa fa-heart-o' ?>" href="#" title="<?= !empty($this->lang->line('add_to_wishlist')) ? str_replace('\\', '', $this->lang->line('add_to_wishlist')) : 'Add To wishlist' ?>" data-product-id="<?= $product['product'][0]['id'] ?>">
                            <i class=""></i>
                        </button>

                        <?php
                        if (isset($settings['whatsapp_status']) && $settings['whatsapp_status'] == 1 && isset($settings['whatsapp_number'])) { ?>
                            <a href="https://api.whatsapp.com/send?phone= <?= ($settings['whatsapp_number'] != '' && isset($settings['whatsapp_number'])) ? $settings['whatsapp_number'] : ((!defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0)  ? str_repeat("X", strlen($settings['whatsapp_number']) - 3) . substr($settings['whatsapp_number'], -3) : $settings['whatsapp_number'])   ?> &amp;text=Hello, I Seen This <?= $product['product'][0]['name'] ?> In Your Website And I Want to Buy This <?= base_url('products/details/' . $product['product'][0]['slug']) ?>" target="_blank" title="Order From Whatsapp" class=" btn btn-success  btn-outline-blue btn-icon btn-sm rounded-pill w-15 p-0">
                                <i class="fs-20 uil uil-whatsapp"></i>
                            </a>
                        <?php } ?>
                    </div>
                    <hr class="my-3">

                    <div class="align-items-center d-flex gap-2">
                        <i class="fs-20 uil uil-shop"></i>
                        <?php if (isset($product['product'][0]['seller_name']) && !empty($product['product'][0]['seller_name'])) { ?>
                            <a target="_BLANK" class="text-danger" href="<?= base_url('products?seller=' . $seller_slug[0]['slug']) ?>">
                                <?= $product['product'][0]['seller_name'] ?>
                            </a>
                            </span>
                        <?php } ?>
                    </div>

                    <?php if ($this->ion_auth->logged_in()) { ?>
                        <div class=""><?= !empty($this->lang->line('seller')) ? str_replace('\\', '', $this->lang->line('chat_with')) : 'Chat With' ?>
                            <?php if (isset($product['product'][0]['seller_name']) && !empty($product['product'][0]['seller_name'])) { ?>
                                <a id="chat-with-button" class="text-success" data-id="<?= $product['product'][0]['seller_id'] ?>" href="#">
                                    <i class="uil uil-comments"></i>
                                    <?= $product['product'][0]['seller_name'] ?></a>
                                </span>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?php if (isset($product['product'][0]['tags']) && !empty($product['product'][0]['tags'])) { ?>
                        <div class="d-flex gap-2">
                            <div>
                                <i class="fs-20 uil uil-tag-alt" title="tags"></i>
                            </div>
                            <div>
                                <?php foreach ($product['product'][0]['tags'] as $tag) { ?>
                                    <a href="<?= base_url('products/tags/' . $tag) ?>" class="text-decoration-none">
                                        <span class="badge badge-secondary p-1"><?= $tag ?></span>
                                    </a>
                                <?php } ?>

                            </div>
                        </div>
                    <?php } ?>
                    <hr class="my-3">

                    <div class="mt-3 d-flex product-permission-feature justify-content-evenly overflow-auto text-center">
                        <?php if (isset($product['product'][0]['cod_allowed']) && !empty($product['product'][0]['cod_allowed']) && $product['product'][0]['cod_allowed'] == 1 && $payment_methods['cod_method'] == 1) {  ?>
                            <div>
                                <div class="product-permission">
                                    <img class="h-5 lazy" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('assets/front_end/classic/images/cod_logo.png') ?>">
                                </div>
                                <div class="fs-13 product-permission-text">
                                    <?= !empty($this->lang->line('cod')) ? str_replace('\\', '', $this->lang->line('cod')) : 'COD' ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div>
                            <?php if (isset($product['product'][0]['is_cancelable']) && !empty($product['product'][0]['is_cancelable']) && $product['product'][0]['is_cancelable'] == 1) {  ?>
                                <div class="product-permission" class="ml-4">
                                    <img class="h-5 lazy" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('assets/front_end/classic/images/cancelable.png') ?>">
                                </div>
                                <div class="fs-13 product-permission-text">
                                    <?= !empty($this->lang->line('cancelable_till')) ? str_replace('\\', '', $this->lang->line('cancelable_till')) : 'Cancelable till' ?> <?= ' ' . $product['product'][0]['cancelable_till'] ?>
                                </div>
                            <?php } else { ?>
                                <div class="product-permission" class="ml-4">
                                    <img class="h-5 lazy" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('assets/front_end/classic/images/notcancelable.png') ?>">
                                </div>
                                <div class="fs-13 product-permission-text">
                                    <?= !empty($this->lang->line('no_cancellation')) ? str_replace('\\', '', $this->lang->line('no_cancellation')) : 'No Cancellation' ?>
                                </div>
                            <?php  } ?>
                        </div>
                        <div>
                            <?php if (isset($product['product'][0]['is_returnable']) && !empty($product['product'][0]['is_returnable']) && $product['product'][0]['is_returnable'] == 1) {  ?>
                                <div class="product-permission" class="ml-4">
                                    <img class="h-5 lazy" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('assets/front_end/classic/images/returnable.png') ?>">
                                </div>
                                <div class="fs-13 product-permission-text">
                                    <?= $settings['max_product_return_days'] ?> <?= !empty($this->lang->line('days_returnable')) ? str_replace('\\', '', $this->lang->line('days_returnable')) : 'Days Returnable' ?>
                                </div>
                            <?php } else { ?>
                                <div class="product-permission" class="ml-4">
                                    <img class="h-5 lazy" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('assets/front_end/classic/images/notreturnable.png') ?>">
                                </div>
                                <div class="fs-13 product-permission-text">
                                    <?= !empty($this->lang->line('no_returnable')) ? str_replace('\\', '', $this->lang->line('no_returnable')) : 'No Returnable' ?>
                                </div>
                            <?php  } ?>
                        </div>
                        <?php if (isset($product['product'][0]['guarantee_period']) && !empty($product['product'][0]['guarantee_period'])) {  ?>
                            <div>
                                <div class="product-permission" class="ml-4">
                                    <img class="h-5 lazy" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('assets/front_end/classic/images/guarantee.png') ?>">
                                </div>
                                <div class="fs-13 product-permission-text">
                                    <?= $product['product'][0]['guarantee_period'] ?> <?= !empty($this->lang->line('guarantee')) ? str_replace('\\', '', $this->lang->line('guarantee')) : 'Guarantee' ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if (isset($product['product'][0]['warranty_period']) && !empty($product['product'][0]['warranty_period'])) {  ?>
                            <div>
                                <div class="product-permission" class="ml-4">
                                    <img class="h-5 lazy" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('assets/front_end/classic/images/warranty.png') ?>">
                                </div>
                                <div class="fs-13 product-permission-text">
                                    <?= $product['product'][0]['warranty_period'] ?> <?= !empty($this->lang->line('warranty')) ? str_replace('\\', '', $this->lang->line('warranty')) : 'Warranty' ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <?php if (isset($product['product'][0]['cod_allowed']) && !empty($product['product'][0]['cod_allowed']) && $product['product'][0]['cod_allowed'] == 1 && $payment_methods['cod_method'] == 1) { ?>
                        <span class="fs-14 fw-bolder text-info">Cash on Delivery is eligible for orders above <?= $settings['currency'] . $payment_methods['min_cod_amount'] ?> and below <?= $settings['currency'] . $payment_methods['max_cod_amount'] ?></span>
                    <?php } ?>

                    <div class="mt-3">
                        <table class="product-detail-tab">
                            <?php
                            if (isset($product['product'][0]['attributes']) && !empty($product['product'][0]['attributes']) && $product['product'][0]['attributes'] != [] && $product['product'][0]['attr_value_ids'] != null) { ?>
                                <?php foreach ($product['product'][0]['attributes'] as $attrbute) { ?>
                                    <tr>
                                        <td><?= ucfirst($attrbute['name']) ?> :</td>
                                        <td><?= $attrbute['value'] ?></td>
                                    </tr>
                                <?php }
                            }
                            if (isset($product['product'][0]['made_in']) && !empty($product['product'][0]['made_in']) && $product['product'][0]['made_in'] != '') { ?>
                                <tr>
                                    <td><?= !empty($this->lang->line('made_in')) ? str_replace('\\', '', $this->lang->line('made_in')) : 'Made In : ' ?></td>
                                    <td><?= ucfirst($product['product'][0]['made_in']) ?></td>
                                </tr>
                                <?php }
                            if (isset($product['product'][0]['brand']) && !empty($product['product'][0]['brand']) && $product['product'][0]['brand'] != '') {

                                $brand_img = fetch_details('brands', ['name' => $product['product'][0]['brand']]);
                                if (isset($brand_img) && !empty($brand_img)) {

                                ?>
                                    <tr>
                                        <td><?= !empty($this->lang->line('brand')) ? str_replace('\\', '', $this->lang->line('brand')) : 'Brand : ' ?></td>
                                        <td>
                                            <a href="<?= base_url('products?brand=' . html_escape($brand_img[0]['slug'])) ?>" class="text-decoration-none">
                                                <img src="<?= base_url($brand_img[0]['image']) ?>" class="h-6">
                                                <?= ucfirst($product['product'][0]['brand']) ?>
                                            </a>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>
                        </table>
                    </div>

                    <div class="mt-5" id="share"></div>
                </div>
            </div>
        </div>

    </div>
    <div class="container mt-n6">
        <div class="row mt-4">

            <div class="nav" id="product-tab" role="tablist">
                <nav class="w-100">
                    <ul class="nav nav-tabs nav-tabs-basic">
                        <li class="nav-item">
                            <?php if (isset($product['product'][0]['description']) && !empty($product['product'][0]['description'])) { ?>
                                <a class="nav-item nav-link product-nav-tab active fs-15" id="product-desc-tab" data-toggle="tab" href="#product-desc" role="tab" aria-controls="product-desc" aria-selected="true"><?= !empty($this->lang->line('description')) ? str_replace('\\', '', $this->lang->line('description')) : 'Description' ?></a>
                            <?php } ?>
                        </li>
                        <li class="nav-item">
                            <?php if (isset($product['product'][0]['description']) && !empty($product['product'][0]['description'])) { ?>

                                <a class="nav-item nav-link product-nav-tab fs-15" id="product-review-tab" data-toggle="tab" href="#product-review" role="tab" aria-controls="product-review" aria-selected="false"><?= !empty($this->lang->line('reviews')) ? str_replace('\\', '', $this->lang->line('reviews')) : 'Reviews' ?></a>
                            <?php } else { ?>

                                <a class="nav-item nav-link product-nav-tab  active fs-15" id="product-review-tab" data-toggle="tab" href="#product-review" role="tab" aria-controls="product-review" aria-selected="false"><?= !empty($this->lang->line('reviews')) ? str_replace('\\', '', $this->lang->line('reviews')) : 'Reviews' ?></a>

                            <?php } ?>

                        </li>
                        <li class="nav-item">
                            <a class="nav-item nav-link product-nav-tab fs-15 <?= !isset($product['product'][0]['description']) && empty($product['product'][0]['description']) ? 'active' : '' ?>" id="product-seller-tab" data-toggle="tab" href="#product-seller" role="tab" aria-controls="product-seller" aria-selected="false"><?= !empty($this->lang->line('sold_by')) ? str_replace('\\', '', $this->lang->line('sold_by')) : 'Sold by' ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-item nav-link product-nav-tab fs-15" id="product-faq-tab" data-toggle="tab" href="#product-faq" role="tab" aria-controls="product-faq" aria-selected="true"><?= !empty($this->lang->line('faq')) ? str_replace('\\', '', $this->lang->line('faq')) : 'FAQ' ?></a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="tab-content p-3 w-100" id="nav-tabContent">
                <div class="tab-pane fade active show" id="product-desc" role="tabpanel" aria-labelledby="product-desc-tab">
                    <div class="row">
                        <div class="col-12 description overflow-auto description_img">
                            <?= (isset($product['product'][0]['description']) && !empty($product['product'][0]['description'])) ? $product['product'][0]['description'] : ""  ?>
                        </div>
                    </div>
                </div>
                <!-- product faq tab -->
                <div class="tab-pane fade" id="product-faq" role="tabpanel" aria-labelledby="product-faq-tab">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="accordion accordion-wrapper" id="accordionSimpleExample">

                                    <?php if ((!isset($faq['data']) && empty($faq['data'])) || $faq['data'] == []) { ?>
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="d-flex flex-column">
                                                <img class="" src="<?= base_url('assets/front_end/modern/img/no-faq.jpg') ?>" alt="No Faq" width="160px" />
                                                <h4><?= !empty($this->lang->line('no_faqs_found')) ? str_replace('\\', '', $this->lang->line('no_faqs_found')) : 'No FAQs Found' ?></h4>
                                            </div>
                                            <div>
                                                <?php if ($this->ion_auth->logged_in()) { ?>
                                                    <div class=" add-faqs-form float-right">
                                                        <button class="btn btn-outline-primary btn-xs mt-2 rounded-pill" type="submit" data-toggle="modal" data-target="#add-faqs-form"><i class="uil uil-plus" aria-hidden="true"></i> &nbsp;Add your question here</button>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <?php foreach ($faq['data'] as $row) {
                                        ?>
                                            <?php if (isset($row['answer']) && !empty($row['answer']) && ($row['answer'] != '')) {
                                            ?>
                                                <div class="card plain accordion-item">
                                                    <div class="card-header" id="<?= "h-" . $row['id'] ?>">
                                                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#<?= "c-" . $row['id'] ?>" aria-expanded="true" aria-controls="collapseSimpleOne">
                                                            <?= html_escape($row['question']) ?>
                                                        </button>
                                                    </div>
                                                    <!--/.card-header -->
                                                    <?php $product_data = fetch_details('users', ['id' => $row['answered_by']], 'username'); ?>
                                                    <div id="<?= "c-" . $row['id'] ?>" class="accordion-collapse collapse" aria-labelledby="<?= "h-" . $row['id'] ?>" data-bs-parent="#accordionSimpleExample">
                                                        <div class="card-body">
                                                            <p class="mb-1"><?= html_escape($row['answer']) ?></p>
                                                            <p class="text-dark"><?= !empty($this->lang->line('answer_by')) ? str_replace('\\', '', $this->lang->line('answer_by')) : 'Answer by' ?> : <?= isset($product_data[0]['username']) && !empty($product_data[0]['username']) ? html_escape($product_data[0]['username']) : "" ?></p>
                                                        </div>
                                                        <!--/.card-body -->
                                                    </div>
                                                    <!--/.accordion-collapse -->
                                                </div>
                                            <?php } ?>
                                    <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- product review tab -->
                <div class="tab-pane fade <?= (isset($product['product'][0]['description']) && !empty($product['product'][0]['description'])) ? "" : "active show"  ?>" id="product-review" role="tabpanel" aria-labelledby="product-review-tab">
                    <?php
                    if (!empty($review_images['total_images'])) {
                        if ($review_images['total_images'] > 0) { ?>
                            <h3 class="review-title"> User Review Images (<span><?= $review_images['total_images'] ?></span>)</h3>
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
                                                <div class="mt-4">
                                                    <a href="<?= $review_images['product_rating'][$i]['images'][$j];  ?>" class="text-decoration-none" data-izimodal-open="#user-review-images" id="review-image-title" data-reached-end="false" data-izimodal-open="#user-review-images" data-review-limit="1" data-review-offset="0" data-product-id="<?= $review_images['product_rating'][$i]['product_id'] ?>" data-review-title="User Review Images(<span><?= $review_images['total_images'] ?></span>)">
                                                        <p class="limit_position text-blue">See all customer images</p>
                                                    </a>
                                                </div>
                                            <?php } else if (!empty($review_images['product_rating'][$i]['images'][$j])) {
                                            ?>

                                                <div class="col-sm-1">
                                                    <div class="review-img">

                                                        <a href="<?= $review_images['product_rating'][$i]['images'][$j];  ?>" data-lightbox="users-review-images" data-title="<?= "<button class='label btn-success'>" . $review_images['product_rating'][$i]['rating'] . " <i class='fa fa-star'></i></button></br>" . $review_images['product_rating'][$i]['user_name'] . "<br>" . $review_images['product_rating'][$i]['comment'] ?> ">
                                                            <img class="lazy" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= $review_images['product_rating'][$i]['images'][$j];  ?>" alt="Review Images">
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
                    <hr class="mb-1 mt-1">
                    <div class="row mt-10">
                        <aside class="col-lg-4 sidebar">

                            <?php if ($product['product'][0]['is_purchased'] == 1 && !empty($my_rating)) {
                                $form_link = (!empty($my_rating)) ? base_url('products/save-rating') : base_url('products/save-rating');  ?>
                                <div id="rating-box" class="">
                                    <div class="add-review p-3 bg-soft-primary">
                                        <h3 class="text-center mb-4"><?= !empty($this->lang->line('edit_your_review')) ? str_replace('\\', '', $this->lang->line('edit_your_review')) : 'Edit Your Review' ?></h3>
                                        <form action="<?= $form_link ?>" id="product-rating-form" method="POST">
                                            <?php if (!empty($my_rating)) { ?>
                                                <input type="hidden" name="rating_id" value="<?= $my_rating['product_rating'][0]['id'] ?>">
                                            <?php } ?>
                                            <input type="hidden" name="csrf_token" value="$csrf_token()" />

                                            <input type="hidden" name="product_id" value="<?= $product['product'][0]['id'] ?>">

                                            <label for="rating" class="fs-17"><?= !empty($this->lang->line('your_rating')) ? str_replace('\\', '', $this->lang->line('your_rating')) : 'Your rating' ?></label>
                                            <div class="pl-0 product-rating-small rating-form mb-2 mt-n2" dir="ltr">
                                                <input id="input" name="rating" class="rating rating-loading d-none mt-n5" data-size="xs" value="<?= isset($my_rating['product_rating'][0]['rating']) ? $my_rating['product_rating'][0]['rating'] : '0' ?>" data-show-clear="false" data-show-caption="false" data-step="1">
                                            </div>
                                            <div class="form-group fs-17">
                                                <label for="exampleFormControlTextarea1"><?= !empty($this->lang->line('your_review')) ? str_replace('\\', '', $this->lang->line('your_review')) : 'Your Review' ?></label>
                                                <textarea class="form-control" name="comment" rows="3"><?= isset($my_rating['product_rating'][0]['comment']) ? $my_rating['product_rating'][0]['comment'] : '' ?></textarea>
                                            </div>
                                            <div class="form-group fs-17">
                                                <label for="exampleFormControlTextarea1"><?= !empty($this->lang->line('images')) ? str_replace('\\', '', $this->lang->line('images')) : 'Images' ?></label>
                                                <input type="file" name="images[]" accept="image/x-png,image/gif,image/jpeg" multiple />
                                            </div>
                                            <button class="btn btn-primary rounded-pill w-100" id="rating-submit-btn"><?= !empty($this->lang->line('submit')) ? str_replace('\\', '', $this->lang->line('submit')) : 'Submit' ?></button>
                                        </form>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($product['product'][0]['is_purchased'] == 1) {
                                $form_link = (!empty($my_rating)) ? base_url('products/edit-rating') : base_url('products/save-rating');
                            ?>
                                <div class=" p-3 bg-soft-primary <?= (!empty($my_rating)) ? 'd-none' : '' ?>" id="rating-box">
                                    <div class="add-review">
                                        <h3 class="review-title"><?= !empty($this->lang->line('add_your_review')) ? str_replace('\\', '', $this->lang->line('add_your_review')) : 'Add Your Review' ?></h3>
                                        <form action="<?= $form_link ?>" id="product-rating-form" method="POST">
                                            <?php if (!empty($my_rating)) { ?>
                                                <input type="hidden" name="rating_id" value="<?= $my_rating['product_rating'][0]['id'] ?>">
                                            <?php } ?>
                                            <input type="hidden" name="product_id" value="<?= $product['product'][0]['id'] ?>">
                                            <label for="rating" class="fs-17"><?= !empty($this->lang->line('your_rating')) ? str_replace('\\', '', $this->lang->line('your_rating')) : 'Your rating' ?></label>
                                            <div class="col-md-12 pl-0 product-rating-small rating-form fs-17 mt-n2 mb-2" dir="ltr">
                                                <input id="input" name="rating" class="rating rating-loading d-none mt-n5" data-size="xs" value="<?= isset($my_rating['product_rating'][0]['rating']) ? $my_rating['product_rating'][0]['rating'] : '0' ?>" data-show-clear="false" data-show-caption="false" data-step="1">
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleFormControlTextarea1"><?= !empty($this->lang->line('your_review')) ? str_replace('\\', '', $this->lang->line('your_review')) : 'Your Review' ?></label>
                                                <textarea class="form-control" name="comment" rows="3"><?= isset($my_rating['product_rating'][0]['comment']) ? $my_rating['product_rating'][0]['comment'] : '' ?></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleFormControlTextarea1"><?= !empty($this->lang->line('images')) ? str_replace('\\', '', $this->lang->line('images')) : 'Images' ?></label>
                                                <input type="file" name="images[]" accept="image/x-png,image/gif,image/jpeg" multiple />
                                            </div>
                                            <button class="btn btn-primary rounded-pill w-100" id="rating-submit-btn"><?= !empty($this->lang->line('submit')) ? str_replace('\\', '', $this->lang->line('submit')) : 'Submit' ?></button>
                                        </form>
                                    </div>
                                </div>
                            <?php } ?>
                        </aside>
                        <!-- /column .sidebar -->
                        <div class="col-md-12 order-md-2 <?= (isset($user->id) == 1) ? "col-lg-7" : "col-lg-12" ?>">

                            <!--/.row -->
                            <div id="comments">
                                <h3 class="review-title mb-2"> <span id="no_ratings"><?= $product['product'][0]['no_of_ratings'] ?></span> <?= !empty($this->lang->line('reviews_for_this_product')) ? str_replace('\\', '', $this->lang->line('reviews_for_this_product')) : 'Reviews For this Product' ?></h3>
                                <ol id="singlecomments" class="commentlist">
                                    <?php if (isset($my_rating) && !empty($my_rating)) {
                                        foreach ($my_rating['product_rating'] as $row) { ?>
                                            <li class="comment mb-5">
                                                <div class="comment-header d-md-flex align-items-center">
                                                    <figure class="user-avatar">
                                                        <img class="rounded-circle h-9 w-9 lazy" alt="" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= THEME_ASSETS_URL . 'img/user.png' ?>" />
                                                    </figure>
                                                    <div>
                                                        <h6 class="comment-author"><a href="#" class="link-dark"><?= $row['user_name'] ?></a></h6>
                                                        <ul class="post-meta">
                                                            <li>
                                                                <span class="review-date text-muted">
                                                                    <?php $date = strtotime($row['data_added']); ?>
                                                                    <i class="uil uil-calendar-alt"></i>&nbsp;<span><?= date("d-M-Y", $date) ?>
                                                                    </span>
                                                            </li>
                                                        </ul>
                                                        <!-- /.post-meta -->
                                                    </div>
                                                    <!-- /div -->
                                                </div>
                                                <!-- /.comment-header -->
                                                <div class="d-flex flex-row align-items-center mt-n3">
                                                    <input id="input" name="rating" class="rating rating-loading d-none" data-size="xs" value="<?= $row['rating'] ?>" data-show-clear="false" data-show-caption="false" readonly>
                                                </div>
                                                <p class="mb-2"><?= $row['comment'] ?></p>
                                                <div class="float-end">
                                                    <a id="delete_rating" href="<?= base_url('products/delete-rating') ?>" class="text-decoration-none text-danger" data-rating-id="<?= $row['id'] ?>">
                                                        <i class="uil uil-trash-alt fs-22"></i></a>
                                                </div>
                                                <div class="row reviews">
                                                    <?php foreach ($row['images'] as $image) { ?>
                                                        <div class="col-md-2">
                                                            <div class="review-img">

                                                                <a href="<?= $image; ?>" data-lightbox="review-images">
                                                                    <img class="lazy" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= $image; ?>" alt="Review Image">
                                                                </a>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </li>
                                    <?php }
                                    } ?>
                                    <?php if (isset($product_ratings) && !empty($product_ratings)) {
                                        $user_id = (isset($user->id)) ? $user->id : '';
                                        foreach ($product_ratings['product_rating'] as $row) {
                                            if ($row['user_id'] != $user_id) { ?>
                                                <li class="comment mb-5">
                                                    <div class="comment-header d-md-flex align-items-center">
                                                        <figure class="user-avatar">
                                                            <img class="rounded-circle h-11 w-11 lazy" alt="" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= THEME_ASSETS_URL . 'img/user.png' ?>" />
                                                        </figure>
                                                        <div>
                                                            <h6 class="comment-author"><a href="#" class="link-dark"><?= $row['user_name'] ?></a></h6>
                                                            <ul class="post-meta">
                                                                <li>
                                                                    <span class="review-date text-muted">
                                                                        <?php $date = strtotime($row['data_added']); ?>
                                                                        <i class="uil uil-calendar-alt"></i>&nbsp;<span><?= date("d-M-Y", $date) ?>
                                                                        </span>
                                                                </li>
                                                            </ul>
                                                            <!-- /.post-meta -->
                                                        </div>
                                                        <!-- /div -->
                                                    </div>
                                                    <!-- /.comment-header -->
                                                    <div class="d-flex flex-row align-items-center mb-2 ">
                                                        <input id="input" name="rating" class="rating rating-loading d-none" data-size="xs" value="<?= $row['rating'] ?>" data-show-clear="false" data-show-caption="false" readonly>
                                                    </div>
                                                    <p><?= $row['comment'] ?></p>
                                                    <div class="row reviews">
                                                        <?php foreach ($row['images'] as $image) { ?>
                                                            <div class="col-md-1">
                                                                <div class="review-img">
                                                                    <a href="<?= $image; ?>" data-lightbox="review-images">
                                                                        <img class="lazy" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= $image; ?>" alt="Review Image">
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </li>
                                    <?php }
                                        }
                                    } ?>
                                </ol>
                            </div>
                            <!-- /#comments -->
                            <nav class="d-flex mt-10">
                                <?php if (isset($product_ratings) && !empty($product_ratings) && count($product_ratings) > 5) { ?>
                                    <div class="col-md-12">
                                        <div class="text-center">
                                        </div>
                                    </div>
                                <?php } ?>
                            </nav>
                            <!-- /nav -->
                        </div>
                    </div>
                </div>
                <!-- product seller tab -->
                <div class="tab-pane fade" id="product-seller" role="tabpanel" aria-labelledby="product-seller-tab">
                    <div class="align-items-center container d-flex flex-wrap gap-3">
                        <div class="seller-image-container">
                            <img class="lazy pic-1" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= $product['product'][0]['seller_profile'] ?>">
                        </div>
                        <div>
                            <div class="card-body-right seller_tab">
                                <h4 class="mb-0"><?= $product['product'][0]['store_name'] . "  " ?><span class="badge badge-success "><?= number_format($product['product'][0]['seller_rating'], 1) . " " ?><i class="fa fa-star"></i></span> </h4>
                                <span class="text-muted d-block mb-2"><?= $product['product'][0]['seller_name'] ?></span>
                                <p class="m-0 mb-3"><?= $product['product'][0]['store_description'] ?></p>
                                <a target="_BLANK" href="<?= base_url('products?seller=' . $seller_slug[0]['slug']) ?>" class="hover text-decoration-none">Explore All Products</a>
                            </div>
                        </div>
                    </div>
                    <!--</div>-->
                </div>
            </div>
        </div>
    </div>
    <!-- /.container -->
</section>
<!-- section -->

<!-- related products -->
<section class="wrapper bg-gray">
    <div class="container pb-10 overflow-hidden">
        <h3 class="h2 my-5 text-center"><?= !empty($this->lang->line('related_products')) ? str_replace('\\', '', $this->lang->line('related_products')) : 'Related Products' ?></h3>
        <div class="col-12 pb-4 mt-2 mb-2 shop">

            <div class="swiper-container text-center mySwiper4 overflow-hidden" data-margin="30" data-items-xl="4" data-items-md="2" data-items-xs="2">

                <div <?= ($is_rtl == true) ? "dir='rtl'" : ""; ?> class="swiper-wrapper">
                    <?php foreach ($related_products['product'] as $product_row) {
                        if ($product_row['type'] == 'simple_product') {
                            $product_stock = $product_row['stock'];
                        } else {
                            $product_stock = $product_row['total_stock'];
                        }
                    ?>
                        <div class="swiper-slide shadow-xl product-bg swiper-slide-style4">
                            <figure class="rounded ">
                                <div>
                                    <a href="<?= base_url('products/details/' . $product_row['slug']) ?>">
                                        <img class="lazy fig_image image-fit" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('media/image?path=' . rawurlencode($product_row['relative_path']) . '&width=640&quality=80') ?>" alt="<?= $product_row['name'] ?>">
                                    </a>
                                </div>

                                <a class="item-like text-decoration-none add-to-fav-btn 
                                                            <?= ($product_row['is_favorite'] == 1) ? 'fa fa-heart text-danger' : 'fa fa-heart-o' ?>  
                                                            " href="#" data-bs-toggle="white-tooltip" title="<?= !empty($this->lang->line('add_to_wishlist')) ? str_replace('\\', '', $this->lang->line('add_to_wishlist')) : 'Add To wishlist' ?>" data-product-id="<?= $product_row['id'] ?>">
                                    <i class=""></i>
                                </a>

                                <a href="#" class="quick-view-btn item-view text-decoration-none" data-bs-toggle="white-tooltip" title="<?= !empty($this->lang->line('quick_view')) ? str_replace('\\', '', $this->lang->line('quick_view')) : 'Quick View' ?>" data-tip="<?= !empty($this->lang->line('quick_view')) ? str_replace('\\', '', $this->lang->line('quick_view')) : 'Quick View' ?>" data-product-id="<?= $product_row['id'] ?>" data-product-variant-id="<?= $product_row['variants'][0]['id'] ?>" data-izimodal-open="#quick-view">
                                    <i class="uil uil-eye"></i>
                                </a>
                                <?php
                                if (count($product_row['variants']) <= 1) {
                                    $variant_id = $product_row['variants'][0]['id'];
                                    $modal = "";
                                } else {
                                    $variant_id = "";
                                    $modal = "#quick-view";
                                }
                                ?>

                                <?php
                                if (count($product_row['variants']) <= 1) {
                                    $variant_id = $product_row['variants'][0]['id'];
                                } else {
                                    $variant_id = "";
                                }
                                ?>
                                <a href="#" class="compare item-compare text-decoration-none" data-bs-toggle="white-tooltip" title="<?= !empty($this->lang->line('compare')) ? str_replace('\\', '', $this->lang->line('compare')) : 'Compare' ?>" data-tip="<?= !empty($this->lang->line('compare')) ? str_replace('\\', '', $this->lang->line('compare')) : 'Compare' ?>" data-product-id="<?= $product_row['id'] ?>" data-product-variant-id="<?= $variant_id ?>">
                                    <i class="uil uil-exchange-alt"></i>
                                </a>

                                <?php if (isset($product_row['min_max_price']['special_price']) && $product_row['min_max_price']['special_price'] != '' && $product_row['min_max_price']['special_price'] != 0 && $product_row['min_max_price']['special_price'] < $product_row['min_max_price']['min_price']) { ?>
                                    <span class="avatar bg-pink d-flex position-absolute text-uppercase text-white sale_tag">
                                        <span class=""><?= !empty($this->lang->line('sale')) ? str_replace('\\', '', $this->lang->line('sale')) : 'Sale' ?></span>
                                    </span>
                                <?php } ?>

                                <div class="card-body my-4 style_4">
                                    <input id="input" name="rating" class="rating rating-loading d-none" data-size="xs" value="<?= $product_row['rating'] ?>" data-show-clear="false" data-show-caption="false" readonly>
                                    <div class="product-content mt-2">
                                        <h4 class="title post-title m-0 title_wrap fs-16" title="<?= output_escaping(str_replace('\r\n', '&#13;&#10;', $product_row['name'])) ?>">
                                            <a class="link-dark text-decoration-none" href="<?= base_url('products/details/' . $product_row['slug']) ?>"><?= str_replace('\r\n', '&#13;&#10;', strip_tags($product_row['name'])) ?></a>
                                        </h4>
                                        <?php if (($product_row['variants'][0]['special_price'] < $product_row['variants'][0]['price']) && ($product_row['variants'][0]['special_price'] != 0)) { ?>
                                            <p class="mb-0 price text-muted">
                                                <span id="price">
                                                    <?php echo $settings['currency'] ?>
                                                    <?php
                                                    $price = $product_row['variants'][0]['special_price'];
                                                    echo format_price($price);
                                                    ?>
                                                </span>
                                                <sup>
                                                    <span class="special-price striped-price text-danger" id="product-striped-price-div">
                                                        <s id="striped-price">
                                                            <?php echo $settings['currency'] ?>
                                                            <?php $price = $product_row['variants'][0]['price'];
                                                            echo format_price($price);
                                                            ?>
                                                        </s>
                                                    </span>
                                                </sup>
                                            </p>
                                        <?php } else { ?>
                                            <p class="mb-0 price text-muted">
                                                <span id="price">
                                                    <?php echo $settings['currency'] ?>
                                                    <?php
                                                    $price = $product_row['variants'][0]['price'];
                                                    echo format_price($price);
                                                    ?>
                                                </span>
                                            </p>
                                        <?php } ?>

                                        <?php $variant_price = ($product_row['variants'][0]['special_price'] > 0 && $product_row['variants'][0]['special_price'] != '') ? $product_row['variants'][0]['special_price'] : $product_row['variants'][0]['price'];
                                        $data_min = (isset($product_row['minimum_order_quantity']) && !empty($product_row['minimum_order_quantity'])) ? $product_row['minimum_order_quantity'] : 1;
                                        $data_step = (isset($product_row['minimum_order_quantity']) && !empty($product_row['quantity_step_size'])) ? $product_row['quantity_step_size'] : 1;
                                        $data_max = (isset($product_row['total_allowed_quantity']) && !empty($product_row['total_allowed_quantity'])) ? $product_row['total_allowed_quantity'] : 0;
                                        ?>
                                        <a href="#" class="add_to_cart btn btn-xs btn-outline-primary rounded-pill"
                                            data-product-id="<?= $product_row['id'] ?>"
                                            data-product-variant-id="<?= $variant_id ?>"
                                            data-product-stock="<?= $product_stock ?>"
                                            data-product-low-stock-limit="<?= isset($product_row['low_stock_limit']) ? $product_row['low_stock_limit'] : '0' ?>"
                                            data-product-title="<?= $product_row['name'] ?>"
                                            data-product-slug="<?= $product_row['slug'] ?>"
                                            data-product-image="<?= $product_row['image']; ?>"
                                            data-product-price="<?= $variant_price; ?>"
                                            data-min="<?= $data_min; ?>"
                                            data-step="<?= $data_step; ?>"
                                            data-product-description="<?= short_description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', strip_tags($product_row['short_description'])))); ?>"
                                            data-izimodal-open="<?= $modal ?>">
                                            <i class="uil uil-shopping-bag"></i>&nbsp;<?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add To Cart' ?></a>
                                    </div>
                                </div>
                            </figure>
                            <!-- /.social -->
                        </div>
                        <!--/.swiper-slide -->
                    <?php } ?>
                </div>
                <!-- </div> -->
                <!--/.swiper-wrapper -->
            </div>
            <div class="swiper-controls mt-0">
                <div class="swiper-pagination product-swiper-pagination swiper-pagination-clickable swiper-pagination-bullets swiper-pagination-horizontal">
                    <span class="swiper-pagination-bullet" tabindex="0" role="button" aria-label="Go to slide 1"></span>
                    <span class="swiper-pagination-bullet" tabindex="0" role="button" aria-label="Go to slide 2"></span>
                    <span class="swiper-pagination-bullet swiper-pagination-bullet-active" tabindex="0" role="button" aria-label="Go to slide 3" aria-current="true"></span>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container -->
</section>
<!-- /section -->

<div class="modal fade" id="add-faqs-form">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header pb-5 pt-8">
                <h4 class="modal-title">Add Faq</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body pb-8 pt-0">
                <form method="post" action='<?= base_url('products/add_faqs') ?>' id="add-faqs">
                    <div class="form-group">

                        <input type="hidden" name=" <?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                        <input type="hidden" name="user_id" value="<?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';  ?>">
                        <input type="hidden" name="seller_id" value="<?= $product['product'][0]['seller_id'];  ?>">
                        <input type="hidden" name="product_id" value="<?= $product['product'][0]['id']  ?>">
                        <input type="text" class="form-control" id="question" placeholder="Enter Your Question Here" name="question">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill" name="add-faqs" value="Save">Add Question</button>
                    <div class="mt-3">
                        <div id="add_faqs_result"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="user-review-images" class='product-page-content'>
    <div class="container" id="review-image-div">
        <?php
        if (isset($review_images['product_rating']) && !empty($review_images['product_rating'])) { ?>
            <div class="d-flex flex-wrap reviews" id="user_image_data">

            </div>
            <div id="load_more_div">
            </div>
        <?php } ?>
    </div>
</div>
<script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>
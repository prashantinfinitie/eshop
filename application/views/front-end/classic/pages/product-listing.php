<!-- breadcrumb -->
<section class="breadcrumb-title-bar colored-breadcrumb deeplink_wrapper">
    <div class="main-content responsive-breadcrumb">
        <h2><?= isset($page_main_bread_crumb) ? $page_main_bread_crumb : 'Products' ?>
            <?= (isset($seller) && !empty($seller[0]['store_name'])) ? " By " . $seller[0]['store_name'] : '' ?></h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                <?php if (isset($right_breadcrumb) && !empty($right_breadcrumb)) {
                    foreach ($right_breadcrumb as $row) {
                ?>
                        <li class="breadcrumb-item"><?= $row ?></li>
                <?php }
                } ?>
                <li class="breadcrumb-item active" aria-current="page"><?= !empty($this->lang->line('products')) ? str_replace('\\', '', $this->lang->line('products')) : 'Products' ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- end breadcrumb -->
<input type="hidden" id="product-filters" value='<?= (!empty($filters)) ? escape_array($filters) : ""  ?>' data-key="<?= $filters_key ?>" />
<input type="hidden" id="brand-filters" value='<?= (!empty($brands)) ? escape_array($brands) : ""  ?>' data-key="<?= $filters_key ?>" />
<input type="hidden" id="category-filters" value='<?= (!empty($categories) ? ($categories) : "") ?>' data-key="<?= $filters_key ?>" />

<section class="listing-page content main-content">
    <div class="product-listing card-solid py-4">
        <div class="row mx-0">
            <!-- Dektop Sidebar -->
            <div class=" order-md-1 col-lg-3 filter-section sidebar-filter-sm container pt-2 pb-2 filter-sidebar-view h-100">
                <?php if (isset($products['filters']) && !empty($products['filters'])) { ?>
                    <div id="product-filters-desktop" class="filter_attributes mb-5 mt-2">
                        <div class="align-content-center d-flex justify-content-between">
                            <h6 class="m-0"><?= label('attributes', 'Attributes') ?></h6>
                            <a href="#" class="text-decoration-none hover product_filter_btn"><?= !empty($this->lang->line('filter')) ? str_replace('\\', '', $this->lang->line('filter')) : 'Filter' ?></a>
                        </div>
                        <div id="product-filters-desktop">
                            <?php foreach ($products['filters'] as $key => $row) {
                                $row_attr_name = str_replace(' ', '-', $row['name']);
                                $attribute_name = isset($_GET[strtolower('filter-' . $row_attr_name)]) ? $this->input->get(strtolower('filter-' . $row_attr_name), true) : 'null';
                                $selected_attributes = explode('|', $attribute_name);
                                $attribute_values = explode(',', $row['attribute_values']);
                                $attribute_values_id = explode(',', $row['attribute_values_id']);
                            ?>
                                <div class="card-custom">
                                    <div class="card-header-custom" id="h1">
                                        <h2 class="clearfix mb-0">
                                            <a class="collapse-arrow btn btn-link collapsed" data-toggle="collapse" data-target="#c<?= $key ?>" aria-expanded="true" aria-controls="collapseone"><?= html_escape($row['name']) ?><i class="fa fa-angle-down rotate"></i></a>
                                        </h2>
                                    </div>
                                    <div id="c<?= $key ?>" class="collapse <?= ($attribute_name != 'null') ? 'show' : '' ?>" aria-labelledby="h1" data-parent="#accordionExample">
                                        <div class="card-body-custom">
                                            <?php foreach ($attribute_values as $key => $values) {
                                                $values = strtolower($values);
                                            ?>
                                                <div class="input-container d-flex">
                                                    <?= form_checkbox(
                                                        $values,
                                                        $values,
                                                        (in_array($values, $selected_attributes)) ? TRUE : FALSE,
                                                        array(
                                                            'class' => 'toggle-input product_attributes',
                                                            'id' => $row_attr_name . ' ' . $values,
                                                            'data-attribute' => strtolower(str_replace('-', ' ', $row['name'])),
                                                        )
                                                    ) ?>
                                                    <label class="toggle checkbox" for="<?= $row_attr_name . ' ' . $values ?>">
                                                        <div class="toggle-inner"></div>
                                                    </label>
                                                    <?= form_label($values, $row_attr_name . ' ' . $values, array('class' => 'text-label')) ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <div id="brand-filters-desktop" class="filter_brands mb-5 mt-2">
                    <?php if (isset($brands) && !empty($brands)) { ?>
                        <div class="align-content-center d-flex justify-content-between">
                            <h6 class="m-0"><?= label('brands', 'Brands') ?></h6>
                            <a href="#" class="text-decoration-none hover product_filter_btn"><?= !empty($this->lang->line('filter')) ? str_replace('\\', '', $this->lang->line('filter')) : 'Filter' ?></a>
                        </div>
                        <div class="brand_filter d-flex flex-wrap gap-4 mb-5 mt-2 p-1">
                            <?php
                            $brands_filter = json_decode(($brands), true);
                            foreach ($brands_filter as $key => $value) {
                            ?>
                                <div class="brand_div form-check">
                                    <label class="form-check-label" for="<?= $value['id'] ?>-brand">
                                        <input class="brand form-check-input" type="radio" name="brandRadio" data-value="<?= $value['slug'] ?>" id="<?= $value['id'] ?>-brand" checked>
                                        <img src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('media/image?path=' . rawurlencode($value['image']) . '&width=35&quality=80') ?>" alt="brand-logo" class="h-6 lazy brand_filter_img">

                                    </label>

                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
                <div id="category-filters-desktop" class="filter_categories mb-5 mt-2">
                    <?php if (isset($categories)) { ?>
                        <div class="align-content-center d-flex justify-content-between">
                            <h6 class="m-0"><?= label('categories', 'Categories') ?></h6>
                            <a href="#" class="text-decoration-none hover product_filter_btn"><?= !empty($this->lang->line('filter')) ? str_replace('\\', '', $this->lang->line('filter')) : 'Filter' ?></a>
                        </div>
                        <div class="category_filter mb-5 mt-2">
                            <?php
                            $categories_filter = json_decode(($categories), true);
                            foreach ($categories_filter as $key => $value) {
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input category" type="radio" name="categoryRadio" data-value="<?= $value['id'] ?>" id="<?= $value['id'] ?>" value="" checked>
                                    <label class="form-check-label" for="<?= $value['id'] ?>">
                                        <?= $value['name'] ?>
                                    </label>
                                </div>
                            <?php } ?>
                        </div>
                    <?php  } ?>
                </div>

                <div class="text-center d-flex gap-2">
                    <button class="button button-rounded button-warning product_filter_btn w-50"><?= !empty($this->lang->line('filter')) ? str_replace('\\', '', $this->lang->line('filter')) : 'Filter' ?></button>
                    <a href="#" id="reload" class="button button-danger-outline button-rounded rounded-pill w-50"><?= label('clear', 'Clear') ?></a>
                </div>
            </div>

            <div class="col-md-12 order-md-2 <?=
                                                (isset($products['filters']) && !empty($products['filters'])) ||
                                                    (isset($categories) && !empty($categories)) || (isset($brands) && !empty($brands)) ? "col-lg-9" : "col-lg-12" ?>">
                <div class="container-fluid filter-section pt-3 pb-3 ">
                    <div class="col-12 pl-0">
                        <div class="dropdown">
                            <div class="filter-bars">
                                <div class="menu js-menu">
                                    <span class="menu__line"></span>
                                    <span class="menu__line"></span>
                                    <span class="menu__line"></span>

                                </div>
                            </div>
                            <div class="col-12 sort-by py-3 pl-0">
                                <?php if (isset($products) && !empty($products['product'])) { ?>
                                    <div class="dropdown float-md-right d-flex mb-4">
                                        <label class="mr-2 dropdown-label"> <?= !empty($this->lang->line('show')) ? str_replace('\\', '', $this->lang->line('show')) : 'Show' ?>:</label>
                                        <a class="btn dropdown-border btn-lg dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= ($this->input->get('per-page', true) ? $this->input->get('per-page', true) : '12') ?> <span class="caret"></span></a>
                                        <a href="#" id="product_grid_view_btn" class="grid-view"><i class="fas fa-th"></i></a>
                                        <a href="#" id="product_list_view_btn" class="grid-view"><i class="fas fa-th-list"></i></a>
                                        <div class="dropdown-menu custom-dropdown-menu" aria-labelledby="navbarDropdown" id="per_page_products">
                                            <a class="dropdown-item" href="#" data-value=12>12</a>
                                            <a class="dropdown-item" href="#" data-value=16>16</a>
                                            <a class="dropdown-item" href="#" data-value=20>20</a>
                                            <a class="dropdown-item" href="#" data-value=24>24</a>
                                        </div>
                                    </div>
                                    <div class="ele-wrapper">
                                        <div class="form-group col-md-4 d-flex pl-0">
                                            <label for="product_sort_by"></label>
                                            <select id="product_sort_by" class="form-control">
                                                <option><?= !empty($this->lang->line('relevance')) ? str_replace('\\', '', $this->lang->line('relevance')) : 'Relevance' ?></option>
                                                <option value="top-rated" <?= ($this->input->get('sort') == "top-rated") ? 'selected' : '' ?>><?= !empty($this->lang->line('top_rated')) ? str_replace('\\', '', $this->lang->line('top_rated')) : 'Top Rated' ?></option>
                                                <option value="date-desc" <?= ($this->input->get('sort') == "date-desc") ? 'selected' : '' ?>><?= !empty($this->lang->line('newest_first')) ? str_replace('\\', '', $this->lang->line('newest_first')) : 'Newest First' ?></option>
                                                <option value="date-asc" <?= ($this->input->get('sort') == "date-asc") ? 'selected' : '' ?>><?= !empty($this->lang->line('oldest_first')) ? str_replace('\\', '', $this->lang->line('oldest_first')) : 'Oldest First' ?></option>
                                                <option value="price-asc" <?= ($this->input->get('sort') == "price-asc") ? 'selected' : '' ?>><?= !empty($this->lang->line('price_low_to_high')) ? str_replace('\\', '', $this->lang->line('price_low_to_high')) : 'Price - Low To High' ?></option>
                                                <option value="price-desc" <?= ($this->input->get('sort') == "price-desc") ? 'selected' : '' ?>><?= !empty($this->lang->line('price_high_to_low')) ? str_replace('\\', '', $this->lang->line('price_high_to_low')) : 'Price - High To Low' ?></option>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php if (isset($sub_categories) && !empty($sub_categories)) { ?>
                        <div class="col-md-9 col-sm-12 text-left py-3">
                            <?php if (isset($single_category) && !empty($single_category)) { ?>
                                <span class="h3"><?= $single_category['name'] ?> <?= !empty($this->lang->line('category')) ? str_replace('\\', '', $this->lang->line('category')) : 'Category' ?></span>
                            <?php } ?>
                        </div>
                        <div class="category-section container-fluid text-center">
                            <div class="row">
                                <?php foreach ($sub_categories as $key => $row) { ?>
                                    <div class="col-md-2 col-sm-6">
                                        <div class="category-image w-75">
                                            <a href="<?= base_url('products/category/' . html_escape($row->slug)) ?>">
                                                <img class="pic-1 lazy" data-src="<?= $row->image ?>">
                                            </a>
                                            <div class="social">
                                                <span><?= html_escape($row->name) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if (isset($products) && !empty($products['product'])) { ?>

                        <?php if (isset($_GET['type']) && $_GET['type'] == "list") { ?>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h4 class="h4"><?= !empty($this->lang->line('products')) ? str_replace('\\', '', $this->lang->line('products')) : 'Products' ?></h4>
                                </div>
                                <?php foreach ($products['product'] as $row) {
                                    if ($row['type'] == 'simple_product') {
                                        $product_stock = $row['stock'];
                                    } else {
                                        $product_stock = $row['total_stock'];
                                    }
                                ?>

                                    <div class="col-md-3">
                                        <div class="product-grid mb-2 p-0">
                                            <div class="product-image">
                                                <div class="product-image-container">
                                                    <a href="<?= base_url('products/details/' . $row['slug']) ?>">
                                                        <img class="pic-1 lazy" data-src="<?= base_url('media/image?path=' . rawurlencode($row['relative_path']) . '&width=330&quality=80') ?>">
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
                                                            <a href="" data-tip="<?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add to Cart' ?>" class="add_to_cart" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $variant_id ?>" data-product-stock="<?= $product_stock ?>" data-product-low-stock-limit="<?= isset($row['low_stock_limit']) ? $row['low_stock_limit'] : '0' ?>" data-product-title="<?= $row['name'] ?>" data-product-image="<?= $row['image'] ?>" data-product-price="<?= $variant_price; ?>" data-min="<?= $data_min; ?>" data-step="<?= $data_step; ?>" data-product-description="<?= short_description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', strip_tags($row['short_description'])))); ?>" data-izimodal-open="<?= $modal ?>">
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
                                                    <span class="product-new-label"><?= !empty($this->lang->line('sale')) ? str_replace('\\', '', $this->lang->line('sale')) : 'Sale' ?><?= $row['min_max_price']['discount_in_percentage'] ?>%</span>
                                                <?php } ?>
                                                <aside class="add-favorite">
                                                    <button type="button" class="btn far fa-heart add-to-fav-btn <?= ($row['is_favorite'] == 1) ? 'fa text-danger' : '' ?>" data-product-id="<?= $row['id'] ?>"></button>
                                                </aside>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="product-content">
                                            <h2 class="list-product-title title"><a href="<?= base_url('products/details/' . $row['slug']) ?>"><?= $row['name'] ?></a></h2>
                                            <div class="col-md-12 mb-3 product-rating-small" dir="ltr">
                                                <input type="text" class="kv-fa rating-loading" value="<?= $row['rating'] ?>" data-size="sm" title="" readonly>
                                            </div>

                                            <p class="text-muted list-product-desc title_wrap"><?= strip_tags($row['short_description']); ?></p>
                                            <div class="price mb-2 list-view-price">
                                                <?php
                                                if (($row['variants'][0]['special_price'] < $row['variants'][0]['price']) && ($row['variants'][0]['special_price'] != 0)) { ?>
                                                    <p class="mb-0 mt-2 price text-dark">
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
                                                    <p class="mb-0 mt-2 price text-dark">
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
                                            <div class="button button-sm m-0 p-0">
                                                <?php $variant_price = ($row['variants'][0]['special_price'] > 0 && $row['variants'][0]['special_price'] != '') ? $row['variants'][0]['special_price'] : $row['variants'][0]['price'];
                                                $data_min = (isset($row['minimum_order_quantity']) && !empty($row['minimum_order_quantity'])) ? $row['minimum_order_quantity'] : 1;
                                                $data_step = (isset($row['minimum_order_quantity']) && !empty($row['quantity_step_size'])) ? $row['quantity_step_size'] : 1;
                                                $data_max = (isset($row['total_allowed_quantity']) && !empty($row['total_allowed_quantity'])) ? $row['total_allowed_quantity'] : 0;
                                                ?>

                                                <?php if ($row['variants'][0]['cart_count'] != 0) { ?>
                                                    <a class="add-to-cart" href="<?= base_url('cart') ?>"><i class='fas fa-arrow-right'></i> <?= !empty($this->lang->line('go_to_cart')) ? str_replace('\\', '', $this->lang->line('go_to_cart')) : 'Go To Cart' ?></a>
                                                <?php } else { ?>
                                                    <a class="add-to-cart add_to_cart" href="" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $variant_id ?>" data-product-stock="<?= $product_stock ?>" data-product-low-stock-limit="<?= isset($row['low_stock_limit']) ? $row['low_stock_limit'] : '0' ?>" data-product-title="<?= $row['name'] ?>" data-product-image="<?= $row['image'] ?>" data-product-price="<?= $variant_price; ?>" data-min="<?= $data_min; ?>" data-step="<?= $data_step; ?>" data-product-description="<?= short_description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', strip_tags($row['short_description'])))); ?>" data-izimodal-open="<?= $modal ?>">+ <?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add To Cart' ?></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="row w-100">
                                <div class="col-12">
                                    <h4 class="h4"><?= !empty($this->lang->line('products')) ? str_replace('\\', '', $this->lang->line('products')) : 'Products' ?></h4>
                                </div>
                                <?php foreach ($products['product'] as $row) {
                                    if ($row['type'] == 'simple_product') {
                                        $product_stock = $row['stock'];
                                    } else {
                                        $product_stock = $row['total_stock'];
                                    }

                                ?>
                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <div class="product-grid">
                                            <aside class="add-favorite">
                                                <button type="button" class="btn far fa-heart add-to-fav-btn <?= ($row['is_favorite'] == 1) ? 'fa text-danger' : '' ?>" data-product-id="<?= $row['id'] ?>"></button>
                                            </aside>
                                            <div class="product-image">
                                                <div class="product-image-container">
                                                    <a href="<?= base_url('products/details/' . $row['slug']) ?>">
                                                        <img class="pic-1 lazy" data-src="<?= base_url('media/image?path=' . rawurlencode($row['relative_path']) . '&width=330&quality=80') ?>">
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
                                                            <a href="" data-tip="<?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add to Cart' ?>" class="add_to_cart" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $variant_id ?>" data-product-stock="<?= $product_stock ?>" data-product-low-stock-limit="<?= isset($row['low_stock_limit']) ? $row['low_stock_limit'] : '0' ?>" data-product-title="<?= $row['name'] ?>" data-product-image="<?= $row['image'] ?>" data-product-price="<?= $variant_price; ?>" data-min="<?= $data_min; ?>" data-step="<?= $data_step; ?>" data-product-description="<?= short_description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', strip_tags($row['short_description'])))); ?>" data-izimodal-open="<?= $modal ?>">
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
                                                    <span class="product-new-label"><?= !empty($this->lang->line('sale')) ? str_replace('\\', '', $this->lang->line('sale')) : 'Sale' ?> <?= $row['min_max_price']['discount_in_percentage'] ?>%</span>

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
                                                    <a class="add-to-cart add_to_cart" href="" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $variant_id ?>" data-product-stock="<?= $product_stock ?>" data-product-low-stock-limit="<?= isset($row['low_stock_limit']) ? $row['low_stock_limit'] : '0' ?>" data-product-title="<?= $row['name'] ?>" data-product-image="<?= $row['image'] ?>" data-product-price="<?= $variant_price; ?>" data-min="<?= $data_min; ?>" data-step="<?= $data_step; ?>" data-product-description="<?= short_description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', strip_tags($row['short_description'])))); ?>" data-izimodal-open="<?= $modal ?>">+ <?= !empty($this->lang->line('add_to_cart')) ? str_replace('\\', '', $this->lang->line('add_to_cart')) : 'Add To Cart' ?></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } ?>

                    <?php if ((!isset($sub_categories) || empty($sub_categories)) && (!isset($products) || empty($products['product']))) { ?>
                        <div class="col-12 text-center">
                            <h1 class="h2"><?= !empty($this->lang->line('no_products_found')) ? str_replace('\\', '', $this->lang->line('no_products_found')) : 'No Products Found' ?></h1>
                            <a href="<?= base_url('products') ?>" class="button button-rounded button-warning"><?= !empty($this->lang->line('go_to_shop')) ? str_replace('\\', '', $this->lang->line('go_to_shop')) : 'Go to Shop' ?></a>
                        </div>
                    <?php } ?>
                    <nav class="text-center mt-4">
                        <?= (isset($links)) ? $links : '' ?>
                    </nav>
                </div>
            </div>


            <!-- Mobile Filter Menu -->
            <div class="filter-nav js-filter-nav filter-nav-sm">
                <div class="filter-nav__list js-filter-nav__list">
                    <div class="col-md-4 order-md-1 col-lg-3">
                        <div id="product-filters-mobile" class="filter_attributes mb-5 mt-2">
                            <div class="align-content-center d-flex justify-content-between">
                                <h6 class="m-0"><?= label('attributes', 'Attributes') ?></h6>
                                <a href="#" class="text-decoration-none hover product_filter_btn"><?= !empty($this->lang->line('filter')) ? str_replace('\\', '', $this->lang->line('filter')) : 'Filter' ?></a>
                            </div>
                            <?php if (isset($products['filters']) && !empty($products['filters'])) { ?>
                                <div class="accordion" id="accordionExample">
                                    <?php foreach ($products['filters'] as $key => $row) {
                                        $row_attr_name = str_replace(' ', '-', $row['name']);
                                        $attribute_name = isset($_GET[strtolower('filter-' . $row_attr_name)]) ? $this->input->get(strtolower('filter-' . $row_attr_name), true) : 'null';
                                        $selected_attributes = explode('|', $attribute_name);
                                        $attribute_values = explode(',', $row['attribute_values']);
                                        $attribute_values_id = explode(',', $row['attribute_values_id']);
                                    ?>
                                        <div class="card-custom">
                                            <div class="card-header-custom" id="headingOne">
                                                <h2 class="mb-0">
                                                    <a class="collapse-arrow btn btn-link collapsed" data-toggle="collapse" data-target="#m<?= $key ?>" aria-expanded="false" aria-controls="#m<?= $key ?>"><?= html_escape($row['name']) ?><i class="fa fa-angle-down rotate"></i></a>
                                                </h2>
                                            </div>
                                            <div id="m<?= $key ?>" class="collapse <?= ($attribute_name != 'null') ? 'show' : '' ?>" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                <div class="card-body-custom">
                                                    <?php foreach ($attribute_values as $key => $values) {
                                                        $values = strtolower($values);
                                                    ?>
                                                        <div class="input-container d-flex">
                                                            <?= form_checkbox(
                                                                $values,
                                                                $values,
                                                                (in_array($values, $selected_attributes)) ? TRUE : FALSE,
                                                                array(
                                                                    'class' => 'toggle-input product_attributes',
                                                                    'id' => 'm' . $row_attr_name . ' ' . $values,
                                                                    'data-attribute' => strtolower(str_replace('-', ' ', $row['name'])),
                                                                )
                                                            ) ?>
                                                            <label class="toggle checkbox" for="<?= 'm' . $values ?>">
                                                                <div class="toggle-inner"></div>
                                                            </label>
                                                            <?= form_label($values, 'm' . $row_attr_name . ' ' . $values, array('class' => 'text-label')) ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>

                        <div id="brand-filters-mobile">
                            <?php if (isset($brands)) { ?>
                                <div class="align-content-center d-flex justify-content-between">
                                    <h6 class="m-0"><?= label('brands', 'Brands') ?></h6>
                                    <a href="#" class="text-decoration-none hover product_filter_btn"><?= !empty($this->lang->line('filter')) ? str_replace('\\', '', $this->lang->line('filter')) : 'Filter' ?></a>
                                </div>
                                <div class="brand_filter d-flex flex-wrap gap-4 mb-5 mt-2 p-1">
                                    <?php
                                    $brands_filter = json_decode(($brands), true);
                                    foreach ($brands_filter as $key => $value) {
                                    ?>
                                        <div class="brand_div">
                                            <label class="" for="<?= $value['id'] ?>">
                                                <input class="brand" type="radio" name="brandRadio" data-value="<?= $value['slug'] ?>" id="<?= $value['id'] ?>" checked>
                                                <img src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('media/image?path=' . rawurlencode($value['image']) . '&width=35&quality=80') ?>" alt="brand-logo" class="h-6 lazy brand_filter_img">

                                            </label>

                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>

                        <div id="category-filters-mobile">
                            <?php if (isset($categories)) { ?>
                                <div class="align-content-center d-flex justify-content-between">
                                    <h6 class="m-0"><?= label('categories', 'Categories') ?></h6>
                                    <a href="#" class="text-decoration-none hover product_filter_btn"><?= !empty($this->lang->line('filter')) ? str_replace('\\', '', $this->lang->line('filter')) : 'Filter' ?></a>
                                </div>
                                <div class="category_filter mb-5 mt-2">
                                    <?php
                                    $categories_filter = json_decode(($categories), true);
                                    foreach ($categories_filter as $key => $value) {
                                    ?>
                                        <div class="form-check">
                                            <input class="form-check-input category" type="radio" name="categoryRadio" data-value="<?= $value['id'] ?>" id="<?= $value['id'] ?>" value="" checked>
                                            <label class="form-check-label" for="<?= $value['id'] ?>">
                                                <?= $value['name'] ?>
                                            </label>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php  } ?>
                        </div>

                        <div class="text-center d-flex gap-2">
                            <button class="button button-rounded button-warning product_filter_btn w-50"><?= !empty($this->lang->line('filter')) ? str_replace('\\', '', $this->lang->line('filter')) : 'Filter' ?></button>
                            <a href="#" id="reload" class="button button-danger-outline button-rounded rounded-pill w-50"><?= label('clear', 'Clear') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
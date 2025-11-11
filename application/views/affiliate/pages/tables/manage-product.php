<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Promoted Products in <?= $category_data['name'] ?></h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('affiliate/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Products</li>
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
                        <div class="container py-4 affiliate_product_container">
                            <!-- <form id="product-search-form">
                                <div class="row mb-4">
                                    <div class="col-md-6 offset-md-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control rounded-left" name="search" id="search-input" placeholder="Search products..." value="<?= isset($search) ? $search : '' ?>">
                                            <button class="btn-primary rounded-right" type="submit" id="search-btn">Search</button>
                                            <button class="btn btn-outline-secondary <?= empty($search) ? 'd-none' : '' ?>" type="button" id="clear-search-btn">Clear</button>
                                        </div>
                                    </div>
                                </div>
                            </form> -->
                            <div id="product-list">
                                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                                    <?php if (!empty($products)) {
                                        foreach ($products as $product) { ?>
                                            <div class="col py-3 px-3">
                                                <div class="card card-affiliate p-3 h-100">
                                                    <?php

                                                    $affiliate = fetch_details('affiliates', ['user_id' => $_SESSION['user_id']], 'uuid, status');
                                                    $affiliate_uuid = $affiliate[0]['uuid'];


                                                    $sale_percentage = find_discount_in_percentage($product['special_price'], $product['price']);

                                                    // Set your default no-image path
                                                    $no_image_url = base_url() . NO_IMAGE;

                                                    // Build the actual image path
                                                    $image_path = FCPATH . $product['image'];
                                                    $image_url = base_url('media/image?path=' . rawurlencode($product['image']) . '&width=610&quality=80');

                                                    // Check if file exists, else use no-image
                                                    $final_image_url = (file_exists($image_path) && !empty($product['image'])) ? $image_url : $no_image_url;


                                                    $percentage = (isset($product['tax_percentage']) && intval($product['tax_percentage']) > 0 && $product['tax_percentage'] != null) ? $product['tax_percentage'] : '0';

                                                    if ((isset($product['is_prices_inclusive_tax']) && $product['is_prices_inclusive_tax'] == 0) || (!isset($product['is_prices_inclusive_tax'])) && $percentage > 0) {

                                                        $product['price'] = strval(calculatePriceWithTax($percentage, $product['price']));
                                                        $product['special_price'] = strval(calculatePriceWithTax($percentage, $product['special_price']));
                                                    } else {
                                                        $product['price'] = strval($product['price']);
                                                        $product['special_price'] = strval($product['special_price']);
                                                    }

                                                    ?>
                                                    <?php if ($product['special_price'] < $product['price']) { ?>
                                                        <div class="ribbon">On Sale</div>
                                                    <?php } ?>

                                                    <div class="text-center mb-2">
                                                        <img src="<?= $final_image_url ?>" class="img-fluid affiliate_product_img" alt="Product Image" >
                                                    </div>

                                                    <p class="mb-3 text-bold text-center title_wrap" title="<?= $product['name'] ?>"><?= $product['name'] ?></p>
                                                    
                                                    <div class="align-items-center d-flex flex-column">
                                                        <p class="mb-0 text-center font-weight-bold text-success">₹ <?= $product['special_price'] ?>
                                                            <?php if ($product['special_price'] < $product['price']) { ?>
                                                                <span class="price-old font-weight-lighter">₹<?= $product['price'] ?></span>
                                                            <?php } ?>
                                                        </p>
                                                        <p class="mb-0">
                                                            <?php if ($product['special_price'] < $product['price']) { ?>
                                                                <span class="text-danger">(<?= $sale_percentage ?>% off)</span>
                                                            <?php } ?>
                                                        </p>
                                                    </div>
                                                    <p class="mb-3 text-center">Profit: <span class="fw-bold text-cyan"><?= $product['affiliate_commission'] ?>%</span></p>
                                                    <div class="">
                                                        <button class="btn btn-outline-secondary copy-affiliate-link-btn w-100 btn-sm"
                                                            data-product_id=<?= $product['product_id'] ?>
                                                            data-user_id=<?= $affiliate_uuid ?>
                                                            data-slug=<?= $product['slug'] ?>
                                                            data-name=<?= $product['name'] ?>
                                                            data-affiliate_commission=<?= $product['affiliate_commission'] ?>
                                                            data-category_id=<?= $product['category_id'] ?>
                                                            <?= $affiliate[0]['status'] == 7 ? 'disabled' : '' ?>><i class="fa fa-link"></i> COPY LINK</button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }
                                    } else { ?>
                                        <div class="col-12 text-center py-5">
                                            <h5>No products found.</h5>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="pagination-container">
                                    <?= $pagination_links; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
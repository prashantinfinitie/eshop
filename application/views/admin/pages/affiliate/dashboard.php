<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid p-3">
            <!-- Top Row: Orders, New Signups, Delivery Boys, Products -->
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                    <div class="card pull-up">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="align-self-center text-warning">
                                        <i class="ion-ios-cart-outline display-4"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h5 class="text-muted text-bold-500">Orders</h5>
                                        <h3 class="text-bold-600"><?= $order_counter ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                    <div class="card pull-up">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="align-self-center text-primary">
                                        <i class="ion-ios-personadd-outline display-4"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h5 class="text-muted text-bold-500">Affiliate Users</h5>
                                        <h3 class="text-bold-600"><?= $user_counter ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                    <div class="card pull-up">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="align-self-center text-success">
                                        <i class="ion-ios-people-outline display-4"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h5 class="text-muted text-bold-500">Admin Earnings </h5>
                                        <h3 class="text-bold-600"><?= $currency ?> <?= $admin_earnings_via_affiliate ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Middle Row: Sales Summary and Category Wise Product's Count -->
            <div class="row mt-3">
                <div class="col-xl-<?php echo has_permissions('read', 'categories') ? '7' : '12'; ?> col-12">
                    <div class="card card-shadow chart-height">
                        <div class="card-body">
                            <div>
                                <h3 class="card-title">Sales Summary</h3>
                                <div class="labels">
                                    <ul class="nav nav-pills nav-pills-rounded chart-action float-right btn-group sales-tab" role="group">
                                        <li class="nav-item"><a class="btn-sm nav-link px px-2 py-1 active monthlyChart" data-toggle="tab" href="#Monthly">Month</a></li>
                                        <li class="nav-item"><a class="btn-sm nav-link px px-2 py-1 weeklyChart" data-toggle="tab" href="#Weekly">Week</a></li>
                                        <li class="nav-item"><a class="btn-sm nav-link px px-2 py-1 dailyChart" data-toggle="tab" href="#Daily">Day</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div id="Chart" class="affiliate-chart-container mt-5"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-<?php echo has_permissions('read', 'orders') ? '5' : '12'; ?> col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Top Selling Categories</h3>
                        </div>
                        <div class="card-body">
                            <div id="piechart_3d_affiliate" class='piechat_height'></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="top-selling-container col-md-5">
                    <div class="section-title">
                        <span>Top Selling Products</span>

                    </div>

                    <div class="products-list">
                        <ul class="list-unstyled">
                            <?php
                            $rank = 1;
                            foreach ($top_products as $product):
                                if ($rank > 5) break;
                            ?>
                                <li class="media mb-4 align-items-center">
                                    <span class="mr-3 font-weight-bold text-muted"><?= $rank++; ?>.</span>
                                    <img src="<?= base_url($product['product_image']); ?>" class="mr-3 rounded-circle top_selling_product_img" alt="Product Image">
                                    <div class="media-body">
                                        <h6 class="mt-0 mb-1 product-name"><?= htmlspecialchars($product['product_name']) ?></h6>
                                        <small class="product-sales">
                                            <i class="fa fa-cog"></i> Sold: <?= $product['sales']; ?>
                                        </small>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <!-- <div class="top-selling-container col-md-5">
                    <div class="section-title">
                        <span>Categories</span>

                    </div>

                    <div class="products-list">
                        <ul class="list-unstyled">
                            <?php
                            $rank = 1;
                            foreach ($affiliate_categories as $affiliate_category):
                                if ($rank > 5) break;
                            ?>
                                <li class="media mb-4 align-items-center">
                                    <span class="mr-3 font-weight-bold text-muted"><?= $rank++; ?>.</span>
                                    <img src="<?= base_url($affiliate_category['image']); ?>" class="mr-3 rounded-circle top_selling_product_img" alt="Product Image">
                                    <div class="media-body">
                                        <h6 class="mt-0 mb-1 product-name"><?= htmlspecialchars($affiliate_category['name']) ?></h6>
                                        <small class="product-sales">
                                            <i class="fa fa-cog"></i> Sold: 1
                                        </small>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div> -->

            </div>



        </div>
    </section>


</div>
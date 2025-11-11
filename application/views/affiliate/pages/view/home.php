<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid p-3">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="dashboard-container">
                            <!-- Total Profit Section -->
                            <div class="total-profit">
                                <div class="align-items-center d-flex justify-content-between">
                                    <h3>
                                        All Time Total Profit
                                        <i class="fas fa-info-circle info-icon" data-toggle="modal" data-target="#profitInfoModal"></i>
                                    </h3>
                                    <div class="font-weight-bolder text-success text-xl">
                                        <span><?= $currency ?></span><?= $earning_data['total_profit'] ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Cards Grid -->
                            <div class="row">
                                <div class="col-lg-3 col-md-6">
                                    <div class="status-card pending">
                                        <div class="d-flex align-items-center">
                                            <div class="status-icon">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <div class="status-content">
                                                <div class="status-label">Pending</div>
                                                <div class="status-amount"><span><?= $currency ?></span><?= $earning_data['pending'] ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <div class="status-card confirmed">
                                        <div class="d-flex align-items-center">
                                            <div class="status-icon">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <div class="status-content">
                                                <div class="status-label">Confirmed</div>
                                                <div class="status-amount"><span><?= $currency ?></span><?= $earning_data['confirm'] ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <div class="status-card paid">
                                        <div class="d-flex align-items-center">
                                            <div class="status-icon">
                                                <i class="fas fa-credit-card"></i>
                                            </div>
                                            <div class="status-content">
                                                <div class="status-label">Paid</div>
                                                <div class="status-amount"><span><?= $currency ?></span><?= $earning_data['paid'] ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <div class="status-card requested">
                                        <div class="d-flex align-items-center">
                                            <div class="status-icon">
                                                <i class="fas fa-paper-plane"></i>
                                            </div>
                                            <div class="status-content">
                                                <div class="status-label">Requested</div>
                                                <div class="status-amount"><span><?= $currency ?></span><?= $earning_data['requested'] ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- What's This Link -->
                            <div class="mt-3">
                                <a href="#" class="whats-this" data-toggle="modal" data-target="#infoModal">What's this?</a>
                            </div>
                        </div>

                        <!-- All Time Total Profit Info Modal -->
                        <div class="modal fade earning_page_modal" id="profitInfoModal" tabindex="-1" role="dialog" aria-labelledby="profitInfoModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title" id="profitInfoModalLabel">What is All Time Total Profit?</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="profit-info-text">This is the total commission you've earned from orders that were successfully delivered and settled.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Modal -->
                        <div class="modal fade earning_page_modal" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="infoModalLabel">Profit Status Information</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="profit-info-section">
                                            <h6 class="profit-info-title">Pending Profit</h6>
                                            <p class="profit-info-text">This commission is tracked but not yet confirmed. It will be verified after the order is delivered and the return or cancellation period ends.</p>
                                        </div>

                                        <div class="profit-info-section">
                                            <h6 class="profit-info-title">Confirmed Profit</h6>
                                            <p class="profit-info-text">This commission is confirmed and ready to be withdrawn. You can now request a payment for this amount.</p>
                                        </div>

                                        <div class="profit-info-section">
                                            <h6 class="profit-info-title">Paid Profit</h6>
                                            <p class="profit-info-text">Your Profit has already been paid to you. Great job! Keep sharing to earn more.</p>
                                        </div>

                                        <div class="profit-info-section">
                                            <h6 class="profit-info-title">Requested Profit</h6>
                                            <p class="profit-info-text">You've submitted a payment request for this amount. It is currently being reviewed or processed.</p>
                                        </div>

                                        <!-- <div class="profit-info-section">
                                            <h6 class="profit-info-title">Cancelled Profit</h6>
                                            <p class="profit-info-text">Your Profit may be cancelled if the transaction wasn't made via your Profit Link, the order was returned or cancelled, or an invalid coupon was used.</p>
                                        </div> -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle Row: Sales Summary and Category Wise Product's Count -->
            <div class="row mt-3">
                <div class="col-xl-7 col-12">
                    <div class="card card-shadow chart-height">
                        <div class="card-body">
                            <div>
                                <h3 class="card-title">Earnings Summary</h3>
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
                <div class="col-xl-5 col-12">
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

            <div class="categories-section">
                <div class="align-items-center d-flex justify-content-between mb-10 section-header">
                    <h2 class="section-title">Categories</h2>
                    <a href="<?= base_url('affiliate/category') ?>" class="view-all-btn">View All</a>
                </div>

                <div class="categories-container">
                    <div class="categories-row">
                        <?php
                        $rank_cat = 1;
                        foreach ($affiliate_categories as $affiliate_category) {
                            if ($rank_cat > 3) break;
                        ?>
                            <!-- Hair Oil -->
                            <a href="<?= base_url() . 'affiliate/product/get_categories_products/' . $affiliate_category['id'] ?>" class="category-item" style="animation-delay: 0.2s;">
                                <div class="affiliate-category-image">
                                    <img src="<?= base_url($affiliate_category['image']); ?>" alt="<?= htmlspecialchars($affiliate_category['name']) ?>">
                                </div>
                                <div class="category-label"><?= htmlspecialchars($affiliate_category['name']) ?></div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>



        </div>
    </section>

</div>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Manage Earnings</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('affiliate/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Earnings</li>
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
                        <div class="dashboard-container">
                            <!-- Total Profit Section -->
                            <div class="total-profit">
                                <h3>
                                    All Time Total Profit
                                    <i class="fas fa-info-circle info-icon" data-toggle="modal" data-target="#profitInfoModal"></i>
                                </h3>
                                <div class="profit-amount">
                                    <span><?= $currency ?></span><?= $earning_data['total_profit'] ?>
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
                                        <p class="profit-info-text">This is the Profit that you have earned across both Finance and Non-Finance brands, since you joined Affiliate System.</p>
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
                                            <p class="profit-info-text">Your Profit has been tracked and will be processed by the brand after the return or cancellation period. If approved, it will be confirmed. If the order is returned, cancelled, or doesn't meet the terms & conditions, it may be cancelled.</p>
                                        </div>

                                        <div class="profit-info-section">
                                            <h6 class="profit-info-title">Confirmed Profit</h6>
                                            <p class="profit-info-text">Your Profit is ready to be withdrawn. Tap <span class="text-success font-weight-bold">'Request Payment'</span> to transfer it to your bank account.</p>
                                        </div>

                                        <div class="profit-info-section">
                                            <h6 class="profit-info-title">Paid Profit</h6>
                                            <p class="profit-info-text">Your Profit has already been paid to you. Great job! Keep sharing to earn more.</p>
                                        </div>

                                        <div class="profit-info-section">
                                            <h6 class="profit-info-title">Requested Profit</h6>
                                            <p class="profit-info-text">Your withdrawal request is being processed and will be completed soon via your selected payment method.</p>
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

            <div class="col-md-12 main-content">
                <div class="card content-area p-4">
                    <div class="card-innr">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="label" class="col-form-label">Filter By Transaction Type</label>
                                <select class='form-control' name='transaction_type_filter' id="transaction_type_filter">
                                    <option value=''>Select Transaction Type</option>
                                    <option value='order'>Order</option>
                                    <option value='withdraw'>withdraw</option>
                                </select>
                            </div>
                        </div>
                        <div class="gaps-1-5x"></div>
                        <table class='table-striped' id='affiliate_wallet_transaction_table' data-toggle="table" data-url="<?= base_url('affiliate/transaction/view_wallet_transactions_list') ?>" 
                        data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" 
                        data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="awt.id" data-sort-order="desc" data-mobile-responsive="true" 
                        data-toolbar="" data-show-export="true" data-maintain-selected="true" data-query-params="wallet_transaction_queryParams">
                            <thead>
                                <tr>
                                    <th data-field="id" data-sortable="true">ID</th>
                                    <th data-field="payment_type" data-sortable="true">Type</th>
                                    <th data-field="amount_requested" data-sortable="false">Amount</th>
                                    <th data-field="reference_type" data-sortable="false">Transaction Type</th>
                                    <th data-field="message" data-sortable="false">Message</th>
                                    <th data-field="date_created" data-sortable="false">Date Created</th>
                                </tr>
                            </thead>
                        </table>
                    </div><!-- .card-innr -->
                </div><!-- .card -->
            </div>
        </div>
    </section>
</div>

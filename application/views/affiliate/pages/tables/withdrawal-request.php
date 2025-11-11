<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Withdrawal Request</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('affiliate/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Withdrawal Request</li>
                    </ol>
                </div>

            </div>

            <!-- Withdrawal Request Modal -->
            <div class="modal fade" id="withdrawalModal" tabindex="-1" role="dialog" aria-labelledby="withdrawalModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title" id="withdrawalModalLabel">Send Withdrawal Request</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form class="form-horizontal form-submit-event" action="<?= base_url('affiliate/transaction/add_withdrawal_request'); ?>" method="POST" enctype="multipart/form-data">

                                <div class="form-group">
                                    <label for="withdrawalAmount" class="form-label">Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?= $currency ?></span>
                                        </div>
                                        <input type="number" class="form-control" id="withdrawalAmount" name="withdrawalAmount" placeholder="Enter amount" min="1" max="<?= !empty($earning_data['pending']) ? $earning_data['pending'] : 0.0 ?>">
                                    </div>
                                    <small class="form-text text-muted">Available balance: <?= $currency ?> <?= $earning_data['confirm'] ?></small>
                                </div>

                                <div class="form-group">
                                    <label for="paymentMethod" class="form-label">Payment Details <span class="text-danger">*</span></label>
                                    <textarea type="text" class="form-control" id="payment_address" placeholder="Payment Details" name="payment_address"></textarea>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary" id="submit_btn">Send Request</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="col-md-12 main-content">
            <div class="card content-area p-4">
                <div class="card-header border-0">
                    <div class="card-tools">
                        <a href="#" class="btn btn-block btn-outline-primary btn-sm" data-toggle="modal" data-target="#withdrawalModal">Send Withdrawal Request</a>
                    </div>
                </div>
                <div class="card-innr">
                    <div class="gaps-1-5x"></div>
                    <table class='table-striped' id='payment_request_table' data-toggle="table" data-url="<?= base_url('affiliate/transaction/view_withdrawal_request_list') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="pr.id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-query-params="queryParams">
                        <thead>
                            <tr>
                                <th data-field="id" data-sortable="true">ID</th>
                                <th data-field="payment_address" data-sortable="false">Payment Address</th>
                                <th data-field="amount_requested" data-sortable="false">Amount Requested</th>
                                <th data-field="remarks" data-sortable="false">Remarks</th>
                                <th data-field="status" data-sortable="false">Status</th>
                                <th data-field="date_created" data-sortable="false">Date Created</th>
                            </tr>
                        </thead>
                    </table>
                </div><!-- .card-innr -->
            </div><!-- .card -->
        </div>

    </section>

</div>
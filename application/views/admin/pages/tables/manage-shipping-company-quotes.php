<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Shipping Company Quotes (Admin)</h4>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped" data-toggle="table" data-url="<?= base_url('admin/shipping_company_quotes/list') ?>"
                        data-side-pagination="server" data-pagination="true" data-search="true" data-page-list="[10,25,50]">
                        <thead>
                            <tr>
                                <th data-field="id">ID</th>
                                <th data-field="company_name">Company</th>
                                <th data-field="zipcode">Zipcode</th>
                                <th data-field="price">Price</th>
                                <th data-field="eta_text">ETA</th>
                                <th data-field="is_active">Active</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

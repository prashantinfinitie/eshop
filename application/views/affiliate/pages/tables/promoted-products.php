<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Manage Promoted Products</h4>
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
            <div class="col-md-12 main-content">
                <div class="card content-area p-4">
                    <div class="card-header border-0">
                        
                    </div>
                    <div class="card-innr">
                       
                        <div class="gaps-1-5x"></div>
                        <table class='table-striped' id='products_table' data-toggle="table" data-url="<?= isset($_GET['flag']) ? base_url('affiliate/product/get_my_promoted_products_list?flag=') . $_GET['flag'] : base_url('affiliate/product/get_my_promoted_products_list') ?>" 
                        data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" 
                        data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc" 
                        data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-export-types='["txt","excel","csv"]' data-export-options='{
                            "fileName": "products-list",
                            "ignoreColumn": ["state"]
                            }' data-query-params="product_query_params">
                            <thead>
                                <tr>
                                    <th data-field="id" data-sortable="true" data-visible='false' data-align='center'>ID</th>
                                    <th data-field="product_id" data-sortable="true" data-align='center'>Product ID</th>
                                    <th data-field="image" data-sortable="false" data-align='center'>Image</th>
                                    <th data-field="name" data-sortable="false" data-align='center'>Name</th>
                                    <th data-field="category_name" data-sortable="false" data-align='center'>Category Name</th>
                                    <th data-field="affiliate_commission" data-sortable="true" data-align='center'>Category Commission</th>
                                    <th data-field="usage_count" data-sortable="true" data-align='center'>Usage Count</th>
                                    <th data-field="commission_earned" data-sortable="true" data-align='center'>Commission Earned</th>
                                    <th data-field="date" data-sortable="true" data-align='center' data-sortable="true">Date</th>
                                </tr>
                            </thead>
                        </table>
                    </div><!-- .card-innr -->
                </div><!-- .card -->
            </div>
        </div>
    </section>
</div>
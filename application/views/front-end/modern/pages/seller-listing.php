<!-- breadcrumb -->
<div class="content-wrapper deeplink_wrapper">
    <section class="wrapper bg-soft-grape">
        <div class="container py-3 py-md-5">
            <nav class="d-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= !empty($this->lang->line('seller')) ? str_replace('\\', '', $this->lang->line('seller')) : 'Seller' ?></li>
                    <?php if (isset($right_breadcrumb) && !empty($right_breadcrumb)) {
                        foreach ($right_breadcrumb as $row) {
                    ?>
                            <li class="breadcrumb-item"><?= $row ?></li>
                    <?php }
                    } ?>
                </ol>
            </nav>
            <!-- /nav -->
        </div>
        <!-- /.container -->
    </section>
</div>
<!-- end breadcrumb -->

<section class="container listing-page mb-15">
    <div class="product-listing card-solid py-4">
        <div class="row mx-0">
            <!-- Dektop Sidebar -->
            <!-- remved filters -->
                <div class="">
                    <h1><?= !empty($this->lang->line('sellers')) ? str_replace('\\', '', $this->lang->line('sellers')) : 'Sellers' ?></h4>
                </div>
            <div class="container-fluid filter-section pb-3">
                <div class="col-12 pl-0">
                    <div class="dropdown">
                        <div class="filter-bars">
                            <div class="menu js-menu">
                                <span class="menu__line"></span>
                                <span class="menu__line"></span>
                                <span class="menu__line"></span>

                            </div>
                        </div>
                        <div class="align-items-center d-flex flex-wrap justify-content-between gap-2">
                            <div class="col-md-5 pl-0">
                                <select id="product_sort_by" class="form-control">
                                    <option><?= !empty($this->lang->line('relevance')) ? str_replace('\\', '', $this->lang->line('relevance')) : 'Relevance' ?></option>
                                    <option value="top-rated" <?= ($this->input->get('sort') == "top-rated") ? 'selected' : '' ?>><?= !empty($this->lang->line('top_rated')) ? str_replace('\\', '', $this->lang->line('top_rated')) : 'Top Rated' ?></option>
                                    <option value="date-desc" <?= ($this->input->get('sort') == "date-desc") ? 'selected' : '' ?>><?= !empty($this->lang->line('newest_first')) ? str_replace('\\', '', $this->lang->line('newest_first')) : 'Newest First' ?></option>
                                    <option value="date-asc" <?= ($this->input->get('sort') == "date-asc") ? 'selected' : '' ?>><?= !empty($this->lang->line('oldest_first')) ? str_replace('\\', '', $this->lang->line('oldest_first')) : 'Oldest First' ?></option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="search" name="seller_search" class="form-control" id="seller_search" value="<?= (isset($seller_search) && !empty($seller_search)) ? $seller_search : "" ?>" placeholder="<?= !empty($this->lang->line('search_seller')) ? str_replace('\\', '', $this->lang->line('search_seller')) : 'Search Seller' ?>">
                            </div>
                            <div class="dropdown float-md-right form-select-wrapper">
                                <div class="align-items-baseline d-flex">
                                    <label class="mr-2 dropdown-label"> <?= !empty($this->lang->line('show')) ? str_replace('\\', '', $this->lang->line('show')) : 'Show' ?>:</label>
                                    <a class="dropdown-border form-select col-4 mr-2" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= ($this->input->get('per-page', true) ? $this->input->get('per-page', true) : '12') ?> <span class="caret ms-4"></span></a>
                                    <a href="#" id="product_grid_view_btn" class="grid-view text-dark text-decoration-none"><i class="fs-20 uil uil-th"></i></a>
                                    <a href="#" id="product_list_view_btn" class="grid-view ps-3 text-dark text-decoration-none"><i class="fs-20 uil uil-list-ul"></i></a>
                                    <div class="dropdown-menu custom-dropdown-menu" aria-labelledby="navbarDropdown" id="per_page_sellers">
                                        <a class="dropdown-item" href="#" data-value=12>12</a>
                                        <a class="dropdown-item" href="#" data-value=16>16</a>
                                        <a class="dropdown-item" href="#" data-value=20>20</a>
                                        <a class="dropdown-item" href="#" data-value=24>24</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (isset($sellers) && !empty($sellers)) { ?>

                    <?php if (isset($_GET['type']) && $_GET['type'] == "list") { ?>
                        <div class="col-md-12 col-sm-6">
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h1 class="h4"><?= !empty($this->lang->line('sellers')) ? str_replace('\\', '', $this->lang->line('sellers')) : 'Sellers' ?></h4>
                                </div>
                                <?php foreach ($sellers as $row) {
                                ?>
                                    <div class="card mt-5" title="<?= $row['seller_name'] ?>">
                                        <div class="align-items-center d-flex flex-wrap gap-2">
                                            <div class="col-md-3">
                                                <div class="">
                                                    <div class="product-image">
                                                        <div class="product-image-container">
                                                            <a href="<?= base_url('sellers/seller_details/' . $row['slug']) ?>">
                                                                <img class="pic-1 lazy product-list-image" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('media/image?path=' . rawurlencode($row['seller_profile_path']) . '&width=800&quality=80') ?>">
                                                                <?php $row['seller_profile']; ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="product-content">
                                                    <h3 class="list-product-title title" title="<?= $row['seller_name'] ?>"><a href="<?= base_url('sellers/seller_details/' . $row['slug']) ?>"><?= $row['seller_name'] ?></a></h3>
                                                    <div class="rating">
                                                        <input id="input" name="rating" class="rating rating-loading d-none" data-size="xs" value="<?= number_format($row['seller_rating'], 1) ?>" data-show-clear="false" data-show-caption="false" readonly>
                                                    </div>
                                                    <p class="text-muted list-product-desc m-0"><?= $row['store_description'] ?></p>
                                                    <p class="price mb-2 list-view-price">
                                                        <?= $row['store_name'] ?>
                                                    </p>
                                                    <a href="<?= base_url('products?seller=' . $row['slug']) ?>" class="view-products  btn btn-sm btn-outline-primary rounded-pill mt-2"><?= !empty($this->lang->line('view_products')) ? str_replace('\\', '', $this->lang->line('view_products')) : 'View Products' ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                    <?php } else { ?>

                        <div class="row">
                            <?php foreach ($sellers as $row) { ?>
                                <div class="col-md-4 col-6 mt-5 " title="<?= $row['seller_name'] ?>">
                                    <div class="card text-center">
                                        <div class="seller-image-container">
                                            <a href="<?= base_url('sellers/seller_details/' . $row['slug']) ?>">
                                                <img class="pic-1 lazy fig_seller_image" src="<?= base_url('assets/front_end/modern/img/product-placeholder.jpg') ?>" data-src="<?= base_url('media/image?path=' . rawurlencode($row['seller_profile_path']) . '&width=800&quality=80') ?>">
                                            </a>
                                        </div>
                                        <div class="rating">
                                            <input id="input" name="rating" class="rating rating-loading d-none" data-size="xs" value="<?= number_format($row['seller_rating'], 1) ?>" data-show-clear="false" data-show-caption="false" readonly>
                                        </div>
                                        <div class="product-content my-3">
                                            <h4 class="title m-0" title="<?= $row['seller_name'] ?>"><a class="text-decoration-none text-dark" href="<?= base_url('sellers/seller_details/' . $row['slug']) ?>"><?= $row['seller_name'] ?></a></h4>
                                            <p class="price fs-14">
                                                <?= $row['store_name'] ?>
                                            </p>
                                            <a href="<?= base_url('products?seller=' . $row['slug']) ?>" class="view-products btn btn-xs btn-outline-primary rounded-pill"><?= !empty($this->lang->line('view_products')) ? str_replace('\\', '', $this->lang->line('view_products')) : 'View Products' ?></a>

                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } ?>

                <?php if (!isset($sellers) || empty($sellers)) { ?>
                    <div class="col-12 text-center mt-5">
                        <h1 class="h2"><?= !empty($this->lang->line('no_sellers_found')) ? str_replace('\\', '', $this->lang->line('no_sellers_found')) : 'No Sellers Found.' ?></h1>
                        <a href="<?= base_url('products') ?>" class="btn rounded-pill btn-warning btn-sm"><?= !empty($this->lang->line('go_to_shop')) ? str_replace('\\', '', $this->lang->line('go_to_shop')) : 'Go to Shop' ?></a>
                    </div>
                <?php } ?>
                <nav class="text-center mt-4">
                    <?= (isset($links)) ? $links : '' ?>
                </nav>
            </div>
        </div>
    </div>
</section>
<!-- breadcrumb -->
<section class="breadcrumb-title-bar colored-breadcrumb deeplink_wrapper">
    <div class="main-content responsive-breadcrumb">
        <h2><?= isset($page_main_bread_crumb) ? $page_main_bread_crumb : 'Blogs Listing' ?></h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                <?php if (isset($right_breadcrumb) && !empty($right_breadcrumb)) {
                    foreach ($right_breadcrumb as $row) {
                ?>
                        <li class="breadcrumb-item"><?= $row ?></li>
                <?php }
                } ?>
                <li class="breadcrumb-item active" aria-current="page"><?= !empty($this->lang->line('blogs')) ? str_replace('\\', '', $this->lang->line('blogs')) : 'Blogs' ?></li>
            </ol>
        </nav>
    </div>
</section>
<!-- end breadcrumb -->

<section class="listing-page content main-content">
    <div class="product-listing card-solid py-4">
        <div class="row mx-0">
            <!-- Dektop Sidebar -->
            <!-- remved filters -->
            <div class="col-md-12 order-md-2">
                <div class="container-fluid filter-section pt-3 pb-3">

                    <div class="col-12 pl-0">
                        <div class="dropdown">
                            <div class="filter-bars d-none">
                                <div class="menu js-menu">
                                    <span class="menu__line"></span>
                                    <span class="menu__line"></span>
                                    <span class="menu__line"></span>

                                </div>
                            </div>
                            <div class="dropdown float-md-right d-flex mb-4">
                                <label class="mr-2 dropdown-label"> <?= !empty($this->lang->line('show')) ? str_replace('\\', '', $this->lang->line('show')) : 'Show' ?>:</label>
                                <a class="btn dropdown-border btn-lg dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= ($this->input->get('per-page', true) ? $this->input->get('per-page', true) : '12') ?> <span class="caret"></span></a>
                                <div class="dropdown-menu custom-dropdown-menu" aria-labelledby="navbarDropdown" id="per_page_sellers">
                                    <a class="dropdown-item" href="#" data-value=6>6</a>
                                    <a class="dropdown-item" href="#" data-value=12>12</a>
                                    <a class="dropdown-item" href="#" data-value=16>16</a>
                                    <a class="dropdown-item" href="#" data-value=20>20</a>
                                    <a class="dropdown-item" href="#" data-value=24>24</a>
                                </div>
                            </div>
                            <div class="ele-wrapper d-flex ">
                                <div class="form-group col-md-6 pl-0">
                                    <label for="product_sort_by"></label>
                                    <select class='form-control' name='category_parent' id="category_parent">
                                        <option value=""><?= !empty($this->lang->line('select_category')) ? str_replace('\\', '', $this->lang->line('select_category')) : 'Select Category' ?></option>
                                        <?php foreach ($fetched_data as $categories) { ?>
                                            <option value="<?= $categories['id'] ?>" <?= ($this->input->get('category_id') == $categories['id']) ? 'selected' : '' ?>><?= $categories['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6 mt-2">
                                    <label for="seller_search"></label>
                                    <input type="search" name="blog_search" class="form-control" id="blog_search" value="<?= (isset($blog_search) && !empty($blog_search)) ? $blog_search : "" ?>" placeholder="<?= !empty($this->lang->line('search_blog')) ? str_replace('\\', '', $this->lang->line('search_blog')) : 'Search Blog' ?>">
                                </div>
                            </div>
                        </div>
                    </div>


                    <?php if (!isset($blogs['data']) && empty($blogs['data']) || $blogs['data'] == []) { ?>
                        <div>
                            <h1 class="h3 text-center"><?= !empty($this->lang->line('no_blogs_found')) ? str_replace('\\', '', $this->lang->line('no_blogs_found')) : 'No Blogs Added Yet.' ?></h1>
                        </div>
                    <?php } ?>

                    <div class="row w-100">
                        <?php foreach ($blogs['data'] as $row) { ?>
                            <div class="col-md-3 mb-4">
                                <div class="blog-card">
                                    <div class="blog-card-img">
                                        <a href="<?= base_url("blogs/view_detail/" . $row['slug']) ?>">
                                            <img src="<?= base_url() . rawurlencode($row['image']) ?>" alt="Card image">
                                        </a>
                                    </div>

                                    <div class="card-body">

                                        <h2 class="blog-title mb-2">
                                            <a class="link-dark text-decoration-none" href="<?= base_url("blogs/view_detail/" . $row['slug']) ?>"><?= $row['title'] ?>
                                            </a>
                                        </h2>

                                        <h6 class="card-text blog-discription">
                                            <?= description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', $row['description']))) ?>
                                        </h6>
                                        <a href="<?= base_url("blogs/view_detail/" . $row['slug'])
                                                    ?>" class="text-primary float-left">
                                            <h3><?= !empty($this->lang->line('read_more')) ? str_replace('\\', '', $this->lang->line('read_more')) : 'Read more >>' ?></h3>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <nav class="text-center mt-4">
                        <?= (isset($links)) ? $links : '' ?>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
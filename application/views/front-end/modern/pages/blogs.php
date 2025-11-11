<!-- breadcrumb -->
<div class="content-wrapper deeplink_wrapper">
    <section class="wrapper bg-soft-grape">
        <div class="container py-3 py-md-5">
            <nav class="d-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                    <?php if (isset($right_breadcrumb) && !empty($right_breadcrumb)) {
                        foreach ($right_breadcrumb as $row) {
                    ?>
                            <li class="breadcrumb-item"><?= $row ?></li>
                    <?php }
                    } ?>
                    <li class="breadcrumb-item active text-muted" aria-current="page"><?= !empty($this->lang->line('blogs')) ? str_replace('\\', '', $this->lang->line('blogs')) : 'Blogs' ?></li>
                </ol>
            </nav>
            <!-- /nav -->
        </div>
        <!-- /.container -->
    </section>
</div>
<!-- end breadcrumb -->


<!-- /section -->
<section class="wrapper bg-light my-10">
    <div class="container mb-15">
        <div class="col-12 pl-0">
            <div class="dropdown">
                <div class="filter-bars">
                    <div class="menu js-menu">
                        <span class="menu__line"></span>
                        <span class="menu__line"></span>
                        <span class="menu__line"></span>

                    </div>
                </div>

                <div class="ele-wrapper d-flex align-items-center flex-wrap">
                    <div class="form-group col-md-5 pl-0 blog_category">
                        <label for="product_sort_by"></label>
                        <select class='form-control' name='category_parent' id="category_parent">
                            <option value=""><?= !empty($this->lang->line('select_category')) ? str_replace('\\', '', $this->lang->line('select_category')) : 'Select Category' ?></option>
                            <?php foreach ($fetched_data as $categories) { ?>
                                <option value="<?= $categories['id'] ?>" <?= ($this->input->get('category_id') == $categories['id']) ? 'selected' : '' ?>><?= $categories['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="seller_search"></label>
                        <input type="search" name="blog_search" class="form-control" id="blog_search" value="<?= (isset($blog_search) && !empty($blog_search)) ? $blog_search : "" ?>" placeholder="<?= !empty($this->lang->line('search_blog')) ? str_replace('\\', '', $this->lang->line('search_blog')) : 'Search Blog' ?>">
                    </div>
                    <div class="dropdown float-md-right mb-4 col-md-3 form-select-wrapper">
                        <div class="d-flex align-items-baseline mt-5">
                            <label class="mr-2 dropdown-label fw-bold fs-16"> <?= !empty($this->lang->line('show')) ? str_replace('\\', '', $this->lang->line('show')) : 'Show' ?>:</label>
                            <a class="dropdown-border form-select col-6" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= ($this->input->get('per-page', true) ? $this->input->get('per-page', true) : '12') ?> <span class="caret"></span></a>
                            <div class="dropdown-menu custom-dropdown-menu" aria-labelledby="navbarDropdown" id="per_page_sellers">
                                <a class="dropdown-item" href="#" data-value=6>6</a>
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
        <div class="row">

            <?php if (!isset($blogs['data']) && empty($blogs['data']) || $blogs['data'] == []) { ?>
                <div>
                    <h1 class="h2 text-center"><?= !empty($this->lang->line('no_blogs_found')) ? str_replace('\\', '', $this->lang->line('no_blogs_found')) : 'No Blogs Added Yet.' ?></h1>
                </div>

            <?php } ?>

            <?php foreach ($blogs['data'] as $row) { ?>
                <div class="item-inner col-md-4">
                    <article>
                        <div class="card">
                            <figure class="card-img-top overlay overlay-1 hover-scale">
                                <a href="<?= base_url("blogs/view_detail/" . $row['slug']) ?>"> <img src="<?= base_url() . rawurlencode($row['image']) ?>" alt="" class="image-fit" /></a>
                                <figcaption>
                                    <h5 class="from-top mb-0"><?= labels('read_more', 'Read More') ?></h5>
                                </figcaption>
                            </figure>
                            <div class="card-body p-4 overflow-hidden" style="height: 200px;">
                                <div class="post-header">
                                    <h2 class="post-title h3 mt-1 mb-3"><a class="link-dark text-decoration-none" href="<?= base_url("blogs/view_detail/" . $row['slug']) ?>">
                                            <?= description_word_limit($row['title'], 20) ?></a></h2>
                                </div>
                                <!-- /.post-header -->
                                <div class="post-content">
                                    <p><?= description_word_limit(output_escaping(str_replace('\r\n', '&#13;&#10;', $row['description'])), 50) ?></p>
                                </div>
                                <!-- /.post-content -->
                            </div>
                            <!--/.card-body -->
                            <div class="card-footer">
                                <ul class="post-meta d-flex mb-0">
                                    <?php $date = strtotime($row['date_added']); ?>
                                    <li class="post-date"><i class="uil uil-calendar-alt"></i><span><?= date("d-M-Y", $date) ?></span></li>
                                </ul>
                                <!-- /.post-meta -->
                            </div>
                            <!-- /.card-footer -->
                        </div>
                        <!-- /.card -->
                    </article>
                    <!-- /article -->
                </div>
                <!-- /.item-inner -->
            <?php } ?>
        </div>
    </div>
</section>
<!-- /section -->
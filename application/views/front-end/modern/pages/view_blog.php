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
                    <li class="breadcrumb-item text-muted" aria-current="page"><?= !empty($this->lang->line('blogs')) ? str_replace('\\', '', $this->lang->line('blogs')) : 'Blogs' ?></li>
                    <li class="breadcrumb-item active text-muted" aria-current="page"><?= str_replace("\'", "'", str_replace('\r\n', '&#13;&#10;', $blog[0]['title'])) ?></li>
                </ol>
            </nav>
            <!-- /nav -->
        </div>
        <!-- /.container -->
    </section>
</div>
<!-- end breadcrumb -->


<section class="listing-page content main-content">
    <div class="product-listing card-solid py-4">
        <div class="container mb-15 pt-3">
            <div class="row w-100">
                    <div class="card">
                        <div class="blog-card-img">
                            <a href="#">
                                <img src="<?= base_url() . rawurlencode($blog[0]['image']) ?>" alt="Card image">
                            </a>
                        </div>
                        <div class="card-body">
                            <h2 class="view-blog-title mb-2 mt-2"><?= str_replace("\'", "'", str_replace('\r\n', '&#13;&#10;', $blog[0]['title'])) ?></h2>
                            <p class="card-text mt-5"><?= str_replace("\'", "'", str_replace('\r\n', '&#13;&#10;', $blog[0]['description'])) ?></p>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</section>
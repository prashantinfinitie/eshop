<section class="container mb-15 deeplink_wrapper">
    <div class="category-section text-center">
        <div class='my-4 featured-section-title'>
            <div class=''>
                <h3 class='section-title'><?= !empty($this->lang->line('brands')) ? str_replace('\\', '', $this->lang->line('brands')) : 'Brands' ?></h3>
            </div>
            <hr class="my-5">
        </div>
        <div class="d-flex flex-wrap gap-3">

            <?php foreach ($brands as $key => $row) { ?>
                <div class="swiper-slide-category text-center brand_image_div mx-2">
                    <a href="<?= base_url('products?brand=' . html_escape($row['brand_slug'])) ?>" class="text-decoration-none">
                        <img class="lazy" src="<?= base_url('media/image?path='. rawurlencode($row['brand_img']) .'&width=110&quality=80') ?>" alt="<?= html_escape($row['brand_name']) ?>" />
                        <h6 class="fs-14 mb-0"><?= html_escape($row['brand_name']) ?></h6>
                    </a>
                </div>
            <?php } ?>

        </div>
        <nav class="text-center mt-14 d-flex overflow-auto" aria-label="pagination">
            <?= (isset($links)) ? $links : '' ?>
        </nav>
    </div>
</section>
<section class="main-content mt-md-5 mt-sm-0 mb-5 deeplink_wrapper">
    <div class="category-section container-fluid text-center">
        <div class='my-4 featured-section-title'>
            <div class='col-md-12'>
                <h3 class='section-title'><?= !empty($this->lang->line('brands')) ? str_replace('\\', '', $this->lang->line('brands')) : 'Brands' ?></h3>
            </div>
            <hr>
        </div>
        <div class="d-flex flex-wrap gap-4">
            <?php foreach ($brands as $key => $row) { ?>
                <div class="align-items-center d-flex flex-column justify-content-center">
                    <a href="<?= base_url('products?brand=' . html_escape($row['brand_slug'])) ?>">
                        <div class="brand_image">
                            <img src="<?= base_url('media/image?path=' . rawurlencode($row['brand_img']) . '&width=110&quality=80') ?>" class="brand_img">
                        </div>
                    </a>
                    <div class="">
                        <span><?= html_escape($row['brand_name']) ?></span>
                    </div>
                </div>
            <?php } ?>
        </div>
        <nav class="text-center mt-14 d-flex overflow-auto" aria-label="pagination">
            <?= (isset($links)) ? $links : '' ?>
        </nav>
    </div>
</section>
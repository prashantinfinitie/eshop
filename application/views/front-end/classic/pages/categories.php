<section class="main-content mt-md-5 mt-sm-0 mb-5 deeplink_wrapper">
    <div class="category-section container-fluid text-center dark-category-section icon-dark-sec">
        <div class='my-4 featured-section-title'>
            <div class='col-md-12'>
                <h3 class='section-title text-white'><?= !empty($this->lang->line('category')) ? str_replace('\\', '', $this->lang->line('category')) : 'Browse Categories' ?></h3>
            </div>
            <hr>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($categories as $key => $row) { ?>
                <div class="">
                    <div class="category-image justify-content-center d-flex w-50">
                        <div class="category-image-container">
                            <a href="<?= base_url('products/category/' . html_escape($row['slug'])) ?>">
                                <img src="<?= base_url('media/image?path='. rawurlencode($row['relative_path']) .'&width=160&quality=80') ?>">
                            </a>
                            <div class="">
                                <span><?= html_escape($row['name']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if ((!isset($categories) || empty($categories))) { ?>
                <div class="col-12 text-center">
                    <h1 class="h2"><?= !empty($this->lang->line('no_category_found')) ? str_replace('\\', '', $this->lang->line('no_category_found')) : 'No Categories Found.' ?></h1>
                    <a href="<?= base_url('products') ?>" class="btn btn-sm rounded-pill btn-warning"><?= !empty($this->lang->line('go_to_shop')) ? str_replace('\\', '', $this->lang->line('go_to_shop')) : 'Go to Shop' ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
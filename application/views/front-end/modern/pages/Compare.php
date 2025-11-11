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
                    <li class="breadcrumb-item active text-muted" aria-current="page"><?= !empty($this->lang->line('compare')) ? str_replace('\\', '', $this->lang->line('compare')) : 'Compare' ?></li>
                </ol>
            </nav>
            <!-- /nav -->
        </div>
        <!-- /.container -->
    </section>
</div>
<!-- end breadcrumb -->

<section class="container main-content mb-15 py-5">
    <div class="entry-content">
        <div id="compare-items" class="d-flex flex-column">
            <div class="container">
                <div class="align-items-center d-flex flex-column">
                    <div class="empty-compare">
                        <img src="<?= base_url('assets/front_end/modern/img/empty-compare.webp') ?>" alt="<?= !empty($this->lang->line('no_items_to_compare')) ? str_replace('\\', '', $this->lang->line('no_items_to_compare')) : 'No items to compare' ?>">
                    </div>
                    <div class="h5"><?= !empty($this->lang->line('no_items_to_compare')) ? str_replace('\\', '', $this->lang->line('no_items_to_compare')) : 'No items to compare' ?></div>
                </div>
            </div>
        </div>
    </div>
</section>
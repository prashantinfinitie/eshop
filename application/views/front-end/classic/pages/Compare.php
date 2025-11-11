<section class="breadcrumb-title-bar colored-breadcrumb deeplink_wrapper">
    <div class="main-content responsive-breadcrumb">
        <h2><?= !empty($this->lang->line('compare')) ? str_replace('\\', '', $this->lang->line('compare')) : 'Compare' ?></h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                <li class="breadcrumb-item active"><?= !empty($this->lang->line('compare')) ? str_replace('\\', '', $this->lang->line('compare')) : 'Compare' ?></li>
            </ol>
        </nav>
    </div>
</section>
<section class="main-content py-5 my-4">
    <div class="entry-content">
        <div id="compare-items">
            <div class="container">
                <div class="align-items-center d-flex flex-column">
                    <div class="empty-compare">
                        <img src="<?= base_url('assets/front_end/classic/images/empty-compare.webp') ?>" alt="<?= !empty($this->lang->line('no_items_to_compare')) ? str_replace('\\', '', $this->lang->line('no_items_to_compare')) : 'No items to compare' ?>">
                    </div>
                    <div class="h5"><?= !empty($this->lang->line('no_items_to_compare')) ? str_replace('\\', '', $this->lang->line('no_items_to_compare')) : 'No items to compare' ?></div>
                </div>
            </div>
        </div>
    </div>
</section>
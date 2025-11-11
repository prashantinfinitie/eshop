<section class="breadcrumb-title-bar colored-breadcrumb deeplink_wrapper">
    <div class="main-content responsive-breadcrumb">
        <h2><?= !empty($this->lang->line('faq')) ? str_replace('\\', '', $this->lang->line('faq')) : 'FAQ' ?></h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                <li class="breadcrumb-item"><?= !empty($this->lang->line('faq')) ? str_replace('\\', '', $this->lang->line('faq')) : 'FAQ' ?></li>
            </ol>
        </nav>
    </div>

</section>
<section class="home_faq_sec mt-5" id="faq_sec">
    <div class="main-content">
        <div class="row">
            <div class="home_faq col-md-7">
                <h2 class="h6"><span class="span-color"><?= !empty($this->lang->line('faq')) ? str_replace('\\', '', $this->lang->line('faq')) : 'FAQ' ?></span></h2>
                <div class="accordion mt-5 pl-0" id="accordionExample">
                    <?php foreach ($faq['data'] as $row) { ?>
                        <div class="card">
                            <div class="card-header" id="<?= "h-" . $row['id'] ?>">
                                <h2 class="clearfix mb-0">
                                    <a class="home_faq_btn pl-0 collapsed faq-btn-text" data-toggle="collapse" data-target="#<?= "c-" . $row['id'] ?>" aria-expanded="true" aria-controls="collapseone">
                                        <?= html_escape($row['question']) ?>
                                        <i class="fa fa-angle-down rotate"></i></a>
                                </h2>
                            </div>
                            <div id="<?= "c-" . $row['id'] ?>" class="collapse " aria-labelledby="<?= "h-" . $row['id'] ?>" data-parent="#accordionExample">
                                <div class="card-body"><?= html_escape($row['answer']) ?></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php if ((!isset($faq['data']) && empty($faq['data'])) || $faq['data'] == []) { ?>
                    <div class="d-flex flex-column align-items-center mt-7">
                        <div>
                            <img src="<?= base_url('assets/front_end/modern/img/new/No-Faq.png') ?>" alt="No Faq" width="160px" />
                        </div>
                        <div>
                            <div class=" add-faqs-form float-right">
                                <h1 class="h2"><?= !empty($this->lang->line('no_faqs_found')) ? str_replace('\\', '', $this->lang->line('no_faqs_found')) : 'No FAQs Found.' ?></h1>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="col-md-5">
                <div class="faq_image">
                    <img src="<?= THEME_ASSETS_URL . 'demo/faq1.png' ?>" alt="faq">
                </div>
            </div>
        </div>
    </div>
</section>
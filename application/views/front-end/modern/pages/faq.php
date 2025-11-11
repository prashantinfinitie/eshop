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
                    <li class="breadcrumb-item active text-muted" aria-current="page"><?= !empty($this->lang->line('faq')) ? str_replace('\\', '', $this->lang->line('faq')) : 'FAQ' ?></li>
                </ol>
            </nav>
            <!-- /nav -->
        </div>
        <!-- /.container -->
    </section>
</div>
<!-- end breadcrumb -->


<section class="wrapper bg-light">
    <div class="container py-14 py-md-16">
        <div class="card bg-soft-navy rounded-4">
            <div class="card-body p-md-10 p-xl-11">
                <div class="row gx-lg-8 gx-xl-12 gy-10">
                    <div class="col-lg-6">
                        <div class="faq_image">
                            <img class="faq-img" src="<?= THEME_ASSETS_URL . 'demo/faq2.png' ?>" alt="faq">
                        </div>
                    </div>
                    <!--/column -->
                    <div class="col-lg-6">
                        <div class="accordion accordion-wrapper" id="accordionExample">
                            <?php foreach ($faq['data'] as $row) { ?>
                                <div class="card plain accordion-item">
                                    <div class="card-header" id="<?= "h-" . $row['id'] ?>">
                                        <button class="collapsed" data-bs-toggle="collapse" data-bs-target="#<?= "c-" . $row['id'] ?>" aria-expanded="true" aria-controls="collapseOne"><?= html_escape($row['question']) ?></button>
                                    </div>
                                    <!--/.card-header -->
                                    <div id="<?= "c-" . $row['id'] ?>" class="accordion-collapse collapse" aria-labelledby="<?= "h-" . $row['id'] ?>" data-bs-parent="#accordionExample">
                                        <div class="card-body">
                                            <p><?= html_escape($row['answer']) ?></p>
                                        </div>
                                        <!--/.card-body -->
                                    </div>
                                    <!--/.accordion-collapse -->
                                </div>
                            <?php } ?>
                            <!--/.accordion-item -->
                        </div>
                        <!--/.accordion -->

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
                    <!--/column -->
                </div>
                <!--/.row -->
            </div>
            <!--/.card-body -->
        </div>
        <!--/.card -->
    </div>
    <!-- /.container -->
</section>
<!-- /section -->
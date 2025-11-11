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
                    <li class="breadcrumb-item active text-muted" aria-current="page"><?= !empty($this->lang->line('return_policy')) ? str_replace('\\', '', $this->lang->line('return_policy')) : 'Return Policy' ?></li>
                </ol>
            </nav>
            <!-- /nav -->
        </div>
        <!-- /.container -->
    </section>
</div>
<!-- end breadcrumb -->

<section class="container main-content mb-15 my-4">
    <div class="text-center">
        <h1 class="display-2"><?= !empty($this->lang->line('return_policy')) ? str_replace('\\', '', $this->lang->line('return_policy')) : 'Return Policy' ?></h1>
    </div>
    <div class="text-justify">
        <div class="hrDiv">
            <p>
                <?= $return_policy ?>
            </p>
        </div>
    </div>
</section>

<!-- breadcrumb -->
<div class="content-wrapper deeplink_wrapper">
    <section class="wrapper bg-soft-grape">
        <div class="container py-3 py-md-5">
            <nav class="d-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('my-account/profile') ?>" class="text-decoration-none"><?= !empty($this->lang->line('dashboard')) ? str_replace('\\', '', $this->lang->line('dashboard')) : 'Dashboard' ?></a></li>
                    <?php if (isset($right_breadcrumb) && !empty($right_breadcrumb)) {
                        foreach ($right_breadcrumb as $row) {
                    ?>
                            <li class="breadcrumb-item"><?= $row ?></li>
                    <?php }
                    } ?>
                    <li class="breadcrumb-item active text-muted" aria-current="page"><?= !empty($this->lang->line('notification')) ? str_replace('\\', '', $this->lang->line('notification')) : 'Notifications' ?></li>
                </ol>
            </nav>
            <!-- /nav -->
        </div>
        <!-- /.container -->
    </section>
</div>
<!-- end breadcrumb -->

<!-- <section class="my-account-section">
    <div class="container mb-15">
        <div class="col-md-12 mt-12 mb-3">
            
        </div>
        <div class="row m5">
            <div class="col-md-4">
                <?php $this->load->view('front-end/' . THEME . '/pages/my-account-sidebar') ?>
            </div> -->
<section class="my-account-section">
    <div class="container mb-15">
        <div class="my-8">
            <?php $this->load->view('front-end/' . THEME . '/pages/dashboard') ?>
        </div>
        <div class=" col-12">
            <div class=' border-0'>
                <div class="card-header bg-white">
                    <h1 class="h4"><?= !empty($this->lang->line('notification')) ? str_replace('\\', '', $this->lang->line('notification')) : 'Notifications' ?></h1>
                </div>
                <hr class="mt-5 mb-5">
                <div class="card-body">
                    <table class='' data-toggle="table" data-url="<?= base_url('admin/Notification_settings/get_notification_list') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-export-types='["txt","excel"]' data-query-params="queryParams">
                        <thead class="thead-light">
                            <tr>
                                <th data-field="id" data-sortable="true"><?= !empty($this->lang->line('id')) ? str_replace('\\', '', $this->lang->line('id')) : 'ID' ?></th>
                                <th data-field="full_notification" data-sortable="true"><?= !empty($this->lang->line('notification')) ? str_replace('\\', '', $this->lang->line('notification')) : 'Notifications' ?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

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
                    <li class="breadcrumb-item active text-muted" aria-current="page"><?= !empty($this->lang->line('orders')) ? str_replace('\\', '', $this->lang->line('orders')) : 'Orders' ?></li>
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
                <? //php $this->load->view('front-end/' . THEME . '/pages/my-account-sidebar') 
                ?>
            </div> -->
<section class="my-account-section">
    <div class="container mb-15">
        <div class="my-8">
            <?php $this->load->view('front-end/' . THEME . '/pages/dashboard') ?>
        </div>
        <div class="col-12">
            <div class="card-header bg-white">
                <h1 class="h4"><?= !empty($this->lang->line('orders')) ? str_replace('\\', '', $this->lang->line('orders')) : 'Orders' ?></h1>
            </div>

            <?php if (!isset($orders['order_data']) && empty($orders['order_data']) || $orders['order_data'] == []) { ?>

                <div class="align-items-center d-flex flex-column">
                    <img src="<?= base_url('assets/front_end/modern/img/empty-orders.webp') ?>" alt="Empty Orders" class="w-23" />
                    <h1 class="h2 text-center"><?= !empty($this->lang->line('no_orders_found')) ? str_replace('\\', '', $this->lang->line('no_orders_found')) : 'No Order placed Yet.' ?></h1>
                </div>
            <?php } ?>

            <hr class="mt-4 mb-4">
            <div class="card-body orders-section">
                <?php

                foreach ($orders['order_data'] as $row) {
                ?>
                    <div class=" border-0">
                        <div class="card mb-2">
                            <div class="card-header bg-white p-2">
                                <div class="d-flex justify-content-between">
                                    <div class="col">
                                        <p class="text-muted"> <?= !empty($this->lang->line('order_id')) ? str_replace('\\', '', $this->lang->line('order_id')) : 'Order ID' ?> <span class="font-weight-bold text-dark"> : <?= $row['id'] ?></span></p>
                                        <p class="text-muted"> <?= !empty($this->lang->line('place_on')) ? str_replace('\\', '', $this->lang->line('place_on')) : 'Place On' ?> <span class="font-weight-bold text-dark"> : <?= $row['date_added'] ?></span> </p>
                                        <!-- <?php if ($row['otp'] != 0) { ?>
                                                <p class="text-muted"> <?= !empty($this->lang->line('otp')) ? str_replace('\\', '', $this->lang->line('otp')) : 'OTP' ?> <span class="font-weight-bold text-dark"> : <?= $row['otp'] ?></span> </p>
                                            <?php } ?> -->
                                    </div>
                                    <div class="flex-col my-auto d-flex gap-2">
                                        <!-- <h6 class=""> -->
                                        <a href="<?= base_url('my-account/order-details/' . $row['id']) ?>" class='btn btn-outline-primary btn-sm'><?= !empty($this->lang->line('view_details')) ? str_replace('\\', '', $this->lang->line('view_details')) : 'View Details' ?></a>
                                        <!-- </h6> -->

                                        <?php
                                        $items = $row["order_items"];
                                        $variants = "";
                                        $qty = "";
                                        foreach ($items as $item) {
                                            if ($variants != "") {
                                                $variants .= ",";
                                                $qty .= ",";
                                            }
                                            $variants .= $item["product_variant_id"];
                                            $qty .= $item["quantity"];
                                        }

                                        ?>
                                        <button class="btn btn-info btn-sm reorder-btn" data-variants="<?= $variants ?>" data-quantity="<?= $qty ?>"><?= !empty($this->lang->line('reorder')) ? str_replace('\\', '', $this->lang->line('reorder')) : 'Reorder' ?></button>

                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <div class="media flex-column flex-sm-row">
                                    <div class="media-body ">
                                        <?php

                                        foreach ($row['order_items'] as $key => $item) {  ?>
                                            <h5 class="bold mt-1 mb-0"><?= ($key + 1) . '. ' . $item['name'] ?></h5>
                                            <?php
                                            if (!empty($item['variant_values'])) {
                                                $values = explode(', ', $item['variant_values']);
                                                $attributes = explode(', ', $item['attr_name']);
                                                // Initialize an empty string to store the final output
                                                $output = '';
                                                // Iterate through both arrays simultaneously
                                                foreach ($attributes as $key => $attribute) {
                                                    // Append the attribute name and corresponding value to the output string
                                                    $output .= '<p class="mb-0 text-dark">' . $attribute . ': ' . $values[$key] . '</p>';
                                                    // Add line break if it's not the last attribute
                                                    if ($key < count($attributes) - 1) {
                                                        $output .= ",";
                                                    }
                                                }

                                            ?>
                                                <div class="d-flex gap-2 mb-0 text-dark"><?= $output ?></div>
                                            <?php } ?>
                                            <p class="text-muted"> <?= !empty($this->lang->line('quantity')) ? str_replace('\\', '', $this->lang->line('quantity')) : 'Quantity' ?> : <?= $item['quantity'] ?></p>
                                            <div class="col-md-12 pl-0 product-rating-small mt-n4" dir="ltr">
                                                <input id="input" name="rating" class="rating rating-loading d-none" data-size="xs" value="<?= $item['product_rating'] ?>" data-show-clear="false" data-show-caption="false" readonly>
                                            </div>
                                            <?php if ($item['otp'] != 0) { ?>
                                                <p class="text-muted"> <?= !empty($this->lang->line('otp')) ? str_replace('\\', '', $this->lang->line('otp')) : 'OTP' ?> <span class="font-weight-bold text-dark"> : <?= $item['otp'] ?></span> </p>
                                            <?php } ?>
                                        <?php } ?>
                                        <h4 class="mt-3 mb-4 bold"> <span class="mt-5"><i><?= $settings['currency'] ?></i></span> <?= number_format($row['final_total'], 2) ?> <span class="small text-muted"> <?= !empty($this->lang->line('via')) ? str_replace('\\', '', $this->lang->line('via')) : 'via' ?> (<?= $row['payment_method'] ?>) </span></h4>
                                    </div>
                                    <?php if (count($row['order_items']) == 1) { ?>
                                        <img class="align-self-center img-fluid logo-fit" src="<?= $row['order_items'][0]['image_sm'] ?>" width="180 " height="180">
                                    <?php } ?>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                <?php } ?>
                <div class="text-center">
                    <?= $links ?>
                </div>
            </div>
            <!-- </div> -->
        </div>
    </div>
</section>

<!-- </div>
    </div>
</section> -->
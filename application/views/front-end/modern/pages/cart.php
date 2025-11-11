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
                    <li class="breadcrumb-item active text-muted" aria-current="page"><?= !empty($this->lang->line('cart')) ? str_replace('\\', '', $this->lang->line('cart')) : 'Cart' ?></li>
                </ol>
            </nav>
            <!-- /nav -->
        </div>
        <!-- /.container -->
    </section>
</div>
<!-- end breadcrumb -->

<div class="container mb-15">
    <div class="row">
        <div class="col-xl-8 mt-5 bg-white">
            <div class="cart-table-wrapper">
                <?php
                $disabled = (empty($cart['sub_total']) || $cart['sub_total'] == '0' || $cart['sub_total'] == 0) ? 'disabled' : '';

                ?>
                <div class="text-right">
                    <button name="clear_cart" id="clear_cart" class="btn btn-outline-primary btn-xs rounded-pill mt-3 mb-4" <?= $disabled ?>><?= !empty($this->lang->line('clear_cart')) ? str_replace('\\', '', $this->lang->line('clear_cart')) : 'Clear Cart' ?></button>
                </div>
                <table id="cart_item_table" class="table table-responsive table-cart-product shopping-cart">
                    <thead>
                        <tr>
                            <th class="ps-0 w-25">
                                <div class="h4 mb-0 text-start"><?= !empty($this->lang->line('product')) ? str_replace('\\', '', $this->lang->line('product')) : 'Product' ?></div>
                            </th>
                            <th>
                                <div class="h4 mb-0"><?= !empty($this->lang->line('price')) ? str_replace('\\', '', $this->lang->line('price')) : 'Price' ?></div>
                            </th>
                            <th>
                                <div class="h4 mb-0"><?= !empty($this->lang->line('tax')) ? str_replace('\\', '', $this->lang->line('tax')) : 'Tax' ?>(%)</div>
                            </th>
                            <th>
                                <div class="h4 mb-0"><?= !empty($this->lang->line('quantity')) ? str_replace('\\', '', $this->lang->line('quantity')) : 'Quantity' ?></div>
                            </th>
                            <th>
                                <div class="h4 mb-0"><?= !empty($this->lang->line('subtotal')) ? str_replace('\\', '', $this->lang->line('subtotal')) : 'Subtotal' ?></div>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $key => $row) {

                            //calculate multiple tax

                            // Check if tax_percentage is a string and not empty
                            if (isset($row['tax_percentage']) && !empty($row['tax_percentage']) && is_string($row['tax_percentage'])) {
                                // Calculate multiple tax
                                $tax_percentage = explode(',', $row['tax_percentage']);
                                $total_tax = array_sum(array_map('floatval', $tax_percentage)); // Ensure values are treated as floats
                            } else {
                                $tax_percentage = [];
                                $total_tax = 0;
                            }

                            if (isset($row['qty']) && $row['qty'] != 0) {
                                $price = $row['special_price'] != '' && $row['special_price'] != null && $row['special_price'] > 0 ? $row['special_price'] : $row['price'];
                        ?>
                                <tr class="cart-product-desc-list">
                                    <td class="option text-start d-flex flex-row align-items-center ps-0" title="<?= $row['name']; ?>">
                                        <figure class="rounded cart-img">
                                            <a href="<?= base_url('products/details/' . $row['slug']) ?>">
                                                <img src="<?= $row['image'] ?>" alt="<?= $row['name']; ?>" class="image-fit" /></a>
                                        </figure>
                                        <div class="id">
                                            <input type="hidden" name="<?= 'id[' . $key . ']' ?>" id="id" value="<?= $row['id'] ?>">
                                        </div>
                                        <div class="w-100 ms-4">
                                            <h3 class="post-title h6 lh-xs mb-1" title="<?= $row['name']; ?>">
                                                <a class="text-decoration-none text-dark" href="<?= base_url('products/details/' . $row['slug']) ?>" target="_blank">
                                                    <?= output_escaping(str_replace('\r\n', '&#13;&#10;', $row['name'])); ?>
                                                </a>
                                                <?php if (!empty($row['product_variants'])) { ?>
                                                    <br><?= str_replace(',', ' | ', $row['product_variants'][0]['variant_values']) ?>
                                                <?php } ?>
                                            </h3>
                                        </div>

                                    </td>
                                    <td>
                                        <p class="price"><span class="amount"><?= $settings['currency'] . '' . number_format($price, 2) ?></span></p>
                                    </td>
                                    <td>
                                        <?= isset($row['tax_percentage']) && !empty($row['tax_percentage']) ? $row['tax_percentage'] : '-' ?>
                                    </td>
                                    <td class="item-quantity">
                                        <div class="num-block skin-2 product-quantity">
                                            <?php $check_current_stock_status = validate_stock([$row['id']], [$row['qty']]); ?>
                                            <?php if (isset($check_current_stock_status['error'])  && $check_current_stock_status['error'] == TRUE) { ?>
                                                <div><span class='text text-danger'> <?= !empty($this->lang->line('out_of_stock')) ? str_replace('\\', '', $this->lang->line('out_of_stock')) : 'Out Of Stock' ?> </span></div>
                                            <?php } else { ?>
                                                <div class="num-in form-control d-flex align-items-center">
                                                    <?php $price = $row['special_price'] != '' && $row['special_price'] != null && $row['special_price'] > 0 ? $row['special_price'] : $row['price']; ?>
                                                    <span class="minus dis" data-min="<?= (isset($row['minimum_order_quantity']) && !empty($row['minimum_order_quantity'])) ? $row['minimum_order_quantity'] : 1 ?>" data-step="<?= (isset($row['minimum_order_quantity']) && !empty($row['quantity_step_size'])) ? $row['quantity_step_size'] : 1 ?>"></span>
                                                    <input type="text" class="in-num itemQty" data-page="cart" data-id="<?= $row['id']; ?>" value="<?= $row['qty'] ?>" data-price="<?= $price ?>" data-step="<?= (isset($row['minimum_order_quantity']) && !empty($row['quantity_step_size'])) ? $row['quantity_step_size'] : 1 ?>" data-min="<?= (isset($row['minimum_order_quantity']) && !empty($row['minimum_order_quantity'])) ? $row['minimum_order_quantity'] : 1 ?>" data-max="<?= (isset($row['total_allowed_quantity']) && !empty($row['total_allowed_quantity'])) ? $row['total_allowed_quantity'] : '' ?>">
                                                    <span class="plus" data-max="<?= (isset($row['total_allowed_quantity']) && !empty($row['total_allowed_quantity'])) ? $row['total_allowed_quantity'] : '0' ?> " data-step="<?= (isset($row['minimum_order_quantity']) && !empty($row['quantity_step_size'])) ? $row['quantity_step_size'] : 1 ?>"></span>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td class="text-center p-0 total-price"><span class="product-line-price"> <?= $settings['currency'] . '' . number_format(($row['qty'] * $price), 2) ?></span></td>
                                    <td class="d-flex gap-2 align-items-center border-0">
                                        <a class="product-removal link_cursor">
                                            <i class="remove-product uil uil-trash-alt fs-23 text-danger" name="remove_inventory" id="remove_inventory" data-id="<?= $row['id']; ?>" title="<?= !empty($this->lang->line('remove_from_cart')) ? str_replace('\\', '', $this->lang->line('remove_from_cart')) : 'Remove From Cart' ?>"></i>
                                        </a>
                                        <a class="save-for-later remove-product link_cursor" data-id="<?= $row['id']; ?>">
                                            <i class="uil uil-bag-alt fs-23 text-blue" title="<?= !empty($this->lang->line('save_for_later')) ? str_replace('\\', '', $this->lang->line('save_for_later')) : 'Save For Later' ?>"></i>
                                        </a>

                                    </td>
                                </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>
            </div>

            <?php
            if ($cart['quantity'] == 0) { ?>
                <div>
                    <h1 class="h2 text-center"><?= !empty($this->lang->line('no_items_found')) ? str_replace('\\', '', $this->lang->line('no_items_found')) : 'No Items Added Yet In Cart.' ?></h1>
                </div>
            <?php } ?>

            <?php if (!empty($save_for_later['variant_id'])) { ?>
                <div class="cart-table-wrapper">
                    <table class="table table-responsive-sm table-cart-product shopping-cart">
                        <h4 class="h4"><?= !empty($this->lang->line('save_for_later')) ? str_replace('\\', '', $this->lang->line('save_for_later')) : 'Save For Later' ?></h1>
                            <thead>
                                <tr class="cart-product-desc-list">
                                    <th class="ps-0 w-25">
                                        <div class="h4 mb-0 text-start"><?= !empty($this->lang->line('product')) ? str_replace('\\', '', $this->lang->line('product')) : 'Product' ?></div>
                                    </th>
                                    <th>
                                        <div class="h4 mb-0"><?= !empty($this->lang->line('price')) ? str_replace('\\', '', $this->lang->line('price')) : 'Price' ?></div>
                                    </th>
                                    <th>
                                        <div class="h4 mb-0"><?= !empty($this->lang->line('tax')) ? str_replace('\\', '', $this->lang->line('tax')) : 'Tax' ?>(%)</div>
                                    </th>
                                    <th>
                                        <div class="h4 mb-0"><?= !empty($this->lang->line('quantity')) ? str_replace('\\', '', $this->lang->line('quantity')) : 'Quantity' ?></div>
                                    </th>
                                    <th>
                                        <div class="h4 mb-0"><?= !empty($this->lang->line('subtotal')) ? str_replace('\\', '', $this->lang->line('subtotal')) : 'Subtotal' ?></div>
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($save_for_later as $key => $row) {
                                    if (isset($row['qty']) && $row['qty'] >= 0) {
                                        $price = $row['special_price'] != '' && $row['special_price'] != null && $row['special_price'] > 0 && $row['special_price'] < $row['price'] ? $row['special_price'] : $row['price'];
                                ?>
                                        <tr class="cart-product-desc-list">
                                            <td class="option text-start d-flex flex-row align-items-center ps-0" title="<?= $row['name']; ?>">
                                                <figure class="rounded cart-img">
                                                    <a href="<?= base_url('products/details/' . $row['slug']) ?>">
                                                        <img src="<?= ($row['image']) ?>" alt="<?= $row['name']; ?>" class="image-fit" /></a>
                                                </figure>
                                                <div class="id">
                                                    <input type="hidden" name="<?= 'id[' . $key . ']' ?>" id="id" value="<?= $row['id'] ?>">
                                                </div>
                                                <div class="w-100 ms-4">
                                                    <h3 class="post-title h6 lh-xs mb-1" title="<?= $row['name']; ?>">
                                                        <a class="text-decoration-none text-dark" href="<?= base_url('products/details/' . $row['slug']) ?>" target="_blank">
                                                            <?= output_escaping(str_replace('\r\n', '&#13;&#10;', $row['name'])); ?>
                                                        </a>
                                                        <?php if (!empty($row['product_variants'])) { ?>
                                                            <br><?= str_replace(',', ' | ', $row['product_variants'][0]['variant_values']) ?>
                                                        <?php } ?>
                                                    </h3>
                                                    <button class="btn remove-product btn-outline-warning move-to-cart btn-xs" data-id="<?= $row['id']; ?>" data-qty="<?= $row['qty'] ?>"><?= !empty($this->lang->line('move_to_cart')) ? str_replace('\\', '', $this->lang->line('move_to_cart')) : 'Move to cart' ?></button>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="price"><span class="amount"><?= $settings['currency'] . '' . number_format($price, 2) ?></span></p>
                                            </td>
                                            <td>
                                                <?= isset($row['tax_percentage']) && !empty($row['tax_percentage']) ? $row['tax_percentage'] : '-' ?>
                                            </td>
                                            <td class="item-quantity">
                                                <div class="num-block skin-2 product-quantity">
                                                    <?php $check_current_stock_status = validate_stock([$row['id']], [$row['qty']]); ?>
                                                    <?php if (isset($check_current_stock_status['error'])  && $check_current_stock_status['error'] == TRUE) { ?>
                                                        <div><span class='text text-danger'> Out of Stock </span></div>
                                                    <?php } else { ?>
                                                        <div class="num-in form-control d-flex align-items-center move-to-cart-qty">
                                                            <?php $price = $row['special_price'] != '' && $row['special_price'] != null && $row['special_price'] > 0 ? $row['special_price'] : $row['price']; ?>
                                                            <span class="minus dis" data-min="<?= (isset($row['minimum_order_quantity']) && !empty($row['minimum_order_quantity'])) ? $row['minimum_order_quantity'] : 1 ?>" data-step="<?= (isset($row['minimum_order_quantity']) && !empty($row['quantity_step_size'])) ? $row['quantity_step_size'] : 1 ?>"></span>
                                                            <input type="text" class="in-num itemQty" data-page="cart" data-id="<?= $row['id']; ?>" value="<?= $row['qty'] ?>" data-price="<?= $price ?>" data-step="<?= (isset($row['minimum_order_quantity']) && !empty($row['quantity_step_size'])) ? $row['quantity_step_size'] : 1 ?>" data-min="<?= (isset($row['minimum_order_quantity']) && !empty($row['minimum_order_quantity'])) ? $row['minimum_order_quantity'] : 1 ?>" data-max="<?= (isset($row['total_allowed_quantity']) && !empty($row['total_allowed_quantity'])) ? $row['total_allowed_quantity'] : '' ?>">
                                                            <span class="plus" data-max="<?= (isset($row['total_allowed_quantity']) && !empty($row['total_allowed_quantity'])) ? $row['total_allowed_quantity'] : '0' ?> " data-step="<?= (isset($row['minimum_order_quantity']) && !empty($row['quantity_step_size'])) ? $row['quantity_step_size'] : 1 ?>"></span>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                            <td class="text-muted p-0 total-price"><span class="product-line-price"> <?= $settings['currency'] . '' . number_format(($row['qty'] * $price), 2) ?></span>
                                            </td>
                                            <td class="pe-0">
                                                <a class="product-removal link_cursor">
                                                    <i class="remove-product uil uil-trash-alt fs-23 text-danger" name="remove_inventory" id="remove_inventory" data-id="<?= $row['id']; ?>" data-is-save-for-later="1"></i>
                                                </a>
                                            </td>
                                        </tr>
                                <?php }
                                } ?>
                            </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
        <div class="col-lg-4 mt-10">
            <h3 class="mb-4"><?= !empty($this->lang->line('cart_total')) ? str_replace('\\', '', $this->lang->line('cart_total')) : 'Cart total' ?></h3>
            <div class="table-responsive">
                <table class="table table-order">
                    <tbody>
                        <tr>
                            <td class="ps-0"><strong class="text-dark"><?= !empty($this->lang->line('subtotal')) ? str_replace('\\', '', $this->lang->line('subtotal')) : 'Subtotal' ?></strong></td>
                            <td class="pe-0 text-end">
                                <p class="price"><?= $settings['currency'] . ' ' . number_format($cart['sub_total'], 2) ?></p>
                            </td>
                        </tr>
                        <?php if (!empty($cart['tax_percentage'])) { ?>
                            <tr class="cart-product-tax d-none">
                                <td class="ps-0"><strong class="text-dark"><?= !empty($this->lang->line('tax')) ? str_replace('\\', '', $this->lang->line('tax')) : 'Tax' ?> (<?= $cart['tax_percentage'] ?>%)</strong></td>
                                <td class="pe-0 text-end">
                                    <p class="price text-red"><?= $settings['currency'] . ' ' . number_format($cart['tax_amount'], 2) ?></p>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php $delivery_charge = !empty($cart['sub_total']) ? number_format($cart['delivery_charge'], 2) : 0 ?>

                        <?php $total = !empty($cart['sub_total']) ? number_format($cart['overall_amount'] - $cart['delivery_charge'], 2) : 0 ?>
                        <tr class="total-price">
                            <td class="ps-0"><strong class="text-dark"><?= !empty($this->lang->line('total')) ? str_replace('\\', '', $this->lang->line('total')) : 'Total' ?></strong></td>
                            <td class="pe-0 text-end">
                                <p class="price text-dark fw-bold"><?= $settings['currency'] . '<span id="final_total"> ' . $total . '</span>' ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php $disabled = empty($cart['sub_total']) ? 'disabled' : '';
            $checkout_url = !$disabled ? base_url('cart/checkout') : '#';
            ?>
            <div class="checkout-method">
                <?php if ($disabled): ?>
                    <span>
                        <button class="btn btn-primary rounded-pill w-100 mt-4 btn-sm" disabled>
                            <?= $this->lang->line('go_to_checkout') ?: 'Go To Checkout' ?>
                        </button>
                    </span>
                <?php else: ?>
                    <a href="<?= $checkout_url ?>" id="checkout">
                        <button class="btn btn-primary rounded-pill w-100 mt-4 btn-sm">
                            <?= $this->lang->line('go_to_checkout') ?: 'Go To Checkout' ?>
                        </button>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
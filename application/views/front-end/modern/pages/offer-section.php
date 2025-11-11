<?php $offers =  get_offers();
foreach ($offers as $row) { ?>
    <img class="img-fluid" src="<?= base_url(rawurlencode($row['image'])) ?>">
<?php } ?>
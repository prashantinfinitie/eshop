<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Manage Categories</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('affiliate/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="container-fluid py-4">


                        <!-- Categories Grid -->
                        <div class="row">
                            <!-- Skincare -->
                            <?php foreach ($affiliate_categories as $affiliate_category) { ?>

                                <div class="col-lg-2 col-md-4 col-sm-6">
                                    <div class="category-card fade-in">
                                        <a href="<?= base_url() . 'affiliate/product/get_categories_products/' . $affiliate_category['id'] ?>">
                                            <img src="<?= base_url($affiliate_category['image']) ?>" alt="<?= $affiliate_category['name'] ?>" class="category-image">
                                            <div class="category-overlay">
                                                <div class="category-title"><?= $affiliate_category['name'] ?></div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>




<style>



</style>


<script>
    // Add click functionality to category cards
    $('.category-card').click(function() {
        const categoryName = $(this).find('.category-title').text() || 'Special Category';
        console.log('Category clicked:', categoryName);
        // You can add navigation or modal functionality here
    });

    // Add staggered animation to cards
    $(document).ready(function() {
        $('.category-card').each(function(index) {
            $(this).css('animation-delay', (index * 0.1) + 's');
        });
    });
</script>
<script src="<?= THEME_ASSETS_URL . 'js/eshop-bundle-js.js' ?>"></script>

<!-- Firebase.js -->
<script src="<?= THEME_ASSETS_URL . 'js/firebase-app.js' ?>"></script>
<script src="<?= THEME_ASSETS_URL . 'js/firebase-auth.js' ?>"></script>
<script src="<?= THEME_ASSETS_URL . 'js/firebase-firestore.js' ?>"></script>
<!-- jsSocial -->
<script src="<?= THEME_ASSETS_URL . 'js/jquery.jssocials.min.js' ?>"></script>
<script src="<?= THEME_ASSETS_URL . 'js/dropzone.js' ?>"></script>
<script src="<?= THEME_ASSETS_URL . 'js/deeplink.js' ?>"></script>
<script src="<?= base_url('firebase-config.js') ?>"></script>

<?php if ($this->session->flashdata('message')) { ?>
    <script>
        Toast.fire({
            icon: '<?= $this->session->flashdata('message_type'); ?>',
            title: "<?= $this->session->flashdata('message'); ?>"
        });
    </script>
<?php } ?>

<?php
if (isset($data_json_ld) && !empty($data_json_ld)): ?>
    <script type="application/ld+json">
        <?= $data_json_ld ?>
    </script>
<?php else: ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "url": "<?= base_url(); ?>",
            "potentialAction": {
                "@type": "SearchAction",
                "target": {
                    "@type": "EntryPoint",
                    "urlTemplate": "<?= base_url() . '/home/get_products/?q={search_term_string}'; ?>"
                },
                "query-input": "required name=search_term_string"
            }
        }
    </script>
<?php endif; ?>

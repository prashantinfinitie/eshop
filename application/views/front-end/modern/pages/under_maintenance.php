<section class='maintenance_mode'>
    <div class="container mb-15">
        <div class="row">
            <div class="col">
                <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>
                <div class="d-flex justify-content-center align-items-center">

                    <dotlottie-player class="under_maintenance" src="<?= base_url('assets/maintenance_1.json') ?>"
                        background="transparent" speed="1" loop autoplay></dotlottie-player>
                </div>
                
                    <h1 data-shadow='Website is under maintenance mode, please check after some time' class="text-center"><?= isset($web_maintenance_mode['message_for_web']) ? $web_maintenance_mode['message_for_web'] : 'Website is under maintenance mode, please check after some time' ?></h1>

            </div>
        </div>
    </div>
</section>
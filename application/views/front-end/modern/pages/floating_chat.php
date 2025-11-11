<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('front-end/' . THEME . '/include-css', $data); ?>
    <script type="module" src="<?= base_url('assets/front_end/modern/js/components-chat-box.js') ?>"></script>
    <script src="<?= THEME_ASSETS_URL . 'js/select2.full.min.js' ?>"></script>
    <script src="<?= THEME_ASSETS_URL . 'js/stisla.js' ?>"></script>

    <!-- Markdown -->
    <script src="<?= THEME_ASSETS_URL . 'js/Markdown.Converter.js' ?>"></script>
    <script src="<?= THEME_ASSETS_URL . 'js/Markdown.Sanitizer.js' ?>"></script>
    <script src="<?= THEME_ASSETS_URL . 'js/Markdown.Editor.js' ?>"></script>
    <?php $this->load->view('front-end/' . THEME . '/include-script'); ?>
</head>

<body>
    <section class="chat-section">
        <div class="main-content deeplink_wrapper">

            <div class="container mt-2">
                <h1><?= !empty($this->lang->line('chat')) ? str_replace('\\', '', $this->lang->line('chat')) : 'Chat'; ?></h1>
            </div>
            <div id="floating_chat_view" class="h-100 mt-0">

                <div class="row h-100">

                    <div class="col-lg-2 floating-chat-users p-0">
                        <div class="card chat-theme-light chat-scroll chat-min">
                            <select name="select_user_id[]" id="chat_user" class="search_user w-100" multiple data-placeholder="Type to search support team and seller" onload="multiselect()">
                                <option value=""></option>
                                <?php
                                $user_details = fetch_details('users', ['active' => 1]);
                                if (!empty($user_details)) {
                                ?>
                                    <option value="<?= $user_details[0]['id'] ?>"> <?= $user_details[0]['username'] ?></option>
                                <?php
                                }

                                ?>
                            </select>
                            <div id="add-scroll-js ">
                                <div class="card-header chat-card-header text-color mt-4">
                                    <h4 class="fw-semibold"><?= !empty($this->lang->line('personal_chat')) ? str_replace('\\', '', $this->lang->line('personal_chat')) : 'Personal Chat'; ?></h4>
                                </div>
                                <div class="chat-card-body">
                                    <ul class="list-unstyled list-unstyled-border chat-list-unstyled-border">
                                        <?php if (!empty($users)) {

                                            foreach ($users as $user) {
                                                if ($user['id'] == $_SESSION['user_id']) {
                                        ?>
                                                    <li class="media">
                                                        <div class="media-body">
                                                            <div class="chat-person" data-picture="" data-type="person" data-id="<?= $user['opponent_user_id'] ?>"><i class="<?= ($user['is_online'] == 1) ? 'fa fa-circle text-success' : 'fa fa-circle'; ?> "></i> <?= $user['opponent_username'] ?> (You)</div>
                                                        </div>
                                                    </li>
                                            <?php }
                                            }
                                        } else { ?>
                                            <p class="card-body p-0 px-5 text-muted"><?= !empty($this->lang->line('it_seems_there_are_no_chats_available_at_the_moment')) ? str_replace('\\', '', $this->lang->line('it_seems_there_are_no_chats_available_at_the_moment')) : 'It seems there are no chats available at the moment'; ?></p>
                                        <?php } ?>


                                        <?php if (!empty($users)) {
                                            foreach ($users as $user) {
                                                if (isset($user['id']) && !empty($user['id']) && $user['id'] != '' && $user['id'] != $_SESSION['user_id']) { ?>
                                                    <li class="media">
                                                        <div class="media-body">
                                                            <div data-unread_msg="<?= $user['unread_msg'] ?>" class="chat-person <?= ($user['unread_msg'] > 0) ? 'new-msg-rcv' : ''; ?>" data-picture="<?= $user['picture'] ?>" data-type="person" data-id="<?= $user['opponent_user_id'] ?>"><i class="<?= ($user['is_online'] == 1) ? 'fa fa-circle text-success' : 'fa fa-circle'; ?> "></i> <?= $user['opponent_username'] ?>
                                                                <?= ($user['unread_msg'] > 0) ? (($user['unread_msg'] > 9) ? '<div class="badge-chat">9 +</div>' : '<div class="badge-chat">' . $user['unread_msg'] . '</div>') : ''; ?>
                                                            </div>
                                                        </div>
                                                    </li>
                                        <?php }
                                            }
                                        } ?>
                                    </ul>
                                </div>
                                <div class="card-header chat-card-header d-flex text-color">
                                    <h4 class="fw-semibold"><?= !empty($this->lang->line('support_chat')) ? str_replace('\\', '', $this->lang->line('support_chat')) : 'Support Team '; ?></h4>
                                </div>
                                <div class="chat-card-body">
                                    <ul class="list-unstyled list-unstyled-border chat-list-unstyled-border">

                                        <?php if (!empty($supporters)) {

                                            foreach ($supporters as $supporter) {
                                                $date = strtotime('now');
                                                $to_id = $this->session->userdata('user_id');

                                                if ($to_id == $supporter['user_permission_id']) {
                                                    $supporter['is_online'] = 1;
                                                } else {
                                                    if ($supporter['last_online'] > $date) {
                                                        $supporter['is_online'] = 1;
                                                    } else {
                                                        $supporter['is_online'] = 0;
                                                    }
                                                }
                                        ?>

                                                <li class="media">
                                                    <div class="media-body">
                                                        <div class="chat-person" data-id="<?= $supporter['userto_id'] ?>" data-type="person">
                                                            <i class="<?= ($supporter['is_online'] == 1) ? 'fa fa-circle text-success' : 'fa fa-circle'; ?> "></i> <?= $supporter['username'] ?>
                                                        </div>
                                                    </div>
                                                </li>

                                            <?php }
                                        } else { ?>
                                            <p class="card-body p-0 px-5 text-muted"><?= !empty($this->lang->line('no_support_team_available')) ? str_replace('\\', '', $this->lang->line('no_support_team_available')) : 'No support team available'; ?> </p>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-8 col-lg-9 d-none" id="chat_area_wait">
                    </div>
                    <!-- <div class="col-12 col-sm-8 col-lg-9 d-none" id="you_not_in_group">

                <div class="pricing pricing-highlight chat-min">
                    <div class="pricing-padding">
                        <div class="pricing-price">
                            <div>You are not in this group</div>
                            <div>Join group and access all the details.</div>
                        </div>
                        <a class="btn btn-success text-white" id="you_not_in_group_btn">Join Group</a>
                    </div>
                </div>

            </div> -->
                    <div class="col-lg-6 d-none p-0" id="chat_area">
                        <div class="card chat-box chat-theme-light chat-min " id="mychatbox2">
                            <div class="align-items-center card-header chat-card-header d-flex">
                                <a href="#" class="px-2 floating-chat-back-btn"><i class="fa fa-arrow-left"></i></a>
                                <div class="mr-3" id="chat-avtar-main">#</div>
                                <div class="media-body">
                                    <div class="mt-0 mb-1 font-weight-bold text-color" id="chat_title"></div>
                                    <div class="text-small font-600-bold" id="chat_online_status"></div>
                                </div>
                                <!-- <form class="card-header-form">
                            <div class="input-group">
                                <input type="text" name="search" id="modal-search-msg" class="form-control chat-search-box" placeholder="Search">

                            </div>
                        </form> -->
                            </div>
                            <div id="chat-box-content" class="chat-bg card-body chat-scroll chat-content">
                                <div class="chat_loader">Loading...</div>
                            </div>
                            <div class="card-body d-none" id="chat-dropbox">
                                <div class="dropzone" id="myAlbum"></div>
                                <div class="text-center mt-3">
                                    <button class="btn btn-danger shadow-none" onclick="closeDropZone();"><?= !empty($this->lang->line('close')) ? str_replace('\\', '', $this->lang->line('close')) : 'Close'; ?>
                                    </button>
                                </div>
                            </div>
                            <div class="form-control theme-inputs d-none" id="chat-input-textarea-result"></div>
                            <div class="card-footer chat-form p-1">
                                <form id="chat-form2" autocomplete="off">
                                    <div class="row">
                                        <div class="input-group">
                                            <input type="hidden" id="opposite_user_id" name="opposite_user_id" value="">
                                            <input type="hidden" id="my_user_id" name="my_user_id" value="<?= $_SESSION['user_id'] ?>" data-picture="">
                                            <input type="hidden" id="chat_type" name="chat_type" value="">
                                            <textarea class="form-control theme-inputs mb-n5" id="chat-input-textarea" rows="1" name="chat-input-textarea"></textarea>
                                        </div>
                                    </div>
                                    <div class="">
                                        <span class="input-group-append  mb-n1">
                                            <div class="form-group">

                                                <a class="bg-success go-to-bottom-btn text-center btn-arrow">
                                                    <i class="fs-20 uil uil-arrow-down"></i>
                                                </a>

                                                <button class="btn btn-danger btn-send-msg">
                                                    <i class="fa fa-paper-plane fs-13"></i>
                                                </button>

                                                <button class="btn-file btn btn-primary" onclick="showDropZone();">
                                                    <i class="uil uil-paperclip"></i>
                                                </button>
                                            </div>
                                        </span>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                    <!--end col-->
                </div>
            </div>

            <!--end row-->



            <div class="modal" tabindex="-1" role="dialog" id="chat-search-modal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Search</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form class="modal-part" id="modal-search-msg-part">
                                <div id="modal-title" class="d-none"><?= !empty($this->lang->line('label_search')) ? str_replace('\\', '', $this->lang->line('label_search')) : 'Search'; ?></div>
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa fa-search"></i>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control" name="in-chat-search" id="in-chat-search">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-none" id="show-search-result">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4><?= !empty($this->lang->line('label_search_result')) ? str_replace('\\', '', $this->lang->line('label_search_result')) : 'Search Result'; ?></h4>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-unstyled list-unstyled-border" id="search-result">

                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
    <!-- chat -->
</body>


</html>
<script src="<?= THEME_ASSETS_URL . 'js/stisla.js' ?>"></script>
<script src="<?= THEME_ASSETS_URL . 'js/Markdown.Converter.js' ?>"></script>
<script src="<?= THEME_ASSETS_URL . 'js/Markdown.Sanitizer.js' ?>"></script>
<script src="<?= THEME_ASSETS_URL . 'js/Markdown.Editor.js' ?>"></script>

<!-- breadcrumb -->
<section class="breadcrumb-title-bar colored-breadcrumb">
    <div class="main-content responsive-breadcrumb">
        <h2><?= !empty($this->lang->line('chat')) ? str_replace('\\', '', $this->lang->line('chat')) : 'Chat' ?></h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('my-account') ?>"><?= !empty($this->lang->line('dashboard')) ? str_replace('\\', '', $this->lang->line('dashboard')) : 'Dashboard' ?></a></li>
                <li class="breadcrumb-item"><a href="#"><?= !empty($this->lang->line('chat')) ? str_replace('\\', '', $this->lang->line('chat')) : 'Chat' ?></a></li>
            </ol>
        </nav>
    </div>
</section>

<section class="my-account-section">
    <div class="main-content deeplink_wrapper">
        <div class="col-md-12 mt-5 mb-3">
            <div class="user-detail align-items-center">
                <div class="ml-3">
                    <h6 class="text-muted mb-0"><?= !empty($this->lang->line('hello')) ? str_replace('\\', '', $this->lang->line('hello')) : 'Hello' ?></h6>
                    <h5 class="mb-0"><?= $user->username ?></h5>
                </div>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-md-4">
                <?php $this->load->view('front-end/' . THEME . '/pages/my-account-sidebar') ?>
            </div>
            <div class="col-lg-2 p-0">
                <div class="card chat-theme-light chat-scroll chat-min">
                    <select name="select_user_id[]" id="chat_user" class="search_user w-100" multiple data-placeholder=" Type to search and select users" onload="multiselect()">
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
                            <h4><?= !empty($this->lang->line('personal_chat')) ? str_replace('\\', '', $this->lang->line('personal_chat')) : 'Personal Chat'; ?></h4>
                        </div>
                        <div class="chat-card-body">
                            <ul class="list-unstyled list-unstyled-border chat-list-unstyled-border">
                                <?php if (!empty($users)) {

                                    foreach ($users as $user) {
                                        if ($user['id'] == $_SESSION['user_id']) {
                                ?>
                                            <li class="media">
                                                <div class="media-body">
                                                    <div class="chat-person" data-picture="" data-type="person" data-id="<?= $user['opponent_user_id'] ?>"><i class="<?= ($user['is_online'] == 1) ? 'fas fa-circle text-success' : 'far fa-circle'; ?> "></i> <?= $user['opponent_username'] ?> (You)</div>
                                                </div>
                                            </li>
                                <?php }
                                    }
                                } ?>


                                <?php if (!empty($users)) {
                                    foreach ($users as $user) {
                                        if (isset($user['id']) && !empty($user['id']) && $user['id'] != '' &&  $user['id'] != $_SESSION['user_id']) { ?>
                                            <li class="media">
                                                <div class="media-body">
                                                    <div data-unread_msg="<?= $user['unread_msg'] ?>" class="chat-person <?= ($user['unread_msg'] > 0) ? 'new-msg-rcv' : ''; ?>" data-picture="<?= $user['picture'] ?>" data-type="person" data-id="<?= $user['opponent_user_id'] ?>"><i class="<?= ($user['is_online'] == 1) ? 'fas fa-circle text-success' : 'far fa-circle'; ?> "></i> <?= $user['opponent_username'] ?>
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
                            <h4><?= !empty($this->lang->line('support_chat')) ? str_replace('\\', '', $this->lang->line('support_chat')) : 'Support Team '; ?></h4>
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
                                                    <i class="<?= ($supporter['is_online'] == 1) ? 'fas fa-circle text-success' : 'far fa-circle'; ?> "></i> <?= $supporter['username'] ?>
                                                </div>
                                            </div>
                                        </li>

                                <?php }
                                } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8 col-lg-9" id="chat_area_wait">
            </div>
            <div class="col-lg-6 d-none p-0" id="chat_area">
                <div class="card chat-box chat-theme-light chat-min " id="mychatbox2">
                    <div class="align-items-center card-header chat-card-header d-flex">
                        <div class="mr-3" id="chat-avtar-main">#</div>
                        <div class="media-body">
                            <div class="mt-0 mb-1 font-weight-bold text-color" id="chat_title"></div>
                            <div class="text-small font-600-bold" id="chat_online_status"></div>
                        </div>

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
                    <div class="card-footer chat-form">
                        <form id="chat-form2" autocomplete="off">
                            <div class="row">
                                <div class="input-group">
                                    <input type="hidden" id="opposite_user_id" name="opposite_user_id" value="">
                                    <input type="hidden" id="my_user_id" name="my_user_id" value="<?= $_SESSION['user_id'] ?>" data-picture="">
                                    <input type="hidden" id="chat_type" name="chat_type" value="">
                                    <textarea class="form-control theme-inputs" id="chat-input-textarea" rows="1" name="chat-input-textarea"></textarea>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <span class="input-group-append ">
                                    <div class="form-group">
                                        <a class="bg-success go-to-bottom-btn text-center">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>

                                        <button class="btn btn-danger btn-send-msg">
                                            <i class="far fa-paper-plane"></i>
                                        </button>

                                        <button class="btn-file btn btn-primary" onclick="showDropZone();">
                                            <i class="fas fa-paperclip"></i>
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

        <!--end row-->
    </div>
    <!--end container-->
</section>

<div class="modal" tabindex="-1" role="dialog" id="chat-search-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= !empty($this->lang->line('search')) ? str_replace('\\', '', $this->lang->line('search')) : 'Search'; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="modal-part" id="modal-search-msg-part">
                    <div id="modal-title" class="d-none"><?= !empty($this->lang->line('search')) ? str_replace('\\', '', $this->lang->line('search')) : 'Search'; ?></div>
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-search"></i>
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

<!-- chat -->
<script type="module" src="<?= THEME_ASSETS_URL . 'js/components-chat-box.js' ?>"></script>
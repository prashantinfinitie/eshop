<!-- breadcrumb -->
<div class="content-wrapper deeplink_wrapper">
    <section class="wrapper bg-soft-grape">
        <div class="container py-3 py-md-5">
            <nav class="d-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>"
                            class="text-decoration-none"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a>
                    </li>
                    <li class="breadcrumb-item"><a href="<?= base_url('my-account/profile') ?>"
                            class="text-decoration-none"><?= !empty($this->lang->line('dashboard')) ? str_replace('\\', '', $this->lang->line('dashboard')) : 'Dashboard' ?></a>
                    </li>
                    <li class="breadcrumb-item active text-muted" aria-current="page">
                        <?= !empty($this->lang->line('support_tickets')) ? str_replace('\\', '', $this->lang->line('support_tickets')) : 'Support Tickets' ?>
                    </li>
                </ol>
            </nav>
            <!-- /nav -->
        </div>
        <!-- /.container -->
    </section>
</div>
<!-- <main> -->
<section class="my-account-section">
    <div class="container mb-15">
        <div class="my-8">
            <?php $this->load->view('front-end/' . THEME . '/pages/dashboard') ?>
        </div>
        <div class="home_faq">
            <div class="align-items-center d-flex flex-wrap justify-content-between">
                <h1 class="h4 m-0">
                    <?= !empty($this->lang->line('support_tickets')) ? str_replace('\\', '', $this->lang->line('support_tickets')) : 'Support Tickets' ?>
                </h1>
                <button type="submit" class="btn btn-sm btn-primary viewmorebtn ticket_button"
                    value="Save"><?= !empty($this->lang->line('create_a_ticket')) ? str_replace('\\', '', $this->lang->line('create_a_ticket')) : 'Create a ticket' ?></button>
            </div>
            <hr class="mt-5 mb-5">
            <div class="display_fields col-md-12 d-none">
                <form class="form-horizontal form-submit-event" id="stock_adjustment_form" method="POST"
                    enctype="multipart/form-data">
                    <select class="col-md-12 form-control" name="ticket_type_id">
                        <?php foreach ($ticket_types as $type) {
                            if (isset($product_details[0]['tax']) && $product_details[0]['tax'] == $row['id']) {
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                        ?>
                            <option id='ticket_type' value="<?= $type['id'] ?>" <?= $selected ?>><?= $type['title'] ?> </option>
                        <?php
                        } ?>
                    </select>

                    <input type="hidden" class="form-control mt-2" value=<?= $_SESSION['user_id'] ?> name="user_id"
                        id='user_id'>
                    <input type="email" class="form-control mt-2" placeholder="Email" name="email" id='email'>
                    <input type="text" class="form-control mt-2" placeholder="Subject" name="subject" id='subject'
                        required>
                    <textarea name="description" id="description" class="form-control mt-2" placeholder="Description"
                        cols="30" rows="3" required></textarea>

                    <button type="submit" class="btn btn-primary mt-2 ask_question"
                        value="Save"><?= !empty($this->lang->line('send')) ? str_replace('\\', '', $this->lang->line('send')) : 'Send' ?></button>


                </form>
            </div>
            <div class="card-body">
                <table class='' data-toggle="table" data-url="<?= base_url('tickets/get_ticket_list') ?>"
                    data-click-to-select="true" data-side-pagination="server" data-pagination="true"
                    data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true"
                    data-show-refresh="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc"
                    data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true"
                    data-query-params="transaction_query_params">
                    <thead class="thead-light">
                        <tr>
                            <th data-field="id" data-sortable="true">
                                <?= !empty($this->lang->line('id')) ? str_replace('\\', '', $this->lang->line('id')) : 'ID' ?>
                            </th>
                            <th data-field="ticket" data-sortable="false">
                                <?= !empty($this->lang->line('ticket')) ? str_replace('\\', '', $this->lang->line('ticket')) : 'Ticket' ?>
                            </th>
                            <th data-field="status" data-sortable="false">
                                <?= !empty($this->lang->line('status')) ? str_replace('\\', '', $this->lang->line('status')) : 'Status' ?>
                            </th>
                            <th data-field="assignee" data-sortable="false">
                                <?= !empty($this->lang->line('assignee')) ? str_replace('\\', '', $this->lang->line('assignee')) : 'Assignee' ?>
                            </th>
                            <th data-field="last_updated" data-sortable="false">
                                <?= !empty($this->lang->line('date')) ? str_replace('\\', '', $this->lang->line('date')) : 'Date' ?>
                            </th>
                            <th data-field="operate" data-sortable="false">
                                <?= !empty($this->lang->line('operate')) ? str_replace('\\', '', $this->lang->line('operate')) : 'Operate' ?>
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="overflow-auto">

                <?php
                foreach ($tickets as $ticket) {
                    $ticket_type = fetch_details('ticket_types', ['id' => $ticket['ticket_type_id']], 'id,title');
                    $ticket_message = fetch_details('ticket_messages', ['ticket_id' => $ticket['id']], 'ticket_id');
                    $user_type = fetch_details('ticket_messages', ['ticket_id' => $ticket['id']], 'user_type');

                    $test = '';
                    foreach ($user_type as $type) {
                        if ($type['user_type'] != 'user') {
                            $test = ($type['user_type']);
                        }
                    }
                    $count = count($ticket_message);
                    $rs = $this->db->query('select  last_updated from ticket_messages  where ticket_id =' . $ticket['id'] . ' order by last_updated desc');
                    $array = $rs->result_array();

                    if ($array[0] != '') {

                        $time = time2str($array[0]['last_updated']);
                    } else {
                        $time = '';
                    } ?>

                    <?php
                    $ticket_data = fetch_details('tickets', ['id' => $ticket['id']], '');
                    foreach ($ticket_data as $data) {
                    ?>

                        <!-- Ticket modal -->
                        <div class="modal fade" id="address-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        <div class="h4">
                                            <?= label('edit_ticket', 'Edit ticket') ?>
                                        </div>
                                        <form class="form-horizontal form-submit-event" id="stock_adjustment_form" method="POST"
                                            enctype="multipart/form-data" action="<?= base_url('tickets/update_ticket'); ?>">
                                            <label class=""><?= label('ticket_type', 'Ticket type') ?></label>
                                            <select class="col-md-12 form-control mt-1 mb-3" name="ticket_type_id">
                                                <?php foreach ($ticket_types as $ticket_type) { ?>

                                                    <option id='ticket_type' value="<?= $ticket_type['id'] ?>"
                                                        <?= (isset($data['ticket_type_id']) && $data['ticket_type_id'] == $ticket_type['id']) ? 'selected' : "" ?>>
                                                        <?= $ticket_type['title'] ?></option>
                                                <?php } ?>
                                            </select>
                                            <input id="user_id" type="hidden" class="form-control" value=<?= $_SESSION['user_id'] ?> name="user_id">
                                            <?php
                                            $user_name = fetch_details('users', ['id' => $_SESSION['user_id']], 'username');
                                            foreach ($user_name as $uname) { ?>

                                                <input id="user_id" type="hidden" class="form-control" value=<?= $_SESSION['user_id'] ?> name="user_id">
                                                <input type="hidden" class="form-control " value=<?= $uname['username'] ?>
                                                    name="username" id="username">
                                            <?php } ?>

                                            <label class="" name="email"
                                                value="<?= $data['email'] ?>"><?= !empty($this->lang->line('email')) ? str_replace('\\', '', $this->lang->line('email')) : 'Email' ?>
                                            </label>
                                            <input type="text" class="form-control  col-md-12  mt-1 mb-3"
                                                placeholder="<?= !empty($this->lang->line('email')) ? str_replace('\\', '', $this->lang->line('email')) : 'Email' ?>"
                                                name="email" value="<?= $data['email'] ?> " id="email_id">

                                            <label class="" name="subject"
                                                value="<?= $data['subject'] ?>"><?= !empty($this->lang->line('subject')) ? str_replace('\\', '', $this->lang->line('subject')) : 'Subject' ?></label>
                                            <input type="text" id="subject_id" class="form-control  col-md-12  mt-1 mb-3"
                                                placeholder="<?= !empty($this->lang->line('subject')) ? str_replace('\\', '', $this->lang->line('subject')) : 'Subject' ?>"
                                                name="subject" value="<?= $data['subject'] ?>">

                                            <label class="" name="description"
                                                value="<?= $data['description'] ?>"><?= !empty($this->lang->line('description')) ? str_replace('\\', '', $this->lang->line('description')) : 'Description' ?></label>
                                            <input type="text" id="description_id" class="form-control  col-md-12  mt-1 mb-3"
                                                placeholder="<?= !empty($this->lang->line('description')) ? str_replace('\\', '', $this->lang->line('description')) : 'Description' ?>"
                                                name="description" value="<?= $data['description'] ?>">

                                            <input type="hidden" class="form-control " value=<?= $ticket['id'] ?> name="edit_id" id="ticket_id">
                                            <footer class="mt-4">
                                                <button type="submit" class="submit btn btn-sm btn-primary rounded-pill"
                                                    value="<?= !empty($this->lang->line('save')) ? str_replace('\\', '', $this->lang->line('save')) : 'Save' ?>"><?= !empty($this->lang->line('update')) ? str_replace('\\', '', $this->lang->line('update')) : 'Update' ?></button>
                                            </footer>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>


            </div>

            <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="ticket_modal"
                data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header p-5">
                            <h5 class="modal-title" id="user_name"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-5 pt-0">
                            <div class="card direct-chat direct-chat-primary">
                                <div class="card-header ui-sortable-handle">
                                    <div class="align-items-center d-flex justify-content-between">
                                        <h4 class="mb-0" id="ticket_type_chat"></h4>
                                        <span id="status"><label class="badge badge-secondary ml-2"></label></span>

                                    </div>
                                    <div class="align-items-center d-flex justify-content-between">
                                        <h3 class="card-title mb-0" id="subject_chat"></h3>
                                        <p class="mb-0" id="date_created"></p>

                                    </div>
                                </div>
                                <?php
                                $offset = 0;
                                $limit = 15;
                                ?>
                                <div class="card-body">
                                    <div class="direct-chat-messages" id="element">
                                        <div class="ticket_msg" data-limit="<?= $limit ?>" data-offset="<?= $offset ?>" data-max-loaded="false">
                                        </div>
                                        <div class="scroll_div"></div>
                                    </div>
                                </div>
                                <div id="chat-box-content" class="chat-bg card-body chat-scroll chat-content">
                                    <div class="chat_loader">Loading...</div>
                                </div>
                                <div class="card-body d-none" id="chat-dropbox">
                                    <div class="dropzone" id="myAlbumTicket"></div>
                                    <div class="text-center mt-3">
                                        <button class="btn btn-danger shadow-none"
                                            onclick="closeDropZone();"><?= !empty($this->lang->line('close')) ? str_replace('\\', '', $this->lang->line('close')) : 'Close'; ?>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-footer px-5">
                                    <form class="form-horizontal " id="ticket_send_msg_form"
                                        action="<?= base_url('tickets/send-message'); ?>" method="POST"
                                        enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="input-group">
                                                <input type="hidden" name="user_id" id="user_id">
                                                <input type="hidden" name="user_type" id="user_type" value="user">
                                                <input type="hidden" name="ticket_id" id="ticket_id">
                                                <input type="text" name="message" id="message_input"
                                                    placeholder="Type Message ..." class="form-control p-2 rounded">
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <span class="input-group-append">
                                                <div class="form-group mb-0">
                                                    <?php

                                                    if (file_exists(FCPATH . @$fetched_data[0]['attachments']) && !empty(@$fetched_data[0]['attachments'])) {
                                                        $fetched_data[0]['attachments'] = get_image_url($fetched_data[0]['attachments']);
                                                    ?>
                                                        <div class="container-fluid row image-upload-section">
                                                            <div class="col-md-3 col-sm-12 shadow bg-white rounded m-3 p-3 text-center grow">
                                                                <div class='image-upload-div'>
                                                                    <img class="img-fluid mb-2"
                                                                        src="<?= $fetched_data[0]['attachments'] ?>"
                                                                        alt="Image Not Found">
                                                                </div>
                                                                <input type="hidden" name="attachments[]"
                                                                    value='<?= $fetched_data[0]['attachments'] ?>'>
                                                            </div>
                                                        </div>
                                                    <?php
                                                    } else { ?>
                                                        <div class="container-fluid row image-upload-section">
                                                        </div>
                                                    <?php } ?>
                                                    <label class="btn btn-primary btn-sm m-0">
                                                        <i class="fa fa-paperclip"></i>
                                                        <input type="file" name="attachments[]" id="attachments" class="d-none" multiple>
                                                    </label>
                                                    <button type="submit" class="btn btn-primary btn-xs"
                                                        id="submit_btn">Send</button>
                                                </div>
                                            </span>
                                        </div>

                                    </form>
                                </div>
                                <!-- /.card-footer-->
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- </main> -->
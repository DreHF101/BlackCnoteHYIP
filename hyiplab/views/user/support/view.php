<?php hyiplab_layout('user/layouts/master'); ?>
<div class="text-end mb-3 d-flex flex-wrap justify-content-between gap-1">
    <h3><?php esc_html_e('View Ticket', HYIPLAB_PLUGIN_NAME);?></h3>
</div>
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card custom--card">
            <div class="card-header card-header-bg d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="mt-0">
                    <?php
                    $html = '';
                    if ($myTicket->status == 0) {
                        $html = '<span class="badge badge--success">' . esc_html__("Open", HYIPLAB_PLUGIN_NAME) . '</span>';
                    } elseif ($myTicket->status == 1) {
                        $html = '<span class="badge badge--primary">' . esc_html__("Answered", HYIPLAB_PLUGIN_NAME) . '</span>';
                    } elseif ($myTicket->status == 2) {
                        $html = '<span class="badge badge--warning">' . esc_html__("Customer Reply", HYIPLAB_PLUGIN_NAME) . '</span>';
                    } elseif ($myTicket->status == 3) {
                        $html = '<span class="badge badge--dark">' . esc_html__("Closed", HYIPLAB_PLUGIN_NAME) . '</span>';
                    }
                    echo wp_kses($html, hyiplab_allowed_html());
                    ?>
                    [<?php esc_html_e('Ticket', HYIPLAB_PLUGIN_NAME); ?>#<?php echo esc_html($myTicket->ticket); ?>] <?php echo esc_html($myTicket->subject); ?>
                </h5>
                <?php if ($myTicket->status != 3) { ?>
                    <button class="btn btn--danger close-button btn-sm confirmationBtn" type="button" data-question="<?php esc_attr_e('Are you sure to close this ticket?', HYIPLAB_PLUGIN_NAME); ?>" data-action="<?php echo hyiplab_route_link('user.ticket.close'); ?>?id=<?php echo intval($myTicket->id); ?>" data-nonce="<?php echo esc_attr(hyiplab_nonce('user.ticket.close'));?>"><i class="fa fa-lg fa-times-circle"></i>
                    </button>
                <?php } ?>
            </div>
            <div class="card-body">
                <form method="post" action="<?php echo hyiplab_route_link('user.ticket.reply'); ?>?id=<?php echo intval($myTicket->id); ?>" enctype="multipart/form-data">
                    <?php hyiplab_nonce_field('user.ticket.reply'); ?>
                    <div class="row justify-content-between">
                        <div class="col-md-12">
                            <div class="form-group">
                                <textarea name="message" class="form-control form--control" rows="4"><?php echo hyiplab_old('message'); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="javascript:void(0)" class="btn btn--base btn--sm addFile"><i class="fa fa-plus"></i> <?php esc_html_e('Add New', HYIPLAB_PLUGIN_NAME); ?></a>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?php esc_html_e('Attachments', HYIPLAB_PLUGIN_NAME); ?></label> <small class="text--danger"><?php esc_html_e('Max 5 files can be uploaded', HYIPLAB_PLUGIN_NAME); ?>. <?php esc_html_e('Maximum upload size is', HYIPLAB_PLUGIN_NAME); ?> <?php echo ini_get('upload_max_filesize'); ?></small>
                        <input type="file" name="attachments[]" class="form-control form--control" />
                        <div id="fileUploadsContainer"></div>
                        <p class="my-2 ticket-attachments-message text-muted">
                            <?php esc_html_e('Allowed File Extensions', HYIPLAB_PLUGIN_NAME); ?>: .<?php esc_html_e('jpg', HYIPLAB_PLUGIN_NAME); ?>, .<?php esc_html_e('jpeg', HYIPLAB_PLUGIN_NAME); ?>, .<?php esc_html_e('png', HYIPLAB_PLUGIN_NAME); ?>, .<?php esc_html_e('pdf', HYIPLAB_PLUGIN_NAME); ?>, .<?php esc_html_e('doc', HYIPLAB_PLUGIN_NAME); ?>, .<?php esc_html_e('docx', HYIPLAB_PLUGIN_NAME); ?>
                        </p>
                    </div>
                    <button type="submit" class="btn btn--base w-100"> <i class="fa fa-reply"></i> <?php esc_html_e('Reply', HYIPLAB_PLUGIN_NAME); ?></button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <?php foreach ($messages as $message) {
                    $ticket = hyiplab_support_ticket($message->support_ticket_id);
                ?>
                    <?php if ($message->admin_id == 0) { ?>
                        <div class="row border border-primary border-radius-3 my-3 py-3 mx-2">
                            <div class="col-md-3 border-end text-end">
                                <h5 class="my-3"><?php echo esc_html($ticket->name); ?></h5>
                            </div>
                            <div class="col-md-9">
                                <p class="text-muted fw-bold my-3">
                                    <?php esc_html_e('Posted on', HYIPLAB_PLUGIN_NAME); ?> <?php echo date('l, dS F Y @ H:i', strtotime($message->created_at)); ?></p>
                                <p><?php echo esc_html($message->message); ?></p>
                                <?php if (count(hyiplab_support_ticket_attachments($message->id)) > 0) { ?>
                                    <div class="mt-2">
                                        <?php foreach (hyiplab_support_ticket_attachments($message->id) as $k => $image) { ?>
                                            <a href="<?php echo hyiplab_route_link('user.ticket.download'); ?>?id=<?php echo hyiplab_encrypt($image->id); ?>" class="me-3">
                                                <i class="fa fa-file"></i> <?php esc_html_e('Attachment', HYIPLAB_PLUGIN_NAME); ?>
                                            </a>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="row border border-warning border-radius-3 my-3 py-3 mx-2" style="background-color: #ffd96729">
                            <div class="col-md-3 border-end text-end">
                                <h5 class="my-3"><?php esc_html_e('Admin', HYIPLAB_PLUGIN_NAME); ?></h5>
                                <p class="lead text-muted"><?php esc_html_e('Staff', HYIPLAB_PLUGIN_NAME); ?></p>
                            </div>
                            <div class="col-md-9">
                                <p class="text-muted fw-bold my-3">
                                    <?php esc_html_e('Posted on', HYIPLAB_PLUGIN_NAME); ?> <?php echo date('l, dS F Y @ H:i', strtotime($message->created_at)); ?></p>
                                <p><?php echo esc_html($message->message); ?></p>
                                <?php if (count(hyiplab_support_ticket_attachments($message->id)) > 0) { ?>
                                    <div class="mt-2">
                                        <?php foreach (hyiplab_support_ticket_attachments($message->id) as $k => $image) { ?>
                                            <a href="<?php echo hyiplab_route_link('user.ticket.download'); ?>?id=<?php echo hyiplab_encrypt($image->id); ?>" class="me-3">
                                                <i class="fa fa-file"></i> <?php esc_html_e('Attachment', HYIPLAB_PLUGIN_NAME); ?>
                                            </a>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php hyiplab_include('partials/confirmation'); ?>

<script>
    jQuery(document).ready(function($) {
        "use strict";
        var fileAdded = 0;
        $('.addFile').on('click', function() {
            if (fileAdded >= 4) {
                alert('You\'ve added maximum number of file');
                return false;
            }
            fileAdded++;
            $("#fileUploadsContainer").append(`
                <div class="input-group my-3">
                    <input type="file" name="attachments[]" class="form-control form--control" required />
                    <button type="submit" class="input-group-text btn-danger remove-btn"><i class="las la-times"></i></button>
                </div>
            `)
        });
        $(document).on('click', '.remove-btn', function() {
            fileAdded--;
            $(this).closest('.input-group').remove();
        });
    });
</script>
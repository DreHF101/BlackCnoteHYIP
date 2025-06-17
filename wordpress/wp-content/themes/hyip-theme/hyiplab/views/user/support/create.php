<?php hyiplab_layout('user/layouts/master'); ?>

<div class="text-end mb-3 d-flex flex-wrap justify-content-between gap-1">
    <h3><?php esc_html_e('Open Ticket', HYIPLAB_PLUGIN_NAME);?></h3>
</div>

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card custom--card">
            <div class="card-body">
                <form action="<?php echo hyiplab_route_link('user.ticket.store'); ?>" method="post" enctype="multipart/form-data">
                    <?php hyiplab_nonce_field('user.ticket.store'); ?>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label"><?php esc_html_e('Name', HYIPLAB_PLUGIN_NAME); ?></label>
                            <input type="text" name="name" value="<?php echo esc_attr($user->display_name); ?>" class="form-control form--control" required readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label"><?php esc_html_e('Email Address', HYIPLAB_PLUGIN_NAME); ?></label>
                            <input type="email" name="email" value="<?php echo esc_attr($user->user_email); ?>" class="form-control form--control" required readonly>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label"><?php esc_html_e('Subject', HYIPLAB_PLUGIN_NAME); ?></label>
                            <input type="text" name="subject" value="<?php echo esc_attr(hyiplab_old('subject')); ?>" class="form-control form--control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label"><?php esc_html_e('Priority', HYIPLAB_PLUGIN_NAME); ?></label>
                            <select name="priority" class="form-control form--control form-select" required>
                                <option value="3"><?php esc_html_e('High', HYIPLAB_PLUGIN_NAME); ?></option>
                                <option value="2"><?php esc_html_e('Medium', HYIPLAB_PLUGIN_NAME); ?></option>
                                <option value="1"><?php esc_html_e('Low', HYIPLAB_PLUGIN_NAME); ?></option>
                            </select>
                        </div>
                        <div class="col-12 form-group">
                            <label class="form-label"><?php esc_html_e('Message', HYIPLAB_PLUGIN_NAME); ?></label>
                            <textarea name="message" id="inputMessage" rows="6" class="form-control form--control" required><?php echo hyiplab_old('message'); ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="text-end">
                            <button type="button" class="btn btn--base btn--sm addFile">
                                <i class="fa fa-plus"></i> <?php esc_html_e('Add New', HYIPLAB_PLUGIN_NAME); ?>
                            </button>
                        </div>
                        <div class="file-upload">
                            <label class="form-label"><?php esc_html_e('Attachments', HYIPLAB_PLUGIN_NAME); ?></label> <small class="text-danger"><?php esc_html_e('Max 5 files can be uploaded', HYIPLAB_PLUGIN_NAME); ?>. <?php esc_html_e('Maximum upload size is', HYIPLAB_PLUGIN_NAME); ?> <?php echo ini_get('upload_max_filesize'); ?></small>
                            <input type="file" name="attachments[]" id="inputAttachments" class="form-control form--control mb-2" />
                            <div id="fileUploadsContainer"></div>
                            <p class="ticket-attachments-message text-muted">
                                <?php esc_html_e('Allowed File Extensions', HYIPLAB_PLUGIN_NAME); ?>: .<?php esc_html_e('jpg', HYIPLAB_PLUGIN_NAME); ?>, .<?php esc_html_e('jpeg', HYIPLAB_PLUGIN_NAME); ?>, .<?php esc_html_e('png', HYIPLAB_PLUGIN_NAME); ?>, .<?php esc_html_e('pdf', HYIPLAB_PLUGIN_NAME); ?>, .<?php esc_html_e('doc', HYIPLAB_PLUGIN_NAME); ?>, .<?php esc_html_e('docx', HYIPLAB_PLUGIN_NAME); ?>
                            </p>
                        </div>

                    </div>

                    <div class="form-group">
                        <button class="btn btn--base w-100" type="submit"><i class="fa fa-paper-plane"></i>&nbsp;<?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
                    <button type="button" class="input-group-text btn--danger remove-btn"><i class="las la-times"></i></button>
                </div>
            `)
        });
        $(document).on('click', '.remove-btn', function() {
            fileAdded--;
            $(this).closest('.input-group').remove();
        });
    });
</script>
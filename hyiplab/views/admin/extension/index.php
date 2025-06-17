<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two custom-data-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Extension', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Status', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Action', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($extensions as $extension) { ?>
                                <tr>
                                    <td>
                                        <div class="user">
                                            <div class="thumb">
                                                <img src="<?php echo hyiplab_assets('admin/images/extensions/' . $extension->image); ?>" alt="<?php esc_attr_e($extension->name, HYIPLAB_PLUGIN_NAME); ?>" class="plugin_bg">
                                            </div>
                                            <span class="name"><?php esc_html_e($extension->name); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($extension->status) { ?>
                                            <span class="badge badge--success"><?php esc_html_e('Enabled', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } else { ?>
                                            <span class="badge badge--warning"><?php esc_html_e('Disabled', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <div class="button--group">
                                            <button type="button" class="btn btn-sm btn-outline--primary ms-1 mb-2 editBtn" data-name="<?php esc_attr_e($extension->name, HYIPLAB_PLUGIN_NAME); ?>" data-shortcode='<?php echo esc_js($extension->shortcode); ?>' data-action="<?php echo hyiplab_route_link('admin.extension.update'); ?>&id=<?php echo intval($extension->id); ?>">
                                                <i class="la la-cogs"></i> <?php esc_html_e('Configure', HYIPLAB_PLUGIN_NAME); ?>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline--dark ms-1 mb-2 helpBtn" data-description="<?php esc_attr_e($extension->description, HYIPLAB_PLUGIN_NAME); ?>" data-support="<?php echo esc_attr($extension->support); ?>">
                                                <i class="la la-question"></i> <?php esc_html_e('Help', HYIPLAB_PLUGIN_NAME); ?>
                                            </button>
                                            <?php if (!$extension->status) { ?>
                                                <button type="button" class="btn btn-sm btn-outline--success ms-1 mb-2 confirmationBtn" data-action="<?php echo hyiplab_route_link('admin.extension.status'); ?>&id=<?php echo intval($extension->id); ?>" data-question="<?php esc_attr_e('Are you sure to enable this extension?'); ?>" data-nonce="<?php echo esc_attr(hyiplab_nonce('admin.extension.status'));?>">
                                                    <i class="la la-eye"></i> <?php esc_html_e('Enable', HYIPLAB_PLUGIN_NAME); ?>
                                                </button>
                                            <?php } else { ?>
                                                <button type="button" class="btn btn-sm btn-outline--danger mb-2 confirmationBtn" data-action="<?php echo hyiplab_route_link('admin.extension.status'); ?>&id=<?php echo intval($extension->id); ?>" data-question="<?php esc_attr_e('Are you sure to disable this extension?', HYIPLAB_PLUGIN_NAME); ?>" data-nonce="<?php echo esc_attr(hyiplab_nonce('admin.extension.status'));?>">
                                                    <i class="la la-eye-slash"></i> <?php esc_html_e('Disable', HYIPLAB_PLUGIN_NAME); ?>
                                                </button>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php hyiplab_include('partials/confirmation'); ?>

<div id="editModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e('Update Extension', HYIPLAB_PLUGIN_NAME); ?>: <span class="extension-name"></span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form method="POST">
                <?php hyiplab_nonce_field('admin.extension.update'); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-md-12 control-label fw-bold"><?php esc_html_e('Script', HYIPLAB_PLUGIN_NAME); ?></label>
                        <div class="col-md-12">
                            <textarea name="script" class="form-control" required rows="8" placeholder="<?php esc_attr_e('Paste your script with proper key', HYIPLAB_PLUGIN_NAME); ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45" id="editBtn"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="helpModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e('Need Help', HYIPLAB_PLUGIN_NAME); ?>?</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        "use strict";
        $(document).on('click', '.editBtn', function() {
            var modal = $('#editModal');
            var shortcode = $(this).data('shortcode');
            modal.find('.extension-name').text($(this).data('name'));
            modal.find('form').attr('action', $(this).data('action'));
            var html = '';
            $.each(shortcode, function(key, item) {
                html += `<div class="form-group">
                    <label class="col-md-12 control-label fw-bold">${item.title}</label>
                    <div class="col-md-12">
                        <input name="${key}" class="form-control" placeholder="--" value="${item.value}" required>
                    </div>
                </div>`;
            })
            modal.find('.modal-body').html(html);
            modal.modal('show');
        });

        $(document).on('click', '.helpBtn', function() {
            var modal = $('#helpModal');
            var path = "<?php echo hyiplab_assets('admin/images/extensions'); ?>";
            modal.find('.modal-body').html(`<div class="mb-2">${$(this).data('description')}</div>`);
            if ($(this).data('support') != 'na') {
                modal.find('.modal-body').append(`<img src="${path}/${$(this).data('support')}">`);
            }
            modal.modal('show');
        });
    });
</script>
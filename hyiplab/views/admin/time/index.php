<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th scope="col"><?php esc_html_e('Name', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Time', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Status', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Action', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($times as $time) { ?>
                                <tr>
                                    <td><?php echo esc_html($time->name);?></td>
                                    <td><?php echo esc_html($time->time);?> <?php echo _n('Hour', 'Hours', $time->time, HYIPLAB_PLUGIN_NAME);?></td>
                                    <td>
                                        <?php if ($time->status == 1) { ?>
                                            <span class="badge badge--success"><?php esc_html_e('Active', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } else { ?>
                                            <span class="badge badge--warning"><?php esc_html_e('Inactive', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <button type="button" data-id="<?php echo esc_attr($time->id);?>" data-name="<?php echo esc_attr($time->name);?>" data-time="<?php echo esc_attr($time->time);?>" data-route="<?php echo hyiplab_route_link('admin.time.update');?>&amp;id=<?php echo intval($time->id);?>" data-bs-toggle="modal" data-bs-target="#editModal" class="btn btn-outline--primary editBtn btn-sm me-2"><i class="las la-pen"></i><?php esc_html_e('Edit', HYIPLAB_PLUGIN_NAME);?></button>

                                        <?php if ($time->status) { ?>
                                            <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="<?php esc_attr_e('Are you sure to disable this time?');?>" data-action="<?php echo hyiplab_route_link('admin.time.status');?>&amp;id=<?php echo intval($time->id);?>" data-nonce="<?php echo esc_attr(hyiplab_nonce('admin.time.status'));?>"><i class="las la-eye-slash"></i><?php esc_html_e('Disable', HYIPLAB_PLUGIN_NAME);?></button>
                                        <?php } else { ?>
                                            <button class="btn btn-sm btn-outline--success confirmationBtn" data-question="<?php esc_attr_e('Are you sure to enable this time?');?>" data-action="<?php echo hyiplab_route_link('admin.time.status');?>&amp;id=<?php echo intval($time->id);?>" data-nonce="<?php echo esc_attr(hyiplab_nonce('admin.time.status'));?>"><i class="las la-eye"></i><?php esc_html_e('Enable', HYIPLAB_PLUGIN_NAME);?></button>
                                        <?php } ?>

                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (hyiplab_check_empty($times)) { ?>
                                <tr>
                                    <td class="text-muted text-center" colspan="100%"><?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME);?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="timeModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php esc_html_e('Add New Time', HYIPLAB_PLUGIN_NAME);?></h4>
                <button type="button" class="close" data-bs-dismiss="modal"><span><i class="las la-times"></i></span></button>
            </div>
            <form method="post" action="<?php echo hyiplab_route_link('admin.time.store');?>">
                <?php hyiplab_nonce_field('admin.time.store');?>
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php esc_html_e('Time Name', HYIPLAB_PLUGIN_NAME);?></label>
                        <input type="text" class="form-control" placeholder="<?php esc_attr_e('e.g. Hour, Day, Week', HYIPLAB_PLUGIN_NAME);?>" name="name" required>
                    </div>
                    <div class="form-group">
                        <label><?php esc_html_e('Time in Hours', HYIPLAB_PLUGIN_NAME);?></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="time" required>
                            <div class="input-group-text"><?php esc_html_e('Hours', HYIPLAB_PLUGIN_NAME);?></div>
                        </div>
                        <p><small class="text-muted text-center"><i class="las la-dot-circle"></i><i><?php esc_html_e('Interest will be given after this time which you\'ve put above', HYIPLAB_PLUGIN_NAME);?></i></small></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45"><i class="fa fa-send"></i> <?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME);?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php esc_html_e('Edit Time', HYIPLAB_PLUGIN_NAME);?></h4>
                <button type="button" class="close" data-bs-dismiss="modal"><span><i class="las la-times"></i></span></button>
            </div>
            <form method="post" action="">
                <?php hyiplab_nonce_field('admin.time.update');?>
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php esc_html_e('Time Name', HYIPLAB_PLUGIN_NAME);?></label>
                        <input type="text" class="form-control" placeholder="<?php esc_attr_e('e.g. Hour, Day, Week');?>" name="name" required>
                    </div>
                    <div class="form-group">
                        <label><?php esc_html_e('Time in Hours', HYIPLAB_PLUGIN_NAME);?></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="time" required>
                            <div class="input-group-text"><?php esc_html_e('Hours', HYIPLAB_PLUGIN_NAME);?></div>
                        </div>
                        <p><small class="text-muted text-center"><i class="las la-dot-circle"></i><i><?php esc_html_e('Interest will be given after this time which you\'ve put above', HYIPLAB_PLUGIN_NAME);?></i></small></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45"><i class="fa fa-send"></i> <?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME);?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php hyiplab_include('partials/confirmation'); ?>

<?php
$html = '<button type="button" data-bs-target="#timeModal" data-bs-toggle="modal" class="btn btn-sm btn-outline--primary"><i class="las la-plus"></i>'.esc_html__('Add New', HYIPLAB_PLUGIN_NAME).'</button>';
hyiplab_push_breadcrumb($html);
?>

<script>
    jQuery(document).ready(function($){
        "use strict";
        $('.editBtn').on('click', function () {
            var modal = $('#editModal');
            modal.find('form').attr('action', $(this).data('route'));
            modal.find('input[name=name]').val($(this).data('name'));
            modal.find('input[name=time]').val($(this).data('time'));
        });
    });
</script>
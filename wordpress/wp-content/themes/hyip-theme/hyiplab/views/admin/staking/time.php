<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th scope="col"><?php esc_html_e('S.N', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Days', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Interest', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Status', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Action', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stakings->data as $staking) { ?>
                                <tr>
                                    <td><?php echo esc_html($staking->id); ?></td>
                                    <td><?php echo esc_html($staking->days); ?><?php echo esc_html(__(' Days', HYIPLAB_PLUGIN_NAME)); ?></td>
                                    <td><?php echo esc_html($staking->interest_percent); ?>
                                        <?php echo __('%', HYIPLAB_PLUGIN_NAME); ?>
                                    </td>
                                    <td>
                                        <?php if ($staking->status == 1) { ?>
                                            <span class="badge badge--success"><?php esc_html_e('Enabled', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } else { ?>
                                            <span class="badge badge--warning"><?php esc_html_e('Disabled', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <button type="button" data-id="<?php echo esc_attr($staking->id); ?>" data-duration="<?php echo esc_attr($staking->days); ?>" data-interest_amount="<?php echo esc_attr($staking->interest_percent); ?>" data-action="<?php echo hyiplab_route_link('admin.staking.time.store'); ?>&amp;id=<?php echo intval($staking->id); ?>" class="btn btn-outline--primary editBtn btn-sm me-2"><i class="las la-pen"></i><?php esc_html_e('Edit', HYIPLAB_PLUGIN_NAME); ?></button>

                                        <?php if ($staking->status) { ?>
                                            <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="<?php esc_attr_e('Are you sure to disable this staking?'); ?>" data-action="<?php echo hyiplab_route_link('admin.staking.time.status'); ?>&amp;id=<?php echo intval($staking->id); ?>" data-nonce="<?php echo esc_attr(hyiplab_nonce('admin.staking.time.status')); ?>"><i class="las la-eye-slash"></i><?php esc_html_e('Disable', HYIPLAB_PLUGIN_NAME); ?></button>
                                        <?php } else { ?>
                                            <button class="btn btn-sm btn-outline--success confirmationBtn" data-question="<?php esc_attr_e('Are you sure to enable this staking?'); ?>" data-action="<?php echo hyiplab_route_link('admin.staking.time.status'); ?>&amp;id=<?php echo intval($staking->id); ?>" data-nonce="<?php echo esc_attr(hyiplab_nonce('admin.staking.time.status')); ?>"><i class="las la-eye"></i><?php esc_html_e('Enable', HYIPLAB_PLUGIN_NAME); ?></button>
                                        <?php } ?>

                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (hyiplab_check_empty($stakings->data)) { ?>
                                <tr>
                                    <td class="text-muted text-center" colspan="100%"><?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="stakingModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php esc_html_e('Add New Staking', HYIPLAB_PLUGIN_NAME); ?></h4>
                <button type="button" class="close" data-bs-dismiss="modal"><span><i class="las la-times"></i></span></button>
            </div>
            <form method="post" action="<?php echo hyiplab_route_link('admin.staking.time.store'); ?>">
                <?php hyiplab_nonce_field('admin.staking.time.store'); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="duration" class="required"><?php esc_html_e('Duration', HYIPLAB_PLUGIN_NAME); ?></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="duration" min="1" required id="duration">
                            <span class="input-group-text"><?php esc_html_e('Days', HYIPLAB_PLUGIN_NAME); ?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="interest_amount" class="required"><?php esc_html_e('Interest Amount', HYIPLAB_PLUGIN_NAME); ?></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="interest_amount" min="0" step="any" required id="interest_amount">
                            <span class="input-group-text"><?php esc_html_e('%', HYIPLAB_PLUGIN_NAME); ?></span>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45"><i class="fa fa-send"></i> <?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php hyiplab_include('partials/confirmation'); ?>

<?php
$html = '<button type="button" data-bs-target="#stakingModal" data-bs-toggle="modal" class="btn btn-sm btn-outline--primary addBtn"><i class="las la-plus"></i>' . esc_html__('Add New', HYIPLAB_PLUGIN_NAME) . '</button>';
hyiplab_push_breadcrumb($html);
?>

<script>
    jQuery(document).ready(function($) {
        "use strict";
        let modal = $('#stakingModal');

        $('.addBtn').on('click', function() {
            console.log('addBtn');
            modal.find('form').trigger('reset');
            modal.find('.modal-title').text(`Add New Staking`);
            modal.find('[name=duration]').val('');
            modal.find('[name=interest_amount]').val('');
            modal.modal('show');
        });

        $('.editBtn').on('click', function() {
            let staking = $(this).data();
            modal.find('.modal-title').text(`Update Staking`);
            modal.find('[name=duration]').val(staking.duration);
            modal.find('[name=interest_amount]').val(staking.interest_amount);
            modal.find('form').attr('action', `${staking.action}`);
            modal.modal('show');
        });

    });
</script>
<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Icon', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Level', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Name', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Minimum Invest', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Bonus', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Status', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Action', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userRankings as $userRanking) { ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo hyiplab_get_image(hyiplab_file_path('userRanking') . '/' . $userRanking->icon); ?>" alt="">
                                    </td>
                                    <td><?php echo esc_html($userRanking->level); ?></td>
                                    <td><?php echo esc_html($userRanking->name); ?></td>
                                    <td><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($userRanking->minimum_invest); ?></td>
                                    <td><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($userRanking->bonus); ?></td>
                                    <td>
                                        <?php echo hyiplab_status_badge($userRanking); ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline--primary editBtn me-1" data-icon="<?php echo hyiplab_get_image(hyiplab_file_path('userRanking') . '/' . $userRanking->icon); ?>" data-ranking='<?php echo wp_json_encode($userRanking); ?>'><i class="las la-pen"></i><?php esc_html_e('Edit', HYIPLAB_PLUGIN_NAME); ?></button>
                                        <?php if ($userRanking->status) { ?>
                                            <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="<?php esc_attr_e('Are you sure to disable this ranking?', HYIPLAB_PLUGIN_NAME); ?>" data-action="<?php echo hyiplab_route_link('admin.ranking.status'); ?>&amp;id=<?php echo intval($userRanking->id); ?>" data-nonce="<?php echo esc_attr(hyiplab_nonce('admin.ranking.status')); ?>"><i class="las la-eye-slash"></i><?php esc_html_e('Disable', HYIPLAB_PLUGIN_NAME); ?></button>
                                        <?php } else { ?>
                                            <button class="btn btn-sm btn-outline--success confirmationBtn" data-question="<?php esc_attr_e('Are you sure to enable this ranking?', HYIPLAB_PLUGIN_NAME); ?>" data-action="<?php echo hyiplab_route_link('admin.ranking.status'); ?>&amp;id=<?php echo intval($userRanking->id); ?>" data-nonce="<?php echo esc_attr(hyiplab_nonce('admin.ranking.status')); ?>"><i class="las la-eye"></i><?php esc_html_e('Enable', HYIPLAB_PLUGIN_NAME); ?></button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (hyiplab_check_empty($userRankings)) { ?>
                                <tr>
                                    <td class="text-muted text-center" colspan="100%"><?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
        </div><!-- card end -->
    </div>
</div>

<div class="modal fade" id="rankingModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e('Add New User Ranking', HYIPLAB_PLUGIN_NAME); ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="<?php echo hyiplab_route_link('admin.ranking.store'); ?>" method="post" enctype="multipart/form-data">
                <?php hyiplab_nonce_field('admin.ranking.store'); ?>
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-4 col-lg-5">
                            <div class="form-group">
                                <label class="icon"><?php esc_html_e('Icon', HYIPLAB_PLUGIN_NAME); ?></label>
                                <div class="image-upload">
                                    <div class="thumb">
                                        <div class="avatar-preview">
                                            <div class="profilePicPreview">
                                                <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="avatar-edit">
                                            <input type="file" class="profilePicUpload d-none" name="icon" id="profilePicUpload1" accept=".png, .jpg, .jpeg" required>
                                            <label for="profilePicUpload1" class="bg--success mt-3"><?php esc_html_e('Upload Image', HYIPLAB_PLUGIN_NAME); ?></label>
                                            <small class="mt-2"><?php esc_html_e('Supported files', HYIPLAB_PLUGIN_NAME); ?>: <b><?php esc_html_e('png', HYIPLAB_PLUGIN_NAME); ?>, <?php esc_html_e('jpeg', HYIPLAB_PLUGIN_NAME); ?>, <?php esc_html_e('jpg', HYIPLAB_PLUGIN_NAME); ?>.</b> <?php esc_html_e('Image will be resized into ', HYIPLAB_PLUGIN_NAME); ?> <?php echo hyiplab_file_size('userRanking'); ?> <?php esc_html_e('px', HYIPLAB_PLUGIN_NAME); ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8 col-lg-7">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php esc_html_e('Level', HYIPLAB_PLUGIN_NAME); ?></label>
                                        <input type="number" class="form-control" name="level" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php esc_html_e('Name', HYIPLAB_PLUGIN_NAME); ?></label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php esc_html_e('Minimum Invest', HYIPLAB_PLUGIN_NAME); ?></label>
                                <div class="input-group">
                                    <input type="number" step="any" name="minimum_invest" min="0" class="form-control" required>
                                    <span class="input-group-text"><?php echo hyiplab_currency('text'); ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php esc_html_e('Team Minimum Invest', HYIPLAB_PLUGIN_NAME); ?></label>
                                <div class="input-group">
                                    <input type="number" step="any" name="team_minimum_invest" min="0" class="form-control" required>
                                    <span class="input-group-text"><?php echo hyiplab_currency('text'); ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php esc_html_e('Minimum Direct Referral', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="number" name="min_referral" min="0" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label><?php esc_html_e('Bonus', HYIPLAB_PLUGIN_NAME); ?></label>
                                <div class="input-group">
                                    <input type="number" step="any" name="bonus" min="0" class="form-control" required>
                                    <span class="input-group-text"><?php echo hyiplab_currency('text'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php hyiplab_include('partials/confirmation'); ?>

<?php
$html = '<button type="button" class="btn btn-outline--primary addBtn" data-icon="' . hyiplab_get_image('') . '"><i class="las la-plus"></i>' . esc_html__("Add New", HYIPLAB_PLUGIN_NAME) . '</button>';
hyiplab_push_breadcrumb($html);
?>

<style>
    .image-upload .thumb .profilePicPreview {
        height: 230px;
    }
</style>

<script>
    jQuery(document).ready(function($) {
        'use strict';
        let modal = $('#rankingModal');
        $('.addBtn').on('click', function() {
            modal.modal('show');
            modal.find('form')[0].reset();
            modal.find('.profilePicPreview').css('backgroundImage', `url(${$(this).data('icon')})`);
        });

        $('.editBtn').on('click', function() {
            let ranking = $(this).data('ranking');
            modal.find('[name=id]').val(ranking.id);
            modal.find('[name=level]').val(ranking.level);
            modal.find('[name=name]').val(ranking.name);
            modal.find('[name=minimum_invest]').val(parseFloat(ranking.minimum_invest).toFixed(2));
            modal.find('[name=team_minimum_invest]').val(parseFloat(ranking.min_referral_invest).toFixed(2));
            modal.find('[name=min_referral]').val(ranking.min_referral);
            modal.find('[name=bonus]').val(parseFloat(ranking.bonus).toFixed(2));
            modal.find('.profilePicPreview').css('backgroundImage', `url(${$(this).data('icon')})`);
            modal.find('.icon').removeClass('required');
            modal.find('[name=icon]').removeAttr('required');
            modal.modal('show');
        });


    });
</script>
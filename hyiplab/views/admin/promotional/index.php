<?php hyiplab_layout('admin/layouts/master'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="card bl--5-primary mb-3">
            <div class="card-body">
                <p><?php echo esc_html__('This module could be enabled or disabled from the ', HYIPLAB_PLUGIN_NAME)  ?> <a href="<?php echo esc_url(hyiplab_route_link('admin.setting.system.configuration'))?>"><?php echo esc_html__('System Setting', HYIPLAB_PLUGIN_NAME); ?></a>. <?php echo esc_html__('If you enable the module you users will be able to use some HTML code to generate the referral users.', HYIPLAB_PLUGIN_NAME) ?></p>
            </div>
        </div>
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th scope="col"><?php esc_html_e('S.N.', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Banner', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Name', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Action', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($promotionalTools->data as $key => $tool) { ?>
                                <tr>
                                    <td><?php echo esc_html(++$key); ?></td>
                                    <td data-label="Banner">
                                        <div class="user d-flex justify-content-center">
                                            <div class="thumb">
                                                <img src="<?php echo esc_url( hyiplab_file_path('promotional') . '/'.  $tool->banner); ?>" alt="image">
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo esc_html($tool->name); ?></td>
                                    
                                    <td data-label="Action">
                                        <button type="button" data-id="<?php echo intval($tool->id); ?>" data-name="<?php echo esc_html($tool->name); ?>" data-image="<?php echo esc_url( hyiplab_file_path('promotional') . '/'.  $tool->banner); ?>" data-action="<?php echo hyiplab_route_link('admin.promotion.store')?>&amp;id=<?php echo $tool->id ?>" class="btn btn-sm btn-outline--primary editBtn"> <i class="las la-pen"></i> <?php echo esc_html__('Edit', HYIPLAB_PLUGIN_NAME); ?> </button>
                                        <button type="button" data-question="Are you sure to delete this promotion?" data-action="<?php echo hyiplab_route_link('admin.promotion.delete')?>&amp;id=<?php echo $tool->id ?>" data-nonce="<?php echo hyiplab_nonce('admin.promotion.delete'); ?>" class="btn btn-sm btn-outline--danger confirmationBtn"> <i class="las la-trash"></i> <?php echo esc_html__('Delete', HYIPLAB_PLUGIN_NAME); ?></button>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (hyiplab_check_empty($promotionalTools->data)) { ?>
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

<div id="addModal" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e('Add New Banner', HYIPLAB_PLUGIN_NAME); ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="las la-times"></i></span>
                </button>
            </div>
            <form action="<?php echo hyiplab_route_link('admin.promotion.store') ?>" method="POST" enctype="multipart/form-data">
                <?php hyiplab_nonce_field('admin.promotion.store'); ?>  
                <div class="modal-body">
                    <div class="image-upload">
                        <div class="thumb">
                            <div class="avatar-preview">
                                <div class="profilePicPreview" style="background-image: url(<?php echo hyiplab_asset('images/default.png') . '?' . time() ?>)">
                                    <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="avatar-edit">
                                <input type="file" class="profilePicUpload p-0" id="profilePicUpload2" accept="jpeg, jpg, png, gif" name="image_input">
                                <label for="profilePicUpload2" class="bg--primary"><?php esc_html_e('Select Banner Image', HYIPLAB_PLUGIN_NAME); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                      <label for="name"><?php echo esc_html__('Name', HYIPLAB_PLUGIN_NAME); ?></label>
                      <input type="text" class="form-control" name="name" id="name">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn w-100 btn--primary h-45"><?php echo esc_html__('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editModal" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo esc_html__('Edit Banner', HYIPLAB_PLUGIN_NAME); ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="las la-times"></i></span>
                </button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <?php hyiplab_nonce_field('admin.promotion.store') ?>   
                <div class="modal-body">
                    <div class="image-upload">
                        <div class="thumb">
                            <div class="avatar-preview">
                                <div class="profilePicPreview" style="background-image: url(<?php echo hyiplab_asset('images/default.png') . '?' . time() ?>)">
                                    <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="avatar-edit">
                                <input type="file" class="profilePicUpload p-0" id="profilePicUpload-1" accept="jpeg, jpg, png, gif" name="image_input">
                                <label for="profilePicUpload-1" class="bg--primary"><?php echo esc_html__('Select Banner Image', HYIPLAB_PLUGIN_NAME); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label for="name1"><?php echo esc_html__('Name', HYIPLAB_PLUGIN_NAME); ?></label>
                        <input type="text" class="form-control" name="name" id="name1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn w-100 btn--primary h-45"><?php echo esc_html__('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php hyiplab_include('partials/confirmation'); ?>

<?php
$html = '<button type="button" data-bs-target="#addModal" data-bs-toggle="modal" class="btn btn-sm addBtn btn-outline--primary"><i class="las la-plus"></i>' . esc_html__('Add New', HYIPLAB_PLUGIN_NAME) . '</button>';
hyiplab_push_breadcrumb($html);
?>

<script>
    (function($){
        'use strict';

        $('.editBtn').on('click', function () {
            var modal = $('#editModal');
            var form = modal.find('form');

            modal.find('input[name=name]').val($(this).data('name'));
            modal.find('.profilePicPreview').css('background-image', `url(${$(this).data('image')})`);
            form.attr('action',$(this).data('action'));
            modal.modal('show');
            
        });

    })(jQuery)
</script>

<style>
    .image-upload .thumb .profilePicUpload {
        display: none
    }
    .avatar-edit {
        padding: 15px 2px 0 ;
    }

    .image-upload .thumb .profilePicPreview {
        background-size: contain !important;
        background-position: center !important;
    }
</style>
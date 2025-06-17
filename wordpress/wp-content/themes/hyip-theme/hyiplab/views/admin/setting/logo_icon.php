<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row mb-none-30">
    <div class="col-md-12 mb-30">
        <div class="card bl--5-primary">
            <div class="card-body">
                <p class="text--primary"><?php esc_html_e('If the logo and favicon are not changed after you update from this page, please', HYIPLAB_PLUGIN_NAME); ?> <span class="text--danger"><?php esc_html_e('clear the cache', HYIPLAB_PLUGIN_NAME); ?></span> <?php esc_html_e('from your browser. As we keep the filename the same after the update, it may show the old image for the cache. usually, it works after clear the cache but if you still see the old logo or favicon, it may be caused by server level or network level caching. Please clear them too.', HYIPLAB_PLUGIN_NAME); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-12 mb-30">
        <div class="card">
            <div class="card-body">
                <form action="<?php echo hyiplab_route_link('admin.setting.logo.icon.submit') ?>" method="POST" enctype="multipart/form-data">
                    <?php hyiplab_nonce_field('admin.setting.logo.icon.submit') ?>
                    <div class="row">
                        <div class="form-group col-xl-4">
                            <label class="font-weight-bold"><?php esc_html_e('Logo For White Background', HYIPLAB_PLUGIN_NAME);?></label>
                            <div class="image-upload">
                                <div class="thumb">
                                    <div class="avatar-preview">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="profilePicPreview logoPicPrev bg--dark" style="background-image: url(<?php echo hyiplab_asset('global/images/logo_light.png') . '?' . time() ?>)">
                                                    <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="avatar-edit">
                                        <input type="file" class="profilePicUpload" id="profilePicUpload1" accept=".png, .jpg, .jpeg" name="logo">
                                        <label for="profilePicUpload1" class="bg--primary"><?php esc_html_e('Select Logo', HYIPLAB_PLUGIN_NAME); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-xl-4">
                            <label class="font-weight-bold"><?php esc_html_e('Logo For Dark Background', HYIPLAB_PLUGIN_NAME);?></label>
                            <div class="image-upload">
                                <div class="thumb">
                                    <div class="avatar-preview">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="profilePicPreview logoPicPrev" style="background-image: url(<?php echo hyiplab_asset('global/images/logo.png') . '?' . time() ?>)">
                                                    <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="avatar-edit">
                                        <input type="file" class="profilePicUpload" id="profilePicUpload2" accept=".png, .jpg, .jpeg" name="logo_dark">
                                        <label for="profilePicUpload2" class="bg--primary"><?php esc_html_e('Select Dark Logo', HYIPLAB_PLUGIN_NAME); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-xl-4">
                            <label class="font-weight-bold"><?php esc_html_e('Favicon Image', HYIPLAB_PLUGIN_NAME);?></label>
                            <div class="image-upload">
                                <div class="thumb">
                                    <div class="avatar-preview">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="profilePicPreview logoPicPrev" style="background-image: url(<?php echo hyiplab_asset('global/images/favicon.png') . '?' . time() ?>)">
                                                    <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="avatar-edit">
                                        <input type="file" class="profilePicUpload" id="profilePicUpload3" accept=".png" name="favicon">
                                        <label for="profilePicUpload3" class="bg--primary"><?php esc_html_e('Select Favicon', HYIPLAB_PLUGIN_NAME); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn--primary w-100 h-45"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>
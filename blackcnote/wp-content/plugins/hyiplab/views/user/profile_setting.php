<?php hyiplab_layout('user/layouts/master'); ?>
<div class="mb-4">
    <h3 class="mb-2"><?php esc_html_e('Profile', HYIPLAB_PLUGIN_NAME); ?></h3>
</div>
<div class="card custom--card">
    <div class="card-body">
        <form class="register" action="<?php echo hyiplab_route_link('user.profile.setting.update'); ?>" method="post">
            <?php hyiplab_nonce_field('user.profile.setting.update'); ?>
            <div class="row">
                <div class="form-group col-12">
                    <label class="form-label" for="display_name"><?php esc_html_e('Full Name', HYIPLAB_PLUGIN_NAME); ?></label>
                    <input type="text" class="form-control form--control" name="display_name" value="<?php echo esc_html($user->display_name); ?>" id="display_name" required>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <label class="form-label"><?php esc_html_e('E-mail Address', HYIPLAB_PLUGIN_NAME); ?></label>
                    <input class="form-control form--control" value="<?php echo esc_attr($user->user_email); ?>" readonly>
                </div>
                <div class="form-group col-sm-6">
                    <label class="form-label"><?php esc_html_e('Mobile Number', HYIPLAB_PLUGIN_NAME); ?></label>
                    <input class="form-control form--control" value="<?php echo esc_attr(get_user_meta($user->ID, 'hyiplab_mobile', true)); ?>" readonly>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <label class="form-label" for="address"><?php esc_html_e('Address', HYIPLAB_PLUGIN_NAME); ?></label>
                    <input type="text" class="form-control form--control" name="address" value="<?php echo esc_attr(get_user_meta($user->ID, 'hyiplab_address', true)); ?>" id="address">
                </div>
                <div class="form-group col-sm-6">
                    <label class="form-label" for="state"><?php esc_html_e('State', HYIPLAB_PLUGIN_NAME); ?></label>
                    <input type="text" class="form-control form--control" name="state" value="<?php echo esc_attr(get_user_meta($user->ID, 'hyiplab_state', true)); ?>" id="state">
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-4">
                    <label class="form-label" for="zip"><?php esc_html_e('Zip Code', HYIPLAB_PLUGIN_NAME); ?></label>
                    <input type="text" class="form-control form--control" name="zip" value="<?php echo esc_attr(get_user_meta($user->ID, 'hyiplab_zip', true)); ?>" id="zip">
                </div>

                <div class="form-group col-sm-4">
                    <label class="form-label" for="city"><?php esc_html_e('City', HYIPLAB_PLUGIN_NAME); ?></label>
                    <input type="text" class="form-control form--control" name="city" value="<?php echo esc_attr(get_user_meta($user->ID, 'hyiplab_city', true)); ?>" id="city">
                </div>

                <div class="form-group col-sm-4">
                    <label class="form-label"><?php esc_html_e('Country', HYIPLAB_PLUGIN_NAME); ?></label>
                    <input class="form-control form--control" value="<?php echo esc_attr(get_user_meta($user->ID, 'hyiplab_country', true)); ?>" disabled>
                </div>
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn--base w-100"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
            </div>
        </form>
    </div>
</div>
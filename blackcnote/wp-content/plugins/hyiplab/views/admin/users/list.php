<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('User', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Email-Phone', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Country', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Joined At', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Balance', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Action', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users->data as $user) {
                            ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold"><?php echo esc_html($user->display_name); ?></span>
                                        <br>
                                        <span class="small">
                                            <a href="<?php echo hyiplab_route_link('admin.users.detail'); ?>&amp;id=<?php echo intval($user->ID); ?>">
                                                <span>@</span><?php echo esc_html($user->user_login); ?>
                                            </a>
                                        </span>
                                    </td>

                                    <td>
                                        <?php echo esc_html($user->user_email); ?><br><?php echo esc_html(get_user_meta($user->ID, 'hyiplab_mobile', true)); ?>
                                    </td>
                                    <td>
                                        <span class="fw-bold" title="<?php echo esc_attr(get_user_meta($user->ID, 'hyiplab_country', true)); ?>">
                                            <?php echo esc_html(get_user_meta($user->ID, 'hyiplab_country_code', true)); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php echo hyiplab_show_date_time($user->user_registered); ?> <br> <?php echo hyiplab_diff_for_humans($user->user_registered); ?>
                                    </td>

                                    <td>
                                        <span class="fw-bold">
                                            <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount(hyiplab_balance($user->ID, 'deposit_wallet')); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="button--group">
                                            <a href="<?php echo hyiplab_route_link('admin.users.detail'); ?>&amp;id=<?php echo intval($user->ID); ?>" class="btn btn-sm btn-outline--primary">
                                                <i class="las la-desktop"></i> <?php esc_html_e('Details', HYIPLAB_PLUGIN_NAME); ?>
                                            </a>
                                        </div>
                                    </td>

                                </tr>
                            <?php } ?>
                            <?php if (hyiplab_check_empty($users->data)) { ?>
                                <tr>
                                    <td class="text-muted text-center" colspan="100%"><?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></td>
                                </tr>
                            <?php } ?>

                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            <?php if ($users->links) { ?>
                <div class="card-footer">
                    <?php echo wp_kses($users->links, hyiplab_allowed_html()); ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php 
$html ='
<div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins">
    <form action="'.hyiplab_route_link('admin.users.all').'" method="GET" class="d-flex flex-wrap gap-2">
        <input type="hidden" name="page" value="hyiplab">
        <input type="hidden" name="module" value="users_all">
        <div class="input-group w-auto flex-fill">
            <input type="search" name="search" class="form-control bg--white" placeholder="'.esc_attr('Username / Email').'" value="'.esc_attr(hyiplab_request()->search).'" id="search">
            <button class="btn btn--primary" type="submit"><i class="la la-search"></i></button>
        </div>
    </form>
</div>';
hyiplab_push_breadcrumb( $html );
?>
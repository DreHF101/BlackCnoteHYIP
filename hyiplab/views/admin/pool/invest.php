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
                                <th><?php esc_html_e('Pool Name', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Invest Amount', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Interest Given', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Status', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($poolInvests->data as $invest) { 
                                $user = \Hyiplab\Models\User::find($invest->user_id);
                                $pool = \Hyiplab\Models\Pool::find($invest->pool_id);
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
                                    <td><?php echo esc_html__($pool->name, HYIPLAB_PLUGIN_NAME); ?></td>
                                    <td> <?php echo esc_html(hyiplab_show_amount($invest->invest_amount)); ?></td>
                                    <td> 
                                    <?php
                                        if ($pool->share_interest){
                                           echo esc_html(hyiplab_currency('sym') . hyiplab_show_amount($invest->invest_amount * $pool->interest / 100)) ;
                                        }else{
                                            echo esc_html__('No return yet!', HYIPLAB_PLUGIN_NAME);
                                        }
                                    
                                    ?></td>
                                    <td>
                                        <?php if ($invest->status == 1) { ?>
                                            <span class="badge badge--success"><?php esc_html_e('Running', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } else { ?>
                                            <span class="badge badge--primary"><?php esc_html_e('Completed', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (hyiplab_check_empty($poolInvests->data)) { ?>
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

<?php 
$html ='
<div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins">
    <form action="'.hyiplab_route_link('admin.staking.time.status').'" method="GET" class="d-flex flex-wrap gap-2">
        <input type="hidden" name="page" value="hyiplab">
        <input type="hidden" name="module" value="stak_invest">
        <div class="input-group w-auto flex-fill">
            <input type="search" name="search" class="form-control bg--white" placeholder="'.esc_attr('Username').'" value="'.esc_attr(hyiplab_request()->search).'" id="search">
            <button class="btn btn--primary" type="submit"><i class="la la-search"></i></button>
        </div>
    </form>
</div>';
hyiplab_push_breadcrumb( $html );
?>
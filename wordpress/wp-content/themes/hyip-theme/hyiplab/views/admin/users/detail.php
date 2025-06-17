<?php hyiplab_layout('admin/layouts/master'); ?>

<div class="row">
    <div class="col-12">
        <div class="row gy-4">

            <div class="col-xxl-4 col-sm-6">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--19">
                    <div class="widget-two__icon b-radius--5 bg--primary">
                        <i class="las la-money-bill-wave-alt"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white"><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount(hyiplab_balance($user->ID, 'deposit_wallet')); ?></h3>
                        <p class="text-white"><?php esc_html_e('Deposit Wallet', HYIPLAB_PLUGIN_NAME); ?></p>
                    </div>
                    <a href="<?php echo hyiplab_route_link('admin.report.transaction'); ?>&amp;username=<?php echo esc_html($user->user_login); ?>" class="widget-two__btn"><?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?></a>
                </div>
            </div>
            <!-- dashboard-w1 end -->

            <div class="col-xxl-4 col-sm-6">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--19">
                    <div class="widget-two__icon b-radius--5 bg--primary">
                        <i class="las la-money-bill-wave-alt"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white"><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount(hyiplab_balance($user->ID, 'interest_wallet')); ?></h3>
                        <p class="text-white"><?php esc_html_e('Interest Wallet', HYIPLAB_PLUGIN_NAME); ?></p>
                    </div>
                    <a href="<?php echo hyiplab_route_link('admin.report.transaction'); ?>&amp;username=<?php echo esc_html($user->user_login); ?>" class="widget-two__btn"><?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?></a>
                </div>
            </div>
            <!-- dashboard-w1 end -->

            <div class="col-xxl-4 col-sm-6">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--primary">
                    <div class="widget-two__icon b-radius--5 bg--primary">
                        <i class="las la-wallet"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white"><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($totalDeposit); ?></h3>
                        <p class="text-white"><?php esc_html_e('Deposits', HYIPLAB_PLUGIN_NAME); ?></p>
                    </div>
                    <a href="<?php echo hyiplab_route_link('admin.deposit.list'); ?>&amp;username=<?php echo esc_html($user->user_login); ?>" class="widget-two__btn"><?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?></a>
                </div>
            </div>
            <!-- dashboard-w1 end -->

            <div class="col-xxl-4 col-sm-6">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--1">
                    <div class="widget-two__icon b-radius--5 bg--primary">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white"><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($totalWithdrawals); ?></h3>
                        <p class="text-white"><?php esc_html_e('Withdrawals', HYIPLAB_PLUGIN_NAME); ?></p>
                    </div>
                    <a href="<?php echo hyiplab_route_link('admin.withdraw.log'); ?>&amp;username=<?php echo esc_html($user->user_login); ?>" class="widget-two__btn"><?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?></a>
                </div>
            </div>
            <!-- dashboard-w1 end -->

            <div class="col-xxl-4 col-sm-6">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--17">
                    <div class="widget-two__icon b-radius--5 bg--primary">
                        <i class="las la-exchange-alt"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white"><?php echo intval($totalTransaction); ?></h3>
                        <p class="text-white"><?php esc_html_e('Transactions', HYIPLAB_PLUGIN_NAME); ?></p>
                    </div>
                    <a href="<?php echo hyiplab_route_link('admin.report.transaction'); ?>&amp;username=<?php echo esc_html($user->user_login); ?>" class="widget-two__btn"><?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?></a>
                </div>
            </div>
            <!-- dashboard-w1 end -->

            <div class="col-xxl-4 col-sm-6">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--18">
                    <div class="widget-two__icon b-radius--5 bg--primary">
                        <i class="las la-ticket-alt"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white"><?php echo intval($pendingTicket); ?></h3>
                        <p class="text-white"><?php esc_html_e('Pending Ticket', HYIPLAB_PLUGIN_NAME); ?></p>
                    </div>
                    <a href="<?php echo hyiplab_route_link('admin.ticket.pending'); ?>" class="widget-two__btn"><?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?></a>
                </div>
            </div>
            <!-- dashboard-w1 end -->

        </div>

        <div class="d-flex flex-wrap gap-3 mt-4">
            <div class="flex-fill">
                <button data-bs-toggle="modal" data-bs-target="#addSubModal" class="btn btn--success btn--shadow w-100 btn-lg bal-btn" data-act="add">
                    <i class="las la-plus-circle"></i> <?php echo esc_html__('Balance', HYIPLAB_PLUGIN_NAME); ?> </button>
            </div>

            <div class="flex-fill">
                <button data-bs-toggle="modal" data-bs-target="#addSubModal" class="btn btn--danger btn--shadow w-100 btn-lg bal-btn" data-act="sub">
                    <i class="las la-minus-circle"></i> <?php echo esc_html__('Balance', HYIPLAB_PLUGIN_NAME); ?> </button>
            </div>

            <div class="flex-fill">
                <a href="<?php echo hyiplab_route_link('admin.users.kyc.data'); ?>&amp;id=<?php echo esc_attr($user->ID); ?>" class="btn btn--dark btn--shadow w-100 btn-lg">
                    <i class="las la-user-check"></i><?php esc_html_e('KYC Data', HYIPLAB_PLUGIN_NAME); ?> </a>
            </div>
            
            <div class="flex-fill">
                <?php if($isBan == 1){ ?>
                <button type="button" class="btn btn--warning btn--gradi btn--shadow w-100 btn-lg userStatus" data-bs-toggle="modal" data-bs-target="#userStatusModal">
                    <i class="las la-ban"></i><?php echo esc_html__('Ban User', HYIPLAB_PLUGIN_NAME); ?>
                </button>
               <?php } else { ?>
                <button type="button" class="btn btn--success btn--gradi btn--shadow w-100 btn-lg userStatus" data-bs-toggle="modal" data-bs-target="#userStatusModal">
                    <i class="las la-undo"></i><?php echo esc_html__('Unban User', HYIPLAB_PLUGIN_NAME); ?>
                </button>
                <?php } ?>
            </div>

        </div>

        <div class="card mt-30">
            <div class="card-header">
                <h5 class="card-title mb-0"><?php esc_html_e('Information of', HYIPLAB_PLUGIN_NAME); ?> <?php echo esc_html($user->display_name); ?></h5>
            </div>
            <div class="card-body">
                <form action="<?php echo hyiplab_route_link('admin.users.update'); ?>&amp;id=<?php echo intval($user->ID); ?>" method="POST" enctype="multipart/form-data">
                    <?php hyiplab_nonce_field('admin.users.update'); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php esc_html_e('Full Name', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input class="form-control w-100" type="text" name="display_name" value="<?php echo esc_attr($user->display_name); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Email', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input class="form-control" type="email" name="email" value="<?php echo esc_attr($user->user_email); ?>" required readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Mobile Number', HYIPLAB_PLUGIN_NAME); ?></label>
                                <div class="input-group ">
                                    <span class="input-group-text mobile-code"></span>
                                    <input type="number" name="mobile" value="<?php echo esc_attr(get_user_meta($user->ID, 'hyiplab_mobile', true)); ?>" id="mobile" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group">
                                <label><?php esc_html_e('Address', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input class="form-control" type="text" name="address" value="<?php echo esc_attr(get_user_meta($user->ID, 'hyiplab_address', true)); ?>">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="form-group">
                                <label><?php esc_html_e('City', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input class="form-control" type="text" name="city" value="<?php echo esc_attr(get_user_meta($user->ID, 'hyiplab_city', true)); ?>">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="form-group ">
                                <label><?php esc_html_e('State', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input class="form-control" type="text" name="state" value="<?php echo esc_attr(get_user_meta($user->ID, 'hyiplab_state', true)); ?>">
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="form-group ">
                                <label><?php esc_html_e('Zip/Postal', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input class="form-control" type="text" name="zip" value="<?php echo esc_attr(get_user_meta($user->ID, 'hyiplab_zip', true)); ?>">
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="form-group ">
                                <label><?php esc_html_e('Country', HYIPLAB_PLUGIN_NAME); ?></label>
                                <select name="country" class="form-control">
                                    <?php foreach ($countries as $key => $country) { ?>
                                        <option data-mobile_code="<?php echo esc_attr($country->dial_code); ?>" value="<?php echo esc_attr($key); ?>"><?php echo esc_html($country->country); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Email Verification', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="<?php esc_attr_e('Verified', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Unverified', HYIPLAB_PLUGIN_NAME); ?>" name="email_verify" <?php if (!get_user_meta($user->ID, 'hyiplab_email_verify', true)) echo 'checked'; ?>>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('KYC', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="<?php esc_attr_e('Verified', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Unverified', HYIPLAB_PLUGIN_NAME); ?>" name="kyc_verify" <?php if (@$kyc == 1) echo 'checked'; ?>>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn--primary w-100 h-45"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="addSubModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="type"></span> 
                    <span><?php echo esc_html__('Balance', HYIPLAB_PLUGIN_NAME); ?></span>
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="<?php echo hyiplab_route_link('admin.users.balance.add.sub');?>&amp;id=<?php echo $user->ID; ?>" method="POST">
                <?php hyiplab_nonce_field('admin.users.balance.add.sub'); ?>
                <input type="hidden" name="act" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="amount" class="required"><?php echo esc_html__('Amount', HYIPLAB_PLUGIN_NAME); ?></label>
                        <div class="input-group">
                            <input type="number" step="any" name="amount" class="form-control" placeholder="Please provide positive amount" required id="amount">
                            <div class="input-group-text"><?php echo hyiplab_currency('text'); ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="wallet_type" class="required"><?php echo esc_html__('Wallet Type', HYIPLAB_PLUGIN_NAME); ?></label>
                        <select name="wallet_type" class="form-control" required id="wallet_type">
                            <option value="" hidden=""><?php echo esc_html__('Select One', HYIPLAB_PLUGIN_NAME); ?></option>
                            <option value="deposit_wallet"><?php echo esc_html__('Deposit Wallet', HYIPLAB_PLUGIN_NAME); ?></option>
                            <option value="interest_wallet"><?php echo esc_html__('Interest Wallet', HYIPLAB_PLUGIN_NAME); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="remark" class="required"><?php echo esc_html__( 'Remark', HYIPLAB_PLUGIN_NAME ); ?></label>
                        <textarea class="form-control" placeholder="Remark" name="remark" rows="4" required id="remark"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary h-45 w-100"><?php echo esc_html__('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="userStatusModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?php if($isBan == 1){ ?>
                        <span> <?php esc_html_e('Ban User', HYIPLAB_PLUGIN_NAME); ?> </span>
                    <?php } else { ?>
                        <span> <?php esc_html_e('Unban User', HYIPLAB_PLUGIN_NAME); ?> </span>
                    <?php } ?>
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="<?php echo hyiplab_route_link('admin.users.ban') ?>&amp;id=<?php echo intval($user->ID); ?>" method="POST">
                <?php hyiplab_nonce_field('admin.users.ban'); ?>
                <div class="modal-body">
                    <?php if($isBan == 1){ ?>
                        <h6 class="mb-2"><?php esc_html_e('If you ban this user he/she won\'t able to access his/her dashboard.', HYIPLAB_PLUGIN_NAME); ?></h6>
                        <div class="form-group">
                            <label for="reason" class="required"><?php esc_html_e('Reason', HYIPLAB_PLUGIN_NAME); ?></label>
                            <textarea class="form-control" name="reason" rows="4" required="" id="reason"></textarea>
                        </div>
                    <?php }else{ ?>
                        <p><span><?php esc_html_e('Ban reason was', HYIPLAB_PLUGIN_NAME); ?>:</span> <?php echo get_user_meta($user->ID, 'hyiplab_user_ban_reason', true); ?></p>
                        <h4 class="text-center mt-3"><?php esc_html_e('Are you sure to unban this user?', HYIPLAB_PLUGIN_NAME); ?></h4>
                    <?php } ?>
                </div>
                <div class="modal-footer">
                    <?php if($isBan == 1){ ?>
                        <button type="submit" class="btn btn--primary h-45 w-100"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                    <?php }else{ ?>
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal"><?php esc_html_e('No', HYIPLAB_PLUGIN_NAME); ?></button>
                        <button type="submit" class="btn btn--primary"><?php esc_html_e('Yes', HYIPLAB_PLUGIN_NAME); ?></button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>


<script>

    jQuery(document).ready(function($) {
        "use strict"
        let mobileElement = $('.mobile-code');
        $('select[name=country]').change(function() {
            mobileElement.text(`+${$('select[name=country] :selected').data('mobile_code')}`);
        });

        $('.bal-btn').click(function(){
            var act = $(this).data('act');
            $('#addSubModal').find('input[name=act]').val(act);
            if (act == 'add') {
                $('.type').text('Add');
            }else{
                $('.type').text('Subtract');
            }
        });

        $('select[name=country]').val('<?php echo get_user_meta($user->ID, "hyiplab_country_code", true); ?>');
        let dialCode = $('select[name=country] :selected').data('mobile_code');
        let mobileNumber = `<?php echo get_user_meta($user->ID, "hyiplab_mobile", true); ?>`;
        mobileNumber = mobileNumber.replace(dialCode, '');
        $('input[name=mobile]').val(mobileNumber);
        mobileElement.text(`+${dialCode}`);
    });

</script>
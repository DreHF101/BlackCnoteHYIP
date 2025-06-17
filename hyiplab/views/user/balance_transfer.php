<?php hyiplab_layout('user/layouts/master'); ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="mb-4">
            <h3 class="mb-2"><?php esc_html_e('Transfer Balance', HYIPLAB_PLUGIN_NAME); ?></h3>
            <p><?php esc_html_e('You can transfer the balance to another user from both of your wallets. The transferred amount will be added to the deposit wallet of the targeted user.', HYIPLAB_PLUGIN_NAME); ?></p>
        </div>
        <div class="card custom--card">
            <form action="<?php echo hyiplab_route_link('user.transfer.balance.submit'); ?>" method="post" enctype="multipart/form-data">
                <?php hyiplab_nonce_field('user.transfer.balance.submit'); ?>
                <div class="card-body">
                    <div class="form-group">
                        <label><?php esc_html_e('Wallet', HYIPLAB_PLUGIN_NAME); ?></label>
                        <select class="form-control form--control form-select" name="wallet">
                            <option value=""><?php esc_html_e('Select a wallet', HYIPLAB_PLUGIN_NAME); ?></option>
                            <option value="deposit_wallet"><?php esc_html_e('Deposit Wallet', HYIPLAB_PLUGIN_NAME); ?> - <?php echo hyiplab_show_amount(hyiplab_balance($user->ID, 'deposit_wallet')); ?> <?php echo hyiplab_currency('text'); ?></option>
                            <option value="interest_wallet"><?php esc_html_e('Interest Wallet', HYIPLAB_PLUGIN_NAME); ?> - <?php echo hyiplab_show_amount(hyiplab_balance($user->ID, 'interest_wallet')); ?> <?php echo hyiplab_currency('text'); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?php esc_html_e('Username', HYIPLAB_PLUGIN_NAME); ?></label>
                        <input type="text" name="username" class="form-control form--control findUser" required>
                        <code class="error-message"></code>
                    </div>
                    <div class="form-group">
                        <label><?php esc_html_e('Amount', HYIPLAB_PLUGIN_NAME); ?> <small class="text--success">(<?php esc_html_e('Charge', HYIPLAB_PLUGIN_NAME); ?>: <?php echo hyiplab_show_amount(get_option('hyiplab_balance_transfer_fixed_charge')); ?> <?php echo hyiplab_currency('text'); ?> + <?php echo hyiplab_show_amount(get_option('hyiplab_balance_transfer_percent_charge')); ?>%)</small></label>
                        <div class="input-group">
                            <input type="number" step="any" autocomplete="off" name="amount" class="form-control form--control" required>
                            <span class="input-group-text"><?php echo hyiplab_currency('text'); ?></span>
                        </div>
                        <small><code class="calculation"></code></small>
                    </div>

                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn--base w-100"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        'use strict';
        $('input[name=amount]').on('input', function() {
            var amo = parseFloat($(this).val());
            var calculation = amo + (parseFloat(<?php echo get_option('hyiplab_balance_transfer_fixed_charge'); ?>) + (amo * parseFloat(<?php echo get_option('hyiplab_balance_transfer_percent_charge'); ?>)) / 100);
            if (calculation) {
                $('.calculation').text(calculation + ' <?php echo hyiplab_currency('text'); ?> will cut from your selected wallet');
            } else {
                $('.calculation').text('');
            }
        });
    })
</script>
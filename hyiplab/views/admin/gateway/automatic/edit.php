<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form action="<?php echo hyiplab_route_link('admin.gateway.automatic.update'); ?>&amp;id=<?php echo intval($gateway->id); ?>" method="POST" enctype="multipart/form-data">
                <?php hyiplab_nonce_field('admin.gateway.automatic.update'); ?>
                <input type="hidden" name="alias" value="<?php echo esc_attr($gateway->alias); ?>">
                <input type="hidden" name="description" value="<?php echo esc_attr($gateway->description); ?>">
                <div class="card-body">
                    <div class="payment-method-item block-item">
                        <div class="payment-method-header">
                            <div class="content ps-0 w-100">
                                <?php if (count((array) $supportedCurrencies) > 0) { ?>
                                    <div class="d-flex justify-content-between">
                                        <h3><?php echo esc_html($gateway->name) ?></h3>
                                        <div class="input-group d-flex flex-wrap justify-content-end width-375">
                                            <select class="newCurrencyVal ">
                                                <option value=""><?php esc_html_e('Select currency', HYIPLAB_PLUGIN_NAME); ?></option>
                                                <?php foreach ($supportedCurrencies as $currency => $symbol) { ?>
                                                    <option value="<?php echo esc_attr($currency); ?>" data-symbol="<?php echo esc_attr($symbol); ?>"><?php echo esc_attr($currency); ?></option>
                                                <?php } ?>
                                            </select>
                                            <button type="button" class="btn btn--primary input-group-text newCurrencyBtn" data-crypto="<?php echo esc_attr($gateway->crypto); ?>" data-name="<?php echo esc_attr($gateway->name); ?>"><?php esc_html_e('Add new', HYIPLAB_PLUGIN_NAME); ?></button>
                                        </div>
                                    </div>
                                <?php } ?>

                                <p><?php echo esc_html($gateway->description); ?></p>

                            </div>
                        </div>

                        <?php if ($gateway->code < 1000 && $gateway->extra) { ?>
                            <div class="payment-method-body mt-2">
                                <h4 class="mb-3"><?php esc_html_e('Configurations', HYIPLAB_PLUGIN_NAME); ?></h4>
                                <div class="row">
                                    <?php foreach (json_decode($gateway->extra) as $key => $param) { ?>
                                        <div class="form-group col-lg-6">
                                            <label><?php echo esc_html($param->title); ?></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" value="<?php echo esc_url(hyiplab_route_link(strtolower($param->value))); ?>" readonly />
                                                <button type="button" class="copyInput input-group-text" title="<?php esc_html_e('Copy', HYIPLAB_PLUGIN_NAME); ?>"><i class="fa fa-copy"></i></button>
                                            </div>

                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="payment-method-body mt-2">
                            <h4 class="mb-3"><?php esc_html_e('Global Setting for', HYIPLAB_PLUGIN_NAME); ?> <?php echo esc_html($gateway->name); ?></h4>
                            <div class="row">
                                <?php foreach ($globalCredentials as $key => $param) { ?>
                                    <div class="form-group col-lg-6">
                                        <label><?php echo esc_html($param->title) ?></label>
                                        <input type="text" class="form-control" name="global[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($param->value); ?>" required />
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <!-- payment-method-item start -->

                    <?php foreach ($activatedCurrencies as $gatewayCurrency) { ?>
                        <input type="hidden" name="currency[<?php echo esc_attr($currencyIndex); ?>][symbol]" value="<?php echo esc_attr($gatewayCurrency->symbol); ?>">
                        <div class="payment-method-item block-item child--item">
                            <div class="payment-method-header">
                                <div class="content w-100 ps-0">
                                    <div class="d-flex justify-content-between">
                                        <div class="form-group">
                                            <h4 class="mb-3"><?php echo esc_html($gateway->name); ?> - <?php echo esc_html($gatewayCurrency->currency); ?></h4>
                                            <input type="text" class="form-control" name="currency[<?php echo esc_attr($currencyIndex); ?>][name]" value="<?php echo esc_attr($gatewayCurrency->name); ?>" required />
                                        </div>
                                        <div class="remove-btn">
                                            <button type="button" class="btn btn--danger confirmationBtn" data-question="<?php echo esc_attr(esc_html__('Are you sure to delete this gateway currency?', HYIPLAB_PLUGIN_NAME)); ?>" data-action="<?php echo hyiplab_route_link('admin.gateway.automatic.currency.remove'); ?>&amp;id=<?php echo intval($gatewayCurrency->id); ?>" data-nonce="<?php echo esc_attr(hyiplab_nonce('admin.gateway.automatic.currency.remove'));?>">
                                                <i class="la la-trash-o me-2"></i> <?php esc_html_e('Remove', HYIPLAB_PLUGIN_NAME); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="payment-method-body">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                                        <div class="card border--primary mt-2">
                                            <h5 class="card-header bg--primary"><?php esc_html_e('Range', HYIPLAB_PLUGIN_NAME); ?></h5>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label><?php esc_html_e('Minimum Amount', HYIPLAB_PLUGIN_NAME); ?></label>
                                                    <div class="input-group">
                                                        <input type="number" step="any" class="form-control" name="currency[<?php echo  $currencyIndex; ?>][min_amount]" value="<?php echo hyiplab_get_amount($gatewayCurrency->min_amount); ?>" required />
                                                        <div class="input-group-text"><?php echo hyiplab_currency('text'); ?></div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label><?php esc_html_e('Maximum Amount', HYIPLAB_PLUGIN_NAME); ?></label>
                                                    <div class="input-group">
                                                        <input type="number" step="any" class="form-control" name="currency[<?php echo esc_attr($currencyIndex); ?>][max_amount]" value="<?php echo hyiplab_get_amount($gatewayCurrency->max_amount); ?>" required />
                                                        <div class="input-group-text"><?php echo hyiplab_currency('text'); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                                        <div class="card border--primary mt-2">
                                            <h5 class="card-header bg--primary"><?php esc_html_e('Charge', HYIPLAB_PLUGIN_NAME); ?></h5>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label><?php esc_html_e('Fixed Charge', HYIPLAB_PLUGIN_NAME); ?></label>
                                                    <div class="input-group">
                                                        <input type="number" step="any" class="form-control" name="currency[<?php echo esc_attr($currencyIndex); ?>][fixed_charge]" value="<?php echo hyiplab_get_amount($gatewayCurrency->fixed_charge); ?>" required />
                                                        <div class="input-group-text"><?php echo hyiplab_currency('text'); ?></div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label><?php esc_html_e('Percent Charge', HYIPLAB_PLUGIN_NAME); ?></label>
                                                    <div class="input-group">
                                                        <input type="number" step="any" class="form-control" name="currency[<?php echo esc_attr($currencyIndex); ?>][percent_charge]" value="<?php echo hyiplab_get_amount($gatewayCurrency->percent_charge); ?>" required />
                                                        <div class="input-group-text">%</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                                        <div class="card border--primary mt-2">
                                            <h5 class="card-header bg--primary"><?php esc_html_e('Currency', HYIPLAB_PLUGIN_NAME); ?></h5>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><?php esc_html_e('Currency', HYIPLAB_PLUGIN_NAME); ?></label>
                                                            <input type="text" name="currency[<?php echo esc_attr($currencyIndex); ?>][currency]" class="form-control border-radius-5 " value="<?php echo esc_attr($gatewayCurrency->currency); ?>" readonly />
                                                        </div>

                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><?php esc_html_e('Symbol', HYIPLAB_PLUGIN_NAME); ?></label>
                                                            <input type="text" name="currency[<?php echo esc_attr($currencyIndex); ?>][symbol]" class="form-control border-radius-5 symbl" value="<?php echo esc_attr($gatewayCurrency->symbol); ?>" data-crypto="<?php echo esc_attr($gateway->crypto); ?>" required />
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label><?php esc_html_e('Rate', HYIPLAB_PLUGIN_NAME); ?></label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">1 <?php echo hyiplab_currency('text'); ?> =</div>
                                                        <input type="number" step="any" class="form-control" name="currency[<?php echo  $currencyIndex; ?>][rate]" value="<?php echo hyiplab_get_amount($gatewayCurrency->rate); ?>" required />
                                                        <div class="input-group-text"><span class="currency_symbol"><?php echo hyiplab_gateway_base_symbol($gatewayCurrency, $gateway); ?></span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <?php if (count($privateCredentials)  != 0) { ?>
                                        <?php
                                        $globalParameters = json_decode($gatewayCurrency->gateway_parameter);
                                        ?>
                                        <div class="col-lg-12">
                                            <div class="card border--primary mt-4">
                                                <h5 class="card-header bg--dark"><?php esc_html_e('Configuration', HYIPLAB_PLUGIN_NAME); ?></h5>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <?php foreach ($privateCredentials as $key => $param) { ?>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label><?php echo esc_html($param->title); ?></label>
                                                                    <input type="text" class="form-control" name="currency[<?php echo esc_attr($currencyIndex); ?>][param][<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($globalParameters->$key); ?>" required />
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php $currencyIndex++; ?>
                    <?php } ?>

                    <!-- payment-method-item end -->


                    <!-- **new payment-method-item start -->
                    <div class="payment-method-item child--item newMethodCurrency d-none">
                        <input disabled type="hidden" name="currency[<?php echo esc_attr($currencyIndex); ?>][symbol]" class="currencySymbol">
                        <div class="payment-method-header">

                            <div class="content w-100 ps-0">
                                <div class="d-flex justify-content-between">
                                    <div class="form-group">
                                        <h4 class="mb-3" id="payment_currency_name"><?php esc_html_e('Name', HYIPLAB_PLUGIN_NAME); ?></h4>
                                        <input disabled type="text" class="form-control" name="currency[<?php echo esc_attr($currencyIndex); ?>][name]" required />
                                    </div>
                                    <div class="remove-btn">
                                        <button type="button" class="btn btn-danger newCurrencyRemove">
                                            <i class="la la-trash-o me-2"></i> <?php esc_html_e('Remove', HYIPLAB_PLUGIN_NAME); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="payment-method-body">
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                                    <div class="card border--primary mt-2">
                                        <h5 class="card-header bg--primary"><?php esc_html_e('Range', HYIPLAB_PLUGIN_NAME); ?></h5>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label><?php esc_html_e('Minimum Amount', HYIPLAB_PLUGIN_NAME); ?></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><?php echo hyiplab_currency('text') ?></div>
                                                    <input disabled type="number" step="any" class="form-control" name="currency[<?php echo esc_attr($currencyIndex); ?>][min_amount]" required />
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label><?php esc_html_e('Maximum Amount', HYIPLAB_PLUGIN_NAME); ?></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><?php echo hyiplab_currency('text') ?></div>
                                                    <input disabled type="number" step="any" class="form-control" name="currency[<?php echo esc_attr($currencyIndex); ?>][max_amount]" required />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                                    <div class="card border--primary mt-2">
                                        <h5 class="card-header bg--primary"><?php esc_html_e('Charge', HYIPLAB_PLUGIN_NAME); ?></h5>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label><?php esc_html_e('Fixed Charge', HYIPLAB_PLUGIN_NAME); ?></label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><?php echo hyiplab_currency('text') ?></div>
                                                    <input disabled type="number" step="any" class="form-control" name="currency[<?php echo esc_attr($currencyIndex) ?>][fixed_charge]" required />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label><?php esc_html_e('Percent Charge', HYIPLAB_PLUGIN_NAME); ?></label>
                                                <div class="input-group">
                                                    <div class="input-group-text">%</div>
                                                    <input disabled type="number" step="any" class="form-control" name="currency[<?php echo esc_attr($currencyIndex); ?>][percent_charge]" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                                    <div class="card border--primary mt-2">
                                        <h5 class="card-header bg--primary"><?php esc_html_e('Currency', HYIPLAB_PLUGIN_NAME); ?></h5>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><?php esc_html_e('Currency', HYIPLAB_PLUGIN_NAME); ?></label>
                                                        <input disabled type="step" class="form-control currencyText border-radius-5" name="currency[<?php echo esc_attr($currencyIndex); ?>][currency]" readonly />
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><?php esc_html_e('Symbol', HYIPLAB_PLUGIN_NAME); ?></label>
                                                        <input disabled type="text" name="currency[<?php echo esc_attr($currencyIndex); ?>][symbol]" class="form-control border-radius-5 symbl" ata-crypto="<?php echo esc_attr($gateway->crypto); ?>" disabled />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label><?php esc_html_e('Rate', HYIPLAB_PLUGIN_NAME); ?></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <b>1 </b>&nbsp; <?php echo hyiplab_currency('text') ?>&nbsp; =
                                                    </span>
                                                    <input disabled type="number" step="any" class="form-control" name="currency[<?php echo esc_attr($currencyIndex); ?>][rate]" required />
                                                    <div class="input-group-text"><span class="currency_symbol"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (count($privateCredentials) != 0) { ?>
                                    <div class="col-lg-12">
                                        <div class="card border--primary mt-4">
                                            <h5 class="card-header bg--dark"><?php esc_html_e('Configuration', HYIPLAB_PLUGIN_NAME); ?></h5>
                                            <div class="card-body">
                                                <div class="row">
                                                    <?php foreach ($privateCredentials as $key => $param) { ?>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label><?php echo esc_html($param->title); ?></label>
                                                                <input disabled type="text" class="form-control" name="currency[<?php echo esc_attr($currencyIndex); ?>][param][<?php echo esc_attr($key); ?>]" required />
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                    <!-- **new payment-method-item end -->
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45">
                        <?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php hyiplab_include('partials/confirmation'); ?>

<script>
    jQuery(document).ready(function($) {
        "use strict";
        $(document).on('click', '.confirmationBtn', function() {
            var modal = $('#confirmationModal');
            let data = $(this).data();
            modal.find('.question').text(`${data.question}`);
            modal.find('form').attr('action', `${data.action}`);
            modal.modal('show');
        });

        $('.newCurrencyBtn').on('click', function() {
            var form = $('.newMethodCurrency');
            var getCurrencySelected = $('.newCurrencyVal').find(':selected').val();
            var currency = $(this).data('crypto') == 1 ? 'USD' : `${getCurrencySelected}`;
            if (!getCurrencySelected) return;
            form.find('input').removeAttr('disabled');
            var symbol = $('.newCurrencyVal').find(':selected').data('symbol');
            form.find('.currencyText').val(getCurrencySelected);
            form.find('.currency_symbol').text(currency);
            $('#payment_currency_name').text(`${$(this).data('name')} - ${getCurrencySelected}`);
            form.removeClass('d-none');
            $('html, body').animate({
                scrollTop: $('html, body').height()
            }, 'slow');

            $('.newCurrencyRemove').on('click', function() {
                form.find('input').val('');
                form.remove();
            });
        });

        $('.symbl').on('input', function() {
            var curText = $(this).data('crypto') == 1 ? 'USD' : $(this).val();
            $(this).parents('.payment-method-body').find('.currency_symbol').text(curText);
        });

        $('.copyInput').on('click', function(e) {
            var copybtn = $(this);
            var input = copybtn.closest('.input-group').find('input');
            if (input && input.select) {
                input.select();
                try {
                    document.execCommand('SelectAll')
                    document.execCommand('Copy', false, null);
                    input.blur();
                    alert('<?php esc_html_e('Copied', HYIPLAB_PLUGIN_NAME) ?>');
                } catch (err) {
                    alert('<?php esc_html_e('Please press Ctrl/Cmd + C to copy', HYIPLAB_PLUGIN_NAME) ?>');
                }
            }
        });
    });
</script>
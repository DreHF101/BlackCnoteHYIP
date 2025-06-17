<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form action="<?php echo hyiplab_route_link('admin.withdraw.method.update'); ?>&amp;id=<?php echo intval($method->id); ?>" method="POST" enctype="multipart/form-data">
                <?php hyiplab_nonce_field('admin.withdraw.method.update'); ?>
                <div class="card-body">
                    <div class="payment-method-item">
                        <div class="form-group">
                            <label><?php esc_html_e('Name', HYIPLAB_PLUGIN_NAME); ?></label>
                            <input type="text" class="form-control" name="name" value="<?php echo esc_attr($method->name); ?>" required />
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php esc_html_e('Currency', HYIPLAB_PLUGIN_NAME); ?></label>
                                    <div class="input-group">
                                        <input type="text" name="currency" class="form-control border-radius-5" value="<?php echo esc_attr($method->currency); ?>" required />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php esc_html_e('Rate', HYIPLAB_PLUGIN_NAME); ?></label>
                                    <div class="input-group">
                                        <span class="input-group-text">1 <?php echo hyiplab_currency('text'); ?>
                                            =
                                        </span>
                                        <input type="text" class="form-control" name="rate" value="<?php echo esc_attr(hyiplab_get_amount($method->rate)); ?>" required />
                                        <span class="currency_symbol input-group-text"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="payment-method-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="card border--primary mb-2">
                                        <h5 class="card-header bg--primary"><?php esc_html_e('Range', HYIPLAB_PLUGIN_NAME); ?></h5>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label><?php esc_html_e('Minimum Amount', HYIPLAB_PLUGIN_NAME); ?></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="min_limit" value="<?php echo esc_attr(hyiplab_get_amount($method->min_limit)); ?>" required />
                                                    <span class="input-group-text"><?php echo hyiplab_currency('text'); ?></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label><?php esc_html_e('Maximum Amount', HYIPLAB_PLUGIN_NAME); ?></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="max_limit" value="<?php echo esc_attr(hyiplab_get_amount($method->max_limit)); ?>" required />
                                                    <span class="input-group-text"><?php echo hyiplab_currency('text'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="card border--primary">
                                        <h5 class="card-header bg--primary"><?php esc_html_e('Charge', HYIPLAB_PLUGIN_NAME); ?></h5>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label><?php esc_html_e('Fixed Charge', HYIPLAB_PLUGIN_NAME); ?></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="fixed_charge" value="<?php echo esc_attr(hyiplab_get_amount($method->fixed_charge)); ?>" required />
                                                    <span class="input-group-text"><?php echo hyiplab_currency('text'); ?></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label><?php esc_html_e('Percent Charge', HYIPLAB_PLUGIN_NAME); ?></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="percent_charge" value="<?php echo esc_attr(hyiplab_get_amount($method->percent_charge)); ?>" required>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="card border--dark my-2">
                                        <h5 class="card-header bg--dark"><?php esc_html_e('Withdraw Instruction', HYIPLAB_PLUGIN_NAME); ?> </h5>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <textarea rows="5" class="form-control border-radius-5 nicEdit" name="instruction"><?php echo balanceTags(wp_kses($method->description, hyiplab_allowed_html())); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="card border--primary p-0 w-100">
                                        <div class="card-header bg--primary d-flex justify-content-between">
                                            <h5 class="text-white"><?php esc_html_e('User Data', HYIPLAB_PLUGIN_NAME); ?></h5>
                                            <button type="button" class="btn btn-sm btn-outline-light float-end form-generate-btn"><?php esc_html_e('Add New', HYIPLAB_PLUGIN_NAME); ?></button>
                                        </div>
                                        <div class="card-body">
                                            <div class="row addedField">
                                                <?php if (!hyiplab_check_empty($form)) {
                                                    $form->form_data = json_decode(json_encode(maybe_unserialize($form->form_data)));
                                                    foreach ($form->form_data as $key => $formData) {
                                                ?>
                                                        <div class="col-md-4">
                                                            <div class="card border mb-3" id="<?php echo esc_attr($key); ?>">
                                                                <input type="hidden" name="form_generator[is_required][]" value="<?php echo esc_attr($formData->is_required); ?>">
                                                                <input type="hidden" name="form_generator[extensions][]" value="<?php echo esc_attr($formData->extensions); ?>">
                                                                <input type="hidden" name="form_generator[options][]" value="<?php echo implode(',', $formData->options); ?>">

                                                                <div class="card-body">
                                                                    <div class="form-group">
                                                                        <label><?php esc_html_e('Label', HYIPLAB_PLUGIN_NAME); ?></label>
                                                                        <input type="text" name="form_generator[form_label][]" class="form-control" value="<?php echo esc_attr($formData->name); ?>" readonly>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label><?php esc_html_e('Type', HYIPLAB_PLUGIN_NAME); ?></label>
                                                                        <input type="text" name="form_generator[form_type][]" class="form-control" value="<?php echo esc_attr($formData->type); ?>" readonly>
                                                                    </div>
                                                                    <?php

                                                                    //Show
                                                                    $jsonData = [
                                                                        'type' => $formData->type,
                                                                        'is_required' => $formData->is_required,
                                                                        'label' => $formData->name,
                                                                        'extensions' => explode(',', $formData->extensions) ?? 'null',
                                                                        'options' => $formData->options,
                                                                        'old_id' => '',
                                                                    ];
                                                                    ?>

                                                                    <div class="btn-group w-100">
                                                                        <button type="button" class="btn btn--primary editFormData" data-form_item='<?php echo wp_json_encode($jsonData); ?>' data-update_id="<?php echo esc_attr($key); ?>">
                                                                            <i class="las la-pen"></i>
                                                                        </button>
                                                                        <button type="button" class="btn btn--danger removeFormData"><i class="las la-times"></i></button>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                <?php }
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div><!-- card end -->
    </div>
</div>

<?php hyiplab_include('form/modal'); ?>

<?php hyiplab_include('form/generator'); ?>

<script>
    "use strict";
    var formGenerator = new FormGenerator();
    formGenerator.totalField = "<?php echo esc_html($form ? count((array) $form->form_data) : 0); ?>";
</script>

<?php hyiplab_include('form/action'); ?>

<script>
    jQuery(document).ready(function($) {
        "use strict";
        $('input[name=currency]').on('input', function() {
            $('.currency_symbol').text($(this).val());
        });
        $('.currency_symbol').text($('input[name=currency]').val());

        $(document).on('click', '.removeBtn', function() {
            $(this).closest('.user-data').remove();
        });

        <?php if (hyiplab_old('currency')) { ?>
            $('input[name=currency]').trigger('input');
        <?php } ?>
    });
</script>
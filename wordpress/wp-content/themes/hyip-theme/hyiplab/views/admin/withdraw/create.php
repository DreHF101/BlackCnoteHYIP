<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form action="<?php echo hyiplab_route_link('admin.withdraw.method.store'); ?>" method="POST" enctype="multipart/form-data">
                <?php hyiplab_nonce_field('admin.withdraw.method.store'); ?>
                <div class="card-body">
                    <div class="payment-method-item">
                        <div class="payment-method-body">
                            <div class="form-group">
                                <label><?php esc_html_e('Name', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="text" class="form-control" name="name" value="<?php echo hyiplab_old('name'); ?>" required />
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php esc_html_e('Currency', HYIPLAB_PLUGIN_NAME); ?></label>
                                        <div class="input-group">
                                            <input type="text" name="currency" class="form-control border-radius-5" value="<?php echo hyiplab_old('currency'); ?>" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php esc_html_e('Rate', HYIPLAB_PLUGIN_NAME); ?></label>
                                        <div class="input-group">
                                            <div class="input-group-text">1 <?php echo hyiplab_currency('text'); ?> =</div>
                                            <input type="number" step="any" class="form-control" name="rate" value="<?php echo hyiplab_old('rate'); ?>" required />
                                            <div class="input-group-text">
                                                <span class="currency_symbol"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="card border--primary mb-2">
                                        <h5 class="card-header bg--primary"><?php esc_html_e('Range', HYIPLAB_PLUGIN_NAME); ?></h5>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label><?php esc_html_e('Minimum Amount', HYIPLAB_PLUGIN_NAME); ?></label>
                                                <div class="input-group">
                                                    <input type="number" step="any" class="form-control" name="min_limit" value="<?php echo hyiplab_old('min_limit'); ?>" required />
                                                    <div class="input-group-text"> <?php echo hyiplab_currency('text'); ?> </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label><?php esc_html_e('Maximum Amount', HYIPLAB_PLUGIN_NAME); ?></label>
                                                <div class="input-group">
                                                    <input type="number" step="any" class="form-control" name="max_limit" value="<?php echo hyiplab_old('max_limit'); ?>" required />
                                                    <div class="input-group-text"> <?php echo hyiplab_currency('text'); ?> </div>
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
                                                    <input type="number" step="any" class="form-control" name="fixed_charge" value="<?php echo hyiplab_old('fixed_charge'); ?>" required />
                                                    <div class="input-group-text"><?php echo hyiplab_currency('text'); ?></div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label><?php esc_html_e('Percent Charge', HYIPLAB_PLUGIN_NAME); ?></label>
                                                <div class="input-group">
                                                    <input type="number" step="any" class="form-control" name="percent_charge" value="<?php echo hyiplab_old('percent_charge'); ?>" required>
                                                    <div class="input-group-text">%</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="card border--primary my-2">

                                        <h5 class="card-header bg--primary"><?php esc_html_e('Withdraw Instruction', HYIPLAB_PLUGIN_NAME); ?></h5>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <textarea rows="5" class="form-control border-radius-5 nicEdit" name="instruction"><?php echo hyiplab_old('instruction'); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="card border--primary mt-3">
                                        <div class="card-header bg--primary d-flex justify-content-between">
                                            <h5 class="text-white"><?php esc_html_e('User Data', HYIPLAB_PLUGIN_NAME); ?></h5>
                                            <button type="button" class="btn btn-sm btn-outline-light float-end form-generate-btn"> <i class="la la-fw la-plus"></i><?php esc_html_e('Add New', HYIPLAB_PLUGIN_NAME); ?></button>
                                        </div>
                                        <div class="card-body">
                                            <div class="row addedField">
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
    "use strict"
    var formGenerator = new FormGenerator();
</script>

<?php hyiplab_include('form/action'); ?>
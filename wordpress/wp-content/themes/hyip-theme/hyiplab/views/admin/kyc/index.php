<?php hyiplab_layout('admin/layouts/master'); ?>

<div class="row mb-none-30">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg--primary d-flex justify-content-between">
                <h5 class="text-white"><?php echo esc_html__('KYC Form for User', HYIPLAB_PLUGIN_NAME); ?></h5>
                <button type="button" class="btn btn-sm btn-outline-light float-end form-generate-btn"> 
                    <i class="la la-fw la-plus"></i><?php echo esc_html__('Add New', HYIPLAB_PLUGIN_NAME); ?></button>
            </div>
            <div class="card-body">
                <form action="<?php echo hyiplab_route_link('admin.kyc.store');?>&amp;id=<?php echo esc_attr($form?->id); ?>" method="post">
                    <?php hyiplab_nonce_field('admin.kyc.store'); ?>                        
                        <div class="row addedField">
                        <?php if (isset($form->data) && !hyiplab_check_empty($form->data)) {
                            $form_data = json_decode(json_encode(maybe_unserialize($form->data->form_data)));
                            foreach ($form_data as $key => $formData) {
                                ?>
                                    <div class="col-md-4">
                                        <div class="card border mb-3" id="<?php echo esc_attr($key); ?>">
                                            <input type="hidden" name="form_generator[is_required][]" value="<?php echo esc_attr($formData->is_required); ?>">
                                            <input type="hidden" name="form_generator[extensions][]" value="<?php echo esc_attr($formData->extensions); ?>">
                                            <input type="hidden" name="form_generator[options][]" value="<?php echo esc_attr(implode(',', $formData->options)); ?>">

                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label><?php esc_html_e('Label', 'ovoform'); ?></label>
                                                    <input type="text" name="form_generator[form_label][]" class="form-control" value="<?php echo esc_attr($formData->name); ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label><?php esc_html_e('Type', 'ovoform'); ?></label>
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
                    <button type="submit" class="btn btn--primary w-100 h-45"><?php echo esc_html__('Submit', HYIPLAB_PLUGIN_NAME); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<?php hyiplab_include('form/modal'); ?>
<?php hyiplab_include('form/generator'); ?>
<script>
    "use strict";
    var formGenerator = new FormGenerator();
</script>
<?php hyiplab_include('form/action'); ?>


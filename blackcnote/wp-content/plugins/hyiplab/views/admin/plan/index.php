<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                            <th><?php esc_html_e('Plan ID', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Name', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Invest Limit', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Interest', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Time', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Status', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Action', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($plans as $plan) { 
                                
                                $time = get_hyiplab_time_setting($plan->time_setting_id);
                                
                                ?>
                                <tr>
                                    <td><?php echo esc_html($plan->id); ?></td>
                                    <td><?php echo esc_html($plan->name); ?></td>
                                    <td>
                                        <?php if ($plan->fixed_amount == 0) { ?>
                                            <span><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($plan->minimum); ?>
                                                - <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($plan->maximum); ?></span>
                                        <?php } else { ?>
                                            <span><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($plan->fixed_amount); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php echo hyiplab_show_amount($plan->interest); ?> <?php if ($plan->interest_type == 1) {
                                                                                        echo '%';
                                                                                    } else {
                                                                                        echo hyiplab_currency('text');
                                                                                    } ?>
                                    </td>
                                    <td><?php echo esc_html($time->time); ?> <?php echo _n('Hour', 'Hours', $time->time, HYIPLAB_PLUGIN_NAME); ?></td>
                                    <td>
                                        <?php if ($plan->status == 1) { ?>
                                            <span class="badge badge--success"><?php esc_html_e('Active', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } else { ?>
                                            <span class="badge badge--warning"><?php esc_html_e('Inactive', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline--primary modalShow me-2" data-type="edit" data-bs-toggle="modal" data-bs-target="#editModal" data-resource='<?php echo wp_json_encode($plan); ?>' data-action="<?php echo hyiplab_route_link('admin.plan.update'); ?>&amp;id=<?php echo intval($plan->id); ?>"><i class="las la-pen"></i><?php esc_html_e('Edit', HYIPLAB_PLUGIN_NAME); ?></button>
                                        <?php if ($plan->status) { ?>
                                            <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="<?php esc_attr_e('Are you sure to disable this plan?');?>" data-action="<?php echo hyiplab_route_link('admin.plan.status');?>&amp;id=<?php echo intval($plan->id);?>" data-nonce="<?php echo esc_attr(hyiplab_nonce('admin.plan.status'));?>"><i class="las la-eye-slash"></i><?php esc_html_e('Disable', HYIPLAB_PLUGIN_NAME);?></button>
                                        <?php } else { ?>
                                            <button class="btn btn-sm btn-outline--success confirmationBtn" data-question="<?php esc_attr_e('Are you sure to enable this plan?');?>" data-action="<?php echo hyiplab_route_link('admin.plan.status');?>&amp;id=<?php echo intval($plan->id);?>" data-nonce="<?php echo esc_attr(hyiplab_nonce('admin.plan.status'));?>"><i class="las la-eye"></i><?php esc_html_e('Enable', HYIPLAB_PLUGIN_NAME);?></button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (hyiplab_check_empty($plans)) { ?>
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

<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e('Add New Plan', HYIPLAB_PLUGIN_NAME); ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="<?php echo hyiplab_route_link('admin.plan.store'); ?>" method="post">
                <?php hyiplab_nonce_field('admin.plan.store'); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Name', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Invest type', HYIPLAB_PLUGIN_NAME); ?></label>
                                <select name="invest_type" class="form-control" required>
                                    <option value="1"><?php esc_html_e('Range', HYIPLAB_PLUGIN_NAME); ?></option>
                                    <option value="2"><?php esc_html_e('Fixed', HYIPLAB_PLUGIN_NAME); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row amount-fields"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Interest type', HYIPLAB_PLUGIN_NAME); ?></label>
                                <select name="interest_type" class="form-control" required>
                                    <option value="1"><?php esc_html_e('Percent', HYIPLAB_PLUGIN_NAME); ?></option>
                                    <option value="2"><?php esc_html_e('Fixed', HYIPLAB_PLUGIN_NAME); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Interest', HYIPLAB_PLUGIN_NAME); ?></label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="interest" required>
                                    <span class="input-group-text interest-type"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Time', HYIPLAB_PLUGIN_NAME); ?></label>
                                <select name="time" class="form-control" required>
                                    <option value=""><?php esc_html_e('Select One', HYIPLAB_PLUGIN_NAME); ?></option>
                                    <?php foreach ($times as $time) { ?>
                                        <option value="<?php echo esc_attr($time->id); ?>"><?php echo esc_html($time->name); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Return type', HYIPLAB_PLUGIN_NAME); ?></label>
                                <select name="return_type" class="form-control" required>
                                    <option value="1"><?php esc_html_e('Lifetime', HYIPLAB_PLUGIN_NAME); ?></option>
                                    <option value="0"><?php esc_html_e('Repeat', HYIPLAB_PLUGIN_NAME); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="repeat-time row"></div>
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for=""><?php esc_html_e('Compound Interest', HYIPLAB_PLUGIN_NAME); ?> <i class="las la-info-circle" title="<?php echo esc_attr('Provide investors with the choice to reinvest their earnings, allowing for compounding growth over time.', HYIPLAB_PLUGIN_NAME); ?>"></i></label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="<?php echo esc_attr(__('Yes', HYIPLAB_PLUGIN_NAME)); ?>" data-off="<?php echo esc_attr(__('No', HYIPLAB_PLUGIN_NAME))?>" name="compound_interest">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 holdCapitalGroup">
                            <div class="form-group">
                                <label for=""><?php esc_html_e('Hold Capital', HYIPLAB_PLUGIN_NAME); ?> <i class="las la-info-circle" title="<?php  echo esc_attr(__('Investor\'s investment capital will be hold after completing the invest. Investors will be able to reinvest or withdraw the capital.', HYIPLAB_PLUGIN_NAME)); ?>"></i></label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="<?php echo esc_attr(__('Yes', HYIPLAB_PLUGIN_NAME)); ?>" data-off="<?php echo esc_attr(__('No', HYIPLAB_PLUGIN_NAME)); ?>" name="hold_capital">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for=""><?php echo esc_html__('Featured', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="<?php echo esc_attr(__('Yes', HYIPLAB_PLUGIN_NAME)); ?>" data-off="<?php echo esc_attr(__('No', HYIPLAB_PLUGIN_NAME)); ?>" name="featured">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e('Edit Plan', HYIPLAB_PLUGIN_NAME);?></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="" method="post">
                <?php hyiplab_nonce_field('admin.plan.update');?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Name', HYIPLAB_PLUGIN_NAME);?></label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Invest type', HYIPLAB_PLUGIN_NAME);?></label>
                                <select name="invest_type" class="form-control" required>
                                    <option value="1"><?php esc_html_e('Range', HYIPLAB_PLUGIN_NAME);?></option>
                                    <option value="2"><?php esc_html_e('Fixed', HYIPLAB_PLUGIN_NAME);?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row amount-fields"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Interest type', HYIPLAB_PLUGIN_NAME);?></label>
                                <select name="interest_type" class="form-control" required>
                                    <option value="1"><?php esc_html_e('Percent', HYIPLAB_PLUGIN_NAME);?></option>
                                    <option value="2"><?php esc_html_e('Fixed', HYIPLAB_PLUGIN_NAME);?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Interest', HYIPLAB_PLUGIN_NAME);?></label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="interest" required>
                                    <span class="input-group-text interest-type"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Time', HYIPLAB_PLUGIN_NAME);?></label>
                                <select name="time" class="form-control" required>
                                    <?php foreach($times as $time){ ?>
                                        <option value="<?php echo esc_attr($time->id);?>"><?php echo esc_html($time->name);?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Return type', HYIPLAB_PLUGIN_NAME);?></label>
                                <select name="return_type" class="form-control" required>
                                    <option value="1"><?php esc_html_e('Lifetime', HYIPLAB_PLUGIN_NAME);?></option>
                                    <option value="0"><?php esc_html_e('Repeat', HYIPLAB_PLUGIN_NAME);?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="repeat-time row"></div>
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for=""><?php esc_html_e('Compound Interest', HYIPLAB_PLUGIN_NAME); ?> <i class="las la-info-circle" title="<?php echo esc_attr('Provide investors with the choice to reinvest their earnings, allowing for compounding growth over time.', HYIPLAB_PLUGIN_NAME); ?>"></i></label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="<?php echo esc_attr(__('Yes', HYIPLAB_PLUGIN_NAME)); ?>" data-off="<?php echo esc_attr(__('No', HYIPLAB_PLUGIN_NAME))?>" name="compound_interest">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 holdCapitalGroup">
                            <div class="form-group">
                                <label for=""><?php esc_html_e('Hold Capital', HYIPLAB_PLUGIN_NAME); ?> <i class="las la-info-circle" title="<?php  echo esc_attr(__('Investor\'s investment capital will be hold after completing the invest. Investors will be able to reinvest or withdraw the capital.', HYIPLAB_PLUGIN_NAME)); ?>"></i></label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="<?php echo esc_attr(__('Yes', HYIPLAB_PLUGIN_NAME)); ?>" data-off="<?php echo esc_attr(__('No', HYIPLAB_PLUGIN_NAME)); ?>" name="hold_capital">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for=""><?php echo esc_html__('Featured', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="<?php echo esc_attr(__('Yes', HYIPLAB_PLUGIN_NAME)); ?>" data-off="<?php echo esc_attr(__('No', HYIPLAB_PLUGIN_NAME)); ?>" name="featured">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME);?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php hyiplab_include('partials/confirmation');?>

<?php
$html = '<button class="btn btn-outline--primary btn-sm modalShow" data-type="add" data-bs-toggle="modal" data-bs-target="#addModal"><i class="las la-plus"></i> ' . esc_html__('Add New', HYIPLAB_PLUGIN_NAME) . '</button>';
hyiplab_push_breadcrumb($html);
?>


<script>
    jQuery(document).ready(function($) {
        "use strict"
        $('.modalShow').on('click', function() {
            //get modal element
            if ($(this).data('type') == 'add') {
                var modal = $('#addModal');
            } else {
                var modal = $('#editModal');
            }
            var plan = new HyipPlan(modal, $(this));

            modal.find('[name=invest_type]').change(function() {
                plan.getInvestType($(this).val());
            }).change()


            modal.find('[name=interest_type]').change(function() {
                plan.getInterestType($(this).val());
            }).change()

            plan.setupEditModal();

            modal.find('[name=return_type]').change(function() {
                plan.getReturnType($(this).val());
            }).change()

            $(modal).on('change', '[name=capital_back]', function() {
                plan.holdCapitalView();
            }).change();

        });

        class HyipPlan {
            constructor(modal, btn) {
                this.modal = modal;
                this.btn = btn;
                this.resource = btn.data('resource');
                this.action = btn.data('action');
                this.fixedAmount = '';
                this.minimumAmount = '';
                this.maximumAmount = '';

                //this block for edit modal
                if (this.resource) {
                    //set amount
                    if (this.resource.fixed_amount <= 0) {
                        this.modal.find('[name=invest_type]').val(1);
                        this.minimumAmount = parseFloat(this.resource.minimum).toFixed(2);
                        this.maximumAmount = parseFloat(this.resource.maximum).toFixed(2);
                    } else {
                        this.modal.find('[name=invest_type]').val(2);
                        this.fixedAmount = parseFloat(this.resource.fixed_amount).toFixed(2);
                    }

                    //set interest type
                    if (this.resource.interest_type == 1) {
                        this.modal.find('[name=interest_type]').val(1);
                    } else {
                        this.modal.find('[name=interest_type]').val(2);
                    }

                    //set repeat type
                    if (this.resource.lifetime == 1) {
                        this.modal.find('[name=return_type]').val(1);
                    } else {
                        this.modal.find('[name=return_type]').val(2);
                    }
                    
                    if (this.resource.compound_interest == '1') {
                        this.modal.find('[name=compound_interest]').bootstrapToggle('on');
                    } else {
                        this.modal.find('[name=compound_interest]').bootstrapToggle('off');
                    }

                    if (this.resource.hold_capital == '1') {
                        this.modal.find('[name=hold_capital]').bootstrapToggle('on');
                    } else {
                        this.modal.find('[name=hold_capital]').bootstrapToggle('off');
                    }

                    if (this.resource.featured == '1') {
                        this.modal.find('[name=featured]').bootstrapToggle('on');
                    } else {
                        this.modal.find('[name=featured]').bootstrapToggle('off');
                    }
                }
            }

            getInvestType(type) {
                if (type == 1) {
                    var html = `
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required"><?php esc_html_e('Minimum Invest', HYIPLAB_PLUGIN_NAME); ?></label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="minimum" value="${this.minimumAmount}" required>
                                    <span class="input-group-text"><?php echo hyiplab_currency('text'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required"><?php esc_html_e('Maximum Invest', HYIPLAB_PLUGIN_NAME); ?></label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="maximum" value="${this.maximumAmount}" required>
                                    <span class="input-group-text"><?php echo hyiplab_currency('text'); ?></span>
                                </div>
                            </div>
                        </div>
                        `;
                } else {
                    var html = `
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="required"><?php esc_html_e('Amount', HYIPLAB_PLUGIN_NAME); ?></label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="amount" value="${this.fixedAmount}" required>
                                    <span class="input-group-text"><?php echo hyiplab_currency('text'); ?></span>
                                </div>
                            </div>
                        </div>
                        `;
                }

                this.modal.find('.amount-fields').html(html);
            }

            getInterestType(type) {
                if (type == 1) {
                    this.modal.find('.interest-type').text('%');
                } else {
                    this.modal.find('.interest-type').text('<?php echo hyiplab_currency('text'); ?>');
                }
            }

            getReturnType(type) {
                var html = ``;
                var resource = this.resource;
                if (type == 0) {
                    var html = `
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required"><?php esc_html_e('Repeat Times', HYIPLAB_PLUGIN_NAME); ?></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="repeat_time" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Capital back', HYIPLAB_PLUGIN_NAME); ?></label>
                                <select name="capital_back" class="form-control" required>
                                    <option value="1"><?php esc_html_e('Yes', HYIPLAB_PLUGIN_NAME); ?></option>
                                    <option value="0"><?php esc_html_e('No', HYIPLAB_PLUGIN_NAME); ?></option>
                                </select>
                            </div>
                        </div>
                    `;
                }
                this.modal.find('.repeat-time').html(html);
                if (resource) {
                    this.modal.find('[name=repeat_time]').val(resource.repeat_time);
                    this.modal.find('[name=capital_back]').val(resource.capital_back);
                }

                this.holdCapitalView();
            }

            setupEditModal() {
                var modal = this.modal;
                var resource = this.resource;
                if (resource) {
                    modal.find('[name=name]').val(resource.name);
                    modal.find('[name=minimum]').val(parseFloat(resource.minimum).toFixed(2));
                    modal.find('[name=maximum]').val(parseFloat(resource.maximum).toFixed(2));
                    modal.find('[name=amount]').val(parseFloat(resource.fixed_amount).toFixed(2));
                    modal.find('[name=interest]').val(parseFloat(resource.interest).toFixed(2));
                    modal.find('[name=time]').val(resource.time_setting_id);
                    modal.find('[name=repeat_time]').val(resource.repeat_time);
                    modal.find('[name=capital_back]').val(resource.capital_back);
                    modal.find('[name=return_type]').val(resource.lifetime);
                    modal.find('form').attr('action', this.btn.data('action'));
                }
            }

            holdCapitalView() {
                var modal = this.modal;
                var capitalBack = modal.find('[name=capital_back]').val();

                if (capitalBack == '1') {
                    modal.find('[name=compound_interest]').closest('.col-md-6').removeClass('col-lg-6').addClass('col-lg-4');
                    modal.find('[name=featured]').closest('.col-md-6').removeClass('col-lg-6').addClass('col-lg-4');
                    modal.find('.holdCapitalGroup').show();
                } else {
                    modal.find('[name=compound_interest]').closest('.col-md-6').removeClass('col-lg-4').addClass('col-lg-6');
                    modal.find('[name=featured]').closest('.col-md-6').removeClass('col-lg-4').addClass('col-lg-6');
                    modal.find('.holdCapitalGroup').hide();
                    modal.find('[name=hold_capital]').bootstrapToggle('off');
                }
            }

        }
    });
</script>
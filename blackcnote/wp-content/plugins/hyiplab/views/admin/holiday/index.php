<?php hyiplab_layout('admin/layouts/master'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th scope="col"><?php esc_html_e('S.N.', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Title', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Date', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Action', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($holidays->data as $key => $holiday) { ?>
                                <tr>
                                    <td><?php echo esc_html(++$key); ?></td>
                                    <td><?php echo esc_html($holiday->title); ?></td>
                                    <td><?php echo esc_html($holiday->date); ?></td>

                                    <td data-label="Action">
                                        <button type="button" data-question="Are you sure to delete this promotion?" data-action="<?php echo hyiplab_route_link('admin.holiday.delete') ?>&amp;id=<?php echo $holiday->id ?>" data-nonce="<?php echo hyiplab_nonce('admin.holiday.delete'); ?>" class="btn btn-sm btn-outline--danger confirmationBtn"> <i class="las la-trash"></i> <?php echo esc_html__('Delete', HYIPLAB_PLUGIN_NAME); ?></button>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (hyiplab_check_empty($holidays->data)) { ?>
                                <tr>
                                    <td class="text-muted text-center" colspan="100%"><?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
        </div>
    </div>
</div>


<?php $offDay = (object) $offDays; ?>
<div class="card mt-5">
    <div class="card-header"><b class="lead"><?php esc_html_e('Weekly Holidays', HYIPLAB_PLUGIN_NAME); ?></b></div>
    <form action="<?php echo hyiplab_route_link('admin.offday.setting'); ?>" method="post">
        <?php hyiplab_nonce_field('admin.offday.setting'); ?>
        <div class="card-body">
            <div class="row">
                <div class="form-group col-lg-3 col-sm-6 col-md-4">
                    <label class="form-control-label" for="off_day[sun]"><?php esc_html_e('Sunday', HYIPLAB_PLUGIN_NAME); ?></label>
                    <div class="form-group">
                        <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Holiday', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Payday', HYIPLAB_PLUGIN_NAME); ?>" name="off_day[sun]" id="off_day[sun]" <?php if (@$offDay->sun == 'on') echo 'checked'; ?>>
                    </div>
                </div>

                <div class="form-group col-lg-3 col-sm-6 col-md-4">
                    <label class="form-control-label" for="off_day[mon]"><?php esc_html_e('Monday', HYIPLAB_PLUGIN_NAME); ?></label>
                    <div class="form-group">
                        <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Holiday', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Payday', HYIPLAB_PLUGIN_NAME); ?>" name="off_day[mon]" id="off_day[mon]" <?php if (@$offDay->mon == 'on') echo 'checked'; ?>>
                    </div>
                </div>
                <div class="form-group col-lg-3 col-sm-6 col-md-4">
                    <label class="form-control-label" for="off_day[tue]"><?php esc_html_e('Tuesday', HYIPLAB_PLUGIN_NAME); ?></label>
                    <div class="form-group">
                        <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Holiday', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Payday', HYIPLAB_PLUGIN_NAME); ?>" name="off_day[tue]" id="off_day[tue]" <?php if (@$offDay->tue == 'on') echo 'checked'; ?>>
                    </div>
                </div>
                <div class="form-group col-lg-3 col-sm-6 col-md-4">
                    <label class="form-control-label" for="off_day[wed]"><?php esc_html_e('Wednesday', HYIPLAB_PLUGIN_NAME); ?></label>
                    <div class="form-group">
                        <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Holiday', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Payday', HYIPLAB_PLUGIN_NAME); ?>" name="off_day[wed]" id="off_day[wed]" <?php if (@$offDay->wed == 'on') echo 'checked'; ?>>
                    </div>
                </div>
                <div class="form-group col-lg-3 col-sm-6 col-md-4">
                    <label class="form-control-label" for="off_day[thu]"><?php esc_html_e('Thursday', HYIPLAB_PLUGIN_NAME); ?></label>
                    <div class="form-group">
                        <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Holiday', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Payday', HYIPLAB_PLUGIN_NAME); ?>" name="off_day[thu]" id="off_day[thu]" <?php if (@$offDay->thu == 'on') echo 'checked'; ?>>
                    </div>
                </div>
                <div class="form-group col-lg-3 col-sm-6 col-md-4">
                    <label class="form-control-label" for="off_day[fri]"><?php esc_html_e('Friday', HYIPLAB_PLUGIN_NAME); ?></label>
                    <div class="form-group">
                        <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Holiday', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Payday', HYIPLAB_PLUGIN_NAME); ?>" name="off_day[fri]" id="off_day[fri]" <?php if (@$offDay->fri == 'on') echo 'checked'; ?>>
                    </div>
                </div>

                <div class="form-group col-lg-3 col-sm-6 col-md-4">
                    <label class="form-control-label" for="off_day[sat]"><?php esc_html_e('Saturday', HYIPLAB_PLUGIN_NAME); ?></label>
                    <div class="form-group">
                        <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Holiday', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Payday', HYIPLAB_PLUGIN_NAME); ?>" name="off_day[sat]" id="off_day[sat]" <?php if (@$offDay->sat == 'on') echo 'checked'; ?>>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <button type="submit" class="btn btn--primary w-100 h-45"><?php echo esc_html__('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>


<div class="modal fade" id="addHoliday">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo esc_html__('Add Holiday', HYIPLAB_PLUGIN_NAME); ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="<?php echo hyiplab_route_link('admin.holiday.store'); ?>" method="post">
                <?php hyiplab_nonce_field('admin.holiday.store'); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title" class="required"><?php echo esc_html__('Title', HYIPLAB_PLUGIN_NAME); ?></label>
                        <input type="text" class="form-control" name="title" required id="title">
                    </div>
                    <div class="form-group">
                        <label for="date" class="required"><?php echo esc_html__('Enter Date', HYIPLAB_PLUGIN_NAME); ?></label>
                        <input type="date" class="form-control" name="date" required id="date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45"><?php echo esc_html__('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>




<?php hyiplab_include('partials/confirmation'); ?>

<?php
$html = '<button type="button" data-bs-target="#addHoliday" data-bs-toggle="modal" class="btn btn-sm addBtn btn-outline--primary"><i class="las la-plus"></i>' . esc_html__('Add New', HYIPLAB_PLUGIN_NAME) . '</button>';
hyiplab_push_breadcrumb($html);
?>
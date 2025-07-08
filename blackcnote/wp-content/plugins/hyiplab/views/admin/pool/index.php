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
                                <th scope="col"><?php esc_html_e('Name', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Amount', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Invested Amount', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('End Date', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Pool Status', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Share Interest', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Status', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Action', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pools->data as $pool) { ?>
                                <tr>
                                    <td><?php echo esc_html($pool->id); ?></td>
                                    <td><?php echo esc_html($pool->name); ?></td>
                                    <td><?php echo esc_html( hyiplab_currency('sym') . hyiplab_show_amount($pool->amount)); ?></td>
                                    <td><?php echo esc_html(hyiplab_currency('sym') . hyiplab_show_amount($pool->invested_amount)); ?></td>
                                    <td><?php echo esc_html(hyiplab_show_date_time($pool->end_date)); ?></td>
                                    <td>
                                        <?php if ($pool->end_date >= hyiplab_date()->now()) { ?>
                                            <span class="badge badge--success"><?php esc_html_e('Running', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } else { ?>
                                            <span class="badge badge--primary"><?php esc_html_e('Completed', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if($pool->share_interest){
                                            echo esc_html($pool->interest . "%");
                                        }else{
                                            echo esc_html("---");
                                        }
                                         ?>
                                    </td>
                                    <td>
                                        <?php if ($pool->status == 1) { ?>
                                            <span class="badge badge--success"><?php esc_html_e('Enabled', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } else { ?>
                                            <span class="badge badge--warning"><?php esc_html_e('Disabled', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <div class="button--group">
                                            <button data-id="7" data-pool='<?php echo json_encode($pool); ?>' data-action="<?php echo hyiplab_route_link('admin.pool.store')?>&amp;id=<?php echo intval($pool->id); ?>" class="btn btn-outline--primary editBtn btn-sm"><i class="las la-pen"></i><?php esc_html_e('Edit', HYIPLAB_PLUGIN_NAME); ?></button>

                                            <button type="button" class="btn btn-sm btn-outline--info" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="las la-ellipsis-v"></i><?php esc_html_e('More', HYIPLAB_PLUGIN_NAME); ?> </button>

                                            <div class="dropdown-menu more-dropdown">

                                                <button class="dropdown-item confirmationBtn" data-question="Are you sure to disable this pool?" data-action="<?php echo hyiplab_route_link('admin.pool.status');?>&amp;id=<?php echo intval($pool->id); ?>" data-nonce="<?php echo hyiplab_nonce('admin.pool.status'); ?>">
                                                    <?php if($pool->status == 1){ ?>
                                                        <i class="las la-eye-slash"></i> 
                                                        <?php esc_html_e('Disable', HYIPLAB_PLUGIN_NAME); ?>
                                                    <?php }else{ ?>
                                                        <i class="las la-eye"></i>
                                                        <?php esc_html_e('Enable', HYIPLAB_PLUGIN_NAME); ?>
                                                    <?php } ?>
                                                </button>
                                                <button class="dropdown-item dispatchBtn" data-pool_id="<?php echo esc_html($pool->id); ?>" data-interest_range="<?php echo esc_html($pool->interest_range); ?>">
                                                    <i class="las la-trophy"></i> <?php esc_html_e('Share Interest', HYIPLAB_PLUGIN_NAME); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (hyiplab_check_empty($pools->data)) { ?>
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

<div class="modal fade" id="poolModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-bs-dismiss="modal"><span><i class="las la-times"></i></span></button>
            </div>
            <form method="post" action="<?php echo hyiplab_route_link('admin.pool.store'); ?>">
                <?php hyiplab_nonce_field('admin.pool.store'); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name" class="required"><?php esc_html_e('Name', HYIPLAB_PLUGIN_NAME); ?> </label>
                        <input type="text" class="form-control" name="name" required id="name">
                    </div>
                    <div class="form-group">
                        <label for="amount" class="required"><?php esc_html_e('Amount', HYIPLAB_PLUGIN_NAME); ?></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="amount" step="any" min="0" required id="amount">
                            <span class="input-group-text"><?php esc_html_e('USD', HYIPLAB_PLUGIN_NAME); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="interest_range" class="required"><?php esc_html_e('Interest Range', HYIPLAB_PLUGIN_NAME); ?></label>
                        <input type="text" class="form-control" name="interest_range" required id="interest_range">
                    </div>
                    <div class="form-group">
                        <label for="start_date" class="required"><?php esc_html_e('Invest Till', HYIPLAB_PLUGIN_NAME); ?></label>
                        <input type="datetime-local" class="form-control" name="start_date" required id="start_date" min >
                    </div>
                    <div class="form-group">
                        <label for="end_date" class="required"><?php esc_html_e('End Date', HYIPLAB_PLUGIN_NAME); ?></label>
                        <input type="datetime-local" class="form-control" name="end_date" required id="end_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45"><i class="fa fa-send"></i><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="dispatchModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php esc_html_e('Dispatch Pool', HYIPLAB_PLUGIN_NAME); ?></h4>
                <button type="button" class="close" data-bs-dismiss="modal"><span><i class="las la-times"></i></span></button>
            </div>
            <form action="<?php echo hyiplab_route_link('admin.pool.dispatch');?>" method="post">
                <?php hyiplab_nonce_field('admin.pool.dispatch'); ?>
                <input type="hidden" name="pool_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="amount" class="required"><?php esc_html_e('Interest Rate', HYIPLAB_PLUGIN_NAME); ?>
                         (<small><?php esc_html_e('Interest Range:', HYIPLAB_PLUGIN_NAME); ?> <span class="interestRange"></span></small>)</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="amount" step="any" min="0" required="" id="amount">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45"><i class="fa fa-send"></i> <?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php hyiplab_include('partials/confirmation'); ?>

<?php
$html = '<button type="button" data-bs-target="#poolModal" data-bs-toggle="modal" class="btn btn-sm addBtn btn-outline--primary"><i class="las la-plus"></i>' . esc_html__('Add New', HYIPLAB_PLUGIN_NAME) . '</button>';
hyiplab_push_breadcrumb($html);

?>


<script>
    jQuery(document).ready(function($) {

        "use strict";

        let modal = $('#poolModal');
        var now = new Date();
        var formattedNow = now.toISOString().slice(0, 16);

        $('.addBtn').on('click', function() {
            modal.find('form').trigger('reset');
            modal.find('.modal-title').text(`Add New Pool`);
            modal.find('[name=start_date]').attr("min", formattedNow);
            modal.find('[name=end_date]').attr("min", formattedNow);
            modal.modal('show');
        });

        $('.editBtn').on('click', function() {
            let data = $(this).data();
            let pool = data.pool;
            modal.find('.modal-title').text(`Update Update`);
            modal.find('[name=name]').val(pool.name);
            modal.find('[name=amount]').val(Number(pool.amount));
            modal.find('[name=interest_range]').val(pool.interest_range);
            modal.find('[name=start_date]').val(pool.start_date);
            modal.find('[name=end_date]').val(pool.end_date);
            modal.find('form').attr('action', `${data.action}`);
            modal.modal('show');
        });

        $('.dispatchBtn').on('click', function() {
            let modal = $('#dispatchModal');
            $('.interestRange').text($(this).data('interest_range'));
            modal.find('[name=pool_id]').val($(this).data('pool_id'));
            modal.find('[name=amount]').val('');
            modal.modal('show');
        });


        $('.table-responsive').on('click', 'button[data-bs-toggle="dropdown"]', function (e) {
            const { top, left } = $(this).next(".dropdown-menu")[0].getBoundingClientRect();
            $(this).next(".dropdown-menu").css({
                position: "fixed",
                inset: "unset",
                transform: "unset",
                top: top + "px",
                left: left + "px",
            });
            });

            if ($('.table-responsive').length) {
            $(window).on('scroll', function (e) {
                $('.table-responsive .dropdown-menu').removeClass('show');
                $('.table-responsive button[data-bs-toggle="dropdown"]').removeClass('show');
            });
        }

    });
</script>
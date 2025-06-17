<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <?php foreach ($commissionTypes as $key => $type) { ?>
        <div class="col-lg-4 mb-4">
            <div class="card border--primary parent">
                <div class="card-header bg--primary">
                    <h5 class="text-white float-start"><?php esc_html_e($type, HYIPLAB_PLUGIN_NAME); ?></h5>
                    <?php if (!get_option('hyiplab_' . sanitize_key($key))) { ?>
                        <a href="<?php echo hyiplab_route_link('admin.referrals.status'); ?>&amp;type=<?php echo sanitize_key($key); ?>" class="btn btn--success btn-sm float-end"><i class="las la-toggle-on"></i> <?php esc_html_e('Enable Now', HYIPLAB_PLUGIN_NAME); ?></a>
                    <?php } else { ?>
                        <a href="<?php echo hyiplab_route_link('admin.referrals.status'); ?>&amp;type=<?php echo sanitize_key($key); ?>" class="btn btn--danger btn-sm float-end"><i class="las la-toggle-off"></i> <?php esc_html_e('Disable Now', HYIPLAB_PLUGIN_NAME); ?></a>
                    <?php } ?>
                </div>

                <div class="card-body">

                    <ul class="list-group list-group-flush">
                        <?php foreach ($referrals as $referral) {
                            if ($referral->commission_type != $key) {
                                continue;
                            }
                        ?>
                            <li class="list-group-item d-flex flex-wrap justify-content-between">
                                <span class="fw-bold"><?php esc_html_e('Level', HYIPLAB_PLUGIN_NAME) ?> <?php echo esc_html($referral->level); ?></span>
                                <span class="fw-bold"><?php echo esc_html($referral->percent); ?>%</span>
                            </li>
                        <?php } ?>
                    </ul>

                    <div class="border-line-area mt-3">
                        <h6 class="border-line-title"><?php esc_html_e('Update Setting', HYIPLAB_PLUGIN_NAME); ?></h6>
                    </div>

                    <div class="form-group">
                        <label><?php esc_html_e('Number of Level', HYIPLAB_PLUGIN_NAME); ?></label>
                        <div class="input-group">
                            <input type="number" name="level" min="1" placeholder="Type a number & hit ENTER ↵" class="form-control">
                            <button type="button" class="btn btn--primary generate"><?php esc_html_e('Generate', HYIPLAB_PLUGIN_NAME); ?></button>
                        </div>
                        <span class="text--danger required-message d-none"><?php esc_html_e('Please enter a number', HYIPLAB_PLUGIN_NAME); ?></span>
                    </div>

                    <form action="<?php echo hyiplab_route_link('admin.referrals.update'); ?>" method="post" class="d-none levelForm">
                        <?php hyiplab_nonce_field('admin.referrals.update');?>
                        <input type="hidden" name="commission_type" value="<?php echo esc_attr($key); ?>">
                        <h6 class="text--danger mb-3"><?php esc_html_e('The Old setting will be removed after generating new', HYIPLAB_PLUGIN_NAME); ?></h6>
                        <div class="form-group">
                            <div class="referralLevels"></div>
                        </div>
                        <button type="submit" class="btn btn--primary h-45 w-100"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                    </form>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<style>
    .border-line-area {
        position: relative;
        text-align: center;
        z-index: 1;
    }

    .border-line-area::before {
        position: absolute;
        content: '';
        top: 50%;
        left: 0;
        width: 100%;
        height: 1px;
        background-color: #e5e5e5;
        z-index: -1;
    }

    .border-line-title {
        display: inline-block;
        padding: 3px 10px;
        background-color: #fff;
    }
</style>

<script>
    jQuery(document).ready(function($) {
        "use strict"

        $('[name="level"]').on('focus', function() {
            $(this).on('keyup', function(e) {
                if (e.which == 13) {
                    generateLevels($(this));
                }
            });
        });

        $(".generate").on('click', function() {
            let $this = $(this).parents('.card-body').find('[name="level"]');
            generateLevels($this);
        });

        $(document).on('click', '.deleteBtn', function() {
            $(this).closest('.input-group').remove();
        });

        function generateLevels($this) {
            let numberOfLevel = $this.val();
            let parent = $this.parents('.card-body');
            let html = '';
            if (numberOfLevel && numberOfLevel > 0) {
                parent.find('.levelForm').removeClass('d-none');
                parent.find('.required-message').addClass('d-none');
                for (i = 1; i <= numberOfLevel; i++) {
                    html += `
                    <div class="input-group mb-3">
                        <span class="input-group-text justify-content-center"><?php esc_html_e('Level', HYIPLAB_PLUGIN_NAME); ?> ${i}</span>
                        <input type="hidden" name="level[]" value="${i}" required>
                        <input name="percent[]" class="form-control col-10" type="text" required placeholder="Commission Percentage">
                        <button class="btn btn--danger input-group-text deleteBtn" type="button"><i class=\'la la-times\'></i></button>
                    </div>`
                }
                parent.find('.referralLevels').html(html);
            } else {
                parent.find('.levelForm').addClass('d-none');
                parent.find('.required-message').removeClass('d-none');
            }
        }
    });
</script>
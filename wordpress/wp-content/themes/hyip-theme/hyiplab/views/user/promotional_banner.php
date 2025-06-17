<?php hyiplab_layout('user/layouts/master'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="text-end mb-3 d-flex flex-wrap justify-content-between gap-1">
            <h3><?php esc_html_e('Promotional Banners', HYIPLAB_PLUGIN_NAME); ?></h3>
        </div>
        <div class="card">
            <div class="card-body">

                <div class="row">
                   <?php foreach($banners as $banner){ ?>
                        <div class="col-md-4 mb-3">
                            <div class="thumb__350px">
                                <img src="<?php echo hyiplab_file_path('promotional') .  '/'. @$banner->banner ?>" class="w-100">
                            </div>
                            <div class="referral-form mt-20 ">

                                <?php
                                    $string = '<a href="'. site_url() .'/register?reference='. wp_get_current_user()->user_login .'" target="_blank"> <img src="'. hyiplab_file_path('promotional') .  '/'. @$banner->banner .'" alt="image"/></a>';
                                ?>

                                <textarea type="url" id="reflink<?php echo $banner->id ?>" class="form--control form-control from-control-lg refCode fs--14px mt-3" rows="5" readonly><?php echo $string ?></textarea>
                                <button type="button" data-copytarget="#reflink3" class="btn--base justify-content-center w-100 mt-3 copybtn btn-block"><i class="fa fa-copy"></i> &nbsp; Copy</button>
                            </div>
                        </div>
                    <?php } ?>
                </div>

            </div>
            <?php if (hyiplab_check_empty($banners)) { ?>
                <div class="card-body text-center">
                    <h4 class="text--muted"><i class="far fa-frown"></i> <?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></h4>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";

        $('.referral-form').on('click', function (e) {
            var text = $(this).find('.refCode').val();
            var vInput = document.createElement("input");
            vInput.value = text;
            document.body.appendChild(vInput);
            vInput.select();
            document.execCommand("Copy");
            document.body.removeChild(vInput);
            $(this).find('.refCode').addClass('copied-referral');
            $(this).find('.copybtn').html('<i class="fa fa-copy"></i> Copied');
            setTimeout(() => {
                $(this).find('.refCode').removeClass('copied-referral');
                $(this).find('.copybtn').html('<i class="fa fa-copy me-1"></i> Copy');
            }, 1000);
        });

    })(jQuery);
</script>
<?php hyiplab_layout('user/layouts/master'); ?>
<div class="mb-4">
    <h3><?php esc_html_e('My Referrals', HYIPLAB_PLUGIN_NAME); ?></h3>
</div>
<div class="row gy-4">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-body">
                <h4 class="mb-1"><?php esc_html_e('Refer & Enjoy the Bonus', HYIPLAB_PLUGIN_NAME); ?></h4>
                <p class="mb-3"><?php esc_html_e('You\'ll get commission against your referral\'s activities. Level has been decided by the', HYIPLAB_PLUGIN_NAME); ?> <strong><i><?php echo get_bloginfo('name'); ?></i></strong> <?php esc_html_e('authority. If you reach the level, you\'ll get commission.', HYIPLAB_PLUGIN_NAME); ?></p>
                <div class="copy-link">
                    <input type="text" class="copyURL" value="<?php echo esc_url( hyiplab_route_link('user.register')); ?>?reference=<?php echo hyiplab_auth()->user->user_login; ?>" readonly>
                    <span class="copyBoard" id="copyBoard"><i class="las la-copy"></i> <strong class="copyText"><?php esc_html_e('Copy', HYIPLAB_PLUGIN_NAME); ?></strong></span>
                </div>
            </div>
        </div>
        <?php if (count(getViserAllReferrer($user->ID)) > 0 && $maxLevel > 0) { ?>
            <div class="card">
                <div class="card-body">
                    <div class="treeview-container">
                        <ul class="treeview">
                            <li class="items-expanded"><?php echo esc_html($user->display_name); ?> ( <?php echo esc_html($user->user_login); ?> )
                                <?php hyiplab_include('user/partials/under_tree', ['user' => $user, 'layer' => 0, 'isFirst' => true, 'maxLevel' => $maxLevel]); ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>


<script>
    jQuery(document).ready(function($) {
        "use strict"
        $('.treeview').treeView();
        $('.copyBoard').on('click', function() {
            var copyText = document.getElementsByClassName("copyURL");
            copyText = copyText[0];
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            /*For mobile devices*/
            document.execCommand("copy");
            $('.copyText').text('<?php esc_html_e('Copied', HYIPLAB_PLUGIN_NAME); ?>');
            setTimeout(() => {
                $('.copyText').text('<?php esc_html_e('Copy', HYIPLAB_PLUGIN_NAME); ?>');
            }, 2000);
        });
    });
</script>

<?php
wp_enqueue_style('tree-view', hyiplab_asset('public/css/jquery.treeView.css'));
wp_enqueue_script('tree-view', hyiplab_asset('public/js/jquery.treeView.js'), array('jquery'), null, true);
?>
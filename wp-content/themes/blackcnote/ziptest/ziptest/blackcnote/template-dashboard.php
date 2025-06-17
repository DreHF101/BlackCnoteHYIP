<?php
/**
 * Template Name: Dashboard
 * Template Post Type: page
 *
 * @package BlackCnote
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

// Check if BlackCnote plugin is active
if (!function_exists('blackcnote_system_instance')) {
    wp_die(esc_html__('BlackCnote plugin is required for this page.', 'blackcnote'));
}
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="blackcnote-dashboard">
            <div class="row">
                <!-- User Overview -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="card-title"><?php esc_html_e('Account Overview', 'blackcnote'); ?></h3>
                            <?php
                            $user = wp_get_current_user();
                            $user_meta = get_user_meta($user->ID);
                            ?>
                            <div class="user-info">
                                <p><strong><?php esc_html_e('Username:', 'blackcnote'); ?></strong> <?php echo esc_html($user->user_login); ?></p>
                                <p><strong><?php esc_html_e('Email:', 'blackcnote'); ?></strong> <?php echo esc_html($user->user_email); ?></p>
                                <p><strong><?php esc_html_e('Member Since:', 'blackcnote'); ?></strong> <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($user->user_registered))); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Investment Summary -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="card-title"><?php esc_html_e('Investment Summary', 'blackcnote'); ?></h3>
                            <?php
                            global $wpdb;
                            try {
                                $user_id = get_current_user_id();
                                $investments = $wpdb->get_results($wpdb->prepare(
                                    "SELECT * FROM {$wpdb->prefix}blackcnote_investments 
                                    WHERE user_id = %d 
                                    ORDER BY created_at DESC 
                                    LIMIT 5",
                                    $user_id
                                ));

                                if ($investments) :
                                    ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th><?php esc_html_e('Plan', 'blackcnote'); ?></th>
                                                    <th><?php esc_html_e('Amount', 'blackcnote'); ?></th>
                                                    <th><?php esc_html_e('Return', 'blackcnote'); ?></th>
                                                    <th><?php esc_html_e('Status', 'blackcnote'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($investments as $investment) : ?>
                                                    <tr>
                                                        <td><?php echo esc_html($investment->plan_name); ?></td>
                                                        <td><?php echo esc_html(number_format($investment->amount, 2)); ?></td>
                                                        <td><?php echo esc_html(number_format($investment->return_amount, 2)); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo esc_attr($investment->status === 'active' ? 'success' : 'secondary'); ?>">
                                                                <?php echo esc_html(ucfirst($investment->status)); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else : ?>
                                    <p class="text-muted"><?php esc_html_e('No investments found.', 'blackcnote'); ?></p>
                                <?php endif;
                            } catch (Exception $e) {
                                error_log('BlackCnote Theme Error: ' . $e->getMessage());
                                echo '<p class="error">' . esc_html__('Error loading investment data.', 'blackcnote') . '</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title"><?php esc_html_e('Quick Actions', 'blackcnote'); ?></h3>
                            <div class="row">
                                <div class="col-md-4">
                                    <a href="<?php echo esc_url(home_url('/invest')); ?>" class="btn btn-primary btn-lg w-100 mb-3">
                                        <?php esc_html_e('New Investment', 'blackcnote'); ?>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="<?php echo esc_url(home_url('/withdraw')); ?>" class="btn btn-success btn-lg w-100 mb-3">
                                        <?php esc_html_e('Withdraw Funds', 'blackcnote'); ?>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="<?php echo esc_url(home_url('/profile')); ?>" class="btn btn-info btn-lg w-100 mb-3">
                                        <?php esc_html_e('Update Profile', 'blackcnote'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BlackCnote Dashboard Shortcode -->
            <div class="row mt-4">
                <div class="col-12">
                    <?php echo do_shortcode('[blackcnote_dashboard]'); ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
get_footer(); 
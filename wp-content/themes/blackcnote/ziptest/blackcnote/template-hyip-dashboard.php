<?php
/**
 * Template Name: BlackCnote Dashboard
 * Template Post Type: page
 *
 * @package BlackCnote_Theme
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

// Check if BlackCnoteLab plugin is active
if (!function_exists('blackcnotelab_system_instance')) {
    wp_die(esc_html__('BlackCnoteLab plugin is required for this page.', 'blackcnote-theme'));
}

// Get current user
$user = wp_get_current_user();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="blackcnote-dashboard">
            <div class="row">
                <!-- Account Overview -->
                <div class="col-md-4">
                    <div class="card">
                        <h3 class="card-title"><?php esc_html_e('Account Overview', 'blackcnote-theme'); ?></h3>
                        <div class="card-body">
                            <p><strong><?php esc_html_e('Username:', 'blackcnote-theme'); ?></strong> <?php echo esc_html($user->user_login); ?></p>
                            <p><strong><?php esc_html_e('Email:', 'blackcnote-theme'); ?></strong> <?php echo esc_html($user->user_email); ?></p>
                            <p><strong><?php esc_html_e('Member Since:', 'blackcnote-theme'); ?></strong> <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($user->user_registered))); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Investment Summary -->
                <div class="col-md-8">
                    <div class="card">
                        <h3 class="card-title"><?php esc_html_e('Investment Summary', 'blackcnote-theme'); ?></h3>
                        <div class="card-body">
                            <?php
                            global $wpdb;
                            try {
                                $investments = $wpdb->get_results(
                                    "SELECT * FROM {$wpdb->prefix}blackcnotelab_investments
                                    WHERE user_id = {$user->ID}
                                    ORDER BY created_at DESC
                                    LIMIT 5"
                                );
                                if ($investments) {
                                    ?>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th><?php esc_html_e('Plan', 'blackcnote-theme'); ?></th>
                                                <th><?php esc_html_e('Amount', 'blackcnote-theme'); ?></th>
                                                <th><?php esc_html_e('Return', 'blackcnote-theme'); ?></th>
                                                <th><?php esc_html_e('Status', 'blackcnote-theme'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($investments as $investment) : ?>
                                                <tr>
                                                    <td><?php echo esc_html($investment->plan_name); ?></td>
                                                    <td><?php echo esc_html(number_format($investment->amount, 2)); ?></td>
                                                    <td><?php echo esc_html(number_format($investment->return_amount, 2)); ?></td>
                                                    <td><?php echo esc_html($investment->status); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <?php
                                } else {
                                    ?>
                                    <p class="text-muted"><?php esc_html_e('No investments found.', 'blackcnote-theme'); ?></p>
                                    <?php
                                }
                            } catch (Exception $e) {
                                error_log('BlackCnote Theme Error: ' . $e->getMessage());
                                echo '<p class="error">' . esc_html__('Error loading investment data.', 'blackcnote-theme') . '</p>';
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
                        <h3 class="card-title"><?php esc_html_e('Quick Actions', 'blackcnote-theme'); ?></h3>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <a href="<?php echo esc_url(home_url('/investment-plans')); ?>" class="btn btn-primary">
                                        <?php esc_html_e('New Investment', 'blackcnote-theme'); ?>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="<?php echo esc_url(home_url('/withdraw')); ?>" class="btn btn-secondary">
                                        <?php esc_html_e('Withdraw Funds', 'blackcnote-theme'); ?>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="<?php echo esc_url(home_url('/profile')); ?>" class="btn btn-info">
                                        <?php esc_html_e('Update Profile', 'blackcnote-theme'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BlackCnoteLab Dashboard Shortcode -->
            <div class="row mt-4">
                <div class="col-12">
                    <?php echo do_shortcode('[blackcnotelab_dashboard]'); ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
get_footer(); 
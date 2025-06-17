<?php
/**
 * Template Name: BlackCnote Dashboard
 *
 * @package BlackCnote
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check if BlackCnote plugin is active
if (!function_exists('blackcnote_system_instance')) {
    wp_die(esc_html__('BlackCnote plugin is required for this page.', 'blackcnote'));
}

get_header();

$user = wp_get_current_user();
?>

<div class="blackcnote-dashboard">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title"><?php esc_html_e('Account Overview', 'blackcnote'); ?></h3>
                        <div class="account-info">
                            <p><strong><?php esc_html_e('Username:', 'blackcnote'); ?></strong> <?php echo esc_html($user->user_login); ?></p>
                            <p><strong><?php esc_html_e('Email:', 'blackcnote'); ?></strong> <?php echo esc_html($user->user_email); ?></p>
                            <p><strong><?php esc_html_e('Member Since:', 'blackcnote'); ?></strong> <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($user->user_registered))); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title"><?php esc_html_e('Investment Summary', 'blackcnote'); ?></h3>
                        <?php
                        global $wpdb;
                        try {
                            $investments = $wpdb->get_results(
                                $wpdb->prepare(
                                    "SELECT * FROM {$wpdb->prefix}blackcnote_investments
                                    WHERE user_id = %d
                                    ORDER BY date DESC",
                                    get_current_user_id()
                                )
                            );
                            ?>
                            <div class="table-responsive">
                                <table class="table blackcnote-table">
                                    <thead>
                                        <tr>
                                            <th><?php esc_html_e('Plan', 'blackcnote'); ?></th>
                                            <th><?php esc_html_e('Amount', 'blackcnote'); ?></th>
                                            <th><?php esc_html_e('Return', 'blackcnote'); ?></th>
                                            <th><?php esc_html_e('Status', 'blackcnote'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($investments): ?>
                                            <?php foreach ($investments as $investment): ?>
                                                <tr>
                                                    <td><?php echo esc_html($investment->plan_name); ?></td>
                                                    <td>$<?php echo number_format($investment->amount, 2); ?></td>
                                                    <td>$<?php echo number_format($investment->expected_return, 2); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo esc_attr($investment->status === 'active' ? 'success' : 'warning'); ?>">
                                                            <?php echo esc_html($investment->status); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4">
                                                    <p class="text-muted"><?php esc_html_e('No investments found.', 'blackcnote'); ?></p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                        } catch (Exception $e) {
                            error_log('BlackCnote Theme Error: ' . $e->getMessage());
                            echo '<p class="error">' . esc_html__('Error loading investment data.', 'blackcnote') . '</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title"><?php esc_html_e('Quick Actions', 'blackcnote'); ?></h3>
                        <div class="quick-actions">
                            <a href="<?php echo esc_url(get_permalink(get_page_by_path('plans'))); ?>" class="btn btn-primary">
                                <?php esc_html_e('New Investment', 'blackcnote'); ?>
                            </a>
                            <a href="<?php echo esc_url(get_permalink(get_page_by_path('withdraw'))); ?>" class="btn btn-secondary">
                                <?php esc_html_e('Withdraw Funds', 'blackcnote'); ?>
                            </a>
                            <a href="<?php echo esc_url(get_permalink(get_page_by_path('profile'))); ?>" class="btn btn-outline-primary">
                                <?php esc_html_e('Update Profile', 'blackcnote'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BlackCnote Dashboard Shortcode -->
        <div class="row mt-4">
            <div class="col-md-12">
                <?php echo do_shortcode('[blackcnote_dashboard]'); ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?> 
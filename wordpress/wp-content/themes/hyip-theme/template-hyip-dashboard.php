<?php
/**
 * Template Name: HYIP Dashboard
 * Template Post Type: page
 *
 * @package HYIP_Theme
 */

if (!hyip_is_hyiplab_active()) {
    wp_die(esc_html__('HYIPLab plugin is required for this template.', 'hyip-theme'));
}

get_header();
?>

<main id="primary" class="site-main">
    <div class="hyip-dashboard">
        <div class="dashboard-header">
            <h1><?php esc_html_e('Investment Dashboard', 'hyip-theme'); ?></h1>
            <div class="user-info">
                <?php if (is_user_logged_in()) : ?>
                    <p><?php esc_html_e('Welcome,', 'hyip-theme'); ?> <?php echo esc_html(wp_get_current_user()->display_name); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="dashboard-stats">
                <div class="stat-box">
                    <h3><?php esc_html_e('Total Balance', 'hyip-theme'); ?></h3>
                    <p class="amount">$0.00</p>
                </div>
                <div class="stat-box">
                    <h3><?php esc_html_e('Active Investments', 'hyip-theme'); ?></h3>
                    <p class="amount">0</p>
                </div>
                <div class="stat-box">
                    <h3><?php esc_html_e('Total Earnings', 'hyip-theme'); ?></h3>
                    <p class="amount">$0.00</p>
                </div>
            </div>

            <div class="dashboard-actions">
                <a href="<?php echo esc_url(home_url('/hyip-plans')); ?>" class="button"><?php esc_html_e('View Investment Plans', 'hyip-theme'); ?></a>
                <a href="<?php echo esc_url(home_url('/hyip-transactions')); ?>" class="button"><?php esc_html_e('View Transactions', 'hyip-theme'); ?></a>
            </div>

            <div class="recent-activity">
                <h2><?php esc_html_e('Recent Activity', 'hyip-theme'); ?></h2>
                <div class="activity-list">
                    <p class="no-activity"><?php esc_html_e('No recent activity to display.', 'hyip-theme'); ?></p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
get_footer(); 
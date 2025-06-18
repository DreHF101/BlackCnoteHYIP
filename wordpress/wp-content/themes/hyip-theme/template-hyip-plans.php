<?php
/**
 * Template Name: HYIP Plans
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
    <div class="hyip-plans">
        <h1><?php esc_html_e('Investment Plans', 'hyip-theme'); ?></h1>

        <div class="plans-grid">
            <div class="plan-card">
                <div class="plan-header">
                    <h2><?php esc_html_e('Basic Plan', 'hyip-theme'); ?></h2>
                    <p class="plan-duration"><?php esc_html_e('30 Days', 'hyip-theme'); ?></p>
                </div>
                <div class="plan-details">
                    <p class="plan-return"><?php esc_html_e('Return: 120%', 'hyip-theme'); ?></p>
                    <p class="plan-min"><?php esc_html_e('Min: $100', 'hyip-theme'); ?></p>
                    <p class="plan-max"><?php esc_html_e('Max: $1,000', 'hyip-theme'); ?></p>
                </div>
                <div class="plan-actions">
                    <a href="#" class="button"><?php esc_html_e('Select Plan', 'hyip-theme'); ?></a>
                </div>
            </div>

            <div class="plan-card featured">
                <div class="plan-header">
                    <h2><?php esc_html_e('Premium Plan', 'hyip-theme'); ?></h2>
                    <p class="plan-duration"><?php esc_html_e('60 Days', 'hyip-theme'); ?></p>
                </div>
                <div class="plan-details">
                    <p class="plan-return"><?php esc_html_e('Return: 150%', 'hyip-theme'); ?></p>
                    <p class="plan-min"><?php esc_html_e('Min: $1,000', 'hyip-theme'); ?></p>
                    <p class="plan-max"><?php esc_html_e('Max: $10,000', 'hyip-theme'); ?></p>
                </div>
                <div class="plan-actions">
                    <a href="#" class="button"><?php esc_html_e('Select Plan', 'hyip-theme'); ?></a>
                </div>
            </div>

            <div class="plan-card">
                <div class="plan-header">
                    <h2><?php esc_html_e('VIP Plan', 'hyip-theme'); ?></h2>
                    <p class="plan-duration"><?php esc_html_e('90 Days', 'hyip-theme'); ?></p>
                </div>
                <div class="plan-details">
                    <p class="plan-return"><?php esc_html_e('Return: 200%', 'hyip-theme'); ?></p>
                    <p class="plan-min"><?php esc_html_e('Min: $10,000', 'hyip-theme'); ?></p>
                    <p class="plan-max"><?php esc_html_e('Max: $100,000', 'hyip-theme'); ?></p>
                </div>
                <div class="plan-actions">
                    <a href="#" class="button"><?php esc_html_e('Select Plan', 'hyip-theme'); ?></a>
                </div>
            </div>
        </div>

        <div class="investment-terms">
            <h2><?php esc_html_e('Investment Terms', 'hyip-theme'); ?></h2>
            <ul>
                <li><?php esc_html_e('All investments are locked for the duration of the plan.', 'hyip-theme'); ?></li>
                <li><?php esc_html_e('Returns are paid at the end of the investment period.', 'hyip-theme'); ?></li>
                <li><?php esc_html_e('Minimum investment amount varies by plan.', 'hyip-theme'); ?></li>
                <li><?php esc_html_e('Maximum investment amount varies by plan.', 'hyip-theme'); ?></li>
            </ul>
        </div>
    </div>
</main>

<?php
get_footer(); 
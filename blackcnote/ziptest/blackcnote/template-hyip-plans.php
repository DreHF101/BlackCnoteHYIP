<?php
/**
 * Template Name: HYIP Investment Plans
 * Template Post Type: page
 *
 * @package HYIP_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Check if HYIPLab plugin is active
if (!function_exists('hyiplab_system_instance')) {
    wp_die(esc_html__('HYIPLab plugin is required for this page.', 'hyip-theme'));
}
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="hyip-plans-page">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('Investment Plans', 'hyip-theme'); ?></h1>
                <p class="page-description">
                    <?php esc_html_e('Choose from our selection of investment plans to start earning returns.', 'hyip-theme'); ?>
                </p>
            </header>

            <!-- Investment Plans Grid -->
            <div class="hyip-plans">
                <?php
                global $wpdb;
                try {
                    $plans = $wpdb->get_results(
                        "SELECT * FROM {$wpdb->prefix}hyiplab_plans 
                        WHERE status = 'active' 
                        ORDER BY min_investment ASC"
                    );

                    if ($plans) :
                        foreach ($plans as $plan) :
                            ?>
                            <div class="plan-card">
                                <div class="plan-header">
                                    <h3 class="plan-name"><?php echo esc_html($plan->name); ?></h3>
                                    <div class="plan-return">
                                        <span class="return-rate"><?php echo esc_html($plan->return_rate); ?>%</span>
                                        <span class="return-period"><?php echo esc_html($plan->return_period); ?></span>
                                    </div>
                                </div>

                                <div class="plan-details">
                                    <div class="detail-item">
                                        <span class="label"><?php esc_html_e('Min Investment:', 'hyip-theme'); ?></span>
                                        <span class="value"><?php echo esc_html(number_format($plan->min_investment, 2)); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label"><?php esc_html_e('Max Investment:', 'hyip-theme'); ?></span>
                                        <span class="value"><?php echo esc_html(number_format($plan->max_investment, 2)); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label"><?php esc_html_e('Return Period:', 'hyip-theme'); ?></span>
                                        <span class="value"><?php echo esc_html($plan->return_period); ?></span>
                                    </div>
                                </div>

                                <?php if (is_user_logged_in()) : ?>
                                    <button class="btn btn-primary invest-btn w-100" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#investModal"
                                            data-plan-id="<?php echo esc_attr($plan->id); ?>"
                                            data-plan-name="<?php echo esc_attr($plan->name); ?>"
                                            data-min-amount="<?php echo esc_attr($plan->min_investment); ?>"
                                            data-max-amount="<?php echo esc_attr($plan->max_investment); ?>"
                                            data-return-rate="<?php echo esc_attr($plan->return_rate); ?>">
                                        <?php esc_html_e('Invest Now', 'hyip-theme'); ?>
                                    </button>
                                <?php else : ?>
                                    <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" class="btn btn-secondary w-100">
                                        <?php esc_html_e('Login to Invest', 'hyip-theme'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <?php
                        endforeach;
                    else :
                        ?>
                        <div class="no-plans">
                            <p><?php esc_html_e('No investment plans available at the moment.', 'hyip-theme'); ?></p>
                        </div>
                        <?php
                    endif;
                } catch (Exception $e) {
                    error_log('HYIP Theme Error: ' . $e->getMessage());
                    echo '<p class="error">' . esc_html__('Error loading investment plans.', 'hyip-theme') . '</p>';
                }
                ?>
            </div>

            <!-- Investment Modal -->
            <div class="modal fade" id="investModal" tabindex="-1" aria-labelledby="investModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="investModalLabel"><?php esc_html_e('Make Investment', 'hyip-theme'); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="investForm" class="hyip-form">
                                <?php wp_nonce_field('hyip_invest_nonce', 'hyip_invest_nonce'); ?>
                                <input type="hidden" name="plan_id" id="plan_id">
                                
                                <div class="mb-3">
                                    <label for="plan_name" class="form-label"><?php esc_html_e('Plan', 'hyip-theme'); ?></label>
                                    <input type="text" class="form-control" id="plan_name" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="amount" class="form-label"><?php esc_html_e('Investment Amount', 'hyip-theme'); ?></label>
                                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                                    <div class="form-text">
                                        <span id="min_amount"></span> - <span id="max_amount"></span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><?php esc_html_e('Expected Return', 'hyip-theme'); ?></label>
                                    <div id="return_calculation" class="alert alert-info">
                                        <?php esc_html_e('Enter amount to calculate return', 'hyip-theme'); ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="payment_method" class="form-label"><?php esc_html_e('Payment Method', 'hyip-theme'); ?></label>
                                    <select class="form-select" id="payment_method" name="payment_method" required>
                                        <option value=""><?php esc_html_e('Select payment method', 'hyip-theme'); ?></option>
                                        <?php
                                        // Get payment methods from HYIPLab
                                        $payment_methods = apply_filters('hyiplab_payment_methods', array());
                                        foreach ($payment_methods as $method) :
                                            ?>
                                            <option value="<?php echo esc_attr($method['id']); ?>">
                                                <?php echo esc_html($method['name']); ?>
                                            </option>
                                            <?php
                                        endforeach;
                                        ?>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <?php esc_html_e('Close', 'hyip-theme'); ?>
                            </button>
                            <button type="button" class="btn btn-primary" id="submitInvestment">
                                <?php esc_html_e('Confirm Investment', 'hyip-theme'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
get_footer(); 
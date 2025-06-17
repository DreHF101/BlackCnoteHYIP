<?php
/**
 * Template Name: BlackCnote Investment Plans
 * Template Post Type: page
 *
 * @package BlackCnote_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// If BlackCnote plugin is not active, show demo content
if (!function_exists('blackcnote_system_instance')) {
    echo '<div class="alert alert-info">Demo: BlackCnote plugin is not active. Showing sample investment plans.</div>';
    ?>
    <main id="primary" class="site-main">
        <div class="container">
            <div class="blackcnote-plans-page">
                <header class="page-header">
                    <h1 class="page-title">Investment Plans</h1>
                    <p class="page-description">Choose from our selection of investment plans to start earning returns.</p>
                </header>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Starter Plan</h5>
                                <p class="card-text">Invest $100 - $999<br>Return: 5% per month</p>
                                <a href="#" class="btn btn-primary disabled">Demo Only</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Pro Plan</h5>
                                <p class="card-text">Invest $1,000 - $4,999<br>Return: 7% per month</p>
                                <a href="#" class="btn btn-primary disabled">Demo Only</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Elite Plan</h5>
                                <p class="card-text">Invest $5,000+<br>Return: 10% per month</p>
                                <a href="#" class="btn btn-primary disabled">Demo Only</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php
    get_footer();
    return;
}

// Check if BlackCnote plugin is active
if (!function_exists('blackcnote_system_instance')) {
    wp_die(esc_html__('BlackCnote plugin is required for this page.', 'blackcnote-theme'));
}
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="blackcnote-plans-page">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('Investment Plans', 'blackcnote-theme'); ?></h1>
                <p class="page-description">
                    <?php esc_html_e('Choose from our selection of investment plans to start earning returns.', 'blackcnote-theme'); ?>
                </p>
            </header>

            <!-- Investment Plans Grid -->
            <div class="blackcnote-plans">
                <?php
                global $wpdb;
                try {
                    $plans = $wpdb->get_results(
                        "SELECT * FROM {$wpdb->prefix}blackcnote_plans 
                        WHERE status = 'active' 
                        ORDER BY min_amount ASC"
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
                                        <span class="label"><?php esc_html_e('Min Investment:', 'blackcnote-theme'); ?></span>
                                        <span class="value"><?php echo esc_html(number_format($plan->min_amount, 2)); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label"><?php esc_html_e('Max Investment:', 'blackcnote-theme'); ?></span>
                                        <span class="value"><?php echo esc_html(number_format($plan->max_amount, 2)); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label"><?php esc_html_e('Return Period:', 'blackcnote-theme'); ?></span>
                                        <span class="value"><?php echo esc_html($plan->return_period); ?></span>
                                    </div>
                                </div>

                                <?php if (is_user_logged_in()) : ?>
                                    <button class="btn btn-primary invest-btn w-100" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#investModal"
                                            data-plan-id="<?php echo esc_attr($plan->id); ?>"
                                            data-plan-name="<?php echo esc_attr($plan->name); ?>"
                                            data-min-amount="<?php echo esc_attr($plan->min_amount); ?>"
                                            data-max-amount="<?php echo esc_attr($plan->max_amount); ?>"
                                            data-return-rate="<?php echo esc_attr($plan->return_rate); ?>">
                                        <?php esc_html_e('Invest Now', 'blackcnote-theme'); ?>
                                    </button>
                                <?php else : ?>
                                    <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" class="btn btn-secondary w-100">
                                        <?php esc_html_e('Login to Invest', 'blackcnote-theme'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <?php
                        endforeach;
                    else :
                        ?>
                        <div class="no-plans">
                            <p><?php esc_html_e('No investment plans available at the moment.', 'blackcnote-theme'); ?></p>
                        </div>
                        <?php
                    endif;
                } catch (Exception $e) {
                    error_log('BlackCnote Theme Error: ' . $e->getMessage());
                    echo '<p class="error">' . esc_html__('Error loading investment plans.', 'blackcnote-theme') . '</p>';
                }
                ?>
            </div>

            <!-- Investment Modal -->
            <div class="modal fade" id="investModal" tabindex="-1" aria-labelledby="investModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="investModalLabel"><?php esc_html_e('Make Investment', 'blackcnote-theme'); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="investForm" class="blackcnote-form">
                                <?php wp_nonce_field('blackcnote_invest_nonce', 'blackcnote_invest_nonce'); ?>
                                <input type="hidden" name="plan_id" id="plan_id">
                                
                                <div class="mb-3">
                                    <label for="plan_name" class="form-label"><?php esc_html_e('Plan', 'blackcnote-theme'); ?></label>
                                    <input type="text" class="form-control" id="plan_name" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="amount" class="form-label"><?php esc_html_e('Investment Amount', 'blackcnote-theme'); ?></label>
                                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                                    <div class="form-text">
                                        <span id="min_amount"></span> - <span id="max_amount"></span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><?php esc_html_e('Expected Return', 'blackcnote-theme'); ?></label>
                                    <div id="return_calculation" class="alert alert-info">
                                        <?php esc_html_e('Enter amount to calculate return', 'blackcnote-theme'); ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="payment_method" class="form-label"><?php esc_html_e('Payment Method', 'blackcnote-theme'); ?></label>
                                    <select class="form-select" id="payment_method" name="payment_method" required>
                                        <option value=""><?php esc_html_e('Select payment method', 'blackcnote-theme'); ?></option>
                                        <?php
                                        // Get payment methods from BlackCnote
                                        $payment_methods = apply_filters('blackcnote_payment_methods', array());
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
                                <?php esc_html_e('Close', 'blackcnote-theme'); ?>
                            </button>
                            <button type="button" class="btn btn-primary" id="submitInvestment">
                                <?php esc_html_e('Confirm Investment', 'blackcnote-theme'); ?>
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
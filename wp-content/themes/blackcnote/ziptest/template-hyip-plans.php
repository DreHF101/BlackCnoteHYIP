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
                    <?php esc_html_e('Choose from our range of investment plans to start earning returns.', 'blackcnote-theme'); ?>
                </p>
            </header>

            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><?php esc_html_e('Investment Calculator', 'blackcnote-theme'); ?></h5>
                            <form id="investment-calculator" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="plan_id" class="form-label">
                                        <?php esc_html_e('Select Plan', 'blackcnote-theme'); ?>
                                    </label>
                                    <select class="form-select" id="plan_id" name="plan_id" required>
                                        <option value=""><?php esc_html_e('Choose a plan...', 'blackcnote-theme'); ?></option>
                                        <?php
                                        global $wpdb;
                                        $plans = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}blackcnote_plans ORDER BY return_rate ASC");
                                        foreach ($plans as $plan) : ?>
                                            <option value="<?php echo esc_attr($plan->id); ?>">
                                                <?php echo esc_html($plan->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        <?php esc_html_e('Please select a plan.', 'blackcnote-theme'); ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="amount" class="form-label">
                                        <?php esc_html_e('Investment Amount', 'blackcnote-theme'); ?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control" 
                                               id="amount" 
                                               name="amount" 
                                               min="0" 
                                               step="0.01" 
                                               required>
                                        <div class="invalid-feedback">
                                            <?php esc_html_e('Please enter a valid amount.', 'blackcnote-theme'); ?>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <?php esc_html_e('Calculate Return', 'blackcnote-theme'); ?>
                                </button>
                            </form>

                            <div id="calculator-result" class="mt-4" style="display: none;">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <?php esc_html_e('Estimated Return', 'blackcnote-theme'); ?>
                                    </h6>
                                    <p class="mb-0">
                                        <span id="return-amount" class="h4 d-block"></span>
                                        <span id="return-rate" class="text-muted"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="row">
                        <?php foreach ($plans as $plan) : ?>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo esc_html($plan->name); ?></h5>
                                        <p class="card-text">
                                            <?php echo esc_html($plan->description); ?>
                                        </p>
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <strong><?php esc_html_e('Return Rate', 'blackcnote-theme'); ?></strong>
                                                <span class="float-end"><?php echo esc_html($plan->return_rate); ?>%</span>
                                            </li>
                                            <li class="mb-2">
                                                <strong><?php esc_html_e('Duration', 'blackcnote-theme'); ?></strong>
                                                <span class="float-end">
                                                    <?php 
                                                    printf(
                                                        /* translators: %d: number of days */
                                                        esc_html__('%d days', 'blackcnote-theme'),
                                                        $plan->duration
                                                    );
                                                    ?>
                                                </span>
                                            </li>
                                            <li class="mb-2">
                                                <strong><?php esc_html_e('Min. Investment', 'blackcnote-theme'); ?></strong>
                                                <span class="float-end">
                                                    <?php 
                                                    printf(
                                                        /* translators: %s: minimum investment amount */
                                                        esc_html__('%s', 'blackcnote-theme'),
                                                        number_format($plan->min_amount, 2) . ' ' . get_woocommerce_currency_symbol()
                                                    );
                                                    ?>
                                                </span>
                                            </li>
                                            <li class="mb-2">
                                                <strong><?php esc_html_e('Max. Investment', 'blackcnote-theme'); ?></strong>
                                                <span class="float-end">
                                                    <?php 
                                                    printf(
                                                        /* translators: %s: maximum investment amount */
                                                        esc_html__('%s', 'blackcnote-theme'),
                                                        number_format($plan->max_amount, 2) . ' ' . get_woocommerce_currency_symbol()
                                                    );
                                                    ?>
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-footer">
                                        <?php if (is_user_logged_in()) : ?>
                                            <a href="<?php echo esc_url(home_url('/invest?plan=' . $plan->id)); ?>" 
                                               class="btn btn-primary w-100">
                                                <?php esc_html_e('Invest Now', 'blackcnote-theme'); ?>
                                            </a>
                                        <?php else : ?>
                                            <a href="<?php echo esc_url(home_url('/login')); ?>" 
                                               class="btn btn-outline-primary w-100">
                                                <?php esc_html_e('Login to Invest', 'blackcnote-theme'); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
jQuery(document).ready(function($) {
    $('#investment-calculator').on('submit', function(e) {
        e.preventDefault();
        
        const planId = $('#plan_id').val();
        const amount = $('#amount').val();
        
        if (!planId || !amount) {
            return;
        }
        
        $.ajax({
            url: blackcnoteTheme.ajaxUrl,
            type: 'POST',
            data: {
                action: 'blackcnote_calculate_return',
                nonce: blackcnoteTheme.nonce,
                plan_id: planId,
                amount: amount
            },
            success: function(response) {
                if (response.success) {
                    $('#return-amount').text(response.data.return_amount);
                    $('#return-rate').text(response.data.return_rate);
                    $('#calculator-result').show();
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('<?php esc_html_e('An error occurred. Please try again.', 'blackcnote-theme'); ?>');
            }
        });
    });
});
</script>

<?php
get_footer(); 
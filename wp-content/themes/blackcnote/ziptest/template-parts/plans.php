<?php
/**
 * Template part for displaying investment plans
 *
 * @package BlackCnote_Theme
 * @since 1.0.0
 */

declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get investment plans
global $wpdb;
$plans = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}blackcnote_plans ORDER BY return_rate ASC");
?>

<div class="plans">
    <!-- Investment Calculator -->
    <div class="row mb-5">
        <div class="col-lg-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Investment Calculator', 'blackcnote-theme'); ?></h5>
                    <form id="investment-calculator" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="plan_id" class="form-label">
                                <?php esc_html_e('Select Plan', 'blackcnote-theme'); ?>
                            </label>
                            <select class="form-select" id="plan_id" name="plan_id" required>
                                <option value=""><?php esc_html_e('Choose a plan...', 'blackcnote-theme'); ?></option>
                                <?php foreach ($plans as $plan) : ?>
                                    <option value="<?php echo esc_attr($plan->id); ?>" 
                                            data-rate="<?php echo esc_attr($plan->return_rate); ?>">
                                        <?php echo esc_html($plan->name); ?> 
                                        (<?php echo esc_html($plan->return_rate); ?>%)
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
                                <span class="input-group-text">
                                    <?php echo esc_html(get_woocommerce_currency_symbol()); ?>
                                </span>
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

                    <div id="calculator-result" class="mt-4 d-none">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <?php esc_html_e('Estimated Return', 'blackcnote-theme'); ?>
                            </h6>
                            <p class="mb-0">
                                <span id="return-amount">0.00</span>
                                <?php echo esc_html(get_woocommerce_currency_symbol()); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Investment Plans -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($plans as $plan) : ?>
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo esc_html($plan->name); ?></h5>
                        <div class="text-center my-4">
                            <span class="display-4 text-primary">
                                <?php echo esc_html($plan->return_rate); ?>%
                            </span>
                            <p class="text-muted mb-0">
                                <?php esc_html_e('Return Rate', 'blackcnote-theme'); ?>
                            </p>
                        </div>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="bi bi-clock me-2"></i>
                                <?php 
                                printf(
                                    /* translators: %d: number of days */
                                    esc_html__('Duration: %d days', 'blackcnote-theme'),
                                    $plan->duration
                                );
                                ?>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-currency-dollar me-2"></i>
                                <?php 
                                printf(
                                    /* translators: %s: minimum investment amount */
                                    esc_html__('Min. Investment: %s', 'blackcnote-theme'),
                                    number_format($plan->min_amount, 2) . ' ' . get_woocommerce_currency_symbol()
                                );
                                ?>
                            </li>
                            <li>
                                <i class="bi bi-currency-dollar me-2"></i>
                                <?php 
                                printf(
                                    /* translators: %s: maximum investment amount */
                                    esc_html__('Max. Investment: %s', 'blackcnote-theme'),
                                    number_format($plan->max_amount, 2) . ' ' . get_woocommerce_currency_symbol()
                                );
                                ?>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
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

<script>
jQuery(document).ready(function($) {
    const calculator = $('#investment-calculator');
    const result = $('#calculator-result');
    const returnAmount = $('#return-amount');

    calculator.on('submit', function(e) {
        e.preventDefault();

        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        const planId = $('#plan_id').val();
        const amount = $('#amount').val();

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
                    returnAmount.text(response.data.return_amount.toFixed(2));
                    result.removeClass('d-none');
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
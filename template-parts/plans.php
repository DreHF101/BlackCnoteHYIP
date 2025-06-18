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
$plans = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}blackcnotelab_plans ORDER BY min_amount ASC");
?>

<section class="investment-plans-section py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Investment Plans</h2>
            <p class="lead text-muted mb-4">Choose from our carefully designed investment plans that offer competitive returns while supporting Black-owned businesses and community projects.</p>
        </div>
        <div class="row justify-content-center">
            <?php foreach ($plans as $i => $plan) : ?>
                <div class="col-12 col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 shadow-lg border-0<?php if ($i === 1) echo ' border-warning border-3'; ?>">
                        <?php if ($i === 1) : ?>
                            <div class="bg-warning text-white text-center py-2 fw-bold">Most Popular</div>
                        <?php endif; ?>
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold mb-3"><?php echo esc_html($plan->name); ?></h5>
                            <div class="display-4 fw-bold text-warning mb-2"><?php echo esc_html($plan->return_rate); ?>%</div>
                            <div class="text-muted mb-2">Daily Return</div>
                            <ul class="list-unstyled mb-4">
                                <li>Min Investment: <span class="fw-semibold">$<?php echo number_format($plan->min_amount); ?></span></li>
                                <li>Max Investment: <span class="fw-semibold">$<?php echo number_format($plan->max_amount); ?></span></li>
                                <li>Duration: <span class="fw-semibold"><?php echo intval($plan->duration); ?> days</span></li>
                                <li>Total Return: <span class="fw-semibold text-success"><?php echo esc_html(number_format($plan->return_rate * $plan->duration, 0)); ?>%</span></li>
                            </ul>
                            <?php if (is_user_logged_in()) : ?>
                                <a href="<?php echo esc_url(home_url('/invest?plan=' . $plan->id)); ?>" class="btn btn-warning w-100"><?php esc_html_e('Invest Now', 'blackcnote-theme'); ?></a>
                            <?php else : ?>
                                <a href="<?php echo esc_url(home_url('/login')); ?>" class="btn btn-outline-warning w-100"><?php esc_html_e('Login to Invest', 'blackcnote-theme'); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

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
            url: blackcnote_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'blackcnote_calculate_return',
                nonce: blackcnote_ajax.nonce,
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
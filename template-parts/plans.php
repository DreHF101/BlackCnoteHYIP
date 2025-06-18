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
$plans = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}blackcnotelab_plans ORDER BY return_rate ASC");
?>

<div class="plans">
    <!-- Investment Calculator -->
    <div class="row mb-5">
        <div class="col-lg-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Investment Calculator', 'blackcnote-theme'); ?></h5>
                    <form id="investment-calculator" class="row g-3">
                        <div class="col-md-6">
                            <label for="plan-select" class="form-label">
                                <?php esc_html_e('Select Plan', 'blackcnote-theme'); ?>
                            </label>
                            <select id="plan-select" name="plan_id" class="form-select" required>
                                <option value=""><?php esc_html_e('Choose a plan...', 'blackcnote-theme'); ?></option>
                                <?php foreach ($plans as $plan) : ?>
                                    <option value="<?php echo esc_attr($plan->id); ?>" 
                                            data-return-rate="<?php echo esc_attr($plan->return_rate); ?>"
                                            data-duration="<?php echo esc_attr($plan->duration); ?>">
                                        <?php echo esc_html($plan->name); ?> (<?php echo esc_html($plan->return_rate); ?>%)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                <?php esc_html_e('Please select a plan.', 'blackcnote-theme'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="investment-amount" class="form-label">
                                <?php esc_html_e('Investment Amount', 'blackcnote-theme'); ?>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="investment-amount" name="amount" class="form-control" 
                                       min="10" step="0.01" placeholder="1000.00" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <?php esc_html_e('Calculate Returns', 'blackcnote-theme'); ?>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Calculation Results -->
                    <div id="calculation-results" class="mt-4" style="display: none;">
                        <h6><?php esc_html_e('Projected Returns:', 'blackcnote-theme'); ?></h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong><?php esc_html_e('Total Return:', 'blackcnote-theme'); ?></strong></p>
                                <p class="text-success h5" id="total-return">$0.00</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong><?php esc_html_e('Profit:', 'blackcnote-theme'); ?></strong></p>
                                <p class="text-primary h5" id="profit">$0.00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Investment Plans -->
    <div class="row">
        <?php if (!empty($plans)) : ?>
            <?php foreach ($plans as $plan) : ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo esc_html($plan->name); ?></h5>
                            <div class="display-6 text-primary mb-3"><?php echo esc_html($plan->return_rate); ?>%</div>
                            <p class="card-text text-muted">
                                <?php
                                printf(
                                    esc_html__('Duration: %d days', 'blackcnote-theme'),
                                    $plan->duration
                                );
                                ?>
                            </p>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <strong><?php esc_html_e('Min Investment:', 'blackcnote-theme'); ?></strong> 
                                    $<?php echo esc_html(number_format($plan->min_amount, 2)); ?>
                                </li>
                                <li class="mb-2">
                                    <strong><?php esc_html_e('Max Investment:', 'blackcnote-theme'); ?></strong> 
                                    $<?php echo esc_html(number_format($plan->max_amount, 2)); ?>
                                </li>
                                <li>
                                    <strong><?php esc_html_e('Daily Return:', 'blackcnote-theme'); ?></strong> 
                                    <?php echo esc_html(number_format($plan->return_rate / $plan->duration, 2)); ?>%
                                </li>
                            </ul>
                            <button type="button" class="btn btn-primary" 
                                    onclick="selectPlan(<?php echo esc_attr($plan->id); ?>, '<?php echo esc_attr($plan->name); ?>', <?php echo esc_attr($plan->return_rate); ?>, <?php echo esc_attr($plan->duration); ?>)">
                                <?php esc_html_e('Select Plan', 'blackcnote-theme'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <?php esc_html_e('No investment plans available at the moment.', 'blackcnote-theme'); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function selectPlan(planId, planName, returnRate, duration) {
    document.getElementById('plan-select').value = planId;
    document.getElementById('plan-select').dispatchEvent(new Event('change'));
}

jQuery(document).ready(function($) {
    $('#investment-calculator').on('submit', function(e) {
        e.preventDefault();
        
        const planId = $('#plan-select').val();
        const amount = parseFloat($('#investment-amount').val());
        
        if (!planId || !amount) {
            alert('<?php esc_html_e('Please fill in all fields.', 'blackcnote-theme'); ?>');
            return;
        }
        
        const selectedOption = $('#plan-select option:selected');
        const returnRate = parseFloat(selectedOption.data('return-rate'));
        const duration = parseInt(selectedOption.data('duration'));
        
        const totalReturn = amount * (1 + returnRate / 100);
        const profit = totalReturn - amount;
        
        $('#total-return').text('$' + totalReturn.toFixed(2));
        $('#profit').text('$' + profit.toFixed(2));
        $('#calculation-results').show();
    });
});
</script> 
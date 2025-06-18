<?php
/**
 * Template Name: BlackCnote Plans
 *
 * @package BlackCnote_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Check if BlackCnoteLab plugin is active
if (!function_exists('blackcnotelab_system_instance')) {
    wp_die(esc_html__('BlackCnoteLab plugin is required for this page.', 'blackcnote-theme'));
}
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="blackcnote-plans">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('Investment Plans', 'blackcnote-theme'); ?></h1>
                <p class="page-description">
                    <?php esc_html_e('Choose Your Investment Plan', 'blackcnote-theme'); ?>
                </p>
            </header>

            <div class="row">
                <?php
                global $wpdb;
                try {
                    $plans = $wpdb->get_results(
                        "SELECT * FROM {$wpdb->prefix}blackcnotelab_plans
                        WHERE status = 'active'
                        ORDER BY min_amount ASC"
                    );

                    if ($plans) {
                        foreach ($plans as $plan) {
                            ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h3 class="card-title"><?php echo esc_html($plan->name); ?></h3>
                                        <div class="plan-details">
                                            <p>
                                                <strong><?php esc_html_e('Minimum Investment:', 'blackcnote-theme'); ?></strong>
                                                <?php echo esc_html(number_format($plan->min_amount, 2)); ?>
                                            </p>
                                            <p>
                                                <strong><?php esc_html_e('Maximum Investment:', 'blackcnote-theme'); ?></strong>
                                                <?php echo esc_html(number_format($plan->max_amount, 2)); ?>
                                            </p>
                                            <p>
                                                <strong><?php esc_html_e('Interest Rate:', 'blackcnote-theme'); ?></strong>
                                                <?php echo esc_html($plan->interest_rate); ?>%
                                            </p>
                                            <p>
                                                <strong><?php esc_html_e('Term:', 'blackcnote-theme'); ?></strong>
                                                <?php echo esc_html($plan->term); ?> days
                                            </p>
                                            <p>
                                                <strong><?php esc_html_e('Return:', 'blackcnote-theme'); ?></strong>
                                                <?php echo esc_html(number_format($plan->return_amount, 2)); ?>
                                            </p>
                                            <p>
                                                <strong><?php esc_html_e('Payment Frequency:', 'blackcnote-theme'); ?></strong>
                                                <?php echo esc_html($plan->payment_frequency); ?>
                                            </p>
                                            <p>
                                                <strong><?php esc_html_e('Status:', 'blackcnote-theme'); ?></strong>
                                                <span class="badge bg-<?php echo $plan->status === 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo esc_html(ucfirst($plan->status)); ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="text-center mt-4">
                                            <button class="btn btn-primary select-plan" data-plan-id="<?php echo esc_attr($plan->id); ?>">
                                                <?php esc_html_e('Select Plan', 'blackcnote-theme'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<p class="text-center">' . esc_html__('No investment plans available.', 'blackcnote-theme') . '</p>';
                    }
                } catch (Exception $e) {
                    error_log('BlackCnote Theme Error: ' . $e->getMessage());
                    echo '<p class="error text-center">' . esc_html__('Error loading investment plans.', 'blackcnote-theme') . '</p>';
                }
                ?>
            </div>

            <!-- Investment Form Modal -->
            <div class="modal fade" id="investmentModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php esc_html_e('Investment Amount', 'blackcnote-theme'); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="investmentForm">
                                <div class="mb-3">
                                    <label for="amount" class="form-label"><?php esc_html_e('Amount', 'blackcnote-theme'); ?></label>
                                    <input type="number" class="form-control" id="amount" name="amount" required>
                                </div>
                                <div class="text-center">
                                    <button type="button" class="btn btn-secondary" id="calculateReturn">
                                        <?php esc_html_e('Calculate Return', 'blackcnote-theme'); ?>
                                    </button>
                                </div>
                                <div class="mt-3" id="returnEstimate" style="display: none;">
                                    <p class="text-center">
                                        <strong><?php esc_html_e('Estimated Return:', 'blackcnote-theme'); ?></strong>
                                        <span id="estimatedReturn">0.00</span>
                                    </p>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php esc_html_e('Close', 'blackcnote-theme'); ?></button>
                            <button type="button" class="btn btn-primary" id="investNow"><?php esc_html_e('Invest Now', 'blackcnote-theme'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
jQuery(document).ready(function($) {
    let selectedPlanId = null;

    $('.select-plan').on('click', function() {
        selectedPlanId = $(this).data('plan-id');
        $('#investmentModal').modal('show');
    });

    $('#calculateReturn').on('click', function() {
        const amount = $('#amount').val();
        if (amount && selectedPlanId) {
            $.ajax({
                url: blackcnote_theme.ajax_url,
                type: 'POST',
                data: {
                    action: 'calculate_return',
                    plan_id: selectedPlanId,
                    amount: amount,
                    nonce: blackcnote_theme.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('#estimatedReturn').text(response.data.return_amount);
                        $('#returnEstimate').show();
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        }
    });

    $('#investNow').on('click', function() {
        const amount = $('#amount').val();
        if (amount && selectedPlanId) {
            $.ajax({
                url: blackcnote_theme.ajax_url,
                type: 'POST',
                data: {
                    action: 'create_investment',
                    plan_id: selectedPlanId,
                    amount: amount,
                    nonce: blackcnote_theme.nonce
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.data.redirect_url;
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        }
    });
});
</script>

<?php
get_footer(); 
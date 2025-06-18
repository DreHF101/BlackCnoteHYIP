<?php
/**
 * Template part for displaying the transactions table
 *
 * @package BlackCnote_Theme
 * @since 1.0.0
 */

declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get transactions from passed data
$transactions = $args['transactions'] ?? [];
?>

<?php if ($transactions) : ?>
    <?php foreach ($transactions as $transaction) : ?>
        <tr>
            <td>
                <span class="badge bg-<?php 
                    echo $transaction->type === 'investment' ? 'primary' : 
                        ($transaction->type === 'interest' ? 'success' : 'warning'); 
                ?>">
                    <?php echo esc_html(ucfirst($transaction->type)); ?>
                </span>
            </td>
            <td><?php echo esc_html(number_format($transaction->amount, 2)); ?> <?php echo esc_html(get_woocommerce_currency_symbol()); ?></td>
            <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($transaction->created_at))); ?></td>
            <td>
                <span class="badge bg-<?php 
                    echo $transaction->status === 'completed' ? 'success' : 
                        ($transaction->status === 'pending' ? 'warning' : 'secondary'); 
                ?>">
                    <?php echo esc_html(ucfirst($transaction->status)); ?>
                </span>
            </td>
            <td>
                <?php if ($transaction->type === 'investment' && $transaction->plan_id) : 
                    global $wpdb;
                    $plan = $wpdb->get_row($wpdb->prepare(
                        "SELECT * FROM {$wpdb->prefix}blackcnotelab_plans WHERE id = %d",
                        $transaction->plan_id
                    ));
                    if ($plan) :
                ?>
                    <button type="button" class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#plan-details-<?php echo esc_attr($transaction->id); ?>">
                        View Plan
                    </button>
                    <div class="modal fade" id="plan-details-<?php echo esc_attr($transaction->id); ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><?php echo esc_html($plan->name); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><strong>Return Rate:</strong> <?php echo esc_html($plan->return_rate); ?>%</li>
                                        <li class="mb-2"><strong>Duration:</strong> <?php printf('%d days', $plan->duration); ?></li>
                                        <li><strong>Investment Amount:</strong> <?php echo esc_html(number_format($transaction->amount, 2)); ?> <?php echo esc_html(get_woocommerce_currency_symbol()); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <tr>
        <td colspan="5" class="text-center">
            <?php esc_html_e('No transactions found.', 'blackcnote-theme'); ?>
        </td>
    </tr>
<?php endif; ?> 
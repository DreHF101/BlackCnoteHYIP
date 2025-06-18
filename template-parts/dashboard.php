<?php
/**
 * Template part for displaying the user dashboard
 *
 * @package BlackCnote_Theme
 * @since 1.0.0
 */

declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get user data
$user = wp_get_current_user();
$user_id = $user->ID;

// Get user's investments
global $wpdb;
$investments = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}blackcnotelab_transactions 
     WHERE user_id = %d AND type = 'investment' 
     ORDER BY created_at DESC",
    $user_id
));

// Get user's earnings
$earnings = $wpdb->get_var($wpdb->prepare(
    "SELECT SUM(amount) FROM {$wpdb->prefix}blackcnotelab_transactions 
     WHERE user_id = %d AND type = 'interest'",
    $user_id
));

// Get active investments
$active_investments = $wpdb->get_var($wpdb->prepare(
    "SELECT SUM(amount) FROM {$wpdb->prefix}blackcnotelab_transactions 
     WHERE user_id = %d AND type = 'investment' AND status = 'active'",
    $user_id
));
?>

<section class="dashboard-section py-5 bg-light">
  <div class="container">
    <div class="row mb-5 text-center">
      <div class="col-md-4 mb-4">
        <div class="card shadow border-0">
          <div class="card-body">
            <h5 class="card-title text-muted"><?php esc_html_e('Total Balance', 'blackcnote-theme'); ?></h5>
            <div class="display-5 fw-bold text-success">
              <?php echo esc_html(number_format($active_investments + $earnings, 2)); ?>
              <small class="text-muted"><?php echo esc_html(get_woocommerce_currency_symbol()); ?></small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card shadow border-0">
          <div class="card-body">
            <h5 class="card-title text-muted"><?php esc_html_e('Active Investments', 'blackcnote-theme'); ?></h5>
            <div class="display-5 fw-bold text-warning">
              <?php echo esc_html(number_format($active_investments, 2)); ?>
              <small class="text-muted"><?php echo esc_html(get_woocommerce_currency_symbol()); ?></small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card shadow border-0">
          <div class="card-body">
            <h5 class="card-title text-muted"><?php esc_html_e('Total Earnings', 'blackcnote-theme'); ?></h5>
            <div class="display-5 fw-bold text-primary">
              <?php echo esc_html(number_format($earnings, 2)); ?>
              <small class="text-muted"><?php echo esc_html(get_woocommerce_currency_symbol()); ?></small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row mb-5">
      <div class="col-12">
        <div class="card shadow border-0">
          <div class="card-body text-center">
            <h5 class="card-title mb-4">Quick Actions</h5>
            <div class="d-flex flex-wrap justify-content-center gap-3">
              <a href="<?php echo esc_url(home_url('/plans')); ?>" class="btn btn-warning btn-lg">New Investment</a>
              <a href="<?php echo esc_url(home_url('/withdraw')); ?>" class="btn btn-outline-warning btn-lg">Withdraw</a>
              <a href="<?php echo esc_url(home_url('/profile')); ?>" class="btn btn-outline-secondary btn-lg">Edit Profile</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row mb-5">
      <div class="col-12">
        <div class="card shadow border-0">
          <div class="card-body">
            <h5 class="card-title mb-4">Active Investments</h5>
            <?php if ($investments) : ?>
              <div class="table-responsive">
                <table class="table align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Plan</th>
                      <th>Amount</th>
                      <th>Return Rate</th>
                      <th>Start Date</th>
                      <th>End Date</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($investments as $investment) : 
                      $plan = $wpdb->get_row($wpdb->prepare(
                        "SELECT * FROM {$wpdb->prefix}blackcnotelab_plans WHERE id = %d",
                        $investment->plan_id
                      ));
                    ?>
                    <tr>
                      <td><?php echo esc_html($plan->name); ?></td>
                      <td><?php echo esc_html(number_format($investment->amount, 2)); ?> <?php echo esc_html(get_woocommerce_currency_symbol()); ?></td>
                      <td><?php echo esc_html($plan->return_rate); ?>%</td>
                      <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($investment->created_at))); ?></td>
                      <td><?php $end_date = strtotime($investment->created_at . ' + ' . $plan->duration . ' days'); echo esc_html(date_i18n(get_option('date_format'), $end_date)); ?></td>
                      <td><span class="badge bg-<?php echo $investment->status === 'active' ? 'success' : 'secondary'; ?>"> <?php echo esc_html(ucfirst($investment->status)); ?> </span></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else : ?>
              <p class="text-muted mb-0">No active investments found.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="card shadow border-0">
          <div class="card-body">
            <h5 class="card-title mb-4">Recent Transactions</h5>
            <?php
            $transactions = $wpdb->get_results($wpdb->prepare(
              "SELECT * FROM {$wpdb->prefix}blackcnotelab_transactions 
               WHERE user_id = %d 
               ORDER BY created_at DESC 
               LIMIT 5",
              $user_id
            ));
            ?>
            <?php if ($transactions) : ?>
              <div class="table-responsive">
                <table class="table align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Type</th>
                      <th>Amount</th>
                      <th>Date</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($transactions as $transaction) : ?>
                    <tr>
                      <td><?php echo esc_html(ucfirst($transaction->type)); ?></td>
                      <td><?php echo esc_html(number_format($transaction->amount, 2)); ?> <?php echo esc_html(get_woocommerce_currency_symbol()); ?></td>
                      <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($transaction->created_at))); ?></td>
                      <td><span class="badge bg-<?php echo $transaction->status === 'completed' ? 'success' : 'warning'; ?>"> <?php echo esc_html(ucfirst($transaction->status)); ?> </span></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <div class="text-end mt-3">
                <a href="<?php echo esc_url(home_url('/transactions')); ?>" class="btn btn-link">View All Transactions</a>
              </div>
            <?php else : ?>
              <p class="text-muted mb-0">No transactions found.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section> 
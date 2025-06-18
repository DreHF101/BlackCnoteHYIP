<?php
/**
 * Homepage Investment Plans Section
 * @package BlackCnote_Theme
 */
global $wpdb;
$plans = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}blackcnotelab_plans ORDER BY min_amount ASC");
?>
<section id="plans" class="plans-section py-5 bg-light">
  <div class="container">
    <h2 class="text-center mb-5 fw-bold">Investment Plans</h2>
    <div class="row justify-content-center">
      <?php if ($plans && count($plans) > 0): ?>
        <?php foreach ($plans as $i => $plan): ?>
          <div class="col-md-3 mb-4">
            <div class="card h-100 border-warning<?php echo ($i === 1) ? ' border-3' : ''; ?>">
              <div class="card-body text-center">
                <h3 class="card-title h5">
                  <?php echo esc_html($plan->name); ?>
                  <?php if ($i === 1): // Highlight 2nd plan as Most Popular ?>
                    <span class="badge bg-warning text-dark ms-2">Most Popular</span>
                  <?php endif; ?>
                </h3>
                <div class="display-6 text-warning"><?php echo esc_html($plan->return_rate); ?>%</div>
                <p class="text-muted mb-2">Daily Return</p>
                <ul class="list-unstyled mb-4">
                  <li>Min: $<?php echo number_format($plan->min_amount); ?></li>
                  <li>Max: $<?php echo number_format($plan->max_amount); ?></li>
                  <li><?php echo intval($plan->duration); ?> days</li>
                </ul>
                <div class="mb-2 text-success fw-bold">
                  Total Return: <?php echo esc_html(number_format($plan->return_rate * $plan->duration, 0)); ?>%
                </div>
                <a href="#" class="btn btn-warning">Invest Now</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center">
          <p>No investment plans found.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section> 
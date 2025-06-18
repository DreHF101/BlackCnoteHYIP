<?php
/**
 * Homepage Stats Section
 * @package BlackCnote
 */
$settings = get_option('blackcnote_theme_settings', [
  'stat_total_invested' => '2500000',
  'stat_active_investors' => '1200',
  'stat_success_rate' => '98.5',
  'stat_years_experience' => '5',
]);
?>
<section class="stats-section py-5 bg-light">
  <div class="container">
    <div class="row text-center">
      <div class="col-md-3 mb-4 mb-md-0">
        <div class="display-5 fw-bold text-warning counter" data-count="<?php echo esc_attr($settings['stat_total_invested']); ?>">$0</div>
        <div class="text-muted">Total Invested</div>
      </div>
      <div class="col-md-3 mb-4 mb-md-0">
        <div class="display-5 fw-bold text-warning counter" data-count="<?php echo esc_attr($settings['stat_active_investors']); ?>">0</div>
        <div class="text-muted">Active Investors</div>
      </div>
      <div class="col-md-3 mb-4 mb-md-0">
        <div class="display-5 fw-bold text-warning counter" data-count="<?php echo esc_attr($settings['stat_success_rate']); ?>">0%</div>
        <div class="text-muted">Success Rate</div>
      </div>
      <div class="col-md-3">
        <div class="display-5 fw-bold text-warning counter" data-count="<?php echo esc_attr($settings['stat_years_experience']); ?>">0</div>
        <div class="text-muted">Years Experience</div>
      </div>
    </div>
  </div>
</section>
<script>
jQuery(document).ready(function($){
  $('.counter').each(function() {
    var $this = $(this), countTo = $this.data('count');
    var isPercent = $this.text().indexOf('%') !== -1;
    var isMoney = $this.text().indexOf('$') !== -1;
    $({ countNum: 0 }).animate({ countNum: countTo }, {
      duration: 2000,
      easing: 'swing',
      step: function() {
        if(isPercent) {
          $this.text(this.countNum.toFixed(1) + '%');
        } else if(isMoney) {
          $this.text('$' + Math.floor(this.countNum).toLocaleString());
        } else {
          $this.text(Math.floor(this.countNum).toLocaleString());
        }
      },
      complete: function() {
        if(isPercent) {
          $this.text(countTo + '%');
        } else if(isMoney) {
          $this.text('$' + Number(countTo).toLocaleString());
        } else {
          $this.text(Number(countTo).toLocaleString());
        }
      }
    });
  });
});
</script> 
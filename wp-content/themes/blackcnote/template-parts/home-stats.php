<?php
/**
 * Homepage Stats Section
 * @package BlackCnote
 */
?>
<section class="stats-section py-5 bg-light">
  <div class="container">
    <div class="row text-center">
      <div class="col-md-3 mb-4 mb-md-0">
        <div class="display-5 fw-bold text-primary counter" data-count="2500000">$0</div>
        <div class="text-muted">Total Invested</div>
      </div>
      <div class="col-md-3 mb-4 mb-md-0">
        <div class="display-5 fw-bold text-primary counter" data-count="1200">0</div>
        <div class="text-muted">Active Investors</div>
      </div>
      <div class="col-md-3 mb-4 mb-md-0">
        <div class="display-5 fw-bold text-primary counter" data-count="98.5">0%</div>
        <div class="text-muted">Success Rate</div>
      </div>
      <div class="col-md-3">
        <div class="display-5 fw-bold text-primary counter" data-count="5">0</div>
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
          $this.text('$' + countTo.toLocaleString());
        } else {
          $this.text(countTo.toLocaleString());
        }
      }
    });
  });
});
</script> 
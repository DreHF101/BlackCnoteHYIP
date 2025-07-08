<?php
/**
 * Homepage Stats Section
 * @package BlackCnote
 */
?>
<section class="stats-section py-5 bg-light">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-3">
        <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition">
          <div class="card-body text-center p-4">
            <div class="display-5 fw-bold text-primary mb-2">
              <span class="counter" data-count="2500000">$0</span>
            </div>
            <div class="text-muted">Total Invested</div>
            <div class="progress mt-3" style="height: 4px;">
              <div class="progress-bar bg-primary" style="width: 85%"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition">
          <div class="card-body text-center p-4">
            <div class="display-5 fw-bold text-primary mb-2">
              <span class="counter" data-count="1200">0</span>
            </div>
            <div class="text-muted">Active Investors</div>
            <div class="progress mt-3" style="height: 4px;">
              <div class="progress-bar bg-primary" style="width: 75%"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition">
          <div class="card-body text-center p-4">
            <div class="display-5 fw-bold text-primary mb-2">
              <span class="counter" data-count="98.5">0</span>%
            </div>
            <div class="text-muted">Success Rate</div>
            <div class="progress mt-3" style="height: 4px;">
              <div class="progress-bar bg-primary" style="width: 98.5%"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition">
          <div class="card-body text-center p-4">
            <div class="display-5 fw-bold text-primary mb-2">
              <span class="counter" data-count="5">0</span>+
            </div>
            <div class="text-muted">Years Experience</div>
            <div class="progress mt-3" style="height: 4px;">
              <div class="progress-bar bg-primary" style="width: 100%"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
.hover-shadow-lg {
  transition: all 0.3s ease;
}
.hover-shadow-lg:hover {
  transform: translateY(-5px);
  box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
}
.transition {
  transition: all 0.3s ease;
}
</style>

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
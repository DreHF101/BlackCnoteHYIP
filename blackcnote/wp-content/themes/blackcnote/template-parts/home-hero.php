<?php
/**
 * Homepage Hero Section
 * @package BlackCnote
 */
?>
<section class="hero-section py-5 bg-gradient-primary text-light position-relative overflow-hidden">
  <div class="container position-relative z-3">
    <div class="row align-items-center">
      <div class="col-lg-6 mb-4 mb-lg-0">
        <h1 class="display-3 fw-bold mb-3">
          Build <span class="text-warning">Black Wealth</span> Through Smart Investment
        </h1>
        <p class="lead mb-4">
          Join our mission to close the wealth gap by 2040. Invest in high-yield programs that empower our community and generate sustainable returns.
        </p>
        <div class="d-flex flex-wrap gap-3">
          <a href="#plans" class="btn btn-warning btn-lg px-4 py-3 shadow-lg">
            <i class="fas fa-chart-line me-2"></i>Start Investing
          </a>
          <a href="#calculator" class="btn btn-outline-light btn-lg px-4 py-3 shadow-lg">
            <i class="fas fa-calculator me-2"></i>Calculate Returns
          </a>
        </div>
        <div class="mt-4 d-flex align-items-center">
          <div class="d-flex -space-x-4">
            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/avatar-1.jpg'); ?>" alt="Investor" class="rounded-circle border border-2 border-white" width="40">
            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/avatar-2.jpg'); ?>" alt="Investor" class="rounded-circle border border-2 border-white" width="40">
            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/avatar-3.jpg'); ?>" alt="Investor" class="rounded-circle border border-2 border-white" width="40">
          </div>
          <div class="ms-3">
            <span class="text-light">Join 1,200+ active investors</span>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="position-relative">
          <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/hero-chart.png'); ?>" alt="Investment Growth Chart" class="img-fluid rounded-4 shadow-lg">
          <div class="position-absolute top-0 end-0 bg-warning text-dark p-3 rounded-3 shadow-lg" style="transform: translate(20%, -20%);">
            <div class="h4 mb-0">98.5%</div>
            <div class="small">Success Rate</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="position-absolute top-0 end-0 w-50 h-100 bg-gradient-warning opacity-10" style="transform: skewX(-15deg);"></div>
</section> 
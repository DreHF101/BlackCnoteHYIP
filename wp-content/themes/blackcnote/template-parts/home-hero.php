<?php
/**
 * Homepage Hero Section
 * @package BlackCnote
 */
?>
<section class="hero-section py-5 bg-dark text-light position-relative">
  <div class="container d-flex flex-column flex-lg-row align-items-center justify-content-between">
    <div class="hero-content mb-4 mb-lg-0">
      <h1 class="display-3 fw-bold mb-3">
        Empowering <span class="text-warning">Black Wealth</span> Through Strategic Investment
      </h1>
      <p class="lead mb-4">
        Join BlackCnote's mission to flip the Black-White wealth gap by 2040. Invest in high-yield programs that circulate wealth within our community.
      </p>
      <div class="d-flex gap-3">
        <a href="#plans" class="btn btn-warning btn-lg shadow">Start Investing</a>
        <a href="#calculator" class="btn btn-outline-light btn-lg shadow">Calculate Returns</a>
      </div>
    </div>
    <div class="hero-image ms-lg-5">
      <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/hero-logo.png'); ?>" alt="BlackCnote Hero Logo" class="img-fluid rounded shadow" style="max-width: 420px;">
    </div>
  </div>
</section> 
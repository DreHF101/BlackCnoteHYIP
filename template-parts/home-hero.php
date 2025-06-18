<?php
/**
 * Homepage Hero Section
 * @package BlackCnote
 */
?>
<section class="hero-section py-5 bg-dark text-light position-relative" style="background: linear-gradient(90deg, #23272f 60%, #23272f00 100%);">
  <div class="container d-flex flex-column flex-lg-row align-items-center justify-content-between">
    <div class="hero-content mb-4 mb-lg-0" style="max-width: 600px;">
      <h1 class="display-3 fw-bold mb-3">
        Empowering <span class="text-warning">Black Wealth</span> Through Strategic Investment
      </h1>
      <p class="lead mb-4">
        Join BlackCnote's mission to flip the Black-White wealth gap by 2040. Invest in high-yield programs that circulate wealth within our community.
      </p>
      <div class="d-flex gap-3">
        <a href="#plans" class="btn btn-warning btn-lg shadow">Start Investing</a>
        <a href="#calculator" class="btn btn-outline-warning btn-lg shadow">Calculate Returns</a>
      </div>
    </div>
    <div class="hero-image ms-lg-5">
      <div style="background: #23272f; border-radius: 24px; padding: 12px; box-shadow: 0 4px 32px rgba(0,0,0,0.15); max-width: 420px;">
        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/hero-logo.png'); ?>" alt="BlackCnote Hero" class="img-fluid rounded" style="width: 100%; height: auto; display: block;">
      </div>
    </div>
  </div>
</section> 
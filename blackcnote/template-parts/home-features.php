<?php
/**
 * Homepage Features Section
 * @package BlackCnote
 */
?>
<section class="features-section py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill">Why Choose Us</span>
      <h2 class="display-4 fw-bold mb-3">Empowering Black Wealth Through Smart Investment</h2>
      <p class="lead text-muted">Join thousands of investors building generational wealth through our platform</p>
    </div>
    
    <div class="row g-4">
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition">
          <div class="card-body p-4">
            <div class="feature-icon-wrapper mb-4">
              <div class="feature-icon bg-warning bg-opacity-10 rounded-3 p-3">
                <i class="bi bi-graph-up-arrow display-6 text-warning"></i>
              </div>
            </div>
            <h3 class="h4 mb-3">High-Yield Returns</h3>
            <p class="text-muted mb-4">Earn competitive returns on your investments with our carefully selected HYIP programs.</p>
            <a href="#plans" class="text-warning text-decoration-none d-flex align-items-center">
              Learn More <i class="bi bi-arrow-right ms-2"></i>
            </a>
          </div>
        </div>
      </div>
      
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition">
          <div class="card-body p-4">
            <div class="feature-icon-wrapper mb-4">
              <div class="feature-icon bg-warning bg-opacity-10 rounded-3 p-3">
                <i class="bi bi-shield-lock display-6 text-warning"></i>
              </div>
            </div>
            <h3 class="h4 mb-3">Secure Platform</h3>
            <p class="text-muted mb-4">Your investments are protected with bank-level security and transparent operations.</p>
            <a href="#security" class="text-warning text-decoration-none d-flex align-items-center">
              Learn More <i class="bi bi-arrow-right ms-2"></i>
            </a>
          </div>
        </div>
      </div>
      
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition">
          <div class="card-body p-4">
            <div class="feature-icon-wrapper mb-4">
              <div class="feature-icon bg-warning bg-opacity-10 rounded-3 p-3">
                <i class="bi bi-people display-6 text-warning"></i>
              </div>
            </div>
            <h3 class="h4 mb-3">Community Focus</h3>
            <p class="text-muted mb-4">Invest in Black-owned businesses and projects that strengthen our community.</p>
            <a href="#community" class="text-warning text-decoration-none d-flex align-items-center">
              Learn More <i class="bi bi-arrow-right ms-2"></i>
            </a>
          </div>
        </div>
      </div>
      
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition">
          <div class="card-body p-4">
            <div class="feature-icon-wrapper mb-4">
              <div class="feature-icon bg-warning bg-opacity-10 rounded-3 p-3">
                <i class="bi bi-calculator display-6 text-warning"></i>
              </div>
            </div>
            <h3 class="h4 mb-3">Profit Calculator</h3>
            <p class="text-muted mb-4">Calculate your potential returns before investing with our advanced profit calculator.</p>
            <a href="#calculator" class="text-warning text-decoration-none d-flex align-items-center">
              Try Calculator <i class="bi bi-arrow-right ms-2"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
    
    <div class="text-center mt-5">
      <a href="#plans" class="btn btn-warning btn-lg px-5 py-3 shadow-lg">
        Start Investing Today
        <i class="bi bi-arrow-right ms-2"></i>
      </a>
    </div>
  </div>
</section>

<style>
.feature-icon-wrapper {
  width: 80px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.feature-icon {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}
.card:hover .feature-icon {
  transform: scale(1.1);
}
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
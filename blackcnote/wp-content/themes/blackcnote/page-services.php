<?php
/**
 * Template Name: Services Page
 * The services page template
 *
 * @package BlackCnote
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title"><?php the_title(); ?></h1>
        </div>

        <div class="page-content">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="default-content">
                    <h2>Investment Services</h2>
                    <p>BlackCnote offers a comprehensive suite of investment services designed to help you build wealth and achieve your financial goals. Our platform combines cutting-edge technology with proven investment strategies.</p>
                    
                    <div class="services-grid">
                        <div class="service-card">
                            <div class="service-icon">📈</div>
                            <h3>Portfolio Management</h3>
                            <p>Professional portfolio management with diversified investment strategies tailored to your risk tolerance and financial goals.</p>
                            <ul>
                                <li>Custom investment portfolios</li>
                                <li>Risk assessment and management</li>
                                <li>Regular portfolio rebalancing</li>
                                <li>Performance tracking and reporting</li>
                            </ul>
                        </div>
                        
                        <div class="service-card">
                            <div class="service-icon">🏠</div>
                            <h3>Real Estate Investment</h3>
                            <p>Access to premium real estate investment opportunities with fractional ownership and passive income potential.</p>
                            <ul>
                                <li>Residential and commercial properties</li>
                                <li>Fractional ownership options</li>
                                <li>Rental income distribution</li>
                                <li>Property appreciation benefits</li>
                            </ul>
                        </div>
                        
                        <div class="service-card">
                            <div class="service-icon">💎</div>
                            <h3>Alternative Investments</h3>
                            <p>Diversify your portfolio with alternative investments including precious metals, commodities, and private equity.</p>
                            <ul>
                                <li>Gold and silver investments</li>
                                <li>Commodity trading</li>
                                <li>Private equity opportunities</li>
                                <li>Hedge fund access</li>
                            </ul>
                        </div>
                        
                        <div class="service-card">
                            <div class="service-icon">🎓</div>
                            <h3>Financial Education</h3>
                            <p>Comprehensive financial education programs to help you make informed investment decisions.</p>
                            <ul>
                                <li>Investment fundamentals</li>
                                <li>Market analysis training</li>
                                <li>Risk management education</li>
                                <li>Wealth building strategies</li>
                            </ul>
                        </div>
                        
                        <div class="service-card">
                            <div class="service-icon">🔒</div>
                            <h3>Secure Trading Platform</h3>
                            <p>Advanced trading platform with real-time market data, secure transactions, and mobile accessibility.</p>
                            <ul>
                                <li>Real-time market data</li>
                                <li>Secure transaction processing</li>
                                <li>Mobile trading app</li>
                                <li>24/7 customer support</li>
                            </ul>
                        </div>
                        
                        <div class="service-card">
                            <div class="service-icon">📊</div>
                            <h3>Investment Analytics</h3>
                            <p>Advanced analytics and reporting tools to track your investment performance and optimize your strategy.</p>
                            <ul>
                                <li>Performance tracking</li>
                                <li>Portfolio analytics</li>
                                <li>Risk assessment tools</li>
                                <li>Investment recommendations</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="service-cta">
                        <h3>Ready to Start Investing?</h3>
                        <p>Join thousands of investors who trust BlackCnote with their financial future.</p>
                        <a href="<?php echo esc_url(home_url('/investment-plans')); ?>" class="btn btn-primary">View Investment Plans</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?> 
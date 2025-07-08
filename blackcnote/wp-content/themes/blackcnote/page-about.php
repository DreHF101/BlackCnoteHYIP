<?php
/**
 * Template Name: About Page
 * The about page template
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
                    <h2>About BlackCnote</h2>
                    <p>BlackCnote is a revolutionary investment platform designed to empower Black communities through strategic financial opportunities. Our mission is to bridge the wealth gap by providing access to high-quality investment products and financial education.</p>
                    
                    <div class="about-features">
                        <div class="feature">
                            <h3>Our Mission</h3>
                            <p>To democratize access to wealth-building opportunities and create generational wealth for Black communities.</p>
                        </div>
                        
                        <div class="feature">
                            <h3>Our Vision</h3>
                            <p>A world where financial prosperity is accessible to all, regardless of background or starting point.</p>
                        </div>
                        
                        <div class="feature">
                            <h3>Our Values</h3>
                            <ul>
                                <li>Transparency in all our operations</li>
                                <li>Community-focused investment strategies</li>
                                <li>Education and empowerment</li>
                                <li>Innovation in financial technology</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="about-stats">
                        <h3>Our Impact</h3>
                        <div class="stats-grid">
                            <div class="stat">
                                <span class="stat-number">$<?php echo esc_html(get_option('blackcnote_stat_total_invested', '2500000')); ?></span>
                                <span class="stat-label">Total Invested</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number"><?php echo esc_html(get_option('blackcnote_stat_active_investors', '1200')); ?>+</span>
                                <span class="stat-label">Active Investors</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number"><?php echo esc_html(get_option('blackcnote_stat_success_rate', '98.5')); ?>%</span>
                                <span class="stat-label">Success Rate</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number"><?php echo esc_html(get_option('blackcnote_stat_years_experience', '5')); ?>+</span>
                                <span class="stat-label">Years Experience</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?> 
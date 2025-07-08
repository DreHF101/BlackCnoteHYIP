<?php
/**
 * Template Name: Contact Page
 * The contact page template
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
                    <div class="contact-intro">
                        <h2>Get in Touch</h2>
                        <p>Have questions about our investment services? We're here to help. Contact our team for personalized assistance and expert guidance.</p>
                    </div>
                    
                    <div class="contact-grid">
                        <div class="contact-form-section">
                            <h3>Send us a Message</h3>
                            <form class="contact-form" method="post" action="">
                                <?php wp_nonce_field('blackcnote_contact_form', 'contact_nonce'); ?>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="contact_name">Full Name *</label>
                                        <input type="text" id="contact_name" name="contact_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="contact_email">Email Address *</label>
                                        <input type="email" id="contact_email" name="contact_email" required>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="contact_phone">Phone Number</label>
                                        <input type="tel" id="contact_phone" name="contact_phone">
                                    </div>
                                    <div class="form-group">
                                        <label for="contact_subject">Subject *</label>
                                        <select id="contact_subject" name="contact_subject" required>
                                            <option value="">Select a subject</option>
                                            <option value="investment-inquiry">Investment Inquiry</option>
                                            <option value="account-support">Account Support</option>
                                            <option value="technical-support">Technical Support</option>
                                            <option value="partnership">Partnership Opportunity</option>
                                            <option value="general">General Question</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="contact_message">Message *</label>
                                    <textarea id="contact_message" name="contact_message" rows="6" required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="contact_newsletter" value="1">
                                        Subscribe to our newsletter for investment updates and market insights
                                    </label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Send Message</button>
                            </form>
                        </div>
                        
                        <div class="contact-info-section">
                            <h3>Contact Information</h3>
                            
                            <div class="contact-info">
                                <div class="contact-item">
                                    <div class="contact-icon">📧</div>
                                    <div class="contact-details">
                                        <h4>Email</h4>
                                        <p><a href="mailto:info@blackcnote.com">info@blackcnote.com</a></p>
                                        <p><a href="mailto:support@blackcnote.com">support@blackcnote.com</a></p>
                                    </div>
                                </div>
                                
                                <div class="contact-item">
                                    <div class="contact-icon">📞</div>
                                    <div class="contact-details">
                                        <h4>Phone</h4>
                                        <p><a href="tel:+1234567890">+1 (234) 567-890</a></p>
                                        <p>Monday - Friday: 9:00 AM - 6:00 PM EST</p>
                                    </div>
                                </div>
                                
                                <div class="contact-item">
                                    <div class="contact-icon">📍</div>
                                    <div class="contact-details">
                                        <h4>Address</h4>
                                        <p>123 Investment Street<br>
                                        Financial District<br>
                                        New York, NY 10001<br>
                                        United States</p>
                                    </div>
                                </div>
                                
                                <div class="contact-item">
                                    <div class="contact-icon">💬</div>
                                    <div class="contact-details">
                                        <h4>Live Chat</h4>
                                        <p>Available 24/7 for urgent inquiries</p>
                                        <button class="btn btn-secondary">Start Chat</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="social-links">
                                <h4>Follow Us</h4>
                                <div class="social-icons">
                                    <a href="#" class="social-icon" title="Facebook">📘</a>
                                    <a href="#" class="social-icon" title="Twitter">🐦</a>
                                    <a href="#" class="social-icon" title="LinkedIn">💼</a>
                                    <a href="#" class="social-icon" title="Instagram">📷</a>
                                    <a href="#" class="social-icon" title="YouTube">📺</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="faq-section">
                        <h3>Frequently Asked Questions</h3>
                        <div class="faq-grid">
                            <div class="faq-item">
                                <h4>How do I get started with investing?</h4>
                                <p>Getting started is easy! Simply create an account, complete your profile, and choose from our range of investment plans. Our team will guide you through the process.</p>
                            </div>
                            
                            <div class="faq-item">
                                <h4>What are the minimum investment amounts?</h4>
                                <p>We offer flexible investment options starting from as little as $100. Different plans have different minimum requirements based on the investment strategy.</p>
                            </div>
                            
                            <div class="faq-item">
                                <h4>How secure is my investment?</h4>
                                <p>Your investments are protected by industry-leading security measures, including encryption, secure servers, and regulatory compliance. We prioritize the safety of your funds.</p>
                            </div>
                            
                            <div class="faq-item">
                                <h4>Can I withdraw my funds anytime?</h4>
                                <p>Withdrawal policies vary by investment plan. Some plans offer flexible withdrawals, while others have specific terms. Check the plan details for specific information.</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?> 
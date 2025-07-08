<?php
/**
 * Template Name: Terms of Service Page
 * The terms of service page template
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
                    <div class="terms-intro">
                        <p><strong>Last updated:</strong> <?php echo date('F j, Y'); ?></p>
                        <p>These Terms of Service ("Terms") govern your use of the BlackCnote investment platform and services. By accessing or using our platform, you agree to be bound by these Terms and all applicable laws and regulations.</p>
                    </div>
                    
                    <div class="terms-sections">
                        <section class="terms-section">
                            <h2>1. Acceptance of Terms</h2>
                            <p>By accessing or using the BlackCnote platform, you acknowledge that you have read, understood, and agree to be bound by these Terms. If you do not agree to these Terms, you must not use our services.</p>
                        </section>
                        
                        <section class="terms-section">
                            <h2>2. Eligibility</h2>
                            <p>To use our services, you must:</p>
                            <ul>
                                <li>Be at least 18 years old</li>
                                <li>Have the legal capacity to enter into binding agreements</li>
                                <li>Reside in a jurisdiction where our services are available</li>
                                <li>Comply with all applicable laws and regulations</li>
                                <li>Provide accurate and complete information</li>
                            </ul>
                        </section>
                        
                        <section class="terms-section">
                            <h2>3. Account Registration and Security</h2>
                            <p>To access certain features, you must create an account. You agree to:</p>
                            <ul>
                                <li>Provide accurate, current, and complete information</li>
                                <li>Maintain and update your account information</li>
                                <li>Keep your login credentials secure and confidential</li>
                                <li>Notify us immediately of any unauthorized access</li>
                                <li>Accept responsibility for all activities under your account</li>
                            </ul>
                        </section>
                        
                        <section class="terms-section">
                            <h2>4. Investment Services</h2>
                            <p>BlackCnote provides investment services including:</p>
                            <ul>
                                <li>Portfolio management and investment advisory services</li>
                                <li>Access to various investment products and opportunities</li>
                                <li>Real-time market data and analytics</li>
                                <li>Educational resources and financial planning tools</li>
                                <li>Customer support and account management</li>
                            </ul>
                            <p><strong>Risk Disclosure:</strong> All investments carry risk, including the potential loss of principal. Past performance does not guarantee future results.</p>
                        </section>
                        
                        <section class="terms-section">
                            <h2>5. Fees and Charges</h2>
                            <p>We may charge fees for our services, including:</p>
                            <ul>
                                <li>Account maintenance fees</li>
                                <li>Transaction fees</li>
                                <li>Management fees</li>
                                <li>Withdrawal fees</li>
                                <li>Other service-related charges</li>
                            </ul>
                            <p>All fees are disclosed in our fee schedule and may be updated with notice.</p>
                        </section>
                        
                        <section class="terms-section">
                            <h2>6. Prohibited Activities</h2>
                            <p>You agree not to:</p>
                            <ul>
                                <li>Use our services for illegal or unauthorized purposes</li>
                                <li>Attempt to gain unauthorized access to our systems</li>
                                <li>Interfere with or disrupt our services</li>
                                <li>Provide false or misleading information</li>
                                <li>Engage in market manipulation or insider trading</li>
                                <li>Violate any applicable laws or regulations</li>
                                <li>Use our services to harm others or their property</li>
                            </ul>
                        </section>
                        
                        <section class="terms-section">
                            <h2>7. Intellectual Property</h2>
                            <p>Our platform and content are protected by intellectual property laws. You may not:</p>
                            <ul>
                                <li>Copy, modify, or distribute our content without permission</li>
                                <li>Reverse engineer our software or systems</li>
                                <li>Use our trademarks or branding without authorization</li>
                                <li>Remove or alter copyright notices</li>
                            </ul>
                        </section>
                        
                        <section class="terms-section">
                            <h2>8. Privacy and Data Protection</h2>
                            <p>Your privacy is important to us. Our collection and use of your information is governed by our Privacy Policy, which is incorporated into these Terms by reference.</p>
                        </section>
                        
                        <section class="terms-section">
                            <h2>9. Disclaimers and Limitations</h2>
                            <p><strong>Service Availability:</strong> We strive to provide reliable services but cannot guarantee uninterrupted access.</p>
                            <p><strong>Investment Advice:</strong> Information provided is for educational purposes and does not constitute investment advice.</p>
                            <p><strong>Third-Party Content:</strong> We are not responsible for third-party content or services.</p>
                            <p><strong>Limitation of Liability:</strong> Our liability is limited to the extent permitted by law.</p>
                        </section>
                        
                        <section class="terms-section">
                            <h2>10. Indemnification</h2>
                            <p>You agree to indemnify and hold harmless BlackCnote and its affiliates from any claims, damages, or expenses arising from your use of our services or violation of these Terms.</p>
                        </section>
                        
                        <section class="terms-section">
                            <h2>11. Termination</h2>
                            <p>We may terminate or suspend your account at any time for violation of these Terms. You may terminate your account by contacting us. Upon termination:</p>
                            <ul>
                                <li>Your access to our services will cease</li>
                                <li>We will process any pending transactions</li>
                                <li>We will retain records as required by law</li>
                                <li>Certain provisions will survive termination</li>
                            </ul>
                        </section>
                        
                        <section class="terms-section">
                            <h2>12. Governing Law and Disputes</h2>
                            <p>These Terms are governed by the laws of the jurisdiction where BlackCnote is incorporated. Any disputes will be resolved through binding arbitration, except for claims that may be brought in small claims court.</p>
                        </section>
                        
                        <section class="terms-section">
                            <h2>13. Changes to Terms</h2>
                            <p>We may update these Terms from time to time. We will notify you of material changes by posting the updated Terms on our platform. Your continued use after changes constitutes acceptance of the updated Terms.</p>
                        </section>
                        
                        <section class="terms-section">
                            <h2>14. Severability</h2>
                            <p>If any provision of these Terms is found to be unenforceable, the remaining provisions will remain in full force and effect.</p>
                        </section>
                        
                        <section class="terms-section">
                            <h2>15. Entire Agreement</h2>
                            <p>These Terms, together with our Privacy Policy and other policies, constitute the entire agreement between you and BlackCnote regarding our services.</p>
                        </section>
                        
                        <section class="terms-section">
                            <h2>16. Contact Information</h2>
                            <p>If you have questions about these Terms, please contact us:</p>
                            <div class="contact-info">
                                <p><strong>Email:</strong> <a href="mailto:legal@blackcnote.com">legal@blackcnote.com</a></p>
                                <p><strong>Phone:</strong> <a href="tel:+1234567890">+1 (234) 567-890</a></p>
                                <p><strong>Address:</strong> 123 Investment Street, Financial District, New York, NY 10001</p>
                            </div>
                        </section>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?> 
<?php
/**
 * Template Name: Privacy Policy Page
 * The privacy policy page template
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
                    <div class="privacy-intro">
                        <p><strong>Last updated:</strong> <?php echo date('F j, Y'); ?></p>
                        <p>At BlackCnote, we are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our investment platform and services.</p>
                    </div>
                    
                    <div class="privacy-sections">
                        <section class="privacy-section">
                            <h2>Information We Collect</h2>
                            <h3>Personal Information</h3>
                            <p>We collect personal information that you provide directly to us, including:</p>
                            <ul>
                                <li>Name, email address, and phone number</li>
                                <li>Date of birth and social security number</li>
                                <li>Financial information and investment preferences</li>
                                <li>Government-issued identification documents</li>
                                <li>Bank account and payment information</li>
                            </ul>
                            
                            <h3>Automatically Collected Information</h3>
                            <p>We automatically collect certain information when you use our platform:</p>
                            <ul>
                                <li>IP address and device information</li>
                                <li>Browser type and operating system</li>
                                <li>Usage patterns and preferences</li>
                                <li>Cookies and similar tracking technologies</li>
                            </ul>
                        </section>
                        
                        <section class="privacy-section">
                            <h2>How We Use Your Information</h2>
                            <p>We use the information we collect to:</p>
                            <ul>
                                <li>Provide and maintain our investment services</li>
                                <li>Process transactions and manage your account</li>
                                <li>Verify your identity and comply with regulations</li>
                                <li>Communicate with you about your investments</li>
                                <li>Improve our platform and develop new features</li>
                                <li>Detect and prevent fraud and security threats</li>
                                <li>Comply with legal and regulatory requirements</li>
                            </ul>
                        </section>
                        
                        <section class="privacy-section">
                            <h2>Information Sharing and Disclosure</h2>
                            <p>We do not sell, trade, or rent your personal information to third parties. We may share your information in the following circumstances:</p>
                            <ul>
                                <li><strong>Service Providers:</strong> With trusted third-party service providers who assist us in operating our platform</li>
                                <li><strong>Legal Requirements:</strong> When required by law or to protect our rights and safety</li>
                                <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets</li>
                                <li><strong>Consent:</strong> With your explicit consent for specific purposes</li>
                            </ul>
                        </section>
                        
                        <section class="privacy-section">
                            <h2>Data Security</h2>
                            <p>We implement industry-standard security measures to protect your personal information:</p>
                            <ul>
                                <li>Encryption of sensitive data in transit and at rest</li>
                                <li>Multi-factor authentication for account access</li>
                                <li>Regular security audits and vulnerability assessments</li>
                                <li>Employee training on data protection practices</li>
                                <li>Secure data centers with physical and digital safeguards</li>
                            </ul>
                        </section>
                        
                        <section class="privacy-section">
                            <h2>Your Rights and Choices</h2>
                            <p>You have the following rights regarding your personal information:</p>
                            <ul>
                                <li><strong>Access:</strong> Request access to your personal information</li>
                                <li><strong>Correction:</strong> Request correction of inaccurate information</li>
                                <li><strong>Deletion:</strong> Request deletion of your personal information</li>
                                <li><strong>Portability:</strong> Request a copy of your data in a portable format</li>
                                <li><strong>Opt-out:</strong> Opt out of marketing communications</li>
                                <li><strong>Cookies:</strong> Manage cookie preferences through your browser settings</li>
                            </ul>
                        </section>
                        
                        <section class="privacy-section">
                            <h2>Cookies and Tracking Technologies</h2>
                            <p>We use cookies and similar technologies to enhance your experience:</p>
                            <ul>
                                <li><strong>Essential Cookies:</strong> Required for basic platform functionality</li>
                                <li><strong>Analytics Cookies:</strong> Help us understand how you use our platform</li>
                                <li><strong>Marketing Cookies:</strong> Used for targeted advertising (with your consent)</li>
                                <li><strong>Preference Cookies:</strong> Remember your settings and preferences</li>
                            </ul>
                        </section>
                        
                        <section class="privacy-section">
                            <h2>Children's Privacy</h2>
                            <p>Our services are not intended for individuals under the age of 18. We do not knowingly collect personal information from children. If you believe we have collected information from a child, please contact us immediately.</p>
                        </section>
                        
                        <section class="privacy-section">
                            <h2>International Data Transfers</h2>
                            <p>Your information may be transferred to and processed in countries other than your own. We ensure appropriate safeguards are in place to protect your information in accordance with this Privacy Policy.</p>
                        </section>
                        
                        <section class="privacy-section">
                            <h2>Changes to This Privacy Policy</h2>
                            <p>We may update this Privacy Policy from time to time. We will notify you of any material changes by posting the new Privacy Policy on our platform and updating the "Last updated" date. Your continued use of our services after such changes constitutes acceptance of the updated Privacy Policy.</p>
                        </section>
                        
                        <section class="privacy-section">
                            <h2>Contact Us</h2>
                            <p>If you have any questions about this Privacy Policy or our privacy practices, please contact us:</p>
                            <div class="contact-info">
                                <p><strong>Email:</strong> <a href="mailto:privacy@blackcnote.com">privacy@blackcnote.com</a></p>
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
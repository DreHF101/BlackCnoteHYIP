<?php
/**
 * Template Name: BlackCnote Dashboard
 * Template Post Type: page
 *
 * @package BlackCnote_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

// If BlackCnoteLab plugin is not active, show demo content
if (!function_exists('blackcnotelab_system_instance')) {
    echo '<div class="alert alert-info">Demo: BlackCnoteLab plugin is not active. Showing sample dashboard content.</div>';
    ?>
    <main id="primary" class="site-main">
        <div class="container">
            <div class="blackcnote-dashboard">
                <div class="row">
                    <!-- User Overview -->
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h3 class="card-title">Account Overview</h3>
                                <div class="user-info">
                                    <p><strong>Username:</strong> johndoe</p>
                                    <p><strong>Email:</strong> johndoe@example.com</p>
                                    <p><strong>Member Since:</strong> January 1, 2022</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Investment Summary -->
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h3 class="card-title">Investment Summary</h3>
                                <ul>
                                    <li>Total Invested: $1,000</li>
                                    <li>Active Plans: 2</li>
                                    <li>Total Earnings: $150</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php
    get_footer();
    return;
}

// Check if BlackCnoteLab plugin is active
if (!function_exists('blackcnotelab_system_instance')) {
    wp_die(esc_html__('BlackCnoteLab plugin is required for this page.', 'blackcnote-theme'));
}
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="blackcnote-dashboard">
            <div class="row">
                <!-- User Overview -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="card-title"><?php esc_html_e('Account Overview', 'blackcnote-theme'); ?></h3>
                            <?php
                            $user = wp_get_current_user();
                            $user_meta = get_user_meta($user->ID);
                            ?>
                            <div class="user-info">
                                <p><strong><?php esc_html_e('Username:', 'blackcnote-theme'); ?></strong> <?php echo esc_html($user->user_login); ?></p>
                                <p><strong><?php esc_html_e('Email:', 'blackcnote-theme'); ?></strong> <?php echo esc_html($user->user_email); ?></p>
                                <p><strong><?php esc_html_e('Member Since:', 'blackcnote-theme'); ?></strong> <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($user->user_registered))); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Investment Summary -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="card-title"><?php esc_html_e('Investment Summary', 'blackcnote-theme'); ?></h3>
                            <?php
                            global $wpdb;
                            try {
                                $user_id = get_current_user_id();
                                $investments = $wpdb->get_results($wpdb->prepare(
                                    "SELECT * FROM {$wpdb->prefix}blackcnotelab_investments 
                                    WHERE user_id = %d 
                                    ORDER BY created_at DESC 
                                    LIMIT 5",
                                    $user_id
                                ));

                                if ($investments) :
                                    ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th><?php esc_html_e('Plan', 'blackcnote-theme'); ?></th>
                                                    <th><?php esc_html_e('Amount', 'blackcnote-theme'); ?></th>
                                                    <th><?php esc_html_e('Return', 'blackcnote-theme'); ?></th>
                                                    <th><?php esc_html_e('Status', 'blackcnote-theme'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($investments as $investment) : ?>
                                                    <tr>
                                                        <td><?php echo esc_html($investment->plan_name); ?></td>
                                                        <td><?php echo esc_html(number_format($investment->amount, 2)); ?></td>
                                                        <td><?php echo esc_html(number_format($investment->return_amount, 2)); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo esc_attr($investment->status === 'active' ? 'success' : 'secondary'); ?>">
                                                                <?php echo esc_html(ucfirst($investment->status)); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else : ?>
                                    <p class="text-muted"><?php esc_html_e('No investments found.', 'blackcnote-theme'); ?></p>
                                <?php endif;
                            } catch (Exception $e) {
                                error_log('BlackCnote Theme Error: ' . $e->getMessage());
                                echo '<p class="error">' . esc_html__('Error loading investment data.', 'blackcnote-theme') . '</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title"><?php esc_html_e('Quick Actions', 'blackcnote-theme'); ?></h3>
                            <div class="row">
                                <div class="col-md-4">
                                    <a href="<?php echo esc_url(home_url('/invest')); ?>" class="btn btn-primary btn-lg w-100 mb-3">
                                        <?php esc_html_e('New Investment', 'blackcnote-theme'); ?>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="<?php echo esc_url(home_url('/withdraw')); ?>" class="btn btn-success btn-lg w-100 mb-3">
                                        <?php esc_html_e('Withdraw Funds', 'blackcnote-theme'); ?>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="<?php echo esc_url(home_url('/profile')); ?>" class="btn btn-info btn-lg w-100 mb-3">
                                        <?php esc_html_e('Update Profile', 'blackcnote-theme'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BlackCnoteLab Dashboard Shortcode -->
            <div class="row mt-4">
                <div class="col-12">
                    <?php echo do_shortcode('[blackcnotelab_dashboard]'); ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
get_footer(); 
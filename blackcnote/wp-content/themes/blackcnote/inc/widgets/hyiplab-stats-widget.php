<?php
/**
 * HYIPLab Stats Widget
 * 
 * Displays investment statistics in the sidebar
 */

if (!defined('ABSPATH')) {
    exit;
}

class BlackCnote_HYIPLab_Stats_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'hyiplab_stats_widget',
            __('HYIPLab - Investment Stats', 'blackcnote'),
            [
                'description' => __('Display investment statistics in the sidebar', 'blackcnote'),
                'classname' => 'widget-hyiplab-stats'
            ]
        );
    }
    
    public function widget($args, $instance) {
        if (!function_exists('hyiplab_system_instance')) {
            return;
        }
        
        $title = !empty($instance['title']) ? $instance['title'] : __('Investment Stats', 'blackcnote');
        $show_balance = !empty($instance['show_balance']);
        $show_investments = !empty($instance['show_investments']);
        $show_profit = !empty($instance['show_profit']);
        
        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }
        
        global $wpdb;
        $investments_table = $wpdb->prefix . 'hyiplab_investments';
        $transactions_table = $wpdb->prefix . 'hyiplab_transactions';
        
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            
            // Get user statistics
            $total_invested = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT SUM(amount) FROM $investments_table WHERE user_id = %d AND status = 'active'",
                    $user_id
                )
            );
            
            $total_profit = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT SUM(total_profit) FROM $investments_table WHERE user_id = %d AND status = 'active'",
                    $user_id
                )
            );
            
            $active_investments = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM $investments_table WHERE user_id = %d AND status = 'active'",
                    $user_id
                )
            );
            
            // Get recent balance from transactions
            $recent_balance = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT SUM(CASE WHEN type = 'deposit' THEN amount ELSE -amount END) 
                     FROM $transactions_table 
                     WHERE user_id = %d AND status = 'completed'",
                    $user_id
                )
            );
            
            echo '<div class="hyiplab-stats-widget">';
            
            if ($show_balance) {
                echo '<div class="stat-item">';
                echo '<div class="stat-label">' . __('Available Balance', 'blackcnote') . '</div>';
                echo '<div class="stat-value">$' . number_format($recent_balance ?: 0, 2) . '</div>';
                echo '</div>';
            }
            
            if ($show_investments) {
                echo '<div class="stat-item">';
                echo '<div class="stat-label">' . __('Total Invested', 'blackcnote') . '</div>';
                echo '<div class="stat-value">$' . number_format($total_invested ?: 0, 2) . '</div>';
                echo '</div>';
                
                echo '<div class="stat-item">';
                echo '<div class="stat-label">' . __('Active Investments', 'blackcnote') . '</div>';
                echo '<div class="stat-value">' . number_format($active_investments ?: 0) . '</div>';
                echo '</div>';
            }
            
            if ($show_profit) {
                echo '<div class="stat-item">';
                echo '<div class="stat-label">' . __('Total Profit', 'blackcnote') . '</div>';
                echo '<div class="stat-value profit">$' . number_format($total_profit ?: 0, 2) . '</div>';
                echo '</div>';
            }
            
            echo '</div>';
            
            echo '<div class="widget-footer">';
            echo '<a href="' . get_permalink(get_page_by_path('dashboard')->ID) . '" class="view-dashboard">';
            echo __('View Dashboard', 'blackcnote');
            echo '</a>';
            echo '</div>';
            
        } else {
            // Show general statistics for non-logged-in users
            $total_plans = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hyiplab_plans WHERE status = 'active'");
            $total_investments = $wpdb->get_var("SELECT COUNT(*) FROM $investments_table WHERE status = 'active'");
            $total_profit_paid = $wpdb->get_var("SELECT SUM(amount) FROM $transactions_table WHERE type = 'profit' AND status = 'completed'");
            
            echo '<div class="hyiplab-stats-widget">';
            
            echo '<div class="stat-item">';
            echo '<div class="stat-label">' . __('Active Plans', 'blackcnote') . '</div>';
            echo '<div class="stat-value">' . number_format($total_plans ?: 0) . '</div>';
            echo '</div>';
            
            echo '<div class="stat-item">';
            echo '<div class="stat-label">' . __('Total Investments', 'blackcnote') . '</div>';
            echo '<div class="stat-value">' . number_format($total_investments ?: 0) . '</div>';
            echo '</div>';
            
            echo '<div class="stat-item">';
            echo '<div class="stat-label">' . __('Profits Paid', 'blackcnote') . '</div>';
            echo '<div class="stat-value">$' . number_format($total_profit_paid ?: 0, 0) . '</div>';
            echo '</div>';
            
            echo '</div>';
            
            echo '<div class="widget-footer">';
            echo '<a href="' . wp_login_url(get_permalink()) . '" class="login-link">';
            echo __('Login to Invest', 'blackcnote');
            echo '</a>';
            echo '</div>';
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Investment Stats', 'blackcnote');
        $show_balance = !empty($instance['show_balance']);
        $show_investments = !empty($instance['show_investments']);
        $show_profit = !empty($instance['show_profit']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'blackcnote'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" 
                   name="<?php echo $this->get_field_name('title'); ?>" type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <input class="checkbox" id="<?php echo $this->get_field_id('show_balance'); ?>" 
                   name="<?php echo $this->get_field_name('show_balance'); ?>" type="checkbox" 
                   <?php checked($show_balance); ?>>
            <label for="<?php echo $this->get_field_id('show_balance'); ?>">
                <?php _e('Show available balance', 'blackcnote'); ?>
            </label>
        </p>
        <p>
            <input class="checkbox" id="<?php echo $this->get_field_id('show_investments'); ?>" 
                   name="<?php echo $this->get_field_name('show_investments'); ?>" type="checkbox" 
                   <?php checked($show_investments); ?>>
            <label for="<?php echo $this->get_field_id('show_investments'); ?>">
                <?php _e('Show investment amounts', 'blackcnote'); ?>
            </label>
        </p>
        <p>
            <input class="checkbox" id="<?php echo $this->get_field_id('show_profit'); ?>" 
                   name="<?php echo $this->get_field_name('show_profit'); ?>" type="checkbox" 
                   <?php checked($show_profit); ?>>
            <label for="<?php echo $this->get_field_id('show_profit'); ?>">
                <?php _e('Show profit amounts', 'blackcnote'); ?>
            </label>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = !empty($new_instance['title']) ? strip_tags($new_instance['title']) : '';
        $instance['show_balance'] = !empty($new_instance['show_balance']);
        $instance['show_investments'] = !empty($new_instance['show_investments']);
        $instance['show_profit'] = !empty($new_instance['show_profit']);
        return $instance;
    }
} 
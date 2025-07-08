<?php
/**
 * HYIPLab Transactions Widget
 * 
 * Displays recent transactions in the sidebar
 */

if (!defined('ABSPATH')) {
    exit;
}

class BlackCnote_HYIPLab_Transactions_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'hyiplab_transactions_widget',
            __('HYIPLab - Recent Transactions', 'blackcnote'),
            [
                'description' => __('Display recent transactions in the sidebar', 'blackcnote'),
                'classname' => 'widget-hyiplab-transactions'
            ]
        );
    }
    
    public function widget($args, $instance) {
        if (!function_exists('hyiplab_system_instance')) {
            return;
        }
        
        $title = !empty($instance['title']) ? $instance['title'] : __('Recent Transactions', 'blackcnote');
        $show_count = !empty($instance['show_count']) ? $instance['show_count'] : 5;
        $show_amount = !empty($instance['show_amount']);
        $show_status = !empty($instance['show_status']);
        
        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }
        
        global $wpdb;
        $transactions_table = $wpdb->prefix . 'hyiplab_transactions';
        
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            
            $transactions = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $transactions_table WHERE user_id = %d ORDER BY created_at DESC LIMIT %d",
                    $user_id,
                    $show_count
                )
            );
        } else {
            // Show recent transactions for all users (public view)
            $transactions = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $transactions_table WHERE status = 'completed' ORDER BY created_at DESC LIMIT %d",
                    $show_count
                )
            );
        }
        
        if (!empty($transactions)) {
            echo '<div class="hyiplab-transactions-widget">';
            foreach ($transactions as $transaction) {
                ?>
                <div class="transaction-item">
                    <div class="transaction-header">
                        <div class="transaction-type <?php echo esc_attr($transaction->type); ?>">
                            <?php echo esc_html(ucfirst($transaction->type)); ?>
                        </div>
                        <?php if ($show_status): ?>
                            <div class="transaction-status <?php echo esc_attr($transaction->status); ?>">
                                <?php echo esc_html(ucfirst($transaction->status)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($show_amount): ?>
                        <div class="transaction-amount">
                            $<?php echo number_format($transaction->amount, 2); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="transaction-date">
                        <?php echo date('M j, Y', strtotime($transaction->created_at)); ?>
                    </div>
                    
                    <?php if (!empty($transaction->description)): ?>
                        <div class="transaction-description">
                            <?php echo esc_html($transaction->description); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php
            }
            echo '</div>';
            
            if (is_user_logged_in()) {
                echo '<div class="widget-footer">';
                echo '<a href="' . get_permalink(get_page_by_path('transactions')->ID) . '" class="view-all-transactions">';
                echo __('View All Transactions', 'blackcnote');
                echo '</a>';
                echo '</div>';
            }
        } else {
            echo '<p>' . __('No transactions found.', 'blackcnote') . '</p>';
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Recent Transactions', 'blackcnote');
        $show_count = !empty($instance['show_count']) ? $instance['show_count'] : 5;
        $show_amount = !empty($instance['show_amount']);
        $show_status = !empty($instance['show_status']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'blackcnote'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" 
                   name="<?php echo $this->get_field_name('title'); ?>" type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('show_count'); ?>"><?php _e('Number of transactions to show:', 'blackcnote'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('show_count'); ?>" 
                   name="<?php echo $this->get_field_name('show_count'); ?>" type="number" 
                   value="<?php echo esc_attr($show_count); ?>" min="1" max="20">
        </p>
        <p>
            <input class="checkbox" id="<?php echo $this->get_field_id('show_amount'); ?>" 
                   name="<?php echo $this->get_field_name('show_amount'); ?>" type="checkbox" 
                   <?php checked($show_amount); ?>>
            <label for="<?php echo $this->get_field_id('show_amount'); ?>">
                <?php _e('Show transaction amounts', 'blackcnote'); ?>
            </label>
        </p>
        <p>
            <input class="checkbox" id="<?php echo $this->get_field_id('show_status'); ?>" 
                   name="<?php echo $this->get_field_name('show_status'); ?>" type="checkbox" 
                   <?php checked($show_status); ?>>
            <label for="<?php echo $this->get_field_id('show_status'); ?>">
                <?php _e('Show transaction status', 'blackcnote'); ?>
            </label>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = !empty($new_instance['title']) ? strip_tags($new_instance['title']) : '';
        $instance['show_count'] = !empty($new_instance['show_count']) ? absint($new_instance['show_count']) : 5;
        $instance['show_amount'] = !empty($new_instance['show_amount']);
        $instance['show_status'] = !empty($new_instance['show_status']);
        return $instance;
    }
} 
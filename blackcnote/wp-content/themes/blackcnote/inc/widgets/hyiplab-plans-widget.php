<?php
/**
 * HYIPLab Plans Widget
 * 
 * Displays investment plans in the sidebar
 */

if (!defined('ABSPATH')) {
    exit;
}

class BlackCnote_HYIPLab_Plans_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'hyiplab_plans_widget',
            __('HYIPLab - Investment Plans', 'blackcnote'),
            [
                'description' => __('Display investment plans in the sidebar', 'blackcnote'),
                'classname' => 'widget-hyiplab-plans'
            ]
        );
    }
    
    public function widget($args, $instance) {
        if (!function_exists('hyiplab_system_instance')) {
            return;
        }
        
        $title = !empty($instance['title']) ? $instance['title'] : __('Investment Plans', 'blackcnote');
        $show_count = !empty($instance['show_count']) ? $instance['show_count'] : 3;
        $show_featured = !empty($instance['show_featured']);
        
        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'hyiplab_plans';
        
        $plans = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE status = 'active' ORDER BY min_amount ASC LIMIT %d",
                $show_count
            )
        );
        
        if (!empty($plans)) {
            echo '<div class="hyiplab-plans-widget">';
            foreach ($plans as $plan) {
                ?>
                <div class="plan-item">
                    <div class="plan-header">
                        <h4 class="plan-name"><?php echo esc_html($plan->name); ?></h4>
                        <div class="plan-rate"><?php echo esc_html($plan->profit_rate); ?>%</div>
                    </div>
                    <div class="plan-range">
                        $<?php echo number_format($plan->min_amount); ?> - $<?php echo number_format($plan->max_amount); ?>
                    </div>
                    <div class="plan-duration"><?php echo esc_html($plan->duration); ?> days</div>
                    <?php if (is_user_logged_in()): ?>
                        <a href="<?php echo get_permalink(get_page_by_path('plans')->ID); ?>?plan=<?php echo esc_attr($plan->id); ?>" class="plan-link">
                            <?php _e('Invest Now', 'blackcnote'); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <?php
            }
            echo '</div>';
            
            if (is_user_logged_in()) {
                echo '<div class="widget-footer">';
                echo '<a href="' . get_permalink(get_page_by_path('plans')->ID) . '" class="view-all-plans">';
                echo __('View All Plans', 'blackcnote');
                echo '</a>';
                echo '</div>';
            }
        } else {
            echo '<p>' . __('No investment plans available.', 'blackcnote') . '</p>';
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Investment Plans', 'blackcnote');
        $show_count = !empty($instance['show_count']) ? $instance['show_count'] : 3;
        $show_featured = !empty($instance['show_featured']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'blackcnote'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" 
                   name="<?php echo $this->get_field_name('title'); ?>" type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('show_count'); ?>"><?php _e('Number of plans to show:', 'blackcnote'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('show_count'); ?>" 
                   name="<?php echo $this->get_field_name('show_count'); ?>" type="number" 
                   value="<?php echo esc_attr($show_count); ?>" min="1" max="10">
        </p>
        <p>
            <input class="checkbox" id="<?php echo $this->get_field_id('show_featured'); ?>" 
                   name="<?php echo $this->get_field_name('show_featured'); ?>" type="checkbox" 
                   <?php checked($show_featured); ?>>
            <label for="<?php echo $this->get_field_id('show_featured'); ?>">
                <?php _e('Show featured plans only', 'blackcnote'); ?>
            </label>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = !empty($new_instance['title']) ? strip_tags($new_instance['title']) : '';
        $instance['show_count'] = !empty($new_instance['show_count']) ? absint($new_instance['show_count']) : 3;
        $instance['show_featured'] = !empty($new_instance['show_featured']);
        return $instance;
    }
} 
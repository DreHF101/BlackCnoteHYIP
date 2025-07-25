<?php
/**
 * BlackCnote Custom Widgets
 *
 * @package BlackCnote
 * @version 1.0.0
 */

declare(strict_types=1);

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * BlackCnote Investment Portfolio Widget
 */
class BlackCnote_Portfolio_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'blackcnote_portfolio_widget',
            esc_html__('BlackCnote - Investment Portfolio', 'blackcnote'),
            array(
                'description' => esc_html__('Display user investment portfolio summary', 'blackcnote'),
                'classname'   => 'widget-blackcnote-portfolio',
            )
        );
    }

    /**
     * Frontend display
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('My Portfolio', 'blackcnote');
        echo $args['before_title'] . esc_html($title) . $args['after_title'];
        
        // Get current user
        $current_user = wp_get_current_user();
        
        if ($current_user->ID) {
            // Get portfolio data (this would integrate with your investment system)
            $portfolio_data = $this->get_portfolio_data($current_user->ID);
            
            if (!empty($portfolio_data)) {
                echo '<div class="blackcnote-portfolio-summary">';
                echo '<div class="portfolio-total">';
                echo '<span class="label">' . esc_html__('Total Value:', 'blackcnote') . '</span>';
                echo '<span class="value">$' . number_format($portfolio_data['total_value'], 2) . '</span>';
                echo '</div>';
                
                echo '<div class="portfolio-change">';
                $change_class = $portfolio_data['daily_change'] >= 0 ? 'positive' : 'negative';
                echo '<span class="label">' . esc_html__('24h Change:', 'blackcnote') . '</span>';
                echo '<span class="value ' . $change_class . '">';
                echo ($portfolio_data['daily_change'] >= 0 ? '+' : '') . number_format($portfolio_data['daily_change'], 2) . '%';
                echo '</span>';
                echo '</div>';
                
                echo '<div class="portfolio-holdings">';
                echo '<span class="label">' . esc_html__('Holdings:', 'blackcnote') . '</span>';
                echo '<span class="value">' . count($portfolio_data['holdings']) . '</span>';
                echo '</div>';
                echo '</div>';
                
                // Display top holdings
                if (!empty($portfolio_data['holdings'])) {
                    echo '<div class="blackcnote-top-holdings">';
                    echo '<h4>' . esc_html__('Top Holdings', 'blackcnote') . '</h4>';
                    echo '<ul>';
                    foreach (array_slice($portfolio_data['holdings'], 0, 5) as $holding) {
                        echo '<li>';
                        echo '<span class="holding-name">' . esc_html($holding['name']) . '</span>';
                        echo '<span class="holding-value">$' . number_format($holding['value'], 2) . '</span>';
                        echo '</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                }
            } else {
                echo '<p class="no-portfolio">' . esc_html__('No portfolio data available.', 'blackcnote') . '</p>';
            }
        } else {
            echo '<p class="login-required">' . esc_html__('Please log in to view your portfolio.', 'blackcnote') . '</p>';
        }
        
        echo $args['after_widget'];
    }

    /**
     * Backend form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('My Portfolio', 'blackcnote');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Title:', 'blackcnote'); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    /**
     * Save widget settings
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }

    /**
     * Get portfolio data (placeholder - integrate with your investment system)
     */
    private function get_portfolio_data($user_id) {
        // This is a placeholder - integrate with your actual investment system
        return array(
            'total_value' => 125000.00,
            'daily_change' => 2.45,
            'holdings' => array(
                array('name' => 'Bitcoin', 'value' => 45000.00),
                array('name' => 'Ethereum', 'value' => 32000.00),
                array('name' => 'Cardano', 'value' => 18000.00),
                array('name' => 'Solana', 'value' => 15000.00),
                array('name' => 'Polkadot', 'value' => 15000.00),
            )
        );
    }
}

/**
 * BlackCnote Market Data Widget
 */
class BlackCnote_Market_Data_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'blackcnote_market_data_widget',
            esc_html__('BlackCnote - Market Data', 'blackcnote'),
            array(
                'description' => esc_html__('Display real-time market data for cryptocurrencies', 'blackcnote'),
                'classname'   => 'widget-blackcnote-market-data',
            )
        );
    }

    /**
     * Frontend display
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Market Data', 'blackcnote');
        echo $args['before_title'] . esc_html($title) . $args['after_title'];
        
        $market_data = $this->get_market_data();
        
        if (!empty($market_data)) {
            echo '<div class="blackcnote-market-data">';
            foreach ($market_data as $crypto) {
                echo '<div class="crypto-item">';
                echo '<div class="crypto-header">';
                echo '<span class="crypto-name">' . esc_html($crypto['name']) . '</span>';
                echo '<span class="crypto-symbol">' . esc_html($crypto['symbol']) . '</span>';
                echo '</div>';
                
                echo '<div class="crypto-price">';
                echo '<span class="price">$' . number_format($crypto['price'], 2) . '</span>';
                
                $change_class = $crypto['change_24h'] >= 0 ? 'positive' : 'negative';
                echo '<span class="change ' . $change_class . '">';
                echo ($crypto['change_24h'] >= 0 ? '+' : '') . number_format($crypto['change_24h'], 2) . '%';
                echo '</span>';
                echo '</div>';
                
                echo '<div class="crypto-volume">';
                echo '<span class="label">' . esc_html__('Volume:', 'blackcnote') . '</span>';
                echo '<span class="value">$' . number_format($crypto['volume_24h'] / 1000000, 1) . 'M</span>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            
            echo '<div class="market-update-time">';
            echo '<small>' . esc_html__('Last updated:', 'blackcnote') . ' ' . current_time('H:i:s') . '</small>';
            echo '</div>';
        } else {
            echo '<p class="no-data">' . esc_html__('Market data temporarily unavailable.', 'blackcnote') . '</p>';
        }
        
        echo $args['after_widget'];
    }

    /**
     * Backend form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Market Data', 'blackcnote');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Title:', 'blackcnote'); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    /**
     * Save widget settings
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }

    /**
     * Get market data (placeholder - integrate with real API)
     */
    private function get_market_data() {
        // This is a placeholder - integrate with CoinGecko, CoinMarketCap, or similar API
        return array(
            array(
                'name' => 'Bitcoin',
                'symbol' => 'BTC',
                'price' => 43250.75,
                'change_24h' => 2.34,
                'volume_24h' => 28500000000
            ),
            array(
                'name' => 'Ethereum',
                'symbol' => 'ETH',
                'price' => 2650.25,
                'change_24h' => -1.25,
                'volume_24h' => 15800000000
            ),
            array(
                'name' => 'Cardano',
                'symbol' => 'ADA',
                'price' => 0.485,
                'change_24h' => 5.67,
                'volume_24h' => 1250000000
            ),
            array(
                'name' => 'Solana',
                'symbol' => 'SOL',
                'price' => 98.45,
                'change_24h' => 3.21,
                'volume_24h' => 2100000000
            )
        );
    }
}

/**
 * BlackCnote Investment Opportunities Widget
 */
class BlackCnote_Opportunities_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'blackcnote_opportunities_widget',
            esc_html__('BlackCnote - Investment Opportunities', 'blackcnote'),
            array(
                'description' => esc_html__('Display featured investment opportunities', 'blackcnote'),
                'classname'   => 'widget-blackcnote-opportunities',
            )
        );
    }

    /**
     * Frontend display
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Featured Opportunities', 'blackcnote');
        echo $args['before_title'] . esc_html($title) . $args['after_title'];
        
        $opportunities = $this->get_investment_opportunities();
        
        if (!empty($opportunities)) {
            echo '<div class="blackcnote-opportunities">';
            foreach ($opportunities as $opportunity) {
                echo '<div class="opportunity-item">';
                echo '<div class="opportunity-header">';
                echo '<h4 class="opportunity-title">' . esc_html($opportunity['title']) . '</h4>';
                echo '<span class="opportunity-category">' . esc_html($opportunity['category']) . '</span>';
                echo '</div>';
                
                echo '<div class="opportunity-details">';
                echo '<div class="opportunity-return">';
                echo '<span class="label">' . esc_html__('Expected Return:', 'blackcnote') . '</span>';
                echo '<span class="value">' . number_format($opportunity['expected_return'], 1) . '%</span>';
                echo '</div>';
                
                echo '<div class="opportunity-duration">';
                echo '<span class="label">' . esc_html__('Duration:', 'blackcnote') . '</span>';
                echo '<span class="value">' . esc_html($opportunity['duration']) . '</span>';
                echo '</div>';
                
                echo '<div class="opportunity-minimum">';
                echo '<span class="label">' . esc_html__('Min Investment:', 'blackcnote') . '</span>';
                echo '<span class="value">$' . number_format($opportunity['minimum_investment'], 0) . '</span>';
                echo '</div>';
                echo '</div>';
                
                echo '<div class="opportunity-actions">';
                echo '<a href="' . esc_url($opportunity['link']) . '" class="button button-primary">' . esc_html__('Learn More', 'blackcnote') . '</a>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p class="no-opportunities">' . esc_html__('No investment opportunities available at this time.', 'blackcnote') . '</p>';
        }
        
        echo $args['after_widget'];
    }

    /**
     * Backend form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Featured Opportunities', 'blackcnote');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Title:', 'blackcnote'); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    /**
     * Save widget settings
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }

    /**
     * Get investment opportunities (placeholder)
     */
    private function get_investment_opportunities() {
        // This is a placeholder - integrate with your investment opportunities system
        return array(
            array(
                'title' => 'DeFi Yield Farming',
                'category' => 'DeFi',
                'expected_return' => 15.5,
                'duration' => '3 months',
                'minimum_investment' => 1000,
                'link' => '#'
            ),
            array(
                'title' => 'NFT Marketplace Investment',
                'category' => 'NFT',
                'expected_return' => 25.0,
                'duration' => '6 months',
                'minimum_investment' => 2500,
                'link' => '#'
            ),
            array(
                'title' => 'Crypto Mining Pool',
                'category' => 'Mining',
                'expected_return' => 12.0,
                'duration' => '12 months',
                'minimum_investment' => 5000,
                'link' => '#'
            )
        );
    }
}

/**
 * BlackCnote News & Updates Widget
 */
class BlackCnote_News_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'blackcnote_news_widget',
            esc_html__('BlackCnote - News & Updates', 'blackcnote'),
            array(
                'description' => esc_html__('Display latest crypto and investment news', 'blackcnote'),
                'classname'   => 'widget-blackcnote-news',
            )
        );
    }

    /**
     * Frontend display
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Latest News', 'blackcnote');
        echo $args['before_title'] . esc_html($title) . $args['after_title'];
        
        $news_items = $this->get_news_items();
        
        if (!empty($news_items)) {
            echo '<div class="blackcnote-news">';
            foreach ($news_items as $news) {
                echo '<div class="news-item">';
                echo '<div class="news-meta">';
                echo '<span class="news-date">' . esc_html($news['date']) . '</span>';
                echo '<span class="news-category">' . esc_html($news['category']) . '</span>';
                echo '</div>';
                
                echo '<h4 class="news-title">';
                echo '<a href="' . esc_url($news['link']) . '">' . esc_html($news['title']) . '</a>';
                echo '</h4>';
                
                if (!empty($news['excerpt'])) {
                    echo '<p class="news-excerpt">' . esc_html($news['excerpt']) . '</p>';
                }
                echo '</div>';
            }
            echo '</div>';
            
            echo '<div class="news-footer">';
            echo '<a href="' . esc_url(home_url('/news/')) . '" class="view-all-news">' . esc_html__('View All News', 'blackcnote') . '</a>';
            echo '</div>';
        } else {
            echo '<p class="no-news">' . esc_html__('No news available at this time.', 'blackcnote') . '</p>';
        }
        
        echo $args['after_widget'];
    }

    /**
     * Backend form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Latest News', 'blackcnote');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Title:', 'blackcnote'); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    /**
     * Save widget settings
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }

    /**
     * Get news items (placeholder)
     */
    private function get_news_items() {
        // This is a placeholder - integrate with your news system or external API
        return array(
            array(
                'title' => 'Bitcoin Reaches New All-Time High',
                'excerpt' => 'Bitcoin has surpassed its previous record, reaching new heights in the cryptocurrency market.',
                'date' => '2 hours ago',
                'category' => 'Bitcoin',
                'link' => '#'
            ),
            array(
                'title' => 'DeFi Protocol Launches New Yield Farming',
                'excerpt' => 'A new DeFi protocol introduces innovative yield farming strategies for investors.',
                'date' => '5 hours ago',
                'category' => 'DeFi',
                'link' => '#'
            ),
            array(
                'title' => 'Regulatory Updates Impact Crypto Markets',
                'excerpt' => 'Recent regulatory changes are shaping the future of cryptocurrency investments.',
                'date' => '1 day ago',
                'category' => 'Regulation',
                'link' => '#'
            )
        );
    }
}

/**
 * BlackCnote Quick Actions Widget
 */
class BlackCnote_Quick_Actions_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'blackcnote_quick_actions_widget',
            esc_html__('BlackCnote - Quick Actions', 'blackcnote'),
            array(
                'description' => esc_html__('Quick access to common investment actions', 'blackcnote'),
                'classname'   => 'widget-blackcnote-quick-actions',
            )
        );
    }

    /**
     * Frontend display
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Quick Actions', 'blackcnote');
        echo $args['before_title'] . esc_html($title) . $args['after_title'];
        
        $current_user = wp_get_current_user();
        
        if ($current_user->ID) {
            echo '<div class="blackcnote-quick-actions">';
            
            $actions = array(
                array(
                    'title' => esc_html__('Deposit Funds', 'blackcnote'),
                    'icon' => 'dashicons-money-alt',
                    'link' => home_url('/deposit/'),
                    'class' => 'action-deposit'
                ),
                array(
                    'title' => esc_html__('Withdraw Funds', 'blackcnote'),
                    'icon' => 'dashicons-download',
                    'link' => home_url('/withdraw/'),
                    'class' => 'action-withdraw'
                ),
                array(
                    'title' => esc_html__('Buy Crypto', 'blackcnote'),
                    'icon' => 'dashicons-chart-line',
                    'link' => home_url('/buy-crypto/'),
                    'class' => 'action-buy'
                ),
                array(
                    'title' => esc_html__('Sell Crypto', 'blackcnote'),
                    'icon' => 'dashicons-chart-area',
                    'link' => home_url('/sell-crypto/'),
                    'class' => 'action-sell'
                ),
                array(
                    'title' => esc_html__('Investment History', 'blackcnote'),
                    'icon' => 'dashicons-clock',
                    'link' => home_url('/history/'),
                    'class' => 'action-history'
                ),
                array(
                    'title' => esc_html__('Support', 'blackcnote'),
                    'icon' => 'dashicons-sos',
                    'link' => home_url('/support/'),
                    'class' => 'action-support'
                )
            );
            
            foreach ($actions as $action) {
                echo '<a href="' . esc_url($action['link']) . '" class="quick-action ' . esc_attr($action['class']) . '">';
                echo '<span class="action-icon dashicons ' . esc_attr($action['icon']) . '"></span>';
                echo '<span class="action-title">' . esc_html($action['title']) . '</span>';
                echo '</a>';
            }
            
            echo '</div>';
        } else {
            echo '<p class="login-required">' . esc_html__('Please log in to access quick actions.', 'blackcnote') . '</p>';
        }
        
        echo $args['after_widget'];
    }

    /**
     * Backend form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Quick Actions', 'blackcnote');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Title:', 'blackcnote'); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    /**
     * Save widget settings
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

/**
 * Register BlackCnote Widgets
 */
function blackcnote_register_widgets() {
    register_widget('BlackCnote_Portfolio_Widget');
    register_widget('BlackCnote_Market_Data_Widget');
    register_widget('BlackCnote_Opportunities_Widget');
    register_widget('BlackCnote_News_Widget');
    register_widget('BlackCnote_Quick_Actions_Widget');
}
add_action('widgets_init', 'blackcnote_register_widgets');

/**
 * Enqueue widget-specific styles and scripts
 */
function blackcnote_widget_assets() {
    if (is_active_widget(false, false, 'blackcnote_portfolio_widget') ||
        is_active_widget(false, false, 'blackcnote_market_data_widget') ||
        is_active_widget(false, false, 'blackcnote_opportunities_widget') ||
        is_active_widget(false, false, 'blackcnote_news_widget') ||
        is_active_widget(false, false, 'blackcnote_quick_actions_widget')) {
        
        wp_enqueue_style(
            'blackcnote-widgets',
            get_template_directory_uri() . '/css/widgets.css',
            array(),
            _S_VERSION
        );
        
        wp_enqueue_script(
            'blackcnote-widgets',
            get_template_directory_uri() . '/js/widgets.js',
            array('jquery'),
            _S_VERSION,
            true
        );
        
        // Localize script for AJAX
        wp_localize_script('blackcnote-widgets', 'blackcnote_widgets', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('blackcnote_widgets_nonce'),
            'refresh_interval' => 30000, // 30 seconds
        ));
    }
}
add_action('wp_enqueue_scripts', 'blackcnote_widget_assets');

/**
 * AJAX handler for widget data refresh
 */
function blackcnote_widget_ajax_refresh() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'blackcnote_widgets_nonce')) {
        wp_die('Security check failed');
    }
    
    $widget_type = sanitize_text_field($_POST['widget_type']);
    $response = array();
    
    switch ($widget_type) {
        case 'portfolio':
            $user_id = get_current_user_id();
            if ($user_id) {
                $portfolio_widget = new BlackCnote_Portfolio_Widget();
                $response['data'] = $portfolio_widget->get_portfolio_data($user_id);
            }
            break;
            
        case 'market_data':
            $market_widget = new BlackCnote_Market_Data_Widget();
            $response['data'] = $market_widget->get_market_data();
            break;
            
        case 'opportunities':
            $opportunities_widget = new BlackCnote_Opportunities_Widget();
            $response['data'] = $opportunities_widget->get_investment_opportunities();
            break;
            
        case 'news':
            $news_widget = new BlackCnote_News_Widget();
            $response['data'] = $news_widget->get_news_items();
            break;
    }
    
    wp_send_json_success($response);
}
add_action('wp_ajax_blackcnote_widget_refresh', 'blackcnote_widget_ajax_refresh');
add_action('wp_ajax_nopriv_blackcnote_widget_refresh', 'blackcnote_widget_ajax_refresh');

/**
 * Add widget-specific body classes
 */
function blackcnote_widget_body_classes($classes) {
    if (is_active_widget(false, false, 'blackcnote_portfolio_widget')) {
        $classes[] = 'has-portfolio-widget';
    }
    if (is_active_widget(false, false, 'blackcnote_market_data_widget')) {
        $classes[] = 'has-market-data-widget';
    }
    if (is_active_widget(false, false, 'blackcnote_opportunities_widget')) {
        $classes[] = 'has-opportunities-widget';
    }
    if (is_active_widget(false, false, 'blackcnote_news_widget')) {
        $classes[] = 'has-news-widget';
    }
    if (is_active_widget(false, false, 'blackcnote_quick_actions_widget')) {
        $classes[] = 'has-quick-actions-widget';
    }
    
    return $classes;
}
add_filter('body_class', 'blackcnote_widget_body_classes');

/**
 * Widget initialization hook for additional setup
 */
function blackcnote_widgets_init_hook() {
    // Add any additional widget initialization here
    do_action('blackcnote_widgets_initialized');
}
add_action('widgets_init', 'blackcnote_widgets_init_hook', 20);

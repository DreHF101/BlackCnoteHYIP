<?php
/**
 * The front page template file
 *
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<div class="container main-content">
    <?php
    // Try to render Elementor template if available
    $elementor_template_id = get_option('blackcnote_elementor_home_id');
    if (function_exists('elementor_theme_do_location') && elementor_theme_do_location('front_page')) {
        // Elementor Pro theme builder handles this
    } elseif ($elementor_template_id && class_exists('Elementor\Plugin')) {
        echo do_shortcode('[elementor-template id="' . esc_attr($elementor_template_id) . '"]');
    } elseif (has_shortcode(get_post_field('post_content', get_the_ID()), 'elementor-template')) {
        echo do_shortcode(get_post_field('post_content', get_the_ID()));
    } elseif (have_posts()) :
        while (have_posts()) : the_post();
            the_content();
        endwhile;
    else :
        echo '<div class="theme-fallback"><h2>Welcome to BlackCnote!</h2><p>This is your homepage. Add content with Elementor, the WordPress editor, or import demo content for a full experience.</p></div>';
    endif;
    ?>
</div>
<?php get_footer(); ?> 
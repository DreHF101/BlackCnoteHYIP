<?php
/**
 * The main template file
 *
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="primary" class="site-main">
    <?php if (is_front_page()) : ?>
        <?php get_template_part('template-parts/home-hero'); ?>
        <?php get_template_part('template-parts/home-stats'); ?>
        <?php get_template_part('template-parts/home-features'); ?>
        <?php get_template_part('template-parts/home-plans'); ?>
        <?php get_template_part('template-parts/home-cta'); ?>
        <section class="page-content-section py-5">
            <div class="container">
                <?php the_content(); ?>
            </div>
        </section>
    <?php else : ?>
        <div class="container py-5">
            <?php
            if (have_posts()) :
                while (have_posts()) :
                    the_post();
                    get_template_part('template-parts/content', get_post_type());
                endwhile;
                the_posts_navigation([
                    'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous:', 'blackcnote') . '</span> <span class="nav-title">%title</span>',
                    'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', 'blackcnote') . '</span> <span class="nav-title">%title</span>',
                ]);
            else :
                get_template_part('template-parts/content', 'none');
            endif;
            ?>
        </div>
    <?php endif; ?>
</main>

<?php
get_footer(); 
<?php
/**
 * The main template file
 *
 * @package BlackCnote_Theme
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
    <div class="container py-5">
        <?php
        if (have_posts()) :
            while (have_posts()) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('card mb-4'); ?>>
                    <div class="card-body">
                        <header class="entry-header">
                            <?php
                            if (is_singular()) :
                                the_title('<h1 class="entry-title card-title">', '</h1>');
                            else :
                                the_title('<h2 class="entry-title card-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
                            endif;

                            if ('post' === get_post_type()) :
                                ?>
                                <div class="entry-meta text-muted small mb-3">
                                    <?php
                                    printf(
                                        /* translators: %s: post date */
                                        esc_html__('Posted on %s', 'blackcnote-theme'),
                                        '<time datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(get_the_date()) . '</time>'
                                    );
                                    ?>
                                </div>
                            <?php endif; ?>
                        </header>

                        <div class="entry-content">
                            <?php
                            if (is_singular()) :
                                the_content(
                                    sprintf(
                                        wp_kses(
                                            /* translators: %s: Name of current post. Only visible to screen readers */
                                            __('Continue reading<span class="screen-reader-text"> "%s"</span>', 'blackcnote-theme'),
                                            [
                                                'span' => [
                                                    'class' => [],
                                                ],
                                            ]
                                        ),
                                        wp_kses_post(get_the_title())
                                    )
                                );

                                wp_link_pages([
                                    'before' => '<div class="page-links">' . esc_html__('Pages:', 'blackcnote-theme'),
                                    'after'  => '</div>',
                                ]);
                            else :
                                the_excerpt();
                                ?>
                                <a href="<?php echo esc_url(get_permalink()); ?>" class="btn btn-primary">
                                    <?php esc_html_e('Read More', 'blackcnote-theme'); ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <?php if (is_singular() && 'post' === get_post_type()) : ?>
                            <footer class="entry-footer mt-3">
                                <?php
                                $categories_list = get_the_category_list(esc_html__(', ', 'blackcnote-theme'));
                                if ($categories_list) {
                                    printf(
                                        /* translators: %s: list of categories */
                                        '<span class="cat-links me-3">' . esc_html__('Posted in %s', 'blackcnote-theme') . '</span>',
                                        $categories_list
                                    );
                                }

                                $tags_list = get_the_tag_list('', esc_html_x(', ', 'list item separator', 'blackcnote-theme'));
                                if ($tags_list) {
                                    printf(
                                        /* translators: %s: list of tags */
                                        '<span class="tags-links">' . esc_html__('Tagged %s', 'blackcnote-theme') . '</span>',
                                        $tags_list
                                    );
                                }
                                ?>
                            </footer>
                        <?php endif; ?>
                    </div>
                </article>
                <?php
            endwhile;

            the_posts_navigation([
                'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous:', 'blackcnote-theme') . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', 'blackcnote-theme') . '</span> <span class="nav-title">%title</span>',
            ]);

        else :
            ?>
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title"><?php esc_html_e('Nothing Found', 'blackcnote-theme'); ?></h2>
                    <div class="card-text">
                        <?php
                        if (is_search()) :
                            ?>
                            <p><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'blackcnote-theme'); ?></p>
                            <?php
                            get_search_form();
                        else :
                            ?>
                            <p><?php esc_html_e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'blackcnote-theme'); ?></p>
                            <?php
                            get_search_form();
                        endif;
                        ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer(); 
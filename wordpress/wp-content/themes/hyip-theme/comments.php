<?php
/**
 * The template for displaying comments
 *
 * @package HYIP_Theme
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            $hyip_theme_comment_count = get_comments_number();
            if ('1' === $hyip_theme_comment_count) {
                printf(
                    esc_html__('One thought on &ldquo;%1$s&rdquo;', 'hyip-theme'),
                    '<span>' . wp_kses_post(get_the_title()) . '</span>'
                );
            } else {
                printf(
                    esc_html(_nx('%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $hyip_theme_comment_count, 'comments title', 'hyip-theme')),
                    number_format_i18n($hyip_theme_comment_count),
                    '<span>' . wp_kses_post(get_the_title()) . '</span>'
                );
            }
            ?>
        </h2>

        <?php the_comments_navigation(); ?>

        <ol class="comment-list">
            <?php
            wp_list_comments(
                array(
                    'style'      => 'ol',
                    'short_ping' => true,
                )
            );
            ?>
        </ol>

        <?php
        the_comments_navigation();

        if (!comments_open()) :
            ?>
            <p class="no-comments"><?php esc_html_e('Comments are closed.', 'hyip-theme'); ?></p>
        <?php
        endif;

    endif;

    comment_form();
    ?>
</div> 
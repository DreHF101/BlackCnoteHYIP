<?php
get_header();
?>
<div class="container main-content">
    <?php
    if (have_posts()) :
        while (have_posts()) : the_post();
            // Render shortcodes in page content
            echo do_shortcode(get_the_content());
        endwhile;
    else :
        echo '<div class="theme-fallback"><h2>Page Not Found</h2><p>No content found for this page. Please add content or import demo data.</p></div>';
    endif;
    ?>
</div>
<?php get_footer(); ?> 
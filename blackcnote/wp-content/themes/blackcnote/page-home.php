<?php
/**
 * Template Name: Home Page
 * The custom home page template for static front page
 *
 * @package BlackCnote
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
        <div id="blackcnote-app">
            <!-- React app will be mounted here -->
            <div class="loading">
                Loading BlackCnote...
            </div>
        </div>
    </div>
</main>

<?php
get_footer(); 
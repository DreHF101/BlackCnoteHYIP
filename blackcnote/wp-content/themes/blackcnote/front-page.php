<?php
/**
 * The front page template file
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
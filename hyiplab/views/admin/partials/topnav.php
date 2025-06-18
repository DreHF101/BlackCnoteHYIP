<!-- navbar-wrapper start -->
<nav class="navbar-wrapper bg--dark">
    <div class="navbar__left">
        <button type="button" class="res-sidebar-open-btn me-3"><i class="las la-bars"></i></button>
        <form class="navbar-search">
            <input type="search" name="#0" class="navbar-search-field h-45" id="searchInput" autocomplete="off" placeholder="<?php esc_attr_e('Search here...', HYIPLAB_PLUGIN_NAME); ?>">
            <i class="las la-search"></i>
            <ul class="search-list"></ul>
        </form>
    </div>
    <div class="navbar__right">
        <ul class="navbar__action-list">
            <li class="dropdown">
                <button type="button" class="" data-bs-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                    <span class="navbar-user">
                        <span class="navbar-user__thumb"><img src="<?php echo get_avatar_url(hyiplab_auth()->user->ID); ?>" alt="image"></span>
                        <span class="navbar-user__info">
                            <span class="navbar-user__name"><?php echo esc_html(hyiplab_auth()->user->display_name); ?></span>
                        </span>
                        <span class="icon"><i class="las la-chevron-circle-down"></i></span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">
                    <a href="<?php echo admin_url('profile.php') ?>" class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-user-circle"></i>
                        <span class="dropdown-menu__caption"><?php esc_html_e('Profile', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>

                    <a href="<?php echo wp_logout_url(home_url()); ?>" class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-sign-out-alt"></i>
                        <span class="dropdown-menu__caption"><?php esc_html_e('Logout', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- navbar-wrapper end -->
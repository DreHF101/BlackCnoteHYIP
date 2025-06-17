<?php

namespace Hyiplab\Hook;

use Hyiplab\BackOffice\AdminRequestHandler;

class AdminMenu
{

    public function menuSetting()
    {
        add_menu_page(
            esc_html__('Hyiplab', HYIPLAB_PLUGIN_NAME),
            esc_html__('Hyiplab', HYIPLAB_PLUGIN_NAME),
            'manage_options',
            hyiplab_route('admin.hyiplab')->query_string,
            [new AdminRequestHandler(), 'handle'],
            'dashicons-admin-settings',
            2
        );
    }
}

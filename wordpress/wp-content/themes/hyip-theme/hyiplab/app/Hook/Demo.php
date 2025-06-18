<?php

namespace Hyiplab\Hook;

class Demo
{
    public function protectPost()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $notify[] = ['info', 'This version is for demonstration purposes only and few actions are blocked'];
        $notify[] = ['warning', 'You can not change anything over this demo'];
        if ($method == 'POST') {
            hyiplab_back($notify);
        }
        if (isset($_GET['action']) && $_GET['action'] == 'activate') {
            hyiplab_back($notify);
        }
        remove_menu_page('edit.php');
        remove_menu_page('upload.php');
        remove_menu_page('edit.php?post_type=page');
        remove_menu_page('edit-comments.php');
        remove_menu_page('themes.php');
        remove_menu_page('tools.php');
    }

    public function disablePluginDeactivation( $actions, $plugin_file, $plugin_data, $context ) {
        unset( $actions['deactivate'] );
        return $actions;
    }
}
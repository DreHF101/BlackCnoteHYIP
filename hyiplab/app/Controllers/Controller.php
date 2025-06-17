<?php

namespace Hyiplab\Controllers;

use Hyiplab\BackOffice\CoreController;

class Controller extends CoreController
{

    public $viewPath = '';

    protected $pageTitle = '';

    public function __construct()
    {
        add_filter('wp_title', function ($title, $sep) {
            return esc_html__($this->pageTitle, HYIPLAB_PLUGIN_NAME) . ' ' . $sep . ' ' . $title;
        }, 10, 2);
        
        $this->viewPath = HYIPLAB_ROOT . 'views';
    }
}

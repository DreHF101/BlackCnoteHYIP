<?php

namespace Hyiplab\Includes;


class RegisterAssets{
    public static $styles = [
        'admin'=>[
            'overwrite.css',
            'vendor/bootstrap-toggle.min.css',
            'vendor/select2.min.css',
            'hyiplab_admin.css',
        ],
        'global'=>[
            'bootstrap.min.css',
            'all.min.css',
            'line-awesome.min.css',
        ],
        'public'=>[
            'hyiplab_public.css',
            'hyiplab_activation.css'
        ]
    ];
    public static $scripts = [
        'admin'=>[
            'vendor/bootstrap-toggle.min.js',
            'vendor/jquery.slimscroll.min.js',
            'nicEdit.js',
            'vendor/select2.min.js',
            'hyiplab_admin.js',
        ],
        'global'=>[
            'bootstrap.bundle.min.js',
        ],
        'public'=>[
            'hyiplab_public.js'
        ]
    ];
}
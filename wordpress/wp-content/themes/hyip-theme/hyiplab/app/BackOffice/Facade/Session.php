<?php

namespace Hyiplab\BackOffice\Facade;

use Hyiplab\BackOffice\Facade\Facade;

class Session extends Facade{
    protected static function getFacadeAccessor()
    {
        return 'session';
    }
}
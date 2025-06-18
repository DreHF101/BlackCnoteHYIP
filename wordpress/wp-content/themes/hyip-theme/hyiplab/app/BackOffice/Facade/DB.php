<?php

namespace Hyiplab\BackOffice\Facade;

use Hyiplab\BackOffice\Facade\Facade;

class DB extends Facade{
    protected static function getFacadeAccessor()
    {
        return 'db';
    }
}
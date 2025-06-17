<?php

namespace Hyiplab\BackOffice;

use Hyiplab\BackOffice\Database\Database;
use Hyiplab\BackOffice\Router\Router;
use Hyiplab\Middleware\RegisterMiddleware;

class System
{

    private static $instance = null;
    public $middleware;
    public $globalMiddleware;
    public $bindValue = [];

    public static $facades = [
        'db'      => Database::class,
        'session' => Session::class
    ];

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    public function handleRequestThroughRouter()
    {
        require_once HYIPLAB_ROOT . 'routes/web.php';
        require_once HYIPLAB_ROOT . 'routes/admin.php';
        hyiplab_system_instance()->bindValue('routes', Router::$routes);
    }

    public function bootMiddleware()
    {
        $registeredMiddleware   = new RegisterMiddleware;
        $this->middleware       = $registeredMiddleware->aliasMiddleware;
        $this->globalMiddleware = $registeredMiddleware->globalMiddleware;
    }

    public function bindValue($key, $value)
    {
        $this->bindValue[$key] = $value;
    }

    public function routes()
    {
        return $this->bindValue['routes'];
    }

    public function route($routeName)
    {
        $routes      = $this->routes();
        $routeExists = false;
        foreach ($routes as $route) {
            if (array_key_exists($routeName, $route)) {
                $routeExists = true;
                return $route[$routeName];
            }
        }
        if (!$routeExists) {
            throw new \Exception($routeName . ' route not define');
        }
    }
}

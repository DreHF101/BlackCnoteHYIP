<?php

namespace Hyiplab\BackOffice;

use Hyiplab\BackOffice\Router\Router;
use Hyiplab\Controllers\Admin\AdminController;
use Hyiplab\Hook\ExecuteRouter;

class AdminRequestHandler
{
    public function handle()
    {
        if (isset(hyiplab_request()->page) && hyiplab_request()->page == HYIPLAB_PLUGIN_NAME) {
            if (!isset(hyiplab_request()->module)) {
                $handler = new MiddlewareHandler();
                $handler->filterGlobalMiddleware();

                $routes = Router::$routes;
                foreach ($routes as $routeKey => $routeValue) {
                    foreach ($routeValue as $routerKey => $router) {
                        if (array_key_exists('query_string', $router) && $router['query_string'] == strtolower(HYIPLAB_PLUGIN_NAME)) {
                            if (array_key_exists('middleware', $router)) {
                                $middleware = $router['middleware'];
                                $handler->handle($middleware);
                            }
                        }
                    }
                }

                $controller = new AdminController;
                $method     = 'dashboard';
                $controller->$method();
            } else {
                $getActions = ExecuteRouter::executeAdminRouter();
                if (!empty($getActions)) {
                    $controller = new $getActions[0];
                    $method     = $getActions[1];
                    $controller->$method();
                } else {
                    if (defined('WP_DEBUG') && true === WP_DEBUG) {
                        throw new \Exception("Something went wrong");
                    }
                    hyiplab_abort(404);
                }
            }
        }
    }
}

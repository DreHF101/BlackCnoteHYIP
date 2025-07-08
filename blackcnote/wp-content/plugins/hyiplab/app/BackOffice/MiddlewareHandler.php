<?php

namespace Hyiplab\BackOffice;

class MiddlewareHandler
{
	public function handle($assignedMiddleware = [])
	{
		foreach ($assignedMiddleware as $middleware) {
			if (array_key_exists($middleware, hyiplab_system_instance()->middleware)) {
				$middlewareName = hyiplab_system_instance()->middleware[$middleware];
				$this->callMiddleware($middlewareName);
			}
		}
	}

	public function filterGlobalMiddleware()
	{
		foreach (hyiplab_system_instance()->globalMiddleware as $middleware) {
			$this->callMiddleware($middleware);
		}
	}

	private function callMiddleware($middleware)
	{
		$middlewareClass = new $middleware;
		$middlewareClass->filterRequest();
	}
}

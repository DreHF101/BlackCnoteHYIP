<?php

namespace Hyiplab\Container;

class Application
{
    private static ?Application $instance = null;
    private Container $container;
    private ServiceProvider $serviceProvider;

    private function __construct()
    {
        $this->container = new Container();
        $this->serviceProvider = new ServiceProvider($this->container);
        $this->serviceProvider->register();
        $this->serviceProvider->boot();
    }

    /**
     * Get the singleton instance
     */
    public static function getInstance(): Application
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get the container instance
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Resolve a service from the container
     */
    public function make(string $abstract)
    {
        return $this->container->make($abstract);
    }

    /**
     * Check if a service is bound
     */
    public function has(string $abstract): bool
    {
        return $this->container->has($abstract);
    }

    /**
     * Bind a service to the container
     */
    public function bind(string $abstract, $concrete = null, bool $shared = false): void
    {
        $this->container->bind($abstract, $concrete, $shared);
    }

    /**
     * Bind a singleton service
     */
    public function singleton(string $abstract, $concrete = null): void
    {
        $this->container->singleton($abstract, $concrete);
    }

    /**
     * Register an existing instance
     */
    public function instance(string $abstract, $instance): void
    {
        $this->container->instance($abstract, $instance);
    }

    /**
     * Clear all bindings
     */
    public function clear(): void
    {
        $this->container->clear();
    }

    /**
     * Get a service using magic method
     */
    public function __get(string $name)
    {
        return $this->make($name);
    }

    /**
     * Call a method on a service
     */
    public function __call(string $name, array $arguments)
    {
        return $this->make($name);
    }
}

/**
 * Global helper function to access the application
 */
if (!function_exists('app')) {
    function app(string $abstract = null)
    {
        $app = Application::getInstance();
        
        if ($abstract === null) {
            return $app;
        }
        
        return $app->make($abstract);
    }
} 
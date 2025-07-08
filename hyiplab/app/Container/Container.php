<?php

namespace Hyiplab\Container;

use ReflectionClass;
use ReflectionParameter;
use InvalidArgumentException;

class Container
{
    private array $bindings = [];
    private array $instances = [];
    private array $singletons = [];

    /**
     * Bind a class or interface to a concrete implementation
     */
    public function bind(string $abstract, $concrete = null, bool $shared = false): void
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => $shared
        ];
    }

    /**
     * Bind a singleton instance
     */
    public function singleton(string $abstract, $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Register an existing instance
     */
    public function instance(string $abstract, $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Resolve a class from the container
     */
    public function make(string $abstract)
    {
        // Check if we have a registered instance
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // Check if we have a singleton instance
        if (isset($this->singletons[$abstract])) {
            return $this->singletons[$abstract];
        }

        // Get the concrete implementation
        $concrete = $this->getConcrete($abstract);

        // Build the instance
        $object = $this->build($concrete);

        // If it's a singleton, store it
        if (isset($this->bindings[$abstract]) && $this->bindings[$abstract]['shared']) {
            $this->singletons[$abstract] = $object;
        }

        return $object;
    }

    /**
     * Get the concrete implementation for an abstract
     */
    protected function getConcrete(string $abstract)
    {
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]['concrete'];
        }

        return $abstract;
    }

    /**
     * Build an instance of the given type
     */
    protected function build($concrete)
    {
        // If concrete is a closure, execute it
        if (is_callable($concrete) && !is_string($concrete)) {
            return $concrete($this);
        }

        // If concrete is a string (class name), use reflection
        if (is_string($concrete)) {
            $reflector = new ReflectionClass($concrete);

            if (!$reflector->isInstantiable()) {
                throw new InvalidArgumentException("Target [$concrete] is not instantiable.");
            }

            $constructor = $reflector->getConstructor();

            if (is_null($constructor)) {
                return new $concrete;
            }

            $dependencies = $this->resolveDependencies($constructor->getParameters());

            return $reflector->newInstanceArgs($dependencies);
        }

        throw new InvalidArgumentException("Unable to build [$concrete].");
    }

    /**
     * Resolve dependencies for a method
     */
    protected function resolveDependencies(array $dependencies): array
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            $results[] = $this->resolveDependency($dependency);
        }

        return $results;
    }

    /**
     * Resolve a single dependency
     */
    protected function resolveDependency(ReflectionParameter $dependency)
    {
        $type = $dependency->getType();

        if ($type && !$type->isBuiltin()) {
            return $this->make($type->getName());
        }

        if ($dependency->isDefaultValueAvailable()) {
            return $dependency->getDefaultValue();
        }

        throw new InvalidArgumentException("Unresolvable dependency [{$dependency->getName()}]");
    }

    /**
     * Check if the container has a binding
     */
    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * Clear all bindings and instances
     */
    public function clear(): void
    {
        $this->bindings = [];
        $this->instances = [];
        $this->singletons = [];
    }
} 
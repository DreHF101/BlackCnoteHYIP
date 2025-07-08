<?php
namespace Hyiplab\Container;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

class NotFoundException extends \Exception implements NotFoundExceptionInterface {}
class ContainerException extends \Exception implements ContainerExceptionInterface {}

class DIContainer implements ContainerInterface {
    private $bindings = [];
    private $instances = [];

    public function set($id, $concrete) {
        $this->bindings[$id] = $concrete;
    }

    public function get($id) {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }
        if (!isset($this->bindings[$id])) {
            throw new NotFoundException("Service not found: $id");
        }
        $concrete = $this->bindings[$id];
        if (is_callable($concrete)) {
            $object = $concrete($this);
        } else {
            $object = $concrete;
        }
        $this->instances[$id] = $object;
        return $object;
    }

    public function has($id) {
        return isset($this->bindings[$id]) || isset($this->instances[$id]);
    }
} 
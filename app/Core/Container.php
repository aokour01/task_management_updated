<?php

namespace App\Core;

use Closure;
use ReflectionClass;
use RuntimeException;

/**
 * A deliberately small service container.
 *
 * - bind() registers an explicit recipe for building something (used for
 *   things like the PDO connection, where we want one shared instance).
 * - make() builds a class, auto-injecting constructor dependencies by
 *   reading their type-hints, so controllers don't need to be constructed
 *   by hand every time a route is dispatched.
 */
class Container
{
    private array $bindings = [];
    private array $instances = [];

    public function bind(string $name, Closure $resolver): void
    {
        $this->bindings[$name] = $resolver;
    }

    public function make(string $name): object
    {
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        if (isset($this->bindings[$name])) {
            $object = ($this->bindings[$name])($this);
            $this->instances[$name] = $object;
            return $object;
        }

        return $this->build($name);
    }

    private function build(string $class): object
    {
        $reflector = new ReflectionClass($class);
        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type === null || $type->isBuiltin()) {
                throw new RuntimeException(
                    "Cannot auto-resolve parameter \${$parameter->getName()} for $class."
                );
            }

            $dependencies[] = $this->make($type->getName());
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}

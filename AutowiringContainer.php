<?php

/**
 * NOTICE OF LICENSE
 *
 * This file is licensed under the Software License Agreement.
 *
 * With the purchase or the installation of the software in your application
 * you accept the license agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author Arkonsoft
 * @copyright 2024 Arkonsoft
 */

 declare(strict_types=1);

namespace Arkonsoft\PsModule\DI;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AutowiringContainer implements AutowiringContainerInterface
{
    private $services = [];
    private $instances = [];

    /**
     * @template T
     * @param class-string<T>|string $id
     * @param mixed $concrete
     * @return void
     */
    public function set(string $id, $concrete)
    {
        $this->services[$id] = $concrete;
    }

    /**
     * @template T
     * @param class-string<T>|string $id
     * @return T|mixed
     * @throws ServiceNotFoundException
     */
    public function get(string $id)
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!$this->has($id)) {
            if (class_exists($id)) {
                return $this->resolveClass($id);
            }
            throw new ServiceNotFoundException("Service $id not found");
        }

        $concrete = $this->services[$id];
        $instance = $this->resolve($concrete);
        
        $this->instances[$id] = $instance;
        
        return $instance;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }

    /**
     * @template T
     * @param mixed|callable|class-string<T> $concrete
     * @return T|mixed
     */
    private function resolve($concrete)
    {
        if (is_callable($concrete)) {
            return $concrete($this);
        }

        if (is_object($concrete)) {
            return $concrete;
        }

        if (is_string($concrete) && class_exists($concrete)) {
            return $this->resolveClass($concrete);
        }

        return $concrete;
    }

    /**
     * @template T
     * @param class-string<T> $className
     * @return T
     * @throws ServiceNotFoundException
     */
    private function resolveClass(string $className)
    {
        $reflectionClass = new \ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return new $className();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            if ($parameter->getClass() === null) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                    continue;
                }
                throw new ServiceNotFoundException(
                    "Cannot resolve parameter {$parameter->getName()} of class $className"
                );
            }

            $dependencyClassName = $parameter->getClass()->getName();
            $dependencies[] = $this->get($dependencyClassName);
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }
} 
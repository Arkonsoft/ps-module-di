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
 * @copyright 2023 Arkonsoft
 */

declare(strict_types=1);

namespace Arkonsoft\PsModule\DI;

use Exception;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Container implements ContainerInterface
{
    private static $services = [];

    /**
     * > It takes a service name as a parameter, checks if it exists in the services array, and if it
     * does, it creates a new instance of the service class and returns it
     *
     * @var string name The name of the service to get.
     *
     * @return ServiceInterface A new instance of the class that is registered with the service
     * manager.
     */
    public function get(string $name): ServiceInterface
    {
        if (empty($name)) {
            if (_PS_MODE_DEV_) {
                throw new Exception("Service name is required", 1);
            }
        }

        if (empty(static::$services[$name])) {
            if (_PS_MODE_DEV_) {
                throw new Exception("Service {$name} is not registered", 1);
            }
        }

        $service = static::$services[$name];

        if (empty($service['class']) || !class_exists($service['class'])) {
            if (_PS_MODE_DEV_) {
                throw new Exception("Service class {$service['class']} does not exists", 1);
            }
        }

        /* Return instance if exists */
        if (!empty($service['instance'])) {
            return $service['instance'];
        }

        $params = [];

        if (!empty($service['params']) && is_array($service['params'])) {
            $params = $service['params'];
        }

        static::$services[$name]['instance'] = new $service['class'](...$params);

        return static::$services[$name]['instance'];
    }

    /**
     * Registers new service in the container
     *
     * @param string name The name of the service.
     * @param string class The class name of the service
     * @param array params to pass into the service
     *
     * @return bool
     */
    public function set(string $name, string $class, array $params = []): bool
    {
        if (empty($name)) {
            if (_PS_MODE_DEV_) {
                throw new Exception("Service name is required", 1);
            }
        }

        if (empty($class)) {
            if (_PS_MODE_DEV_) {
                throw new Exception("Service class is required", 1);
            }
        }

        if (!class_exists($class)) {
            if (_PS_MODE_DEV_) {
                throw new Exception("Service class {$class} does not exists", 1);
            }
        }

        static::$services[$name] = [
            'class' => $class,
            'params' => $params,
            'instance' => null
        ];

        return true;
    }
}

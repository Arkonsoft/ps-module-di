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

interface AutowiringContainerInterface
{
    /**
     * @template T
     * @param class-string<T>|string $id
     * @param mixed $concrete
     * @return void
     */
    public function set(string $id, $concrete);

    /**
     * @template T
     * @param class-string<T>|string $id
     * @return T|mixed
     * @throws ServiceNotFoundException
     */
    public function get(string $id);

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool;
}

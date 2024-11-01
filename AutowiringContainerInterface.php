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
     * Rejestruje serwis w kontenerze
     * 
     * @param string $id Identyfikator serwisu
     * @param mixed $concrete Definicja serwisu (klasa, obiekt, callback lub wartość)
     * @return void
     */
    public function set(string $id, $concrete);

    /**
     * Pobiera serwis z kontenera
     * 
     * @param string $id Identyfikator serwisu
     * @throws ServiceNotFoundException gdy serwis nie istnieje
     * @return mixed
     */
    public function get(string $id);

    /**
     * Sprawdza czy serwis istnieje w kontenerze
     * 
     * @param string $id Identyfikator serwisu
     * @return bool
     */
    public function has(string $id): bool;
}

<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Extend;

use DuRoom\Extension\Extension;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;

class ModelUrl implements ExtenderInterface
{
    private $modelClass;
    private $slugDrivers = [];

    /**
     * @param string $modelClass: The ::class attribute of the model you are modifying.
     *                           This model should extend from \DuRoom\Database\AbstractModel.
     */
    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * Add a slug driver.
     *
     * @param string $identifier: Identifier for slug driver.
     * @param string $driver: ::class attribute of driver class, which must implement DuRoom\Http\SlugDriverInterface.
     * @return self
     */
    public function addSlugDriver(string $identifier, string $driver): self
    {
        $this->slugDrivers[$identifier] = $driver;

        return $this;
    }

    public function extend(Container $container, Extension $extension = null)
    {
        if ($this->slugDrivers) {
            $container->extend('duroom.http.slugDrivers', function ($existingDrivers) {
                $existingDrivers[$this->modelClass] = array_merge(Arr::get($existingDrivers, $this->modelClass, []), $this->slugDrivers);

                return $existingDrivers;
            });
        }
    }
}

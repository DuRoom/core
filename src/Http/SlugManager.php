<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Http;

use Illuminate\Support\Arr;

class SlugManager
{
    protected $drivers = [];

    public function __construct(array $drivers)
    {
        $this->drivers = $drivers;
    }

    public function forResource(string $resourceName): SlugDriverInterface
    {
        return Arr::get($this->drivers, $resourceName, null);
    }
}

<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Settings;

class UninstalledSettingsRepository implements SettingsRepositoryInterface
{
    public function all(): array
    {
        return [];
    }

    public function get($key, $default = null)
    {
        return $default;
    }

    public function set($key, $value)
    {
        // Do nothing
    }

    public function delete($keyLike)
    {
        // Do nothing
    }
}

<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Extend;

use DuRoom\Extension\Extension;
use DuRoom\User\User as DuRoomUser;
use Illuminate\Contracts\Container\Container;

class User implements ExtenderInterface
{
    private $displayNameDrivers = [];
    private $groupProcessors = [];
    private $preferences = [];

    /**
     * Add a display name driver.
     *
     * @param string $identifier: Identifier for display name driver. E.g. 'username' for UserNameDriver
     * @param string $driver: ::class attribute of driver class, which must implement DuRoom\User\DisplayName\DriverInterface
     * @return self
     */
    public function displayNameDriver(string $identifier, string $driver): self
    {
        $this->displayNameDrivers[$identifier] = $driver;

        return $this;
    }

    /**
     * Dynamically process a user's list of groups when calculating permissions.
     * This can be used to give a user permissions for groups they aren't actually in, based on context.
     * It will not change the group badges displayed for the user.
     *
     * @param callable|string $callback
     *
     * The callable can be a closure or invokable class, and should accept:
     * - \DuRoom\User\User $user: the user in question.
     * - array $groupIds: an array of ids for the groups the user belongs to.
     *
     * The callable should return:
     * - array $groupIds: an array of ids for the groups the user belongs to.
     *
     * @return self
     */
    public function permissionGroups($callback): self
    {
        $this->groupProcessors[] = $callback;

        return $this;
    }

    /**
     * Register a new user preference.
     *
     * @param string $key
     * @param callable $transformer
     * @param mixed|null $default
     * @return self
     */
    public function registerPreference(string $key, callable $transformer = null, $default = null): self
    {
        $this->preferences[$key] = compact('transformer', 'default');

        return $this;
    }

    public function extend(Container $container, Extension $extension = null)
    {
        $container->extend('duroom.user.display_name.supported_drivers', function ($existingDrivers) {
            return array_merge($existingDrivers, $this->displayNameDrivers);
        });

        $container->extend('duroom.user.group_processors', function ($existingRelations) {
            return array_merge($existingRelations, $this->groupProcessors);
        });

        foreach ($this->preferences as $key => $preference) {
            DuRoomUser::registerPreference($key, $preference['transformer'], $preference['default']);
        }
    }
}

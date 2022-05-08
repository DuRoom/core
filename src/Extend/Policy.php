<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Extend;

use DuRoom\Extension\Extension;
use DuRoom\User\Access\AbstractPolicy;
use Illuminate\Contracts\Container\Container;

class Policy implements ExtenderInterface
{
    private $globalPolicies = [];
    private $modelPolicies = [];

    /**
     * Add a custom policy for when an ability check is ran without a model instance.
     *
     * @param string $policy: ::class attribute of policy class, which must extend DuRoom\User\Access\AbstractPolicy
     * @return self
     */
    public function globalPolicy(string $policy): self
    {
        $this->globalPolicies[] = $policy;

        return $this;
    }

    /**
     * Add a custom policy for when an ability check is ran on an instance of a model.
     *
     * @param string $modelClass: The ::class attribute of the model you are applying policies to.
     *                           This model should extend from \DuRoom\Database\AbstractModel.
     * @param string $policy: ::class attribute of policy class, which must extend DuRoom\User\Access\AbstractPolicy
     * @return self
     */
    public function modelPolicy(string $modelClass, string $policy): self
    {
        if (! array_key_exists($modelClass, $this->modelPolicies)) {
            $this->modelPolicies[$modelClass] = [];
        }

        $this->modelPolicies[$modelClass][] = $policy;

        return $this;
    }

    public function extend(Container $container, Extension $extension = null)
    {
        $container->extend('duroom.policies', function ($existingPolicies) {
            foreach ($this->modelPolicies as $modelClass => $addPolicies) {
                if (! array_key_exists($modelClass, $existingPolicies)) {
                    $existingPolicies[$modelClass] = [];
                }

                foreach ($addPolicies as $policy) {
                    $existingPolicies[$modelClass][] = $policy;
                }
            }

            $existingPolicies[AbstractPolicy::GLOBAL] = array_merge($existingPolicies[AbstractPolicy::GLOBAL], $this->globalPolicies);

            return $existingPolicies;
        });
    }
}

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

class ServiceProvider implements ExtenderInterface
{
    private $providers = [];

    /**
     * Register a service provider.
     *
     * Service providers are an advanced feature and might give access to DuRoom internals that do not come with backward compatibility.
     * Please read our documentation about service providers for recommendations.
     * @see https://duroom.js.org/docs/extend/service-provider/
     *
     * @param string $serviceProviderClass The ::class attribute of the service provider class.
     * @return self
     */
    public function register(string $serviceProviderClass): self
    {
        $this->providers[] = $serviceProviderClass;

        return $this;
    }

    public function extend(Container $container, Extension $extension = null)
    {
        $app = $container->make('duroom');

        foreach ($this->providers as $provider) {
            $app->register($provider);
        }
    }
}

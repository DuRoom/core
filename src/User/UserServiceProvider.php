<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\User;

use DuRoom\Discussion\Access\DiscussionPolicy;
use DuRoom\Discussion\Discussion;
use DuRoom\Foundation\AbstractServiceProvider;
use DuRoom\Foundation\ContainerUtil;
use DuRoom\Group\Access\GroupPolicy;
use DuRoom\Group\Group;
use DuRoom\Post\Access\PostPolicy;
use DuRoom\Post\Post;
use DuRoom\Settings\SettingsRepositoryInterface;
use DuRoom\User\Access\ScopeUserVisibility;
use DuRoom\User\DisplayName\DriverInterface;
use DuRoom\User\DisplayName\UsernameDriver;
use DuRoom\User\Event\EmailChangeRequested;
use DuRoom\User\Event\Registered;
use DuRoom\User\Event\Saving;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;

class UserServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerDisplayNameDrivers();
        $this->registerPasswordCheckers();

        $this->container->singleton('duroom.user.group_processors', function () {
            return [];
        });

        $this->container->singleton('duroom.policies', function () {
            return [
                Access\AbstractPolicy::GLOBAL => [],
                Discussion::class => [DiscussionPolicy::class],
                Group::class => [GroupPolicy::class],
                Post::class => [PostPolicy::class],
                User::class => [Access\UserPolicy::class],
            ];
        });
    }

    protected function registerDisplayNameDrivers()
    {
        $this->container->singleton('duroom.user.display_name.supported_drivers', function () {
            return [
                'username' => UsernameDriver::class,
            ];
        });

        $this->container->singleton('duroom.user.display_name.driver', function (Container $container) {
            $drivers = $container->make('duroom.user.display_name.supported_drivers');
            $settings = $container->make(SettingsRepositoryInterface::class);
            $driverName = $settings->get('display_name_driver', '');

            $driverClass = Arr::get($drivers, $driverName);

            return $driverClass
                ? $container->make($driverClass)
                : $container->make(UsernameDriver::class);
        });

        $this->container->alias('duroom.user.display_name.driver', DriverInterface::class);
    }

    protected function registerPasswordCheckers()
    {
        $this->container->singleton('duroom.user.password_checkers', function (Container $container) {
            return [
                'standard' => function (User $user, $password) use ($container) {
                    if ($container->make('hash')->check($password, $user->password)) {
                        return true;
                    }
                }
            ];
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Container $container, Dispatcher $events)
    {
        foreach ($container->make('duroom.user.group_processors') as $callback) {
            User::addGroupProcessor(ContainerUtil::wrapCallback($callback, $container));
        }

        User::setHasher($container->make('hash'));
        User::setPasswordCheckers($container->make('duroom.user.password_checkers'));
        User::setGate($container->makeWith(Access\Gate::class, ['policyClasses' => $container->make('duroom.policies')]));
        User::setDisplayNameDriver($container->make('duroom.user.display_name.driver'));

        $events->listen(Saving::class, SelfDemotionGuard::class);
        $events->listen(Registered::class, AccountActivationMailer::class);
        $events->listen(EmailChangeRequested::class, EmailConfirmationMailer::class);

        $events->subscribe(UserMetadataUpdater::class);

        User::registerPreference('discloseOnline', 'boolval', true);
        User::registerPreference('indexProfile', 'boolval', true);
        User::registerPreference('locale');

        User::registerVisibilityScoper(new ScopeUserVisibility(), 'view');
    }
}

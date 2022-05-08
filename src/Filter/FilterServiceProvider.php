<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Filter;

use DuRoom\Discussion\Filter\DiscussionFilterer;
use DuRoom\Discussion\Query as DiscussionQuery;
use DuRoom\Foundation\AbstractServiceProvider;
use DuRoom\Foundation\ContainerUtil;
use DuRoom\Group\Filter as GroupFilter;
use DuRoom\Group\Filter\GroupFilterer;
use DuRoom\Post\Filter as PostFilter;
use DuRoom\Post\Filter\PostFilterer;
use DuRoom\User\Filter\UserFilterer;
use DuRoom\User\Query as UserQuery;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;

class FilterServiceProvider extends AbstractServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->container->singleton('duroom.filter.filters', function () {
            return [
                DiscussionFilterer::class => [
                    DiscussionQuery\AuthorFilterGambit::class,
                    DiscussionQuery\CreatedFilterGambit::class,
                    DiscussionQuery\HiddenFilterGambit::class,
                    DiscussionQuery\UnreadFilterGambit::class,
                ],
                UserFilterer::class => [
                    UserQuery\EmailFilterGambit::class,
                    UserQuery\GroupFilterGambit::class,
                ],
                GroupFilterer::class => [
                    GroupFilter\HiddenFilter::class,
                ],
                PostFilterer::class => [
                    PostFilter\AuthorFilter::class,
                    PostFilter\DiscussionFilter::class,
                    PostFilter\IdFilter::class,
                    PostFilter\NumberFilter::class,
                    PostFilter\TypeFilter::class
                ],
            ];
        });

        $this->container->singleton('duroom.filter.filter_mutators', function () {
            return [];
        });
    }

    public function boot(Container $container)
    {
        // We can resolve the filter mutators in the when->needs->give callback,
        // but we need to resolve at least one regardless so we know which
        // filterers we need to register filters for.
        $filters = $this->container->make('duroom.filter.filters');

        foreach ($filters as $filterer => $filterClasses) {
            $container
                ->when($filterer)
                ->needs('$filters')
                ->give(function () use ($filterClasses) {
                    $compiled = [];

                    foreach ($filterClasses as $filterClass) {
                        $filter = $this->container->make($filterClass);
                        $compiled[$filter->getFilterKey()][] = $filter;
                    }

                    return $compiled;
                });

            $container
                ->when($filterer)
                ->needs('$filterMutators')
                ->give(function () use ($container, $filterer) {
                    return array_map(function ($filterMutatorClass) {
                        return ContainerUtil::wrapCallback($filterMutatorClass, $this->container);
                    }, Arr::get($container->make('duroom.filter.filter_mutators'), $filterer, []));
                });
        }
    }
}

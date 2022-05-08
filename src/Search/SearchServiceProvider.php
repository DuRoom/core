<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Search;

use DuRoom\Discussion\Query as DiscussionQuery;
use DuRoom\Discussion\Search\DiscussionSearcher;
use DuRoom\Discussion\Search\Gambit\FulltextGambit as DiscussionFulltextGambit;
use DuRoom\Foundation\AbstractServiceProvider;
use DuRoom\Foundation\ContainerUtil;
use DuRoom\User\Query as UserQuery;
use DuRoom\User\Search\Gambit\FulltextGambit as UserFulltextGambit;
use DuRoom\User\Search\UserSearcher;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;

class SearchServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->container->singleton('duroom.simple_search.fulltext_gambits', function () {
            return [
                DiscussionSearcher::class => DiscussionFulltextGambit::class,
                UserSearcher::class => UserFulltextGambit::class
            ];
        });

        $this->container->singleton('duroom.simple_search.gambits', function () {
            return [
                DiscussionSearcher::class => [
                    DiscussionQuery\AuthorFilterGambit::class,
                    DiscussionQuery\CreatedFilterGambit::class,
                    DiscussionQuery\HiddenFilterGambit::class,
                    DiscussionQuery\UnreadFilterGambit::class,
                ],
                UserSearcher::class => [
                    UserQuery\EmailFilterGambit::class,
                    UserQuery\GroupFilterGambit::class,
                ]
            ];
        });

        $this->container->singleton('duroom.simple_search.search_mutators', function () {
            return [];
        });
    }

    public function boot(Container $container)
    {
        $fullTextGambits = $container->make('duroom.simple_search.fulltext_gambits');

        foreach ($fullTextGambits as $searcher => $fullTextGambitClass) {
            $container
                ->when($searcher)
                ->needs(GambitManager::class)
                ->give(function () use ($container, $searcher, $fullTextGambitClass) {
                    $gambitManager = new GambitManager($container->make($fullTextGambitClass));
                    foreach (Arr::get($container->make('duroom.simple_search.gambits'), $searcher, []) as $gambit) {
                        $gambitManager->add($container->make($gambit));
                    }

                    return $gambitManager;
                });

            $container
                ->when($searcher)
                ->needs('$searchMutators')
                ->give(function () use ($container, $searcher) {
                    $searchMutators = Arr::get($container->make('duroom.simple_search.search_mutators'), $searcher, []);

                    return array_map(function ($mutator) {
                        return ContainerUtil::wrapCallback($mutator, $this->container);
                    }, $searchMutators);
                });
        }
    }
}

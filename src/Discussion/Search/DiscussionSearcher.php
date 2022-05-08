<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Discussion\Search;

use DuRoom\Discussion\DiscussionRepository;
use DuRoom\Search\AbstractSearcher;
use DuRoom\Search\GambitManager;
use DuRoom\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Builder;

class DiscussionSearcher extends AbstractSearcher
{
    /**
     * @var DiscussionRepository
     */
    protected $discussions;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * @param DiscussionRepository $discussions
     * @param Dispatcher $events
     * @param GambitManager $gambits
     * @param array $searchMutators
     */
    public function __construct(DiscussionRepository $discussions, Dispatcher $events, GambitManager $gambits, array $searchMutators)
    {
        parent::__construct($gambits, $searchMutators);

        $this->discussions = $discussions;
        $this->events = $events;
    }

    protected function getQuery(User $actor): Builder
    {
        return $this->discussions->query()->select('discussions.*')->whereVisibleTo($actor);
    }
}

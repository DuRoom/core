<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Group\Filter;

use DuRoom\Filter\AbstractFilterer;
use DuRoom\Group\GroupRepository;
use DuRoom\User\User;
use Illuminate\Database\Eloquent\Builder;

class GroupFilterer extends AbstractFilterer
{
    /**
     * @var GroupRepository
     */
    protected $groups;

    /**
     * @param GroupRepository $groups
     * @param array $filters
     * @param array $filterMutators
     */
    public function __construct(GroupRepository $groups, array $filters, array $filterMutators)
    {
        parent::__construct($filters, $filterMutators);

        $this->groups = $groups;
    }

    protected function getQuery(User $actor): Builder
    {
        return $this->groups->query()->whereVisibleTo($actor);
    }
}

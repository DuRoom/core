<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\User\Filter;

use DuRoom\Filter\AbstractFilterer;
use DuRoom\User\User;
use DuRoom\User\UserRepository;
use Illuminate\Database\Eloquent\Builder;

class UserFilterer extends AbstractFilterer
{
    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * @param UserRepository $users
     * @param array $filters
     * @param array $filterMutators
     */
    public function __construct(UserRepository $users, array $filters, array $filterMutators)
    {
        parent::__construct($filters, $filterMutators);

        $this->users = $users;
    }

    protected function getQuery(User $actor): Builder
    {
        return $this->users->query()->whereVisibleTo($actor);
    }
}

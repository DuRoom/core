<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Discussion\Query;

use DuRoom\Filter\FilterInterface;
use DuRoom\Filter\FilterState;
use DuRoom\Search\AbstractRegexGambit;
use DuRoom\Search\SearchState;
use DuRoom\User\UserRepository;
use Illuminate\Database\Query\Builder;

class AuthorFilterGambit extends AbstractRegexGambit implements FilterInterface
{
    /**
     * @var \DuRoom\User\UserRepository
     */
    protected $users;

    /**
     * @param \DuRoom\User\UserRepository $users
     */
    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * {@inheritdoc}
     */
    public function getGambitPattern()
    {
        return 'author:(.+)';
    }

    /**
     * {@inheritdoc}
     */
    protected function conditions(SearchState $search, array $matches, $negate)
    {
        $this->constrain($search->getQuery(), $matches[1], $negate);
    }

    public function getFilterKey(): string
    {
        return 'author';
    }

    public function filter(FilterState $filterState, string $filterValue, bool $negate)
    {
        $this->constrain($filterState->getQuery(), $filterValue, $negate);
    }

    protected function constrain(Builder $query, $rawUsernames, $negate)
    {
        $usernames = trim($rawUsernames, '"');
        $usernames = explode(',', $usernames);

        $ids = [];
        foreach ($usernames as $username) {
            $ids[] = $this->users->getIdForUsername($username);
        }

        $query->whereIn('discussions.user_id', $ids, 'and', $negate);
    }
}

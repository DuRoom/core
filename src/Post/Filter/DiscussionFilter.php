<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Post\Filter;

use DuRoom\Filter\FilterInterface;
use DuRoom\Filter\FilterState;

class DiscussionFilter implements FilterInterface
{
    public function getFilterKey(): string
    {
        return 'discussion';
    }

    public function filter(FilterState $filterState, string $filterValue, bool $negate)
    {
        $discussionId = trim($filterValue, '"');

        $filterState->getQuery()->where('posts.discussion_id', $negate ? '!=' : '=', $discussionId);
    }
}

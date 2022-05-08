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

class IdFilter implements FilterInterface
{
    public function getFilterKey(): string
    {
        return 'id';
    }

    public function filter(FilterState $filterState, string $filterValue, bool $negate)
    {
        $idString = trim($filterValue, '"');
        $ids = explode(',', $idString);

        $filterState->getQuery()->whereIn('posts.id', $ids, 'and', $negate);
    }
}

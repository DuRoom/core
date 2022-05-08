<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Group\Filter;

use DuRoom\Filter\FilterInterface;
use DuRoom\Filter\FilterState;

class HiddenFilter implements FilterInterface
{
    public function getFilterKey(): string
    {
        return 'hidden';
    }

    public function filter(FilterState $filterState, string $filterValue, bool $negate)
    {
        $filterState->getQuery()->where('is_hidden', $negate ? '!=' : '=', $filterValue);
    }
}

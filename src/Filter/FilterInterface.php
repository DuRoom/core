<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Filter;

interface FilterInterface
{
    /**
     * This filter will only be run when a query contains a filter param with this key.
     */
    public function getFilterKey(): string;

    /**
     * Filters a query.
     *
     * @param FilterState $filter
     * @param string $value The value of the requested filter
     */
    public function filter(FilterState $filterState, string $filterValue, bool $negate);
}

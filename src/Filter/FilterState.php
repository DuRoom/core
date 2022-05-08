<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Filter;

use DuRoom\Query\AbstractQueryState;

class FilterState extends AbstractQueryState
{
    /**
     * @var FilterInterface[]
     */
    protected $activeFilters = [];

    /**
     * Get a list of the filters that are active.
     *
     * @return FilterInterface[]
     */
    public function getActiveFilters()
    {
        return $this->activeFilters;
    }

    /**
     * Add a filter as being active.
     *
     * @param FilterInterface $filter
     * @return void
     */
    public function addActiveFilter(FilterInterface $filter)
    {
        $this->activeFilters[] = $filter;
    }
}

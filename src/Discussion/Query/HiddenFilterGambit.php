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
use Illuminate\Database\Query\Builder;

class HiddenFilterGambit extends AbstractRegexGambit implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function getGambitPattern()
    {
        return 'is:hidden';
    }

    /**
     * {@inheritdoc}
     */
    protected function conditions(SearchState $search, array $matches, $negate)
    {
        $this->constrain($search->getQuery(), $negate);
    }

    public function getFilterKey(): string
    {
        return 'hidden';
    }

    public function filter(FilterState $filterState, string $filterValue, bool $negate)
    {
        $this->constrain($filterState->getQuery(), $negate);
    }

    protected function constrain(Builder $query, bool $negate)
    {
        $query->where(function ($query) use ($negate) {
            if ($negate) {
                $query->whereNull('hidden_at')->where('comment_count', '>', 0);
            } else {
                $query->whereNotNull('hidden_at')->orWhere('comment_count', 0);
            }
        });
    }
}

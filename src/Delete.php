<?php

namespace Siqubu;

use Siqubu\Features\FromTrait;
use Siqubu\Features\GroupByTrait;
use Siqubu\Features\JoinInterface;
use Siqubu\Features\JoinTrait;
use Siqubu\Features\LimitTrait;
use Siqubu\Features\OrderByTrait;
use Siqubu\Features\WhereOrHavingTrait;
use Siqubu\Features\WhereTrait;
use SplQueue;

/**
 * Delete builder.
 */
class Delete extends AbstractBuilder implements JoinInterface
{
    use FromTrait, JoinTrait, WhereTrait, WhereOrHavingTrait, GroupByTrait, OrderByTrait, LimitTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->join     = new SplQueue();
        $this->where    = new SplQueue();
    }

    /**
     * Renders the whole query.
     *
     * @return string
     */
    public function render()
    {
        return implode(' ', array_filter([
            'DELETE',
            $this->renderFrom(),
            $this->renderJoin(),
            $this->renderWhere(),
            $this->renderGroupBy(),
            $this->renderOrderBy(),
            $this->renderLimit(),
        ]));
    }
}

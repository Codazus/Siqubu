<?php

namespace Siqubu;

use Siqubu\Features\FromTrait;
use Siqubu\Features\GroupByTrait;
use Siqubu\Features\HavingTrait;
use Siqubu\Features\JoinInterface;
use Siqubu\Features\JoinTrait;
use Siqubu\Features\LimitTrait;
use Siqubu\Features\OrderByTrait;
use Siqubu\Features\SelectInterface;
use Siqubu\Features\SelectTrait;
use Siqubu\Features\WhereOrHavingTrait;
use Siqubu\Features\WhereTrait;
use SplQueue;

/**
 * Select builder.
 */
class Select extends AbstractBuilder implements SelectInterface, JoinInterface
{
    use SelectTrait, FromTrait, JoinTrait, WhereTrait, WhereOrHavingTrait, GroupByTrait, HavingTrait, OrderByTrait, LimitTrait;

    /**
     * Select builder constructor.
     *
     * @param mixed $columns Columns to select
     */
    public function __construct($columns = null)
    {
        $this->join     = new SplQueue();
        $this->where    = new SplQueue();
        $this->having   = new SplQueue();

        if (null !== $columns) {
            foreach (func_get_args() as $arg) {
                $this->select($arg);
            }
        }
    }

    /**
     * Renders the whole query.
     *
     * @return string
     */
    public function render()
    {
        return implode(' ', array_filter([
            $this->renderSelect(),
            $this->renderFrom(),
            $this->renderJoin(),
            $this->renderWhere(),
            $this->renderGroupBy(),
            $this->renderHaving(),
            $this->renderOrderBy(),
            $this->renderLimit(),
        ]));
    }
}

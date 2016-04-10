<?php

namespace Siqubu;

use Siqubu\Features\JoinInterface;
use Siqubu\Features\JoinTrait;
use Siqubu\Features\LimitTrait;
use Siqubu\Features\OrderByTrait;
use Siqubu\Features\SetTrait;
use Siqubu\Features\WhereOrHavingTrait;
use Siqubu\Features\WhereTrait;
use SplQueue;

/**
 * Update builder.
 */
class Update extends AbstractBuilder implements JoinInterface
{
    use JoinTrait, SetTrait, WhereTrait, WhereOrHavingTrait, OrderByTrait, LimitTrait;

    /**
     * Table to update.
     *
     * @var string
     */
    protected $table;

    /**
     * Constructor.
     *
     * @param string $table The table to UPDATE
     */
    public function __construct($table)
    {
        $this->join     = new SplQueue();
        $this->where    = new SplQueue();
        $this->table    = (string) $table;
    }

    /**
     * Renders the whole query.
     *
     * @return string
     */
    public function render()
    {
        return implode(' ', array_filter([
            $this->renderUpdate(),
            $this->renderJoin(),
            $this->renderSet(),
            $this->renderWhere(),
            $this->renderOrderBy(),
            $this->renderLimit(),
        ]));
    }

    /**
     * Renders the UPDATE part.
     *
     * @return string
     */
    protected function renderUpdate()
    {
        return sprintf('UPDATE %s', $this->table);
    }
}

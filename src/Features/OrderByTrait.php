<?php

namespace Siqubu\Features;

use Siqubu\Expressions\Literal;
use Siqubu\Select;

trait OrderByTrait
{
    /**
     * ORDER BY parts.
     *
     * @var array
     */
    protected $order_by = [];

    /**
     * Add an ORDER BY clause.
     *
     * @param mixed $data The ORDER BY data
     *
     * @return OrderByTrait
     */
    public function orderBy($data)
    {
        $this->order_by = array_merge($this->order_by, is_array($data) ? $data : func_get_args());

        return $this;
    }

    /**
     * Renders the ORDER BY parts.
     *
     * @return string
     */
    protected function renderOrderBy()
    {
        if (empty($this->order_by)) {
            return '';
        }

        $order_by = [];

        foreach ($this->order_by as $value) {
            if ($value instanceof Literal) {
                $value = $value->render();
            } elseif ($value instanceof Select) {
                $value = sprintf('(%s)', $value->render());
            }

            $order_by[] = $value;
        }

        return trim(sprintf('ORDER BY %s', implode(', ', $order_by)));
    }
}

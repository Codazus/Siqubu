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
        if (!is_array($data)) {
            $data = [$data];
        }

        // If $args contains more than 1 data, we add them to existing columns.
        if (1 < func_num_args()) {
            $data = array_merge($data, array_slice(func_get_args(), 1));
        }

        foreach ($data as $key => $value) {
            if (!is_numeric($key)) {
                $value = [$key => $value];
            }

            $this->order_by[] = $value;
        }

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

        foreach ($this->order_by as $order_data) {
            list($alias, $value) = $this->getAliasData($order_data);

            if ($value instanceof Literal) {
                $value = $value->render();
            } elseif ($value instanceof Select) {
                $value = sprintf('(%s)', $value->render());
            }

            if (null !== $alias) {
                $value = sprintf('%s.%s', $alias, $value);
            }

            $order_by[] = $value;
        }

        return trim(sprintf('ORDER BY %s', implode(', ', $order_by)));
    }
}

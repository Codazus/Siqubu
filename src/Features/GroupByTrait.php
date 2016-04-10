<?php

namespace Siqubu\Features;

use Siqubu\Expressions\Literal;
use Siqubu\Select;

trait GroupByTrait
{
    /**
     * GROUP BY parts.
     *
     * @var array
     */
    protected $group_by = [];

    /**
     * Add a GROUP BY clause.
     *
     * @param mixed $data The GROUP BY data
     *
     * @return GroupByTrait
     */
    public function groupBy($data)
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

            $this->group_by[] = $value;
        }

        return $this;
    }

    /**
     * Renders the GROUP BY parts.
     *
     * @return string
     */
    protected function renderGroupBy()
    {
        if (empty($this->group_by)) {
            return '';
        }

        $group_by = [];

        foreach ($this->group_by as $group_data) {
            list($alias, $value) = $this->getAliasData($group_data);

            if ($value instanceof Literal) {
                $value = $value->render();
            } elseif ($value instanceof Select) {
                $value = sprintf('(%s)', $value->render());
            }

            if (null !== $alias) {
                $value = sprintf('%s.%s', $alias, $value);
            }

            $group_by[] = $value;
        }

        return trim(sprintf('GROUP BY %s', implode(', ', $group_by)));
    }
}

<?php

namespace Siqubu\Features;

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
        $this->group_by = array_merge($this->group_by, is_array($data) ? $data : func_get_args());

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

        foreach ($this->group_by as $value) {
            if ($value instanceof Select) {
                $value = sprintf('(%s)', $value->render());
            }

            $group_by[] = (string) $value;
        }

        return trim(sprintf('GROUP BY %s', implode(', ', $group_by)));
    }
}

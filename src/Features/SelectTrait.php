<?php

namespace Siqubu\Features;

use Siqubu\Select;

trait SelectTrait
{
    /**
     * Columns to use.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Set the selected columns.
     *
     * @param mixed $columns Columns to select.
     *
     * @return Select
     */
    public function select($column)
    {
        $this->columns = array_merge($this->columns, func_get_args());

        return $this;
    }

    /**
     * Renders the SELECT part.
     *
     * @return string
     */
    protected function renderSelect()
    {
        $fields = [];

        if (empty($this->columns)) {
            $this->columns = self::WILDCARD;
        }

        foreach ($this->columns as $data) {
            list($field, $alias) = is_array($data) ? $data : [$data, null];

            if ($field instanceof Select) {
                $field = sprintf('(%s)', $field->render());
            }

            if (null !== $alias) {
                $field = trim(sprintf('%s AS %s', $field, $alias));
            }

            $fields[] = (string) $field;
        }

        return trim(sprintf('SELECT %s', implode(', ', $fields)));
    }
}

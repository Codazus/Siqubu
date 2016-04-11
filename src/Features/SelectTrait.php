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
     * @param string|array $columns Columns to select.<br />
     * - If a string is passed, the value will be quoted,
     * - if a \Siqubu\Select is passed, he will be rendered,
     * - if an array is passed the key will be the table name and his value
     * the field.
     *
     * @return Select
     */
    public function select($columns)
    {
        $this->columns = array_merge($this->columns, is_array($columns) ? $columns : func_get_args());

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
                $field = trim(sprintf('%s %s', $field, $alias));
            }

            $fields[] = (string) $field;
        }

        return trim(sprintf('SELECT %s', implode(', ', $fields)));
    }
}

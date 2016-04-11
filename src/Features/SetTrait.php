<?php

namespace Siqubu\Features;

use Siqubu\Select;

trait SetTrait
{
    /**
     * Values to set.
     *
     * @var array
     */
    protected $set_values = [];

    /**
     * Set a new value to a column.
     *
     * @param string $column Column to set
     * @param mixed $value Value applied to the column
     *
     * @return SetTrait
     */
    public function set($column, $value)
    {
        $this->set_values[$column] = $value;

        return $this;
    }

    /**
     * Renders the SET parts.
     *
     * @return string
     */
    protected function renderSet()
    {
        $fields = [];

        if (empty($this->set_values)) {
            return '';
        }

        foreach ($this->set_values as $field => $value) {
            if ($value instanceof Select) {
                $value = sprintf('(%s)', $value->render());
            } else {
                $value = null === $value ? 'NULL' : $value;
            }

            $fields[] = sprintf('%s = %s', $field, $value);
        }

        return trim(sprintf('SET %s', implode(', ', $fields)));
    }
}

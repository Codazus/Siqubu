<?php

namespace Siqubu\Features;

trait LimitTrait
{
    /**
     * LIMIT part.
     *
     * @var array
     */
    protected $limit = [];

    /**
     * Add a LIMIT clause.
     *
     * @param int|null $offset Offset of the first row to return (or acts like
     * $count if it is the only argument)
     * @param int|null $count Maximum number of rows to return
     *
     * @return LimitTrait
     */
    public function limit($offset, $count = null)
    {
        $offset = (int) $offset;

        if (null === $count) {
            $count  = $offset;
            $offset = null;
        } else {
            $count = (int) $count;
        }

        $this->limit = [
            'count'     => $count,
            'offset'    => $offset,
        ];

        return $this;
    }

    /**
     * Renders the LIMIT part.
     *
     * @return string
     */
    protected function renderLimit()
    {
        if (empty($this->limit)) {
            return '';
        }

        $count  = $this->limit['count'];
        $offset = $this->limit['offset'];

        if (null === $offset) {
            return trim(sprintf('LIMIT %u', $count));
        }

        return trim(sprintf('LIMIT %u, %u', $offset, $count));
    }
}

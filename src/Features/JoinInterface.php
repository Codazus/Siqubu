<?php

namespace Siqubu\Features;

interface JoinInterface
{
    /**
     * @var string
     */
    const INNER_JOIN = 'INNER JOIN';

    /**
     * @var string
     */
    const LEFT_JOIN = 'LEFT JOIN';

    /**
     * @var string
     */
    const RIGHT_JOIN = 'RIGHT JOIN';

    /**
     * @var string
     */
    const FULL_JOIN = 'FULL JOIN';

    /**
     * @var string
     */
    const CROSS_JOIN = 'CROSS JOIN';

    /**
     * @var string
     */
    const NATURAL_JOIN = 'NATURAL JOIN';
}

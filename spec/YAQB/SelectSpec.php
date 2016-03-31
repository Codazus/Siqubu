<?php

namespace spec\YAQB;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SelectSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('YAQB\Select');
    }

    function it_returns_sql_string() {
        $this->from('users')
            ->select(['id', 'firstname', 'lastname'])
            ->render()
            ->shouldReturn('SELECT `id`, `firstname`, `lastname` FROM `users`');
    }
}

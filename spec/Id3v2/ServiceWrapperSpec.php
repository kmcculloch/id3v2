<?php

namespace spec\Id3v2;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ServiceWrapperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Id3v2\ServiceWrapper');
    }
}

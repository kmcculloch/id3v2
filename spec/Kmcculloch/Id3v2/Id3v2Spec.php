<?php

namespace spec\Kmcculloch\Id3v2;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Id3v2Spec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Kmcculloch\Id3v2\Id3v2');
    }
}

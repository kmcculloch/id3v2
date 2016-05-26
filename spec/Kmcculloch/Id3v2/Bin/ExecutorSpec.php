<?php

namespace spec\Kmcculloch\Id3v2\Bin;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ExecutorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Kmcculloch\Id3v2\Bin\Executor');
    }

    function it_can_run_a_command()
    {
        $this->exec('echo "hello world"')->shouldReturn(array('hello world'));
    }
}

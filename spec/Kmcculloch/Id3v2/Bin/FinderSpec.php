<?php

namespace spec\Kmcculloch\Id3v2\Bin;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FinderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Kmcculloch\Id3v2\Bin\Finder');
    }

    function it_can_locate_id3v2()
    {
        $usualPath = '/usr/bin/id3v2';

        if (file_exists($usualPath)) {
            $this->locate('id3v2')->shouldReturn($usualPath);
        }
    }
}

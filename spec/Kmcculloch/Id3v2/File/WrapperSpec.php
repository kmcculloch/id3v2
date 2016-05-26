<?php

namespace spec\Kmcculloch\Id3v2\File;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WrapperSpec extends ObjectBehavior
{
    function let()
    {
        $filePath = realpath(__DIR__.'/../../../../test_data/cusb-cyl14777d.mp3');
        $this->beConstructedWith($filePath);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Kmcculloch\Id3v2\File\Wrapper');
    }

    function it_checks_that_the_file_exists()
    {
        $file = '/foo/bar';
        $this->beConstructedWith($file);

        $exception = new \Exception(sprintf('File %s does not exist', $file));
        $this->shouldThrow($exception)->duringInstantiation();
    }

    function it_checks_that_the_file_is_an_mp3()
    {
        // Spoof a path to a real, non-executable file.
        $file = realpath(__DIR__.'/../../../../composer.json');
        $this->beConstructedWith($file);

        $exception = new \Exception(sprintf('File %s is not an audio file', $file));
        $this->shouldThrow($exception)->duringInstantiation();
    }

    function it_checks_for_id3v1()
    {
        $this->hasV1()->shouldReturn(true);
    }

    function it_checks_for_id3v2()
    {
        $this->hasV1()->shouldReturn(true);
    }
}

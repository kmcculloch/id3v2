<?php

namespace spec\Kmcculloch\Id3v2\Tag;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FileCheckerSpec extends ObjectBehavior
{
    function let()
    {
        // Test against a dummy file path.
        $this->beConstructedWith('/foo/bar.mp3');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Kmcculloch\Id3v2\Tag\FileChecker');
    }

    function it_checks_that_the_file_is_readable()
    {
        $this->checkIsReadable()->shouldReturn(false);
    }

    function it_checks_that_the_file_is_audio()
    {
        $this->checkIsAudio()->shouldReturn(false);
    }

    function it_checks_that_the_file_is_writeable()
    {
        $this->checkIsWriteable()->shouldReturn(false);
    }

    function it_returns_the_file_path()
    {
        $this->getPath()->shouldReturn('/foo/bar.mp3');
    }
}

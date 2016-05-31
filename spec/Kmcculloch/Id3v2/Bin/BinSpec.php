<?php

namespace spec\Kmcculloch\Id3v2\Bin;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Kmcculloch\Id3v2\Bin\Finder;
use Kmcculloch\Id3v2\Bin\Executor;

class BinSpec extends ObjectBehavior
{
    private $finder;
    private $executor;

    function let($finder, $executor)
    {
        // Spoof normal responses from the finder and executor.
        $this->finder = $finder;
        $this->finder->beADoubleOf('Kmcculloch\Id3v2\Bin\Finder');
        $this->finder->locate('id3v2')->willReturn('/usr/bin/id3v2');

        $this->executor = $executor;
        $this->executor->beADoubleOf('Kmcculloch\Id3v2\Bin\Executor');
        $this->executor->exec('/usr/bin/id3v2 --version')->willReturn(array(
            'id3v2 0.1.12',
            'Uses id3lib-3.8.3',
            '',
            'This program adds/modifies/removes/views id3v2 tags,',
            'and can convert from id3v1 tags',
        ));
        $this->beConstructedWith($this->finder, $this->executor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Kmcculloch\Id3v2\Bin\Bin');
    }

    function it_checks_that_the_binary_exists()
    {
        // Spoof a null return from the locator.
        $binPath = null;
        $this->finder->locate('id3v2')->willReturn($binPath);

        $exception = new \Exception('Could not locate id3v2 executable');
        $this->shouldThrow($exception)->duringInstantiation();
    }

    function it_checks_that_the_binary_is_a_file()
    {
        // Spoof a path to an imaginary file.
        $binPath = '/foo/bar';
        $this->finder->locate('id3v2')->willReturn($binPath);

        $exception = new \Exception(sprintf('File %s does not exist', $binPath));
        $this->shouldThrow($exception)->duringInstantiation();
    }

    function it_checks_that_the_binary_is_executable()
    {
        // Spoof a path to a real, non-executable file.
        $binPath = realpath(__DIR__.'/../../../../composer.json');
        $this->finder->locate('id3v2')->willReturn($binPath);

        $exception = new \Exception(sprintf('File %s is not executable', $binPath));
        $this->shouldThrow($exception)->duringInstantiation();
    }

    function it_confirms_the_binary_version()
    {
        // Spoof an unsupported id3v2 version.
        $this->executor->exec('/usr/bin/id3v2 --version')->willReturn(array(
            'id3v2 0.1.11',
        ));

        $exception = new \Exception('Only id3v2 version 0.1.12 is supported at this time.');
        $this->shouldThrow($exception)->duringInstantiation();
    }
}

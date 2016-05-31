<?php

namespace spec\Kmcculloch\Id3v2\File;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FileSpec extends ObjectBehavior
{
    private $filePath;
    private $checker;
    private $bin;

    function let($checker, $bin)
    {
        $this->filePath = '/foo/bar';

        $this->bin = $bin;
        $this->bin->beADoubleOf('Kmcculloch\Id3v2\Bin\Bin');
        $this->bin->exec(array(
            '--list',
            $this->filePath,
        ))->willReturn(array(
            '04 Fine For Now.mp3: No ID3 tag',
        ));

        $this->checker = $checker;
        $this->checker->beADoubleOf('Kmcculloch\Id3v2\File\Checker');
        $this->checker->checkIsReadable()->willReturn(true);
        $this->checker->checkIsAudio()->willReturn(true);
        $this->checker->checkIsWriteable()->willReturn(true);
        $this->checker->getPath()->willReturn($this->filePath);

        $this->beConstructedWith($this->checker, $this->bin);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Kmcculloch\Id3v2\File\File');
    }

    function it_checks_that_the_file_exists()
    {
        $this->checker->checkIsReadable()->willReturn(false);

        $exception = new \Exception(
            sprintf('File %s does not exist or cannot be read', $this->filePath)
        );
        $this->shouldThrow($exception)->duringInstantiation();
    }

    function it_checks_that_the_file_is_an_mp3()
    {
        $this->checker->checkIsAudio()->willReturn(false);

        $exception = new \Exception(
            sprintf('File %s is not an audio file', $this->filePath)
        );
        $this->shouldThrow($exception)->duringInstantiation();
    }

        // $this->bin->exec(array(
            // '--delete-v1',
            // $this->filePath,
        // ))->willReturn(array(
            // sprintf(
                // 'Stripping id3 tag in "%s"...id3v1 stripped.',
                // $this->filePath
            // ),
        // ));
        // $this->bin->exec(array(
            // '--delete-v2',
            // $this->filePath,
        // ))->willReturn(array(
            // sprintf(
                // 'Stripping id3 tag in "%s"...id3v2 stripped.',
                // $this->filePath
            // ),
        // ));
        // $this->bin->exec(array(
            // '--delete-all',
            // $this->filePath,
        // ))->willReturn(array(
            // sprintf(
                // 'Stripping id3 tag in "%s"...id3v1 and v2 stripped.',
                // $this->filePath
            // ),
        // ));
        // $col1 = new PhpSpec\Wrapper\Collaborator;
        // $col1->beADoubleOf('Kmcculloch\Id3v2\File\Tag');
        // $col1->get()->willReturn('All We Ask');
        // $this->tag->song->willReturn($col1);
        // // $this->tag->v1->willReturn($this->tag);
        // // $this->tag->v1->song->willReturn($this->tag);
        // // $this->tag->v1->beADoubleOf('Kmcculloch\Id3v2\File\Tag');
        // $this->tag->v1->song->beADoubleOf('Kmcculloch\Id3v2\File\Tag');
        // $this->tag->v1->song->get()->willReturn('All We Ask');
    // function it_checks_for_id3v1()
    // {
        // $this->checkForV1()->shouldReturn(true);
    // }

    // function it_checks_for_id3v2()
    // {
        // $this->checkForV2()->shouldReturn(true);
    // }

    // function it_returns_id3v1_tag()
    // {
        // $this->getV1()->shouldReturn(true);
    // }

    // function it_returns_id3v2_tag()
    // {
        // // $this->getV2()->shouldReturn(true);
    // }

    // function it_deletes_id3v1()
    // {
        // // $this->deleteV1()->shouldReturn(true);
    // }

    // function it_deletes_id3v2()
    // {
        // // $this->deleteV2()->shouldReturn(true);
    // }

    // function it_deletes_both()
    // {
        // // $this->deleteTags()->shouldReturn(true);
    // }

    // function it_converts_v1_to_v2()
    // {
        // // $this->convertV1ToV2()->shouldReturn(true);
    // }

    // function it_writes_id3v1()
    // {
        // // $tag = new TagV1();

        // // $this->writeTag($tag)->shouldReturn(true);
    // }

    // function it_writes_id3v2()
    // {
        // // $tag = new TagV2();

        // // $this->writeTag($tag)->shouldReturn(true);
    // }
}

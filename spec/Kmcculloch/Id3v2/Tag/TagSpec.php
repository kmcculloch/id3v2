<?php

namespace spec\Kmcculloch\Id3v2\Tag;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TagSpec extends ObjectBehavior
{
    private $filePath;
    private $bin;
    private $fileChecker;

    function let($fileChecker, $bin)
    {
        $this->filePath = '/foo/bar.mp3';

        $this->bin = $bin;
        $this->bin->beADoubleOf('Kmcculloch\Id3v2\Bin\Bin');
        $this->bin->exec(array(
            '--list',
            $this->filePath,
        ))->willReturn(array(
            '/foo/bar.mp3: No ID3 tag',
        ));

        $this->fileChecker = $fileChecker;
        $this->fileChecker->beADoubleOf('Kmcculloch\Id3v2\Tag\FileChecker');
        $this->fileChecker->checkIsReadable()->willReturn(true);
        $this->fileChecker->checkIsAudio()->willReturn(true);
        $this->fileChecker->checkIsWriteable()->willReturn(true);
        $this->fileChecker->getPath()->willReturn($this->filePath);

        $this->beConstructedWith($this->fileChecker, $this->bin);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Kmcculloch\Id3v2\Tag\Tag');
    }

    function it_checks_that_the_file_exists()
    {
        $this->fileChecker->checkIsReadable()->willReturn(false);

        $exception = new \Exception(
            sprintf('File %s does not exist or cannot be read', $this->filePath)
        );
        $this->shouldThrow($exception)->duringInstantiation();
    }

    function it_checks_that_the_file_is_an_mp3()
    {
        $this->fileChecker->checkIsAudio()->willReturn(false);

        $exception = new \Exception(
            sprintf('File %s is not an audio file', $this->filePath)
        );
        $this->shouldThrow($exception)->duringInstantiation();
    }

    function it_handles_v1_and_v2_together()
    {
        $this->bin->exec(array(
            '--list',
            $this->filePath,
        ))->willReturn(array(
            'id3v1 tag info for 01 Southern Point.mp3:',
            'Title  : Southern Point                  Artist: Grizzly Bear',
            'Album  : Veckatimest                     Year: 2009, Genre: Indie (131)',
            'Comment: Comment                         Track: 1',
            'id3v2 tag info for 01 Southern Point.mp3:',
            'TIT2 (Title/songname/content description): Southern Point',
            'TPE1 (Lead performer(s)/Soloist(s)): Grizzly Bear',
            'TALB (Album/Movie/Show title): Veckatimest',
            'TYER (Year): 2009',
            'TRCK (Track number/Position in set): 1',
            'COMM (Comments): ()[]: Comment',
            'COMM (Comments): (ID3v1 Comment)[XXX]: Comment',
            'TCON (Content type): Indie (131)',
        ));

        $this->v1->artist->get()->shouldReturn('Grizzly Bear');
        $this->v1->album->get()->shouldReturn('Veckatimest');
        $this->v1->song->get()->shouldReturn('Southern Point');
        $this->v1->comment->get()->shouldReturn('Comment');
        $this->v1->genre->get()->shouldReturn('131');
        $this->v1->year->get()->shouldReturn('2009');
        $this->v1->track->get()->shouldReturn('1');

        $this->v2->artist->get()->shouldReturn('Grizzly Bear');
        $this->v2->album->get()->shouldReturn('Veckatimest');
        $this->v2->song->get()->shouldReturn('Southern Point');
        $this->v2->comment->get()->shouldReturn('Comment');
        $this->v2->genre->get()->shouldReturn('Indie');
        $this->v2->year->get()->shouldReturn('2009');
        $this->v2->track->get()->shouldReturn('1');

        $this->artist->get()->shouldReturn('Grizzly Bear');
        $this->album->get()->shouldReturn('Veckatimest');
        $this->song->get()->shouldReturn('Southern Point');
        $this->comment->get()->shouldReturn('Comment');
        $this->genre->get()->shouldReturn('Indie');
        $this->year->get()->shouldReturn('2009');
        $this->track->get()->shouldReturn('1');

        $this->v1->get()->shouldReturn(array(
            'song' => 'Southern Point',
            'artist' => 'Grizzly Bear',
            'album' => 'Veckatimest',
            'year' => '2009',
            'genre' => '131',
            'comment' => 'Comment',
            'track' => '1',
        ));

        $this->v2->get()->shouldReturn(array(
            'TIT2' => 'Southern Point',
            'TPE1' => 'Grizzly Bear',
            'TALB' => 'Veckatimest',
            'TYER' => '2009',
            'TRCK' => '1',
            'COMM' => 'Comment',
            'TCON' => 'Indie',
        ));

        $this->get()->shouldReturn(array(
            'TIT2' => 'Southern Point',
            'TPE1' => 'Grizzly Bear',
            'TALB' => 'Veckatimest',
            'TYER' => '2009',
            'TRCK' => '1',
            'COMM' => 'Comment',
            'TCON' => 'Indie',
        ));
    }

    function it_handles_v1_only()
    {
        $this->bin->exec(array(
            '--list',
            $this->filePath,
        ))->willReturn(array(
            'id3v1 tag info for 02 Two Weeks.mp3:',
            'Title  : Two Weeks                       Artist: Grizzly Bear',
            'Album  : Veckatimest                     Year: 2009, Genre: Unknown (255)',
            'Comment:                                 Track: 2',
            '02 Two Weeks.mp3: No ID3v2 tag',
        ));

        $this->v1->song->get()->shouldReturn('Two Weeks');
        $this->v1->artist->get()->shouldReturn('Grizzly Bear');
        $this->v1->album->get()->shouldReturn('Veckatimest');
        $this->v1->year->get()->shouldReturn('2009');
        $this->v1->genre->get()->shouldReturn('255');
        $this->v1->comment->get()->shouldReturn('');
        $this->v1->track->get()->shouldReturn('2');

        $this->v2->song->get()->shouldReturn('');

        $this->song->get()->shouldReturn('Two Weeks');

        $this->v1->get()->shouldReturn(array(
            'song' => 'Two Weeks',
            'artist' => 'Grizzly Bear',
            'album' => 'Veckatimest',
            'year' => '2009',
            'genre' => '255',
            'comment' => '',
            'track' => '2',
        ));

        $this->v2->get()->shouldReturn(array());

        $this->get()->shouldReturn(array(
            'song' => 'Two Weeks',
            'artist' => 'Grizzly Bear',
            'album' => 'Veckatimest',
            'year' => '2009',
            'genre' => '255',
            'comment' => '',
            'track' => '2',
        ));
    }

    function it_handles_v2_only()
    {
        $this->bin->exec(array(
            '--list',
            $this->filePath,
        ))->willReturn(array(
            'id3v2 tag info for 03 All We Ask.mp3:',
            'TIT2 (Title/songname/content description): All We Ask',
            'TPE1 (Lead performer(s)/Soloist(s)): Grizzly Bear',
            'TALB (Album/Movie/Show title): Veckatimest',
            'TYER (Year): 2009',
            'TRCK (Track number/Position in set): 3',
            '03 All We Ask.mp3: No ID3v1 tag',
        ));

        $this->v1->song->get()->shouldReturn('');

        $this->v2->song->get()->shouldReturn('All We Ask');
        $this->v2->TIT2->get()->shouldReturn('All We Ask');

        $this->song->get()->shouldReturn('All We Ask');

        $this->v1->get()->shouldReturn(array());

        $this->v2->get()->shouldReturn(array(
            'TIT2' => 'All We Ask',
            'TPE1' => 'Grizzly Bear',
            'TALB' => 'Veckatimest',
            'TYER' => '2009',
            'TRCK' => '3',
        ));

        $this->get()->shouldReturn(array(
            'TIT2' => 'All We Ask',
            'TPE1' => 'Grizzly Bear',
            'TALB' => 'Veckatimest',
            'TYER' => '2009',
            'TRCK' => '3',
        ));
    }

    function it_handles_no_tag()
    {
        $this->bin->exec(array(
            '--list',
            $this->filePath,
        ))->willReturn(array(
            '04 Fine For Now.mp3: No ID3 tag',
        ));

        $this->v1->song->get()->shouldReturn('');
        $this->v2->song->get()->shouldReturn('');
        $this->song->get()->shouldReturn('');

        $this->v1->get()->shouldReturn(array());
        $this->v2->get()->shouldReturn(array());
        $this->get()->shouldReturn(array());
    }
}

<?php

namespace Kmcculloch\Id3v2\File;

use Kmcculloch\Id3v2\Bin\Finder;
use Kmcculloch\Id3v2\Bin\Executor;
use Kmcculloch\Id3v2\Bin\Service;

class Wrapper
{
    private $file;
    private $service;
    private $listOutput;

    public function __construct($file)
    {
        $this->file = $file;

        // Check that we're looking at a real file.
        if (!file_exists($this->file)) {
            throw new \Exception(sprintf('File %s does not exist', $this->file));
        }

        // Check that the file is an MP3.
        exec(sprintf('file %s', $this->file), $output);
        if (!strpos($output[0], 'Audio file')) {
            throw new \Exception(sprintf('File %s is not an audio file', $file));
        }

        // Build our id3v2 service object.
        $finder = new Finder('id3v2');
        $executor = new Executor();
        $this->service = new Service($finder, $executor);
    }

    public function hasV1()
    {
        // Refresh the list output.
        $this->getListOutput();

        // Filter the list output looking for the id3v1 tag.
        $filtered = array_filter($this->listOutput, function ($v) {
            return preg_match('/^id3v1 tag info for/', $v);
        });
        return (bool) $filtered;
    }

    public function hasV2()
    {
        // Refresh the list output.
        $this->getListOutput();

        // Filter the list output looking for the id3v2 tag.
        $filtered = array_filter($this->listOutput, function ($v) {
            return preg_match('/^id3v2 tag info for/', $v);
        });
        return (bool) $filtered;
    }

    private function getListOutput()
    {
        $arguments = array(
            '-l',
            $this->file,
        );

        $this->listOutput = $this->service->exec($arguments);
    }
}

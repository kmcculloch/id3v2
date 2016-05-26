<?php

namespace Kmcculloch\Id3v2\File;

class Wrapper
{
    private $file;

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
    }
}

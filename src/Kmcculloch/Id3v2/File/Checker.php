<?php

namespace Kmcculloch\Id3v2\File;

/**
 * Check for possible problems with the file.
 */
class Checker
{
    protected $filePath;

    /**
     * Constructor.
     *
     * @param str $filePath
     *   Path to the file we'll be checking.
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Check that the file exists and can be read.
     *
     * @return bool
     */
    public function checkIsReadable()
    {
        return is_readable($this->filePath);
    }

    /**
     * Check that the file is an audio file.
     *
     * @return bool
     */
    public function checkIsAudio()
    {
        exec(sprintf('file %s', $this->filePath), $output);

        return !strpos($output[0], 'Audio file') === false;
    }

    /**
     * Check that the file is writable.
     *
     * @return bool
     */
    public function checkIsWriteable()
    {
        return is_writable($this->filePath);
    }

    /**
     * Retrieve the file path.
     *
     * @return str The file path.
     */
    public function getPath()
    {
        return $this->filePath;
    }
}

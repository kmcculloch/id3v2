<?php

namespace Kmcculloch\Id3v2\File;

/**
 * CheckerInterface.
 */
interface CheckerInterface
{
    /**
     * Constructor.
     *
     * @param str $filePath
     *   Path to the file we'll be checking.
     */
    public function __construct($filePath);

    /**
     * Check that the file exists and can be read.
     *
     * @return bool
     */
    public function checkIsReadable();

    /**
     * Check that the file is an audio file.
     *
     * @return bool
     */
    public function checkIsAudio();

    /**
     * Check that the file is writable.
     *
     * @return bool
     */
    public function checkIsWriteable();

    /**
     * Retrieve the file path.
     *
     * @return str The file path.
     */
    public function getPath();
}

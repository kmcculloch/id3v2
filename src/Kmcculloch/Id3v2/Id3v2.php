<?php

/**
 * @file
 * Kmcculloch/Id3v2/Id3v2.php
 */

namespace Kmcculloch\Id3v2;

use Kmcculloch\Id3v2\Bin\Finder;
use Kmcculloch\Id3v2\Bin\Executor;
use Kmcculloch\Id3v2\Bin\Bin;
use Kmcculloch\Id3v2\File\Checker;
use Kmcculloch\Id3v2\File\File;

/**
 * Service wrapper for id3v2.
 */
class Id3v2
{
    private $bin;

    /**
     * Constructor for id3v2 service.
     */
    public function __construct()
    {
        // Find the id3v2 executable and build the binary wrapper.
        $finder = new Finder('id3v2');
        $executor = new Executor();
        $this->bin = new Bin($finder, $executor);
    }

    /**
     * Get a file manipulation object.
     *
     * @param str $filePath
     *   The file to wrap.
     *
     * @return File
     *   The file manipulation object.
     */
    public function get($filePath)
    {
        // Construct a checker object to validate the file.
        $checker = new Checker($filePath);

        return new File($checker, $this->bin);
    }

    /**
     * Alternative syntax for getting a file manipulation object.
     *
     * @param str $filePath
     *   The file to wrap.
     *
     * @return File
     *   The file manipulation object.
     */
    public function with($filePath)
    {
        return $this->get($filePath);
    }
}

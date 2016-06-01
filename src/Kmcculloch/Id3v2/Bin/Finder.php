<?php

namespace Kmcculloch\Id3v2\Bin;

/**
 * A helper to locate a binary on the filesystem.
 *
 * Wrapping this functionality into a separate object lets us feed dummy
 * command execution results to our PHPSpec tests. Ideally, our PHPSpec
 * tests will pass even if the requested binary is not installed locally.
 */
class Finder
{
    /**
     * Retrieve the path to the id3v2 binary on the filesystem.
     *
     * @param string $bin
     *   The name of the binary command to locate.
     *
     * @return string
     *   The path to the binary, if found.
     */
    public function locate($bin)
    {
        $command = sprintf('which %s', $bin);
        exec($command, $output);

        if ($output) {
            return $output[0];
        }
    }
}

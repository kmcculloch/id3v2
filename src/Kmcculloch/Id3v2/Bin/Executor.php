<?php

namespace Kmcculloch\Id3v2\Bin;

/**
 * Helper to pass along a shell command and retrieve the result.
 *
 * Wrapping this functionality into a separate object allows us to feed
 * dummy command execution results to our PHPSpec tests.
 */
class Executor
{
    /**
     * Execute a shell command.
     *
     * @param string $command
     *   The command string.
     *
     * @return array
     *   The output array from PHP's exec() command.
     */
    public function exec($command)
    {
        exec($command, $output);

        return $output;
    }
}

<?php

namespace Kmcculloch\Id3v2\Bin;

/**
 * ExecutorInterface.
 */
interface ExecutorInterface
{
    /**
     * Execute a command.
     *
     * @param str $command
     *   An executable bash command.
     */
    public function exec($command);
}

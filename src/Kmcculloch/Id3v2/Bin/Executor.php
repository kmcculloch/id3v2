<?php

namespace Kmcculloch\Id3v2\Bin;

class Executor implements ExecutorInterface
{
    public function exec($command)
    {
        exec($command, $output);

        return $output;
    }
}

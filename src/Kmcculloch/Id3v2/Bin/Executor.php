<?php

namespace Kmcculloch\Id3v2\Bin;

class Executor
{
    public function exec($command)
    {
        exec($command, $output);

        return $output;
    }
}

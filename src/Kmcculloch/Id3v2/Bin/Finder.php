<?php

namespace Kmcculloch\Id3v2\Bin;

class Finder
{
    public function locate($bin)
    {
        $command = sprintf('which %s', $bin);
        exec($command, $output);
        
        if ($output) {
            return $output[0];
        }
    }
}

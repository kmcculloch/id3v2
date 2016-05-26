<?php

namespace Kmcculloch\Id3v2\Bin;

class Finder implements FinderInterface
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

<?php

namespace Kmcculloch\Id3v2\Bin;

/**
 * FinderInterface.
 *
 * We code our binary executable helpers to an interface so that we can easily
 * return mock data during tests.
 */
interface FinderInterface
{
    public function locate($bin);
}

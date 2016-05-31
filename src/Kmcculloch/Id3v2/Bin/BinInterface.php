<?php

namespace Kmcculloch\Id3v2\Bin;

/**
 * BinInterface.
 *
 * We code our binary executable helpers to an interface so that we can easily
 * return mock data during tests.
 */
interface BinInterface
{
    public function __construct(Finder $finder, Executor $executor);
    public function exec(array $arguments);
}


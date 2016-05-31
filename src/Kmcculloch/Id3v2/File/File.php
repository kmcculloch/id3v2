<?php

namespace Kmcculloch\Id3v2\File;

use Kmcculloch\Id3v2\Bin\Bin;
use Kmcculloch\Id3v2\File\Checker;
use Kmcculloch\Id3v2\File\Tag;

/**
 * File wrapper object.
 */
class File
{
    protected $filePath;
    protected $checker;
    protected $bin;
    protected $tag;
    // protected $listOutput;

    /**
     * Constructor
     *
     * @param Checker $checker
     * @param Bin     $bin
     */
    public function __construct(Checker $checker, Bin $bin)
    {
        $this->filePath = $checker->getPath();
        $this->checker = $checker;
        $this->bin = $bin;

        // Check that we're looking at a real file.
        if (!$checker->checkIsReadable()) {
            throw new \Exception(
                sprintf('File %s does not exist or cannot be read', $this->filePath)
            );
        }

        // Check that the file is an audio file.
        if (!$checker->checkIsAudio()) {
            throw new \Exception(
                sprintf('File %s is not an audio file', $this->filePath)
            );
        }

        // Build the tag.
        $this->buildTag();
        // var_dump($this->tag->get());
    }

    /**
     * Check for the existence of an id3v1 tag.
     *
     * @return bool
     */
    public function checkForV1()
    {
        $this->buildTag();
        var_dump($this->tag->get());
    }

    /**
     * Check for the existence of an id3v2 tag.
     *
     * @return bool
     */
    public function checkForV2()
    {
        return $this->inList('/^id3v2 tag info for/');
    }

    public function getTag()
    {
        $output = $this->getList();

        $tag = new Tag;
    }
    /**
     * Retrieve an id3v1 tag wrapper object.
     *
     * @return TagV1 $tag
     */
    public function getV1()
    {
    }

    /**
     * Retrieve an id3v2 tag wrapper object.
     *
     * @return TagV2 $tag
     */
    public function getV2()
    {
        // TODO: write logic here
    }

    /**
     * Delete the id3v1 tag from the file.
     *
     * @return bool
     */
    public function deleteV1()
    {
        return $this->deleteCommand('--delete-v1', '/id3v1 stripped.$/');
    }

    /**
     * Delete the id3v2 tag from the file.
     *
     * @return bool
     */
    public function deleteV2()
    {
        return $this->deleteCommand('--delete-v2', '/id3v2 stripped.$/');
    }

    /**
     * Delete both tags from the file.
     *
     * @return bool
     */
    public function deleteTags()
    {
        return $this->deleteCommand(
            '--delete-all',
            '/id3v1 and v2 stripped.$/'
        );
    }

    /**
     * Convert the id3v1 tag to id3v2.
     */
    public function convertV1ToV2()
    {
        // TODO: write logic here
    }

    /**
     * Write a tag to the file.
     *
     * @param TagInterface $tag
     *   The tag object to write to the file.
     */
    public function writeTag(TagInterface $tag)
    {
        // TODO: write logic here
    }

    protected function buildTag()
    {
        $output = $this->getList();
        $this->tag = new Tag();
        $this->tag->parseListOutput($output);
    }

    /**
     * Get the results of the --list command.
     */
    protected function getList()
    {
        $arguments = array(
            '--list',
            $this->filePath,
        );

        return $this->bin->exec($arguments);
    }

    /**
     * Search the results of the --list command with a regular expression.
     */
    protected function inList($regex)
    {
        $list = $this->getList();

        $filtered = array_filter($list, function ($v) use ($regex) {
            return preg_match($regex, $v);
        });

        return (bool) $filtered;
    }

    protected function deleteCommand($cmd, $regex)
    {
        $arguments = array(
            $cmd,
            $this->filePath,
        );

        $output = $this->bin->exec($arguments);

        return (bool) preg_match($regex, $output[0]);
    }
}

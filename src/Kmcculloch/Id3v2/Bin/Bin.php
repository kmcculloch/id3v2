<?php

namespace Kmcculloch\Id3v2\Bin;

/**
 * Service wrapper for the id3v2 binary.
 */
class Bin
{
    protected $bin;
    protected $executor;

    /**
     * Constructor.
     *
     * @param Finder   $finder
     *   A finder object, used to locate the binary on the system.
     * @param Executor $executor
     *   An executor object, used to pass commands to the binary.
     */
    public function __construct(Finder $finder, Executor $executor)
    {
        // Use the finder to store the path to the id3v2 binary.
        $this->bin = $finder->locate('id3v2');

        // Check that the finder could locate id3v2.
        if ($this->bin == null) {
            throw new \Exception('Could not locate id3v2 executable');
        }

        // Check that finder result is, in fact, a file.
        if (!file_exists($this->bin)) {
            throw new \Exception(
                sprintf('File %s does not exist', $this->bin)
            );
        }

        // Check that the file is executable.
        if (!is_executable($this->bin)) {
            throw new \Exception(
                sprintf('File %s is not executable', $this->bin)
            );
        }

        // Store the executor helper object so that we can execute commands.
        $this->executor = $executor;

        // Check that we have a tested version of id3v2 installed.
        $output = $this->exec(array('--version'));
        if ($output[0] != $this->bin.' 0.1.12') {
            throw new \Exception(
                'Only id3v2 version 0.1.12 is supported at this time.'
            );
        }
    }

    /**
     * Pass a command along to the executor.
     *
     * @param array $arguments
     *   An ordered sequence of inputs to be strung together into a
     *   shell command.
     *
     * @return array
     *   The output array from PHP's exec() command.
     */
    public function exec(array $arguments)
    {
        // Prepend the binary to the arguments array.
        array_unshift($arguments, $this->bin);

        // Merge the arguments array into a command string.
        $command = implode($arguments, ' ');

        // Run the command and return the result.
        return $this->executor->exec($command);
    }
}

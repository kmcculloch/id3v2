<?php

namespace Kmcculloch\Id3v2\Tag;

use Kmcculloch\Id3v2\Bin\Bin;

/**
 * Tag wrapper object.
 */
class Tag
{
    protected $filePath;
    protected $bin;

    protected $whichTag;
    protected $whichProperty;

    protected $v1 = array();
    protected $v2 = array();

    /**
     * Constructor: Parse id3v2 list output into tag storage.
     *
     * @param FileChecker $fileChecker
     *   A file checker object to validate the audio file.
     * @param Bin         $bin
     *   The wrapper object for the binary id3v2 executable.
     */
    public function __construct(FileChecker $fileChecker, Bin $bin)
    {
        $this->filePath = $fileChecker->getPath();
        $this->bin = $bin;

        // Check that we're looking at a real file.
        if (!$fileChecker->checkIsReadable()) {
            throw new \Exception(
                sprintf('File %s does not exist or cannot be read', $this->filePath)
            );
        }

        // Check that the file is an audio file.
        if (!$fileChecker->checkIsAudio()) {
            throw new \Exception(
                sprintf('File %s is not an audio file', $this->filePath)
            );
        }

        // Build the tag.
        $this->buildTag();
    }

    protected function buildTag()
    {
        $arguments = array(
            '--list',
            $this->filePath,
        );

        $output = $this->bin->exec($arguments);

        $id3v1 = array();
        $id3v2 = array();
        $useArray = '';

        foreach ($output as $line) {
            if (preg_match('/^id3v1 tag info for/', $line)) {
                // Tag info follows. Start using the id3v1 array.
                $useArray = 'id3v1';
            } elseif (preg_match('/^id3v2 tag info for/', $line)) {
                // Tag info follows. Start using the id3v2 array.
                $useArray = 'id3v2';
            } elseif (preg_match('/No ID3(v1|v2|) tag$/', $line)) {
                // No tag info. Do nothing.
            } elseif ($useArray) {
                // This line belongs to one of the tag arrays.
                ${$useArray}[] = $line;
            }
        }

        // Parse the id3v1 input and store it.
        if ($id3v1) {
            list($start, $song, $artist, $end) = preg_split('/Title  :(.*)Artist:(.*)/', $id3v1[0], null, PREG_SPLIT_DELIM_CAPTURE);
            $this->v1['song'] = trim($song);
            $this->v1['artist'] = trim($artist);

            list($start, $album, $year, $genre, $end) = preg_split('/Album  :(.*)Year:(.*), Genre: .* \((\d*)\)/', $id3v1[1], null, PREG_SPLIT_DELIM_CAPTURE);
            $this->v1['album'] = trim($album);
            $this->v1['year'] = trim($year);
            $this->v1['genre'] = trim($genre);

            list($start, $comment, $track, $end) = preg_split('/Comment:(.*)Track:(.*)/', $id3v1[2], null, PREG_SPLIT_DELIM_CAPTURE);
            $this->v1['comment'] = trim($comment);
            $this->v1['track'] = trim($track);
        }

        // Parse the id3v2 input and store it.
        if ($id3v2) {
            foreach ($id3v2 as $line) {
                list($frame, $value) = preg_split('/([A-Z0-9]{3,4}) .*?: (.*)/', $line, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

                if ($frame == 'COMM') {
                    list($value) = preg_split('/\(.*\)\[.*\]: (.*)/', $value, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                } elseif ($frame == 'TCON') {
                    list($value) = preg_split('/(.*) \(\d*\)/', $value, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                }
                $this->v2[$frame] = $value;
            }
        }
    }

    /**
     * Magic __get() method.
     *
     * @param string $name
     *   Either a tag type ('v1, 'v2') or a tag property name ('artist', 'TALB',
     *   etc.).
     *
     * @return Tag
     *   A reference to this object so that we can chain property references.
     */
    public function __get($name)
    {
        if (in_array($name, array('v1', 'v2'))) {
            return $this->getTag($name);
        } else {
            return $this->getProperty($name);
        }
    }

    /**
     * Property chain-end get() method.
     *
     * @return mixed
     *   A single property value string or an array of properties.
     */
    public function get()
    {
        // Fetch tag and property request values.
        $tag = $this->whichTag;
        $property = $this->whichProperty;

        // Reset tag and property request storage.
        $this->whichTag = null;
        $this->whichProperty = null;

        // When no tag request is set, we default to v2 if it contains data.
        if (!$tag) {
            $tag = !empty($this->v2) ? 'v2' : 'v1';
        }

        // When no property request is set, we return the entire tag array.
        if (!$property) {
            return $this->{$tag};
        }

        // Make sure the requester has specified a valid property.
        if (!$this->validateProperty($property)) {
            throw new \Exception(
                sprintf('Invalid property: %s', $property)
            );
        }

        // If we're dealing with v2 tags, translate regular property names
        // (artist, song) into v2 frame identifiers.
        if ($tag == 'v2') {
            $property = $this->v2Property($property);
        }

        // Return the property if it is set, or an empty string.
        if (array_key_exists($property, $this->{$tag})) {
            return $this->{$tag}[$property];
        } else {
            return '';
        }
    }

    /**
     * Indicate which tag is requested and return this object to allow
     * property request chaining.
     */
    protected function getTag($name)
    {
        $this->whichTag = $name;

        return $this;
    }

    /**
     * Indicate which property is requested and return this object to allow
     * property request chaining.
     */
    protected function getProperty($name)
    {
        $this->whichProperty = $name;

        return $this;
    }

    /**
     * Translate regular property names into their id3v2 equivalent.
     */
    protected function v2Property($property)
    {
        $properties = array(
            'artist' => 'TPE1',
            'album' => 'TALB',
            'song' => 'TIT2',
            'comment' => 'COMM',
            'genre' => 'TCON',
            'year' => 'TYER',
            'track' => 'TRCK',
        );

        if (array_key_exists($property, $properties)) {
            return $properties[$property];
        } else {
            return $property;
        }
    }

    /**
     * Check a property name against a list of valid values.
     */
    protected function validateProperty($property)
    {
        $properties = array(
            'artist',
            'album',
            'song',
            'comment',
            'genre',
            'year',
            'track',
            'AENC',
            'APIC',
            'COMM',
            'COMR',
            'ENCR',
            'EQUA',
            'ETCO',
            'GEOB',
            'GRID',
            'IPLS',
            'LINK',
            'MCDI',
            'MLLT',
            'OWNE',
            'PRIV',
            'PCNT',
            'POPM',
            'POSS',
            'RBUF',
            'RVAD',
            'RVRB',
            'SYLT',
            'SYTC',
            'TALB',
            'TBPM',
            'TCOM',
            'TCON',
            'TCOP',
            'TDAT',
            'TDLY',
            'TENC',
            'TEXT',
            'TFLT',
            'TIME',
            'TIT1',
            'TIT2',
            'TIT3',
            'TKEY',
            'TLAN',
            'TLEN',
            'TMED',
            'TOAL',
            'TOFN',
            'TOLY',
            'TOPE',
            'TORY',
            'TOWN',
            'TPE1',
            'TPE2',
            'TPE3',
            'TPE4',
            'TPOS',
            'TPUB',
            'TRCK',
            'TRDA',
            'TRSN',
            'TRSO',
            'TSIZ',
            'TSRC',
            'TSSE',
            'TXXX',
            'TYER',
            'UFID',
            'USER',
            'USLT',
            'WCOM',
            'WCOP',
            'WOAF',
            'WOAR',
            'WOAS',
            'WORS',
            'WPAY',
            'WPUB',
            'WXXX',
        );

        return in_array($property, $properties);
    }
}

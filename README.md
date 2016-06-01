## Usage

``` php
<?php

// Create an id3v2 service wrapper. This will find and authenticate the
// binary, which usually lives at /usr/bin/id3v2.
$id3v2 = new Kmcculloch\Id3v2\Id3v2();

// Fetch a id3v1/id3v2 tag object for a music file.
$tag = $id3v2->get('/home/user/music/my_song.mp3');

// Get a field from the id3v1 tag. The field names correspond to id3v2
// arguments.
$artist  = $tag->v1->artist->get();
$album   = $tag->v1->album->get();
$song    = $tag->v1->song->get();
$comment = $tag->v1->comment->get();
$genre   = $tag->v1->genre->get();
$year    = $tag->v1->year->get();
$track   = $tag->v1->track->get();

// Get a field from the id3v2 tag. You can use the same field names as above.
$artist  = $tag->v2->artist->get();

// You can also manipulate the v2 tag using the proper id3v2 v2.3.0 frame
// names.
$artist  = $tag->v2->TPE1->get();
$album   = $tag->v2->TALB->get();
$song    = $tag->v2->TIT2->get();
$comment = $tag->v2->COMM->get();
$genre   = $tag->v2->TCON->get();
$year    = $tag->v2->TYER->get();
$track   = $tag->v2->TRCK->get();

// If you don't specify the tag version, you'll get the v2 value if it is set,
// or the v1 value otherwise.
$tag->artist->get();

// Save your changes back to the music file.
$id3v2->write($tag);
```

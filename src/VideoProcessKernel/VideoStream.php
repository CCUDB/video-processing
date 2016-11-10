<?php
/**
 * Video Stream interface
 *
 * @author FATESAIKOU
 */

namespace Video;

include_once 'Includes.php';

class VideoStream
{
    private $construct_str = "VideoStream Constructed";
    function __construct(){
        $content = new VideoContent();

        echo "$this->construct_str\n";
    }
}


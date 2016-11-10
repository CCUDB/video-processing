<?php
/**
 * Video Content manager
 *
 * @author FATESAIKOU
 */

namespace Video;

include_once 'Includes.php';

class VideoContent
{
    private $construct_str = "VideoContent Constructed";

    function __construct(){
        echo "$this->construct_str\n";
    }
} 

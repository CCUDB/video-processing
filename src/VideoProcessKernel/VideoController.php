<?php
/**
 * Video Controller
 *
 * @author FATESAIKOU
 */
 
include_once 'Includes.php';

use Video\VideoStream;
use Video\VideoContent;

/*$action = $_GET['action'];

if ($action === "play") {
    $video_stream_obj = new VideoStream('[SumiSora&CASO][Sansyasanyou][01][GB][720p].mp4');
    $video_stream_obj->start();
} else {
    //echo "Go Fuck U Self.";
}*/

$video_stream_obj = new VideoStream('[SumiSora&CASO][Sansyasanyou][01][GB][720p].mp4');
$video_stream_obj->start();

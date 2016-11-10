<?php
/**
 * Video Controller
 *
 * @author FATESAIKOU
 */
 
include_once 'Includes.php';

use Video\VideoStream;
use Video\VideoContent;

$action = $_GET['action'];

if ($action === "play") {
    $play_info = [
        'video_name' => $_GET['video_name'],
        'current-time' => $_GET['current_time']
    ];

    $video_stream_obj = new VideoStream($play_info);
    $video_stream_obj->start();
} else {
    echo "Go Fuck U Self.";
}

$video_content = new VideoContent('[SumiSora&CASO][Sansyasanyou][01][GB][720p].mp4');
$video_content->dumpVars();

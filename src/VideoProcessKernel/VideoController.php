<?php
/**
 * Video Controller
 *
 * @author FATESAIKOU
 */
 
include_once 'Includes.php';

use Video\VideoStream;

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

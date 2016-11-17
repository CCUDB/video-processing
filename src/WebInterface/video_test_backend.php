<?php
/**
 * This is a test backend for video-processing-kernel
 *
 * @author FATESAIKOU
 */
include_once '../Includes.php';

use \Video\VideoStream;


$action = $_GET['action'];

switch ($action) {
    case 'play':    // play a video
        //$video_name = $_GET['video_name'];
        $video_name = '[SumiSora&CASO][Sansyasanyou][01][GB][720p].mp4';

        $video_stream_obj = new VideoStream($video_name);
        $video_stream_obj->start();
        break;
    case 'list':    // list all video
        break;
    case 'upload':  // upload a video
        break;
    case 'update':  // update video meta
        break;
    case 'delete':  // delete a video record
        break;
}

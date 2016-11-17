<?php
/**
 * Video Stream interface
 *
 * @author FATESAIKOU
 */

namespace Video;

include_once '../Includes.php';

class VideoStream
{
    private $video_name;
    private $video_size;
    private $video_start;
    private $video_end;

    private $conn;

    private $video_content_obj;

    function __construct($video_name, $options = []) {
        $this->video_name = $video_name;
        $this->video_size = filesize(VIDEO_STORAGE . '/' . $video_name);

        $this->conn = \r\connect(DB_HOST, DB_PORT);
        $this->video_content_obj = new VideoContent($video_name, 0);
    }

    private function rdbInit() {
        ;
    }

    private function setHeader() {
        ob_get_clean();
        ob_start();
        header('Content-type: video/mp4');
        header('Cache-Control: max-age=2592000, public');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time()+2592000) . ' GMT');

        $this->video_start = 0;
        $this->video_end = $this->video_size - 1;
        header("Accept-Ranges: 0-$this->video_end");

        if (isset($_SERVER['HTTP_RANGE'])) {
            list($c_start, $c_end) = [$this->video_start, $this->video_end];

            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);

            // If Format Error, Return.
            if (strpos($range, ',') !== false) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $this->video_start-$this->video_end/$this->video_size");
                exit;
            }

            // Check Range
            if ($range == '-') {
                $c_start = $this->video_size;
            } else {
                $range = explode('-', $range);
                $c_start = $range[0];

                $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $c_end;
            }
            $c_end = min($c_end, $this->video_end);

            // If start/end Error, Return.
            if ($c_start > $c_end || $c_start > ($this->video_size - 1) || $c_end > $this->video_size) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $this->video_start-$this->video_end/$this->video_size");
                exit;
            }

            $this->video_start = $c_start;
            $this->video_end = $c_end;
            $content_length = $this->video_end - $this->video_start + 1;
            $this->video_content_obj->setOffset($this->video_start);
            header("{$_SERVER['SERVER_PROTOCOL']} 206 Partial Content");
            header("Content-Length: $content_length");
            header("Content-Range: bytes $this->video_start-$this->video_end/$this->video_size");
        } else {
            header("Content-Length: $this->video_size");
        }
    }

    private function rdbStream() {
        set_time_limit(0);

        while (true) {
            $chunk = $this->video_content_obj->nextContent();
            if ($chunk === true) {
                break;
            }
            echo $chunk;
            ob_flush();
            flush();
        }
    }

    private function rdbEnd() {
        $this->conn->close();
    }

    public function start() {
        $this->rdbInit();
        $this->setHeader();
        $this->rdbStream();
        $this->rdbEnd();
    } 
}


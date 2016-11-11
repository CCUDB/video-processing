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
    private $real_file_path;
    private $file_name;
    private $file_id;
    private $counter; // from 1 to n

    private $conn;
    private $meta_table;
    private $content_table;
    

    function __construct($file_name, $offset = 0) {
        $this->conn = \r\connect(DB_HOST, DB_PORT);
        $this->meta_table = \r\db(DB_NAME)->table('video_meta');
        $this->content_table = \r\db(DB_NAME)->table('content_data');

        $this->real_file_path = VIDEO_STORAGE . '/' . $file_name;
        $this->file_name = $file_name;
        $this->file_id = $this->meta_table->get($this->real_file_path)->run($this->conn)['video_id'];
        $this->counter = (int)(ceil($offset / CHUNK_SIZE));
    }

    function __destruct() {
        $this->conn->close();
    }

    public function setOffset($offset) {
        $this->counter = (int)(ceil($offset / CHUNK_SIZE));
    }

    public function nextContent() {
        $content_id = $this->file_id + '_' + $this->counter;
        $content = $this->content_table->get($content_id)->run($this->conn);

        // If content not exist, create -> update. 
        if ($content === NULL) {
            // Create Record
            $this->content_table->insert([
                'id' => $content_id,
                'chunk' => NULL,
                'ctime' => time()
            ])->run($this->conn);

            // If fulled, delete some record.
            if ($this->content_table->count()->run($this->conn) > MAX_RECORD_NUM) {
                $this->content_table->orderBy('ctime')->limit(FREE_NUM)->delete()->run($this->conn);
            }

            // Read Data
            $data = file_get_contents(
                $this->real_file_path,
                NULL,
                NULL,
                CHUNK_SIZE * ($this->counter - 1),
                CHUNK_SIZE
            );

            // If there is no data to read, than set the insert-data content to True
            if ($data === NULL) {
                $insert_data = true;
            } else {
                $insert_data = \r\binary($data);
            }

            // Insert data content.
            $this->content_table->get($content_id)->update(['chunk' => $insert_data])->run($this->conn);
            
            // Reget content again.
            $content = $this->content_table->get($content_id)->run($this->conn);
        } else if ($content['chunk'] === NULL) {
            // If content is in updating, wait for it.
            $this->content_table->get($content_id)->changes()->run($this->conn)->next();

            // Reget content again.
            $content = $this->content_table->get($content_id)->run($this->conn);
        }

        // Counter Increment. 
        $this->counter ++;

        // Return data.
        return $content['chunk'];
    }

    public function dumpVars() {
        echo '<pre>';
        print_r([
            $this->real_file_path,
            $this->file_name,
            $this->file_id,
            $this->counter
        ]);
        echo '</pre>';
    }
} 

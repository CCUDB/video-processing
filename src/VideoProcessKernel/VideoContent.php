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
    private $file_offset;
    private $file_id;
    private $first_chunk_id;
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
        $this->file_offset = $offset;
        $this->file_id = $this->meta_table->get($this->real_file_path)->run($this->conn)['video_id'];
        $this->first_chunk_id = (int)(ceil($offset / CHUNK_SIZE));
        $this->counter = $this->first_chunk_id;
    }

    function __destruct() {
        $this->conn->close();
    }

    public function setOffset($offset) {
        $this->file_offset = $offset;
        $this->first_chunk_id = (int)(ceil($offset / CHUNK_SIZE));
        $this->counter = $this->first_chunk_id;
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
            $content = $this->getContent($content_id);
        } else if ($content['chunk'] === NULL) {
            // If content is in updating, wait for it.
            $this->content_table->get($content_id)->changes()->run($this->conn)->next();

            // Reget content again.
            $content = $this->getContent($content_id);
        } else if ($file->counter === $file->first_chunk_id) {
            $chunk_offset = $file_offset - ($this->first_chunk_id * CHUNK_SIZE);
            $content = $this->content_table->get($content_id)->slice($chunk_offset)->run($this->conn);
        }

        // Counter Increment. 
        $this->counter ++;

        // Return data.
        return $content['chunk'];
    }

    private function getContent($content_id) {
        if ($this->counter != $this->first_chunk_id) {
            return $this->content_table->get($content_id)->run($this->conn);
        } else {
            $chunk_offset = $file_offset - ($this->first_chunk_id * CHUNK_SIZE);
            return $this->content_table->get($content_id)->slice($chunk_offset)->run($this->conn);
        }
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

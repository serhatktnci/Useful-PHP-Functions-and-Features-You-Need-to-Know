<?php
/**
 * Created by PhpStorm.
 * User: serhat.ketenci
 * Date: 10.1.2016
 * Time: 03:53 PM
 */

class CURLReader {
    private $url;
    private $length = 8192;
    private $pos = 0;
    private $timeout = 60;

    public function __construct($url) {
        $this->url = $url;
    }

    public function setLength($length) {
        $this->length = $length;
    }

    public function setTimeout($timeout) {
        $this->timeout = $timeout;
    }

    public function setPos($pos) {
        $this->pos = $pos;
    }

    public function getPos() {
        return $this->pos;
    }

    public function read() {
        $part = $this->getPart("0-1");

        // Check partial Support
        if ($part && strlen($part) === 2) {
            // Split data with curl
            $this->runPartial($local);
        } else {
            // Use stream copy
            $this->runNormal($local);
        }
    }

    public function download($local) {
        $part = $this->getPart("0-1");

        // Check partial Support
        if ($part && strlen($part) === 2) {
            // Split data with curl
            $this->runPartial($local);
        } else {
            // Use stream copy
            $this->runNormal($local);
        }
    }

    private function runNormal($local) {
        $in = fopen($this->url, "r");
        $out = fopen($local, 'w');
        $pos = ftell($in);
        while(($pos = ftell($in)) <= $this->pos) {
            $n = ($pos + $this->length) > $this->length ? $this->length : $this->pos;
            fread($in, $n);
        }
        $this->pos = stream_copy_to_stream($in, $out);
        return $this->pos;
    }

    private function runPartial($local) {
        $i = $this->pos;
        $fp = fopen($local, 'w');
        fseek($fp, $this->pos);
        while(true) {
            $data = $this->getPart(sprintf("%d-%d", $i, ($i + $this->length)));

            $i += strlen($data);
            fwrite($fp, $data);

            $this->pos = $i;
            if ($data === - 1)
                throw new Exception("File Corupted");

            if (! $data)
                break;
        }

        fclose($fp);
    }

    private function getPart($range) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RANGE, $range);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Request not Satisfiable
        if ($code == 416)
            return false;

        // Check 206 Partial Content
        if ($code != 206)
            return - 1;

        return $result;
    }





    /*
Set Headers
Get total size of file
Then loop through the total size incrementing a chunck size
*/
    function download($file,$chunks){
        set_time_limit(0);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-disposition: attachment; filename='.basename($file));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        header('Pragma: public');
        $size = get_size($file);
        header('Content-Length: '.$size);

        $i = 0;
        while($i<=$size){
            //Output the chunk
            get_chunk($file,(($i==0)?$i:$i+1),((($i+$chunks)>$size)?$size:$i+$chunks));
            $i = ($i+$chunks);
        }

    }

    //Callback function for CURLOPT_WRITEFUNCTION, This is what prints the chunk
    function chunk($ch, $str) {
        print($str);
        return strlen($str);
    }

    //Function to get a range of bytes from the remote file
    function get_chunk($file,$start,$end){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $file);
        curl_setopt($ch, CURLOPT_RANGE, $start.'-'.$end);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, 'chunk');
        $result = curl_exec($ch);
        curl_close($ch);
    }

    //Get total size of file
    function getSize($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        return intval($size);
    }
}
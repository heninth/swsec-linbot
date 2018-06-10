<?php
namespace App;
abstract class HTTP {

    public function GET($url, $header = []) {
        $ch = curl_init($url);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $header
        );
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        //echo $curl_errno;
        //echo $curl_error;
        curl_close($ch);
        return $response;
    }

    public function POST($url, $data, $header = []) {
        $ch = curl_init($url);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST            => 1,
            CURLOPT_POSTFIELDS     => $data,
            CURLOPT_HTTPHEADER     => $header
        );
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        //echo $curl_errno;
        //echo $curl_error;
        curl_close($ch);
        return $response;
    }
}


?>
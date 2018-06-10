<?php
namespace App\Action;
/*
can't use anymore.
*/
use App\HTTP;

class TranslateApi extends HTTP {
    private $baseUrl = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=';

    public function query($q, $source, $target) {
        $url = $this->baseUrl . $source . '&tl=' . $target . '&dt=t&q=' . urlencode($q); 
        $response = json_decode($this->GET($url));
        return $response[0][0][0];
    }
}

?>
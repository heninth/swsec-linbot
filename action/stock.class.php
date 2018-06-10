<?php
namespace App\Action;

use App\HTTP;

class StockApi extends HTTP {
    private $baseUrl = 'https://marketdata.set.or.th/';

    private function urlEndpoint($endpoint) {
        return $this->baseUrl . $endpoint;
    }

    function query($symbol) {
        $url = $this->urlEndpoint('mkt/stockquotation.do?symbol=' . $symbol);
        $response = $this->GET($url);

        $name = explode('<h3', $response);
        $name = explode('>', $name[1]);
        $name = explode('</h3' , $name[1]);
        $name = $name[0];

        $response = explode('<td>ล่าสุด</td>', $response);
        if ($response[0] == '') return 'ฉันไม่รู้จักหุ้น ' . $symbol;
        $response = explode('</td>', $response[1]);
        $response = explode('>', $response[0]);
        $price = (float) $response[1];
        if ($price == 0) {
            $price = (float) $response[3];
        }

        return $name . "\n" . 'ราคาล่าสุด ' . $price . ' บาท';
    }
}

?>
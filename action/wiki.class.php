<?php
namespace App\Action;

use App\HTTP;

class WikiApi extends HTTP {
    private $baseUrl = 'https://th.wikipedia.org/w/api.php?';
    private $tailUrl = '&format=json';

    private function urlEndpoint($endpoint) {
        return $this->baseUrl . $endpoint . $this->tailUrl;
    }

    function query($q) {
        $url = $this->urlEndpoint('action=opensearch&search=' . $q . '&limit=1');
        $response = json_decode($this->GET($url), true);
        //pushLog('wiki->q', var_export($q, true));
        //pushLog('wiki->opensearch', var_export($url, true));

        $url = $this->urlEndpoint('action=query&prop=extracts&exsentences=1&exsectionformat=plain&exintro&explaintext&redirects&titles=' . urlencode($response[1][0]));
        $response = json_decode($this->GET($url), true);
        //pushLog('wiki->query', var_export($response, true));
        
        $desc = current($response['query']['pages'])['extract'];
        $result = $desc;

        if (strpos($desc, $q . ' อาจหมายถึง') !== false) {
            $result = 'คุณหมายถึง ' . $q . ' ไหน';
        }

        if (strpos($desc, "\n") !== false) {
            $desc = explode("\n", $desc);
            $result = $desc[0];
        }
        if (!$result) $result = 'ฉันไม่รู้จัก ' . $q;

        return $result;
    }
}

?>
<?php
namespace App\Action;

use App\HTTP;

class DarkskyApi extends HTTP {
    private $baseUrl = 'https://api.darksky.net/forecast/';
    private $token;

    public function setToken(string $token) {
        $this->token = $token;
    }

    private function urlEndpoint($endpoint) {
        return $this->baseUrl . $this->token . '/' . $endpoint;
    }

    public function query($location) {
        $url = $this->urlEndpoint($location . '?exclude=flags,alerts,minutely,daily&units=si');
        $response = json_decode($this->GET($url));
        //$translate = new TranslateApi();
        //$data['currently']['summary'] = $translate->query($response->currently->summary, 'en', 'th');
        //$data['hourly']['summary'] = $translate->query($response->hourly->summary, 'en', 'th');
        return $response->currently->temperature . '°C' . " " . $response->currently->summary . "\n" . $response->hourly->summary;
    }
}

?>
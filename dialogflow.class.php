<?php
namespace App;

use App\HTTP;

class DialogflowApi extends HTTP {
    private $baseUrl = 'https://api.dialogflow.com/v1/';
    private $protocolVersion = '20170712';
    private $clientToken;

    public function setClientToken(string $clientToken) {
        $this->clientToken = $clientToken;
    }

    private function urlEndpoint($endpoint) {
        return $this->baseUrl . $endpoint . '?v=' . $this->protocolVersion;
    }

    public function query($query) {
        $query = str_replace(['คือ'], [' คือ '], $query);
        $url = $this->urlEndpoint('query');
        $header = [
            'Authorization: Bearer ' . $this->clientToken,
            'Content-Type: application/json; charset=utf-8',
        ];
        $data = [
            'query' => $query,
            'lang' => 'th',
            'sessionId' => '123456'
        ];

        //pushLog('dialogflow->query', var_export($data, true));
        $response = json_decode($this->POST($url, json_encode($data), $header));
        //pushLog('dialogflow->response', var_export($response, true));

        if ($response->status->code == '200' && $response->status->errorType == 'success') {
            return [
                'success' => true,
                'action' => $response->result->action,
                'parameters' => (array) $response->result->parameters,
                'speech' => $response->result->fulfillment->speech
            ];
        } else {
            return [
                'success' => false,
                'errorCode' => $response->status->code,
                'errorType' => $response->status->errorType,
                'errorDetails' => $response->status->errorDetails,
            ];
        }
    }
}

?>
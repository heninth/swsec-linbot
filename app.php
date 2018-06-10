<?php
require 'config.php';
require 'vendor/autoload.php';
require 'http.class.php';
require 'dialogflow.class.php';
//require 'action/translate.class.php';
require 'action/wiki.class.php';
require 'action/darksky.class.php';
require 'action/stock.class.php';
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\Exception\UnknownEventTypeException;
use LINE\LINEBot\Exception\UnknownMessageTypeException;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

//init api
$dialogflow = new \App\DialogflowApi();
$dialogflow->setClientToken($config['dialogflow_client_token']);
//$translate = new \App\Action\TranslateApi();
$wiki = new \App\Action\WikiApi();
$darksky = new \App\Action\DarkskyApi();
$darksky->setToken($config['darksky_token']);
$stock = new \App\Action\StockApi();

//die(var_export($dialogflow->query('hello')));
//linebot
$lineHttpClient = new CurlHTTPClient($config['linebot_access_token']);
$bot = new LINEBot($lineHttpClient, ['channelSecret' => $config['linebot_secret']]);

$httpRequestBody = file_get_contents('php://input'); // Request body string
$signature = $_SERVER['HTTP_X_LINE_SIGNATURE'];

if (empty($signature)) {
    header('HTTP/1.1 400 Bad Request');
    die();
}

// Check request with signature and parse request
try {
    $events = $bot->parseEventRequest($httpRequestBody, $signature);
} catch (InvalidSignatureException $e) {
    header('HTTP/1.1 400 Invalid signature');
    die();
} catch (UnknownEventTypeException $e) {
    header('HTTP/1.1 400 Unknown event type has come');
    die();
} catch (UnknownMessageTypeException $e) {
    header('HTTP/1.1 400 Unknown message type has come');
    die();
} catch (InvalidEventRequestException $e) {
    header('HTTP/1.1 400 Invalid event request');
    die();
}

foreach ($events as $event) {
    if ($event instanceof MessageEvent) {
        if ($event instanceof TextMessage) {
            $reqText = $event->getText();
            $response = $dialogflow->query($reqText);
            if ($response['success']) {
                if ($response['speech'] != '') {
                    reply($bot, $event, $response['speech']);
                } else {
                    switch($response['action']) {
                        case 'action.wiki':
                            reply($bot, $event, $wiki->query($response['parameters']['query']));
                            break;
                        case 'action.weather':
                            reply($bot, $event, $darksky->query('13.625,100.417'));
                            break;
                        case 'action.stock':
                            reply($bot, $event, $stock->query($response['parameters']['symbol']));
                            break;
                        default:
                        reply($bot, $event, "ฉันไม่เข้าใจ " . $response['action']);
                    }
                }
            } else {
                reply($bot, $event, "เกิดข้อผิดพลาด");
            }
        } else { continue; }
    } else { continue; }
}

function reply($bot, $event, $text) {
    $resp = $bot->replyText($event->getReplyToken(), $text);
}

function pushLog ($logger, $message) {
    $data = file_get_contents('log');
    file_put_contents('log', $data . "\n-- Start " . $logger ." --\n" . $message . "\n-- End " . $logger ." --\n");
}


//$response = $translate->query('Humid and Partly Cloudy.', 'en', 'th');
//$response = $wiki->query($argv[1]);
//$response = $darksky->query('13.625,100.417');
//$response = $stock->query($argv[1]);

?>
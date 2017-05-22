<?php
/**
 * @author Adam Englander <adamenglander@yahoo.com>
 * @copyright 2017 Adam L. Englander. See project license for usage.
 */

require_once __DIR__ . '/keys.php';

use Amp\Artax\Client as ArtaxHttpClient;
use PeeHaa\AsyncTwitter\Api\Client\Client as ApiClient;
use PeeHaa\AsyncTwitter\Api\Request\Stream\Filter;
use PeeHaa\AsyncTwitter\Credentials\AccessToken;
use PeeHaa\AsyncTwitter\Credentials\Application;
use PeeHaa\AsyncTwitter\Http\Artax as ApiHttpClient;

$host = (new Aerys\Host)->expose('0.0.0.0', 8080);

$host->use(Aerys\Root(__DIR__ . "/public/html"));

$ws = new class implements \Aerys\Websocket
{
    /** @var  \Aerys\Websocket\Endpoint */
    private $ws;

    public function onStart(Aerys\Websocket\Endpoint $endpoint)
    {
        $this->ws = $endpoint;
    }

    public function onHandshake(Aerys\Request $request, Aerys\Response $response) { }

    public function onOpen(int $clientId, $handshakeData) { }

    public function onData(int $clientId, Aerys\Websocket\Message $msg) { }

    public function onClose(int $clientId, int $code, string $reason) { }

    public function onStop() { }

    /**
     * Broadcast a message to all connected Websocket clients
     * @param string $message
     */
    public function send(string $message)
    {
        $this->ws->send(null, $message);
    }
};
$host->use(Aerys\websocket($ws));

// Create application credentials object for Twitter client
$applicationCredentials = new Application($key, $secret);

// Create token credentials object for Twitter client
$accessToken = new AccessToken($token, $tokenSecret);

// Create a new API HTTP Client with the Artax HTTP client
$httpClient = new ApiHttpClient(new ArtaxHttpClient());

// Create an API client with the API HTTP client and credentials
$apiClient = new ApiClient($httpClient, $applicationCredentials, $accessToken);

// Make a filtered user stream request that tracks keywords
$request = (new Filter)->track(["phptek", "phptek2017"]);

// Have Amp run the following
\Amp\immediately(function () use ($apiClient, $request, $ws) {

    // Open a stream
    $stream = yield $apiClient->request($request);

    // Loop through stream responses until there are none
    while (null !== $message = yield $stream->read()) {

        // Send the user's screen name to connected users
        $ws->send(json_encode(['user' => $message['user']['screen_name'], 'message' => $message['text']]));
    }
});

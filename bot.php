<?php declare(strict_types = 1);
/**
 * @author Adam Englander <adamenglander@yahoo.com>
 * @copyright 2017 Adam L. Englander. See project license for usage.
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/keys.php';

use Amp\Artax\Client as ArtaxHttpClient;
use PeeHaa\AsyncTwitter\Api\Request\Stream\Filter;
use PeeHaa\AsyncTwitter\Credentials\AccessToken;
use PeeHaa\AsyncTwitter\Credentials\Application;
use PeeHaa\AsyncTwitter\Api\Client\Client as ApiClient;
use PeeHaa\AsyncTwitter\Http\Artax as ApiHttpClient;

// Create application credentials object for Twitter client
$applicationCredentials = new Application($key, $secret);

// Create token credentials object for Twitter client
$accessToken = new AccessToken($token, $tokenSecret);

// Create a new API HTTP Client with the Artax HTTP client
$httpClient = new ApiHttpClient(new ArtaxHttpClient());

// Create an API client with the API HTTP client and credentials
$apiClient = new ApiClient($httpClient, $applicationCredentials, $accessToken);

// Make a filtered user stream request that tracks keywords
$request = (new Filter)
    ->language(['en'])
    ->track(["phptekbot", "phptekbots"]);

// Have Amp run the following
\Amp\run(function () use ($apiClient, $request, $twitterUser) {

    // Open a stream
    $stream = yield $apiClient->request($request);

    // Loop through stream responses until there are none
    while (null !== $message = yield $stream->read()) {

        // If the message is from me, ignore it
        if ($message['user']['screen_name'] == $twitterUser) {
            print("SKIPPING MY OWN MESSAGE");
        } else {
            // Print the message
            print(sprintf(
                "Responding to (%s) who said %s",
                $message['user']['screen_name'],
                $message['text']
            ));

            // Create a message
            $reply = new Update(
                sprintf("Thanks for thinking about us @%s. #phptek2017", $message['user']['screen_name'])
            );

            // Responding the the current message
            $reply->replyTo($message['id']);

            // Send an API request for the reply
            $apiClient->request($reply);
        }
        print("\n==================\n");
    }
});

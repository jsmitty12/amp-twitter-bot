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
    ->track(["pkptek", "phptek2017"]);

// Have Amp run the following
\Amp\run(function () use ($apiClient, $request) {

    // Open a stream
    $stream = yield $apiClient->request($request);

    // Loop through stream responses until there are none
    while (null !== $message = yield $stream->read()) {

        // Print the user's screen name and update text
        print(sprintf(
            "(%s)\n%s",
            $message['user']['screen_name'],
            $message['text']
        ));
        print("\n==================\n");
    }
});

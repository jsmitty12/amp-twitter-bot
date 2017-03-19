<?php
/**
 * @author Adam Englander <adamenglander@yahoo.com>
 * @copyright 2017 Adam L. Englander. See project license for usage.
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/keys.php';

use Amp\Artax\Client as ArtaxHttpClient;
use PeeHaa\AsyncTwitter\Credentials\AccessToken;
use PeeHaa\AsyncTwitter\Credentials\Application;
use PeeHaa\AsyncTwitter\Api\Client\Client as ApiClient;
use PeeHaa\AsyncTwitter\Api\Request\Status\UserTimeline;
use PeeHaa\AsyncTwitter\Http\Artax as ApiHttpClient;

try {
    // Create application credentials object for Twitter client
    $applicationCredentials = new Application($key, $secret);

    // Create token credentials object for Twitter client
    $accessToken = new AccessToken($token, $tokenSecret);

    // Create a new API HTTP Client with the Artax HTTP client
    $httpClient = new ApiHttpClient(new ArtaxHttpClient());

    // Create an API client with the API HTTP client and credentials
    $apiClient = new ApiClient($httpClient, $applicationCredentials, $accessToken);

    // Create a user timeline request
    $request = new UserTimeline();

    // Make the asynchronous request
    $promise = $apiClient->request($request);

    // Wait for the response
    $response = Amp\wait($promise);

    // Print a pretty JSON response
    print(json_encode($response, JSON_PRETTY_PRINT));
} catch (Amp\Artax\ClientException $error) {
    // If something goes wrong the Promise::wait() call will throw the relevant
    // exception. The Client::request() method itself will never throw.
    echo $error;
}

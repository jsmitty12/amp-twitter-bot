# Twitter Bot

Twitter bot using the AMP asynchronous framework for PHP.

## Requirements

### PHP 7.0+

Although the version of AMPHP in this example does not require
PHP 7.x, the Twitter client does require it. It was way too much
work to build a 5.6 Twitter client for streams. So I went with 
requiring PHP 7.x.

### PHP intl extension

The intl extension is required as the Twitter API supports 
multi-byte characters.

### Composer

If you are not familiar with Composer, head to 
https://getcomposer.org/doc/00-intro.md and install Composer.
Once installed, spend a few moments reflecting on
all you could have accomplished had you done this sooner.

## Installation

1. Clone the repo 
    ```git clone https://github.com/aenglander/amp-twitter-bot.git```
1. Change to directory `cd amp-twitter-bot`
1. Composer install `./composer.phar install`
1. Create a Twitter account. You will probably want an separate 
    account for your bot.
   * [Create a new account](https://twitter.com/signup)
   * [Add an additional account](https://support.twitter.com/articles/20169956)
1. Create a new app at Twitter Dev logged in as your bot 
    account: [https://apps.twitter.com/app/new](https://apps.twitter.com/app/new)

## Configuration

Copy the [./keys.php.example](./keys.php.example) file to 
`keys.php` and update the values as instructed.

## Running Examples

### User Timeline

The User Timeline example utilizes the Twitter REST API to make 
a single request.

Run ``` php timeline.php``` to run the example. It
will print out a pretty formatted JSON string of the 
result.

### Stream

The Stream example will connect to the Twitter User Stream API
and print out every Twitter update matching the filter criteria.

Run ``` php stream.php``` to run the example. It will
print out the twitter username and update text. You can press
```<CTRL><c>``` at any point to end the stream.

### Twitter Bot

The Twitter Bot will connect to the Twitter User Stream API
and and respond to every update matching the filter criteria
that was not from itself. You can press
```<CTRL><c>``` at any point to end the stream.

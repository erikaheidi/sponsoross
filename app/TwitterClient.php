<?php


namespace App;


use Minicli\Curly\Client;

class TwitterClient
{
    protected $api_token;
    protected $client;

    static $TWITTER_API = 'https://api.twitter.com/2';

    public function __construct($api_token)
    {
        $this->api_token = $api_token;
        $this->client = new Client();
    }

    public function getTweetInfo($tweet_id)
    {
        $query_path = sprintf("%s/tweets?ids=%s&tweet.fields=author_id,created_at,conversation_id", self::$TWITTER_API, $tweet_id);

        return $this->client->get($query_path, $this->getHeaders());
    }

    public function getConversation($conversation_id, $max_results = 10, $next = null)
    {
        $query_path = sprintf("%s/tweets/search/recent?query=conversation_id:%s&tweet.fields=author_id,created_at,entities&max_results=%s", self::$TWITTER_API, $conversation_id, $max_results);
        return $this->client->get($query_path, $this->getHeaders());
    }

    public function getHeaders()
    {
        return [
            "User-Agent: wios-bot v0.1",
            'Authorization: Bearer ' . $this->api_token
        ];
    }
}
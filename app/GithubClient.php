<?php


namespace App;

use Minicli\Curly\Client;

class GithubClient
{
    protected string $api_token;

    public Client $client;

    static string $API_GRAPH = 'https://api.github.com/graphql';

    public function __construct($api_token)
    {
        $this->api_token = $api_token;
        $this->client = new Client();
    }

    public function getUserInfo($username)
    {
        $query = sprintf('
query { 
  user(login: "%s") {
    name,
    login,
    avatarUrl,
    bio,
    location,
    twitterUsername,
    websiteUrl,
    sponsorsListing {
      fullDescription,
      shortDescription
    },
    topRepositories(first: 10, orderBy: { field: STARGAZERS, direction: DESC}) {
      nodes {
        name,
        url,
        homepageUrl,
        stargazerCount,
        primaryLanguage {
          name,
        }
      }
    }
  }
}', $username);
        $response = $this->githubQuery($query);
        if ($response['code'] === 200) {
            //var_dump($response);
            $result = json_decode($response['body'], 1);
            return $result['data']['user'];
        }

        return false;
    }

    public function isSponsorable($username)
    {
        $query = sprintf('
query { 
  user(login: "%s") {
    hasSponsorsListing
  }
}', $username);

        $response = $this->githubQuery($query);
        //var_dump($response);
        if ($response['code'] === 200) {
            $result = json_decode($response['body'], 1);
            return $result['data']['user']['hasSponsorsListing'];
        }

        return false;
    }

    protected function githubQuery($query, $params = [])
    {
        $headers =  [
            "User-Agent: wios-bot v0.1",
            "Content-Type: application/json",
            "Authorization: bearer $this->api_token"
        ];


        return $this->client->post(self::$API_GRAPH, [ 'query' => $query, 'variables' => $params ], $headers);
    }
}
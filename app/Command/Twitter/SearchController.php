<?php

namespace App\Command\Twitter;

use App\GithubClient;
use App\TwitterClient;
use Librarian\Content;
use Minicli\Command\CommandController;
use Minicli\Curly\Client;

class SearchController extends CommandController
{

    public function handle()
    {
        $twitterId = $this->hasParam('id') ? $this->getParam('tweet') : '1384503318324649985';
        $this->getPrinter()->display(sprintf("Looking for profiles in the reply thread %s", $twitterId));

        $twitter = new TwitterClient($this->getApp()->config->TWITTER_BEARER_TOKEN);
        $github = new GithubClient($this->getApp()->config->GITHUB_API_TOKEN);

        $tweetInfo = $twitter->getTweetInfo($twitterId);

        if ($tweetInfo['code'] !== 200) {
            $this->getPrinter()->error('There was an error when making the request.');
            return 1;
        }

        $response = json_decode($tweetInfo['body'], 1);
        $conversation_id = $response['data'][0]['conversation_id'];

        $conversation_query = $twitter->getConversation($conversation_id, 50);

        if ($conversation_query['code'] !== 200) {
            $this->getPrinter()->error('There was an error when making the request for the conversation tweets.');
            return 1;
        }

        $response = json_decode($conversation_query['body'], 1);
        $data_path = $this->getApp()->config->profile_dir;

        foreach ($response['data'] as $tweet) {
            //$this->getPrinter()->info($tweet['text']);

            //find GH URLs
            if (isset($tweet['entities']['urls'])) {
                foreach ($tweet['entities']['urls'] as $url) {
                    $expanded = $url['expanded_url'];

                    //check if it's a github url to obtain username
                    if (preg_match("/(?:github\.com)\/([\S]+)/", $expanded, $matches)) {
                        //$this->getPrinter()->success("Found GitHub URL: $expanded");
                        $user = $matches[1];
                        $user = str_replace('sponsors/', '', $user);

                        $this->getPrinter()->success("Found profile: $user");

                        $this->getApp()->runCommand(['librarian', 'import', 'github', $user]);
                    }
                }
            }
        }

        $this->getPrinter()->success('Finished.');
        return 0;
    }
}
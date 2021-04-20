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

                        if ($github->isSponsorable($user)) {
                            $this->getPrinter()->success("User $user is sponsorable! yay!", 1);

                            $profile_path = $data_path . '/' . $user . '.md';
                            //checks if user already exists, in this case skips
                            if (is_file($profile_path)) {
                                $this->getPrinter()->info("User is already in the list, skipping...");
                                continue;
                            }

                            //make new request to obtain user info
                            $user_info = $github->getUserInfo($user);
                            $content = new Content($this->buildUserPage($user_info));
                            $content->save($profile_path);
                            $this->getPrinter()->info("Saved user info.");
                        }
                    }
                }
            }
        }

        $this->getPrinter()->success('Finished.');
        return 0;
    }

    public function buildUserPage(array $user_data)
    {
        $tags = [];
        $projects = [];

        //generate tags for this user's top repo languages
        foreach ($user_data['topRepositories']['nodes'] as $repository) {
            $projects[] = $repository;
            if (array_key_exists('primaryLanguage', $repository)) {
                $tags[] = $repository['primaryLanguage']['name'];
            }
        }

        //front matter
        $content = "---\n";
        $content .= "title: " . $user_data['name'] . "\n";
        $content .= "description: " . $user_data['sponsorsListing']['shortDescription'] . "\n";
        $content .= "published: true\n";
        $content .= "user: " . $user_data['login'] . "\n";
        $content .= "cover_image: " . $user_data['avatarUrl'] . "\n";
        $content .= "tags: " . implode(', ', array_unique($tags)) . "\n";
        $content .= "---\n\n";

        $content .= $user_data['sponsorsListing']['fullDescription'];

        return $content;
    }
}
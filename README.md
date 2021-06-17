# Sponsor Underrepresented Open Source Creators

[https://sponsoropensource.dev](https://sponsoropensource.dev)

This project started as an initiative to bring more visibility for underrepresented open source developers enrolled on the [GitHub Sponsors Program](https://github.com/sponsors),
after [it came to my attention](https://twitter.com/erikaheidi/status/1379811139291389970) that it was almost impossible to find any woman to sponsor while browsing through the featured profiles in the program pages.

## Manifesto

Working on open source software is hard, and the GitHub Sponsors program was a great initiative intended to support open source creators globally.

Unfortunately, as with many other spaces in technology and software development, minority groups don't get the same visibility as man. This perpetuates the issue of visibility, or lack of.
As someone from an underrepresented group, you'll always look for people like you to feel safer in a group. That's a process known as identification. 
If you can't relate or identify with other people in a group that has large visibility and reach, it often feels like you cannot be part of that group, or that you are **not welcome**. 
The problem then is perpetuated, as a self-fulfilling prophecy. Moreover, such groups of privileged individuals tend to have unequal / unfair power of decision in matters that affect all of us, whether it's in the context of open source or private code. 

It is therefore important that we all feel represented. You can't aspire to be what you can't see.

## I Want To Be Part of the List
If you'd like to be part of the list, you can open an issue in this repository **providing your Github Sponsors URL**. At first, I pulled profiles from a [Twitter Thread](https://twitter.com/erikaheidi/status/1384503318324649985), but by opening an issue we'll be able to verify your username and historically ensure that you've **opted in**. Consent is important!

I'll then pull your information with a script that will obtain your public sponsors profile info from Github and generate a `.md` file with your profile, that I will deploy to the [live website](https://sponsoropensource.dev).

If you'd like to do the process on your own and send a PR with your profile, you can check the "Contributing" section for more information.

## I Want To Refer Someone to the List
If you know someone who identifies as woman, non-binary, or trans, and you think they should be in the list,
you may create an issue or pull request on their behalf, however **we'll only merge the PR if they explicitly give consent as a comment on the issue or Pull Request you created**.

## I Want To Be Removed From the List
If you'd like to have your profile removed from the website, you can either:

- Open an issue asking to be removed
- Send a PR that removes your `.md` file from `data/profiles`
- Send me a DM on [Twitter](https://twitter.com/erikaheidi)

## Contributing
We welcome contributions to the website, and to include folks **who already opted in by creating an issue in this repository**. 

To run this project on your local machine, you'll need PHP-cli (7.4+) and [Composer](https://getcomposer.org).

### Running the Project Locally

If you want to make pull requests to the project, you'll first need to create a **fork** of this repository on your own account. Then, clone your own fork locally:

```command
git clone https://github.com/myuser/sponsoross.git
cd sponsoross
composer install
```

Then run the project with the built-in PHP server:

```command
php -S 0.0.0.0:8000 -t web/
```

Now you can go to your browser and access `localhost:8000` to view your local version of the website.

### Importing a new profile

To run the `import` command, you'll need to edit your `config.php` file to set up your `GITHUB_API_TOKEN`. You can generate this token at the [Developer Settings Page](https://github.com/settings/tokens). 

To import a new GitHub profile, run:

```command
php librarian import github USERNAME
```

Where `USERNAME` is the sponsorable user's GitHub login. If the provided user is not enrolled in the GitHub Sponsors program,
an error will be printed and the import will fail.



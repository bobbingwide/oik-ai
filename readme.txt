=== oik-ai ===
Contributors: bobbingwide, vsgloik
Donate link: https://www.oik-plugins.com/oik/oik-donate/
Tags: oik, AI, content, images
Requires at least: 6.4
Tested up to: 6.4.2
Stable tag: 0.2.0

AI tool using OpenAI to respond to freeform prompts to generate or manipulate content for your WordPress website.

== Description ==
A tool for playing with AI to generate content for WordPress websites.

== Installation ==
This is prototype code that's not intended for use in a live WordPress website.
I, Herb Miller, have been using it to help generate content for the website wp-secrets.co.uk

It started out as a very simple exploration, learning how to use the OpenAI PHP library.

The code is totally dependent upon this library.
Even though it's written as if it were a WordPress plugin
it is actually implemented as standalone code, with no use of WordPress functionality.
In order to provide a platform independent UI, it's (currently) dependent upon several files from a private repository ( bobbingwide/bw ).
 
You'll need Composer to install the OpenAI code; see composer.json.
And you'll need an OpenAI API key, which you set in settings.json.
I've only used it with the gpt-4 and dall-e-3 models.

== Frequently Asked Questions ==

= What is this plugin for? =
Playing with AI.

== Screenshots ==
1. oik-ai - Default prompts - loaded from prompts.json
2. oik-ai - Correct the spelling and grammar
3. oik-ai - Generated image
4. oik-ai - Prompt history
5. oik-ai - Loaded response for "Funny"

== Upgrade Notice ==
= 0.2.0 = 
Now supports image generation using DALL-E-3 (HD).

== Changelog ==
= 0.2.0 = 
* Added: Build an image generator using OpenAI #3 
* Added: Add prompts to Generate a comment, Generate some PHP #2
* Changed: Improved History display #4
* Tested: With PHP 8.3
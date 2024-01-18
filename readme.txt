=== oik-ai ===
Contributors: bobbingwide, vsgloik
Donate link: https://www.oik-plugins.com/oik/oik-donate/
Tags: oik, AI
Requires at least: 6.4
Tested up to: 6.4.2
Stable tag: 0.1.0

AI tool using OpenAI to respond to freeform prompts to generate or manipulate content for your WordPress website.

== Description ==
A tool for playing with AI to generate content for WordPress websites.

== Installation ==
This is prototype code that's not intended for use in a live WordPress website.
I, Herb Miller, have been using it to help generate content for the website wp-secrets.co.uk

It started out as a very simple exploration, learning how to use the OpenAI PHP library.

The code is totally dependent upon this library.
In order to provide a platform independent UI it's also dependent upon several files from a private repository called bw.
 
You'll need Composer to install the OpenAI code; see composer.json.
And you'll need an OpenAI API key, which you set in settings.json.
I've only used it with the gpt-4 model

== Frequently Asked Questions ==

= What is this plugin for? =
Playing with AI.

== Screenshots ==
1. oik-ai 
2. oik-ai - prompt history 

== Upgrade Notice ==
= 0.1.0 = 
Now supports predefined prompts for common requests.

== Changelog ==
= 0.1.0 = 
* Added: Build an excerpt, meta description generator using AI #2 
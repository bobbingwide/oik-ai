<?php
/**
Plugin Name: oik-ai
Plugin URI: https://www.oik-plugins.com/oik-plugins/oik-ai
Description: AI for WordPress
Version: 0.0.0
Author: bobbingwide
Author URI: https://bobbingwide.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2023 Bobbing Wide (email : herb@bobbingwide.com )

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2,
as published by the Free Software Foundation.

You may NOT assume that you can use any other version of the GPL.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

The license for this software can likely be found here:
http://www.gnu.org/licenses/gpl-2.0.html
*/

function oik_ai_loaded() {
	$OpenAIKey = oik_ai_get_key();
	echo $OpenAIKey;
	if ( $OpenAIKey ) {
		$client=OpenAI::client( $OpenAIKey );
	} else {
		echo "Please set OPENAI_KEY in your environment";
	}
	//print_r( $client );
	//oik_ai_interact( $client );
	//oik_ai_list_models( $client );
	oik_ai_completion( $client );
	$content = "I took this photo of a collage of paintings at my mum’s care home since one of the paintings, the one in the middle, had SB on it. It wasn’t until later that I noticed the painting in the top right. I believe it was done by my sister Kate, and it’s of my mum’s cat, Sebastian.	
Anyway, while editing the painting on my iPhone, I noticed that the Information button now supports AI. When I opened the Information box, several of the paintings were overlaid with an icon. Clicking on the icon performed a Look Up Artwork.
To my surprise the 4th painting, middle left, was matched to 'Don’t Snark Back by Snark Notes'.";
	
	$messages = oik_ai_get_excerpt_messages( $content) ;
	oik_ai_chat( $client, $messages );


}

function oik_ai_get_key() {
	$OpenAIKey = getenv( "OPENAI_KEY" );
	return $OpenAIKey;
}

/**
 * First interaction with OpenAI.
 *
 * https://github.com/openai-php/client#get-started
 * @param $client
 *
 * @return void
 */
function oik_ai_interact( $client ) {
	$result = $client->chat()->create([
		'model' => 'gpt-4',
		'messages' => [
			['role' => 'user', 'content' => 'Hello!'],
		],
	]);
	print_r( $result );

	echo $result->choices[0]->message->content;
}

function oik_ai_chat( $client, $messages ) {
	$result = $client->chat()->create([
		'model' => 'gpt-4',
		'messages' => $messages,
	]);
	//print_r( $result );

	echo $result->choices[0]->message->content;
}

function oik_ai_completion( $client ) {
	$response = $client->completions()->create([
		'model' => 'gpt-3.5-turbo-instruct',
		'prompt' => 'Say this is a test',
		'max_tokens' => 6,
		'temperature' => 0
	]);
	//print_r( $result );
	foreach ($response->choices as $result) {
		echo $result->text; // '\n\nThis is a test'
		//$result->index; // 0
		//$result->logprobs; // null
		echo $result->finishReason; // 'length' or null
	}
}

function oik_ai_list_models( $client ) {
	$response = $client->models()->list();
	print_r( $response );
	//$response->object; // 'list'

	foreach ($response->data as $result) {
		$result->id; // 'gpt-3.5-turbo-instruct'
		$result->object; // 'model'
		// ...
	}

	$response->toArray(); // ['object' => 'list', 'data' => [...]]
}

function oik_ai_get_excerpt_messages( $content ) {
	$messages = [
		[
			"role" => "system",
			"content" => "You will be provided with a block of text, and your task is to summarize it in under 50 words."
		],
		[
			"role" => "user",
			"content" => $content
		]
	];
	return $messages;
}

// Load Composer’s autoloader
require_once 'vendor/autoload.php';
oik_ai_loaded();


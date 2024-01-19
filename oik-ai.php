<?php
/**
Plugin Name: oik-ai
Plugin URI: https://github.com/bobbingwide/oik-ai
Description: AI for WordPress
Version: 0.1.0
Author: bobbingwide
Author URI: https://bobbingwide.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2023,2024 Bobbing Wide (email : herb@bobbingwide.com )

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
	// Do nothing for now.
	if ( PHP_SAPI === 'cli' ) {
		oik_ai_loaded_batch();
	}
}

function oik_ai_loaded_batch() {
	require_once( "classes/class-Oik-AI.php" );
	$oik_ai =new Oik_AI();

	//oik_ai_invoke_chat();
	oik_ai_invoke_image( $oik_ai);
	//oik_ai_invoke_models( $oik_ai );

}

function oik_ai_invoke_chat( $oik_ai ) {
	$content       =oik_ai_get_content();
	$system_message="You will be provided with a block of text, and your task is to summarize it in under 50 words.";
	$oik_ai->set_system_message( $system_message );
	$result=$oik_ai->chat( $content );
	echo "Message:" . PHP_EOL;
	echo $content;
	echo PHP_EOL;
	echo "Result:", PHP_EOL;
	echo $result;
}

function oik_ai_invoke_image( $oik_ai ) {
	//$content = 'stupidly brilliant';
	$system_message = 'stupidly brilliant';
	$oik_ai->set_system_message( $system_message );
	//$result = $oik_ai->image();
	$result = $oik_ai->image_data( '');
	echo "Message:"  . PHP_EOL;
	echo $system_message;
	echo PHP_EOL;
	echo "Result:" , PHP_EOL;
	echo strlen( $result ), PHP_EOL;
	$file_name = oik_ai_get_image_file_name( $system_message );
	$file = file_put_contents( $file_name, base64_decode( $result ) );
	echo "File: $file_name $file", PHP_EOL;
	//print_r( $file );
}

function oik_ai_get_image_file_name( $system_message ) {
	$date = bw_format_date( null, 'Ymd-His');
	$file_name = 'C:/apache/htdocs/ai/' . $date . '-' . $system_message . '.png';
	return $file_name;
}

function oik_ai_invoke_models( $oik_ai ) {
	$oik_ai->models();
}


function oik_ai_get_content() {
	$content="I took this photo of a collage of paintings at my mum’s care home since one of the paintings, the one in the middle, had SB on it. It wasn’t until later that I noticed the painting in the top right. I believe it was done by my sister Kate, and it’s of my mum’s cat, Sebastian.	
Anyway, while editing the painting on my iPhone, I noticed that the Information button now supports AI. When I opened the Information box, several of the paintings were overlaid with an icon. Clicking on the icon performed a Look Up Artwork.
To my surprise the 4th painting, middle left, was matched to 'Don’t Snark Back by Snark Notes'.";

	/*
	$content = "Sajt Burger is the Hungarian for cheese burger, where Sajt is pronounced a bit like shite.
	 There are other countries where you can buy food or drink that doesn’t sound quite right. In France you can drink Pschitt, by Perrier.
BTW. It should be spelt sajtburger – one word";
	*/
return $content;
}


// Load Composer’s autoloader - can this be deferred until actually needed?
require_once 'vendor/autoload.php';
oik_ai_loaded();
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
	require_once( "classes/class-Oik-AI.php" );
	$oik_ai =new Oik_AI();
	$content=oik_ai_get_content();
	$result =$oik_ai->chat( $content );
	echo $result;
}

function oik_ai_get_content() {
	$content="I took this photo of a collage of paintings at my mum’s care home since one of the paintings, the one in the middle, had SB on it. It wasn’t until later that I noticed the painting in the top right. I believe it was done by my sister Kate, and it’s of my mum’s cat, Sebastian.	
Anyway, while editing the painting on my iPhone, I noticed that the Information button now supports AI. When I opened the Information box, several of the paintings were overlaid with an icon. Clicking on the icon performed a Look Up Artwork.
To my surprise the 4th painting, middle left, was matched to 'Don’t Snark Back by Snark Notes'.";

	$content = "Sajt Burger is the Hungarian for cheese burger, where Sajt is pronounced a bit like shite.
	 There are other countries where you can buy food or drink that doesn’t sound quite right. In France you can drink Pschitt, by Perrier.
BTW. It should be spelt sajtburger – one word";
return $content;
}


// Load Composer’s autoloader - can this be deferred until actually needed?
require_once 'vendor/autoload.php';
oik_ai_loaded();


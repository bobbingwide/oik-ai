<?php

/**
 * @copyright (C) Copyright Bobbing Wide 2023, 2024
 * @package bw/ai
 *
 * Experiment with OpenAI to produce text content or images.
 */

if ( defined( 'ABSPATH') ) {
    echo "This has been loaded by WordPress";
} else {
	require_once("inc/bobbset.php");
}

oik_require( "classes/class-ai.php", "oik-ai" );
oik_require( "classes/class-ai-history.php", 'oik-ai' );
oik_require( "classes/class-ai-prompts.php", 'oik-ai' );

echo '<body>';

$ai = new ai();

$ai->maybe_perform_action();

$ai->process_form();

$ai->form();

$ai->previous_results();

echo '</body>';
echo '</html>';

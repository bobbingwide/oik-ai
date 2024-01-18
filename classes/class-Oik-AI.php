<?php
/**
 * @copyright (C) Copyright Bobbing Wide 2023,2024
 * @package oik-ai
 *
 * Interface to OpenAI from https://github.com/openai-php/client
 */

class Oik_AI {

	private $OpenAIKey;
	private $client;
	private $result;
    private $system_message;

	function __construct() {
        $this->getSettings();
		$this->getKey();
		if ( $this->OpenAIKey ) {
			$this->client = OpenAI::client( $this->OpenAIKey );
		} else {
			echo "Please set OPENAI_KEY in your environment";
		}
	}

	function getKey() {
		//$this->OpenAIKey = getenv( "OPENAI_KEY" );

		return $this->OpenAIKey;
	}

    function getSettings() {
        oik_require( 'class-ai-settings.php', 'oik-ai');

        $AI_settings = new AI_settings();
        $this->OpenAIKey = $AI_settings->get_openai_key();

    }

	function models( ) {
		$response = $this->client->models()->list();
		print_r( $response );

	}

    function set_system_message( $system_message ) {
        $this->system_message = $system_message;
    }

	function get_excerpt_messages( $content ) {
		$messages = [
			[
				"role" => "system",
				"content" => $this->system_message
			],
			[
				"role" => "user",
				"content" => $content
			]
		];
		return $messages;
	}

	/**
	 * First interaction with OpenAI.
	 *
	 * https://platform.openai.com/docs/guides/text-generation/chat-completions-api
	 * https://platform.openai.com/docs/guides/text-generation
	 */
	function chat(  $content ) {
		$messages = $this->get_excerpt_messages( $content );
		$this->result = $this->client->chat()->create([
			'model' => 'gpt-4',
			'messages' => $messages,
		]);
		//print_r( $this->result );
		return $this->result->choices[0]->message->content;
	}

    function get_finish_reason() {
        //print_r( $this->result);
        $reason = ( $this->result) ? $this->result->choices[0]->finishReason : null;
        return $reason;
    }

    function get_details() {
        //print_r( $this->result);
        return $this->result;
    }

}

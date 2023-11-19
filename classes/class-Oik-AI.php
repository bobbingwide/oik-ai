<?php

class Oik_AI {

	private $OpenAIKey;
	private $client;
	private $result;

	function __construct() {
		$this->getKey();
		if ( $this->OpenAIKey ) {
			$this->client = OpenAI::client( $this->OpenAIKey );
		} else {
			echo "Please set OPENAI_KEY in your environment";
		}
	}

	function getKey() {
		$this->OpenAIKey = getenv( "OPENAI_KEY" );
		return $this->OpenAIKey;
	}

	function models( ) {
		$response = $this->client->models()->list();
		print_r( $response );

	}

	function get_excerpt_messages( $content ) {
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
		print_r( $this->result );
		return $this->result->choices[0]->message->content;
	}

}

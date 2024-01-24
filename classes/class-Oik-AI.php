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
	private $size;
	private $style = 'vivid';

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
        oik_require( 'classes/class-ai-settings.php', 'oik-ai');

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

	/**
	 * Returns the details.
	 *
	 * Notes:
	 * - When the request is for an image then the details includes the `b64_json` field
	 * - which can be a few megabytes.
	 * - It also includes the revisedPrompt, which perform_save() extracts separately.
	 *
	 *
	 * @return mixed
	 */
    function get_details() {
        //print_r( $this->result);
        return $this->result;
    }

	/**
	 * Generate an image using DALL-E-3
	 *
	 *  https://platform.openai.com/docs/guides/images/usage?context=node
	 * https://platform.openai.com/docs/api-reference/images/create
	 *
	 * - size: 1024x1024, 1024x1792 or 1792x1024 pixels
	 * - n: You can request 1 image at a time with DALL·E 3
	 * - quality: 'standard' or 'hd'
	 *
	 * With the release of DALL·E 3, the model
	 * - now takes in the default prompt provided
	 * - and automatically re-write it for safety reasons,
	 * - and to add more detail (more detailed prompts generally result in higher quality images).
	 *
	 * While it is not currently possible to disable this feature,
	 * you can use prompting to get outputs closer to your requested image
	 * by adding the following to your prompt:
	 *
	 * I NEED to test how the tool works with extremely simple prompts. DO NOT add any detail, just use it AS-IS:.
	 *
	 * Each image can be returned as either a URL or Base64 data, using the response_format parameter.
	 * URLs will expire after an hour.
	 *
	 * style: 'vivid' or 'natural'
	 *
	 * The style of the generated images. Must be one of vivid (default) or natural.
	 * Vivid causes the model to lean towards generating hyper-real and dramatic images.
	 * Natural causes the model to produce more natural, less hyper-real looking images.
	 * This param is only supported for dall-e-3
	 *
	 * @return mixed
	 */
	function image() {
		$result = $this->client->images()->create([
			'model' => 'dall-e-2',
			'prompt' => $this->system_message,
			'n' => 1,
			'size' => '1024x1024',
			'response_format' => 'url',
		]);
		print_r( $result );
		$url = $result->data[0]['url'];
		return $url;
	}

	function image_data( $user_message ) {
		$prompt = $this->get_prompt( $user_message );
		$result = $this->client->images()->create([
			'model' => 'dall-e-3',
			'prompt' => $prompt,
			'quality' => 'hd',
			'n' => 1,
			'size' => $this->size,
			'style' => $this->style,
			'response_format' => 'b64_json',
		]);
		//print_r( $result );
		$image = $result->data[0]['b64_json'];

		//$result->data[0]['b64_json'] = '';
		//print_r( $result );
		$this->result = $result;
		return $image;
	}

	function set_size( $size ) {
		$this->size = $size;
	}
	function set_style( $style='vivid') {
		$this->style = $style;
	}

	function get_prompt( $user_message ) {
		return $this->system_message . ' ' . $user_message;
	}



	/**
	 * Return the revised_prompt.
	 *
	 * Available since openai-php/client v0.7.9
	 */
	function get_revised_prompt() {
		//print_r( $this->result );
		return $this->result->data[0]['revised_prompt'];
	}

}

<?php

/**
* @copyright (C) Copyright Bobbing Wide 2024
* @package oik-ai ?
*
*/

class AI_settings
{

    private $openai_key = null;
    private $settings = [];

    function __construct() {
        $this->load_settings();
        $this->set_openai_key();
    }

    function load_settings() {
        $file = oik_path( 'settings.json', 'oik-ai' );
        $json = file_get_contents($file);
        $this->settings = json_decode($json, true);
    }

    function set_openai_key( $openai_key=null ) {
        if ( null === $openai_key ) {
            $openai_key = $this->settings['openai_key'];
        }
        if ( 'OPENAI_KEY' === $openai_key ) {
            $openai_key = getenv( 'OPENAI_KEY');
        }
        $this->openai_key = $openai_key;

    }

    function get_openai_key() {
        return $this->openai_key;
    }

    function get_model() {
        return $this->settings['model'];
    }



}
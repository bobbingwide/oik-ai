<?php
/**
 * @copyright (C) Copyright Bobbing Wide 2024
 * @package oik-ai ?
 *
 */

class AI_prompts
{


    private $default_prompt_options =
        ['Generate SEO focus keyphrase',
            'Generate <=130 chars',
            'Generate <=50 words'
        ];
    private $prompt_options = [];

    function __construct()     {
//echo "AI_prompts";

    }

    function get_prompts() {
        return $this->prompt_options;
    }



    function display_prompts()
    {
        oik_require_lib('bobbforms');
        $prompt_value = bw_array_get($_POST, 'prompts', null);
        $prompt = bw_array_get($this->prompt_options, $prompt_value, '');
        e(iselect("prompts", $prompt, ['#options' => $this->prompt_options, '#optional' => true]));

        //BW_::bw_textarea('prompt_text', 80, "All prompts", $this->prompts, count($this->prompt_options));
        bw_flush();

    }



    function display_prompt()
    {
        static $count = 0;
        $count++;
        //echo $count;
        $this->prompt_options[] = $this->result['system'];
        $this->prompts .= $this->result['system'];
        $this->prompts .= PHP_EOL;
    }


    function load_prompts() {
        $file = oik_path( 'prompts.json', 'oik-ai' );
        $json = file_get_contents($file);
        $prompt_options = json_decode($json, true);
        //print_r( $prompt_options);
        foreach ( $prompt_options as $prompt ) {
            $this->prompt_options[ $prompt['label'] ] = $prompt['system'];
        }
    }

    function get_prompt_text( $prompt ) {
        $prompt_text = bw_array_get( $this->prompt_options, $prompt, '');
        return $prompt_text;
    }

}
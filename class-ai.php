<?php
/**
 * @copyright (C) Copyright Bobbing Wide 2023,2024
 * @package oik-ai
 *
 */

class AI
{

    private $system_message;
    private $user_message;
    private $result;
    private $finish_reason;
    private $oik_ai;
    private $ai_history;
    private $ai_prompts;
    private $prompt_options;
    private $original_system_message;

    function __construct()  {
        $this->system_message ='';
        $this->original_system_message = '';
        $this->user_message = "";
        $this->result = '';
        $this->finish_reason = '- not yet available';
        $this->oik_ai = null;
        $this->ai_history = null;
        $this->ai_prompts = null;
        $this->load_prompts();
        $this->enqueue_css();
    }

    function enqueue_css()  {
        echo '<link rel="stylesheet" href="ai.css" type="text/css" media="screen" />';
    }

//
// generate a meta description for the provided text.
// The meta description should be fewer than 156 characters in length.

// generate an excerpt for the provided text.
// the excerpt should be no longer than 30 words
    function form() {
        oik_require_lib( 'bobbforms');
        bw_form();

        //$this->load_prompts();
        e( 'Choose a standard prompt and/or write a System message.');
        $this->display_prompts();
		bw_is_table( false );
		//stag( 'table' );
	    BW_::bw_textarea( "system_message", 80, "System message", $this->original_system_message, 3 );
        BW_::bw_textarea( 'user_message', 80, 'User message. Type or paste your message content here', trim( $this->user_message, '"') , 10 );
        BW_::bw_textarea( 'assistant_message', 80, "Assistant message {$this->finish_reason} ", trim( $this->result, '"' ), 10 );
        //etag( 'table' );
	    br();
        e( isubmit( 'submit', 'Send') );
        //e( isubmit( 'save', 'Save') );
	    e( ' ');
        e( isubmit( 'history', 'Load history') );
        //e( isubmit( 'prompts', 'Prompts') );

        etag("form");
        bw_flush();
    }

    /**
     * Processes the submitted form
     */
    function process_form()  {
        echo '<div class="oik-ai">';
        echo '<h2>OIK-AI</h2>';
        //echo "<p>" . $_SERVER['REQUEST_METHOD'] . "</p>";
        echo "<p>Time: " . bw_format_date( null, "Y-m-d h:i:s"). "</p>" ;
        if ( !empty( $_POST ) ) {
            //print_r($_POST);
            $action = bw_array_get( $_POST, 'submit', null );
            if ( $action ) {
                echo "Processing: " . $action;
                $this->perform_get_excerpt();
            }

            $action = bw_array_get( $_POST, 'save', null );
            if ( $action ) {
                echo "Processing: " . $action;
                $this->perform_save();
            }
            $action = bw_array_get( $_POST, 'history', null );
            if ( $action ) {
                echo "Processing: " . $action;
                $this->perform_history();
            }
            /*
            $action = bw_array_get( $_POST, 'prompts', null );
            if ( $action ) {
                echo "Processing: " . $action;
                $this->perform_prompts();
            }
            */

        } else {
            if ( 'GET' === $_SERVER['REQUEST_METHOD']) {
                echo "<p>It's a GET</p>";
                // These will both be empty arrays.
                //print_r( $_GET );
                //print_r( $_REQUEST );
                //$this->perform_merge();
            } else {
                echo '<p>Form (re)submitted with no data!</p>';
                //print_r($_SERVER);
            }
        }
        //echo '</div>';
        //$this->hexdump( $this->output_csv );

    }

    /**
     * Prepend the selected action to the system message.
     *
     * @return void
     */
    function maybe_override_system_message()
    {
        $prompt = bw_array_get($_POST, 'prompts', '0');

        $system_message = null;
        if ($prompt !== '0') {
            $system_message = $this->ai_prompts->get_prompt_text( $prompt );
            $system_message .= ' ';
        }
        $this->original_system_message = bw_array_get($_POST, 'system_message', null);
        $system_message .= $this->original_system_message;
        return $system_message;
    }

    function set_message_fields() {
        $this->system_message = $this->maybe_override_system_message();
        $this->user_message = bw_array_get( $_POST, 'user_message', null );

    }

    function perform_get_excerpt() {
        $this->set_message_fields();
        echo " for " . $this->system_message;
        $this->get_excerpt();

    }

    function get_excerpt() {
        oik_require( 'vendor/autoload.php', 'oik-ai');
        oik_require( "classes/class-Oik-AI.php", 'oik-ai' );
        $this->oik_ai = $this->oik_ai ? $this->oik_ai : new Oik_AI();
        $this->oik_ai->set_system_message( $this->system_message );
        //$content=oik_ai_get_content();
        $this->result =$this->oik_ai->chat( $this->user_message );
        //echo $result;
        $this->finish_reason = $this->oik_ai->get_finish_reason();
        $this->perform_save( true);
    }

    function perform_save( $details = false)
    {
        $output = ["system" => $this->system_message,
            "user" => $this->user_message,
            "result" => $this->result,
            //"details" => $this->oik_ai->get_details()
        ];
        if ($details && $this->oik_ai ) {
            $output['details'] = $this->oik_ai->get_details();
        }
        $json = json_encode( $output );
        $filename = $this->get_savefile();
        file_put_contents( $filename, $json );

    }
    function perform_history() {
        $this->ai_history = $this->ai_history ? $this->ai_history : new AI_history();
        $this->ai_history->display();

    }

    /*
    function perform_prompts() {
        $this->ai_history = $this->ai_history ? $this->ai_history : new AI_history();
        $this->ai_history->display_prompts();

    }
    */

    function get_savefile() {
        $file = [];
        $file[] = __DIR__;
        $file[] = 'saved';
        $file[] = bw_format_date( null, 'Ymd-His') . '.json';
        $filepath = implode( '/', $file );
        return $filepath;
    }

    // https://www.dashword.com/meta-description-generator
    function maybe_perform_action() {

        if ( !empty( $_POST )) {
            return;
        }

        $delete = bw_array_get( $_GET, 'delete', null );
        if ( $delete ) {
            $this->ai_history = $this->ai_history ? $this->ai_history : new AI_history();
            $this->ai_history->delete( $delete );
            $this->ai_history->display();
            unset( $_POST['history']);
        }

        $load = bw_array_get( $_GET, 'load', null );
        if ( $load ) {
            $this->ai_history = $this->ai_history ? $this->ai_history : new AI_history();
            $this->ai_history->load( $load );
            $this->set_message_fields();

        }
    }

    function load_prompts() {
        $this->ai_prompts = $this->ai_prompts ? $this->ai_prompts : new AI_prompts();
        $this->prompt_options = $this->ai_prompts->load_prompts();
    }

    function display_prompts() {
        $this->ai_prompts->display_prompts();
    }

}
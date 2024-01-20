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
	private $image_save_dir = 'C:/apache/htdocs/ai/';  // @TODO should this use DOCPATH?

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

	/**
    * Displays the AI form.
    * The standard prompts, which are loaded from prompts.json
    * will do things such as:
    *
    * - generate a meta description for the provided text.
    * - The meta description should be fewer than 156 characters in length.
    *
    * - generate an excerpt for the provided text.
    * - the excerpt should be no longer than 30-50 words
    */
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
	    e( ' ' );
	    e ( isubmit( 'image', 'Image: 1024x1024') );
	    e( ' ' );
	    e ( isubmit( '1024x1792', 'Portrait: 1024x1792') );
	    e( ' ' );
	    e ( isubmit( '1792x1024', 'Landscape: 1792x1024') );
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
                $this->perform_get_response();
            }

			$action = bw_array_get( $_POST, 'image', null );
			if ( $action ) {
				echo "Processing: " . $action;
				$this->perform_get_image();
			}

	        $action = bw_array_get( $_POST, '1024x1792', null );
	        if ( $action ) {
		        echo "Processing: " . $action;
		        $this->perform_get_image( '1024x1792');
	        }

	        $action = bw_array_get( $_POST, '1792x1024', null );
	        if ( $action ) {
		        echo "Processing: " . $action;
		        $this->perform_get_image( '1792x1024' );
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
                //echo "<p>It's a GET</p>";
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
		$this->result = bw_array_get( $_POST, 'assistant_message', null );

    }

	/**
	 * Gets the response.
	 *
	 * @return void
	 */
    function perform_get_response() {
        $this->set_message_fields();
        echo " for " . $this->system_message;
        $this->get_response();

    }

	/**
	 * Fetches the response from the AI chat
	 *
	 * @return void
	 */
    function get_response() {
        oik_require( 'vendor/autoload.php', 'oik-ai');
        oik_require( "classes/class-Oik-AI.php", 'oik-ai' );
        $this->oik_ai = $this->oik_ai ? $this->oik_ai : new Oik_AI();
        $this->oik_ai->set_system_message( $this->system_message );
        $this->result =$this->oik_ai->chat( $this->user_message );
        //echo $result;
        $this->finish_reason = $this->oik_ai->get_finish_reason();
        $this->perform_save( true);
    }

	/**
	 * Gets an image from the AI chat.
	 *
	 * @return void
	 */
	function perform_get_image( $size='1024x1024') {
		$this->set_message_fields();
		echo " for " . $this->system_message;
		$this->get_image( $size );

	}
	/**
	 * Fetches an image from the AI chat.
	 * @return void
	 */
	function get_image( $size ) {
		oik_require( 'vendor/autoload.php', 'oik-ai');
		oik_require( "classes/class-Oik-AI.php", 'oik-ai' );
		$this->oik_ai = $this->oik_ai ? $this->oik_ai : new Oik_AI();
		$this->oik_ai->set_system_message( $this->system_message );
		$this->oik_ai->set_size( $size );
		$image_data = $this->oik_ai->image_data( $this->user_message );
		//echo $result;
		$this->finish_reason = $this->oik_ai->get_finish_reason();
		$this->result = $this->save_image_file( $image_data );
		$this->perform_save( true);
		$this->display_image();

	}

	/**
	 * Saves the base64 image as a .png file.
	 *
	 * @param $image_data
	 * @return string - just the file name part.
	 */
	function save_image_file( $image_data) {
		$file_name = $this->get_image_file_name( $this->system_message );
		$file = file_put_contents( $this->image_save_dir . $file_name, base64_decode( $image_data ) );
		//echo "File: $file_name $file", PHP_EOL;
		//print_r( $file );
		return $file_name;
	}

	function get_image_file_name( $system_message ) {
		$date     =bw_format_date( null, 'Ymd-His' );
		$file_name= $date . '-' . $system_message . '.png';
		return $file_name;
	}

	function display_image() {
		oik_require_lib( 'bobbforms');
		$image_url = 'http';
		// Does it matter what it's set to? "on" or 1 **?**
		if ( isset( $_SERVER["HTTPS"] ) ) {
			$image_url .= "s";
		}
		$image_url .= "://";
		$image_url .= $_SERVER["SERVER_NAME"];
		$image_url .= '/ai/';
		$image_url .= $this->result;
		//echo $image_url . PHP_EOL;

		$img = retimage( null, $image_url);
		//$link = retlink( null, , $this->result );
		echo $img;
		//echo $link;
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
        $file[] = dirname( __DIR__ );
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
<?php
/**
 * @copyright (C) Copyright Bobbing Wide 2023,2024
 * @package oik-ai ?
 *
 */
class AI_history
{

    private $files = [];
    private $result;
    private $prompt_options =
        ['Generate SEO focus keyphrase',
         'Generate <=130 chars',
         'Generate <=50 words'
        ];
    private $prompts = '';

    function display() {
        $this->list_files();
    }

    function display_prompts() {
        gob();
        oik_require_lib( 'bobbforms');
        $prompt_value = bw_array_get( $_POST, 'prompts', '1');
        $prompt = bw_array_get( $this->prompt_options, $prompt_value, '');
        e( iselect( "prompts", $prompt, ['#options' => $this->prompt_options ] ));
        BW_::bw_textarea( 'prompt_text', 80, "All prompts", $this->prompts, count( $this->prompt_options) );
        bw_flush();

    }

    function display_saved_prompts() {
        $this->files = glob( 'saved/*.json');
        foreach ( $this->files as $file ) {
            //br();
            //e( $file );
            $this->load_file( $file);
            $this->display_prompt();

        }
        //print_r( $this->prompt_options);
        oik_require_lib( 'bobbforms');
        $prompt_value = bw_array_get( $_POST, 'prompts', 'Generate excerpt <= 50 words');
        e( iselect( "prompts", '', ['#options' => $this->prompt_options ] ));
        BW_::bw_textarea( 'prompt_text', 80, "All prompts", $this->prompts, count( $this->prompt_options) );
        bw_flush();

    }

    function display_prompt() {
        static $count =0;
        $count++;
        //echo $count;
        $this->prompt_options[] = $this->result['system'];
        $this->prompts .= $this->result['system'] ;
        $this->prompts .= PHP_EOL;
    }


    function list_files() {
        $this->files = glob( 'saved/*.json');
        foreach ( $this->files as $file ) {
            //br();
            //e( $file );
            $this->load_file( $file);
            $this->display_result( $file);

        }
        bw_flush();
    }

    function load_file( $file ) {
        $json = file_get_contents( $file );
        $this->result = json_decode( $json, true );

    }

    function display_result( $file ) {
        oik_require_lib( 'bobbforms');
        $row = [];
        $row['file'] = $this->sediv( $this->get_file_actions( $file ), 'file' );
        $row['system'] = $this->sediv( $this->result['system'], 'system' );
		// We need to strip HTML from the user and result fields
	    //require_once( ABSPATH . '/wp-includes/formatting.php');
	    $esc_user = $this->abbreviate( $this->result['user'] );
        $row['user'] = $this->sediv( $esc_user, 'user' );
		$esc_result = $this->abbreviate( $this->result['result'] );
        $row['result'] = $this->sediv( $esc_result, 'result' );

        bw_gridrow( $row, 'history');

    }

	function abbreviate( $long_text ) {
		$length = strlen( $long_text);
		//$dots = $length > 256 ? '...' : '';
		$text = substr( $long_text, 0, 256 );
		$text = htmlspecialchars( $text );
		$text .= ( $length > 256 ) ? ' ...' : '';
		return $text;
	}

    function get_file_actions( $file ) {
        $file_actions = $file;
        $file_actions .= ' ';
        $file_actions .= retlink( null, "?delete=$file", "Delete" );
        $file_actions .= ' ';
        $file_actions .= retlink( null, "?load=$file", "Load" );

        return $file_actions;
    }

    function sediv( $value, $class='' ) {
        return "<div class=\"$class\">$value</div>";
    }

    function delete( $file ) {
        $full_file = __DIR__  . '/' . $file;
        echo "<br />Deleting $full_file";
        if ( file_exists( $full_file ) ) {
            unlink( $full_file);
        }
    }

    function load( $file ) {
        echo "<br />Loading $file";
        $this->load_file( $file );
        //print_r( $this->result );
        // Pass the values to class AI via the $_POST
        $_POST['system_message'] = $this->result['system'];
        $_POST['user_message'] = $this->result['user'];
		$_POST['assistant_message'] = $this->result['result'];
    }
}
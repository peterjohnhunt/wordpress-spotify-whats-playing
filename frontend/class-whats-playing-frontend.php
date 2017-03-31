<?php

//░░░░░░░░░░░░░░░░░░░░░░░░
//
//     DIRECTORY
//
//     _Variables
//     _Setup
//     _CSS
//     _Render
//
//░░░░░░░░░░░░░░░░░░░░░░░░

namespace Whats_Playing\Frontend;
use Whats_Playing\Includes;

class Whats_Playing_Frontend {
	//≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
	// _Variables
	//≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡

	private $version;

	private $spotify;


	//≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
	// _Setup
	//≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡

	public function __construct( $version ) {
		$this->version = $version;
		$this->spotify = new Includes\Spotify_API();
	}


	//≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
	// _CSS
	//≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡

	public function enqueue_styles(){
		if ( $this->spotify->is_authenticated() ) {
			wp_enqueue_style(
				'whats-playing-open-sans',
				'https://fonts.googleapis.com/css?family=Open+Sans:400,700',
				array(),
				$this->version,
				FALSE
			);
			wp_enqueue_style(
				'whats-playing-frontend-css',
				plugin_dir_url( dirname(__FILE__) ) . 'assets/css/whats-playing-frontend.min.css',
				array(),
				$this->version,
				FALSE
			);
		}
	}

	public function enqueue_scripts(){
		if ( $this->spotify->is_authenticated() ) {
			wp_enqueue_script(
				'whats-playing-frontend-js',
				plugin_dir_url( dirname(__FILE__) ) . 'assets/js/whats-playing-frontend.min.js',
				array('jquery'),
				$this->version,
				TRUE
			);
			wp_localize_script(
				'whats-playing-frontend-js',
				'WHATS_PLAYING',
				array(
					'ajax' => array(
						'url' => admin_url( 'admin-ajax.php' ),
						'nonce' => wp_create_nonce( 'whats-playing-nonce' )
					)
				)
			);
		}
	}


	//≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
	// _Render
	//≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡

	public function render_whats_playing(){
		if ( $this->spotify->is_authenticated() ) {
			echo '<aside id="whats-playing"><div class="bubbles"><span></span><span></span></div><div class="wrapper"></div></aside>';
		}
	}


	//≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
	// _Ajax
	//≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡

	public function get_whats_playing(){
		$nonce = $_POST['nonce'];
	    if ( empty($_POST) || ! wp_verify_nonce( $nonce, 'whats-playing-nonce' ) ){
	        wp_send_json_error( 'WHATS PLAYING: nonce invalid!' );
	    }

		if ( $this->spotify->is_authenticated() ) {
			$profile = $this->spotify->get_profile();
			$playing = $this->spotify->get_playing_song();
			ob_start();
			require_once plugin_dir_path( __FILE__ ) . 'partials/whats-playing.php';
			$html = ob_get_clean();

			if ($html) {
				wp_send_json_success(array('html' => $html));
			}
		}

		wp_send_json_error('WHATS PLAYING: Please Authenticate Your Spotify!');
	}
}
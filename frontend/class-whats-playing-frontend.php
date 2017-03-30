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

	public function enqueue_scripts(){
		wp_enqueue_script(
			'whats-playing-frontend-js',
			plugin_dir_url( dirname(__FILE__) ) . 'assets/js/whats-playing-frontend.min.js',
			array('jquery'),
			$this->version,
			TRUE
		);
	}


	//≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
	// _Render
	//≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡

	public function render_whats_playing(){
		if ( $this->spotify->is_authenticated() ) {
			$profile = $this->spotify->get_profile();
			$playing = $this->spotify->get_playing_song();
			require_once plugin_dir_path( __FILE__ ) . 'partials/whats-playing.php';
		}
	}
}
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
			'whats-playing-frontend',
			plugin_dir_url( __FILE__ ) . 'css/whats-playing-frontend.css',
			array(),
			$this->version,
			FALSE
		);
	}


	//≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
	// _Render
	//≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡

	public function render_whats_playing(){
		$options = get_option( 'whats_playing_settings' );

		if (isset($options['whats_playing_auth_code'])) {
			$access_token = $options['whats_playing_auth_code'];
			$this->spotify->set_token($access_token);

			if ( $this->spotify->is_authenticated() ) {
				$profile = $this->spotify->get_profile();
				$playing = $this->spotify->get_playing_song();

				if ($profile || $playing) {
					require_once plugin_dir_path( __FILE__ ) . 'partials/whats-playing.php';
				} else {
					$options = get_option('whats_playing_settings');
					unset($options['whats_playing_auth_code']);
					update_option('whats_playing_settings', $options);
				}
			}
		}
	}
}
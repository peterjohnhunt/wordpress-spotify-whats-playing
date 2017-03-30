<?php

//░░░░░░░░░░░░░░░░░░░░░░░░
//
//     DIRECTORY
//
//     _Variables
//     _Setup
//     _CSS
//     _Settings
//       ∟Page
//       ∟Fields
//       ∟Actions
//
//░░░░░░░░░░░░░░░░░░░░░░░░

namespace Whats_Playing\Admin;
use Whats_Playing\Includes;

class Whats_Playing_Admin {
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

	public function enqueue_styles() {
		wp_enqueue_style(
			'whats-playing-admin',
			plugin_dir_url( dirname(__FILE__) ) . 'assets/css/whats-playing-admin.min.css',
			array(),
			$this->version,
			FALSE
		);
	}


	//≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
	// _Settings
	//≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡
	//∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴
	// ∟Page
	//∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴

	public function add_plugin_page() {
		ob_start();
		require_once plugin_dir_path( dirname(__FILE__) ) . 'assets/images/spotify.svg';
		$icon = ob_get_clean();
		$hook = add_menu_page(
			'Whats Playing',
			'Whats Playing',
			'manage_options',
			'whats-playing',
			array($this, 'render_plugin_page'),
			'data:image/svg+xml;base64,'.base64_encode($icon),
			'99'
		);

		add_action('load-'.$hook, array($this, 'on_settings_save'));
	}

	public function render_plugin_page(){
		ob_start();
		require_once plugin_dir_path( __FILE__ ) . 'partials/whats-playing.php';
		echo ob_get_clean();
	}

	//∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴
	// ∟Fields
	//∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴

	public function add_plugin_settings() {
		register_setting( 'whats_playing', 'whats_playing_settings' );

		add_settings_section(
			'settings_section',
			'API Credentials',
			false,
			'whats_playing'
		);

		add_settings_field(
			'client_id',
			'Client ID',
			array($this, 'render_settings_client_id'),
			'whats_playing',
			'settings_section'
		);

		add_settings_field(
			'client_secret',
			'Client Secret',
			array($this, 'render_settings_client_secret'),
			'whats_playing',
			'settings_section'
		);

		if ( $this->spotify->is_authenticated() ) {
			add_settings_field(
				'refresh_token',
				'Refresh Token',
				array($this, 'render_settings_refresh_token'),
				'whats_playing',
				'settings_section'
			);
		}
	}

	public function render_settings_client_id() {
		ob_start();
		require_once plugin_dir_path( __FILE__ ) . 'partials/fields/client-id.php';
		echo ob_get_clean();
	}

	public function render_settings_client_secret() {
		ob_start();
		require_once plugin_dir_path( __FILE__ ) . 'partials/fields/client-secret.php';
		echo ob_get_clean();
	}

	public function render_settings_refresh_token() {
		ob_start();
		require_once plugin_dir_path( __FILE__ ) . 'partials/fields/refresh-token.php';
		echo ob_get_clean();
	}


	//∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴
	// ∟Actions
	//∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴

	public function on_settings_save() {
		if ( isset($_GET['settings-updated']) ) {
			$this->spotify->authenticate();
		}

		if ( $this->spotify->is_authenticated() ) {
			add_settings_error('whats_playing_settings','whats-up-authenticated','Successfully Authenticated!', 'updated');
		} else {
			add_settings_error('whats_playing_settings','whats-up-error','Not Authenticated!', 'error');
		}
	}

	public function on_settings_authenticate() {
		if( is_front_page() && is_user_logged_in() && current_user_can( 'manage_options' ) && isset($_GET[ 'code' ]) && $_GET[ 'code' ]){
			$code = isset($_GET[ 'code' ]) ? $_GET[ 'code' ] : '';
			$authenticated = $this->spotify->save_tokens($code);
			wp_safe_redirect(admin_url('admin.php?page=whats-playing'));
			exit();
		}
	}
}
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
			plugin_dir_url( __FILE__ ) . 'css/whats-playing-admin.css',
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
		require_once plugin_dir_path( __FILE__ ) . 'images/spotify.svg';
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
			'whats_playing_settings_section',
			'API Credentials',
			false,
			'whats_playing'
		);

		add_settings_field(
			'whats_playing_client_id',
			'Client ID',
			array($this, 'render_settings_client_id'),
			'whats_playing',
			'whats_playing_settings_section'
		);

		add_settings_field(
			'whats_playing_client_secret',
			'Client Secret',
			array($this, 'render_settings_client_secret'),
			'whats_playing',
			'whats_playing_settings_section'
		);

		$options = get_option( 'whats_playing_settings' );
		if (isset($options['whats_playing_auth_code'])) {
			add_settings_field(
				'whats_playing_auth_code',
				'Authorization Code',
				array($this, 'render_settings_auth_code'),
				'whats_playing',
				'whats_playing_settings_section'
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

	public function render_settings_auth_code() {
		ob_start();
		require_once plugin_dir_path( __FILE__ ) . 'partials/fields/auth-code.php';
		echo ob_get_clean();
	}


	//∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴
	// ∟Actions
	//∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴∵∴

	public function on_settings_save() {
		if (isset($_GET['settings-updated']) && ($options = get_option('whats_playing_settings'))) {
			$client_id = isset($options['whats_playing_client_id']) ? $options['whats_playing_client_id'] : '';

			if ($client_id) {
				$this->spotify->authenticate($client_id);
			}
		}
	}

	public function on_settings_authenticate() {
		if( is_front_page() && is_user_logged_in() && current_user_can( 'manage_options' ) && isset($_GET[ 'code' ]) && $_GET[ 'code' ]){
			$options       = get_option('whats_playing_settings');
			$client_id     = isset($options['whats_playing_client_id']) ? $options['whats_playing_client_id'] : '';
			$client_secret = isset($options['whats_playing_client_secret']) ? $options['whats_playing_client_secret'] : '';
			$code          = isset($_GET[ 'code' ]) ? $_GET[ 'code' ] : '';

			if ($client_id && $client_secret && $code) {
				$access_token = $this->spotify->get_token($client_id, $client_secret, $code);
				if ($access_token) {
					$options['whats_playing_auth_code'] = $access_token;
				} else {
					unset($options['whats_playing_auth_code']);
				}
				update_option('whats_playing_settings', $options);
			}
			wp_safe_redirect(admin_url('admin.php?page=whats-playing'));
			exit();
		}
	}
}
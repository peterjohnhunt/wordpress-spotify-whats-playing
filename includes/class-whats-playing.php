<?php

namespace Whats_Playing\Includes;
use Whats_Playing\Admin;
use Whats_Playing\Frontend;

class Whats_Playing {

	protected $loader;

	protected $plugin_slug;

	protected $version;

	public function __construct() {
		$this->plugin_slug = 'whats-playing';
		$this->version = '1.0.1';
		$this->loader = new Whats_Playing_Loader();

		$this->define_admin_hooks();
		$this->define_frontend_hooks();
	}

	private function define_admin_hooks() {
		$admin = new Admin\Whats_Playing_Admin( $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_menu', $admin, 'add_plugin_page' );
		$this->loader->add_action( 'admin_init', $admin, 'add_plugin_settings' );
		$this->loader->add_action( 'template_redirect', $admin, 'on_settings_authenticate' );
	}

	private function define_frontend_hooks() {
		$frontend = new Frontend\Whats_Playing_Frontend( $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $frontend, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $frontend, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_footer', $frontend, 'render_whats_playing' );
	}

	public function run() {
		$this->loader->run();
	}

	public function get_version() {
		return $this->version;
	}
}
<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Whats Playing
 * Plugin URI:        http://github.com/peterjohnhunt/whats-playing
 * Description:       Whats Playing displays a bar of your current playing Spotify music
 * Version:           1.0.0
 * Author:            PeterJohn Hunt
 * Author URI:        http://peterjohnhunt.com
 * Text Domain:       whats-playing-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// TODO:
// AJAX
// Options Helpers

namespace Whats_Playing;
use Whats_Playing\Includes;

if ( ! defined( 'WPINC' ) ) {
	die;
}

include_once plugin_dir_path( __FILE__ ) . 'lib/autoloader.php';

function run_whats_playing() {
	$whats_playing = new Includes\Whats_Playing();
	$whats_playing->run();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\run_whats_playing' );
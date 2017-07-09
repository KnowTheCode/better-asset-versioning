<?php
/**
 * Better Asset Versioning
 *
 * @package     KnowTheCode\BetterAssetVersioning
 * @author      hellofromTonya
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Better Asset Versioning
 * Plugin URI:  https://github.com/KnowTheCode/BetterAssetVersioning
 * Description: Improve asset version control by embedding the version number into the URL instead of as an optional query parameter.
 * Version:     1.0.0
 * Author:      hellofromTonya
 * Author URI:  https://KnowTheCode.io
 * Text Domain: genkit-asset-manager
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace KnowTheCode\BetterAssetVersioning;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Cheatin&#8217; uh?' );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\launch', 9999 );
/**
 * Launch the plugin.
 *
 * @since 1.0.0
 *
 * @return void
 */
function launch() {
	load_dependencies();

	get_controller();
}

/**
 * Load the file dependencies.
 *
 * @since 1.0.0
 *
 * @return void
 */
function load_dependencies() {
	$files = array(
		'src/Support/string-helpers.php',
		'src/AssetHandler.php',
	);

	$plugin_dir = trailingslashit( __DIR__ );
	foreach ( $files as $filename ) {
		require_once( $plugin_dir . $filename );
	}
}

add_filter( 'get_better_assets_versioning_handler', __NAMESPACE__ . '\get_controller' );
/**
 * Get the instance of Assets Handler
 *
 * If it's not instantiated, do that first.  Then return it.
 *
 * This function provides the means for 3rd party plugins/themes to get access
 * to the controller instance.
 *
 * @since 1.0.0
 *
 * @return AssetsVersioning
 */
function get_controller() {
	static $controller;

	if ( ! $controller ) {

		$config = require_once( trailingslashit( __DIR__ ) . 'config/assets.php' );

		$controller = new AssetHandler( $config );
	}

	return $controller;
}

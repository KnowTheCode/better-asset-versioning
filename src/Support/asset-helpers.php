<?php
/**
 * Asset handlers
 *
 * @package     KnowTheCode\BetterAssetVersioning
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://KnowTheCode.io
 * @license     GNU-2.0+
 */

if ( ! function_exists( 'change_theme_stylesheet_uri_to_min_version' ) ) :

	add_filter( 'stylesheet_uri', 'change_theme_stylesheet_uri_to_min_version' );
	/**
	 * Change the theme's stylesheet to the minified version when not in debug mode.
	 *
	 * @since 1.0.0
	 *
	 * @param string $stylesheet_uri Stylesheet URI
	 *
	 * @return string
	 */
	function change_theme_stylesheet_uri_to_min_version( $stylesheet_uri ) {
		if ( site_is_in_debug_mode() ) {
			return $stylesheet_uri;
		}

		$minified_stylesheet_uri = get_stylesheet_directory_uri() . '/style.min.css';

		return file_exists( $minified_stylesheet_uri )
			? $minified_stylesheet_uri
			: $stylesheet_uri;
	}

endif;

if ( ! function_exists( 'get_file_current_version_number' ) ) :
	/**
	 * Get the asset file's current version number.  We use `filemtime` to get
	 * the file's modification time.
	 *
	 * @since 1.0.0
	 *
	 * @param string $asset_file Asset's path/to/filename.extension
	 *
	 * @return string
	 */
	function get_file_current_version_number( $asset_file ) {
		return filemtime( $asset_file );
	}
endif;

if ( ! function_exists( 'get_theme_version_number' ) ) :
	/**
	 * Get the current theme's version, by using its stylesheet's
	 * last modification time.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	function get_theme_version_number() {
		$stylesheet = get_stylesheet_directory() . 'style.css';

		return get_asset_file_current_version_number( $stylesheet );
	}
endif;

if ( ! function_exists( 'site_is_in_debug_mode' ) ) :
	/**
	 * Checks if the website is in debug mode by using `WP_DEBUG`, which is
	 * set in the wp-config.php file.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	function site_is_in_debug_mode() {
		if ( ! defined( 'SCRIPT_DEBUG' ) ) {
			return false;
		}

		return (bool) SCRIPT_DEBUG;
	}
endif;
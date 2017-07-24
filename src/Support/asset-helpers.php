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

	add_filter( 'stylesheet_uri', 'change_theme_stylesheet_uri_to_min_version', 9999, 2 );
	/**
	 * Change the theme's stylesheet to the minified version when not in debug mode.
	 *
	 * @since 1.0.0
	 *
	 * @param string $stylesheet_uri Stylesheet URL
	 * @param string $stylesheet_dir_uri Stylesheet's directory URL
	 *
	 * @return string
	 */
	function change_theme_stylesheet_uri_to_min_version( $stylesheet_uri, $stylesheet_dir_uri ) {
		if ( site_is_in_development_mode() ) {
			return $stylesheet_uri;
		}

		$minified_stylesheet_file = '/style.min.css';

		return file_exists( get_stylesheet_directory() . $minified_stylesheet_file )
			? $stylesheet_dir_uri . $minified_stylesheet_file
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

if ( ! function_exists( 'get_theme_version' ) ) :
	/**
	 * Get the current theme's version.  There are 2 strategies in this function:
	 *
	 * 1. When in development/debug mode, we grab the style.css file's modification time.
	 * 2. Else and if enabled, we grab the style.css Version parameter.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $use_stylesheet_version Use the stylesheet's version number when
	 *                                     not in development/debug mode.
	 * @return string
	 */
	function get_theme_version( $use_stylesheet_version = true ) {
		if ( ! $use_stylesheet_version || site_is_in_development_mode() ) {
			$stylesheet = get_stylesheet_directory() . '/style.css';

			return get_file_current_version_number( $stylesheet );
		}

		$theme = wp_get_theme();

		return $theme->get('Version');
	}
endif;

if ( ! function_exists( 'site_is_in_development_mode' ) ) :
	/**
	 * Checks if the website is in debug mode by using `WP_DEBUG`, which is
	 * set in the wp-config.php file.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	function site_is_in_development_mode() {
		if ( ! defined( 'SCRIPT_DEBUG' ) ) {
			return false;
		}

		return (bool) SCRIPT_DEBUG;
	}
endif;

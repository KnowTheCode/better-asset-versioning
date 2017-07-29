<?php
/**
 * URL Converter
 *
 * @package     KnowTheCode\BetterAssetVersioning
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://KnowTheCode.io
 * @license     GNU-2.0+
 */

namespace KnowTheCode\BetterAssetVersioning;

class URLConverter {

	protected $config;
	protected $local_url = '';

	public function __construct( array $config ) {
		$this->config = $config;

		$this->init_parameters();
		$this->init_events();
	}

	/**
	 * Initialize the parameters/properties.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_parameters() {
		$parsed_site_url = parse_url( home_url() );
		if ( isset( $parsed_site_url['host'] ) ) {
			$this->local_url = $parsed_site_url['host'];
		}
	}

	/**
	 * Initialize the events.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_events() {
		add_filter( 'script_loader_src', array( $this, 'run' ), 9999, 2 );
		add_filter( 'style_loader_src', array( $this, 'run' ), 9999, 2 );
	}

	/**
	 * Process the stylesheet or script's URL.  If it passes
	 * the checks, then the version number will be converted
	 * into the filename.{version number}.ext.
	 *
	 * @since 1.0.0
	 *
	 * @param string $asset_url Style/script's URL
	 * @param string $handle Style/script's enqueued handle.
	 *
	 * @return string
	 */
	public function run( $asset_url, $handle ) {
		if ( is_admin() ) {
			return $asset_url;
		}

		if ( $this->skip_this_asset( $handle ) ) {
			return $asset_url;
		}

		$parsed_url = parse_url( $asset_url );
		if ( ! $this->is_well_formed( $parsed_url ) ) {
			return $asset_url;
		}

		if ( ! $this->has_version_query_string( $parsed_url ) ) {
			return $asset_url;
		}

		if ( ! $this->is_local_asset( $parsed_url['host'] ) ) {
			return remove_query_arg( 'ver', $asset_url );
		}

		// another check that looks for numbers on the end of the
		// filename.

		return $this->do_conversion( $asset_url );
	}

	/**
	 * Checks if the URL is well-formed.
	 *
	 * @since 1.0.0
	 *
	 * @param array $parsed_url Parsed URL
	 *
	 * @return bool
	 */
	protected function is_well_formed( array $parsed_url ) {
		return isset( $parsed_url['host'] );
	}

	/**
	 * Checks if the URL has the verion's query string.
	 *
	 * @since 1.0.0
	 *
	 * @param array $parsed_url Parsed URL
	 *
	 * @return bool
	 */
	protected function has_version_query_string( array $parsed_url ) {
		if ( ! isset( $parsed_url['query'] ) ) {
			return false;
		}

		return str_has_substring( $parsed_url['query'], $this->config['version_query_key'] );
	}

	/**
	 * Checks if this asset should be skipped by comparing to
	 * the configured handles.
	 *
	 * @since 1.0.0
	 *
	 * @param string $handle The script/style's enqueued handle
	 *
	 * @return bool
	 */
	protected function skip_this_asset( $handle ) {
		return in_array( $handle, $this->config['skip_these_assets'] );
	}

	/**
	 * Checks if the asset is local to the website.
	 *
	 * External assets would be:
	 *  - fontawesome
	 *  - Google fonts
	 *  - Bootstrap
	 *  - Zurb's Foundation
	 *
	 * @since 1.0.0
	 *
	 * @param $asset_url_host
	 *
	 * @return bool
	 */
	protected function is_local_asset( $asset_url_host ) {
		return ( $asset_url_host === $this->local_url );
	}

	/**
	 * Do the conversion.
	 *
	 * @since 1.0.0
	 *
	 * @param string $asset_url The script/style's URL
	 *
	 * @return string
	 */
	protected function do_conversion( $asset_url ) {
		return preg_replace(
			'/\.(min.js|min.css|js|css)\?ver=(.+)$/',
			'.$2.$1',
			$asset_url
		);
	}

}

<?php
/**
 * Asset Handler
 *
 * This class handles:
 *
 * 1. Validating if we should reassemble the asset's URL
 * 2. If yes, then it moves the version number into the URL.
 *
 * @package     KnowTheCode\BetterAssetVersioning
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://KnowTheCode.io
 * @license     GNU-2.0+
 */

namespace KnowTheCode\BetterAssetVersioning;

class AssetHandler {

	protected $config;

	protected $local_url = array();
	protected $local_scheme = '';
	protected $version_query_key_with_separator;
	protected $version_query_key = 'ver=';
	protected $version_query_key_length = 4;

	public function __construct( array $config ) {
		$this->config = $config;

		$this->init_parameters();
		$this->init_events();
	}

	/**
	 * Initialize the parameters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_parameters() {
		$this->local_url = parse_url( home_url() );

		$this->version_query_key_with_separator = $this->config['version_query_key_with_separator'];
		$this->version_query_key                = $this->config['version_query_key'];
		$this->version_query_key_length         = strlen( $this->version_query_key );
	}

	/**
	 * Initialize the events by registering the callbacks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_events() {
		add_filter( 'script_loader_src', array( $this, 'change_src_to_embed_version' ), 9999 );
		add_filter( 'style_loader_src', array( $this, 'change_src_to_embed_version' ), 9999 );
	}

	/**
	 * Change the asset's URL to embed the version number instead of
	 * having it as a query parameter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $asset_url URL of the asset
	 *
	 * @return string
	 */
	function change_src_to_embed_version( $asset_url ) {
		$asset_url_parts = parse_url( $asset_url );
		if ( false === $asset_url_parts ) {
			return $asset_url;
		}

		if ( $this->is_okay_to_embed_version( $asset_url, $asset_url_parts ) ) {
			return $this->reassemble( $asset_url_parts );
		}

		return $asset_url;
	}

	/**
	 * Let's reassemble the URL with the version number as a folder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $url_parts Parts of the URL
	 *
	 * @return string
	 */
	protected function reassemble( array $url_parts ) {
		$version_number      = $this->get_version_number( $url_parts['query'] );
		$asset_file_pathinfo = pathinfo( $url_parts['path'] );

		$scheme = isset( $url_parts['scheme'] )
			? $url_parts['scheme'] . ':'
			: '';

		return sprintf( '%s//%s%s/betterassetversioning-%s/%s',
			$scheme,
			$url_parts['host'],
			$asset_file_pathinfo['dirname'],
			$version_number,
			$asset_file_pathinfo['basename']
		);
	}

	/**
	 * Get the version number.
	 *
	 * It uses the helper function get_substring().  The starting position is
	 * 4 characters in, as the query key is ver=.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url_query
	 *
	 * @return string
	 */
	protected function get_version_number( $url_query ) {
		$version_number = get_substring( $url_query, $this->version_query_key_length );

		return trim( $version_number );
	}

	/**
	 * Checks if we should embed the version for this particular
	 * asset URL. The ones that we don't want to touch include:
	 *
	 * 1. Anything when we're in the admin area.
	 * 2. Any URL that doesn't have the ?ver= query parameter. Why? There's nothing to move.
	 * 3. External URLs that are not from this particular local website, i.e. for example Google Fonts.
	 *
	 * @since 1.0.0
	 *
	 * @param string $asset_url Asset's URL
	 * @param array $asset_url_parts Array of the parts of the asset's URL
	 *
	 * @return bool
	 */
	function is_okay_to_embed_version( $asset_url, array $asset_url_parts ) {
		if ( is_admin() ) {
			return false;
		}

		if ( ! str_has_substring( $asset_url, $this->version_query_key_with_separator ) !== false ) {
			return false;
		}

		return $this->is_local_asset( $asset_url_parts );
	}

	/**
	 * Check that the asset's host matches this local website.
	 *
	 * Why? We don't want to work on assets that are external to our website, e.g. Google Fonts.
	 * Why? Those assets need to have their own version handlers.
	 *
	 * @since 1.0.0
	 *
	 * @param $asset_url_parts
	 *
	 * @return bool
	 */
	protected function is_local_asset( $asset_url_parts ) {
		if ( ! isset( $asset_url_parts['host'] ) ) {
			return false;
		}

		return ( $asset_url_parts['host'] === $this->local_url['host'] );
	}

}
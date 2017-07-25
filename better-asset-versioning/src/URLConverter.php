<?php
/**
 * URL Converter
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

class URLConverter {

	protected $config;

	protected $local_url = array();
	protected $local_scheme = '';
	protected $version_query_key_with_separator;
	protected $version_query_key = 'ver=';
	protected $version_query_key_length = 4;
	protected $inline_version = 'inline';

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

		$this->inline_version                   = $this->config['inline_version'];
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
		add_filter( 'script_loader_src', array( $this, 'run_version_converter' ), 9999, 2 );
		add_filter( 'style_loader_src', array( $this, 'run_version_converter' ), 9999, 2 );
	}

	/**
	 * Change the asset's URL to embed the version number instead of
	 * having it as a query parameter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $asset_url URL of the asset
	 * @param string $handle Handle of the asset
	 *
	 * @return string
	 */
	function run_version_converter( $asset_url, $handle ) {
		if ( $this->skip_this_asset( $handle ) ) {
			return $asset_url;
		}

		$version_number = $this->get_enqueued_version_number( $handle );
		if ( $version_number == null ) {
			return $asset_url;
//			return remove_query_arg( 'ver', $asset_url );
		}

		return $this->change_src_to_embed_version( $asset_url, $version_number );
	}

	protected function skip_this_asset( $handle ) {
		return in_array( $handle, $this->config['skip_these_assets'] );
	}

	/**
	 * Change the asset's URL to embed the version number instead of
	 * having it as a query parameter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $asset_url URL of the asset
	 * @param string $version_number Asset's version number
	 *
	 * @return string
	 */
	function change_src_to_embed_version( $asset_url, $version_number ) {
		$asset_url_parts = parse_url( $asset_url );
		if ( false === $asset_url_parts ) {
			return $asset_url;
		}

		if ( ! $this->is_okay_to_embed_version( $asset_url, $asset_url_parts ) ) {
			return $asset_url;
		}

		return preg_replace(
			'/\.(min.js|min.css|js|css)\?ver=(.+)$/',
			'.$2.$1',
			$asset_url
		);
	}

	/**
	 * Get the original "registered" enqueued version number
	 *
	 * @since 1.0.0
	 *
	 * @param string $handle Handle of the asset
	 *
	 * @return string
	 */
	protected function get_enqueued_version_number( $handle ) {
		$wp_scripts = wp_scripts();
		$wp_styles  = wp_styles();

		if ( array_key_exists( $handle, $wp_scripts->registered ) ) {
			$asset_config = $wp_scripts->registered[ $handle ];
		} elseif ( array_key_exists( $handle, $wp_styles->registered ) ) {
			$asset_config = $wp_styles->registered[ $handle ];
		} else {
			return null;
		}

		return $asset_config->ver;
	}

	/**
	 * Check if we should skip this conversion.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $version_number Version number
	 *
	 * @return bool
	 */
	protected function bypass_and_strip( $version_number ) {
		return ( $version_number === $this->inline_version || $version_number == null );
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

		if ( $this->ends_with_number( $asset_url_parts['path'] ) ) {
			return false;
		}

		return $this->is_local_asset( $asset_url_parts );
	}

	/**
	 * Checks if the file ends with a number. Why? Some files may already have
	 * the version number appended as a suffix.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file
	 *
	 * @return bool
	 */
	function ends_with_number( $file ) {
		$paths = explode( '/', $file );
		$file  = array_pop( $paths );

		$file_parts = explode( '.', $file );
		$filename   = array_shift( $file_parts );
		if ( ! $filename ) {
			return false;
		}

		return is_numeric( $filename[ strlen( $filename ) - 1 ] );
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

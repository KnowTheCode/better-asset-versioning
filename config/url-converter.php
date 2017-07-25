<?php
/**
 * Asset URL Converter Runtime configuration parameters.
 *
 * @package     KnowTheCode\BetterAssetVersioning
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://KnowTheCode.io
 * @license     GNU-2.0+
 */

namespace KnowTheCode\BetterAssetVersioning;

return array(
	'is_enabled'                       => true,
	// Enqueued version argument value to alert us
	// to strip off the version and NOT do our conversion.
	'inline_version'                   => 'inline',
	'version_query_key_with_separator' => '?ver=',
	'version_query_key'                => 'ver=',
	'version_query_key_length'         => 4,
	'skip_these_assets' => array(
		'jquery-core',
		'jquery-migrate',
		'jquery-ui-core',
		'jquery-ui-datepicker',
		'jquery-ui-draggable',
		'jquery-ui-droppable',
	),
);
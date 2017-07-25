<?php
/**
 * PHP String Checker and Converter Helper Functions.
 *
 * @package     KnowTheCode\BetterAssetVersioning
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://KnowTheCode.io
 * @license     GNU-2.0+
 */

namespace KnowTheCode\BetterAssetVersioning;

if ( ! function_exists( 'get_substring' ) ) :
	/**
	 * Get part of a string using safe character encoding
	 * to ensure the characters are properly searched.
	 *
	 * PHP substr() uses Unicode character, which can cause search
	 * issues with certain characters in languages, such as A-umlaut (Ä).
	 *
	 * @since 1.0.0
	 *
	 * @param string $haystack String to search.
	 * @param int $startPosition Starting position to begin the search
	 * @param int|null $length
	 * @param string $characterEncoding default is 'UTF-8'
	 *
	 * @return string
	 */
	function get_substring( $haystack, $startPosition, $length = null, $characterEncoding = 'UTF-8' ) {
		return mb_substr(
			$haystack,
			$startPosition,
			$length,
			$characterEncoding
		);
	}
endif;

if ( ! function_exists( 'str_has_substring' ) ) :
	/**
	 * Checks if a string has a substring
	 *
	 * @since 1.0.0
	 *
	 * @param string $haystack  The string to be searched
	 * @param string $needle    The character or substring to
	 *                          find within the $haystack
	 * @param string $encoding  Default is UTF-8
	 *
	 * @return bool
	 */
	function str_has_substring( $haystack, $needle, $encoding = 'UTF-8' ) {
		return ( mb_strpos( $haystack, $needle, 0, $encoding ) !== false );
	}
endif;

if ( ! function_exists( 'str_starts_with' ) ) :
	/**
	 * Checks if a string starts with a character or substring.
	 *
	 * @since 1.0.0
	 *
	 * @param string $haystack  The string to be searched
	 * @param string $needle    The character or substring to
	 *                          find at the start of the $haystack
	 * @param string $encoding  Default is UTF-8
	 *
	 * @return bool
	 */
	function str_starts_with( $haystack, $needle, $encoding = 'UTF-8' ) {
		$needle_length = mb_strlen( $needle, $encoding );

		return ( mb_substr( $haystack, 0, $needle_length, $encoding ) === $needle );
	}
endif;

if ( ! function_exists( 'str_ends_with' ) ) :
	/**
	 * Checks if a string (the haystack) ends with a character or substring (the needle).
	 *
	 * @since 1.0.0
	 *
	 * @param string $haystack  The string to be searched
	 * @param string $needle    The character or substring to
	 *                          find at the end of the $haystack
	 * @param string $encoding  Default is UTF-8
	 *
	 * @return bool
	 */
	function str_ends_with( $haystack, $needle, $encoding = 'UTF-8' ) {
		$starting_offset = - mb_strlen( $needle, $encoding );

		return ( mb_substr( $haystack, $starting_offset, null, $encoding ) === $needle );
	}
endif;

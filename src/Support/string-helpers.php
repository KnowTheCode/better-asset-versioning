<?php
/**
 * PHP String Checker and Converter Helper Functions.
 *
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://KnowTheCode.io
 * @license     GNU-2.0+
 */

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

if ( ! function_exists( 'str_has_substring' ) ) {
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
}

if ( ! function_exists( 'str_starts_with' ) ) {
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
}

if ( ! function_exists( 'str_ends_with' ) ) {
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
}

if ( ! function_exists( 'truncate_by_number_characters' ) ) {
	/**
	 * Truncates the string by the specified number of characters.
	 *
	 * @since 1.0.0
	 *
	 * @param string $string_to_truncate  The string to be truncated.
	 * @param integer $character_limit Number of characters to limit the string by
	 *                                 Default: 100
	 * @param string $ending_suffix  Ending characters appending to the end of
	 *                               the truncated string. Default: '...'
	 * @param string $encoding  Default is UTF-8
	 *
	 * @return bool
	 */
	function truncate_by_number_characters( $string_to_truncate, $character_limit = 100, $ending_suffix = '...', $encoding = 'UTF-8' ) {
		if ( mb_strwidth( $string_to_truncate, $encoding ) <= $character_limit ) {
			return $string_to_truncate;
		}

		$string_to_truncate = wp_strip_all_tags( $string_to_truncate );

		$truncated_string = mb_strimwidth( $string_to_truncate, 0, $character_limit, '', $encoding );

		return rtrim( $truncated_string ) . $ending_suffix;
	}
}

if ( ! function_exists( 'truncate_by_words' ) ) :
	/**
	 * Truncate the given string by the specified the number of words.
	 *
	 * @since 1.0.0
	 *
	 * @param string $string_to_truncate The string to truncate
	 * @param int $word_limit Number of characters to limit the string to
	 * @param string $ending_characters (Optional) Characters to append to the end of the truncated string.
	 *
	 * @return string
	 */
	function truncate_by_words( $string_to_truncate, $word_limit = 100, $ending_characters = '...' ) {
		$string_to_truncate = wp_strip_all_tags( $string_to_truncate );

		preg_match( '/^\s*+(?:\S++\s*+){1,' . $word_limit . '}/u', $string_to_truncate, $matches );

		if ( ! isset( $matches[0] ) ) {
			return $string_to_truncate;
		}

		if ( mb_strlen( $string_to_truncate ) === mb_strlen( $matches[0] ) ) {
			return $string_to_truncate;
		}

		return rtrim( $matches[0] ) . $ending_characters;
	}
endif;

if ( ! function_exists( 'convert_str_to_lowercase' ) ) :
	/**
	 * Converts the string to lowercase and is UTF-8 safe.
	 *
	 * @since 1.0.0
	 *
	 * @param string $string_to_convert The string to truncate
	 * @param string $encoding (Default) UTF-8
	 *
	 * @return string
	 */
	function convert_str_to_lowercase( $string_to_convert, $encoding = 'UTF-8' ) {
		return mb_strtolower( $string_to_convert, $encoding );
	}
endif;

if ( ! function_exists( 'convert_str_to_uppercase' ) ) :
	/**
	 * Converts the string to uppercase and is UTF-8 safe.
	 *
	 * @since 1.0.0
	 *
	 * @param string $string_to_convert The string to truncate
	 * @param string $encoding (Default) UTF-8
	 *
	 * @return string
	 */
	function convert_str_to_uppercase( $string_to_convert, $encoding = 'UTF-8' ) {
		return mb_strtoupper( $string_to_convert, $encoding );
	}
endif;

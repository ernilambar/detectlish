<?php
/**
 * Detectlish
 *
 * @package Detectlish
 */

namespace Nilambar\Detectlish;

/**
 * Detectlish class.
 *
 * @since 1.0.0
 */
class Detectlish {

	/**
	 * Detect if the given string is English or not.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text The string to check.
	 * @return bool True if the string is English, false otherwise.
	 */
	public function isEnglish( string $text ): bool {
		return ( 1 === preg_match( '/^[\x00-\x7F]*$/', $text ) );
	}
}

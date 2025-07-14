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
	 * Detect if the given string is English.
	 *
	 * This method considers a string English if the non-English certainty is less than 0.5.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text The string to check.
	 * @return bool True if the string is English, false otherwise.
	 */
	public function isEnglish( string $text ): bool {
		return ( false === $this->isNonEnglish( $text, 0.5 ) );
	}

	/**
	 * Detect if the given string is non-English based on certainty threshold.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text      The string to analyze.
	 * @param float  $threshold The certainty threshold (between 0 and 1). Default 0.5.
	 *
	 * @return bool True if the string is non-English with certainty above threshold, false otherwise.
	 *
	 * @throws \InvalidArgumentException If threshold is not between 0 and 1.
	 */
	public function isNonEnglish( string $text, float $threshold = 0.5 ): bool {
		if ( ( 0 > $threshold ) || ( 1 < $threshold ) ) {
			throw new \InvalidArgumentException( 'Threshold must be between 0 and 1.' );
		}

		if ( 0 === strlen( $text ) ) {
			return false;
		}

		// Character-level certainty (ignore emojis).
		$totalCount    = 0;
		$nonAsciiCount = 0;
		$len           = mb_strlen( $text );

		for ( $i = 0; $i < $len; $i++ ) {
			$char = mb_substr( $text, $i, 1 );

			if ( $this->isEmoji( $char ) ) {
				continue;
			}

			++$totalCount;

			if ( 1 !== preg_match( '/^[\x00-\x7F]$/', $char ) ) {
				++$nonAsciiCount;
			}
		}

		// Word-level certainty (ignore emojis in words).
		$words     = preg_split( '/\W+/u', strtolower( $text ), -1, PREG_SPLIT_NO_EMPTY );
		$wordList  = $this->getEnglishWordList();
		$matched   = 0;
		$wordCount = 0;

		foreach ( $words as $word ) {
			// If the word is just an emoji, skip it.
			if ( $this->isEmoji( $word ) ) {
				continue;
			}

			++$wordCount;

			if ( isset( $wordList[ $word ] ) ) {
				++$matched;
			}
		}

		// If, after filtering, the string contains only emojis, return false (not non-English).
		if ( 0 === $totalCount && 0 === $wordCount ) {
			return false;
		}

		$charCertainty = ( $totalCount > 0 ) ? ( $nonAsciiCount / $totalCount ) : 0;
		$wordCertainty = 1;

		if ( $wordCount > 0 ) {
			$wordCertainty = 1 - ( $matched / $wordCount );
		}

		// Combine with equal weight.
		$certainty = 0.5 * $charCertainty + 0.5 * $wordCertainty;

		return ( $certainty >= $threshold );
	}

	/**
	 * Check if a string is an emoji (covers most emoji blocks and ZWJ sequences).
	 *
	 * @since 1.0.0
	 *
	 * @param string $str The character or string to check.
	 * @return bool True if the string is an emoji, false otherwise.
	 */
	private function isEmoji( string $str ): bool {
		return (bool) preg_match( '/([\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]|[\x{FE00}-\x{FE0F}]|[\x{1F900}-\x{1F9FF}]|[\x{1FA70}-\x{1FAFF}]|[\x{200D}]|[\x{2300}-\x{23FF}])/xu', $str );
	}

	/**
	 * Get the English word list.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, bool> Associative array of English words.
	 */
	private function getEnglishWordList(): array {
		static $wordList = null;

		if ( null === $wordList ) {
			$file     = __DIR__ . '/../data/english-words.txt';
			$wordList = [];

			if ( file_exists( $file ) ) {
				$lines = file( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

				foreach ( $lines as $line ) {
					$wordList[ trim( strtolower( $line ) ) ] = true;
				}
			}
		}
		return $wordList;
	}
}

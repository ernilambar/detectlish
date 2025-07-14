<?php
/**
 * Tests for the Detectlish class.
 *
 * @package plugin-check
 */

namespace Nilambar\Detectlish\Tests;

use Nilambar\Detectlish\Detectlish;
use PHPUnit\Framework\TestCase;

class DetectlishTest extends TestCase {

	public function testIsEnglishWithEnglishText() {
		$detector = new Detectlish();
		$this->assertTrue( $detector->isEnglish( 'This is an English sentence.' ) );
		$this->assertTrue( $detector->isEnglish( 'Hello, world! 123' ) );
	}

	public function testIsEnglishWithNonEnglishText() {
		$detector = new Detectlish();
		$this->assertFalse( $detector->isEnglish( 'यह एक हिंदी वाक्य है।' ) );
		$this->assertFalse( $detector->isEnglish( '这是一个中文句子。' ) );
		$this->assertFalse( $detector->isEnglish( 'Это русское предложение.' ) );
	}

	public function testIsNonEnglishWithNonEnglishTextHighThreshold() {
		$detector = new Detectlish();
		$this->assertTrue( $detector->isNonEnglish( 'यह एक हिंदी वाक्य है।' ) );
		$this->assertTrue( $detector->isNonEnglish( '这是一个中文句子。' ) );
		$this->assertTrue( $detector->isNonEnglish( 'Это русское предложение.' ) );
	}

	public function testIsNonEnglishWithEnglishText() {
		$detector = new Detectlish();
		$this->assertFalse( $detector->isNonEnglish( 'This is an English sentence.' ) );
		$this->assertFalse( $detector->isNonEnglish( 'Hello, world! 123' ) );
	}

	public function testIsNonEnglishWithPureEnglishText() {
		$detector = new Detectlish();
		$this->assertFalse( $detector->isNonEnglish( 'This is a simple English sentence.' ) );
		$this->assertFalse( $detector->isNonEnglish( 'Hello world' ) );
	}

	public function testIsNonEnglishWithPureNonEnglishText() {
		$detector = new Detectlish();
		$this->assertTrue( $detector->isNonEnglish( '这是一个中文句子。' ) );
		$this->assertTrue( $detector->isNonEnglish( 'यह एक हिंदी वाक्य है।' ) );
		$this->assertTrue( $detector->isNonEnglish( 'Это русское предложение.' ) );
	}

	public function testIsNonEnglishWithMixedText() {
		$detector = new Detectlish();
		$this->assertTrue( $detector->isNonEnglish( 'This is English और हिंदी mixed.', 0.3 ) );
		$this->assertFalse( $detector->isNonEnglish( 'This is English और हिंदी mixed.', 0.8 ) );
	}

	public function testIsNonEnglishWithEnglishTextAndNonAscii() {
		$detector = new Detectlish();
		$this->assertTrue( $detector->isNonEnglish( 'Café résumé naïve façade' ) );
	}

	public function testIsNonEnglishWithNonEnglishTextContainingEnglishWords() {
		$detector = new Detectlish();
		$this->assertTrue( $detector->isNonEnglish( 'Hello 你好 world 世界', 0.3 ) );
	}

	public function testIsNonEnglishWithEmptyString() {
		$detector = new Detectlish();
		$this->assertFalse( $detector->isNonEnglish( '' ) );
	}

	public function testIsNonEnglishWithInvalidThreshold() {
		$detector = new Detectlish();
		$this->expectException( \InvalidArgumentException::class );
		$detector->isNonEnglish( 'Test', -0.1 );
		$this->expectException( \InvalidArgumentException::class );
		$detector->isNonEnglish( 'Test', 1.1 );
	}

	public function testIsNonEnglishWithEmojis() {
		$detector = new Detectlish();
		// Only emojis should not be considered non-English.
		$this->assertFalse( $detector->isNonEnglish( '😀😂👍' ) );
		// English with emojis should still be English.
		$this->assertFalse( $detector->isNonEnglish( 'Hello world 😀😂' ) );
		// Non-English with emojis should still be non-English.
		$this->assertTrue( $detector->isNonEnglish( 'यह एक हिंदी वाक्य है। 😀😂' ) );
	}
}

<?php

namespace Nilambar\Detectlish\Tests;

use Nilambar\Detectlish\Detectlish;
use PHPUnit\Framework\TestCase;

class DetectlishTest extends TestCase
{
    public function testIsEnglishWithEnglishText()
    {
        $detector = new Detectlish();
        $this->assertTrue($detector->isEnglish('This is an English sentence.'));
        $this->assertTrue($detector->isEnglish('Hello, world! 123'));
    }

    public function testIsEnglishWithNonEnglishText()
    {
        $detector = new Detectlish();
        $this->assertFalse($detector->isEnglish('यह एक हिंदी वाक्य है।'));
        $this->assertFalse($detector->isEnglish('这是一个中文句子。'));
        $this->assertFalse($detector->isEnglish('Это русское предложение.'));
    }
}

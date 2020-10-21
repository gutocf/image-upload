<?php

namespace Gutocf\Test\TestCase\Lib\Filename;

use Gutocf\ImageUpload\Lib\Filename\FilenameHandler;
use Cake\TestSuite\TestCase;

class FilenameHandlerTest extends TestCase {

	public function testSuffix() {
		$filename = TEST_ROOT . 'tests' . DS . 'TestCase' . DS . 'Lib' . DS . 'Resizer' . DS . 'image_portrait.png';
		$expected = TEST_ROOT . 'tests' . DS . 'TestCase' . DS . 'Lib' . DS . 'Resizer' . DS . 'image_portrait-0.png';
		$actual = FilenameHandler::applyNumericSuffix($filename);
		$this->assertEquals($expected, $actual);
	}
}

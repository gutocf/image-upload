<?php

namespace Gutocf\Test\TestCase\Lib\Filename;

use Gutocf\ImageUpload\Lib\Filename\FilenameHandler;
use Cake\TestSuite\TestCase;

class FilenameHandlerTest extends TestCase {

	public function testSuffix() {
		$filename = TEST_ROOT . 'tests' . DS . 'TestCase' . DS . 'Lib' . DS . 'Resizer' . DS . 'image_resizer_test.png';
		$expected = TEST_ROOT . 'tests' . DS . 'TestCase' . DS . 'Lib' . DS . 'Resizer' . DS . 'image_resizer_test-0.png';
		$actual = FilenameHandler::applyNumericSuffix($filename);
		$this->assertEquals($expected, $actual);
	}
}

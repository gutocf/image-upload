<?php

namespace Gutocf\Test\TestCase\Lib\Filename;

use Gutocf\ImageUpload\Lib\Filename\FilenameHandler;
use Cake\TestSuite\TestCase;

class FilenameHandlerTest extends TestCase {

	public function testSuffix() {
		$actual = FilenameHandler::applyNumericSuffix(TEST_ROOT . 'TestCase\Lib\ImageUpload\Resizer\image_resizer_test.png');
		$expected = TEST_ROOT . 'TestCase\Lib\ImageUpload\Resizer\image_resizer_test-0.png';
		$this->assertEquals($expected, $actual);
	}
}

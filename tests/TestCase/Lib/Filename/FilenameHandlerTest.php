<?php

	 namespace App\Test\TestCase\Lib\ImageUpload\Filename;

	 use App\Lib\ImageUpload\Filename\FilenameHandler;
	 use Cake\TestSuite\TestCase;

	 class FilenameHandlerTest extends TestCase {

		 public function testSuffix() {
			 $actual = FilenameHandler::applyNumericSuffix('C:\Users\gutoc\Documents\www\www.galerianc.com.br\tests\TestCase\Lib\ImageUpload\Resizer\image_resizer_test.png');
			 $expected = 'C:\Users\gutoc\Documents\www\www.galerianc.com.br\tests\TestCase\Lib\ImageUpload\Resizer\image_resizer_test-0.png';
			 $this->assertEquals($expected, $actual);
		 }

	 }
	 
<?php

namespace Gutocf\ImageUpload\Test\TestCase\Lib\Utils;

use Cake\TestSuite\TestCase;
use Gutocf\ImageUpload\Lib\Utils\Text;

class TextTest extends TestCase {

	public function testSuffix() {
		$actual = Text::suffix('guto', 'ferreira');
		$this->assertEquals('guto_ferreira', $actual);
	}

	public function testSuffixWithSeparator() {
		$this->assertEquals('guto.ferreira', Text::suffix('guto', 'ferreira', '.'));
		$this->assertEquals('guto*ferreira', Text::suffix('guto', 'ferreira', '*'));
		$this->assertEquals('guto"ferreira', Text::suffix('guto', 'ferreira', '"'));
	}

	public function testGetNextNumericSuffix() {
		$this->assertEquals('1', Text::getNextNumericSuffix('teste-0'));
		$this->assertEquals('100', Text::getNextNumericSuffix('teste-99'));
		$this->assertEquals('10', Text::getNextNumericSuffix('teste_9', '_'));
		$this->assertEquals('100', Text::getNextNumericSuffix('teste_99', '_'));
	}

	public function testIncNumericSuffix() {
		$this->assertEquals('teste-1', Text::incNumericSuffix('teste-0'));
		$this->assertEquals('teste-100', Text::incNumericSuffix('teste-99'));
		$this->assertEquals('teste_10', Text::incNumericSuffix('teste_9', '_'));
		$this->assertEquals('teste_100', Text::incNumericSuffix('teste_99', '_'));
	}

	public function testClearNumericSuffix() {
		$this->assertEquals('teste', Text::clearNumericSuffix('teste-0'));
		$this->assertEquals('teste', Text::clearNumericSuffix('teste-1'));
		$this->assertEquals('teste', Text::clearNumericSuffix('teste-01'));
		$this->assertEquals('teste', Text::clearNumericSuffix('teste-10'));
		$this->assertEquals('teste', Text::clearNumericSuffix('teste'));
	}

	public function testClearNumericSuffixWithSeparator() {
		$this->assertEquals('teste', Text::clearNumericSuffix('teste.0', '.'));
		$this->assertEquals('teste', Text::clearNumericSuffix('teste=1', '='));
		$this->assertEquals('teste', Text::clearNumericSuffix('teste2', ''));
		$this->assertEquals('teste', Text::clearNumericSuffix('teste_01', '_'));
		$this->assertEquals('teste', Text::clearNumericSuffix('testex10', 'x'));
		$this->assertEquals('teste', Text::clearNumericSuffix('testexxx10', 'xxx'));
		$this->assertEquals('teste', Text::clearNumericSuffix('teste', '.'));
	}
}

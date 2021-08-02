<?php

namespace Gutocf\ImageUpload\Test\TestCase\Lib\Filename;

use Cake\TestSuite\TestCase;
use Gutocf\ImageUpload\Lib\Filename\TokenProcessor;

/**
 * @property TokenProcessor $TokenProcessor
 */
class TokenProcessorTest extends TestCase
{

	private $TokenProcessor;

	public function setUp(): void
	{
		parent::setUp();
	}

	public function testReplaceDate()
	{
		$expected = date('Y-m-d');
		$actual = TokenProcessor::getInstance()
			->replace('{year}-{month}-{day}');
		$this->assertEquals($expected, $actual);
	}

	public function testReplaceTimestamp()
	{
		$expected = time();
		$actual = TokenProcessor::getInstance()
			->setReplacement('time', $expected)
			->replace('{time}');
		$this->assertEquals($expected, $actual);
	}

	public function testReplaceDS()
	{
		$expected = DS;
		$actual = TokenProcessor::getInstance()
			->replace('{DS}');
		$this->assertEquals($expected, $actual);

		$expected = '-';
		$actual = TokenProcessor::getInstance()
			->setReplacement('DS', '-')
			->replace('{DS}');
		$this->assertEquals($expected, $actual);
	}

	public function testReplaceModelAlias()
	{
		$expected = 'model-table-alias';
		$actual = TokenProcessor::getInstance()
			->setModelAlias('ModelTableAlias')
			->replace('{model}');
		$this->assertEquals($expected, $actual);
	}

	public function testReplaceFilename()
	{
		$expected = 'praia-florianopolis.png';
		$actual = TokenProcessor::getInstance()
			->setFilename('Praia Florianópolis.PNG')
			->replace('{filename}.{ext}');
		$this->assertEquals($expected, $actual);
	}
}

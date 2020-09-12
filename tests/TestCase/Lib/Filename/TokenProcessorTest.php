<?php

namespace Gutocf\ImageUpload\Test\TestCase\Lib\Filename;

use Cake\TestSuite\TestCase;
use Gutocf\ImageUpload\Lib\Filename\TokenProcessor;

/**
 * @property TokenProcessor $TokenProcessor
 */
class TokenProcessorTest extends TestCase {

	private $TokenProcessor;

	public function setUp(): void {
		parent::setUp();
		$this->TokenProcessor = new TokenProcessor('TableName', 'fieldname', 'Praia Florianópolis.png');
	}

	public function testReplace() {
		$this->assertEquals('tablename' . DS . date('Y') . DS . date('m') . DS . date('d'), $this->TokenProcessor->replace('{model}{DS}{year}{DS}{month}{DS}{day}'));
		$this->assertEquals('praia-florianopolis.png', $this->TokenProcessor->replace('{slug}.{ext}'));
	}
}

<?php

namespace Gutocf\Test\TestCase\Lib\Filename;

use Gutocf\ImageUpload\Lib\Filename\PathBuilder;
use Cake\TestSuite\TestCase;

/**
 * @property PathBuilder $pathBuilder
 */
class PathBuilderTest extends TestCase {

	private $pathBuilder;

	public function setUp(): void {
		parent::setUp();
	}

	public function testExistentFile() {
		$config = [
			'baseDir' => TEST_ROOT . 'tests' . DS . 'TestCase' . DS . 'Lib',
			'dir' => 'img{DS}{model}{DS}{field}',
			'filename' => '{slug}.{ext}',
		];
		$this->pathBuilder = PathBuilder::getInstance('fieldname', 'Foto Florianópolis.jpg', 'model', $config);
		$baseDir = TEST_ROOT . 'tests' . DS . 'TestCase' . DS . 'Lib' .  DS;
		$absolutePath = TEST_ROOT . 'tests' . DS . 'TestCase' . DS . 'Lib' .  DS . 'img' . DS . 'model' . DS . 'fieldname' . DS . 'foto-florianopolis-0.jpg';
		$thumbnailAbsolutePath = TEST_ROOT . 'tests' . DS . 'TestCase' . DS . 'Lib' .  DS . 'img' . DS . 'model' . DS . 'fieldname' . DS . 'thumb' . DS . 'foto-florianopolis-0.jpg';
		$dir = DS . 'img' . DS . 'model' . DS . 'fieldname' . DS;
		$dirname = TEST_ROOT . 'tests' . DS . 'TestCase' . DS . 'Lib' .  DS . 'img' . DS . 'model' . DS . 'fieldname' . DS;
		$filename = 'foto-florianopolis-0.jpg';
		$this->assertEquals($baseDir, $this->pathBuilder->baseDir());
		$this->assertEquals($absolutePath, $this->pathBuilder->absolutePath());
		$this->assertEquals($thumbnailAbsolutePath, $this->pathBuilder->thumbnailAbsolutePath('thumb'));
		$this->assertEquals($dir, $this->pathBuilder->dir());
		$this->assertEquals($dirname, $this->pathBuilder->dirname());
		$this->assertEquals($filename, $this->pathBuilder->filename());
	}

	public function testNonExistentFile() {
		$config = [
			'baseDir' => TEST_ROOT . 'tests' . DS . 'TestCase' . DS . 'Lib',
			'dir' => 'img{DS}{model}{DS}{field}',
			'filename' => '{slug}.{ext}',
		];
		$this->pathBuilder = PathBuilder::getInstance('fieldname', 'Foto Palhoça.jpg', 'model', $config);
		$baseDir = TEST_ROOT . 'tests' . DS . 'TestCase' . DS . 'Lib' .  DS;
		$absolutePath = TEST_ROOT . 'tests' . DS . 'TestCase' . DS . 'Lib'  . DS . 'img' . DS . 'model' . DS . 'fieldname' . DS . 'foto-palhoca.jpg';
		$thumbnailAbsolutePath = TEST_ROOT . 'tests' . DS . 'TestCase' . DS . 'Lib'  . DS . 'img' . DS . 'model' . DS . 'fieldname' . DS . 'thumb' . DS . 'foto-palhoca.jpg';
		$dir = '' . DS . 'img' . DS . 'model' . DS . 'fieldname' . DS;
		$dirname = TEST_ROOT . 'tests' . DS . 'TestCase' . DS . 'Lib' . DS . 'img' . DS . 'model' . DS . 'fieldname' . DS;
		$filename = 'foto-palhoca.jpg';
		$this->assertEquals($baseDir, $this->pathBuilder->baseDir());
		$this->assertEquals($absolutePath, $this->pathBuilder->absolutePath());
		$this->assertEquals($thumbnailAbsolutePath, $this->pathBuilder->thumbnailAbsolutePath('thumb'));
		$this->assertEquals($dir, $this->pathBuilder->dir());
		$this->assertEquals($dirname, $this->pathBuilder->dirname());
		$this->assertEquals($filename, $this->pathBuilder->filename());
	}
}

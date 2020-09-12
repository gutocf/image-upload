<?php

	 namespace App\Test\TestCase\Lib\ImageUpload\Filename;

	 use App\Lib\ImageUpload\Filename\PathBuilder;
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
				  'baseDir' => 'C:\Users\gutoc\Documents\www\www.galerianc.com.br\tests\TestCase\Lib\ImageUpload',
				  'dir' => 'img{DS}{model}{DS}{field}',
				  'filename' => '{slug}.{ext}',
			 ];
			 $this->pathBuilder = PathBuilder::getInstance('fieldname', 'Foto Florianópolis.jpg', 'model', $config);
			 $baseDir = 'C:\Users\gutoc\Documents\www\www.galerianc.com.br\tests\TestCase\Lib\ImageUpload\\';
			 $absolutePath = 'C:\Users\gutoc\Documents\www\www.galerianc.com.br\tests\TestCase\Lib\ImageUpload\img\model\fieldname\foto-florianopolis-0.jpg';
			 $thumbnailAbsolutePath = 'C:\Users\gutoc\Documents\www\www.galerianc.com.br\tests\TestCase\Lib\ImageUpload\img\model\fieldname\thumb\foto-florianopolis-0.jpg';
			 $dir = '\img\model\fieldname\\';
			 $dirname = 'C:\Users\gutoc\Documents\www\www.galerianc.com.br\tests\TestCase\Lib\ImageUpload\img\model\fieldname\\';
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
				  'baseDir' => 'C:\Users\gutoc\Documents\www\www.galerianc.com.br\tests\TestCase\Lib\ImageUpload',
				  'dir' => 'img{DS}{model}{DS}{field}',
				  'filename' => '{slug}.{ext}',
			 ];
			 $this->pathBuilder = PathBuilder::getInstance('fieldname', 'Foto Palhoça.jpg', 'model', $config);
			 $baseDir = 'C:\Users\gutoc\Documents\www\www.galerianc.com.br\tests\TestCase\Lib\ImageUpload\\';
			 $absolutePath = 'C:\Users\gutoc\Documents\www\www.galerianc.com.br\tests\TestCase\Lib\ImageUpload\img\model\fieldname\foto-palhoca.jpg';
			 $thumbnailAbsolutePath = 'C:\Users\gutoc\Documents\www\www.galerianc.com.br\tests\TestCase\Lib\ImageUpload\img\model\fieldname\thumb\foto-palhoca.jpg';
			 $dir = '\img\model\fieldname\\';
			 $dirname = 'C:\Users\gutoc\Documents\www\www.galerianc.com.br\tests\TestCase\Lib\ImageUpload\img\model\fieldname\\';
			 $filename = 'foto-palhoca.jpg';
			 $this->assertEquals($baseDir, $this->pathBuilder->baseDir());
			 $this->assertEquals($absolutePath, $this->pathBuilder->absolutePath());
			 $this->assertEquals($thumbnailAbsolutePath, $this->pathBuilder->thumbnailAbsolutePath('thumb'));
			 $this->assertEquals($dir, $this->pathBuilder->dir());
			 $this->assertEquals($dirname, $this->pathBuilder->dirname());
			 $this->assertEquals($filename, $this->pathBuilder->filename());
		 }

	 }
	 
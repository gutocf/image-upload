<?php

namespace Gutocf\ImageUpload\Test\TestCase\Lib\Resizer;

use Gutocf\ImageUpload\Lib\Resizer\ImageResizer;
use Cake\TestSuite\TestCase;
use Imagine\Gd\Imagine;
use Imagine\Image\ImageInterface;

/**
 * @property ImageResizer $this->imageProcessor
 * @property Imagine $imagine
 * @property ImageInterface $images
 */
class ImageResizerTest extends TestCase {

	private $ImageResizer;

	private $Imagine;

	private $image;

	public function setUp(): void {
		parent::setUp();
		$this->ImageResizer = new ImageResizer();
		$this->Imagine = new Imagine();
		$this->image = $this->Imagine->open(TEST_ROOT . 'tests' . DS . 'TestCase' . DS . 'Lib' . DS . 'Resizer' . DS . 'image_resizer_test.png');
	}

	public function testResizeByWidth(): void {
		$imageRatio = $this->image->getSize()->getWidth() / $this->image->getSize()->getHeight();
		$resize = $this->ImageResizer->resizeByWidth($this->image, 100);
		$this->assertEquals(100, $resize->getSize()->getWidth());
		$this->assertEquals(round(100 / $imageRatio), $resize->getSize()->getHeight());
	}

	public function testResizeByHeigth(): void {
		$imageRatio = $this->image->getSize()->getWidth() / $this->image->getSize()->getHeight();
		$resize = $this->ImageResizer->resizeByHeight($this->image, 100);
		$this->assertEquals(round(100 * $imageRatio), $resize->getSize()->getWidth());
		$this->assertEquals(100, $resize->getSize()->getHeight());
	}

	public function testCrop(): void {
		$crop = $this->ImageResizer->crop($this->image, 200, 200);
		$this->assertEquals(200, $crop->getSize()->getWidth());
		$this->assertEquals(200, $crop->getSize()->getHeight());
	}

	public function testResizeWidth(): void {
		$imageRatio = $this->image->getSize()->getWidth() / $this->image->getSize()->getHeight();
		$resize = $this->ImageResizer->resize($this->image, 100);
		$this->assertEquals(100, $resize->getSize()->getWidth());
		$this->assertEquals(round(100 / $imageRatio), $resize->getSize()->getHeight());
	}

	public function testResizeHeigth(): void {
		$imageRatio = $this->image->getSize()->getWidth() / $this->image->getSize()->getHeight();
		$resize = $this->ImageResizer->resize($this->image, null, 100);
		$this->assertEquals(round(100 * $imageRatio), $resize->getSize()->getWidth());
		$this->assertEquals(100, $resize->getSize()->getHeight());
	}

	public function testResizeCropCenter(): void {
		$crop = $this->ImageResizer->resize($this->image, 200, 200);
		$this->assertEquals(200, $crop->getSize()->getWidth());
		$this->assertEquals(200, $crop->getSize()->getHeight());
	}
}

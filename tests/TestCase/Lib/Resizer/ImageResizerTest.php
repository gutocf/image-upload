<?php

namespace Gutocf\ImageUpload\Test\TestCase\Lib\Resizer;

use Gutocf\ImageUpload\Lib\Resizer\ImageResizer;
use Cake\TestSuite\TestCase;
use Imagine\Gd\Imagine;
use Imagine\Image\ImageInterface;

/**
 * @property Imagine $imagine
 * @property ImageInterface $imageLandscape
 * @property ImageInterface $imagePortrait
 */
class ImageResizerTest extends TestCase
{


	private $Imagine;

	private $imageLandscape;

	private $imagePortrait;

	public function setUp(): void
	{
		parent::setUp();
		$this->Imagine = new Imagine();
		$this->imageLandscape = $this->Imagine->open(TEST_ROOT . 'tests' . DS . 'TestCase' . DS . 'Lib' . DS . 'Resizer' . DS . 'image_landscape.png');
		$this->imagePortrait = $this->Imagine->open(TEST_ROOT . 'tests' . DS . 'TestCase' . DS . 'Lib' . DS . 'Resizer' . DS . 'image_portrait.png');
	}

	public function testResizeByWidthLandscape(): void
	{
		$imageRatio = $this->imageLandscape->getSize()->getWidth() / $this->imageLandscape->getSize()->getHeight();
		$resize = ImageResizer::getInstance()->resizeByWidth($this->imageLandscape, 100);
		$this->assertEquals(100, $resize->getSize()->getWidth());
		$this->assertEquals(round(100 / $imageRatio), $resize->getSize()->getHeight());
	}

	public function testResizeByHeigthLandscape(): void
	{
		$imageRatio = $this->imageLandscape->getSize()->getWidth() / $this->imageLandscape->getSize()->getHeight();
		$resize = ImageResizer::getInstance()->resizeByHeight($this->imageLandscape, 100);
		$this->assertEquals(round(100 * $imageRatio), $resize->getSize()->getWidth());
		$this->assertEquals(100, $resize->getSize()->getHeight());
	}

	public function testCropLandscape(): void
	{
		$crop = ImageResizer::getInstance()->crop($this->imageLandscape, 200, 200);
		$crop->save('d:\landscape.png');
		$this->assertEquals(200, $crop->getSize()->getWidth());
		$this->assertEquals(200, $crop->getSize()->getHeight());
	}

	public function testResizeWidthLandscape(): void
	{
		$imageRatio = $this->imageLandscape->getSize()->getWidth() / $this->imageLandscape->getSize()->getHeight();
		$resize = ImageResizer::getInstance()->resize($this->imageLandscape, 100);
		$this->assertEquals(100, $resize->getSize()->getWidth());
		$this->assertEquals(round(100 / $imageRatio), $resize->getSize()->getHeight());
	}

	public function testResizeHeigthLandscape(): void
	{
		$imageRatio = $this->imageLandscape->getSize()->getWidth() / $this->imageLandscape->getSize()->getHeight();
		$resize = ImageResizer::getInstance()->resize($this->imageLandscape, null, 100);
		$this->assertEquals(round(100 * $imageRatio), $resize->getSize()->getWidth());
		$this->assertEquals(100, $resize->getSize()->getHeight());
	}

	public function testResizeCropCenterLandscape(): void
	{
		$crop = ImageResizer::getInstance()->resize($this->imageLandscape, 200, 200);
		$this->assertEquals(200, $crop->getSize()->getWidth());
		$this->assertEquals(200, $crop->getSize()->getHeight());
	}


	public function testResizeByWidthPortrait(): void
	{
		$imageRatio = $this->imagePortrait->getSize()->getWidth() / $this->imagePortrait->getSize()->getHeight();
		$resize = ImageResizer::getInstance()->resizeByWidth($this->imagePortrait, 100);
		$this->assertEquals(100, $resize->getSize()->getWidth());
		$this->assertEquals(round(100 / $imageRatio), $resize->getSize()->getHeight());
	}

	public function testResizeByHeigthPortrait(): void
	{
		$imageRatio = $this->imagePortrait->getSize()->getWidth() / $this->imagePortrait->getSize()->getHeight();
		$resize = ImageResizer::getInstance()->resizeByHeight($this->imagePortrait, 100);
		$this->assertEquals(round(100 * $imageRatio), $resize->getSize()->getWidth());
		$this->assertEquals(100, $resize->getSize()->getHeight());
	}

	public function testCropPortrait(): void
	{
		$crop = ImageResizer::getInstance()->crop($this->imagePortrait, 200, 200);
		$crop->save('d:\portrait.png');
		$this->assertEquals(200, $crop->getSize()->getWidth());
		$this->assertEquals(200, $crop->getSize()->getHeight());
	}

	public function testResizeWidthPortrait(): void
	{
		$imageRatio = $this->imagePortrait->getSize()->getWidth() / $this->imagePortrait->getSize()->getHeight();
		$resize = ImageResizer::getInstance()->resize($this->imagePortrait, 100);
		$this->assertEquals(100, $resize->getSize()->getWidth());
		$this->assertEquals(round(100 / $imageRatio), $resize->getSize()->getHeight());
	}

	public function testResizeHeigthPortrait(): void
	{
		$imageRatio = $this->imagePortrait->getSize()->getWidth() / $this->imagePortrait->getSize()->getHeight();
		$resize = ImageResizer::getInstance()->resize($this->imagePortrait, null, 100);
		$this->assertEquals(round(100 * $imageRatio), $resize->getSize()->getWidth());
		$this->assertEquals(100, $resize->getSize()->getHeight());
	}

	public function testResizeCropCenterPortrait(): void
	{
		$crop = ImageResizer::getInstance()->resize($this->imagePortrait, 200, 200);
		$this->assertEquals(200, $crop->getSize()->getWidth());
		$this->assertEquals(200, $crop->getSize()->getHeight());
	}
}

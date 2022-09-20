<?php

namespace Gutocf\ImageUpload\Lib\Resizer;

use Cake\Filesystem\Folder;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use InvalidArgumentException;
use Laminas\Diactoros\UploadedFile;
use Psr\Http\Message\StreamInterface;

/**
 * @property ImageResizer $instance
 * @property Imagine $Imagine
 */
class ImageResizer
{

	private static $instance;

	private $Imagine;

	private function __construct()
	{
		$this->Imagine = new Imagine();
	}

	public static function getInstance(): self
	{
		if (self::$instance === null) {
			self::$instance = new ImageResizer();
		}
		return self::$instance;
	}

	public function createThumbnail(string $source_file, string $format = 'png', int $width = null, int $height = null)
	{
		$image = $this->Imagine->open($source_file);
		return $this
			->resize($image, $width, $height)
			->get($format);
	}

	public function resize(ImageInterface $image, int $width = null, int $height = null): ImageInterface
	{
		switch (true) {
			case !is_null($width) && !is_null($height):
				return $this->crop($image, $width, $height);
			case !is_null($width):
				return $this->resizeByWidth($image, $width);
			case !is_null($height):
				return $this->resizeByHeight($image, $height);
			default:
				throw new InvalidArgumentException('You must define values for at least one of $width and $height parameters.');
		}
	}

	public function crop(ImageInterface $image, int $width, int $height): ImageInterface
	{
		$boxCrop = new Box($width, $height);
		$imageRatio = $image->getSize()->getWidth() / $image->getSize()->getHeight();
		$thumbRatio = $boxCrop->getWidth() / $boxCrop->getHeight();
		if ($imageRatio >= $thumbRatio) {
			$resizeW = round($height * $imageRatio);
			$resizeH = $height;
			$start = new Point(intval(($resizeW - $width) / 2), 0);
		} else {
			$resizeW = $width;
			$resizeH = round($height / $imageRatio);
			$start = new Point(0, intval(($resizeH - $height) / 2));
		}
		$boxResize = new Box($resizeW, $resizeH);
		return $image->resize($boxResize)
			->crop($start, $boxCrop);
	}

	public function resizeByHeight(ImageInterface $image, int $height): ImageInterface
	{
		return $image->resize($image->getSize()->heighten($height));
	}

	public function resizeByWidth(ImageInterface $image, int $width): ImageInterface
	{
		return $image->resize($image->getSize()->widen($width));
	}

	public function open(string $path): ImageInterface
	{
		return $this->Imagine->open($path);
	}
}

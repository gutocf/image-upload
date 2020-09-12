<?php

	 namespace App\Lib\ImageUpload\Resizer;

	 use Cake\Filesystem\Folder;
	 use Imagine\Gd\Imagine;
	 use Imagine\Image\Box;
	 use Imagine\Image\ImageInterface;
	 use Imagine\Image\Point;
	 use InvalidArgumentException;

	 class ImageResizer {

		 private $Imagine;

		 public function __construct() {
			 $this->Imagine = new Imagine();
		 }

		 public function createThumbnail(string $sourcePath, string $thumbnailPath, int $width = null, int $height = null): void {
			 $source = $this->open($sourcePath);
			 $thumbnail = $this->resize($source, $width, $height);
			 $this->save($thumbnail, $thumbnailPath);
		 }

		 public function resize(ImageInterface $image, int $width = null, int $height = null): ImageInterface {
			 switch (true) {
				 case!is_null($width) && !is_null($height):
					 return $this->crop($image, $width, $height);
				 case!is_null($width):
					 return $this->resizeByWidth($image, $width);
				 case!is_null($height):
					 return $this->resizeByHeight($image, $height);
				 default:
					 throw new InvalidArgumentException('You must define values for at least one of $width and $height parameters.');
			 }
		 }

		 public function crop(ImageInterface $image, int $width, int $height): ImageInterface {
			 $boxCrop = new Box($width, $height);
			 $imageRatio = $image->getSize()->getWidth() / $image->getSize()->getHeight();
			 $thumbRatio = $boxCrop->getWidth() / $boxCrop->getHeight();
			 if ($imageRatio >= $thumbRatio) {
				 $resizeW = $height * $imageRatio;
				 $resizeH = $height;
				 $start = new Point(round(($resizeW - $width) / 2), 0);
			 } else {
				 $resizeW = $width * $imageRatio;
				 $resizeH = $width;
				 $start = new Point(0, round(($resizeH - $height) / 2));
			 }
			 $boxResize = new Box($resizeW, $resizeH);
			 return $image->resize($boxResize)
								  ->crop($start, $boxCrop);
		 }

		 public function resizeByHeight(ImageInterface $image, int $height): ImageInterface {
			 return $image->resize($image->getSize()->heighten($height));
		 }

		 public function resizeByWidth(ImageInterface $image, int $width): ImageInterface {
			 return $image->resize($image->getSize()->widen($width));
		 }

		 public function open(string $path): ImageInterface {
			 return $this->Imagine->open($path);
		 }

		 public function save(ImageInterface $image, string $path): ImageInterface {
			 $dirname = pathinfo($path, PATHINFO_DIRNAME);
			 new Folder($dirname, 775, true);
			 return $image->save($path);
		 }

	 }
	 
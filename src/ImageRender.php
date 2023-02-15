<?php
/*
 * BSD 3-Clause License
 *
 * Copyright (c) 2019, TASoft Applications
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 *  Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 *  Neither the name of the copyright holder nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

namespace Skyline\ImageTool\Render;


class ImageRender implements ImageRenderInterface
{
	private $orientationTransformationMap = [
		1 => array ( 1 => 0, 2 => 1, 3 => 8, 4 => 2, 5 => 5, 6 => 16, 7 => 6, 8 => 4 ),
		2 => array ( 1 => 1, 2 => 0, 3 => 2, 4 => 8, 5 => 16, 6 => 5, 7 => 4, 8 => 6 ),
		3 => array ( 1 => 8, 2 => 2, 3 => 0, 4 => 1, 5 => 6, 6 => 4, 7 => 5, 8 => 16 ),
		4 => array ( 1 => 2, 2 => 8, 3 => 1, 4 => 0, 5 => 4, 6 => 6, 7 => 16, 8 => 5 ),
		5 => array ( 1 => 5, 2 => 4, 3 => 6, 4 => 16, 5 => 0, 6 => 2, 7 => 8, 8 => 1 ),
		6 => array ( 1 => 4, 2 => 5, 3 => 16, 4 => 6, 5 => 2, 6 => 0, 7 => 1, 8 => 8 ),
		7 => array ( 1 => 6, 2 => 16, 3 => 5, 4 => 4, 5 => 8, 6 => 1, 7 => 0, 8 => 2 ),
		8 => array ( 1 => 16, 2 => 6, 3 => 4, 4 => 5, 5 => 1, 6 => 8, 7 => 2, 8 => 0 )
	];

	/**
	 * @inheritDoc
	 */
	public function flipImage(ImageReferenceInterface $image, int $mode): bool
	{
		if (function_exists('imageflip')) {
			return imageflip($image->getImageResource(), $mode);
		}

		$new_width = $src_width = $image->getWidth();
		$new_height = $src_height = $image->getHeight();

		$new_img = imagecreatetruecolor($new_width, $new_height);

		$src_x = 0;
		$src_y = 0;
		switch ($mode) {
			case '1': // flip on the horizontal axis
				$src_y = $new_height - 1;
				$src_height = -$new_height;
				break;
			case '2': // flip on the vertical axis
				$src_x  = $new_width - 1;
				$src_width = -$new_width;
				break;
			case '3': // flip on both axes
				$src_y = $new_height - 1;
				$src_height = -$new_height;
				$src_x  = $new_width - 1;
				$src_width = -$new_width;
				break;
			default:
				trigger_error("ImageGenerator::flipImage => unknown mode $mode", E_USER_WARNING);
				return false;
		}

		if(imagecopyresampled(
			$new_img,
			$image->getImageResource(),
			0,
			0,
			$src_x,
			$src_y,
			$new_width,
			$new_height,
			$src_width,
			$src_height
		)) {
			$image->replaceImageResource($new_img);
			return true;
		}
		imagedestroy($new_img);
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function rotateImage(ImageReferenceInterface $image, float $angle, $backgroundColor = 0): bool
	{
		$img = imagerotate($image->getImageResource(), -$angle, $backgroundColor);
		if($img) {
			$image->replaceImageResource($img);
			return true;
		}
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function orientImage(ImageReferenceInterface $image, int $destOrientation = ImageReferenceInterface::ORIENTED_TOP_LEFT, int $sourceOrientation = NULL): bool
	{
		$flipH = 1;
		$flipV = 2;
		$rot90 = 4;
		$rot180 = 8;
		$rot270 = 16;

		if($sourceOrientation == NULL)
			$sourceOrientation = $image->getOrientation();

		$transform = $this->orientationTransformationMap[ $sourceOrientation ] [ $destOrientation ] ?? NULL;

		if($transform) {
			if($transform & $flipH)
				$this->flipImage($image, self::FLIP_HORIZONTAL);
			if($transform & $flipV)
				$this->flipImage($image, self::FLIP_VERTICAL);
			if($transform & $rot90)
				$this->rotateImage($image, 90);
			if($transform & $rot180)
				$this->rotateImage($image, 180);
			if($transform & $rot270)
				$this->rotateImage($image, 270);
		}

		return $transform !== NULL;
	}

	/**
	 * @inheritDoc
	 */
	public function scaleImageTo(ImageReferenceInterface $image, int $width, int $height): bool
	{
		$image->getSize($img_width, $img_height);

		if (($img_width / $img_height) >= ($width / $height)) {
			$new_width = $img_width / ($img_height / $height);
			$new_height = $height;
		} else {
			$new_width = $width;
			$new_height = $img_height / ($img_width / $width);
		}
		$dst_x = 0 - ($new_width - $width) / 2;
		$dst_y = 0 - ($new_height - $height) / 2;
		$new_img = imagecreatetruecolor($width, $height);

		switch ($image->getType()) {
			case ImageInterface::IMAGE_GIF:
				imagecolortransparent($new_img, imagecolorallocate($new_img, 0, 0, 0));
			case ImageInterface::IMAGE_PNG:
				imagealphablending($new_img, false);
				imagesavealpha($new_img, true);
				break;
		}

		if(imagecopyresampled(
			$new_img,
			$image->getImageResource(),
			$dst_x,
			$dst_y,
			0,
			0,
			$new_width,
			$new_height,
			$img_width,
			$img_height
		)) {
			$image->replaceImageResource($new_img);
			return true;
		}
		imagedestroy($new_img);
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function scaleImageBy(ImageReferenceInterface $image, float $factor): bool
	{
		$image->getSize($img_width, $img_height);
		$new_width = $img_width*$factor;
		$new_height = $img_height*$factor;
		return $this->scaleImageTo($image, $new_width, $new_height);
	}

	/**
	 * @inheritDoc
	 */
	public function scaleImageMax(ImageReferenceInterface $image, int $maxWidth, int $maxHeight): bool
	{
		$image->getSize($img_width, $img_height);

		$scale = min(
			$maxWidth / $img_width,
			$maxHeight / $img_height
		);

		return $this->scaleImageBy($image, $scale);
	}

	/**
	 * @inheritDoc
	 */
	public function getSizeOfText(string $text, string $font, $fontSize = 12.0, $angle = 0, int &$width = NULL, int &$height = NULL)
	{
		$box = imagettfbbox($fontSize, $angle, $font, $text);
		if($box) {
			$min_x = min( array($box[0], $box[2], $box[4], $box[6]) );
			$max_x = max( array($box[0], $box[2], $box[4], $box[6]) );
			$min_y = min( array($box[1], $box[3], $box[5], $box[7]) );
			$max_y = max( array($box[1], $box[3], $box[5], $box[7]) );
			$width  = ( $max_x - $min_x );
			$height = ( $max_y - $min_y );
			return true;
		}
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function createImage(int $width, int $height, bool $usesAlpha = true)
	{
		$img = imagecreatetruecolor($width, $height);
		if($usesAlpha) {
			imagecolortransparent($img, imagecolorallocatealpha($img, 0, 0, 0, 127));
		}
		return $img;
	}
}
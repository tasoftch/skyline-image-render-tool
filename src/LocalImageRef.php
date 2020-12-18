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


class LocalImageRef extends LocalImage implements ImageReferenceInterface
{


	private $_io;

	public function __construct(string $filename)
	{
		parent::__construct($filename);
		switch ($this->getType()) {
			case self::IMAGE_JPEG:
				$this->_io[0] = "imagecreatefromjpeg";
				$this->_io[1] = "imagejpeg";
				break;
			case self::IMAGE_PNG:
				$this->_io[0] = "imagecreatefrompng";
				$this->_io[1] = "imagepng";
				break;
			case self::IMAGE_GIF:
				$this->_io[0] = "imagecreatefromgif";
				$this->_io[1] = "imagegif";
				break;
			case self::IMAGE_BMP:
				$this->_io[0] = "imagecreatefrombmp";
				$this->_io[1] = "imagebmp";
				break;
		}
	}

	/**
	 * @return array
	 */
	public function getMetadata()
	{
		if($this->_io[3] === NULL && function_exists("exif_read_data"))
			$this->_io[3] = exif_read_data($this->getFilename());

		return $this->_io[3];
	}

	/**
	 * @inheritDoc
	 */
	public function getImageResource()
	{
		if(!$this->_io[2])
			$this->_io[2] = ($this->_io[0])($this->getFilename());
		return $this->_io[2];
	}

	/**
	 * Returns the orientation
	 *
	 * @return int
	 * @see LocalImage::ORIENTED_* constants
	 */
	public function getOrientation(): int {
		return ($this->getMetadata()["Orientation"] ?? 1)*1;
	}

	/**
	 * Gets the real size from an image
	 *
	 * @param $width
	 * @param $height
	 * @return static
	 */
	public function getSize(&$width, &$height): LocalImageRef
	{
		if($this->_io[2]) {
			$width = imagesx($this->_io[2]);
			$height = imagesy($this->_io[2]);
		} else {
			$width = $this->getWidth();
			$height = $this->getHeight();
		}
		return $this;
	}

	/**
	 * Saves the current local image reference on disk
	 *
	 * @param $newFile
	 * @param int $compression
	 * @return bool
	 */
	public function save($newFile, $compression = -1):bool {
		if($this->_io[2]) {
			return @($this->_io[1])($this->_io[2], $newFile, $compression);
		} else {
			return @copy($this->getFilename(), $newFile);
		}
	}

	/**
	 * @param $src
	 * @return bool
	 */
	public function replaceImageResource($src): bool
	{
		if($this->_io[2])
			imagedestroy($this->_io[2]);
		if(is_resource($src)) {
			$this->_io[2] = $src;
			$this->width = imagesx($src);
			$this->height = imagesy($src);
		}
		return true;
	}

	public function __destruct()
	{
		if($this->_io[2])
			imagedestroy($this->_io[2]);
	}
}
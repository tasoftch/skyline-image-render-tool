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


use Skyline\ImageTool\Render\Exception\UnsupportedImageTypeException;

class LocalImage implements ImageInterface
{
	/** @var string */
	private $filename;
	/** @var int */
	private $type;
	/** @var int */
	protected $width;
	/** @var int */
	protected $height;

	/**
	 * LocalImage constructor.
	 * @param string $filename
	 */
	public function __construct(string $filename)
	{
		$this->filename = $filename;
		$info = getimagesize($filename);
		$this->width = $info[0];
		$this->height = $info[1];

		switch ($info["mime"]) {
			case "image/jpeg":
			case "image/jpg":
				$this->type = self::IMAGE_JPEG;
				break;
			case "image/gif":
				$this->type = self::IMAGE_GIF;
				break;
			case "image/png":
				$this->type = self::IMAGE_PNG;
				break;
			case "image/bmp":
			case "image/tiff":
				$this->type = self::IMAGE_BMP;
				break;
			default:
				throw (new UnsupportedImageTypeException("Unsupported image type {$info["name"]}", 401))->setType($info["name"]);
		}
	}

	/**
	 * @return string
	 */
	public function getFilename(): string
	{
		return $this->filename;
	}

	/**
	 * @return int
	 */
	public function getType(): int
	{
		return $this->type;
	}

	/**
	 * @return int
	 */
	public function getWidth(): int
	{
		return $this->width;
	}

	/**
	 * @return int
	 */
	public function getHeight(): int
	{
		return $this->height;
	}
}
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


interface ImageRenderInterface
{
	const FLIP_HORIZONTAL = 1;
	const FLIP_VERTICAL = 2;
	const FLIP_BOTH = 3;


	/**
	 * @param ImageReferenceInterface $image
	 * @param int $mode
	 * @return bool
	 * @see ImageRenderInterface::FLIP_* constants
	 */
	public function flipImage(ImageReferenceInterface $image, int $mode): bool;

	/**
	 * @param ImageReferenceInterface $image
	 * @param float $angle
	 * @param int $backgroundColor
	 * @return bool
	 */
	public function rotateImage(ImageReferenceInterface $image, float $angle, $backgroundColor = 0): bool;

	/**
	 * Reorients an image
	 *
	 * @param ImageReferenceInterface $image
	 * @param int $destOrientation
	 * @param int|NULL $sourceOrientation
	 * @return bool
	 */
	public function orientImage(ImageReferenceInterface $image, int $destOrientation = ImageReferenceInterface::ORIENTED_TOP_LEFT, int $sourceOrientation = NULL): bool;

	/**
	 * @param ImageReferenceInterface $image
	 * @param int $width
	 * @param int $height
	 * @return bool
	 */
	public function scaleImageTo(ImageReferenceInterface $image, int $width, int $height):bool;

	/**
	 * @param ImageReferenceInterface $image
	 * @param float $factor
	 * @return bool
	 */
	public function scaleImageBy(ImageReferenceInterface $image, float $factor): bool;

	/**
	 * @param ImageReferenceInterface $image
	 * @param int $maxWidth
	 * @param int $maxHeight
	 * @return bool
	 */
	public function scaleImageMax(ImageReferenceInterface $image, int $maxWidth, int $maxHeight): bool;

	/**
	 * @param string $text
	 * @param string $font
	 * @param float $fontSize
	 * @param int $angle
	 * @param int|null $width
	 * @param int|null $height
	 * @return mixed
	 */
	public function getSizeOfText(string $text, string $font, $fontSize = 12.0, $angle = 0, int &$width = NULL, int &$height = NULL);

	/**
	 * @param int $width
	 * @param int $height
	 * @param bool $usesAlpha
	 * @return resource
	 */
	public function createImage(int $width, int $height, bool $usesAlpha = true);
}
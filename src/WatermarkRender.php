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

class WatermarkRender extends ImageRender
{
	public function createWatermark(string $text, string $font, float $size = 12, float $angle = 0, $color = 0, int $margin = 4): ?ImageReferenceInterface {
		$this->getSizeOfText($text, $font, $size, $angle, $w, $h);
		$this->getSizeOfText($text, $font, $size, 0, $w0, $h0);

		$temp = new TemporaryLocalImageRef($w+$margin, $h+$margin, true);

		imagefill($temp->getImageResource(), 0, 0, imagecolorallocatealpha($temp->getImageResource(), 255, 255, 255, 127));

		if(imagettftext($temp->getImageResource(), $size, $angle, (int) ($margin/2+sin($angle/360*2*M_PI)*$h0), $h+$margin/2, $color, $font, $text))
			return $temp;
		unset($temp);
		return NULL;
	}

	public function mergeWatermark(ImageReferenceInterface $image, ImageReferenceInterface $watermark, int $fraction) {
		imagecopymerge($image->getImageResource(), $watermark->getImageResource(), 0, 0, 0, 0, $watermark->getWidth(), $watermark->getHeight(), $fraction);
	}
}
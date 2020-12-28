<?php


namespace Skyline\ImageTool\Render;


class ImageTranslationRender extends ImageRender
{
	/** @var int */
	private $targetWidth;
	/** @var int */
	private $targetHeight;

	/**
	 * ImageTranslationRender constructor.
	 * @param int $targetWidth
	 * @param int $targetHeight
	 */
	public function __construct(int $targetWidth, int $targetHeight = 0)
	{
		$this->targetWidth = $targetWidth;
		$this->targetHeight = $targetHeight ?: $targetWidth;
	}

	/**
	 * @return int
	 */
	public function getTargetWidth(): int
	{
		return $this->targetWidth;
	}

	/**
	 * @return int
	 */
	public function getTargetHeight(): int
	{
		return $this->targetHeight;
	}

	public function renderTranslation(ImageReferenceInterface $image, $translateX, $translateY, $scale) {
		if($scale != 1) {
			$this->scaleImageBy($image, $scale);
		}

		$img = $this->createImage( $w = $this->getTargetWidth(), $h = $this->getTargetHeight() );
		if(!imagecopy($img, $image->getImageResource(), 0, 0, -$translateX, -$translateY, $w, $h)) {
			imagedestroy($img);
			return false;
		}
		return $image->replaceImageResource($img);
	}
}
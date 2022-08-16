<?php

namespace Cacko\Yii2\Widgets\Video\Components\Video;

use Cacko\Yii2\Widgets\Video\Models\ScreenshotInterface;

/**
 * Interface VideoScreenshotInterface
 * @package app\components\video
 * @property-read string $screenshotId
 * @property-read string $defaultScreenshot
 */
interface VideoScreenshotInterface
{

    public function getScreenshotId(): string;

    public function getScreenshot(): ScreenshotInterface;

    public function getDefaultScreenshot(): string;

}
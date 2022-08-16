<?php

namespace Cacko\Yii2\Widgets\Video\Components\Video;

/**
 * Interface VideoInterface
 * @package app\components\video
 * @property-read string $type
 * @property-read  string $id
 * @property-read ScreenshotInterface $screenshot
 * @property-read string $embedUrl
 * @property-read string $embedType
 * @property-read array $playerVars
 */
interface VideoInterface
{

    public function getEmbedUrl(): string;

    public function getType(): string;

    public function getId(): ?string;

    public function setPlaysInline($mode): void;

    public function getPlayerVars(): array;

    public function getEmbedType(): string;

}
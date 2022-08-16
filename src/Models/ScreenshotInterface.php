<?php

namespace Cacko\Yii2\Widgets\Video\Models;


interface ScreenshotInterface
{

    /**
     * 
     * @param string $id 
     * @return \Cacko\Yii2\Widgets\Video\Models\ScreenshotInterface 
     */
    public function setId(string $id): ScreenshotInterface;

    /**
     * 
     * @param string $url 
     * @return \Cacko\Yii2\Widgets\Video\Models\ScreenshotInterface 
     */
    public function setUrl(string $url): ScreenshotInterface;

    /**
     * 
     * @return string 
     */
    public function getId(): string;

    /**
     * 
     * @return string 
     */
    public function getUrl(): string;

    /**
     * 
     * @return bool 
     */
    public function save(): bool;

    /**
     * 
     * @param mixed $condition 
     * @return null|\Cacko\Yii2\Widgets\Video\Models\ScreenshotInterface 
     */
    public static function findOne($condition): ?ScreenshotInterface;
}

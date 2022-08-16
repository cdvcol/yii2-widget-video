<?php

namespace Cacko\Yii2\Widgets\Video\Controller;


interface ControllerInterface
{

    const CONTROLLER_ID = 'cacko-video';

    const URL_SAVE = '/cacko-video/save';

    public function actionSave(string $id): void;
}

<?php

namespace Cacko\Yii2\Widgets\Video\Controller;

use Cacko\Yii2\Widgets\Video\Models\ScreenshotInterface;
use Yii;
use yii\web\Controller as WebController;
use yii\web\NotFoundHttpException;

class Controller extends WebController implements ControllerInterface
{

    public function actionSave($id): void
    {
        $request = Yii::$app->request;

        if (!$request->isPut) {
            throw new NotFoundHttpException();
        }

        $screenshot = Yii::createObject(ScreenshotInterface::class);

        $screenshot->setId($id)->setUrl($request->getRawBody())->save();
    }
}

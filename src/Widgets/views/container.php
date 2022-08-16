<?php

use Cacko\Yii2\Widgets\Video\Widgets\VideoWidget;
use yii\helpers\Html;
use yii\helpers\Json;

/** @var VideoWidget $context  */
$context = $this->context;
$addControls = $context->hideControls;
$type = $context->videoType;

?>
<div class="clearfix embed-responsive embed-responsive-16by9">
    <?php if ($addControls) : ?>
        <?= $this->render('controls') ?>
    <?php endif; ?>
    <?=
    Html::tag('div', '', [
        'id' => $context->id . '_video',
        'class' => array_merge(['embed-responsive-item'], $context->customCssClasses),
        'data' => [
            'video-id' => $context->videoId,
            'player-vars' => Json::encode($context->playerVars)
        ],
    ])
    ?>
</div>
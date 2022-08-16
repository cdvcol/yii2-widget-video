<?php

use yii\helpers\Html;

/**
 * @var string $screenshot
 * @var bool $noContainer
 * @var string $modalToggle
 */

if (empty($screenshot)) {
    return '';
}

$widgetDomId = $this->context->id;
$content = '';

if (!isset($modalToggle)) {
    $modalToggle = '';
}

if ($modalToggle) {
    $content = Html::tag(
        'a',
        '<div class="play"></div>',
        [
            'class' => 'play-button',
            'data-toggle' => 'modal',
            'data-target' => "#$modalToggle",
        ]
    );
}
?>
<div class="screenshot-container" style="background-image: url('<?= $screenshot ?>')">
    <?= $content ?>
</div>

<div class="video-thumb video-broadcast clearfix embed-responsive embed-responsive-16by9" <?= $modalToggle ?>>
    <?= Html::img($screenshot, ['class' => 'embed-responsive-item center-block']) ?>
</div>
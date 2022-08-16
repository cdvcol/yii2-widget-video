<?php


use yii\helpers\Html;

/** @var Cacko\Yii2\Widgets\Video\Widgets'VideoWidget $context */

$context = $this->context;
$addControls = $context->hideControls && !$context->isNonInteractive;
$type = $context->videoType;
$embedUrl = $context->embedUrl;

?>

<div class="clearfix embed-responsive embed-responsive-16by9">
    <?php if ($addControls): ?>
        <?= $this->render('controls') ?>
    <?php endif; ?>
    <?=
    Html::tag('iframe', '', [
        'id' => $context->id . '_video',
        'class' => array_filter(['embed-responsive-item', $type == 'wistia' ? 'wistia_embed' : null]),
        'src' => $embedUrl,
        'allow' => join(';', ['autoplay', 'fullscreen', 'encrypted-media', 'gyroscope']),
        'name' => $type === 'wistia' ? 'wistia_embed' : null,
    ])
    ?>
</div>


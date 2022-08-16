<?php

use Cacko\Yii2\Widgets\Video\Controller\ControllerInterface;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var bool $doCapture */

/** @var Cacko\Yii2\Widgets\Video\Widgets'VideoWidget $context */
$context = $this->context;
$doCapture = !empty($doCapture);
$hasControls = !$context->hideControls;
$playsinline = $context->hideControls || $context->autoPlay || $context->openInModal;
$js = $context->playerVars['js'];
$videoJsId = sprintf('%s_video', $context->id);

if ($context->openInModal) {
    $css = <<<CSS
#{$context->id} .embed-responsive-16by9 {
    height: 100%;
    width: 100%;
    padding-bottom: 0;
}
#{$context->id} .video-js {
    height: 100%;
    width: 100%;
}
CSS;
    echo '<style>' . $css . '</style>';
}


?>
<div class="clearfix embed-responsive embed-responsive-16by9">
    <?php if (!$hasControls) : ?>
        <?= $this->render('controls') ?>
    <?php endif; ?>
    <?= Html::tag(
        'video-js',
        '',
        [
            'data' => $context->playerVars,
            'id' => $videoJsId,
            'width' => '100%',
            'playsinline' => $playsinline,
            'controls' => $hasControls ? '' : null,
            'class' => 'vjs-fluid embed-responsive-item',
            'loop' => $context->loop ? '' : null,
            'muted' => $context->autoPlay ? '' : null,
        ]
    ) ?>
</div>
<?php if ($doCapture) : ?>
    <script>
        (function(require, define, requirejs) {
            require(['<?= $js ?>'], () => {
                const player = bc('<?= $videoJsId ?>');
                player.on('loadstart', function() {
                    this.autoplay(false);
                    $('#<?= $context->id ?>').trigger('screenshot.video', [this.mediainfo.poster]);
                    $.ajax({
                        url: "<?= Url::to([ControllerInterface::URL_SAVE, 'id' => $context->screenshotCaptureId]) ?>",
                        method: "PUT",
                        data: this.mediainfo.poster
                    }).always(() => $('#<?= $context->id ?>_temp').remove());
                });
            });
        }(__require.require, __require.define, __require.requirejs));
    </script>
<?php endif; ?>
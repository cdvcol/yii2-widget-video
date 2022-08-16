<?php

use Cacko\Yii2\Widgets\Video\Controller\ControllerInterface;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var Cacko\Yii2\Widgets\Video\Widgets'VideoWidget $context */
$context = $this->context;

$doCapture = !empty($doCapture);
$hasControls = !$context->hideControls;
$playsinline = $context->hideControls || $context->autoPlay || $context->openInModal;
$sourceUrl = $context->url;

?>
<div class="clearfix embed-responsive embed-responsive-16by9">
    <?php if (!$hasControls) : ?>
        <?= $this->render('controls') ?>
    <?php endif; ?>
    <?= Html::tag(
        'video',
        Html::tag('source', '', ['src' => $sourceUrl, 'type' => 'video/mp4']), 
        array_merge([
            'class' => 'embed-responsive-item',
            'width' => '100%',
            'id' => $context->id . '_video',
        ], $context->playerVars)
    ) ?>
</div>

<?php if ($doCapture) : ?>

    <script>
        (async () => {
            const maxWidth = 640;
            const videoObjectUrl = document.createElement('source');
            videoObjectUrl.src = '<?= $sourceUrl ?>';
            const video = document.createElement("video");
            video.setAttribute("crossorigin", "anonymous");
            video.addEventListener('loadedmetadata', () => {
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                const [w, h] = [maxWidth, maxWidth * (video.videoHeight / video.videoWidth)];
                canvas.width = w;
                canvas.height = h;
                video.currentTime = 3;
                video.addEventListener('seeked', () => {
                    context.drawImage(video, 0, 0, w, h);
                    const image = canvas.toDataURL(`image/${navigator.userAgent.indexOf("Firefox") ? 'jpeg' : 'webp'}`);
                    $('#<?= $context->id ?>').trigger('screenshot.video', [image]);
                    $.ajax({
                        url: "<?= Url::to([ControllerInterface::URL_SAVE, 'id' => $context->screenshotCaptureId]) ?>",
                        method: "PUT",
                        data: image
                    });
                });
            });
            video.appendChild(videoObjectUrl);
        })();
    </script>
<?php endif; ?>
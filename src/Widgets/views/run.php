<?php

use yii\helpers\Html;

$context = $this->context;

$modalToggle = $context->openInModal ? $context->id . '-modal' : '';

?>

<?= Html::beginTag('div', ['id' => $context->id, 'class' => ['video-widget', 'fullscreen-widget']]) ?>
<div style="position: relative">
    <?= $this->render('screenshot', [
        'screenshot' => $context->placeholderImage ?: ($context->screenshot ?: $context->defaultScreenshot),
        'modalToggle' => $modalToggle,
    ]) ?>
    <?php if ($context->openInModal) : ?>
        <div class="modal fade" tabindex="-1" role="dialog" id="<?= $modalToggle ?>">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body widget-container">
                        <i class="modal-loading icon-spin6 animate-spin"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="widget-container"></div>
    <?php endif; ?>
</div>
<?= Html::endTag('div') ?>

<?php if (!$context->screenshot && $context->requireScreenshotCapture) : ?>
    <div id="<?= $context->id ?>_temp" style="display: none">
        <?= $this->render($context->embedType, ['doCapture' => true]) ?>
    </div>
<?php endif; ?>
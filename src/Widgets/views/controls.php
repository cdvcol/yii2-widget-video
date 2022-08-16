<?php

/** @var $context Cacko\Yii2\Widgets\Video\Widgets\VideoWidget */
$context = $this->context;
$fullscreenOptions = $context->getFullScreenOptions();
$selectorToggle = $fullscreenOptions->selectorToggle;

$css = <<<CSS
#{$context->id} .video-controls {
    display: flex;
    position: absolute;
    bottom: 0;
    right: 0;
    opacity: 0;
}
#{$context->id} .embed-responsive:hover .video-controls {
    opacity: 1;
}
#{$context->id} .video-controls {$selectorToggle} {
    position: unset;
}
#{$context->id}:hover {$fullscreenOptions->selectorToggle} {
    opacity: inhert;
    transition: none;
}
#{$context->id} {$fullscreenOptions->selectorToggle} {
    opacity: inhert;
    transition: none;
}
CSS;
?>
<style>
    <?= $css ?>
</style>
<div class="video-controls-container embed-responsive-item" style="display: none">
    <div class="click-to-unmute mute_btn" style="display: none">
        <div><i class="icon-video-volume-high"></i></div>
        <div>Click to unmute</div>
    </div>
    <div class="video-controls" style="display:none;">
        <div class="volume-control">
            <div class="volume volume-button">
                <i class="icon-video-volume-high on-icon"></i>
                <i class="icon-video-volume-off mute-icon"></i>
                <div class="volume volume-range">
                    <input class="range" type="range" min="0" max="100" />
                </div>
            </div>
        </div>
        <?php if (!$context->isFlippingIphone) : ?>
            <div class="<?= $fullscreenOptions->classToggle ?>">
                <i class="<?= $fullscreenOptions->iconExpand ?>"></i>
            </div>
        <?php endif; ?>
    </div>
    <a class="play-button">
        <div class="play"></div>
    </a>
</div>
/* jshint -W030 */
(function ($) {
    class WidgetVideo {
        /* jshint ignore:start */
        _target = $();
        _startInterval = null;
        $playButton = $();
        $muteButton = $();
        $unmuteButton = $();
        $controls = $();
        $controlsContainer = $();
        loaded = false;
        widgetId;
        unMuted = false;
        videoEnded = false;
        playStarted = false;
        $screenshotContainer = $();
        autoplaying = false;

        /* jshint ignore:end */
        constructor(options) {
            this._target = options._target;
            this.widgetId = this._target.attr('id');
            this.options = options;
            this.$screenshotContainer = this._target.find(this.options.screenshotContainer);
            this._target.on('screenshot.video', $.proxy(this.onScreenshot, this));
            (async () => {
                await this.listenForUnlock();
                if (this.options.lightbox) {
                    this.initModal();
                } else {
                    this.load();
                }
            })();
        }

        load() {
            (async () => {
                ((require, define, requirejs) => {
                    require([`players/${this.options.videoType}`], (Player) => {
                        const startInterval = setInterval(async () => {
                            const now = (new Date()).getTime();
                            if (!this.options.startTime || now >= this.options.startTime) {
                                clearInterval(startInterval);
                                if (!this.loaded) {
                                    await this.initEmbedCode();
                                    this.$unmuteButton = this._target.find(this.options.unmuteButton);
                                    this.$muteButton = this._target.find(this.options.muteButton);
                                    this.$playButton = this._target.find(this.options.playButton);
                                    this.$controls = this._target.find(this.options.controlsSelector);
                                    this.$controlsContainer = this._target.find(this.options.controlsContainerSelector);
                                    if (this.options.startTime && now >= this.options.startTime) {
                                        this.options.startPosition = (now - this.options.startTime) / 1000;
                                    }
                                    this.player = new Player(this._target, { start: this.options.startPosition });
                                    await this.player.load();
                                    this.$controlsContainer.show();
                                    this.registerListeners();
                                    this.loaded = true;
                                    this._target.trigger('loaded.widget');
                                }
                                this.onLoad();
                            }
                        }, 1000);
                    }, () => console.warn(`${this.videoType} is not supported`));
                })(__require.require, __require.define, __require.requirejs);
            })();
        }

        initModal() {
            const $modal = $(`#${this.widgetId}-modal`);
            const $body = $modal.find('.modal-body');
            const initContent = $body.html();
            $modal.on('hidden.bs.modal', (e) => {
                $body.empty();
                this.loaded = false;
                this.$screenshotContainer.show();
            });
            $modal.on('show.bs.modal', (e) => {
                $body.html(initContent);
            });
            $modal.on('shown.bs.modal', (e) => {
                this.load();
            });
        }

        initEmbedCode() {
            return new Promise(resolve => {
                const $screenshotThumb = this._target.find('.video-thumb');
                if ($screenshotThumb.length || this.$widgetContainer.hasClass('modal-body')) {
                    if (!this.options.lightbox) {
                        $screenshotThumb.remove();
                    }
                    resolve(this.$widgetContainer.html(this.options.embedCode));
                } else {
                    resolve(true);
                }
            });
        }

        registerListeners() {
            if (this.hasControls) {
                this.$playButton.on("click", $.proxy(this.onPlayToggle, this));
                this.$unmuteButton.on("click", $.proxy(this.onAudioToggle, this));
                this.$muteButton.on("click", "i", $.proxy(this.onAudioToggle, this));
                this._target.on("input", this.options.volumeButton, $.proxy(this.onVolumeUpdate, this));
            }
            this._target.on('playing.video', $.proxy(this.onPlayingVideo, this));
            this._target.on('paused.video', $.proxy(this.onPausedVideo, this));
            this._target.on('ended.video', $.proxy(this.onEndedVideo, this));
            this._target.on('mute.video', $.proxy(this.onMuteVideo, this));
            this._target.on('unmute.video', $.proxy(this.onUnmuteVideo, this));
            this._target.on('destroy.video', $.proxy(this.onDestroy, this));
        }

        onScreenshot(event, screenshot) {
            this._target.find('.video-thumb img').attr('src', screenshot);
            this.$screenshotContainer.css('background-image', `url('${screenshot}')`);
        }

        listenForUnlock() {
            return new Promise(resolve => {
                if (this.audioUnlocked) {
                    return resolve(true);
                }
                const unlock = () => {
                    window.audioUnlocked = true;
                    document.removeEventListener(`touchstart`, unlock, true);
                    document.removeEventListener(`touchend`, unlock, true);
                    document.removeEventListener(`click`, unlock, true);
                };
                document.addEventListener(`touchstart`, unlock, true);
                document.addEventListener(`touchend`, unlock, true);
                document.addEventListener(`click`, unlock, true);
                resolve(false);
            });
        }

        onLoad() {
            (async () => {
                this.$screenshotContainer.hide();
                this._target.trigger('loaded.video');

                if (!this.options.autoplay) {
                    return this.hasControls && this.$playButton.fadeIn();
                }

                if (!this.audioUnlocked) {
                    await this.player.mute().catch(() => true);
                }

                await this.player.autoplay().catch(() => true);

                if (this.options.startPosition) {
                    await this.player.seek(this.options.startPosition).catch(() => false);
                }

                if (this.videoEnded) {
                    return;
                }

                this.options.allowUnmute && (this.autoplaying = true) && await this.player.unmute().catch(() => false);
            })();
        }

        get audioUnlocked() {
            return window.audioUnlocked === true;
        }

        onAudioToggle() {
            (async () => {
                this.$unmuteButton.remove();
                if (await this.player.isMuted()) {
                    await this.player.unmute().catch(() => false);
                } else {
                    await this.player.mute().catch(() => false);
                }
            })();
        }

        onMuteVideo() {
            this.$unmuteButton.show();
            !this.playStarted && !this.unMuted && this.$controls.hide();
            this.$muteButton.addClass("muted");
        }

        onUnmuteVideo() {
            this.unMuted = true;
            this.$unmuteButton.hide();
            this.$controls.show();
            this.$muteButton.removeClass("muted");
            this.hasControls && this.playStarted && this.player.videoContainer.toggleClass('no-touch', true);
        }

        onVolumeUpdate(e) {
            this.$unmuteButton.remove();
            (async () => this.player.volume(e.currentTarget.value))();
        }

        onDestroy() {
            (async () => this.player.destroy().catch(() => false));
        }

        onPlayToggle() {
            (async () => {
                if (await this.player.isPlaying()) {
                    return;
                }
                const now = (new Date()).getTime();
                if (this.hasControls && this.options.startTime && now >= this.options.startTime) {
                    const startPosition = (now - this.options.startTime) / 1000;
                    await this.player.seek(startPosition);
                    !this.options.autoplay && await this.player.pause();
                }
                await this.play();
            })();
        }

        onPlayingVideo() {
            this.autoplaying = false;
            this.playStarted = true;
            this.videoEnded = false;
            this.hasControls
                && !!this.$playButton.fadeOut()
                && !!this.player.videoContainer.toggleClass('no-touch', true)
                && this.$controls.show();
        }

        onPausedVideo() {
            this.hasControls && !this.videoEnded && !this.autoplaying && this.$playButton.fadeIn();
            if (this.autoplaying) {
                (async () => {
                    this.autoplaying = false;
                    await this.player.mute().catch(() => false);
                    await this.player.play().catch(() => false);
                })();
            }
        }

        onEndedVideo() {
            (async () => {
                this.videoEnded = true;
                if (this.options.loop) {
                    return this._target.trigger('loop.player');
                }
                await this.player.stop().catch(e => false);
                if (this.options.endImage) {
                    this.$screenshotContainer.replaceWith(this.options.endImage);
                    this.$widgetContainer.remove();
                }
                if (this.hasControls) {
                    this.$controlsContainer.hide();
                    this.$screenshotContainer.show();
                }
                this._target.trigger("ended.widget");
            })();
        }

        play() {
            (async () => {
                if (await this.player.isPlaying()) {
                    return;
                }
                this.player.play().then(() => {
                    this.hasControls && !this.videoEnded && this.$playButton.fadeOut();
                }).catch(() => {
                    this.hasControls && !this.videoEnded && this.$playButton.fadeIn();
                });
            })();
        }

        stop() {
            this.player && this.player.stop();
        }

        get $widgetContainer() {
            return this._target.find(this.options.widgetContainer);
        }

        get hasControls() {
            return this.$controlsContainer.length > 0;
        }
    }

    $.fn.widgetVideo = function (option) {
        const args = arguments;

        return this.each(function () {
            let data = $(this).data("WidgetVideo");
            const options = typeof option === "object" ? option : {};

            if (data === undefined) {
                const defaultOptions = $.extend(true, {}, $.fn.widgetVideo.defaults);
                options._target = $(this);

                $(this).data("WidgetVideo", (data = new WidgetVideo(
                    $.extend(defaultOptions, options)
                )));
            }

            if (typeof option === "string") { //call method
                data[option].apply(data, Array.prototype.slice.call(args, 1));
            }
        });
    };
}
)(jQuery);
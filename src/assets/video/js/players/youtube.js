/* jshint -W030 */
(function (require, define, requirejs) {
    define(['./base', 'module'], function (Base, module) {
        module.exports = class extends Base {
            /* jshint ignore:start */
            lastVolume = 100;
            muted = false;
            unmuting = false;
            duration;

            /* jshint ignore:end */
            load() {
                return new Promise((resolve) => {
                    if (this.player) {
                        return resolve(true);
                    }
                    this.player = new YT.Player(this.videoContainerId, {
                        videoId: this.videoContainer.data('video-id'),
                        playerVars: Object.assign({}, this.videoContainer.data('player-vars'), this.playerVars),
                    });
                    this.player.addEventListener("onReady", () => {
                        this.lastVolume = this.player.getVolume();
                        resolve(this.player.addEventListener("onStateChange", e => this.onStateChange(e)));
                        this.isPlaying().then(playing => playing && this.container.trigger("playing.video"));
                    });
                    this.container.on('loop.player', () => {
                        (async () => {
                            await this.seekTo(0);
                            this.play();
                        })();
                    });
                });
            }

            stop() {
                return Promise.resolve(this.player.stopVideo());
            }

            /*eslint no-return-assign: "error"*/
            onStateChange(e) {
                switch (e.data) {
                    case -1:
                        return this.unmuting && this.container.trigger("paused.video") && (this.unmuting = false);
                    case YT.PlayerState.ENDED:
                        return !this.player.getPlaylist() && this.container.trigger("ended.video");
                    case YT.PlayerState.PLAYING:
                        return this.container.trigger("playing.video");
                    case YT.PlayerState.PAUSED:
                        return this.container.trigger("paused.video");
                }
            }

            autoplay() {
                return Promise.resolve((this.play()));
            }

            play() {
                return Promise.resolve(this.player.playVideo());
            }

            pause() {
                return Promise.resolve(this.player.pauseVideo());
            }

            destroy() {
                return Promise.resolve(this.player.destroy());
            }

            mute() {
                return new Promise(resolve => {
                    if (!this.player.muted) {
                        this.player.mute();
                        (async () => {
                            const isMuted = await this.player.isMuted();
                            if (!isMuted) {
                                await this.player.setVolume(0);
                            }
                        })();
                    }
                    this.muted = true;
                    this.container.trigger('mute.video');
                    resolve(true);
                });
            }

            unmute() {
                return new Promise(resolve => {
                    (async () => {
                        this.unmuting = true;
                        this.player.unMute();
                        const volume = this.player.getVolume();
                        if (!volume) {
                            await this.volume();
                        }
                        if (!this.player.isMuted()) {
                            this.muted = false;
                            this.container.trigger('unmute.video');
                            return resolve(!this.muted);
                        }
                        const isPlaying = await this.isPlaying();
                        if (this.unmuting && isPlaying) {
                            this.unmuting = false;
                            this.container.trigger('unmute.video');
                        }
                        resolve(!this.muted);
                    })();
                });
            }

            isMuted() {
                return Promise.resolve(this.muted || !this.player.getVolume() || this.player.isMuted());
            }

            isPaused() {
                return Promise.resolve([YT.PlayerState.PAUSED, YT.PlayerState.BUFFERING].includes(this.player.getPlayerState()));
            }

            seekTo(seconds) {
                return new Promise(resolve => {
                    const durationCheck = setInterval(() => {
                        this.duration = this.player.getDuration();
                        if (this.duration) {
                            clearInterval(durationCheck);
                            if (seconds >= this.duration) {
                                resolve(this.container.trigger("ended.video"));
                            } else {
                                resolve(this.player.seekTo(seconds));
                            }
                        }
                    }, 200);
                });
            }

            volume(level) {
                const val = level || this.lastVolume;
                this.lastVolume = val;
                return Promise.resolve(this.player.setVolume(val));
            }

            isPlaying() {
                return Promise.resolve(this.player.getPlayerState() === YT.PlayerState.PLAYING);
            }
        };
    });
}(__require.require, __require.define, __require.requirejs));



(function (require, define, requirejs) {
    define(['./base', 'require', 'module'], function (Base, require, module) {
        module.exports = class extends Base {
            /* jshint ignore:start */
            _volume = 1;
            duration = 0;
            playing = false;

            /* jshint ignore:end */
            load() {
                return new Promise(resolve => {
                    if (this.player) {
                        return resolve(true);
                    }
                    this.player = new Vimeo.Player(this.videoContainerId, this.videoContainer.data('player-vars'));
                    this.player.ready().then(() => {
                        this.player.on("play", () => {
                            this.playing = true;
                            this.container.trigger("playing.video");
                            (async () => {
                                const isMuted = await this.isMuted();
                                this.container.trigger(`${isMuted ? 'mute' : 'unmute'}.video`);
                            })();
                        });
                        this.player.on("pause", () => {
                            this.playing = false;
                            this.container.trigger("paused.video");
                        });
                        this.player.on("ended", () => {
                            this.player = false;
                            this.container.trigger("ended.video");
                        });
                        this.player.on("volumechange", v => this.container.trigger(`${v.volume ? 'unmute' : 'mute'}.video`));
                        this.player.getDuration().then(d => {
                            this.duration = d;
                            resolve(true);
                        });
                    });
                });
            }

            autoplay() {
                return this.player.play();
            }

            play() {
                return this.player.play();
            }

            stop() {
                return this.player.pause();
            }

            seek(seconds) {
                if (!seconds) {
                    return Promise.resolve(false);
                }

                if (seconds >= this.duration) {
                    this.playing = false;
                    this.container.trigger("ended.video");
                    return Promise.resolve(false);
                }

                return this.player.setCurrentTime(seconds);
            }

            isMuted() {
                return new Promise(r => this.player.getVolume().then(res => r(res === 0)));
            }

            isPaused() {
                return new Promise(r => this.player.getPaused().then(res => r(res)));
            }

            isPlaying() {
                return Promise.resolve(this.playing);
            }

            volume(level) {
                return this.player.setVolume(level / 100);
            }

            mute() {
                return new Promise(resolve => {
                    (async () => {
                        this._volume = await this.player.getVolume();
                        !this._volume && this.container.trigger('mute.video');
                        resolve(this.player.setVolume(0));
                    })();
                });
            }

            unmute() {
                return new Promise(resolve => {
                    (async () => {
                        const isMuted = await this.isMuted();
                        !isMuted && this.container.trigger('unmute.video');
                        resolve(this.player.setVolume(this._volume || 1));
                    })();
                });
            }

            destroy() {
                return Promise.resolve(this.player.destroy());
            }
        };
    });
}(__require.require, __require.define, __require.requirejs));


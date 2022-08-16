(function (require, define, requirejs) {
    define(['./base', 'module'], function (Base, module) {
        module.exports = class extends Base {
            /* jshint ignore:start */
            lastVolume = 100;
            muted = false;
            duration = 0;
            playing = false;
            /* jshint ignore:end */

            load() {
                return new Promise(resolve => {
                    if (this.player) {
                        return resolve(true);
                    }
                    const vars = this.videoContainer.data('player-vars');
                    if (!vars) {
                        this.player = true;
                        return resolve(true);
                    }
                    this.player = new Twitch.Player(this.videoContainerId, vars);
                    this.player.addEventListener(Twitch.Player.READY, () => {
                        this.duration = this.player.getDuration() || Number.MAX_VALUE;
                        this.player.addEventListener(Twitch.Player.PLAYING, () => this.container.trigger("playing.video") && (this.playing = true));
                        this.player.addEventListener(Twitch.Player.PLAY, () => this.container.trigger("playing.video") && (this.playing = true));
                        this.player.addEventListener(Twitch.Player.PAUSE, () => this.container.trigger("paused.video") && (this.playing = false));
                        this.player.addEventListener(Twitch.Player.OFFLINE, () => this.container.trigger("ended.video") && (this.playing = false));
                        this.player.addEventListener(Twitch.Player.ENDED, () => this.container.trigger("ended.video") && (this.playing = false));
                        resolve(true);
                    });
                });
            }

            stop() {
                return Promise.resolve(this.player === true || this.player.pause() || this.seek(0.1));
            }

            autoplay() {
                return this.play();
            }

            play() {
                return Promise.resolve(this.player === true || this.player.play());
            }

            pause() {
                return Promise.resolve(this.player === true || this.player.pause());
            }

            mute() {
                if (this.player === true) {
                    return Promise.resolve(false);
                }
                this.player.setMuted(true);
                if (!this.player.getMuted()) {
                    this.lastVolume = this.player.getVolume() * 100;
                    this.volume(0);
                }
                this.muted = true;
                return Promise.resolve((this.container.trigger('mute.video')));
            }

            seek(seconds) {
                if (this.player === true) {
                    return Promise.resolve(true);
                }
                return Promise.resolve(seconds >= this.duration ? this.container.trigger("ended.video") : this.player.seek(seconds));
            }

            unmute() {
                if (this.player === true) {
                    return Promise.resolve(true);
                }
                this.player.setMuted(false);
                if (!this.player.getVolume()) {
                    this.volume(this.lastVolume || 50);
                }
                this.muted = this.player.getMuted();
                return Promise.resolve((this.container.trigger('unmute.video')));
            }

            isMuted() {
                return Promise.resolve(this.player === true || this.muted || !this.player.getVolume() || this.player.getMuted());
            }

            isPaused() {
                return Promise.resolve(this.player === true || this.player.isPaused());
            }

            isPlaying() {
                return Promise.resolve(this.player === true && this.playing);
            }

            volume(level) {
                return Promise.resolve(this.player === true || this.player.setVolume(level / 100));
            }

            destroy() {
                return Promise.resolve(this.player.pause());
            }
        };
    });
}(__require.require, __require.define, __require.requirejs));



(function (require, define, requirejs) {
    define(['./base', 'module'], function (Base, module) {
        module.exports = class extends Base {
            /* jshint ignore:start */
            duration = 0;
            playing = false;
            autoplaying = false;
            _volume = 1;
            /* jshint ignore:end */
            load() {
                return new Promise(resolve => {
                    if (this.player) {
                        return resolve(true);
                    }
                    this.player = this.videoContainer.get(0);
                    this.player.load();
                    this.player.addEventListener('loadedmetadata', () => {
                        this.duration = this.player.duration;
                        this.player.addEventListener("play", () => (this.playing = true) && this.container.trigger("playing.video") && (this.autoplaying = false));
                        this.player.addEventListener("pause", () => this.container.trigger("paused.video") && (this.playing = false));
                        this.player.addEventListener("ended", () => this.container.trigger("ended.video") && (this.playing = false));
                        this.player.addEventListener("seeked", () => this.duration <= this.player.currentTime && this.container.trigger("ended.video"));
                        this.player.addEventListener("volumechange", () => this.container.trigger(`${this.player.muted ? 'mute' : 'unmute'}.video`));
                        resolve(true);
                    }, { once: true });
                });
            }

            autoplay() {
                this.autoplaying = true
                return this.play();
            }

            play() {
                return Promise.resolve(this.duration > this.player.currentTime && this.player.play());
            }

            stop() {
                return Promise.resolve(this.player.pause());
            }

            mute() {
                return new Promise(resolve => {
                    if (this.player.muted || !this.player.volume) {
                        this.container.trigger('mute.video');
                        return resolve(true);
                    }
                    this.player.volume = 0;
                    this.player.muted = true;
                    this.player.addEventListener("volumechange", () => {
                        resolve(this.player.muted);
                    }, { once: true });
                });
            }

            unmute() {
                return new Promise(resolve => {
                    if (!this.player.muted && this.player.volume) {
                        this.container.trigger('unmute.video');
                        return resolve(true);
                    }
                    this.player.volume = this._volume;
                    this.player.muted = false;
                    this.player.addEventListener("volumechange", () => {
                        if (this.autoplaying && this.player.paused) {
                            this.container.trigger("paused.video");
                        }
                        this.container.trigger(`${this.player.muted ? 'mute' : 'unmute'}.video`);
                        resolve(!this.player.muted);
                    }, { once: true });

                });
            }

            isMuted() {
                return Promise.resolve(this.player.muted);
            }

            isPaused() {
                return Promise.resolve(this.player.paused);
            }

            seek(seconds) {
                return Promise.resolve(seconds >= this.duration ? this.container.trigger("ended.video") : (this.player.currentTime = seconds));
            }

            volume(level) {
                this._volume = this.player.volume;
                return Promise.resolve(this.player.volume = level / 100);
            }

            isPlaying() {
                return Promise.resolve(this.playing);
            }

            destroy() {
                return Promise.resolve(this.player.pause() && this.player.removeAttribute('src') && this.player.load());
            }

        };
    });
}(__require.require, __require.define, __require.requirejs));



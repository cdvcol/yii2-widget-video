(function (require, define, requirejs) {
    define(['./base', 'module'], function (Base, module) {
        module.exports = class extends Base {
            /* jshint ignore:start */
            muted = false;
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
                    brightcovePlayerLoader({
                        refNode: this.videoContainer.get(0),
                        accountId: this.videoContainer.data('account'),
                        playerId: this.videoContainer.data('player'),
                        videoId: this.videoContainer.data('video-id')
                    }).then(() => {
                        this.player = videojs.getPlayers()[this.videoContainerId];
                        this.player.load();
                        this.player.on('loadedmetadata', () => {
                            this.duration = this.player.mediainfo.duration;
                            this.player.on("play", () => (this.playing = true) && this.container.trigger("playing.video"));
                            this.player.on("pause", () => this.container.trigger("paused.video"));
                            this.player.on("ended", () => this.container.trigger("ended.video"));
                            this.player.on("seeked", () => this.duration <= this.player.currentTime() && this.container.trigger("ended.video"));
                            this.player.on("volumechange", () => this.container.trigger(`${this.player.muted() ? 'mute' : 'unmute'}.video`));
                            resolve(true);
                        });
                    });
                });
            }

            autoplay() {
                this.autoplaying = true
                return Promise.resolve((this.player.play()));
            }

            play() {
                return Promise.resolve(this.duration > this.player.currentTime() && this.player.play());
            }

            stop() {
                return Promise.resolve(this.player.pause());
            }

            mute() {
                if (this.player.muted()) {
                    this.container.trigger("mute.video");
                } else {
                    this.player.muted(true);
                }
                return Promise.resolve(true);
            }

            unmute() {
                return new Promise(resolve => {
                    if (!this.player.muted() && this.player.volume()) {
                        this.container.trigger("unmute.video");
                        return resolve(true);
                    }
                    this.player.volume(this._volume);
                    this.player.muted(false);
                    this.player.on("volumechange", () => {
                        if (this.autoplaying && this.player.paused()) {
                            this.container.trigger("paused.video");
                        }
                        resolve(!this.player.muted());
                    });

                });

            }

            isMuted() {
                return Promise.resolve(this.player.muted());
            }

            isPaused() {
                return Promise.resolve(this.player.paused());
            }

            seek(seconds) {
                return Promise.resolve(seconds >= this.duration ? this.container.trigger("ended.video") : this.player.currentTime(seconds));
            }

            volume(level) {
                this._volume = this.player.volume();
                return Promise.resolve(this.player.volume(level / 100));
            }

            isPlaying() {
                return Promise.resolve(this.playing);
            }

            destroy() {
                return Promise.resolve(this.player.dispose());
            }
        };
    });
}(__require.require, __require.define, __require.requirejs));



/* jshint -W016 */
(function (require, define, requirejs) {
    define(['./base', 'module'], function (Base, module) {
        module.exports = class extends Base {
            /* jshint ignore:start */
            unMuted = false;
            lastVolume = 100;
            duration = 0;

            /* jshint ignore:end */
            load() {
                return new Promise(resolve => {
                    if (this.player) {
                        return resolve(true);
                    }
                    window._wq = window._wq || [];
                    const onReady = (video) => {
                        this.player = video;
                        this.duration = video.duration();
                        this.player.bind("play", () => {
                            (async () => {
                                this.container.trigger("playing.video");
                                const isMuted = await this.isMuted();
                                this.container.trigger(`${isMuted ? 'mute' : 'unmute'}.video`)
                            })();
                        });
                        this.player.bind("pause", () => this.container.trigger("paused.video"));
                        this.player.bind("end", () => this.container.trigger("ended.video"));
                        this.player.bind("mutechange", isMuted => this.container.trigger(`${isMuted ? 'mute' : 'unmute'}.video`) && (this.unMuted |= !isMuted));
                        this.player.bind("silentplaybackmodechange", inSilentPlaybackMode => this.container.trigger(`${inSilentPlaybackMode ? 'mute' : 'unmute'}.video`));
                        resolve(true);
                    };
                    _wq.push({
                        id: `${this.videoContainerId}`,
                        onReady
                    });
                });
            }

            autoplay() {
                return Promise.resolve((this.player.play()));
            }

            play() {
                return Promise.resolve(this.player.play());
            }

            stop() {
                return Promise.resolve(this.player.pause());
            }

            mute() {
                return Promise.resolve(this.unMuted ? (this.player.volume(0) && this.container.trigger('mute.video')) : this.player.mute());
            }

            unmute() {
                return Promise.resolve(this.unMuted ? this.volume(this.lastVolume) : this.player.unmute());
            }

            isMuted() {
                return Promise.resolve(!this.player.volume() || this.player.isMuted());
            }

            isPaused() {
                return Promise.resolve(['paused', 'beforeplay'].includes(this.player.state()));
            }

            seek(seconds) {
                return Promise.resolve(seconds >= this.duration ? this.container.trigger("ended.video") : this.player.time(seconds));
            }

            volume(level) {
                return Promise.resolve(this.player.volume(level / 100) && (this.lastVolume = level));
            }

            isPlaying() {
                return Promise.resolve(this.player.state === 'playing');
            }

            destroy() {
                return Promise.resolve(this.player.remove());
            }
        };
    });
}(__require.require, __require.define, __require.requirejs));



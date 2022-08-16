(function (require, define, requirejs) {
    define(['jquery', 'module'], function ($, module) {
        module.exports = class {

            /* jshint ignore:start */
            container;
            player;
            playerVars = {};
            /* jshint ignore:end */

            constructor(container, playerVars) {
                this.container = container;
                this.playerVars = Object.assign({}, playerVars);
            }

            get videoContainerId() {
                return `${this.container.attr("id")}_video`;
            }

            get videoContainer() {
                return $(`#${this.videoContainerId}`);
            }

            stop() {
                return Promise.resolve(true);
            }

            isMuted() {
                return Promise.resolve(true);
            }

            isPaused() {
                return Promise.resolve(true);
            }

            mute() {
                return Promise.resolve(true);
            }

            unmute() {
                return Promise.resolve(true);
            }

            volume(level) {
                return Promise.resolve(level);
            }

            play() {
                return Promise.resolve(true);
            }

            pause() {
                return Promise.resolve(true);
            }

            seek(seconds) {
                return Promise.resolve(seconds);
            }

            isPlaying() {
                return Promise.resolve(false);
            }

            autoplay() {
                return Promise.resolve(false);
            }

            destroy() {
                return Promise.resolve(false);
            }
        };
    });
}(__require.require, __require.define, __require.requirejs));

# yii2-widget-video

Yii2 widget for handling videos from:
* youtube - videos and playlists
* vimeo - can't remember, probably both
* wistia - whatever, it's paid crap
* brightcove - same as above
* twitch - supports it all, there's obviously limitations on the livestreams etc, just use common sense
* mp4 containers - as long as the browser supports it

it obviously relies on all the shite Yii2 decided to put as their defaults - bootshite, jQueer...etc

## Demo

https://yii.cacko.net/video/widget it's just a form with all options, so you can test, it has a barbaric look and feel. Demo site source is at https://gitlab.com/cacko/yii2-widgets

## Usage

```php
VideoWidget::widget([
  'url' => 'https://www.youtube.com/watch?v=N4q2kBe82-o',
])
  ````
  
### Options

* `url` - mandatory
* `autoPlay` - auto play the video. if using the new IE aka Chromium based browsers - you will most likely end up with muted video. the script will try its best ot play it with sound whenever possible. *JUST DO NOT USE IT, IT IS STUPID IDEA*.
* `hideContols` - hides the native provder controls and puts volume and full screen only. genereraly the idea is combined with startTimestamp to make a a non live stream look like a stream. pretty much useless, since average joe can just open dev tools and remove the non touch events, anyway.
* `startTimestamp` - when to start it, in Unix timestamp in seconds. Note that the timestamp comes from the server, but the wait is happening on the client browser, so if their's time is off, it will be off.
* `startPosition` - from where to start the video, in seconds
* `openInModal` - open the video in modal dialog
* `loop` - rather pointless thing, but whatever, repeats the video or playlist after it ends.
* `placeholderImage` - replaces the thumbnail from the video, in case you want to put your crappy logo to boost your genitals
* `placeholderEndImage` - shows it after the video ends. logically if you use loop that will never show..capishe ?

combining some options can be nonse, again use common sense.

## Setup

## Instalation
just use composer, ok ? or better just write your own.
```shell
composer require cacko/yii2-widget-video
```

### Screenshot persistance
the extension stores downloaded thumbnails in the system temp folder.
If you want to change, set a dependency of `Cacko\Yii2\Widgets\Video\Models\ScreenshotInterface`. if you don't know how - [RTFM](https://www.yiiframework.com/doc/guide/2.0/en/concept-di-container).

### Controller for captures
some providers or if you are using mp4 file, do not have api for screeshots, so it is done via the shite way and requires a controller endpoint. it points somewhere now, if you want to change it, same as above , DI -> `Cacko\Yii2\Widgets\Video\Controller\ControllerInterface`..etc

```php
Yii::$container->set('Cacko\Yii2\Widgets\Video\Models\ScreenshotInterface', MyJunkPersistanceCrap::class);
```

### Providers credentials
currently only youtube and twich require credentials, simply set a component definitions in the main app config.

```php
    'components' => [

        'youtubeApi' => [
            'class' => 'Cacko\Yii2\Widgets\Video\Components\YouTube\Api',
            'youtubeKey' => 'xxxx',
        ],
        'twitchApi' => [
            'class' => 'Cacko\Yii2\Widgets\Video\Components\Twitch\Api',
            'clientId' => 'xxx',
            'secretId' => 'xxx',
        ]
```

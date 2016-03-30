# Monolog Handlers

* TelegramHandler - Monolog Handler to push notification to Telegram.
* MemcachedBufferHandler - Monolog Handler that allows to reduce message rate.

## Installation

Add the following to your composer.json:
```js
  "require": {
        "privatedev/monolog-handlers": "dev-master",
    }
...
  "repositories": [
        {
            "type": "vcs",
            "url":  "https://github.com/PrivateDev/monolog-handlers.git"
        }
    ],
```

## Usage
------------
### TelegramHandler

Requires TelegramBot's ID and Telegram chat/channel id or username
1. [Create a bot](https://core.telegram.org/bots#3-how-do-i-create-a-bot) and get it's ID ```(example: 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11)```
2. Create a channel/chat and get it's id or username. Add members to your chat
3. Pass both params to TelegramHandler constructor.

### SlackHandler

Requires Slack webhook, channel and username
1. Make a webhook ```(example: https://hooks.slack.com/services/XXXXXXX/XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX)```
2. Create a channel and get it's username. Add members to your chat
3. Pass params to SlackHandler constructor.

### MemcachedBufferHandler

**Requires Memcached driver**. Pass it and your Handler, where you want to reduce message rate, to MemcachedBufferHandler controller.
> To pass more than one handler you may use Monolog's GroupHandler to group them.

### How to add custom Handler see your framework docs:

- Laravel: http://laravel.com/docs/5.1/errors#configuration
- Symfony2: http://symfony.com/doc/current/cookbook/logging/monolog.html#using-several-handlers
# PhergieIsDownCheck

[Phergie](http://github.com/phergie/phergie-irc-bot-react/) plugin to check websites using [downforeveryoneorjustme](http://downforeveryoneorjustme.com).

## Install

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `~`.

```
composer require hashworks/PhergieIsDownCheck
```

See Phergie documentation for more information on
[installing and enabling plugins](https://github.com/phergie/phergie-irc-bot-react/wiki/Usage#plugins).

## Configuration

```php
// dependency
new \WyriHaximus\Phergie\Plugin\Dns\Plugin,
new \WyriHaximus\Phergie\Plugin\Http\Plugin(array('dnsResolverEvent' => 'dns.resolver')),
new \Phergie\Irc\Plugin\React\Command\Plugin,
new \hashworks\Phergie\Plugin\IsDownCheck\Plugin()
```

##Syntax

```
<%hashworks> !isdown hashworks.net
< moonbase> Checking hashworks.net...
< moonbase> It's just you. http://hashworks.net is up.
```
# Signal Handler for PHP

[![Build Status](https://travis-ci.org/romainneutron/signal-handler.png?branch=master)](https://travis-ci.org/romainneutron/signal-handler)

A simple signal handler to manage incoming posix signals.

It is implemented as a singleton as `pcntl_signal` can only register one
callback per signal.

Using this one, you can register as many callbacks per signal as needed.

For more information about signals, `man signal`.

```php
// mandatory to listen to signals
declare(ticks=1);
$handler = Neutron\SignalHandler\SignalHandler::getInstance();
$handler->register(array(SIGINT, SIGTERM), function () { echo "stoppin !"; exit(); });
$handler->register(SIGCONT, function () { echo "all systems go..."; });
```

## Register a signal handler

```php
// register a handler for SIGCONT in default namespace
$handler->register(SIGCONT, function () { echo "SIGCONT received"; });
// register a handler for SIGCONT in "a namespace"
$handler->register(SIGCONT, function () { echo "SIGCONT received"; }, 'a namespace');
// register a handler for SIGCONT in "another namespace"
$handler->register(SIGINT, function () { echo "Interrupted !"; exit(); }, 'another namespace');
```

## Unregister signals handler

Two ways are available to unregister signals.

By namespace :

```php
// unregister all handlers in "another namespace"
$handler->unregisterNamespace('another namespace');
// unregister all SIGINT handlers in "a namespace"
$handler->unregisterNamespace('a namespace', SIGINT);
```

By signal :

```php
// unregister all SIGINT handlers in any namespace
$handler->unregisterSignal(SIGINT);
```

## License

This project is released under the MIT license.

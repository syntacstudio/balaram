# Balaram

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

Backup Laravel app to Telegram automatically

## Installation

Via Composer

``` bash
$ composer require syntac/balaram
```

## Usage
add these ENV vars to your ENV file
BACKUP_TARGET=storage,public
BACKUP_DATABASE=true
BACKUP_DATABSE_TYPE=MySql
TELEGRAM_BOT_TOKEN=
TELEGRAM_CHAT_ID=
TELEGRAM_BOT_USERNAME=

then add artisan command "backup:run" to your Console/Kernel.php set the variety of schedules you may assign to backup task.

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [author name][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/syntac/balaram.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/syntac/balaram.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/syntac/balaram/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/syntac/balaram
[link-downloads]: https://packagist.org/packages/syntac/balaram
[link-travis]: https://travis-ci.org/syntac/balaram
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/syntac
[link-contributors]: ../../contributors

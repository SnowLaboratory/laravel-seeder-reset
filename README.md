<p align="center"><img width="472" src="./art/laravel-seeder-reset-banner.png" alt="Laravel SeederReset Package Logo"></p>

<p align="center">
    <a href="https://packagist.org/packages/snowbuilds/laravel-seeder-reset">
        <img src="https://img.shields.io/packagist/v/snowbuilds/laravel-seeder-reset.svg?style=flat-square" alt="Latest Version on Packagist" />
    </a>
    <a href="https://packagist.org/packages/snowbuilds/laravel-seeder-reset">
        <img src="https://img.shields.io/packagist/dt/snowbuilds/laravel-seeder-reset.svg?style=flat-square" alt="Total Downloads" />
    </a>
    <a href="#">
        <img src="https://github.com/SnowLaboratory/laravel-seeder-reset/actions/workflows/main.yml/badge.svg" alt="GitHub Actions" />
    </a>
</p>


- [Introduction](#introduction)
- [Installation](#installation)
- [Usage](#usage)
- [Roadmap](#roadmap)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Security Vulnerabilities](#security)
- [Code of Conduct](#code-of-conduct)
- [License](#license)

<a name="introduction"></a>
## Introduction
Truncate tables and delete old data before executing seeders. 

<a name="installation"></a>
## Installation

You can install the package via composer:

```bash
composer require "snowbuilds/laravel-seeder-reset:^0.0.1-alpha"
```

```bash
php artisan vendor:publish --provider="SnowBuilds\SeederReset\SeederResetServiceProvider"
```

<a name="usage"></a>
## Usage
Include the `SeederReset` trait in your seeders to prompt for truncation.

```php
use SnowBuilds\SeederReset\Concerns\Recommendations;
use SnowBuilds\SeederReset\SeederReset;

use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    use SeederReset;

    protected function truncated() {
        return [
            Analytics::class,
            Comment::class,
            Post::class,
        ];
    }
}
```


<a name="roadmap"></a>
## Roadmap
- [ ] Truncate tables from list of models
- [ ] Truncate using table names
- [ ] Delete data using queries


<a name="changelog"></a>
### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

<a name="contributing"></a>
## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

<a name="security"></a>
### Security

If you discover any security-related issues, please email dev@snowlaboratory.com instead of using the issue tracker.

## Code of Conduct
<a name="code-of-conduct"></a>

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

<a name="credits"></a>
## Credits

-   [Snow Labs](https://github.com/snowbuilds)
-   [All Contributors](../../contributors)

<a name="license"></a>
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

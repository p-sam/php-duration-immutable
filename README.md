p-sam/duration-immutable
=============================

[![license-badge]][license] [![release-version-badge]][packagist] ![php-version-badge]

Immutable PHP class to store time durations while not being tied to a Date.

Internally uses DateInterval and DateTime to do the actual operations.

## Installation ##

Install with composer:

```
$ composer require p-sam/duration-immutable
```

## Usage ##

### Instanciating ###

```php
use SP\DurationImmutable\DurationImmutable;

DurationImmutable::fromSeconds(2); // 2s
DurationImmutable::fromSeconds(-30); // - 30s
DurationImmutable::fromSeconds(92, 0.200130); // 1m 32s 200ms 130µs

DurationImmutable::fromPHPDateInterval(new DateInterval('P1DT4H')); // 1d 4H
$refDate = DateTimeImmutable::createFromFormat(DateTimeImmutable::ATOM, '2020-01-10T00:00:00+00:00');
DurationImmutable::fromPHPDateInterval(
    new DateInterval('P1M3D'),
    $refDate
); // 34d

// refer to DateInterval::createFromDateString
// for documentation on accepted formats
DurationImmutable::fromHuman('1 hour - 15 minutes'); // 45m
DurationImmutable::fromHuman('yesterday'); // - 1d 
```

### Ops ###

```php
use SP\DurationImmutable\DurationImmutable;

$duration = DurationImmutable::fromHuman('120 minutes'); // 2h

$duration->abs(); // 2h
$duration->add(DurationImmutable::fromHuman('30 minutes')); // 2h 30m
$duration->sub(DurationImmutable::fromHuman('3 hours + 10 minutes')); // - 1h 10m
$duration->mul(2.5); // 5h
$duration->div(-4); // - 30m

$dateTime = new DateTimeImmutable();
$duration->addTo($dateTime); // now + 2h

$duration->toSeconds(); // 7200
$duration->toIntervalSpec(); // "PT2H"
$duration->toPHPDateInterval(); // DateInterval("PT2H")
```

<!-- Badges -->
[packagist]: https://packagist.org/packages/p-sam/duration-immutable
[license]: LICENSE
[license-badge]: https://img.shields.io/github/license/p-sam/php-duration-immutable.svg?style=flat-square
[php-version-badge]: https://img.shields.io/packagist/php-v/p-sam/duration-immutable.svg?style=flat-square
[release-version-badge]: https://img.shields.io/packagist/v/p-sam/duration-immutable.svg?style=flat-square&label=release
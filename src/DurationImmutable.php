<?php

declare(strict_types=1);

namespace SP\DurationImmutable;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

class DurationImmutable
{
    /**
     * @var DateInterval
     */
    protected $interval;

    protected function __construct(DateInterval $interval)
    {
        $this->interval = $interval;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s%s (%.06F)',
            (bool) $this->interval->invert ? '-' : '+',
            $this->toIntervalSpec(),
            $this->interval->f
        );
    }

    /**
     * Creates a duration from a PHP DateInterval.
     * Since the interval also contains date-dependant infos (ex: months), a reference date can be used to compute days.
     */
    public static function fromPHPDateInterval(DateInterval $interval, ?DateTimeInterface $refDate = null): self
    {
        $refDate = static::normalizeDate($refDate);

        // DateInterval refresh (ex: 3732s -> 1h 2m 12s)
        $interval = $refDate->diff($refDate->add($interval));

        // Stripping date-dependant infos
        $interval->y = 0;
        $interval->m = 0;
        $interval->d = $interval->days;

        return new static($interval);
    }

    /**
     * Creates a duration from a human readable string (ex: "3 hours", "- 2 days", "tomorrow").
     */
    public static function fromHuman(string $duration): self
    {
        return static::fromPHPDateInterval(
            DateInterval::createFromDateString($duration)
        );
    }

    /**
     * Creates a duration from a number of seconds and optionally also microseconds as a fraction of a second.
     */
    public static function fromSeconds(int $secs, float $usecs = 0.0): self
    {
        $interval = new DateInterval('PT0S');
        $interval->s = $secs;
        $interval->f = $usecs;

        return static::fromPHPDateInterval($interval);
    }

    public function toSeconds(): int
    {
        return (new DateTimeImmutable('@0'))->add($this->interval)->getTimestamp();
    }

    public function toPHPDateInterval(): DateInterval
    {
        return clone $this->interval;
    }

    public function toIntervalSpec(): string
    {
        $dateItems = array_filter([
            'D' => $this->interval->d,
        ]);

        $timeItems = array_filter([
            'H' => $this->interval->h,
            'M' => $this->interval->i,
            'S' => $this->interval->s,
        ]);

        $timeIntervalSpec = \count($timeItems) > 0 ? 'T' : '';
        foreach ($timeItems as $k => $v) {
            $timeIntervalSpec .= $v.$k;
        }

        $intervalSpec = '';
        foreach ($dateItems as $k => $v) {
            $intervalSpec .= $v.$k;
        }

        if ('' === $timeIntervalSpec && '' === $intervalSpec) {
            return 'PT0S';
        }

        return 'P'.$intervalSpec.$timeIntervalSpec;
    }

    public function addTo(DateTimeInterface $dateTime): DateTimeInterface
    {
        return $dateTime->add($this->interval);
    }

    public function abs(): self
    {
        $interval = clone $this->interval;
        $interval->invert = 0;

        return new static($interval);
    }

    public function add(self $duration): self
    {
        $now = new DateTimeImmutable();

        return new static(
            $now->diff(
                $now->add($this->interval)
                    ->add($duration->interval)
            )
        );
    }

    public function sub(self $duration): self
    {
        $now = new DateTimeImmutable();

        return new static(
            $now->diff(
                $now->add($this->interval)
                    ->sub($duration->interval)
            )
        );
    }

    public function mul(float $f): self
    {
        $usecs = (float) ($this->toSeconds()) + $this->getFloatMicroseconds();
        $usecs *= $f;

        return static::fromSeconds(
            (int) ($usecs),
            fmod($usecs, 1)
        );
    }

    public function div(float $f): self
    {
        $usecs = (float) ($this->toSeconds()) + $this->getFloatMicroseconds();
        $usecs /= $f;

        return static::fromSeconds(
            (int) ($usecs),
            fmod($usecs, 1)
        );
    }

    public function isNegative(): bool
    {
        return (bool) $this->interval->invert;
    }

    public function getDays(): int
    {
        return $this->interval->d;
    }

    public function getHours(): int
    {
        return $this->interval->h;
    }

    public function getMinutes(): int
    {
        return $this->interval->i;
    }

    public function getSeconds(): int
    {
        return $this->interval->s;
    }

    public function getMicroseconds(): int
    {
        return (int) ($this->interval->f * 1e6);
    }

    public function getFloatMicroseconds(): float
    {
        return $this->interval->f;
    }

    protected static function normalizeDate(?DateTimeInterface $refDate): DateTimeImmutable
    {
        if (null === $refDate) {
            return new DateTimeImmutable();
        }
        if ($refDate instanceof DateTimeImmutable) {
            return $refDate;
        }

        /* @var DateTime $refDate */
        return DateTimeImmutable::createFromMutable($refDate);
    }
}

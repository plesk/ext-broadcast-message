<?php
// Copyright 1999-2026. WebPros International GmbH.
namespace PleskExt\BroadcastMessage\Helper;

class Expiration
{
    public const FORMAT = 'Y-m-d H:i';

    public static function isValidFormat(?string $value): bool
    {
        if ($value === null || trim($value) === '') {
            return true;
        }
        return static::parse($value) !== null;
    }

    public static function isExpired(?string $value, ?\DateTimeImmutable $now = null): bool
    {
        if ($value === null) {
            return false;
        }
        $until = static::parse($value);
        if ($until === null) {
            return false;
        }
        $now = $now ?? new \DateTimeImmutable();
        return $now > $until;
    }

    private static function parse(string $value): ?\DateTimeImmutable
    {
        $value = trim($value);
        $date = \DateTimeImmutable::createFromFormat(self::FORMAT, $value);
        if ($date === false || $date->format(self::FORMAT) !== $value) {
            return null;
        }
        return $date;
    }
}

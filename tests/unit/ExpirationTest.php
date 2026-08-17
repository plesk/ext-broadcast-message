<?php
// Copyright 1999-2026. WebPros International GmbH.

namespace tests\unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PleskExt\BroadcastMessage\Helper\Expiration;

class ExpirationTest extends TestCase
{
    #[DataProvider('getValidFormatDataProvider')]
    public function testIsValidFormat($value, $expected): void
    {
        $this->assertSame($expected, Expiration::isValidFormat($value));
    }

    public static function getValidFormatDataProvider(): iterable
    {
        yield 'null is allowed' => [null, true];
        yield 'empty string is allowed' => ['', true];
        yield 'whitespace only is allowed' => ['   ', true];
        yield 'valid date and time' => ['2026-12-31 23:59', true];
        yield 'valid date with surrounding spaces' => [' 2026-12-31 23:59 ', true];
        yield 'date without time' => ['2026-12-31', false];
        yield 'time without date' => ['23:59', false];
        yield 'wrong order' => ['31-12-2026 23:59', false];
        yield 'nonexistent day' => ['2026-02-30 10:00', false];
        yield 'nonexistent time' => ['2026-12-31 25:00', false];
        yield 'with seconds' => ['2026-12-31 23:59:59', false];
        yield 'arbitrary text' => ['tomorrow', false];
    }

    #[DataProvider('getExpiredDataProvider')]
    public function testIsExpired($value, $expected): void
    {
        $now = new \DateTimeImmutable('2026-08-17 12:00');
        $this->assertSame($expected, Expiration::isExpired($value, $now));
    }

    public static function getExpiredDataProvider(): iterable
    {
        yield 'null never expires' => [null, false];
        yield 'empty string never expires' => ['', false];
        yield 'invalid value never expires' => ['tomorrow', false];
        yield 'moment in the past' => ['2026-08-17 11:59', true];
        yield 'current moment' => ['2026-08-17 12:00', false];
        yield 'moment in the future' => ['2026-08-17 12:01', false];
        yield 'past date with surrounding spaces' => [' 2025-01-01 00:00 ', true];
    }
}

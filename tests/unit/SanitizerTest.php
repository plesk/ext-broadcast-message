<?php
// Copyright 1999-2026. WebPros International GmbH. All rights reserved.

namespace tests\unit;

use PHPUnit\Framework\TestCase;
use PleskExt\BroadcastMessage\Helper\Sanitizer;

class SanitizerTest extends TestCase
{
    /**
     * @dataProvider getDataProvider
     */
    public function testSanitize($initialText, $expected): void
    {
        $actual = Sanitizer::sanitize($initialText);
        $this->assertEquals($expected, $actual);
    }

    public static function getDataProvider(): iterable
    {
        yield [
            '<p>Hello <b>"world"</b></p>',
            '<p>Hello <b>&#34;world&#34;</b></p>',
        ];
        yield [
            '<div class="useless">Hello world</div>',
            '<div>Hello world</div>',
        ];
        yield [
            '<img src="https://example.com/pic.png" onerror="alert(1)">',
            '<img src="https://example.com/pic.png" />',
        ];
        yield [
            '<p>Test<script>alert("xss")</script></p>',
            '<p>Testalert(&#34;xss&#34;)</p>',
        ];
        yield [
            '<svg width="100" height="100"><rect x="10" y="10" width="30" height="30" fill="red"/></svg>',
            '<svg width="100" height="100"><rect x="10" y="10" width="30" height="30" fill="red"></rect></svg>',
        ];
        yield [
            '<svg onload="alert(1)"><script>alert(2)</script><circle cx="50" cy="50" r="10" fill="blue"/></svg>',
            '<svg>alert(2)<circle cx="50" cy="50" r="10" fill="blue"></circle></svg>',
        ];
        yield [
            '<svg><foreignObject><div>test</div></foreignObject></svg>',
            '<svg></svg>',
        ];
        yield [
            '<a href="javascript:alert(1)">click me</a>',
            '<a>click me</a>',
        ];
        yield [
            '<a href="https://example.com" title="link">click me</a>',
            '<a href="https://example.com" title="link">click me</a>',
        ];
        yield [
            '<a href="https://example.com" title="link" target="_blank">click me</a>',
            '<a href="https://example.com" title="link" target="_blank">click me</a>',
        ];
        yield [
            '<a href="https://example.com" title="link" target="_blank" rel="noopener noreferrer">click me</a>',
            '<a href="https://example.com" title="link" target="_blank" rel="noopener noreferrer">click me</a>',
        ];
        yield [
            '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAA" />', //onerror in data URI
            '<img />',
        ];
        yield [
            '<img src="data:image/svg+xml;base64,PHN2ZyBvbmVycm9yPSJhbGVydCgxKSI+PC9zdmc+" />', //onerror in data URI
            '<img />',
        ];
        yield [
            '<div style="color: red;">text</div>',
            '<div style="color: red">text</div>',
        ];
        yield [
            '<span style=\'width:100px; height:50px;\'>ok</span>',
            '<span style="width: 100px; height: 50px">ok</span>',
        ];
        yield [
            '<div style="background:url(javascript:alert(1))">bad</div>',
            '<div >bad</div>',
        ];
        yield [
            '<h2 title="Heading" style="text-align: center">Hi</h2>',
            '<h2 title="Heading" style="text-align: center">Hi</h2>',
        ];
    }
}

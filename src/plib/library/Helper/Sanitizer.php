<?php
// Copyright 1999-2025. WebPros International GmbH. All rights reserved.
namespace PleskExt\BroadcastMessage\Helper;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class Sanitizer
{
    private static ?HtmlSanitizer $sanitizer = null;

    private static array $allowedStyles = [
        'color',
        'background-color',
        'width',
        'height',
        'max-width',
        'max-height',
        'font-size',
        'font-weight',
        'text-align',
        'border',
        'border-radius',
        'margin',
        'padding',
    ];

    private static function getSanitizer(): HtmlSanitizer
    {
        if (!self::$sanitizer) {
            $config = (new HtmlSanitizerConfig())
                ->allowSafeElements()
                ->allowElement('div', ['style'])
                ->allowElement('p', ['style'])
                ->allowElement('span', ['style'])
                ->allowElement('img', ['style', 'src', 'alt', 'width', 'height'])
                ->allowElement('svg', ['viewBox', 'width', 'height', 'xmlns'])
                ->allowElement('path', ['d', 'fill', 'stroke'])
                ->allowElement('circle', ['cx', 'cy', 'r', 'fill', 'stroke'])
                ->allowElement('rect', ['x', 'y', 'width', 'height', 'fill', 'stroke'])
                ->allowElement('a', ['style', 'href', 'title'])
                ->allowLinkSchemes(['https', 'mailto'])
                ->allowMediaSchemes(['https'])
                ->blockElement('script')
                ->blockElement('foreignObject');
            self::$sanitizer = new HtmlSanitizer($config);
        }
        return self::$sanitizer;
    }

    private static function sanitizeStyles(string $message): string
    {
        foreach (['/style="([^"]*)"/i', "/style='([^']*)'/i"] as $pattern) {
            $message = preg_replace_callback(
                $pattern,
                function ($matches) {
                    $safe = [];
                    foreach (explode(';', $matches[1]) as $rule) {
                        if (!$rule) {
                            continue;
                        }
                        $ruleParts = explode(':', $rule);
                        if (count($ruleParts) !== 2) {
                            continue;
                        }
                        $prop = trim($ruleParts[0]);
                        $val = trim($ruleParts[1]);
                        if (in_array(strtolower($prop), self::$allowedStyles, true) &&
                            $val &&
                            !preg_match('/\b(url|expression|@import)\s*\(/i', $val)) {
                            $safe[] = "$prop: $val";
                        }
                    }
                    return $safe ? 'style="' . htmlspecialchars(implode('; ', $safe), ENT_QUOTES) . '"' : '';
                },
                $message
            );
        }

        return $message;
    }

    public static function sanitize(string $message): string
    {
        $message = self::getSanitizer()->sanitize($message);
        return self::sanitizeStyles($message);
    }
}

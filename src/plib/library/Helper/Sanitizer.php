<?php
// Copyright 1999-2026. WebPros International GmbH.
namespace PleskExt\BroadcastMessage\Helper;

class Sanitizer
{
    private const GLOBAL_ATTRIBUTES = ['style', 'title'];

    private const ALLOWED_ELEMENTS = [
        'a' => ['href', 'target', 'rel'],
        'abbr' => [],
        'b' => [],
        'blockquote' => [],
        'br' => [],
        'caption' => [],
        'code' => [],
        'dd' => [],
        'div' => [],
        'dl' => [],
        'dt' => [],
        'em' => [],
        'h1' => [],
        'h2' => [],
        'h3' => [],
        'h4' => [],
        'h5' => [],
        'h6' => [],
        'hr' => [],
        'i' => [],
        'img' => ['src', 'alt', 'width', 'height'],
        'li' => [],
        'ol' => ['type', 'start', 'reversed'],
        'p' => [],
        'pre' => [],
        's' => [],
        'small' => [],
        'span' => [],
        'strong' => [],
        'sub' => [],
        'sup' => [],
        'table' => [],
        'tbody' => [],
        'td' => ['colspan', 'rowspan'],
        'tfoot' => [],
        'th' => ['colspan', 'rowspan', 'scope'],
        'thead' => [],
        'tr' => [],
        'u' => [],
        'ul' => ['type'],
        'svg' => ['viewbox', 'width', 'height', 'xmlns'],
        'path' => ['d', 'fill', 'stroke'],
        'circle' => ['cx', 'cy', 'r', 'fill', 'stroke'],
        'rect' => ['x', 'y', 'width', 'height', 'fill', 'stroke'],
    ];

    private const DROPPED_ELEMENTS = [
        'style', 'iframe', 'frame', 'frameset', 'object', 'embed',
        'applet', 'template', 'foreignobject', 'head', 'title',
    ];

    private const VOID_ELEMENTS = ['br', 'hr', 'img'];

    private const ATTRIBUTE_CASE = ['viewbox' => 'viewBox'];

    private const URL_SCHEMES = [
        'href' => ['https', 'mailto'],
        'src' => ['https'],
    ];

    private const ALLOWED_STYLES = [
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

    public static function sanitize(string $message): string
    {
        $message = preg_replace('/<(?![a-zA-Z\/!?])/', '&lt;', $message);

        $document = new \DOMDocument();
        $useInternalErrors = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="UTF-8"><html><body>' . $message . '</body></html>',
            LIBXML_NONET
        );
        libxml_clear_errors();
        libxml_use_internal_errors($useInternalErrors);

        $body = $document->getElementsByTagName('body')->item(0);
        if (!$body) {
            return '';
        }

        $result = '';
        foreach ($body->childNodes as $node) {
            $result .= self::serializeNode($node);
        }
        return self::sanitizeStyles($result);
    }

    private static function serializeNode(\DOMNode $node): string
    {
        if ($node instanceof \DOMText) {
            return self::escape($node->nodeValue);
        }
        if (!$node instanceof \DOMElement) {
            return '';
        }

        $tag = strtolower($node->tagName);
        if (in_array($tag, self::DROPPED_ELEMENTS, true)) {
            return '';
        }

        $children = '';
        foreach ($node->childNodes as $child) {
            $children .= self::serializeNode($child);
        }

        if (!isset(self::ALLOWED_ELEMENTS[$tag])) {
            return $children;
        }

        $attributes = '';
        foreach ($node->attributes as $attribute) {
            $name = strtolower($attribute->name);
            if (!in_array($name, self::GLOBAL_ATTRIBUTES, true)
                && !in_array($name, self::ALLOWED_ELEMENTS[$tag], true)
            ) {
                continue;
            }
            $value = $attribute->value;
            if (isset(self::URL_SCHEMES[$name]) && !self::isUrlAllowed($value, self::URL_SCHEMES[$name])) {
                continue;
            }
            $name = self::ATTRIBUTE_CASE[$name] ?? $name;
            $attributes .= ' ' . $name . '="' . self::escape($value) . '"';
        }

        if (in_array($tag, self::VOID_ELEMENTS, true)) {
            return "<{$tag}{$attributes} />";
        }
        return "<{$tag}{$attributes}>{$children}</{$tag}>";
    }

    private static function escape(string $text): string
    {
        return str_replace(
            '&quot;',
            '&#34;',
            htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
        );
    }

    private static function isUrlAllowed(string $url, array $allowedSchemes): bool
    {
        if (!preg_match('/^([a-z][a-z0-9+.-]*):/i', trim($url), $matches)) {
            return false;
        }
        return in_array(strtolower($matches[1]), $allowedSchemes, true);
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
                        if (in_array(strtolower($prop), self::ALLOWED_STYLES, true) &&
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
}

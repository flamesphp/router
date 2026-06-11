<?php
declare(strict_types=1);


namespace Flames\Router\Parser;

final class UrlNormalizer
{
    public static function fromRequest(?string $requestUrl, string $basePath): string
    {
        $url = $requestUrl ?? ($_SERVER['REQUEST_URI'] ?? '/');
        $url = substr($url, strlen($basePath));

        return self::stripQuery($url);
    }

    public static function stripQuery(string $url): string
    {
        $position = strpos($url, '?');

        return $position === false ? $url : substr($url, 0, $position);
    }

    public static function lastChar(string $url): string
    {
        return $url === '' ? '' : $url[strlen($url) - 1];
    }
}

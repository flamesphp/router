<?php
declare(strict_types=1);


namespace Flames\Router\Parser;

final class RouteProbe
{
    public static function match(CompiledRoute $route, string $url, string $lastChar): array|false
    {
        return match ($route->kind) {
            RouteKind::WILDCARD => self::wildcard($route),
            RouteKind::RAW_REGEX => self::regex($route, $url),
            RouteKind::LITERAL => self::literal($route, $url),
            RouteKind::PARAMETRIC => self::parametric($route, $url, $lastChar),
            default => false,
        };
    }

    private static function wildcard(CompiledRoute $route): array
    {
        return self::hit($route, []);
    }

    private static function regex(CompiledRoute $route, string $url): array|false
    {
        return preg_match($route->regex, $url, $params) === 1
            ? self::hit($route, $params)
            : false;
    }

    private static function literal(CompiledRoute $route, string $url): array|false
    {
        return strcmp($url, $route->pattern) === 0
            ? self::hit($route, [])
            : false;
    }

    private static function parametric(CompiledRoute $route, string $url, string $lastChar): array|false
    {
        if (!self::prefixMatches($route, $url, $lastChar)) {
            return false;
        }

        return preg_match($route->regex, $url, $params) === 1
            ? self::hit($route, $params)
            : false;
    }

    private static function prefixMatches(CompiledRoute $route, string $url, string $lastChar): bool
    {
        if (strncmp($url, $route->pattern, $route->prefixLength) === 0) {
            return true;
        }

        return $lastChar !== '/' && $route->prefixEndsWithSlash;
    }

    private static function hit(CompiledRoute $route, array $params): array
    {
        return [
            'target' => $route->target,
            'params' => self::namedParams($params),
            'name' => $route->name,
        ];
    }

    private static function namedParams(array $params): array
    {
        foreach ($params as $key => $value) {
            if (is_numeric($key)) {
                unset($params[$key]);
            }
        }

        return $params;
    }
}

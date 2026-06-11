<?php
declare(strict_types=1);


namespace Flames\Router\Parser;

final class RouteCompiler
{
    /** @param array<string, string> $matchTypes */
    public static function compile(
        string $method,
        string $route,
        mixed $target,
        ?string $name,
        array $matchTypes,
    ): CompiledRoute {
        return match (true) {
            $route === '*' => self::wildcard($method, $route, $target, $name),
            isset($route[0]) && $route[0] === '@' => self::rawRegex($method, $route, $target, $name),
            !str_contains($route, '[') => self::literal($method, $route, $target, $name),
            default => self::parametric($method, $route, $target, $name, $matchTypes),
        };
    }

    private static function wildcard(string $method, string $route, mixed $target, ?string $name): CompiledRoute
    {
        return new CompiledRoute($method, $route, $target, $name, RouteKind::WILDCARD, null, 0, false);
    }

    private static function rawRegex(string $method, string $route, mixed $target, ?string $name): CompiledRoute
    {
        $regex = '`' . substr($route, 1) . '`u';

        return new CompiledRoute($method, $route, $target, $name, RouteKind::RAW_REGEX, $regex, 0, false);
    }

    private static function literal(string $method, string $route, mixed $target, ?string $name): CompiledRoute
    {
        return new CompiledRoute($method, $route, $target, $name, RouteKind::LITERAL, null, 0, false);
    }

    /** @param array<string, string> $matchTypes */
    private static function parametric(
        string $method,
        string $route,
        mixed $target,
        ?string $name,
        array $matchTypes,
    ): CompiledRoute {
        $prefixLength = strpos($route, '[') ?: 0;
        $endsWithSlash = $prefixLength > 0 && $route[$prefixLength - 1] === '/';
        $regex = PatternCompiler::toRegex($route, $matchTypes);

        return new CompiledRoute(
            $method,
            $route,
            $target,
            $name,
            RouteKind::PARAMETRIC,
            $regex,
            $prefixLength,
            $endsWithSlash,
        );
    }
}

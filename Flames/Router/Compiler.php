<?php
declare(strict_types=1);


namespace Flames\Router;

final class Compiler
{
    public static function compile(string $route): RouteBlueprint
    {
        $route = str_replace(' ', '', $route);

        return str_contains($route, '{')
            ? self::dynamic($route)
            : self::static($route);
    }

    private static function static(string $route): RouteBlueprint
    {
        $path = strtolower($route);

        return new RouteBlueprint($path, [], $path);
    }

    private static function dynamic(string $route): RouteBlueprint
    {
        $names = self::parameterNames($route);
        $path = self::caseInsensitivePath($route, $names);

        return new RouteBlueprint($path, $names, self::parserPath($path, $names));
    }

    /** @return list<string> */
    private static function parameterNames(string $route): array
    {
        preg_match_all('/\{([^{}]+)\}/', $route, $matches);

        return $matches[1] ?? [];
    }

    /** @param list<string> $names */
    private static function caseInsensitivePath(string $route, array $names): string
    {
        $tokens = [];
        $path = $route;

        foreach ($names as $index => $name) {
            $token = '{%' . $index . '%}';
            $tokens[$token] = '{' . $name . '}';
            $path = str_replace('{' . $name . '}', $token, $path);
        }

        $path = strtolower($path);

        foreach ($tokens as $token => $original) {
            $path = str_replace($token, $original, $path);
        }

        return $path;
    }

    /** @param list<string> $names */
    private static function parserPath(string $path, array $names): string
    {
        $parserPath = $path;

        foreach ($names as $index => $name) {
            $parserPath = str_replace(
                '{' . $name . '}',
                '[*:item' . ($index + 1) . 'item]',
                $parserPath,
            );
        }

        return $parserPath;
    }
}

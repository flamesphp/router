<?php
declare(strict_types=1);


namespace Flames\Router\Parser;

final class PatternCompiler
{
    private const TOKEN_PATTERN = '`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`';

    /** @param array<string, string> $matchTypes */
    public static function toRegex(string $route, array $matchTypes): string
    {
        if (!preg_match_all(self::TOKEN_PATTERN, $route, $matches, PREG_SET_ORDER)) {
            return self::wrap($route);
        }

        return self::wrap(self::injectTokens($route, $matches, $matchTypes));
    }

    /** @param list<array{0: string, 1: string, 2: string, 3: string, 4: string}> $matches */
    private static function injectTokens(string $route, array $matches, array $matchTypes): string
    {
        foreach ($matches as $match) {
            $route = str_replace($match[0], self::tokenPattern($match, $matchTypes), $route);
        }

        return $route;
    }

    /** @param array{0: string, 1: string, 2: string, 3: string, 4: string} $match */
    private static function tokenPattern(array $match, array $matchTypes): string
    {
        [$block, $pre, $type, $param, $optional] = $match;
        $type = $matchTypes[$type] ?? $type;
        $pre = $pre === '.' ? '\.' : $pre;
        $optionalFlag = $optional !== '' ? '?' : '';

        return '(?:' . $pre . '(' . ($param !== '' ? "?P<$param>" : '') . $type . ')' . $optionalFlag . ')' . $optionalFlag;
    }

    private static function wrap(string $route): string
    {
        return '`^' . $route . '$`u';
    }
}

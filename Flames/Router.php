<?php
declare(strict_types=1);


namespace Flames;

use Flames\Router\Compiler;
use Flames\Router\Matcher;
use Flames\Router\Registry;
use Flames\Router\RouteEntry;
use Flames\Router\RouteMatch;

final class Router
{
    private static ?Registry $registry = null;
    private static ?Matcher $matcher = null;

    private function __construct()
    {
    }

    public static function clear(): void
    {
        self::registry()->clear();
    }

    public static function hasRoutes(): bool
    {
        return self::registry()->hasRoutes();
    }

    public static function add(string $method = 'GET', string $route = '/', ?string $controller = null): void
    {
        if ($controller === null || $controller === '') {
            return;
        }

        $blueprint = Compiler::compile($route);
        $index = count(self::registry()->routes());

        self::registry()->add(new RouteEntry(
            strtoupper($method),
            $blueprint->path,
            $controller,
            $blueprint->parameterNames,
            $blueprint->parserPath,
            $index,
        ));
    }

    public static function getMatch(): ?RouteMatch
    {
        return self::matcher()->match();
    }

    /** @return list<RouteEntry> */
    public static function getMetadata(): array
    {
        return self::registry()->routes();
    }

    private static function registry(): Registry
    {
        return self::$registry ??= new Registry();
    }

    private static function matcher(): Matcher
    {
        return self::$matcher ??= new Matcher(self::registry());
    }
}

<?php
declare(strict_types=1);


namespace Flames\Router\Parser;

final class MethodMatcher
{
    public static function allows(string $methods, string $requestMethod): bool
    {
        return stripos($methods, $requestMethod) !== false;
    }

    public static function fromRequest(?string $requestMethod): string
    {
        return $requestMethod ?? ($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }
}

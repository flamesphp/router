<?php
declare(strict_types=1);


namespace Flames\Router;

final class ParameterExtractor
{
    /** @param array<string, mixed> $parserParams */
    public static function from(RouteEntry $entry, array $parserParams): array
    {
        $parameters = [];

        foreach ($entry->parameterNames as $index => $name) {
            $key = 'item' . ($index + 1) . 'item';
            if (isset($parserParams[$key])) {
                $parameters[$name] = (string) $parserParams[$key];
            }
        }

        return $parameters;
    }
}

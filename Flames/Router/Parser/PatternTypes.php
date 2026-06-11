<?php
declare(strict_types=1);


namespace Flames\Router\Parser;

final class PatternTypes
{
    /** @var array<string, string> */
    public const DEFAULT = [
        'i'  => '[0-9]++',
        'a'  => '[0-9A-Za-z]++',
        'h'  => '[0-9A-Fa-f]++',
        '*'  => '.+?',
        '**' => '.++',
        ''   => '[^/\.]++',
    ];

    /** @param array<string, string> $custom */
    public static function merge(array $custom): array
    {
        return $custom === [] ? self::DEFAULT : array_merge(self::DEFAULT, $custom);
    }
}

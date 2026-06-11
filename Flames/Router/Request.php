<?php
declare(strict_types=1);


namespace Flames\Router;

final readonly class Request
{
    public function __construct(
        public string $url,
        public string $path,
        public string $method,
    ) {
    }

    public static function fromServer(): self
    {
        $url = $_SERVER['REQUEST_URI'] ?? '/';
        $path = self::stripQuery($url);
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        return new self($url, $path, $method);
    }

    private static function stripQuery(string $url): string
    {
        $position = strpos($url, '?');

        return $position === false
            ? $url
            : substr($url, 0, $position);
    }
}

<?php
declare(strict_types=1);


namespace Flames\Router;

final readonly class RouteEntry
{
    /** @param list<string> $parameterNames */
    public function __construct(
        public string $methods,
        public string $routeFormatted,
        public string $controller,
        public array $parameterNames,
        public string $parserPath,
        public int $index,
    ) {
    }

    public function isDynamic(): bool
    {
        return $this->parameterNames !== [];
    }
}

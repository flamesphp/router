<?php
declare(strict_types=1);


namespace Flames\Router;

final readonly class RouteMatch
{
    /**
     * @param array<string, string> $parameters
     */
    public function __construct(
        public ?string $url,
        public ?string $command,
        public string $controller,
        public array $parameters,
    ) {
    }
}

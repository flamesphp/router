<?php
declare(strict_types=1);


namespace Flames\Router;

final readonly class RouteBlueprint
{
    /** @param list<string> $parameterNames */
    public function __construct(
        public string $path,
        public array $parameterNames,
        public string $parserPath,
    ) {
    }
}

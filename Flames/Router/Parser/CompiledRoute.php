<?php
declare(strict_types=1);


namespace Flames\Router\Parser;

final readonly class CompiledRoute
{
    public function __construct(
        public string $methods,
        public string $pattern,
        public mixed $target,
        public ?string $name,
        public int $kind,
        public ?string $regex,
        public int $prefixLength,
        public bool $prefixEndsWithSlash,
    ) {
    }
}

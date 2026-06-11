<?php
declare(strict_types=1);


namespace Flames\Router;

final class Registry
{
    /** @var list<RouteEntry> */
    private array $routes = [];

    /** @var array<string, array<string, int>> */
    private array $staticIndex = [];

    private ?Parser $parser = null;

    public function clear(): void
    {
        $this->routes = [];
        $this->staticIndex = [];
        $this->parser = null;
    }

    public function hasRoutes(): bool
    {
        return $this->routes !== [];
    }

    /** @return list<RouteEntry> */
    public function routes(): array
    {
        return $this->routes;
    }

    public function add(RouteEntry $entry): void
    {
        $this->routes[] = $entry;
        $this->parser = null;
        $this->indexStaticRoute($entry);
    }

    private function indexStaticRoute(RouteEntry $entry): void
    {
        if ($entry->parameterNames !== []) {
            return;
        }

        $this->staticIndex[$entry->methods][$entry->routeFormatted] = $entry->index;
    }

    public function findStatic(string $method, string $path): ?RouteEntry
    {
        $index = $this->staticIndex[$method][strtolower($path)] ?? null;

        return $index === null ? null : $this->routes[$index];
    }

    public function parser(): Parser
    {
        return $this->parser ??= $this->buildParser();
    }

    private function buildParser(): Parser
    {
        $parser = new Parser();

        foreach ($this->routes as $entry) {
            if ($entry->isDynamic()) {
                $parser->map($entry->methods, $entry->parserPath, null, (string) $entry->index);
            }
        }

        return $parser;
    }
}

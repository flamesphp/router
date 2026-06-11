<?php
declare(strict_types=1);


namespace Flames\Router;

use Flames\Router\Parser\CompiledRoute;
use Flames\Router\Parser\MethodMatcher;
use Flames\Router\Parser\RouteCompiler;
use Flames\Router\Parser\RouteProbe;
use Flames\Router\Parser\PatternTypes;
use Flames\Router\Parser\UrlNormalizer;

/**
 * @internal
 */
final class Parser
{
    /** @var list<CompiledRoute> */
    private array $routes = [];

    private string $basePath = '';

    /** @var array<string, string> */
    private array $matchTypes = PatternTypes::DEFAULT;

    public function setBasePath(string $basePath): void
    {
        $this->basePath = $basePath;
    }

    /** @param array<string, string> $matchTypes */
    public function addMatchTypes(array $matchTypes): void
    {
        $this->matchTypes = PatternTypes::merge($matchTypes);
    }

    public function map(string $method, string $route, mixed $target, ?string $name = null): void
    {
        $this->routes[] = RouteCompiler::compile($method, $route, $target, $name, $this->matchTypes);
    }

    public function match(?string $requestUrl = null, ?string $requestMethod = null): array|false
    {
        $url = UrlNormalizer::fromRequest($requestUrl, $this->basePath);
        $method = MethodMatcher::fromRequest($requestMethod);
        $lastChar = UrlNormalizer::lastChar($url);

        foreach ($this->routes as $route) {
            if (!MethodMatcher::allows($route->methods, $method)) {
                continue;
            }

            $hit = RouteProbe::match($route, $url, $lastChar);

            if ($hit !== false) {
                return $hit;
            }
        }

        return false;
    }
}

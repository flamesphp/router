<?php
declare(strict_types=1);


namespace Flames\Router;

final readonly class Matcher
{
    public function __construct(private Registry $registry)
    {
    }

    public function match(): ?RouteMatch
    {
        return \Flames\Forge\Cli::isCli()
            ? $this->matchCli()
            : $this->matchWeb();
    }

    private function matchWeb(): ?RouteMatch
    {
        $request = Request::fromServer();
        $static = $this->registry->findStatic($request->method, $request->path);

        if ($static !== null) {
            return self::result($request->url, null, $static, []);
        }

        return $this->matchDynamic($request);
    }

    private function matchDynamic(Request $request): ?RouteMatch
    {
        $hit = $this->registry->parser()->match($request->url, $request->method);

        if ($hit === false) {
            return null;
        }

        $entry = $this->registry->routes()[(int) $hit['name']];
        $params = ParameterExtractor::from($entry, $hit['params']);

        return self::result($request->url, null, $entry, $params);
    }

    private function matchCli(): ?RouteMatch
    {
        $parts = [];

        foreach (array_slice($_SERVER['argv'], 1) as $arg) {
            if (str_starts_with($arg, '-')) {
                break;
            }

            $parts[] = $arg;
        }

        if ($parts === []) {
            return null;
        }

        $command = strtolower(implode(' ', $parts));

        foreach ($this->registry->routes() as $entry) {
            if ($entry->methods === 'CLI' && !$entry->isDynamic() && $entry->routeFormatted === $command) {
                return self::result(null, $command, $entry, []);
            }
        }

        $hit = $this->registry->parser()->match($command, 'CLI');

        if ($hit === false) {
            return null;
        }

        $entry = $this->registry->routes()[(int) $hit['name']];

        if ($entry->methods !== 'CLI') {
            return null;
        }

        return self::result(
            null,
            $command,
            $entry,
            ParameterExtractor::from($entry, $hit['params']),
        );
    }

    private static function result(?string $url, ?string $command, RouteEntry $entry, array $params): RouteMatch
    {
        return new RouteMatch($url, $command, $entry->controller, $params);
    }
}

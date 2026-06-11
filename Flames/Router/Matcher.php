<?php
declare(strict_types=1);


namespace Flames\Router;

final class Matcher
{
    public function __construct(private readonly Registry $registry)
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
        $command = ($_SERVER['argv'][1] ?? null);

        if ($command === null) {
            return null;
        }

        foreach ($this->registry->routes() as $entry) {
            if ($entry->methods === 'CLI' && $entry->routeFormatted === $command) {
                return self::result(null, $command, $entry, []);
            }
        }

        return null;
    }

    private static function result(?string $url, ?string $command, RouteEntry $entry, array $params): RouteMatch
    {
        return new RouteMatch($url, $command, $entry->controller, $params);
    }
}

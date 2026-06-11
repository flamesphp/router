<?php
declare(strict_types=1);


namespace Flames\Router\Parser;

final class RouteKind
{
    public const WILDCARD = 0;
    public const RAW_REGEX = 1;
    public const LITERAL = 2;
    public const PARAMETRIC = 3;
}

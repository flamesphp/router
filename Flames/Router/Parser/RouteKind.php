<?php
declare(strict_types=1);


namespace Flames\Router\Parser;

final class RouteKind
{
    public const int WILDCARD = 0;
    public const int RAW_REGEX = 1;
    public const int LITERAL = 2;
    public const int PARAMETRIC = 3;
}

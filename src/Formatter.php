<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\getStylish;
use function Differ\Formatters\Plain\getPlain;
use function Differ\Formatters\Json\getJson;

function format(mixed $diffTree, string $format): string
{
    return match ($format) {
        "stylish" => getStylish($diffTree),
        "plain" => getPlain($diffTree),
        "json" => getJson($diffTree),
        default => throw new \Exception("Unknown format - $format"),
    };
}

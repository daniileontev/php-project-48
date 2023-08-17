<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\getStylish;
use function Differ\Formatters\Plain\getPlain;
use function Differ\Formatters\Json\getJson;

function getFormat(mixed $diff, string $format): string
{
    return match ($format) {
        "stylish" => getStylish($diff),
        "plain" => getPlain($diff),
        "json" => getJson($diff),
        default => throw new \Exception("Unknown format - " . $format),
    };
}

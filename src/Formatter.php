<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\getStylish;
use function Differ\Formatters\Plain\getPlain;

function getFormat(mixed $diff, string $format): string
{
    return match ($format) {
        "stylish" => getStylish($diff),
        "plain" => getPlain($diff),
        default => throw new \Exception("Unknown format - " . $format),
    };
}

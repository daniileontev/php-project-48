<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function getParseCode(string $dataFile, string $extension): mixed
{
    return match ($extension) {
        'json' => json_decode($dataFile, true),
        'yml', 'yaml' => Yaml::parse($dataFile),
        default => throw new \Exception('Unknown extension ' . $extension),
    };
}

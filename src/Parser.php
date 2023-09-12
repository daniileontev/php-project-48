<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function getParseCode(mixed $fileData, string $format): mixed
{
    return match ($format) {
        'json' => json_decode($fileData, true),
        'yml', 'yaml' => Yaml::parse($fileData),
        default => throw new \Exception("Unknown extension - $format"),
    };
}

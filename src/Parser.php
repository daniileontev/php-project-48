<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function getParseCode(string $dataFile, string $extension): mixed
{
    switch ($extension) {
        case 'json':
            return json_decode($dataFile, true);
        case 'yml':
        case 'yaml':
            return Yaml::parse($dataFile);
        default:
            throw new \Exception('Unknown extension ' . $extension);
    }
}

<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function isBool(array $array): array
{
    return array_map(function ($value) {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            return 'null';
        } else {
            return $value;
        }
    }, $array);
}

function getRealPath(string $pathToFile): string
{
    $fullPath = realpath($pathToFile);
    if ($fullPath === false) {
        throw new \Exception("File does not exists");
    }
    return $fullPath;
}

function getExtension(string $pathToFile): string
{
    $fullPath = getRealPath($pathToFile);
    return pathinfo($fullPath, PATHINFO_EXTENSION);
}

function getDataFile(string $pathToFile): mixed
{
    $fullPath = getRealPath($pathToFile);
    return file_get_contents($fullPath);
}


function getData(string $pathToFile): mixed
{
    $extension = getExtension($pathToFile);
    $dataFile = getDataFile($pathToFile);
    return isBool(getParseCode($dataFile, $extension));
}

function getParseCode(string $dataFile, string $extension): mixed
{
    return match ($extension) {
        'json' => json_decode($dataFile, true),
        'yml', 'yaml' => Yaml::parse($dataFile),
        default => throw new \Exception('Unknown extension ' . $extension),
    };
}

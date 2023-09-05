<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function getRealPath(string $pathToFile): string
{
    $fullPath = realpath($pathToFile);
    if ($fullPath === false) {
        throw new \Exception("File does not exists");
    }
    return $fullPath;
}

function getDataFile(string $pathToFile): string|bool
{
    $fullPath = getRealPath($pathToFile);
    return file_get_contents($fullPath);
}


function getParseCode(string $pathToFile)
{
    $fileData = getDataFile($pathToFile);
    $extension = pathinfo($pathToFile, PATHINFO_EXTENSION);
    return match ($extension) {
        'json' => json_decode($fileData, true),
        'yml', 'yaml' => Yaml::parse($fileData),
        default => throw new \Exception('Unknown extension ' . $extension),
    };
}

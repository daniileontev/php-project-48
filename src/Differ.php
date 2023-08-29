<?php

namespace Differ\Differ;

use function Differ\Formatters\getFormat;
use function Differ\Parser\getParseCode;

function convertValueToString(array $array): array
{
    return array_map(function ($value) {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            return 'null';
        } elseif (is_array($value)) {
            return convertValueToString($value);
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

function getDataFile(string $pathToFile): string|bool
{
    $fullPath = getRealPath($pathToFile);
    return file_get_contents($fullPath);
}


function getData(string $pathToFile): array
{
    $extension = getExtension($pathToFile);
    $dataFile = getDataFile($pathToFile);
    return convertValueToString(getParseCode($dataFile, $extension));
}

function buildDiffTree(array $data1, array $data2): array
{
    $tree = [];
    $keys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    sort($keys);

    foreach ($keys as $key) {
        $existingArr1 = array_key_exists($key, $data1);
        $existingArr2 = array_key_exists($key, $data2);
        if ($existingArr1 && $existingArr2 && is_array($data1[$key]) && is_array($data2[$key])) {
            $comparison = buildDiffTree($data1[$key], $data2[$key]);
            $tree[] = [
                'key' => $key,
                'type' => 'nested',
                'value1' => $comparison,
                'value2' => $comparison
            ];
        } elseif (!array_key_exists($key, $data2)) {
            $tree[] = [
                'key' => $key,
                'type' => 'removed',
                'value1' => $data1[$key],
                'value2' => null
            ];
        } elseif (!array_key_exists($key, $data1)) {
            $tree[] = [
                'key' => $key,
                'type' => 'added',
                'value1' => null,
                'value2' => $data2[$key]
            ];
        } elseif ($data1[$key] !== $data2[$key]) {
            $tree[] = [
                'key' => $key,
                'type' => 'updated',
                'value1' => $data1[$key],
                'value2' => $data2[$key]
            ];
        } else {
            $tree[] = [
                'key' => $key,
                'type' => 'unchanged',
                'value1' => $data1[$key],
                'value2' => $data2[$key]
            ];
        }
    }
    return $tree;
}

function genDiff(string $pathToFile1, string $pathToFile2, $format = "stylish"): string
{
    $fileData1 = getData($pathToFile1);
    $fileData2 = getData($pathToFile2);
    $diff = buildDiffTree($fileData1, $fileData2);
    return getFormat($diff, $format);
}

<?php

namespace Differ\Differ;

use function Differ\Formatters\format;
use function Differ\Parser\getParseCode;
use function Functional\sort;

function buildDiffTree(array $data1, array $data2): array
{
    $data1Keys = array_keys($data1);
    $data2Keys = array_keys($data2);
    $mergedKeys = array_merge($data1Keys, $data2Keys);
    $uniqueKeys = array_unique($mergedKeys);
    $sortedKeys = sort($uniqueKeys, fn ($left, $right) => strcmp($left, $right));

    return array_map(function ($key) use ($data1, $data2) {
        $value = $data1[$key] ?? null;
        $value2 = $data2[$key] ?? null;

        if (is_array($value) && is_array($value2)) {
            return [
                'key' => $key,
                'type' => 'nested',
                'value' => buildDiffTree($value, $value2),
            ];
        }

        if (!array_key_exists($key, $data1)) {
            return [
                'key' => $key,
                'type' => 'added',
                'value' => $value2,
            ];
        }

        if (!array_key_exists($key, $data2)) {
            return [
                'key' => $key,
                'type' => 'removed',
                'value' => $value,
            ];
        }

        if ($value === $value2) {
            return [
                'key' => $key,
                'type' => 'unchanged',
                'value' => $value,
            ];
        }

        return [
            'key' => $key,
            'type' => 'updated',
            'value' => $value,
            'value2' => $value2
        ];
    }, $sortedKeys);
}

function getFileData(string $pathToFile): string
{
    $fileData = file_get_contents($pathToFile);
    if ($fileData === false) {
        throw new \Exception("Can't read file");
    }
    return $fileData;
}

function genDiff(string $pathToFile1, string $pathToFile2, string $format = "stylish"): string
{
    $rawData1 = getFileData($pathToFile1);
    $fileExtension1 = pathinfo($pathToFile1, PATHINFO_EXTENSION);
    $rawData2 = getFileData($pathToFile2);
    $fileExtension2 = pathinfo($pathToFile2, PATHINFO_EXTENSION);

    $fileData1 = getParseCode($rawData1, $fileExtension1);
    $fileData2 = getParseCode($rawData2, $fileExtension2);
    $diffTree = buildDiffTree($fileData1, $fileData2);
    return format($diffTree, $format);
}

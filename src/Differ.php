<?php

namespace Differ\Differ;

use function Differ\Formatters\getFormat;
use function Differ\Parser\getParseCode;
use function Functional\sort;

function makeSortedKeys(array $data1, array $data2): array
{
    $data1Keys = array_keys($data1);
    $data2Keys = array_keys($data2);
    $mergedKeys = array_merge($data1Keys, $data2Keys);
    $uniqueKeys = array_unique($mergedKeys);
    return sort($uniqueKeys, fn($left, $right) => strcmp($left, $right));
}

function buildDiffTree(array $data1, array $data2): array
{
    $sortedKeys = makeSortedKeys($data1, $data2);

    $tree = function ($key) use ($data1, $data2) {
        $value1 = $data1[$key] ?? null;
        $value2 = $data2[$key] ?? null;

        if (is_array($value1) && is_array($value2)) {
            return [
                'key' => $key,
                'type' => 'nested',
                'value1' => buildDiffTree($value1, $value2),
                'value2' => null
            ];
        }

        if (!array_key_exists($key, $data1)) {
            return [
                'key' => $key,
                'type' => 'added',
                'value1' => $value2,
                'value2' => null
            ];
        }

        if (!array_key_exists($key, $data2)) {
            return [
                'key' => $key,
                'type' => 'removed',
                'value1' => $value1,
                'value2' => null
            ];
        }

        if ($value1 !== $value2) {
            return [
                'key' => $key,
                'type' => 'updated',
                'value1' => $value1,
                'value2' => $value2
            ];
        }

        return [
            'key' => $key,
            'type' => 'unchanged',
            'value1' => $value1,
            'value2' => null
        ];
    };
    return array_map($tree, $sortedKeys);
}

function genDiff(string $pathToFile1, string $pathToFile2, string $format = "stylish"): string
{
    $fileData1 = getParseCode($pathToFile1);
    $fileData2 = getParseCode($pathToFile2);
    $diffTree = buildDiffTree($fileData1, $fileData2);
    return getFormat($diffTree, $format);
}

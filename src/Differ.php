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
    $sortedKeys = sort($uniqueKeys, fn($left, $right) => strcmp($left, $right));

    $tree = function ($key) use ($data1, $data2) {
        $value1 = $data1[$key] ?? null;
        $value2 = $data2[$key] ?? null;

        if (is_array($value1) && is_array($value2)) {
            return [
                'key' => $key,
                'type' => 'nested',
                'value1' => buildDiffTree($value1, $value2),
            ];
        }

        if (!array_key_exists($key, $data1)) {
            return [
                'key' => $key,
                'type' => 'added',
                'value1' => $value2,
            ];
        }

        if (!array_key_exists($key, $data2)) {
            return [
                'key' => $key,
                'type' => 'removed',
                'value1' => $value1,
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
        ];
    };
    return array_map($tree, $sortedKeys);
}


function getDataFile(string $pathToFile): string
{
    $fileData = file_get_contents($pathToFile);
    if ($fileData === false) {
        throw new \Exception("Can't read file");
    }
    return $fileData;
}

function genDiff(string $pathToFile1, string $pathToFile2, string $format = "stylish"): string
{
    $fileData1 = getParseCode(getDataFile($pathToFile1), pathinfo($pathToFile1, PATHINFO_EXTENSION));
    $fileData2 = getParseCode(getDataFile($pathToFile2), pathinfo($pathToFile2, PATHINFO_EXTENSION));
    $diffTree = buildDiffTree($fileData1, $fileData2);
    return format($diffTree, $format);
}

<?php

namespace Differ\Differ;

use function Differ\Formatters\getFormat;
use function Differ\Parser\getData;

function compareArrays(array $arr1, array $arr2): array
{
    $result = [];
    $keys = array_unique(array_merge(array_keys($arr1), array_keys($arr2)));
    sort($keys);

    foreach ($keys as $key) {
        $existingArr1 = array_key_exists($key, $arr1);
        $existingArr2 = array_key_exists($key, $arr2);
        if ($existingArr1 && $existingArr2 && is_array($arr1[$key]) && is_array($arr2[$key])) {
            $comparison = compareArrays($arr1[$key], $arr2[$key]);
            $result[] = [
                'key' => $key,
                'type' => 'nested',
                'value1' => $comparison,
                'value2' => $comparison
            ];
        } elseif (!array_key_exists($key, $arr2)) {
            $result[] = [
                'key' => $key,
                'type' => 'removed',
                'value1' => $arr1[$key],
                'value2' => null
            ];
        } elseif (!array_key_exists($key, $arr1)) {
            $result[] = [
                'key' => $key,
                'type' => 'added',
                'value1' => null,
                'value2' => $arr2[$key]
            ];
        } elseif ($arr1[$key] !== $arr2[$key]) {
            $result[] = [
                'key' => $key,
                'type' => 'updated',
                'value1' => $arr1[$key],
                'value2' => $arr2[$key]
            ];
        } else {
            $result[] = [
                'key' => $key,
                'type' => 'unchanged',
                'value1' => $arr1[$key],
                'value2' => $arr2[$key]
            ];
        }
    }
    return $result;
}

function genDiff(string $pathToFile1, string $pathToFile2, $format = "stylish"): string
{
    $fileData1 = getData($pathToFile1);
    $fileData2 = getData($pathToFile2);
    $diff = compareArrays($fileData1, $fileData2);
    return getFormat($diff, $format);
}

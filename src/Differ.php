<?php

namespace Differ\Differ;

use function Differ\Parser\getData;
use function Differ\Formatters\Stylish\stringify;

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
            if (!empty($comparison)) {
                $result['    ' . $key] = $comparison;
            }
        } elseif (!array_key_exists($key, $arr2)) {
            $result['  - ' . $key] = $arr1[$key];
        } elseif (!array_key_exists($key, $arr1)) {
            $result['  + ' . $key] = $arr2[$key];
        } elseif ($arr1[$key] !== $arr2[$key]) {
            $result['  - ' . $key] = $arr1[$key];
            $result['  + ' . $key] = $arr2[$key];
        } else {
            $result['    ' . $key] = $arr1[$key];
        }
    }
    return $result;
}

function genDiff(string $pathToFile1, string $pathToFile2, $format = "stylish"): string
{
    $fileData1 = getData($pathToFile1);
    $fileData2 = getData($pathToFile2);
    $diff = compareArrays($fileData1, $fileData2);
    return "{\n" . stringify($diff) . "}";
}

<?php

namespace Differ\Differ;

use Exception;

use function Differ\Parser\getData;
use function Differ\Parser\getParseCode;

function compareArrays(array $arr1, array $arr2): array
{
    $result = [];
    $keys = array_merge((array_keys($arr1)), array_keys($arr2));
    sort($keys);

    foreach ($keys as $key) {
        if (array_key_exists($key, $arr1) && array_key_exists($key, $arr2)
            && is_array($arr1[$key]) && is_array($arr2[$key])) {
            $comparison = compareArrays($arr1[$key], $arr2[$key]);
            $result[' '] = $comparison;
        } elseif (!array_key_exists($key, $arr2)) {
            $result['- ' . $key] = $arr1[$key];
        } elseif (!array_key_exists($key, $arr1)) {
            $result['+ ' . $key] = $arr2[$key];
        } elseif ($arr1[$key] !== $arr2[$key]) {
            $result['- ' . $key] = $arr1[$key];
            $result['+ ' . $key] = $arr2[$key];
        } else {
            $result['- ' . $key] = $arr1[$key];
        }
    }
    return $result;
}

function toString($value): string
{
    return trim(var_export($value, true), "'");
}

function stringify($value, string $replacer = ' ', int $spacesCount = 4): string
{
    $iter = function ($currentValue, $depth) use (&$iter, $replacer, $spacesCount) {
        if (!is_array($currentValue)) {
            return toString($currentValue);
        }

        $indentSize = $depth * $spacesCount;
        $currentIndent = str_repeat($replacer, $indentSize);
        $bracketIndent = str_repeat($replacer, $indentSize - $spacesCount);

        $lines = array_map(
            fn($key, $val) => "{$currentIndent}{$key}: {$iter($val, $depth + 1)}",
            array_keys($currentValue),
            $currentValue
        );

        $result = ['{', ...$lines, "{$bracketIndent}}"];

        return implode("\n", $result);
    };

    return $iter($value, 1);
}


function genDiff(string $pathToFile1, string $pathToFile2): string
{
    $fileData1 = getData($pathToFile1);
//    echo "first file: \n";
//    var_dump($fileData1);

    $fileData2 = getData($pathToFile2);
//    echo "second file: \n";
//    var_dump($fileData2);
    $diff = compareArrays($fileData1, $fileData2);
//    echo "Result: \n";
    return stringify($diff, $replacer = ' ', $spaceCount = 4);
}

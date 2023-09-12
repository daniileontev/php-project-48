<?php

namespace Differ\Formatters\Stylish;

function formatToStringFromDiffTree(array $diffTree, int $depth = 0): array
{
    $spaces = str_repeat("    ", $depth);
    $depthOfDepth = $depth + 1;
    return array_map(function ($node) use ($spaces, $depthOfDepth) {
        $key = $node['key'];
        $type = $node['type'];
        $value1 = $node['value1'] ?? null;
        $value2 = $node['value2'] ?? null;

        switch ($type) {
            case 'nested':
                $nested = formatToStringFromDiffTree($value1, $depthOfDepth);
                $stringifiedNest = implode("\n", $nested);
                return "{$spaces}    {$key}: {\n{$stringifiedNest}\n{$spaces}    }";
            case 'unchanged':
                $stringifiedValue1 = valueToString($value1, $depthOfDepth);
                return "{$spaces}    {$key}: {$stringifiedValue1}";
            case 'added':
                $stringifiedValue1 = valueToString($value1, $depthOfDepth);
                return "{$spaces}  + {$key}: {$stringifiedValue1}";
            case 'removed':
                $stringifiedValue1 = valueToString($value1, $depthOfDepth);
                return "{$spaces}  - {$key}: {$stringifiedValue1}";
            case 'updated':
                $stringifiedValue1 = valueToString($value1, $depthOfDepth);
                $stringifiedValue2 = valueToString($value2, $depthOfDepth);
                return "{$spaces}  - {$key}: {$stringifiedValue1}\n{$spaces}  + {$key}: {$stringifiedValue2}";
            default:
                throw new \Exception("Unknown type - $type");
        }
    }, $diffTree);
}

function valueToString(mixed $value, int $depth): string
{
    if (is_null($value)) {
        return 'null';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_array($value)) {
        $result = convertArrayToString($value, $depth);
        $spaces = str_repeat("    ", $depth);
        return "{{$result}\n{$spaces}}";
    }
    return "$value";
}

function convertArrayToString(array $value, int $depth): string
{
    $keys = array_keys($value);
    $depthOfDepth = $depth + 1;

    return implode('', array_map(function ($key) use ($value, $depthOfDepth) {
        $newValue = valueToString($value[$key], $depthOfDepth);
        $spaces = str_repeat("    ", $depthOfDepth);

        return "\n$spaces$key: $newValue";
    }, $keys));
}


function getStylish(array $diffTree): string
{
    $formattedDiff = formatToStringFromDiffTree($diffTree);
    $result = implode("\n", $formattedDiff);

    return "{\n$result\n}";
}

<?php

namespace Differ\Formatters\Stylish;

function formatToStringFromDiffTree(array $diffTree, int $depth = 0): array
{
    $indent = buildIndent($depth);
    $depthOfDepth = $depth + 1;
    return array_map(function ($node) use ($indent, $depthOfDepth) {
        $key = $node['key'];
        $type = $node['type'];
        $value = $node['value'];

        switch ($type) {
            case 'nested':
                $nested = formatToStringFromDiffTree($value, $depthOfDepth);
                $stringifiedNest = implode("\n", $nested);
                return "{$indent}    {$key}: {\n{$stringifiedNest}\n{$indent}    }";
            case 'unchanged':
                $stringifiedValue = valueToString($value, $depthOfDepth);
                return "{$indent}    {$key}: {$stringifiedValue}";
            case 'added':
                $stringifiedValue = valueToString($value, $depthOfDepth);
                return "{$indent}  + {$key}: {$stringifiedValue}";
            case 'removed':
                $stringifiedValue = valueToString($value, $depthOfDepth);
                return "{$indent}  - {$key}: {$stringifiedValue}";
            case 'updated':
                $stringifiedValue = valueToString($value, $depthOfDepth);
                $stringifiedValue2 = valueToString($node['value2'], $depthOfDepth);
                return "{$indent}  - {$key}: {$stringifiedValue}\n{$indent}  + {$key}: {$stringifiedValue2}";
            default:
                throw new \Exception("Unknown type - $type");
        }
    }, $diffTree);
}

function buildIndent(int $depth)
{
    return str_repeat("    ", $depth);
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
        $spaces = buildIndent($depth);
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
        $spaces = buildIndent($depthOfDepth);

        return "\n$spaces$key: $newValue";
    }, $keys));
}


function getStylish(array $diffTree): string
{
    $formattedDiff = formatToStringFromDiffTree($diffTree);
    $result = implode("\n", $formattedDiff);

    return "{\n$result\n}";
}

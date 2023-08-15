<?php

namespace Differ\Formatters\Stylish;

function toString(mixed $value): string
{
    if ($value === null) {
        return 'null';
    }
    return trim(var_export($value, true), "'");
}
function stringify(array $arr, int $depth = 0): string
{
    $indent = str_repeat(" ", $depth * 4);
    $bracketIndent = str_repeat(' ', $depth * 4 + 4);
    $result = "";
    foreach ($arr as $key => $value) {
        if (is_array($value)) {
            $result .= $indent . formatKey($key) . ": {\n";
            $result .= stringify($value, $depth + 1);
            $result .= $bracketIndent . "}\n";
        } elseif ($value === '') {
            $result .= $indent . formatKey($key) . ":" . "\n";
        } else {
            $result .= $indent . formatKey($key) . ": " . toString($value) . "\n";
        }
    }

    return $result;
}
function formatKey(string $key): string
{
    if (str_starts_with($key, '  + ')) {
        return $key;
    } elseif (str_starts_with($key, '  - ')) {
        return $key;
    } elseif (str_starts_with($key, '    ')) {
        return $key;
    }
    return '    ' . $key;
}

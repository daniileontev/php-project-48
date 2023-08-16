<?php

namespace Differ\Formatters\Stylish;

function toString(mixed $value): string
{
    if ($value === null) {
        return 'null';
    }
    return trim(var_export($value, true), "'");
}


function getStylish(mixed $value, string $replacer = ' ', int $spaceCount = 4): string
{
    if (!is_array($value)) {
        return toString($value);
    }

    $iter = function ($currentValue, $depth) use (&$iter, $replacer, $spaceCount) {

        if (!is_array($currentValue)) {
            return toString($currentValue);
        }

        $indentLength = $spaceCount * $depth;
        $shiftToLeft = 2;
        $indentForUnchanged = str_repeat($replacer, $indentLength);
        $indentForChanged = str_repeat($replacer, $indentLength - $shiftToLeft);
        $bracketIndent = str_repeat($replacer, $indentLength - $spaceCount);

        $line = array_map(
            function ($item, $key) use ($iter, $indentForChanged, $indentForUnchanged, $depth) {
                if (!is_array($item)) {
                    return $indentForUnchanged . $key . ": " . $iter($item, $depth + 1);
                }
                if (!array_key_exists('type', $item)) {
                    return $indentForUnchanged . $key . ": " . $iter($item, $depth + 1);
                }
                if ($item['type'] === "added") {
                    return $indentForChanged . "+ " . $item[$key] . ": " . $iter($item['value2'], $depth + 1);
                }
                if ($item['type'] === "removed") {
                    return $indentForChanged . "- " . $item[$key] . ": " . $iter($item['value1'], $depth + 1);
                }
                if ($item['type'] === "updated") {
                    return $indentForChanged . "- " . $item[$key] . ": " . $iter($item['value1'], $depth + 1)
                        . "\n" . $indentForChanged . "+ " . $item[$key] . ": " . $iter($item['value2'], $depth + 1);
                }
                return $indentForUnchanged . $key . ": " . $iter($item['value1'], $depth + 1);
            },
            $currentValue,
            array_keys($currentValue)
        );

        $result = ['{', ...$line, $bracketIndent . '}'];

        return implode("\n", $result);
    };
    return $iter($value, 1);
}
